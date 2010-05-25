<?php

/**
 * lostpassword.php
 *
 * @version 1.0st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by Tom1991 for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('lostpassword');

$email               = SYS_mysqlSmartEscape($_POST['email']);

  if ($action != 1) {
    $parse               = $lang;
    $parse['servername'] = $game_config['game_name'];
    $page .= parsetemplate(gettemplate('lostpassword'), $parse);
    display($page, $lang['system'], false, '', false, false);
  }
  if ($action == 1) {
    sendnewpassword($email);
    message('Le nouveau mot de passe a &eacute;t&eacute; envoy&eacute; avec succ&egrave;s !', 'OK');
  }

// History version
// 1.0 Création (Tom)
?>