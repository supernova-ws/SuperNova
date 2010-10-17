<?php

/**
 * credit.php
 *
 * @version 1.0
 * @copyright 2008 by e-Zobar for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('credit');

$parse   = $lang;
$parse['admin_email'] = $config->game_adminEmail;
$parse['forum_url'] = '/phpBB3/';

display(parsetemplate(gettemplate('credit_body'), $parse), $lang['cred_credit'], false, "", false, false);
?>