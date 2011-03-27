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

if($user['authlevel'] < 2)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode      = $_POST['mode'];

$PageTpl   = gettemplate("admin/add_money");
$parse     = $lang;

if ($mode == 'addit') {
  $id          = $_POST['id'];
  $metal       = $_POST['metal'];
  $cristal     = $_POST['cristal'];
  $deut        = $_POST['deut'];
  $QryUpdatePlanet  = "UPDATE {{planets}} SET ";
  $QryUpdatePlanet .= "`metal` = `metal` + '". $metal ."', ";
  $QryUpdatePlanet .= "`crystal` = `crystal` + '". $cristal ."', ";
  $QryUpdatePlanet .= "`deuterium` = `deuterium` + '". $deut ."' ";
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '". $id ."' ";
  doquery( $QryUpdatePlanet);

  AdminMessage ( $lang['adm_am_done'], $lang['adm_am_ttle'] );
}
$Page = parsetemplate($PageTpl, $parse);

display ($Page, $lang['adm_am_ttle'], false, '', true);

?>
