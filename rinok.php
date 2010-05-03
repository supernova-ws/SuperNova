<?php

/**
 * marchand.php
 *
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');include($ugamela_root_path . 'common.' . $phpEx);if ($IsUserChecked == false) {	includeLang('login');	header("Location: login.php");}check_urlaubmodus ($user);
function rinokpage ( $CurrentUser) {
	global $lang;
$rinok_lom = RINOK_LOM;
$rinok_flot = RINOK_FLOT;
$parse['lom_cost'] = $rinok_lom;
$parse['flot_cost'] = $rinok_flot;
$parse['xz'] = $CurrentUser['rpg_points'];
if ($CurrentUser['rpg_points'] >= $rinok_lom) {
$parse['lom'] = '<a href="./marchand.php">Вызвать скупщика лома</a> <br> стоит  <font color="green">';
} else {
$parse['lom'] = 'Вызвать скупщика лома <br> стоит <font color="red">';
}

if ($CurrentUser['rpg_points'] >= $rinok_flot) {
$parse['flot'] = '<a href="./schrotti.php">Вызвать скупщика флота</a> <br> стоит  <font color="green">';
}else {
$parse['flot'] = 'Вызвать скупщика флота <br> стоит <font color="red">';
}

	$page = parsetemplate(gettemplate('rinok'), $parse);
	return $page;
}
    $page = RinokPage ( $user);
	display ( $page, $lang['mod_marchand'], true, '', false );


// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version originelle (Tom1991)
// 1.1 - Version 2.0 de Tom1991 ajout java
// 1.2 - Rййcriture Chlorel passage aux template, optimisation des appels et des requetes SQL
?>