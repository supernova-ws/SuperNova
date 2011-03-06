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

$mode = intval($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

includeLang('admin');

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

}

display( parsetemplate(gettemplate("admin/admin_tools", true)), $lang['adm_bn_ttle'], false, '', true);
?>
