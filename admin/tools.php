<?php
/**
 * erreurs.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if($user['authlevel'] < 1)
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = sys_get_param_int('mode');

switch($mode){
  case ADM_TOOL_CONFIG_RELOAD:
    $config->db_loadAll();
    sys_refresh_tablelist($config->db_prefix);

    $config->db_loadItem('game_watchlist');
    if($config->game_watchlist)
    {
      $config->game_watchlist_array = explode(';', $config->game_watchlist);
    }
    else
    {
      unset($config->game_watchlist_array);
    }
  break;
  
  case ADM_TOOL_MD5:
    $template = gettemplate("admin/md5enc", true);
    $password_seed = sys_get_param_str_unsafe('seed', SN_SYS_SEC_CHARS_ALLOWED);
    $password_length = sys_get_param_int('length', 16);
    $string = ($string = sys_get_param_str_unsafe('string')) ? $string : sys_random_string($password_length, $password_seed);

    $template->assign_vars(array(
      'SEED' => $password_seed,
      'LENGTH' => $password_length,
      'STRING' => htmlentities($string),
      'MD5' => md5($string),
    ));
    display($template, $lang['adm_tools_md5_header'], false, '', true );
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

  case ADM_TOOL_INFO_MYSQL:
    $template = gettemplate("simple_table", true);

    $template->assign_block_vars('table', $lang['adm_tool_mysql_table']['server']);
    $status = array(
      $lang['adm_tool_mysql_server_version'] => mysql_get_server_info(),
      $lang['adm_tool_mysql_client_version'] => mysql_get_client_info(),
      $lang['adm_tool_mysql_host_info']      => mysql_get_host_info(),
    );
    foreach($status as $key => $value)
    {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $key,
        'VALUE_2' => $value,
      ));
    }

    $template->assign_block_vars('table', $lang['adm_tool_mysql_table']['status']);
    $status = explode('  ', mysql_stat());
    foreach($status as $value)
    {
      $row = explode(': ', $value);
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row[0],
        'VALUE_2' => $row[1],
      ));
    }


    $template->assign_block_vars('table', $lang['adm_tool_mysql_table']['params']);
    $result = doquery('SHOW STATUS;');
    while($row = mysql_fetch_assoc($result))
    {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row['Variable_name'],
        'VALUE_2' => $row['Value'],
      ));
    }

    $template->assign_vars(array(
      'PAGE_HEADER' => $lang['adm_tool_mysql_page_header'],
      'COLUMN_NAME_1' => $lang['adm_tool_mysql_param_name'],
      'COLUMN_NAME_2' => $lang['adm_tool_mysql_param_value'],
      'TABLE_FOOTER' => 'test',
    ));

    display( $template, $lang['adm_bn_ttle'], false, '', true);
  break;

}

display( parsetemplate(gettemplate("admin/admin_tools", true)), $lang['adm_bn_ttle'], false, '', true);
?>
