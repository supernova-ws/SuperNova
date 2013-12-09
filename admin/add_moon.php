<?php

/**
 * add_moon.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if ($user['authlevel'] < 2)
if ($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = $_POST['mode'];

$PageTpl = gettemplate("admin/add_moon", true);
$parse = $lang;

if ($mode == 'addit')
{
  $PlanetID = sys_get_param_id('user');
  $MoonName = sys_get_param_str('name');

  $PlanetSelected = doquery("SELECT `galaxy`, `system`, `planet`, `id_owner` FROM {{planets}} WHERE `id` = '{$PlanetID}' LIMIT 1;", true);

  $Galaxy = $PlanetSelected['galaxy'];
  $System = $PlanetSelected['system'];
  $Planet = $PlanetSelected['planet'];
  $Owner  = $PlanetSelected['id_owner'];

  uni_create_moon($Galaxy, $System, $Planet, $Owner, 0, $MoonName);

  AdminMessage($lang['addm_done'], $lang['addm_title']);
}
$Page = parsetemplate($PageTpl, $parse);

display($Page, $lang['addm_title'], false, '', true);

?>
