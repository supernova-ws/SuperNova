<?php
/**
 * Created by Gorlum 04.12.2017 5:04
 */

// PLANET FUNCTIONS ----------------------------------------------------------------------------------------------------------------
function eco_planet_fields_max($planet) {
  return $planet['field_max'] + ($planet['planet_type'] == PT_PLANET ? mrc_get_level($user, $planet, STRUC_TERRAFORMER) * 5 : (mrc_get_level($user, $planet, STRUC_MOON_STATION) * 3));
}

function GetPhalanxRange($phalanx_level) {
  return $phalanx_level > 1 ? pow($phalanx_level, 2) - 1 : 0;
}

/**
 * @param array $planet
 *
 * @return bool
 */
function CheckAbandonPlanetState(&$planet) {
  if ($planet['destruyed'] && $planet['destruyed'] <= SN_TIME_NOW) {
    DBStaticPlanet::db_planet_delete_by_id($planet['id']);

    return true;
  }

  return false;
}

function planet_density_price_chart($planet_row) {
  $sn_data_density = sn_get_groups('planet_density');
  $density_price_chart = array();

  foreach ($sn_data_density as $density_id => $density_data) {
    // Отсекаем записи с RARITY = 0 - служебные записи и супер-ядра
    $density_data[UNIT_PLANET_DENSITY_RARITY] ? $density_price_chart[$density_id] = $density_data[UNIT_PLANET_DENSITY_RARITY] : false;
  }
  unset($density_price_chart[PLANET_DENSITY_NONE]);

  $total_rarity = array_sum($density_price_chart);

  foreach ($density_price_chart as &$density_data) {
    $density_data = ceil($total_rarity / $density_data * $planet_row['field_max'] * PLANET_DENSITY_TO_DARK_MATTER_RATE);
  }

  return $density_price_chart;
}

function sn_sys_sector_buy($redirect = 'overview.php') {
  global $lang, $user, $planetrow;

  if (!sys_get_param_str('sector_buy') || $planetrow['planet_type'] != PT_PLANET) {
    return;
  }

  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true, '*');
  $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
  // Тут не надо делать обсчет - ресурсы мы уже посчитали, очередь (и количество зданий) - тоже
//  $planetrow = sys_o_get_updated($user, $planetrow, SN_TIME_NOW);
//  $user = $planetrow['user'];
//  $planetrow = $planetrow['planet'];
  $sector_cost = eco_get_build_data($user, $planetrow, UNIT_SECTOR, mrc_get_level($user, $planetrow, UNIT_SECTOR), true);
  $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
  if ($sector_cost <= mrc_get_level($user, null, RES_DARK_MATTER)) {
    $planet_name_text = uni_render_planet($planetrow);
    if (rpg_points_change($user['id'], RPG_SECTOR, -$sector_cost, sprintf($lang['sys_sector_purchase_log'],
        $user['username'], $user['id'], $planet_name_text, $lang['sys_planet_type'][$planetrow['planet_type']], $planetrow['id'], $sector_cost)
    )) {
      $sector_db_name = pname_resource_name(UNIT_SECTOR);
      DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "{$sector_db_name} = {$sector_db_name} + 1");
    } else {
      sn_db_transaction_rollback();
    }
  }
  sn_db_transaction_commit();

  sys_redirect($redirect);
}

function sn_sys_planet_core_transmute(&$user, &$planetrow) {
  if (!sys_get_param_str('transmute')) {
    return array();
  }

  global $lang;

  try {
    if ($planetrow['planet_type'] != PT_PLANET) {
      throw new exception($lang['ov_core_err_not_a_planet'], ERR_ERROR);
    }

    if ($planetrow['density_index'] == ($new_density_index = sys_get_param_id('density_type'))) {
      throw new exception($lang['ov_core_err_same_density'], ERR_WARNING);
    }

    sn_db_transaction_start();
    $user = db_user_by_id($user['id'], true, '*');
    $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');

    $planet_density_index = $planetrow['density_index'];

    $density_price_chart = planet_density_price_chart($planetrow);
    if (!isset($density_price_chart[$new_density_index])) {
      // Hack attempt
      throw new exception($lang['ov_core_err_denisty_type_wrong'], ERR_ERROR);
    }

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $transmute_cost = $density_price_chart[$new_density_index];
    if ($user_dark_matter < $transmute_cost) {
      throw new exception($lang['ov_core_err_no_dark_matter'], ERR_ERROR);
    }

    $sn_data_planet_density = sn_get_groups('planet_density');
    foreach ($sn_data_planet_density as $key => $value) {
      if ($key == $new_density_index) {
        break;
      }
      $prev_density_index = $key;
    }

    $new_density = round(($sn_data_planet_density[$new_density_index][UNIT_PLANET_DENSITY] + $sn_data_planet_density[$prev_density_index][UNIT_PLANET_DENSITY]) / 2);

    rpg_points_change($user['id'], RPG_PLANET_DENSITY_CHANGE, -$transmute_cost,
      array(
        'Planet %1$s ID %2$d at coordinates %3$s changed density type from %4$d "%5$s" to %6$d "%7$s". New density is %8$d kg/m3',
        $planetrow['name'],
        $planetrow['id'],
        uni_render_coordinates($planetrow),
        $planet_density_index,
        $lang['uni_planet_density_types'][$planet_density_index],
        $new_density_index,
        $lang['uni_planet_density_types'][$new_density_index],
        $new_density
      )
    );

    DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`density` = {$new_density}, `density_index` = {$new_density_index}");
    sn_db_transaction_commit();

    $planetrow['density'] = $new_density;
    $planetrow['density_index'] = $new_density_index;
    $result = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => sprintf($lang['ov_core_err_none'], $lang['uni_planet_density_types'][$planet_density_index], $lang['uni_planet_density_types'][$new_density_index], $new_density),
    );
  } catch (exception $e) {
    sn_db_transaction_rollback();
    $result = array(
      'STATUS'  => $e->getCode(),
      'MESSAGE' => $e->getMessage(),
    );
  }

  return $result;
}

function can_capture_planet() { return sn_function_call('can_capture_planet', array(&$result)); }

function sn_can_capture_planet(&$result) {
  return $result = false;
}
