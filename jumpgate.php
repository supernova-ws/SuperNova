<?php

/**
 * jumpgate.php
 *
 * Jump Gate interface, I presume
 *
 * @version 1.0st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

  includeLang ('infos');

  if ($_POST) {
    $RestString   = GetNextJumpWaitTime ( $planetrow );
    $NextJumpTime = $RestString['value'];
    $JumpTime     = time();
    // Dit monsieur, j'ai le droit de sauter ???
    if ( $NextJumpTime == 0 ) {
      // Dit monsieur, ou je veux aller ca existe ???
      $TargetPlanet = intval($_POST['jmpto']);
      $TargetGate   = doquery ( "SELECT `id`, `sprungtor`, `last_jump_time` FROM {{table}} WHERE `id` = '". $TargetPlanet ."';", 'planets', true);
      // Dit monsieur, ou je veux aller y a une porte de saut ???
      if ($TargetGate['sprungtor'] > 0) {
        $RestString   = GetNextJumpWaitTime ( $TargetGate );
        $NextDestTime = $RestString['value'];
        // Dit monsieur, chez toi aussi peut y avoir un saut ???
        if ( $NextDestTime == 0 ) {
          // Bon j'ai eu toutes les autorisations, donc je compte les radis !!!
          $ShipArray   = array();
          $SubQueryOri = "";
          $SubQueryDes = "";
          for ( $Ship = 200; $Ship < 300; $Ship++ ) {
            $ShipLabel = "c". $Ship;
            $ShipNum = intval($_POST[ $ShipLabel ]);
            if ( $ShipNum > $planetrow[ $sn_data[$Ship]['name'] ] ) {
              $ShipArray[ $Ship ] = $planetrow[ $sn_data[$Ship]['name'] ];
            } else {
              $ShipArray[ $Ship ] = $ShipNum;
            }
            if ($ShipArray[ $Ship ] <> 0) {
              $SubQueryOri .= "`". $sn_data[$Ship]['name'] ."` = `". $sn_data[$Ship]['name'] ."` - '". $ShipArray[ $Ship ] ."', ";
              $SubQueryDes .= "`". $sn_data[$Ship]['name'] ."` = `". $sn_data[$Ship]['name'] ."` + '". $ShipArray[ $Ship ] ."', ";
            }
          }
          // Dit monsieur, y avait quelque chose a envoyer ???
          if ($SubQueryOri != "") {
            // Soustraction de la lune de depart !
            $QryUpdateOri  = "UPDATE {{table}} SET ";
            $QryUpdateOri .= $SubQueryOri;
            $QryUpdateOri .= "`last_jump_time` = '". $JumpTime ."' ";
            $QryUpdateOri .= "WHERE ";
            $QryUpdateOri .= "`id` = '". $planetrow['id'] ."';";
            doquery ( $QryUpdateOri, 'planets');

            // Addition à la lune d'arrivée !
            $QryUpdateDes  = "UPDATE {{table}} SET ";
            $QryUpdateDes .= $SubQueryDes;
            $QryUpdateDes .= "`last_jump_time` = '". $JumpTime ."' ";
            $QryUpdateDes .= "WHERE ";
            $QryUpdateDes .= "`id` = '". $TargetGate['id'] ."';";
            doquery ( $QryUpdateDes, 'planets');

            // Deplacement vers la lune d'arrivée
            $QryUpdateUsr  = "UPDATE {{table}} SET ";
            $QryUpdateUsr .= "`current_planet` = '". $TargetGate['id'] ."' ";
            $QryUpdateUsr .= "WHERE ";
            $QryUpdateUsr .= "`id` = '". $user['id'] ."';";
            doquery ( $QryUpdateUsr, 'users');

            $planetrow['last_jump_time'] = $JumpTime;
            $RestString    = GetNextJumpWaitTime ( $planetrow );
            $RetMessage    = $lang['gate_jump_done'] ." - ". $RestString['string'];
          } else {
            $RetMessage = $lang['gate_wait_data'];
          }
        } else {
          $RetMessage = $lang['gate_wait_dest'] ." - ". $RestString['string'];
        }
      } else {
        $RetMessage = $lang['gate_no_dest_g'];
      }
    } else {
      $RetMessage = $lang['gate_wait_star'] ." - ". $RestString['string'];
    }
  } else {
    $RetMessage = $lang['gate_wait_data'];
  }

  message ($RetMessage, $lang['tech'][43], "infos.php?gid=43", 4);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version from scrap .. y avait pas ... bin maintenant y a !!
?>