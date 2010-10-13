<?php

/**
 * erreurs.php
 *
 * @version 1.2   - Added ability to view single error details (like backtrace) by Gorlum for http://supernova.ws
 * @version 1.1st - Tested by Gorlum for http://supernova.ws
 * @version 1.1   - Remade with more robust template by Gorlum for http://supernova.ws
 * @version 1.0s  - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by e-Zobar for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

includeLang('admin');

$delete    = intval($_GET['delete']);
$detail    = intval($_GET['detail']);
$deleteall = SYS_mysqlSmartEscape($_GET['deleteall']);

// Supprimer les erreurs
if ($delete) {
  doquery("DELETE FROM `{{table}}` WHERE `error_id`=$delete", 'errors');
} elseif ($deleteall == 'yes') {
  doquery("TRUNCATE TABLE `{{table}}`", 'errors');
}

if($detail){
  $errorInfo = doquery("SELECT * FROM `{{table}}` WHERE `error_id` = {$detail}", 'errors', true);
  $errorInfo['error_time'] = date($config->game_date_withTime, $errorInfo['error_time']);
  display(parsetemplate(gettemplate('admin/error_detail'), $errorInfo), "Errors", false, '', true);
}else{
  $parse = $lang;

  // Afficher les erreurs
  $query = doquery("SELECT * FROM `{{table}}`", 'errors');
  $i = 0;
  while ($u = mysql_fetch_array($query)) {
    $i++;
    $parse['errors_list'] .= "
    <tr><td class=n><a href=errors.php?detail={$u['error_id']}><u>{$u['error_id']}</u></a></td>
    <td class=n>{$u['error_sender']}</td>
    <td class=n>{$u['error_type']}</td>
    <td class=n>". date($config->game_date_withTime, $u['error_time']) ."</td>
    <td class=b>{$u['error_page']}</td>
    <td class=n><a href=\"?delete=". $u['error_id'] ."\"><img src=\"../images/r1.png\"></a></td>
    </tr>
    <tr><td colspan=\"6\" class=b>".  nl2br($u['error_text'])."</td></tr>";
  }
  $parse['errors_num'] = $i;

  display(parsetemplate(gettemplate('admin/errors_body'), $parse), "Errors", false, '', true);
}
?>
