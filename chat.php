<?php
//print('Chat temporary disabled');
//die();

  /**
   * chat.php
   *
   * @version 1.2s Security checks by Gorlum for http://supernova.ws
   * @version 1.2 by Ihor
   * @version 1.0
   * @copyright 2008 by e-Zobar for XNova
   */

ini_set('display_error',0);
ini_set('error_reporting',0);

define('INSIDE'  , true);
define('INSTALL' , false);
$xnova_root_path = './';

$doNotUpdateFleet = true;
include($xnova_root_path . 'extension.inc');
include($xnova_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);

includeLang('chat');

$nick = $user['username'];
$parse = $lang;

if ($_GET) {
  if($_GET["chat_type"]=="ally"){
    $parse['chat_type'] = $_GET["chat_type"];
    $parse['ally_id']   = $user['ally_id'];
  }
}

if ($game_config['OverviewClickBanner'] != '') {
  $parse['ClickBanner'] = stripslashes( $game_config['OverviewClickBanner'] );
}

$page = parsetemplate(gettemplate('chat_body'), $parse);

display($page, $lang['Chat'], false);

// Shoutbox by e-Zobar - Copyright XNova Team 2008
?>