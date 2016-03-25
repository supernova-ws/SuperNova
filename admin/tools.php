<?php
/**
 * erreurs.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if($user['authlevel'] < 1)
if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$mode = sys_get_param_int('mode');

switch($mode) {
  case ADM_TOOL_CONFIG_RELOAD:
    $config->db_loadAll();
    sys_refresh_tablelist();

    $config->db_loadItem('game_watchlist');
    if($config->game_watchlist) {
      $config->game_watchlist_array = explode(';', $config->game_watchlist);
    } else {
      unset($config->game_watchlist_array);
    }
  break;

  case ADM_TOOL_MD5:
    $template = gettemplate("admin/md5enc", true);
    $password_seed = sys_get_param_str_unsafe('seed', SN_SYS_SEC_CHARS_ALLOWED);
    $password_length = sys_get_param_int('length', 16);
    $string = ($string = sys_get_param_str_unsafe('string')) ? $string : sys_random_string($password_length, $password_seed);

    $template->assign_vars(array(
      'SEED'   => $password_seed,
      'LENGTH' => $password_length,
      'STRING' => htmlentities($string),
      'MD5'    => md5($string),
    ));
    display($template, classLocale::$lang['adm_tools_md5_header'], false, '', true);
  break;

  case ADM_TOOL_FORCE_ALL:
    $config->db_saveItem('db_version', 0);
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_FORCE_LAST:
    $config->db_saveItem('db_version', floor($config->db_version - 1));
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_INFO_PHP:
    phpinfo();
  break;

  case ADM_TOOL_INFO_SQL:
    $template = gettemplate("simple_table", true);

    $template->assign_block_vars('table', classLocale::$lang['adm_tool_sql_table']['server']);
    $status = array(
      classLocale::$lang['adm_tool_sql_server_version'] => db_get_server_info(),
      classLocale::$lang['adm_tool_sql_client_version'] => db_get_client_info(),
      classLocale::$lang['adm_tool_sql_host_info']      => db_get_host_info(),
    );
    foreach($status as $key => $value) {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $key,
        'VALUE_2' => $value,
      ));
    }

    $template->assign_block_vars('table', classLocale::$lang['adm_tool_sql_table']['status']);
    $status = explode('  ', db_server_stat());
    foreach($status as $value) {
      $row = explode(': ', $value);
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row[0],
        'VALUE_2' => $row[1],
      ));
    }


    $template->assign_block_vars('table', classLocale::$lang['adm_tool_sql_table']['params']);
    $result = db_core_show_status();
    while($row = db_fetch($result)) {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row['Variable_name'],
        'VALUE_2' => $row['Value'],
      ));
    }

    $template->assign_vars(array(
      'PAGE_HEADER'   => classLocale::$lang['adm_tool_sql_page_header'],
      'COLUMN_NAME_1' => classLocale::$lang['adm_tool_sql_param_name'],
      'COLUMN_NAME_2' => classLocale::$lang['adm_tool_sql_param_value'],
      'TABLE_FOOTER'  => 'test',
    ));

    display($template, classLocale::$lang['adm_bn_ttle'], false, '', true);
  break;

}

display(parsetemplate(gettemplate("admin/admin_tools", true)), classLocale::$lang['adm_bn_ttle'], false, '', true);
?>
