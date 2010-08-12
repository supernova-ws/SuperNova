<?php

/**
 * imperium.php
 *
 * Overview you empire
 *
 * @version 1.0
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

includeLang('imperium');

$planetsrow = doquery("SELECT * FROM {{planets}} WHERE `id_owner` = '{$user['id']}';");

$planet = array();
$parse  = $lang;

while ($p = mysql_fetch_array($planetsrow)) {
  $planet[] = $p;
}

$template = gettemplate('imperium', true);
$template->assign_var(mount, count($planet) + 1);

//$parse['mount'] = count($planet) + 1;

foreach ($planet as $p) {
  $planetCaps = ECO_getPlanetCaps($user, $p);

  $template->assign_block_vars('planet', array(
    'ID' => $p['id'],
    'TYPE' => $p['planet_type'],
    'IMAGE' => $p['image'],
    'NAME' => $p['name'],
    'COORDINATES' => INT_makeCoordinates($p),

    'FIELDS_CUR' => $p['field_current'],
    'FIELDS_MAX' => $p['field_max'] + $p[$resource[33]] * 5,

    'METAL_CUR'  => pretty_number($p['metal'], true, $planetCaps['planet']['metal_max']),
    'METAL_PROD' => pretty_number($p['metal_perhour']),

    'CRYSTAL_CUR'  => pretty_number($p['crystal'], true, $planetCaps['planet']['crystal_max']),
    'CRYSTAL_PROD' => pretty_number($p['crystal_perhour']),

    'DEUTERIUM_CUR'  => pretty_number($p['deuterium'], true, $planetCaps['planet']['deuterium_max']),
    'DEUTERIUM_PROD' => pretty_number($p['deuterium_perhour']),

    'ENERGY_CUR' => pretty_number($p['energy_max'] - $p['energy_used'], true, true),
    'ENERGY_MAX' => pretty_number($p['energy_max']),
  ));
}

$last = -1000;
foreach ($resource as $i => $res) {
  if (in_array($i, $reslist['build']))
    $mode = 'buildings';
  elseif (in_array($i, $reslist['fleet']))
    $mode = 'fleet';
  elseif (in_array($i, $reslist['defense']))
    $mode = 'defense';
  else
    $mode = '';

  if($mode){
    if((int) ($i/100) != (int)($last/100)){
      $template->assign_block_vars('prods', array(
        'NAME' => $lang['tech'][(int) ($i/100)*100],
      ));
    }

    $template->assign_block_vars('prods', array(
      'ID'    => $i,
      'FIELD' => $resource[$i],
      'NAME'  => $lang['tech'][$i],
      'MODE'  => $mode,
    ));

    foreach($planet as $p){
      $template->assign_block_vars('prods.planet', array(
        'ID' => $p['id'],
        'TYPE' => $p['planet_type'],
        'LEVEL' => $p[$resource[$i]],
      ));
    }
    $last = $i;
  }
}

display(parsetemplate($template, $parse), $lang['Imperium']);

// Created by Perberos. All rights reserved (C) 2006
?>