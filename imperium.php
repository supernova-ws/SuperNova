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

  $template->assign_block_vars('planet', array_merge(tpl_parse_planet($p), array(
    'FIELDS_CUR' => $p['field_current'],
    'FIELDS_MAX' => $p['field_max'] + $p[$sn_data[33]['name']] * 5,

    'METAL_CUR'  => pretty_number($p['metal'], true, $planetCaps['planet']['metal_max']),
    'METAL_PROD' => pretty_number($p['metal_perhour']),

    'CRYSTAL_CUR'  => pretty_number($p['crystal'], true, $planetCaps['planet']['crystal_max']),
    'CRYSTAL_PROD' => pretty_number($p['crystal_perhour']),

    'DEUTERIUM_CUR'  => pretty_number($p['deuterium'], true, $planetCaps['planet']['deuterium_max']),
    'DEUTERIUM_PROD' => pretty_number($p['deuterium_perhour']),

    'ENERGY_CUR' => pretty_number($p['energy_max'] - $p['energy_used'], true, true),
    'ENERGY_MAX' => pretty_number($p['energy_max']),
  )));
}

$last = -1000;
foreach ($sn_data as $unit_id => $res) {
  if (in_array($unit_id, $reslist['build']))
    $mode = 'buildings';
  elseif (in_array($unit_id, $reslist['fleet']))
    $mode = 'fleet';
  elseif (in_array($unit_id, $reslist['defense']))
    $mode = 'defense';
  else
    $mode = '';

  if($mode){
    if((int) ($unit_id/100) != (int)($last/100)){
      $template->assign_block_vars('prods', array(
        'NAME' => $lang['tech'][(int) ($unit_id/100)*100],
      ));
    }

    $template->assign_block_vars('prods', array(
      'ID'    => $unit_id,
      'FIELD' => $resource[$unit_id],
      'NAME'  => $lang['tech'][$unit_id],
      'MODE'  => $mode,
    ));

    foreach($planet as $p){
      $template->assign_block_vars('prods.planet', array(
        'ID' => $p['id'],
        'TYPE' => $p['planet_type'],
        'LEVEL' => $p[$resource[$unit_id]],
      ));
    }
    $last = $unit_id;
  }
}

display(parsetemplate($template, $parse), $lang['Imperium']);

// Created by Perberos. All rights reserved (C) 2006
?>