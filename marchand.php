<?php

/**
 * marchand.php
 *
 * 1.3s - Security checks by Gorlum for http://supernova.ws
 * 1.3 - Fixing error whith overwriting current resources on planet when exchanging
 *       copyright (c) 2010 by Gorlum for http://supernova.ws
 * 1.2 - Réécriture Chlorel passage aux template, optimisation des appels et des requetes SQL
 * 1.1 - Version 2.0 de Tom1991 ajout java
 * 1.0 - Version originelle (Tom1991)
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
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

function ModuleMarchand ( $CurrentUser, &$CurrentPlanet ) {
  global $lang, $_POST;

  $POST_ress    = SYS_mysqlSmartEscape($_POST['ress']);
  $POST_metal   = intval($_POST['metal']);
  $POST_cristal = intval($_POST['cristal']);
  $POST_deut    = intval($_POST['deut']);
  $POST_action  = intval($_POST['action']);
  $POST_choix   = SYS_mysqlSmartEscape($_POST['choix']);

  includeLang('marchand');

  $parse   = $lang;
  $rinok_lom = RINOK_LOM;

  if ($CurrentUser['rpg_points'] >= $rinok_lom) {
    if ($POST_ress != '') {
      $PageTPL = gettemplate('message_body');
      $Error   = false;
      switch ($POST_ress) {
        case 'metal':
          $Necessaire   = (($POST_cristal * 2) + ($POST_deut * 4));
          if (($POST_cristal < 0) || ($POST_deut < 0)){
            $Message = "Failed";
            $Error   = true;
          } elseif ($CurrentPlanet['metal'] > $Necessaire) {
            $CurrentPlanet['metal'] -= $Necessaire;
          } else {
            $Message = $lang['mod_ma_noten'] ." ". $lang['Metal'] ."! ";
            $Error   = true;
          }
          break;

        case 'cristal':
          $Necessaire   = (($POST_metal * 0.5) + ($POST_deut * 2));
          if(($POST_metal < 0) || ($POST_deut < 0)){
            $Message = "Failed";
            $Error   = true;
          } elseif ($CurrentPlanet['crystal'] > $Necessaire) {
            $CurrentPlanet['crystal'] -= $Necessaire;
          } else {
            $Message = $lang['mod_ma_noten'] ." ". $lang['Crystal'] ."! ";
            $Error   = true;
          }
          break;

        case 'deuterium':
          $Necessaire   = (($POST_metal * 0.25) + ($POST_cristal * 0.5));
          if(($POST_metal < 0) || ($POST_cristal < 0)){
            $Message = "Failed";
            $Error   = true;
          } elseif ($CurrentPlanet['deuterium'] > $Necessaire) {
            $CurrentPlanet['deuterium'] -= $Necessaire;
          } else {
            $Message = $lang['mod_ma_noten'] ." ". $lang['Deuterium'] ."! ";
            $Error   = true;
          }
          break;
        default: $Error = true;
      }
      if ($Error == false) {
        $rinok_lom = RINOK_LOM;
        $CurrentUser['rpg_points']         -= $rinok_lom;
        $CurrentPlanet['metal']     += $POST_metal;
        $CurrentPlanet['crystal']   += $POST_cristal;
        $CurrentPlanet['deuterium'] += $POST_deut;
            $QryUpdateUser  = "UPDATE {{table}} SET ";
            $QryUpdateUser .= "`rpg_points` = '". $CurrentUser['rpg_points'] ."' ";
            $QryUpdateUser .= "WHERE ";
            $QryUpdateUser .= "`id` = '". $CurrentUser['id'] ."';";
            doquery( $QryUpdateUser, 'users' );
        $QryUpdatePlanet  = "UPDATE {{table}} SET ";
        $QryUpdatePlanet .= "`metal` = `metal` + '".         $POST_metal   ."', ";
        $QryUpdatePlanet .= "`crystal` = `crystal` + '".     $POST_cristal ."', ";
        $QryUpdatePlanet .= "`deuterium` = `deuterium` + '". $POST_deut    ."' ";
        $QryUpdatePlanet .= "WHERE ";
        $QryUpdatePlanet .= "`id` = '".        $CurrentPlanet['id']        ."';";
        doquery ( $QryUpdatePlanet , 'planets');
        $Message = $lang['mod_ma_done'];
      }
      if ($Error == true) {
        $parse['title'] = $lang['mod_ma_error'];
      } else {
        $parse['title'] = $lang['mod_ma_donet'];
      }
      $parse['mes']   = $Message;
    } else {
      if ($POST_action != 2) {
        $PageTPL = gettemplate('marchand_main');
      } else {
        $parse['mod_ma_res']   = "1";
        switch ($POST_choix) {
          case 'metal':
            $PageTPL = gettemplate('marchand_metal');
            $parse['mod_ma_res_a'] = "2";
            $parse['mod_ma_res_b'] = "4";
            break;
          case 'cristal':
            $PageTPL = gettemplate('marchand_cristal');
            $parse['mod_ma_res_a'] = "0.5";
            $parse['mod_ma_res_b'] = "2";
            break;
          case 'deut':
            $PageTPL = gettemplate('marchand_deuterium');
            $parse['mod_ma_res_a'] = "0.25";
            $parse['mod_ma_res_b'] = "0.5";
            break;
        }
      }
    }
  }
  $Page    = parsetemplate ( $PageTPL, $parse );
  return  $Page;
}

  $Page = ModuleMarchand ( $user, $planetrow );
  display ( $Page, $lang['mod_marchand'], true, '', false );


// -----------------------------------------------------------------------------------------------------------
?>