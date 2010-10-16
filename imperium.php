<?php

/**
 * imperium.php
 *
 * Overview you empire
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
// Created by Perberos. All rights reserved (C) 2006
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

$planets = array();
$parse  = $lang;

while ($planet = mysql_fetch_array($planetsrow)) {
  $planets[] = $planet;
}

$template = gettemplate('imperium', true);
$template->assign_var(mount, count($planets) + 2);

//$parse['mount'] = count($planets) + 1;

foreach ($planets as $planet_index => $planet) {
//  $planetCaps = ECO_getPlanetCaps($user, $planet);
  PlanetResourceUpdate($user, $planet, $time_now);

  $planet_template = tpl_parse_planet($planet);

  $template->assign_block_vars('planet', array_merge($planet_template, array(
    'FIELDS_CUR' => $planet['field_current'],
    'FIELDS_MAX' => $planet['field_max'] + $planet[$sn_data[33]['name']] * 5,

    'METAL_CUR'  => pretty_number($planet['metal'], true, $planet['metal_max']),
    'METAL_PROD' => pretty_number($planet['metal_perhour']),

    'CRYSTAL_CUR'  => pretty_number($planet['crystal'], true, $planet['crystal_max']),
    'CRYSTAL_PROD' => pretty_number($planet['crystal_perhour']),

    'DEUTERIUM_CUR'  => pretty_number($planet['deuterium'], true, $planet['deuterium_max']),
    'DEUTERIUM_PROD' => pretty_number($planet['deuterium_perhour']),

    'ENERGY_CUR' => pretty_number($planet['energy_max'] - $planet['energy_used'], true, true),
    'ENERGY_MAX' => pretty_number($planet['energy_max']),
  )));
  $planets[$planet_index]['fleet_list'] = $planet_template['fleet_list'];
  $planets[$planet_index]['BUILDING_ID'] = $planet_template['BUILDING_ID'];
  $planets[$planet_index]['hangar_que'] = $planet_template['hangar_que'];

  $total['fields'] += $planet['field_current'];
  $total['metal'] += $planet['metal'];
  $total['crystal'] += $planet['crystal'];
  $total['deuterium'] += $planet['deuterium'];
  $total['energy'] += $planet['energy_max'] - $planet['energy_used'];

  $total['fields_max'] += $planet['field_max'] + $planet[$sn_data[33]['name']] * 5;
  $total['metal_perhour'] += $planet['metal_perhour'];
  $total['crystal_perhour'] += $planet['crystal_perhour'];
  $total['deuterium_perhour'] += $planet['deuterium_perhour'];
  $total['energy_max'] += $planet['energy_max'];
}

$template->assign_block_vars('planet', array_merge(array(
  'NAME'       => 'хрнцн',

  'FIELDS_CUR' => $total['fields'],
  'FIELDS_MAX' => $total['fields_max'],

  'METAL_CUR'  => pretty_number($total['metal']),
  'METAL_PROD' => pretty_number($total['metal_perhour']),

  'CRYSTAL_CUR'  => pretty_number($total['crystal']),
  'CRYSTAL_PROD' => pretty_number($total['crystal_perhour']),

  'DEUTERIUM_CUR'  => pretty_number($total['deuterium']),
  'DEUTERIUM_PROD' => pretty_number($total['deuterium_perhour']),

  'ENERGY_CUR' => pretty_number($total['energy']),
  'ENERGY_MAX' => pretty_number($total['energy_max']),
)));


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

    $unit_count = 0;
    foreach($planets as $planet)
    {
      $level_plus['LEVEL_PLUS_YELLOW'] = 0;
      $level_plus['LEVEL_PLUS_GREEN'] = 0;
      switch($mode)
      {
        case 'buildings':
          if($planet['BUILDING_ID'] == $unit_id)
          {
            $level_plus['LEVEL_PLUS_GREEN'] = 1;
          }
        break;

        case 'fleet':
          $level_plus['LEVEL_PLUS_YELLOW'] = $planet['fleet_list']['own'][$unit_id];

        case 'defense':
          if($planet['hangar_que'][$unit_id])
          {
            $level_plus['LEVEL_PLUS_GREEN'] += $planet['hangar_que'][$unit_id];
          }
        break;

        default:
        break;
      }

      $template->assign_block_vars('prods.planet', array_merge($level_plus, array(
        'ID'         => $planet['id'],
        'TYPE'       => $planet['planet_type'],
        'LEVEL'      => $planet[$resource[$unit_id]],
      )));
      $unit_count += $planet[$resource[$unit_id]];
    }

    $template->assign_block_vars('prods.planet', array(
      'LEVEL' => $unit_count,
    ));

    $last = $unit_id;
  }
}

display(parsetemplate($template, $parse), $lang['imp_overview']);

?>
