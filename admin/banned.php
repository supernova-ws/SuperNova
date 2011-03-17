<?php

/**
 * banned.php
 *
 * @version 1.3  copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *   [~] Optimized SQL-queries
 * @version 1.2 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.1  - (c) Copyright by Gorlum for http://supernova.ws
 * @version 1.0  - copyright 2008 by Chlorel for XNova
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('admin');

$mode = sys_get_param_str('mode', 'banit');
$name = sys_get_param_str('name');
$action = sys_get_param_str('action');

if ($mode == 'banit' && $action) {
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

  $QryUpdateUser     = "UPDATE {{users}} SET ";
  $QryUpdateUser    .= "`bana` = '1', ";
  $QryUpdateUser    .= "`banaday` = '". $BannedUntil ."' ";
  if($isVacation){
    $QryUpdateUser    .= ", `vacation` = '{$BannedUntil}' ";
  }
  $QryUpdateUser    .= "WHERE ";
  $QryUpdateUser    .= "`username` = \"". $name ."\" LIMIT 1;";

  $QryResult = doquery( $QryUpdateUser);

  if($QryResult){
    doquery("INSERT INTO {{banned}} SET `who` = \"{$name}\", `theme` = '{$reas}', `who2` = '{$name}', `time` = '{$Now}', `longer` = '{$BannedUntil}', `author` = '{$admin}', `email` = '{$mail}';");

    $DoneMessage = "{$lang['adm_bn_thpl']} {$name} {$lang['adm_bn_isbn']}";
    if($isVacation)
    {
      $DoneMessage .= $lang['adm_bn_vctn'];
    }

    $QryResult = doquery( "SELECT `id` FROM {{users}} WHERE `username` = \"". $name ."\" LIMIT 1;", '', true);

    $QryResult =
      doquery("UPDATE {{planets}}
       SET
         `metal_mine_porcent` = '0', `crystal_mine_porcent` = '0', `deuterium_sintetizer_porcent` = '0',
         `solar_plant_porcent` = '0', `fusion_plant_porcent` = '0', `solar_satelit_porcent` = '0', `que` = ''
       WHERE `id_owner` = {$QryResult['id']};");

    if($QryResult){
      $DoneMessage .= $lang['adm_bn_plnt'];
    }else{
      $DoneMessage .= $lang['adm_bn_err2'];
    };
  }
  else
  {
    $DoneMessage = sprintf($lang['adm_bn_errr'], $name);
  };

  AdminMessage ($DoneMessage, $lang['adm_ban_title']);
}
elseif ($mode == 'unbanit' && $action)
{
  doquery("UPDATE {{users}} SET bana=0, banaday=0, `vacation` = {$time_now} WHERE username like '{$name}';");
  $DoneMessage       = $lang['adm_unbn_thpl'] ." ". $name ." ". $lang['adm_unbn_isbn'];
  AdminMessage ($DoneMessage, $lang['adm_unbn_ttle']);
};

$parse['name'] = $name;
$parse['mode'] = $mode;

display( parsetemplate(gettemplate("admin/admin_ban", true), $parse), $lang['adm_ban_title'], false, '', true);

?>
