<?php

/**
 * officier.php
 *
 * @version 1.1
 * @copyright 2008 By Tom1991 for XNova
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

check_urlaubmodus ($user);
function ShowOfficierPage ( &$CurrentUser ) {
  global $lang, $resource, $reslist, $_GET;

  includeLang('officier');

  // Vérification que le joueur n'a pas un nombre de points négatif
  if ($CurrentUser['rpg_points'] < 0) {
    doquery("UPDATE {{table}} SET `rpg_points` = '0' WHERE `id` = '". $CurrentUser['id'] ."';", 'users');
  }
  //darkmater constant
    $darkmater_cost = DARKMATER_COST;
  // Si recrutement d'un officier
  if ($_GET['mode'] == 2) {
    if ($CurrentUser['rpg_points'] >= $darkmater_cost) {
      $Selected    = $_GET['offi'];
      if ( in_array($Selected, $reslist['officier']) ) {
        $Result = IsOfficierAccessible ( $CurrentUser, $Selected );
        if ( $Result == 1 ) {
          $CurrentUser[$resource[$Selected]] += 1;
          $CurrentUser['rpg_points']         -= $darkmater_cost;
          $QryUpdateUser  = "UPDATE {{table}} SET ";
          $QryUpdateUser .= "`rpg_points` = '". $CurrentUser['rpg_points'] ."', ";
          $QryUpdateUser .= "`".$resource[$Selected]."` = '". $CurrentUser[$resource[$Selected]] ."' ";
          $QryUpdateUser .= "WHERE ";
          $QryUpdateUser .= "`id` = '". $CurrentUser['id'] ."';";
          doquery( $QryUpdateUser, 'users' );
          $Message = $lang['OffiRecrute'];
          Header("Location: officier.php");
        } elseif ( $Result == -1 ) {
          $Message = $lang['Maxlvl'];
        } elseif ( $Result == 0 ) {
          $Message = $lang['Noob'];
        }
      }
    } else {
      $Message = $lang['NoPoints'];
    }
    $MessTPL        = gettemplate('message_body');
    $parse['title'] = $lang['Officier'];
    $parse['mes']   = $Message;

    $page           = parsetemplate( $MessTPL, $parse);

  } else {
    // Pas de recrutement d'officier
    $PageTPL = gettemplate('officier_body');
    $RowsTPL = gettemplate('officier_rows');
    $parse['off_points']   = $lang['off_points'];
    $parse['alv_points']   = $CurrentUser['rpg_points'];
    $parse['disp_off_tbl'] = "";
    for ( $Officier = 601; $Officier <= 615; $Officier++ ) {
      $Result = IsOfficierAccessible ( $CurrentUser, $Officier );
      if ( $Result != 0 ) {
        $bloc['off_id']       = $Officier;
        $bloc['off_tx_lvl']   = $lang['off_tx_lvl'];
        $bloc['off_lvl']      = $CurrentUser[$resource[$Officier]];
        $bloc['off_desc']     = $lang['Desc'][$Officier];
        $bloc['DARK_COST'] = $darkmater_cost;
        if ($Result == 1) {
          $bloc['off_link'] = "<a href=\"officier.php?mode=2&offi=".$Officier."\"><font color=\"#00ff00\">". $lang['link'][$Officier]."</font>";
        } else {
          $bloc['off_link'] = $lang['Maxlvl'];
        }
        $parse['disp_off_tbl'] .= parsetemplate( $RowsTPL, $bloc);

      }
    }
    $page           = parsetemplate( $PageTPL, $parse);
  }

  return $page;
}

  $page = ShowOfficierPage ( $user );
  display($page, $lang['officier']);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version originelle (Tom1991)
// 1.1 - Réécriture Chlorel pour integration complete dans XNova
?>