<?php

// TODO: Переделать под $template_result

/*
 * imperium.php
 *
 * Overview you empire
 *
 * @version 2.1 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 *
 */

function sn_imperium_view($template = null) {
  global $user, $lang;

  imperiumStoreMinesLoad($user);

  list($planets, $ques) = getUpdatedUserPlanetsAndQues($user);

  $template = gettemplate('imperium', $template);

  templateFillPercent($template);

  $template->assign_var('amount', count($planets) + 2);

  $imperiumStats = imperiumAssignFleetsAndCalculateTotal($template, $planets, $lang);

  $sn_group_factories = sn_get_groups('factories');
  foreach (
    [
      UNIT_STRUCTURES         => 'structures',
      UNIT_STRUCTURES_SPECIAL => 'structures',
      UNIT_SHIPS              => 'fleet',
      UNIT_DEFENCE            => 'defense',
    ] as $unit_group_id => $mode
  ) {
    $template->assign_block_vars('prods', array(
      'NAME' => $lang['tech'][$unit_group_id],
    ));
    $unit_group = get_unit_param('techtree', $unit_group_id);
    foreach ($unit_group as $unit_id) {
      $unit_count = $unit_count_abs = 0;
      $block_vars = array();
      $unit_is_factory = in_array($unit_id, $sn_group_factories) && get_unit_param($unit_id, P_MINING_IS_MANAGED);
      foreach ($planets as $planet) {
        $unit_level_plain = mrc_get_level($user, $planet, $unit_id, false, true);

        $level_plus['FACTORY'] = $unit_is_factory;
        $level_plus['LEVEL_PLUS_YELLOW'] = 0;
        $level_plus['LEVEL_PLUS_GREEN'] = 0;

        $level_plus['PERCENT'] = $unit_is_factory ? ($unit_level_plain ? $planet[pname_factory_production_field_name($unit_id)] * 10 : -1) : -1;
        switch ($mode) {

          /** @noinspection PhpMissingBreakStatementInspection */
          case 'fleet':
            $level_plus['LEVEL_PLUS_YELLOW'] = $planet['fleet_list']['own']['total'][$unit_id] <= 0 ? $planet['fleet_list']['own']['total'][$unit_id] : "+{$planet['fleet_list']['own']['total'][$unit_id]}";
            $imperiumStats['units'][$unit_id]['LEVEL_PLUS_YELLOW'] += floatval($level_plus['LEVEL_PLUS_YELLOW']);

          case 'structures':
          case 'defense':
            $level_plus_build = $ques[$planet['id']]['in_que'][que_get_unit_que($unit_id)][$user['id']][$planet['id']][$unit_id];
            if ($level_plus_build) {
              $level_plus['LEVEL_PLUS_GREEN'] = $level_plus_build < 0 ? $level_plus_build : "+{$level_plus_build}";
              $imperiumStats['units'][$unit_id]['LEVEL_PLUS_GREEN'] += floatval($level_plus['LEVEL_PLUS_GREEN']);
            }
          break;

          default:
          break;
        }

        $level_plus['LEVEL_PLUS_GREEN_TEXT'] = ($level_plus['LEVEL_PLUS_GREEN'] > 0 ? '+' : '') . pretty_number($level_plus['LEVEL_PLUS_GREEN']);
        $level_plus['LEVEL_PLUS_YELLOW_TEXT'] = ($level_plus['LEVEL_PLUS_YELLOW'] > 0 ? '+' : '') . pretty_number($level_plus['LEVEL_PLUS_YELLOW']);
        $block_vars[] = array_merge($level_plus, array(
          'ID'         => $planet['id'],
          'TYPE'       => $planet['planet_type'],
          'LEVEL'      => $unit_level_plain == 0 && !$level_plus['LEVEL_PLUS_YELLOW'] && !$level_plus['LEVEL_PLUS_GREEN'] ? '-' : $unit_level_plain,
          'LEVEL_TEXT' => $unit_level_plain == 0 && !$level_plus['LEVEL_PLUS_YELLOW'] && !$level_plus['LEVEL_PLUS_GREEN'] ? '-' : pretty_number($unit_level_plain),
        ));
        $unit_count += $unit_level_plain;
        $unit_count_abs += $unit_level_plain + abs($level_plus['LEVEL_PLUS_YELLOW']) + abs($level_plus['LEVEL_PLUS_GREEN']);
      }

      if ($unit_count_abs) {
        $template->assign_block_vars('prods', array(
          'ID'    => $unit_id,
          'FIELD' => 'unit_' . $unit_id, // TODO Делать это прямо в темплейте
          'NAME'  => $lang['tech'][$unit_id],
          'MODE'  => $mode,
        ));

        foreach ($block_vars as $block_var) {
          $template->assign_block_vars('prods.planet', $block_var);
        }
        $unit_green = $imperiumStats['units'][$unit_id]['LEVEL_PLUS_GREEN'];
        $unit_yellow = $imperiumStats['units'][$unit_id]['LEVEL_PLUS_YELLOW'];
        $template->assign_block_vars('prods.planet', array(
          'ID'                     => 0,
          'LEVEL'                  => $unit_count,
          'LEVEL_TEXT'             => pretty_number($unit_count),
          'LEVEL_PLUS_GREEN'       => $unit_green,
          'LEVEL_PLUS_YELLOW'      => $unit_yellow,
          'LEVEL_PLUS_GREEN_TEXT'  => $unit_green == 0 ? '' : (($unit_green > 0 ? '+' : '') . pretty_number($unit_green)),
          'LEVEL_PLUS_YELLOW_TEXT' => $unit_yellow == 0 ? '' : (($unit_yellow > 0 ? '+' : '') . pretty_number($unit_yellow)),
          'PERCENT'                => $unit_is_factory ? '' : -1,
          'FACTORY'                => $unit_is_factory,
        ));
      }
    }
  }

  $template->assign_block_vars('planet', array_merge(array(
    'ID'   => 0,
    'NAME' => $lang['sys_total'],

    'FIELDS_CUR' => $imperiumStats['fields'],
    'FIELDS_MAX' => $imperiumStats['fields_max'],

    'METAL_CUR'  => pretty_number($imperiumStats['metal']),
    'METAL_PROD' => pretty_number($imperiumStats['metal_perhour']),

    'CRYSTAL_CUR'  => pretty_number($imperiumStats['crystal']),
    'CRYSTAL_PROD' => pretty_number($imperiumStats['crystal_perhour']),

    'DEUTERIUM_CUR'  => pretty_number($imperiumStats['deuterium']),
    'DEUTERIUM_PROD' => pretty_number($imperiumStats['deuterium_perhour']),

    'ENERGY_CUR' => pretty_number($imperiumStats['energy']),
    'ENERGY_MAX' => pretty_number($imperiumStats['energy_max']),

    'TEMP_MIN' => $imperiumStats['temp_min'],
    'TEMP_MAX' => $imperiumStats['temp_max'],
  )));


  $template->assign_vars(array(
    'COLONIES_CURRENT' => get_player_current_colonies($user),
    'COLONIES_MAX'     => get_player_max_colonies($user),

    'EXPEDITIONS_CURRENT' => fleet_count_flying($user['id'], MT_EXPLORE),
    'EXPEDITIONS_MAX'     => get_player_max_expeditons($user),

    'PLANET_DENSITY_RICHNESS_NORMAL'  => PLANET_DENSITY_RICHNESS_NORMAL,
    'PLANET_DENSITY_RICHNESS_AVERAGE' => PLANET_DENSITY_RICHNESS_AVERAGE,
    'PLANET_DENSITY_RICHNESS_GOOD'    => PLANET_DENSITY_RICHNESS_GOOD,
    'PLANET_DENSITY_RICHNESS_PERFECT' => PLANET_DENSITY_RICHNESS_PERFECT,

    'PAGE_HEADER' => $lang['imp_overview'],
  ));

  return $template;
}

/**
 * Store current mines load in DB
 *
 * @param array $user
 */
function imperiumStoreMinesLoad($user) {
  if (!sys_get_param('save_production') || !is_array($production = sys_get_param('percent')) || empty($production)) {
    return;
  }

  $sn_group_factories = sn_get_groups('factories');

  $query = [];
  foreach (DBStaticPlanet::db_planet_list_sorted($user, false, '*') as $planet) {
    foreach ($sn_group_factories as $factory_unit_id) {
      $unit_db_name_porcent = pname_factory_production_field_name($factory_unit_id);
      if (
        get_unit_param($factory_unit_id, P_MINING_IS_MANAGED)
        && isset($production[$factory_unit_id][$planet['id']])
        && ($actual_porcent = intval($production[$factory_unit_id][$planet['id']] / 10)) >= 0
        && $actual_porcent <= 10
        && $actual_porcent != $planet[$unit_db_name_porcent]
      ) {
        $query[$planet['id']][] = "{$unit_db_name_porcent} = {$actual_porcent}";
      }
    }
  }

  foreach ($query as $planet_id => $query_data) {
    DBStaticPlanet::db_planet_set_by_id($planet_id, implode(',', $query_data));
  }
}

/**
 * @param template    $template
 * @param array[]     $planets
 * @param classLocale $lang
 *
 * @return array
 */
function imperiumAssignFleetsAndCalculateTotal($template, &$planets, $lang) {
  $planet_density = sn_get_groups('planet_density');

  $imperiumStats = array();
  $imperiumStats['temp_min'] = 1000;
  $imperiumStats['temp_max'] = -999;

  $fleets = [];
  foreach ($planets as $planetId => &$planet) {
    $templatizedPlanet = tpl_parse_planet($planet, $fleets);

    foreach ([RES_METAL, RES_CRYSTAL, RES_DEUTERIUM] as $resourceId) {
      if (empty($templatizedPlanet['fleet_list']['own']['total'][$resourceId])) {
        $templatizedPlanet['RES_' . $resourceId] = 0;
      } else {
        $templatizedPlanet['RES_' . $resourceId] = $templatizedPlanet['fleet_list']['own']['total'][$resourceId];
        $templatizedPlanet['RES_' . $resourceId . '_TEXT'] = pretty_number($templatizedPlanet['fleet_list']['own']['total'][$resourceId]);
      }
    }

    $template->assign_block_vars('planet', array_merge($templatizedPlanet, array(
      'METAL_CUR'  => pretty_number($planet['metal'], true, $planet['caps']['total_storage'][RES_METAL]),
      'METAL_PROD' => pretty_number($planet['caps']['total'][RES_METAL]),

      'CRYSTAL_CUR'  => pretty_number($planet['crystal'], true, $planet['caps']['total_storage'][RES_CRYSTAL]),
      'CRYSTAL_PROD' => pretty_number($planet['caps']['total'][RES_CRYSTAL]),

      'DEUTERIUM_CUR'  => pretty_number($planet['deuterium'], true, $planet['caps']['total_storage'][RES_DEUTERIUM]),
      'DEUTERIUM_PROD' => pretty_number($planet['caps']['total'][RES_DEUTERIUM]),

      'ENERGY_CUR' => pretty_number($planet['caps'][RES_ENERGY][BUILD_CREATE] - $planet['caps'][RES_ENERGY][BUILD_DESTROY], true, true),
      'ENERGY_MAX' => pretty_number($planet['caps'][RES_ENERGY][BUILD_CREATE]),

      'TEMP_MIN' => $planet['temp_min'],
      'TEMP_MAX' => $planet['temp_max'],

      'DENSITY_CLASS'      => $planet['density_index'],
      'DENSITY_RICHNESS'   => $planet_density[$planet['density_index']][UNIT_PLANET_DENSITY_RICHNESS],
      'DENSITY_CLASS_TEXT' => $lang['uni_planet_density_types'][$planet['density_index']],
    )));

    $planet['fleet_list'] = $templatizedPlanet['fleet_list'];
    $planet['BUILDING_ID'] = $templatizedPlanet['BUILDING_ID'];
    $planet['hangar_que'] = $templatizedPlanet['hangar_que'];
    $planet['full_que'] = $templatizedPlanet;

    $imperiumStats['fields'] += $planet['field_current'];
    $imperiumStats['metal'] += $planet['metal'];
    $imperiumStats['crystal'] += $planet['crystal'];
    $imperiumStats['deuterium'] += $planet['deuterium'];
    $imperiumStats['energy'] += $planet['energy_max'] - $planet['energy_used'];

    $imperiumStats['fields_max'] += eco_planet_fields_max($planet);
    $imperiumStats['metal_perhour'] += $planet['caps']['total'][RES_METAL];
    $imperiumStats['crystal_perhour'] += $planet['caps']['total'][RES_CRYSTAL];
    $imperiumStats['deuterium_perhour'] += $planet['caps']['total'][RES_DEUTERIUM];
    $imperiumStats['energy_max'] += $planet['caps'][RES_ENERGY][BUILD_CREATE];

    $imperiumStats['temp_min'] = min($planet['temp_min'], $imperiumStats['temp_min']);
    $imperiumStats['temp_max'] = max($planet['temp_max'], $imperiumStats['temp_max']);
  }

  tpl_assign_fleet($template, $fleets);

  return $imperiumStats;
}

/**
 * @param template $template
 */
function templateFillPercent($template) {
  for ($i = 100; $i >= 0; $i -= 10) {
    $template->assign_block_vars('percent', array('PERCENT' => $i));
  }
}

/**
 * @param array $user
 *
 * @return array[]
 */
function getUpdatedUserPlanetsAndQues($user) {
  $planets = array();
  $ques = array();
  $planet_row_list = DBStaticPlanet::db_planet_list_sorted($user);
  foreach ($planet_row_list as $planet) {
    sn_db_transaction_start();
    $global_data = sys_o_get_updated($user, $planet['id'], SN_TIME_NOW, false, true);
    $planets[$planet['id']] = $global_data['planet'];
    $ques[$planet['id']] = $global_data['que'];
    sn_db_transaction_commit();
  }

  return array($planets, $ques);
}
