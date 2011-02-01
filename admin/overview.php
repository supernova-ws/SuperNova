<?php

/**
 * overview.php
 *
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
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

  $GET_cmd  = SYS_mysqlSmartEscape($_GET['cmd']);
  $TypeSort = SYS_mysqlSmartEscape($_GET['type']);

  if ($user['authlevel'] >= 1) {
    includeLang('admin');

    if ($GET_cmd == 'sort') {
    } else {
      $TypeSort = "id";
    }

    $PageTPL  = gettemplate('admin/overview_body');
    $RowsTPL  = gettemplate('admin/overview_rows');

    $parse                      = $lang;
    $parse['dpath']             = $dpath;
    $parse['mf']                = '_self';
    $parse['adm_ov_data_yourv'] = colorRed(VERSION);

    $Last15Mins = doquery("SELECT * FROM {{table}} WHERE `onlinetime` >= '". (time() - 15 * 60) ."' ORDER BY `". $TypeSort ."` ASC;", 'users');
    $Count      = 0;
    $Color      = "lime";
    while ( $TheUser = mysql_fetch_assoc($Last15Mins) ) {
      if ($PrevIP != "") {
        if ($PrevIP == $TheUser['user_lastip']) {
          $Color = "red";
        } else {
          $Color = "lime";
        }
      }

      $UserPoints = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $TheUser['id'] . "';", 'statpoints', true);
      $Bloc['dpath']               = $dpath;
      $Bloc['adm_ov_altpm']        = $lang['adm_ov_altpm'];
      $Bloc['adm_ov_wrtpm']        = $lang['adm_ov_wrtpm'];
      $Bloc['adm_ov_data_id']      = $TheUser['id'];
      $Bloc['adm_ov_data_name']    = $TheUser['username'];
      $Bloc['adm_ov_data_agen']    = $TheUser['user_agent'];
      $Bloc['adm_ov_data_clip']    = $Color;
      $Bloc['adm_ov_data_adip']    = $TheUser['user_lastip'];
      $Bloc['adm_ov_data_ally']    = $TheUser['ally_name'];
      $Bloc['adm_ov_data_point']   = pretty_number ( $UserPoints['total_points'] );
      $Bloc['adm_ov_data_activ']   = pretty_time ( time() - $TheUser['onlinetime'] );
      $Bloc['adm_ov_data_pict']    = "m.gif";
      $PrevIP                      = $TheUser['user_lastip'];

      $parse['adm_ov_data_table'] .= parsetemplate( $RowsTPL, $Bloc );
      $Count++;
    }

    $parse['adm_ov_data_count']  = $Count;
    $Page = parsetemplate($PageTPL, $parse);

    display ( $Page, $lang['sys_overview'], false, '', true);
  } else {
    AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }
?>