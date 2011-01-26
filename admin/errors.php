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

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

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
  doquery("DELETE FROM `{{logs}}` WHERE `log_id` = {$delete} LIMIT 1;");
} elseif ($deleteall == 'yes') {
//  doquery("TRUNCATE TABLE `{{logs}}`");
}

if($detail){
  $errorInfo = doquery("SELECT * FROM `{{logs}}` WHERE `log_id` = {$detail} LIMIT 1;", '', true);
  $template = gettemplate('admin/error_detail', true);
  $error_dump = unserialize($errorInfo['log_dump']);
  foreach($error_dump as $key => $value)
  {
    $v = array(
      'VAR_NAME' => $key,
      'VAR_VALUE' => $key == 'query_log' ? $value : dump($value, $key)
    );

    $template->assign_block_vars('vars', $v);
  }
  display(parsetemplate($template, $errorInfo), "Errors", false, '', true);
}else{
  $template = gettemplate('admin/errors_body', true);
  $parse = $lang;

  // Afficher les erreurs
  $query = doquery("SELECT * FROM `{{logs}}` ORDER BY log_id DESC LIMIT 100;");
  $i = 0;
  while ($u = mysql_fetch_assoc($query)) {
    $i++;
    /*
    $parse['errors_list'] .= "
    <tr><th class=n><a href=errors.php?detail={$u['log_id']}><u>{$u['log_id']}</u></a></th>
    <th class=n>{$u['log_username']}</th>
    <th class=n>{$u['log_title']}</th>
    <th class=n>". date(FMT_DATE_TIME, $u['log_time']) ."</th>
    <th class=b>{$u['log_page']}</th>
    <th class=n><a href=\"?delete=". $u['log_id'] ."\"><img src=\"../design/images/r1.png\"></a></th>
    </tr>
    <tr><td colspan=\"6\" class=b>".  nl2br($u['log_text'])."</td></tr>";
    */

    foreach($u as $key => $value)
    {
      $v[strtoupper($key)] = $value;
    }

    $template->assign_block_vars('error', $v);
  }
  $parse['errors_num'] = $i;




  display(parsetemplate($template, $parse), "Errors", false, '', true);
}

?>
