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

if($user['authlevel'] < 1)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = sys_get_param_int('mode');

switch($mode){
  case 1:
    $config->db_loadAll();

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

  case 2:
    $config->db_saveItem('db_version', 0);
    require_once('../includes/update.php');
  break;

  case 3:
    $config->db_saveItem('db_version', floor($config->db_version - 1));
    require_once('../includes/update.php');
  break;

  case 4:
    phpinfo();
  break;

}

display( parsetemplate(gettemplate("admin/admin_tools", true)), $lang['adm_bn_ttle'], false, '', true);
?>
