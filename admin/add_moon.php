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

// if ($user['authlevel'] < 2)
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate("admin/add_moon", true);

if(sys_get_param_str('mode') == 'addit')
{
  $PlanetID = sys_get_param_id('user');
  $MoonName = sys_get_param_str('name');

  sn_db_transaction_start();
  $PlanetSelected = db_planet_by_id($PlanetID, true, '`galaxy`, `system`, `planet`, `id_owner`');
  uni_create_moon($PlanetSelected['galaxy'], $PlanetSelected['system'], $PlanetSelected['planet'], $PlanetSelected['id_owner'], 0, $MoonName);
  sn_db_transaction_commit();

  AdminMessage($lang['addm_done'], $lang['addm_title']);
}

display($template, $lang['addm_title'], false, '', true);
