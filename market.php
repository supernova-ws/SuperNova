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
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);
if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);

includeLang('market');
includeLang('tech');

$parse = $lang;

$parse['merchant_cost'] = MARKET_MERCHANT;
$parse['scraper_cost'] = MARKET_SCRAPER;

if ($CurrentUser['rpg_points'] >= MARKET_MERCHANT) {
  $parse['lom'] = '<a href="./marchand.php">Вызвать скупщика лома</a> <br> стоит  <font color="green">';
} else {
  $parse['lom'] = 'Вызвать скупщика лома <br> стоит <font color="red">';
}

if ($CurrentUser['rpg_points'] >= MARKET_SCRAPER) {
  $parse['flot'] = '<a href="./schrotti.php">Вызвать скупщика флота</a> <br> стоит  <font color="green">';
}else {
  $parse['flot'] = 'Вызвать скупщика флота <br> стоит <font color="red">';
}


$page = parsetemplate(gettemplate('market'), $parse);
display ( $page, $lang['mod_marchand'], true, '', false );



// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version originelle (Tom1991)
// 1.1 - Version 2.0 de Tom1991 ajout java
// 1.2 - Rййcriture Chlorel passage aux template, optimisation des appels et des requetes SQL
?>