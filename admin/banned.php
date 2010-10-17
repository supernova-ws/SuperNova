<?php

/**
 * banned.php
 *
 * @version 1.1s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.1  - (c) Copyright by Gorlum for http://supernova.ws
 * @version 1.0  - copyright 2008 by Chlorel for XNova
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

  $parse     = $lang;
  if ($mode == 'banit') {
    $name              = $_POST['name'];
    $reas              = $_POST['why'];
    $days              = $_POST['days'];
    $hour              = $_POST['hour'];
    $mins              = $_POST['mins'];
    $secs              = $_POST['secs'];
    $isVacation        = $_POST['isVacation'];

    $admin             = $user['username'];
    $mail              = $user['email'];

    $Now               = time();
    $BanTime           = $days * 86400;
    $BanTime          += $hour * 3600;
    $BanTime          += $mins * 60;
    $BanTime          += $secs;
    $BannedUntil       = $Now + $BanTime;

    $QryInsertBan      = "INSERT INTO {{table}} SET ";
    $QryInsertBan     .= "`who` = \"". $name ."\", ";
    $QryInsertBan     .= "`theme` = '". $reas ."', ";
    $QryInsertBan     .= "`who2` = '". $name ."', ";
    $QryInsertBan     .= "`time` = '". $Now ."', ";
    $QryInsertBan     .= "`longer` = '". $BannedUntil ."', ";
    $QryInsertBan     .= "`author` = '". $admin ."', ";
    $QryInsertBan     .= "`email` = '". $mail ."';";
    doquery( $QryInsertBan, 'banned');

    $QryUpdateUser     = "UPDATE {{table}} SET ";
    $QryUpdateUser    .= "`bana` = '1', ";
    $QryUpdateUser    .= "`banaday` = '". $BannedUntil ."' ";
    if($isVacation){
      $QryUpdateUser    .= ", `urlaubs_until` = '". $BannedUntil ."' ";
      $QryUpdateUser    .= ", `urlaubs_modus` = '1' ";
    }
    $QryUpdateUser    .= "WHERE ";
    $QryUpdateUser    .= "`username` = \"". $name ."\";";

    $QryResult = doquery( $QryUpdateUser, 'users');

    if($QryResult){
      $DoneMessage = $lang['adm_bn_thpl'] ." ". $name ." ". $lang['adm_bn_isbn'];
      if($isVacation){
        $DoneMessage .= $lang['adm_bn_vctn'];
      }

      $QryResult = doquery( "SELECT `id` FROM {{table}} WHERE `username` = \"". $name ."\";", 'users', true);

      $QryUpdateUser     = "UPDATE {{table}} SET ";
      $QryUpdateUser    .= "  `metal_mine_porcent` = '0' ";
      $QryUpdateUser    .= ", `crystal_mine_porcent` = '0' ";
      $QryUpdateUser    .= ", `deuterium_sintetizer_porcent` = '0' ";
      $QryUpdateUser    .= ", `solar_plant_porcent` = '0' ";
      $QryUpdateUser    .= ", `fusion_plant_porcent` = '0' ";
      $QryUpdateUser    .= ", `solar_satelit_porcent` = '0' ";
      $QryUpdateUser    .= ", `b_building` = '0' ";
      $QryUpdateUser    .= ", `b_building_id` = '0' ";
      $QryUpdateUser    .= "WHERE ";
      $QryUpdateUser    .= "`id_owner` = ". $QryResult['id']. ";";

      $QryResult = doquery( $QryUpdateUser, 'planets');

      if($QryResult){
        $DoneMessage .= $lang['adm_bn_plnt'];
      }else{
        $DoneMessage .= $lang['adm_bn_err2'];
      };

    }else{
      $DoneMessage = sprintf($lang['adm_bn_errr'], $name);
    };

    AdminMessage ($DoneMessage, $lang['adm_bn_ttle']);
  }elseif ($mode == 'unbanit') {
    $nam = $_POST['name'];
    //doquery("DELETE FROM {{table}} WHERE who2='{$nam}'", 'banned');
    //doquery("UPDATE {{banned}} SET `longer` = {$time_now} WHERE who2='{$nam}'", 'banned');
    doquery("UPDATE {{table}} SET bana=0, banaday=0, `urlaubs_until` = 0 WHERE username like '{$nam}'", "users");
    $DoneMessage       = $lang['adm_unbn_thpl'] ." ". $name ." ". $lang['adm_unbn_isbn'];
    AdminMessage ($DoneMessage, $lang['adm_unbn_ttle']);
  };

  $parse['name'] = SYS_mysqlSmartEscape($_GET['name']);

  $mode = SYS_mysqlSmartEscape($_GET['mode'] ? $_GET['mode'] : 'banit');
  $parse['mode'] = $mode;
  $PageTpl = ($mode == 'banit') ? gettemplate("admin/banned") : gettemplate("admin/unbanned");

  // adm_bn_username

  $Page = parsetemplate($PageTpl, $parse);
  display( $Page, $lang['adm_bn_ttle'], false, '', true);
} else {
  AdminMessage ($lang['sys_noalloaw'], $lang['sys_noaccess']);
}
?>