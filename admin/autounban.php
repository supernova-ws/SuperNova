<?php

/**
 * autounban.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */


// Mais qu'est ce qu'il voullait demontrer lui ????

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

$lang['PHP_SELF'] = 'options.'.$phpEx;
doquery("UPDATE {{table}} SET `banaday` =` banaday` - '1' WHERE `banaday` != '0';",'users');
doquery("UPDATE {{table}} SET `bana` = '0' WHERE `banaday` < '1';",'users');
$parse['dpath'] = $dpath;
$parse['debug'] = ($config->debug == 1) ? " checked='checked'/":'';

?>
