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

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$planets = array();
$ques = array();

//$planet_row_list = doquery("SELECT `id` FROM {{planets}} WHERE `id_owner` = '{$user['id']}';");
$planet_row_list = SortUserPlanets($user);
while ($planet = mysql_fetch_assoc($planet_row_list))
{
  $global_data = sys_o_get_updated($user, $planet['id'], $time_now);
  $planets[$planet['id']] = $global_data['planet'];
  $ques[$planet['id']] = $global_data['que'];
}

$template = gettemplate('imperium', true);
$template->assign_var('amount', count($planets) + 2);

$fleet_id = 1;
$fleets = array();

$total['temp_min'] = 1000;
$total['temp_max'] = -999;

foreach ($planets as $planet_index => &$planet)
{
  $list_planet_que = $ques[$planet_index];
  $planet_template = tpl_parse_planet($planet, $list_planet_que);

  $planet_fleet_id = 0;
  $fleet_list = $planet_template['fleet_list'];//flt_get_fleets_to_planet($planet);
  if($fleet_list['own']['count'])
  {
    $planet_fleet_id = "p{$fleet_id}";
    $fleets[] = tpl_parse_fleet_sn($fleet_list['own']['total'], $planet_fleet_id);
    $fleet_id++;
  }

  $template->assign_block_vars('planet', array_merge($planet_template, array(
    'PLANET_FLEET_ID'   => $planet_fleet_id,

    'METAL_CUR'         => pretty_number($planet['metal'], true, $planet['metal_max']),
    'METAL_PROD'        => pretty_number($planet['metal_perhour']),

    'CRYSTAL_CUR'       => pretty_number($planet['crystal'], true, $planet['crystal_max']),
    'CRYSTAL_PROD'      => pretty_number($planet['crystal_perhour']),

    'DEUTERIUM_CUR'     => pretty_number($planet['deuterium'], true, $planet['deuterium_max']),
    'DEUTERIUM_PROD'    => pretty_number($planet['deuterium_perhour']),

    'ENERGY_CUR'        => pretty_number($planet['energy_max'] - $planet['energy_used'], true, true),
    'ENERGY_MAX'        => pretty_number($planet['energy_max']),

    'TEMP_MIN'          => $planet['temp_min'],
    'TEMP_MAX'          => $planet['temp_max'],
  )));

  $planet['fleet_list'] = $planet_template['fleet_list'];
  $planet['BUILDING_ID'] = $planet_template['BUILDING_ID'];
  $planet['hangar_que'] = $planet_template['hangar_que'];
  $planet['full_que'] = $list_planet_que;

  $total['fields'] += $planet['field_current'];
  $total['metal'] += $planet['metal'];
  $total['crystal'] += $planet['crystal'];
  $total['deuterium'] += $planet['deuterium'];
  $total['energy'] += $planet['energy_max'] - $planet['energy_used'];

  $total['fields_max'] += $planet['field_max'] + $planet[$sn_data[STRUC_TERRAFORMER]['name']] * 5;
  $total['metal_perhour'] += $planet['metal_perhour'];
  $total['crystal_perhour'] += $planet['crystal_perhour'];
  $total['deuterium_perhour'] += $planet['deuterium_perhour'];
  $total['energy_max'] += $planet['energy_max'];

  $total['temp_min'] = min($planet['temp_min'], $total['temp_min']);
  $total['temp_max'] = max($planet['temp_max'], $total['temp_max']);
}

tpl_assign_fleet($template, $fleets);

$template->assign_block_vars('planet', array_merge(array(
  'NAME'       => $lang['sys_total'],

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

  'TEMP_MIN' => $total['temp_min'],
  'TEMP_MAX' => $total['temp_max'],
)));
unset($planet);

$last = -1000;
foreach ($sn_data as $unit_id => $res) {
  if (in_array($unit_id, $sn_data['groups']['structures']))
    $mode = 'buildings';
  elseif (in_array($unit_id, $sn_data['groups']['fleet']))
    $mode = 'fleet';
  elseif (in_array($unit_id, $sn_data['groups']['defense']))
    $mode = 'defense';
  else
    $mode = '';

  if($mode)
  {
    if((int) ($unit_id/100) != (int)($last/100))
    {
      $template->assign_block_vars('prods', array(
        'NAME' => $lang['tech'][(int) ($unit_id/100)*100],
      ));
    }

    $template->assign_block_vars('prods', array(
      'ID'    => $unit_id,
      'FIELD' => $sn_data[$unit_id]['name'],
      'NAME'  => $lang['tech'][$unit_id],
      'MODE'  => $mode,
    ));

    $unit_count = 0;
    foreach($planets as $planet)
    {
      $level_plus['LEVEL_PLUS_YELLOW'] = 0;
      $level_plus['LEVEL_PLUS_GREEN'] = 0;
      if(in_array($unit_id, $sn_data['groups']['prod']))
      {
        $level_plus['PERCENT'] = $planet["{$sn_data[$unit_id]['name']}_porcent"] * 10;
      }
      else
      {
        $level_plus['PERCENT'] = -1;
      }
      switch($mode)
      {
        case 'buildings':
          $level_plus_build = $planet['full_que']['in_que'][$unit_id];
          if($level_plus_build)
          {
            $level_plus['LEVEL_PLUS_GREEN'] = $level_plus_build < 0 ? $level_plus_build : "+{$level_plus_build}";
          }
        break;

        case 'fleet':
          $level_plus['LEVEL_PLUS_YELLOW'] = $planet['fleet_list']['own']['total'][$unit_id]<=0 ? $planet['fleet_list']['own']['total'][$unit_id] : "+{$planet['fleet_list']['own']['total'][$unit_id]}";

        case 'defense':
          if($planet['hangar_que'][$unit_id])
          {
            $level_plus['LEVEL_PLUS_GREEN'] = "+{$planet['hangar_que'][$unit_id]}";
          }
        break;

        default:
        break;
      }

      $unit_db_name = $sn_data[$unit_id]['name'];
      $template->assign_block_vars('prods.planet', array_merge($level_plus, array(
        'ID'         => $planet['id'],
        'TYPE'       => $planet['planet_type'],
        'LEVEL'      => $planet[$unit_db_name] == 0 && !$level_plus['LEVEL_PLUS_YELLOW'] && !$level_plus['LEVEL_PLUS_GREEN'] ? '-' : $planet[$unit_db_name],
      )));
      $unit_count += $planet[$unit_db_name];
    }

    $template->assign_block_vars('prods.planet', array(
      'LEVEL' => $unit_count,
    ));

    $last = $unit_id;
  }
}

display(parsetemplate($template), $lang['imp_overview']);

?>
