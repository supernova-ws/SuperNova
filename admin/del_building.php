<?php

/**
 * add_ship.php
 *
 * @version 1.0
 * @copyright 2008 By Xire -AlteGarde-
 *
 */


define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

  if ($user['authlevel'] >= 1) {
    includeLang('admin');

    $mode      = $_POST['mode'];

    $PageTpl   = gettemplate("admin/del_building");
    $parse     = $lang;

    if ($mode == 'addit') {
      $id          = $_POST['id'];
      $metal_mine       = $_POST['metal_mine'];
      $crystal_mine    = $_POST['crystal_mine'];
      $deuterium_sintetizer        = $_POST['deuterium_sintetizer'];
      $solar_plant        = $_POST['solar_plant'];
      $fusion_plant    = $_POST['fusion_plant'];
      $robot_factory        = $_POST['robot_factory'];
      $nano_factory      = $_POST['nano_factory'];
      $hangar        = $_POST['hangar'];
      $metal_store       = $_POST['metal_store'];
      $crystal_store      = $_POST['crystal_store'];
          $deuterium_store     = $_POST['deuterium_store'];
          $laboratory       = $_POST['laboratory'];
          $terraformer       = $_POST['terraformer'];
          $ally_deposit      = $_POST['ally_deposit'];
          $silo      = $_POST['silo'];
      $QryUpdatePlanet  = "UPDATE {{table}} SET ";
      $QryUpdatePlanet .= "`metal_mine` = `metal_mine` - '". $metal_mine ."', ";
      $QryUpdatePlanet .= "`crystal_mine` = `crystal_mine` - '". $crystal_mine ."', ";
      $QryUpdatePlanet .= "`deuterium_sintetizer` = `deuterium_sintetizer` - '". $deuterium_sintetizer ."', ";
      $QryUpdatePlanet .= "`solar_plant` = `solar_plant` - '". $solar_plant ."', ";
      $QryUpdatePlanet .= "`fusion_plant` = `fusion_plant` - '". $fusion_plant ."', ";
      $QryUpdatePlanet .= "`robot_factory` = `robot_factory` - '". $robot_factory ."', ";
      $QryUpdatePlanet .= "`nano_factory` = `nano_factory` - '". $nano_factory ."', ";
      $QryUpdatePlanet .= "`hangar` = `hangar` - '". $hangar ."', ";
      $QryUpdatePlanet .= "`metal_store` = `metal_store` - '". $metal_store ."', ";
      $QryUpdatePlanet .= "`crystal_store` = `crystal_store` - '". $crystal_store ."', ";
      $QryUpdatePlanet .= "`deuterium_store` = `deuterium_store` - '". $deuterium_store ."', ";
      $QryUpdatePlanet .= "`laboratory` = `laboratory` - '". $laboratory ."', ";
      $QryUpdatePlanet .= "`terraformer` = `terraformer` - '". $terraformer ."', ";
      $QryUpdatePlanet .= "`ally_deposit` = `ally_deposit` - '". $ally_deposit ."', ";
      $QryUpdatePlanet .= "`silo` = `silo` - '". $silo ."' ";
      $QryUpdatePlanet .= "WHERE ";
      $QryUpdatePlanet .= "`id` = '". $id ."' ";
      doquery( $QryUpdatePlanet, "planets");

      AdminMessage ( $lang['adm_delbuilding2'], $lang['adm_delbuilding1'] );
    }
    $Page = parsetemplate($PageTpl, $parse);

    display ($Page, $lang['adm_am_ttle'], false, '', true);
  } else {
    AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }

?>
