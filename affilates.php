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

$page = parsetemplate( gettemplate('affilates') , $parse );

display( $page, $lang['sys_affilates_title']);
?>