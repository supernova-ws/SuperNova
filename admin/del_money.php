<?php

/**
 * add_money.php
 *
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 * portion to e-Zobar
 */


define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('admin');

$mode      = $_POST['mode'];

$PageTpl   = gettemplate("admin/del_money");
$parse     = $lang;

if ($mode == 'addit') {
  $id          = $_POST['id'];
  $metal       = $_POST['metal'];
  $cristal     = $_POST['cristal'];
  $deut        = $_POST['deut'];
  $QryUpdatePlanet  = "UPDATE {{table}} SET ";
  $QryUpdatePlanet .= "`metal` = `metal` - '". $metal ."', ";
  $QryUpdatePlanet .= "`crystal` = `crystal` - '". $cristal ."', ";
  $QryUpdatePlanet .= "`deuterium` = `deuterium` - '". $deut ."' ";
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '". $id ."' ";
  doquery( $QryUpdatePlanet, "planets");

  AdminMessage ( $lang['adm_delmoney2'], $lang['adm_delmoney1'] );
}
$Page = parsetemplate($PageTpl, $parse);

display ($Page, $lang['adm_am_ttle'], false, '', true);

?>
