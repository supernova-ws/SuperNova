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

$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

$mode = intval($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

includeLang('admin');
$parse = $lang;

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

display( parsetemplate(gettemplate("admin/admin_tools"), $parse), $lang['adm_bn_ttle'], false, '', true);
?>
