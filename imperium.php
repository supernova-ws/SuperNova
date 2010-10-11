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
$template->assign_var(mount, count($planet) + 2);

//$parse['mount'] = count($planet) + 1;

foreach ($planet as &$p) {
//  $planetCaps = ECO_getPlanetCaps($user, $p);
  PlanetResourceUpdate($user, $p, $time_now, true);

  $planet_template = tpl_parse_planet($p);

  $template->assign_block_vars('planet', array_merge($planet_template, array(
    'FIELDS_CUR' => $p['field_current'],
    'FIELDS_MAX' => $p['field_max'] + $p[$sn_data[33]['name']] * 5,

    'METAL_CUR'  => pretty_number($p['metal'], true, $p['metal_max']),
    'METAL_PROD' => pretty_number($p['metal_perhour']),

    'CRYSTAL_CUR'  => pretty_number($p['crystal'], true, $p['crystal_max']),
    'CRYSTAL_PROD' => pretty_number($p['crystal_perhour']),

    'DEUTERIUM_CUR'  => pretty_number($p['deuterium'], true, $p['deuterium_max']),
    'DEUTERIUM_PROD' => pretty_number($p['deuterium_perhour']),

    'ENERGY_CUR' => pretty_number($p['energy_max'] - $p['energy_used'], true, true),
    'ENERGY_MAX' => pretty_number($p['energy_max']),
  )));
  $p['fleet_list'] = $planet_template['fleet_list'];

  // pdump($p['fleet_list']);

  $total['fields'] += $p['field_current'];
  $total['metal'] += $p['metal'];
  $total['crystal'] += $p['crystal'];
  $total['deuterium'] += $p['deuterium'];
  $total['energy'] += $p['energy_max'] - $p['energy_used'];

  $total['fields_max'] += $p['field_max'] + $p[$sn_data[33]['name']] * 5;
  $total['metal_perhour'] += $p['metal_perhour'];
  $total['crystal_perhour'] += $p['crystal_perhour'];
  $total['deuterium_perhour'] += $p['deuterium_perhour'];
  $total['energy_max'] += $p['energy_max'];
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
    foreach($planet as $p)
    {
      switch($mode)
      {
        case 'buildings':
          $level_plus['LEVEL_PLUS'] = '';
        break;

        case 'fleet':
          $level_plus['LEVEL_PLUS'] = $p['fleet_list']['own'][$unit_id];
        break;

        case 'defense':
          $level_plus['LEVEL_PLUS'] = '';
        break;

        default:
          $level_plus['LEVEL_PLUS'] = '';
        break;
      }

      if ($mode == 'buildings')
      {
        $level_plus['LEVEL_PLUS'] = '';
      }
      elseif ($mode == 'fleet')
      {
      }
      elseif ($mode == 'defense')
      {
      }
      else
      {
        $level_plus['LEVEL_PLUS'] = '';
      }

      $template->assign_block_vars('prods.planet', array_merge($level_plus, array(
        'ID'         => $p['id'],
        'TYPE'       => $p['planet_type'],
        'LEVEL'      => $p[$resource[$unit_id]],
      )));
      $unit_count += $p[$resource[$unit_id]];
    }

    $template->assign_block_vars('prods.planet', array(
      'LEVEL' => $unit_count,
    ));

    $last = $unit_id;
  }
}

display(parsetemplate($template, $parse), $lang['imp_overview']);

// Created by Perberos. All rights reserved (C) 2006
?>