<?php

/**
 * admin/add_moon.php
 *
 * @version 2
 * @copyright 2014 Gorlum for http://supernova.ws
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate("admin/add_moon", true);

if(sys_get_param_str('mode') == 'addit')
{
  $PlanetID = sys_get_param_id('user');
  $MoonName = sys_get_param_str('name');

  sn_db_transaction_start();
  $PlanetSelected = DBStaticPlanet::db_planet_by_id($PlanetID, true, '`galaxy`, `system`, `planet`, `id_owner`');
  uni_create_moon($PlanetSelected['galaxy'], $PlanetSelected['system'], $PlanetSelected['planet'], $PlanetSelected['id_owner'], 0, $MoonName);
  sn_db_transaction_commit();

  AdminMessage(classLocale::$lang['addm_done'], classLocale::$lang['addm_title']);
}

display($template, classLocale::$lang['addm_title'], false, '', true);
