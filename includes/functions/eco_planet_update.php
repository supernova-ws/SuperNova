<?php

/*
 * PlanetResourceUpdate.php
 *
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Bit more optimization
 * 2.0 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *     [+] Full rewrote and optimization
 *
 */

use Planet\DBStaticPlanet;

function sys_o_get_updated($user, $planet, $UpdateTime, $simulation = false, $no_user_update = false) {
  sn_db_transaction_check(true);

  $no_data = array('user' => false, 'planet' => false, 'que' => false);

  if (!$planet) {
    return $no_data;
  }

  if (!$no_user_update) {
    $user = intval(is_array($user) && $user['id'] ? $user['id'] : $user);
    if (!$user) {
      // TODO - Убрать позже
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sys_o_get_updated() - USER пустой!</h1>');
      $backtrace = debug_backtrace();
      array_shift($backtrace);
      pdump($backtrace);
      die();
    }

    $user = db_user_by_id($user, !$simulation, '*', true);
  }

  if (empty($user['id'])) {
    return $no_data;
  }

  if (is_array($planet) && isset($planet['galaxy']) && $planet['galaxy']) {
    $planet = DBStaticPlanet::db_planet_by_vector($planet, '', !$simulation);
  } else {
    $planet = intval(is_array($planet) && isset($planet['id']) ? $planet['id'] : $planet);
    $planet = DBStaticPlanet::db_planet_by_id($planet, !$simulation);
  }
  if (!is_array($planet) || !isset($planet['id'])) {
    return $no_data;
  }

  $que = que_process($user, $planet, $UpdateTime);

  $ProductionTime = max(0, $UpdateTime - $planet['last_update']);
  $planet['prev_update'] = $planet['last_update'];
  $planet['last_update'] += $ProductionTime;

  // TODO ЭТО НАДО ДЕЛАТЬ ТОЛЬКО ПРИ СПЕЦУСЛОВИЯХ

//  if ($ProductionTime > 0)
  {
    $capsObj = new \Meta\Economic\ResourceCalculations();
    $capsObj->eco_get_planet_caps($user, $planet, 3600);
    $resources_increase = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );

    switch ($planet['planet_type']) {
      case PT_PLANET:
        foreach ($resources_increase as $resource_id => &$increment) {
          $resource_name = pname_resource_name($resource_id);

          $increment = $planet[$resource_name . '_perhour'] * $ProductionTime / 3600;
          $store_free = $planet[$resource_name . '_max'] - $planet[$resource_name];
          $increment = min($increment, max(0, $store_free));

          if ($planet[$resource_name] + $increment < 0 && !$simulation) {
            global $debug;
            $debug->warning("Player ID {$user['id']} have negative resources on ID {$planet['id']}.{$planet['planet_type']} [{$planet['galaxy']}:{$planet['system']}:{$planet['planet']}]. Difference {$planet[$resource_name]} of {$resource_name}", 'Negative Resources', 501);
          }
          $planet[$resource_name] += $increment;
        }
      break;

      case PT_MOON:
      default:
        $planet['metal_perhour'] = 0;
        $planet['crystal_perhour'] = 0;
        $planet['deuterium_perhour'] = 0;
        $planet['energy_used'] = 0;
        $planet['energy_max'] = 0;
      break;
    }

    // TODO пересчитывать размер планеты только при постройке чего-нибудь и при покупке сектора
    $planet['field_current'] = 0;
    $sn_group_build_allow = sn_get_groups('build_allow');
    if (is_array($sn_group_build_allow[$planet['planet_type']])) {
      foreach ($sn_group_build_allow[$planet['planet_type']] as $building_id) {
        $planet['field_current'] += mrc_get_level($user, $planet, $building_id, !$simulation, true);
      }
    }

    // Saving data if not a simulation
    if (!$simulation) {
      DBStaticPlanet::db_planet_set_by_id($planet['id'],
        "`last_update` = '{$planet['last_update']}', `field_current` = {$planet['field_current']},
    `metal` = `metal` + '{$resources_increase[RES_METAL]}', `crystal` = `crystal` + '{$resources_increase[RES_CRYSTAL]}', `deuterium` = `deuterium` + '{$resources_increase[RES_DEUTERIUM]}',
    `metal_perhour` = '{$planet['metal_perhour']}', `crystal_perhour` = '{$planet['crystal_perhour']}', `deuterium_perhour` = '{$planet['deuterium_perhour']}',
    `energy_used` = '{$planet['energy_used']}', `energy_max` = '{$planet['energy_max']}'"
      );
    }
  }

  return array('user' => $user, 'planet' => $planet, 'que' => $que);
}
