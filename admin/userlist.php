<?php

/**
 * userlist.php
 *
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

  $GET_cmd  = SYS_mysqlSmartEscape($_GET['cmd']);
  $GET_user = intval($_GET['user']);
  $TypeSort = SYS_mysqlSmartEscape($_GET['type']);

  if ($user['authlevel'] >= 1) {
    includeLang('admin');

    if ($GET_cmd == 'dele') {
      DeleteSelectedUser ( $GET_user );
    }

    if ($GET_cmd == 'sort') {
    } else {
      $TypeSort = "id";
    }

    $PageTPL = gettemplate('admin/userlist_body');
    $RowsTPL = gettemplate('admin/userlist_rows');

    $query   = doquery("SELECT * FROM {{table}} ORDER BY `". $TypeSort ."` ASC", 'users');

    $parse                 = $lang;
    $parse['adm_ul_table'] = "";
    $i                     = 0;
    $Color                 = "lime";

    $Bloc['sort'] = $TypeSort?"&sort=" . $TypeSort:"";
    while ($u = mysql_fetch_assoc ($query) ) {
      if ($PrevIP != "") {
        if ($PrevIP == $u['user_lastip']) {
          $Color = "red";
        } else {
          $Color = "lime";
        }
      }
      $Bloc['dpath']              = $dpath;
      $Bloc['adm_ul_data_id']     = $u['id'];
      $Bloc['adm_ul_data_name']   = $u['username'];
      $Bloc['adm_ul_data_mail']   = $u['email'];
      $Bloc['adm_ov_altpm']       = $lang['adm_ov_altpm'];
      $Bloc['adm_ov_wrtpm']       = $lang['adm_ov_wrtpm'];
      $Bloc['adm_ul_data_adip']   = "<font color=\"".$Color."\">". $u['user_lastip'] ."</font>";
      $Bloc['adm_ul_data_regd']   = date ( $config->game_date_withTime, $u['register_time'] );
      $Bloc['adm_ul_data_lconn']  = date ( $config->game_date_withTime, $u['onlinetime'] );
      $Bloc['adm_ul_data_banna']  = ( $u['bana'] == 1 ) ? "<span title=\"". date ( $config->game_date_withTime, $u['banaday']) ."\">". $lang['adm_ul_yes'] ."</span>" : $lang['adm_ul_no'];
      $Bloc['adm_ul_ban_mode']    = ( $u['bana'] == 1 ) ? 'unbanit' : 'banit' ;
      // $Bloc['adm_ul_data_actio']  = "<a href=\"userlist.php?cmd=dele&user=".$u['id']."\"><img src=\"../images/r1.png\"></a>"; // Lien vers actions 'effacer'
      $PrevIP                     = $u['user_lastip'];
      $parse['adm_ul_table']     .= parsetemplate( $RowsTPL, $Bloc );
      $i++;
    }
    $parse['adm_ul_count'] = $i;
    $parse['dpath']        = $dpath;

    $page = parsetemplate( $PageTPL, $parse );
    display( $page, $lang['adm_ul_title'], false, '', true);
  } else {
    message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }

// Created by e-Zobar. All rights reversed (C) XNova Team 2008
?>