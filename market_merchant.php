<?php

/**
 * marchand.php
 *
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

includeLang('marchand');

$parse   = $lang;
$rinok_lom = MARKET_MERCHANT;

if ($CurrentUser['rpg_points'] >= $rinok_lom) {
  if ($_POST['ress'] != '') {
    $PageTPL = gettemplate('message_body');
    $Error   = false;
    switch ($_POST['ress']) {
      case 'metal':
        $Necessaire   = (($_POST['cristal'] * 2) + ($_POST['deut'] * 4));
        if (($_POST['cristal'] < 0) || ($_POST['deut'] < 0)){
          $Message = "Failed";
          $Error   = true;
        } elseif ($CurrentPlanet['metal'] > $Necessaire) {
          $CP['metal'] = $Necessaire;
        } else {
          $Message = $lang['mod_ma_noten'] ." ". $lang['Metal'] ."! ";
          $Error   = true;
        }
        break;

      case 'cristal':
        $Necessaire   = (($_POST['metal'] * 0.5) + ($_POST['deut'] * 2));
        if(($_POST['metal'] < 0) || ($_POST['deut'] < 0)){
          $Message = "Failed";
          $Error   = true;
        } elseif ($CurrentPlanet['crystal'] > $Necessaire) {
          $CP['crystal'] = $Necessaire;
        } else {
          $Message = $lang['mod_ma_noten'] ." ". $lang['Crystal'] ."! ";
          $Error   = true;
        }
        break;

      case 'deuterium':
        $Necessaire   = (($_POST['metal'] * 0.25) + ($_POST['cristal'] * 0.5));
        if(($_POST['metal'] < 0) || ($_POST['cristal'] < 0)){
          $Message = "Failed";
          $Error   = true;
        } elseif ($CurrentPlanet['deuterium'] > $Necessaire) {
          $CP['deuterium'] = $Necessaire;
        } else {
          $Message = $lang['mod_ma_noten'] ." ". $lang['Deuterium'] ."! ";
          $Error   = true;
        }
        break;
      default: $Error = true;
    }
    if ($Error == false) {
      $rinok_lom = MARKET_MERCHANT;
      $CurrentUser['rpg_points']         -= $rinok_lom;
      $CP['metal']     += $_POST['metal'];
      $CP['crystal']   += $_POST['cristal'];
      $CP['deuterium'] += $_POST['deut'];
          $QryUpdateUser  = "UPDATE {{table}} SET ";
          $QryUpdateUser .= "`rpg_points` = '". $CurrentUser['rpg_points'] ."' ";
          $QryUpdateUser .= "WHERE ";
          $QryUpdateUser .= "`id` = '". $CurrentUser['id'] ."';";
          doquery( $QryUpdateUser, 'users' );
      $QryUpdatePlanet  = "UPDATE {{table}} SET ";
      $QryUpdatePlanet .= "`metal` = `metal` + '".     $CP['metal']     ."', ";
      $QryUpdatePlanet .= "`crystal` = `crystal` + '".   $CP['crystal']   ."', ";
      $QryUpdatePlanet .= "`deuterium` = `deuterium` + '". $CP['deuterium'] ."' ";
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
    if ($_POST['action'] != 2) {
      $PageTPL = gettemplate('marchand_main');
    } else {
      $parse['mod_ma_res']   = "1";
      switch ($_POST['choix']) {
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

$planetrow = doquery ("SELECT * FROM {{table}} WHERE `id` = '". $planetrow['id'] ."';", 'planets', true);


// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version originelle (Tom1991)
// 1.1 - Version 2.0 de Tom1991 ajout java
// 1.2 - Réécriture Chlorel passage aux template, optimisation des appels et des requetes SQL
?>