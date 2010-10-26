<?php

define('INSIDE'  , true);
define('INSTALL' , false);

$allow_anonymous = true;
$skip_ban_check = true;

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

display(parsetemplate(gettemplate('dark_matter', true), $parse), $lang['sys_dark_matter']);

?>
