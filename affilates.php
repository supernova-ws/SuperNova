<?php

/**
 * banner_list.php
 *
 * v1 (c) copyright 2010 by Gorlum for http://supernova.ws
 */
define('INSIDE', true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin');

$parse          = $lang;
$parse['dpath'] = $dpath;
$parse['user_id'] = $user['id'];

$parse['serverURL'] = "http://".$_SERVER["SERVER_NAME"];

$bannerURL = "http://".$_SERVER["SERVER_NAME"]. $config->int_banner_URL;
$bannerURL .= strpos($bannerURL, '?') ? '&' : '?';
$bannerURL .= "id=" . $user['id'];
$parse['bannerURL'] = $bannerURL;

$userbarURL = "http://" . $_SERVER["SERVER_NAME"] . $config->int_userbar_URL;
$userbarURL .= strpos($userbarURL, '?') ? '&' : '?';
$userbarURL .= "id=" . $user['id'];
$parse['userbarURL'] = $userbarURL;

$template = parsetemplate( gettemplate('affilates', true) , $parse );

$affilates = doquery("SELECT r.*, u.username, u.register_time FROM {{referrals}} AS r LEFT JOIN {{users}} AS u ON u.id = r.id WHERE id_partner = {$user['id']}");
while ($affilate = mysql_fetch_array($affilates)) {
  $gained += floor($affilate['dark_matter']/10);
  $template->assign_block_vars('affilates', array(
    'REGISTERED'  => date($config->game_date_withTime,$affilate['register_time']),
    'USERNAME'    => $affilate['username'],
    'DARK_MATTER' => $affilate['dark_matter'],
    'GAINED'      => floor($affilate['dark_matter']/$config->rpg_bonus_divisor),
  ));
}


$template->assign_var('GAINED', $gained);
$template->assign_var('rpg_bonus_divisor', $config->rpg_bonus_divisor);

display( $template, $lang['sys_affilates_title']);
?>