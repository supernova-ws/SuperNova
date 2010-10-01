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

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

  if ($user['authlevel'] >= 1) {
    includeLang('admin');

    $mode      = $_POST['mode'];

    $PageTpl   = gettemplate("admin/add_def");
    $parse     = $lang;

    if ($mode == 'addit') {
      $id          = $_POST['id'];
      $misil_launcher       = $_POST['misil_launcher'];
      $small_laser    = $_POST['small_laser'];
      $big_laser        = $_POST['big_laser'];
      $gauss_canyon        = $_POST['gauss_canyon'];
      $ionic_canyon    = $_POST['ionic_canyon'];
      $buster_canyon        = $_POST['buster_canyon'];
      $small_protection_shield      = $_POST['small_protection_shield'];
      $big_protection_shield        = $_POST['big_protection_shield'];
      $interceptor_misil       = $_POST['interceptor_misil'];
      $interplanetary_misil      = $_POST['interplanetary_misil'];
      $QryUpdatePlanet  = "UPDATE {{table}} SET ";
      $QryUpdatePlanet .= "`misil_launcher` = `misil_launcher` + '". $misil_launcher ."', ";
      $QryUpdatePlanet .= "`small_laser` = `small_laser` + '". $small_laser ."', ";
      $QryUpdatePlanet .= "`big_laser` = `big_laser` + '". $big_laser ."', ";
      $QryUpdatePlanet .= "`gauss_canyon` = `gauss_canyon` + '". $gauss_canyon ."', ";
      $QryUpdatePlanet .= "`ionic_canyon` = `ionic_canyon` + '". $ionic_canyon ."', ";
      $QryUpdatePlanet .= "`buster_canyon` = `buster_canyon` + '". $buster_canyon ."', ";
      $QryUpdatePlanet .= "`small_protection_shield` = `small_protection_shield` + '". $small_protection_shield ."', ";
      $QryUpdatePlanet .= "`big_protection_shield` = `big_protection_shield` + '". $big_protection_shield ."', ";
      $QryUpdatePlanet .= "`interceptor_misil` = `interceptor_misil` + '". $interceptor_misil ."', ";
      $QryUpdatePlanet .= "`interplanetary_misil` = `interplanetary_misil` + '". $interplanetary_misil ."' ";
      $QryUpdatePlanet .= "WHERE ";
      $QryUpdatePlanet .= "`id` = '". $id ."' ";
      doquery( $QryUpdatePlanet, "planets");

      AdminMessage ( $lang['adm_adddef2'], $lang['adm_adddef1'] );
    }
    $Page = parsetemplate($PageTpl, $parse);

    display ($Page, $lang['adm_am_ttle'], false, '', true);
  } else {
    AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }

?>
