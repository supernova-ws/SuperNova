<?php

/**
 * add_moon.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
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

includeLang('admin');

$mode      = $_POST['mode'];

$PageTpl   = gettemplate("admin/add_moon");
$parse     = $lang;

if ($mode == 'addit') {
  $PlanetID  = $_POST['user'];
  $MoonName  = $_POST['name'];

  $QrySelectPlanet  = "SELECT * FROM {{table}} ";
  $QrySelectPlanet .= "WHERE ";
  $QrySelectPlanet .= "`id` = '". $PlanetID ."';";
  $PlanetSelected = doquery ( $QrySelectPlanet, 'planets', true);

  $Galaxy    = $PlanetSelected['galaxy'];
  $System    = $PlanetSelected['system'];
  $Planet    = $PlanetSelected['planet'];
  $Owner     = $PlanetSelected['id_owner'];

  uni_create_moon ( $Galaxy, $System, $Planet, $Owner, 20, $MoonName);

  AdminMessage ( $lang['addm_done'], $lang['addm_title'] );
}
$Page = parsetemplate($PageTpl, $parse);

display ($Page, $lang['addm_title'], false, '', true);

?>
