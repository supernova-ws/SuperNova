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

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

if($delete = sys_get_param_id('delete')) {
  db_log_delete_by_id($delete);
} elseif(sys_get_param_str('delete_update_info')) {
  db_log_delete_update_and_stat_calc();
} elseif(sys_get_param_str('deleteall') == 'yes') {
//  doquery("TRUNCATE TABLE `{{logs}}`");
}

if($detail = sys_get_param_id('detail')) {
  $template = gettemplate('admin/adm_log_main_detail', true);

  $errorInfo = db_log_get_by_id($detail);
  $error_dump = unserialize($errorInfo['log_dump']);
  if(is_array($error_dump)) {
    foreach($error_dump as $key => $value) {
      $v = array(
        'VAR_NAME'  => $key,
        'VAR_VALUE' => $key == 'query_log' ? $value : dump($value, $key)
      );

      $template->assign_block_vars('vars', $v);
    }
  }
  $template->assign_vars($errorInfo);
} else {
  $template = gettemplate('admin/adm_log_main', true);

  $i = 0;
  $query = db_log_list_get_last_100();
  while($u = db_fetch($query)) {
    $i++;
    $v = array();
    foreach($u as $key => $value) {
      $v[strtoupper($key)] = $value;
    }
    $template->assign_block_vars('error', $v);
  }
  $query = db_log_count($i);

  $template->assign_vars($query);
}

display($template, classLocale::$lang['adm_er_ttle'], false, '', true);
