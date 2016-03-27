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

/**
 * @param $template
 * @param $str
 * @param $status
 */
function templateAssignTable($template, $str, $status) {
  $template->assign_block_vars('table', classLocale::$lang['adm_tool_sql_table'][$str]);
  foreach($status as $key => $value) {
    $template->assign_block_vars('table.row', array(
      'VALUE_1' => $key,
      'VALUE_2' => $value,
    ));
  }
}

switch($mode) {
  case ADM_TOOL_CONFIG_RELOAD:
    classSupernova::$config->db_loadAll();
    sys_refresh_tablelist();

    classSupernova::$config->db_loadItem('game_watchlist');
    if(classSupernova::$config->game_watchlist) {
      classSupernova::$config->game_watchlist_array = explode(';', classSupernova::$config->game_watchlist);
    } else {
      unset(classSupernova::$config->game_watchlist_array);
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
    classSupernova::$config->db_saveItem('db_version', 0);
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_FORCE_LAST:
    classSupernova::$config->db_saveItem('db_version', floor(classSupernova::$config->db_version - 1));
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_INFO_PHP:
    phpinfo();
  break;

  case ADM_TOOL_INFO_SQL:
    $template = gettemplate("simple_table", true);

    $status = array(
      classLocale::$lang['adm_tool_sql_server_version'] => classSupernova::$db->db_get_server_info(),
      classLocale::$lang['adm_tool_sql_client_version'] => classSupernova::$db->db_get_client_info(),
      classLocale::$lang['adm_tool_sql_host_info']      => classSupernova::$db->db_get_host_info(),
    );
    templateAssignTable($template, 'server', $status);

    templateAssignTable($template, 'status', classSupernova::$db->db_get_server_stat());
    templateAssignTable($template, 'params', classSupernova::$db->db_core_show_status());

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
