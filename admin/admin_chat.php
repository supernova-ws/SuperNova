<?php

/**
 * chat.php
 *
 * @version 1.1  - Remade with more robust template by Gorlum for http://supernova.ws
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By e-Zobar for XNova
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
$parse = $lang;

  // extract($_GET);
  $delete    = intval($_GET['delete']);
  $deleteall = SYS_mysqlSmartEscape($_GET['deleteall']);

  if ($user['authlevel'] >= 3) {
    // Système de suppression
    if ($delete) {
      doquery("DELETE FROM {{chat}} WHERE `messageid`={$delete};");
    } elseif ($deleteall == 'yes') {
      doquery("DELETE FROM {{chat}};");
    }

    // Affichage des messages
    $query = doquery("SELECT * FROM {{chat}} ORDER BY messageid DESC LIMIT 25;");
    $i = 0;
    while ($e = mysql_fetch_assoc($query)) {
      $i++;
      $parse['msg_list'] .= stripslashes("<tr>" .
        "<td class=n>{$e['messageid']}</td>" .
        "<td class=n><center>" . str_replace(' ', '&nbsp;', date(FMT_DATE_TIME, $e['timestamp'])) . "</center></td>" .
        "<td class=n><center>{$e['user']}</center></td>" .
        "<td class=b width=100%>" . nl2br($e['message']) .
        "<td class=n><center><a href=\"admin/admin_chat.php?delete={$e['messageid']}\"><img src=\"design/images/r1.png\"></a></center></td>" .
      "</td></tr>");
    }
    $parse['msg_num'] = $i;

    display(parsetemplate(gettemplate('admin/admin_chat'), $parse), "Chat", false, '', true);
  } else {
    message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }
?>