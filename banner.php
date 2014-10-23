<?php
/**
* banner.php
* @version 1.0s - Security checks by Gorlum for http://supernova.ws
* @version 1.0
*
* Simple wrapper for INT_createBanner.php
* Create banner or userbar
* banner.php?id=<userid>&type=<banner|userbar>&format=<png>
*
* @copyright 2010 by Gorlum for http://supernova.ws
*
*/

$allow_anonymous = true;
include('includes/init.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');

$id = sys_get_param_id('id');
$type = sys_get_param_str('type', 'userbar');
$format = sys_get_param_str('format', 'png');

int_banner_create($id, $type, $format);
