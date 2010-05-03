<?PHP

/**
 * leftmenu.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

includeLang('leftmenu');

	if ($user['authlevel'] == "1") {
      $parse                 = $lang;
      $parse['mf']           = "Hauptframe";
      $parse['dpath']        = $dpath;
      $parse['XNovaRelease'] = VERSION;
      $parse['servername']   = XNova;
      $Page                  = parsetemplate(gettemplate('admin/left_menu_modo'), $parse);
      display( $Page, "", false, '', true);
   }
	elseif ($user['authlevel'] == "2") {
      $parse                 = $lang;
      $parse['mf']           = "Hauptframe";
      $parse['dpath']        = $dpath;
      $parse['XNovaRelease'] = VERSION;
      $parse['servername']   = XNova;
      $Page                  = parsetemplate(gettemplate('admin/left_menu_op'), $parse);
      display( $Page, "", false, '', true);
   }
	elseif ($user['authlevel'] >= "3") {
      $parse                 = $lang;
      $parse['mf']           = "Hauptframe";
      $parse['dpath']        = $dpath;
      $parse['XNovaRelease'] = VERSION;
      $parse['servername']   = XNova;
      $Page                  = parsetemplate(gettemplate('admin/left_menu'), $parse);
      display( $Page, "", false, '', true);
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
?>