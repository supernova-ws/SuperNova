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

$mode = intval($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

includeLang('admin');
$parse = $lang;

if ($user['authlevel'] >= 3) {

  switch($mode){
    case 1:
      $config->db_loadAll();
      break;
  }

  display( parsetemplate(gettemplate("admin/admin_tools"), $parse), $lang['adm_bn_ttle'], false, '', true);
} else {
  AdminMessage( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
