<?php

/**
 * admin/adm_log_main.php
 *
 * @version 2.0 - full rewrote
 * @copyright 2014 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

if($delete = sys_get_param_id('delete'))
{
  doquery("DELETE FROM `{{logs}}` WHERE `log_id` = {$delete} LIMIT 1;");
}
elseif(sys_get_param_str('deleteall') == 'yes')
{
//  doquery("TRUNCATE TABLE `{{logs}}`");
}

if($detail = sys_get_param_id('detail'))
{
  $template = gettemplate('admin/adm_log_main_detail', true);

  $errorInfo = doquery("SELECT * FROM `{{logs}}` WHERE `log_id` = {$detail} LIMIT 1;", true);
  $error_dump = unserialize($errorInfo['log_dump']);
  if(is_array($error_dump))
  {
    foreach ($error_dump as $key => $value)
    {
      $v = array(
        'VAR_NAME' => $key,
        'VAR_VALUE' => $key == 'query_log' ? $value : dump($value, $key)
      );

      $template->assign_block_vars('vars', $v);
    }
  }
  $template->assign_vars($errorInfo);
}
else
{
  $template = gettemplate('admin/adm_log_main', true);

  $i = 0;
  $query = doquery("SELECT * FROM `{{logs}}` ORDER BY log_id DESC LIMIT 100;");
  while($u = mysql_fetch_assoc($query))
  {
    $i++;
    $v = array();
    foreach($u as $key => $value)
    {
      $v[strtoupper($key)] = $value;
    }
    $template->assign_block_vars('error', $v);
  }
  $query = doquery("SELECT COUNT(*) AS LOG_MESSAGES_TOTAL, {$i} AS LOG_MESSAGES_VISIBLE FROM `{{logs}}`;", true);

  $template->assign_vars($query);
}

display($template, $lang['adm_er_ttle'], false, '', true);
