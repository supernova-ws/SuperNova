<?php

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;
use Universe\Universe;

function uni_create_planet_get_density($position_data, $user_row, $planet_sectors) {
  $density_list = sn_get_groups('planet_density');
  $density_min = reset($density_list);
  unset($density_list[PLANET_DENSITY_NONE]);

  $possible_cores = array();
  $probability = 0;
  foreach ($density_list as $possible_core_id => $core_data) {
    if (!$core_data[UNIT_PLANET_DENSITY_RARITY]) {
      continue;
    }

    if (
      // Core type exists
      in_array($possible_core_id, $position_data['core_types'])
      // Limit core type with planet sector count
      && $planet_sectors < $density_list[$possible_core_id][UNIT_PLANET_DENSITY_MAX_SECTORS]
      // Limit core type with player AstroTech level
      && (empty($user_row) || mrc_get_level($user_row, null, TECH_ASTROTECH) >= $density_list[$possible_core_id][UNIT_PLANET_DENSITY_MIN_ASTROTECH])
    ) {
      // Фильтруем типы ядер, которые не подходят по размеру планеты
      $probability += $density_list[$possible_core_id][UNIT_PLANET_DENSITY_RARITY];
      $possible_cores[$possible_core_id] = array(
        UNIT_PLANET_DENSITY_INDEX  => $possible_core_id,
        UNIT_PLANET_DENSITY_RARITY => $probability,
        UNIT_PLANET_DENSITY        => mt_rand($density_min[UNIT_PLANET_DENSITY], $density_list[$possible_core_id][UNIT_PLANET_DENSITY] - 1),
      );
    }
    $density_min = $density_list[$possible_core_id];
  }

  $random = mt_rand(1, $probability);
  $selected_core = null;
  foreach ($possible_cores as $core_type => $core_info) {
    if ($random <= $core_info[UNIT_PLANET_DENSITY_RARITY]) {
      $selected_core = $core_info;
      break;
    }
  }

  return $selected_core;
}

/**
 * @param int        $Galaxy
 * @param int        $System
 * @param int        $Position
 * @param int        $PlanetOwnerID
 * @param string     $planet_name_unsafe
 * @param bool|false $HomeWorld
 * @param array      $options = [
 *   'skip_check' => true,
 *   'user_row' => [],
 *   'force_name' => (string), // Force full planet name
 *   'image' => (string), // Force image
 * ]
 *
 * @return bool
 */
function uni_create_planet($Galaxy, $System, $Position, $PlanetOwnerID, $planet_name_unsafe = '', $HomeWorld = false, $options = []) {
  $Position = intval($Position);

  if (!isset($options['skip_check']) && DBStaticPlanet::db_planet_by_gspt($Galaxy, $System, $Position, PT_PLANET, true, '`id`')) {
    return false;
  }

  $user_row = !empty($options['user_row']) && is_array($options['user_row']) ? $options['user_row'] : db_user_by_id($PlanetOwnerID);


  $planet_generator = sn_get_groups('planet_generator');

  if ($HomeWorld) {
    $position_data = $planet_generator[0];
  } else {
    $position_data = $planet_generator[$Position >= UNIVERSE_RANDOM_PLANET_START || $Position < 1 ? UNIVERSE_RANDOM_PLANET_START : $Position];
    if ($Position >= UNIVERSE_RANDOM_PLANET_START) {
      // Корректируем температуру для планеты-странника
      $position_data['t_max_max'] -= UNIVERSE_RANDOM_PLANET_TEMPERATURE_DECREASE * ($Position - UNIVERSE_RANDOM_PLANET_START);
    }
  }

  if (!empty($options['image'])) {
    $planet_image = $options['image'];
  } else {
    $planet_images = sn_get_groups('planet_images');
    $planet_image = $position_data['planet_images'][mt_rand(0, count($position_data['planet_images']) - 1)];
    $planet_image .= 'planet' . $planet_images[$planet_image][mt_rand(0, count($planet_images[$planet_image]) - 1)];
  }

  $t_max = sn_rand_gauss_range($position_data['t_max_min'], $position_data['t_max_max'], true, 1.3, true);
  $t_min = $t_max - sn_rand_gauss_range($position_data['t_delta_min'], $position_data['t_delta_max'], true, 1.3, true);

  $planet_sectors = sn_rand_gauss_range($position_data['size_min'], $position_data['size_max'], true, 1.7, true);
//  $planet_diameter = round(pow($planet_sectors, 2) * 1000);
  $planet_diameter = round(sqrt($planet_sectors) * 1000);

  $core_info = uni_create_planet_get_density($position_data, $user_row, $planet_sectors);

  $planet_name_unsafe = !empty($options['force_name']) ? $options['force_name'] :
    ($user_row['username'] . ' ' . (
      $HomeWorld
        ? SN::$lang['sys_capital']
        : ($planet_name_unsafe ? $planet_name_unsafe : SN::$lang['sys_colo_defaultname'])
      )
    );

  $planet['name'] = db_escape(strip_tags(trim($planet_name_unsafe)));
  $planet['id_owner'] = $PlanetOwnerID;
  $planet['last_update'] = SN_TIME_NOW;
  $planet['image'] = $planet_image;

  $planet['galaxy'] = $Galaxy;
  $planet['system'] = $System;
  $planet['planet'] = $planet['position_original'] = $Position;
  $planet['planet_type'] = PT_PLANET;

  $planet['diameter'] = $planet_diameter;
  $planet['field_max'] = $planet['field_max_original'] = $planet_sectors;
  $planet['density'] = $core_info[UNIT_PLANET_DENSITY];
  $planet['density_index'] = $core_info[UNIT_PLANET_DENSITY_INDEX];
  $planet['temp_min'] = $planet['temp_min_original'] = $t_min;
  $planet['temp_max'] = $planet['temp_max_original'] = $t_max;

  $planet['metal'] = SN::$config->eco_planet_starting_metal;
  $planet['crystal'] = SN::$config->eco_planet_starting_crystal;
  $planet['deuterium'] = SN::$config->eco_planet_starting_deuterium;
  $planet['metal_max'] = SN::$config->eco_planet_storage_metal;
  $planet['crystal_max'] = SN::$config->eco_planet_storage_crystal;
  $planet['deuterium_max'] = SN::$config->eco_planet_storage_deuterium;

  $density_info_resources = &$density_list[$core_info[UNIT_PLANET_DENSITY_INDEX]][UNIT_RESOURCES];
  $planet['metal_perhour'] = SN::$config->metal_basic_income * $density_info_resources[RES_METAL];
  $planet['crystal_perhour'] = SN::$config->crystal_basic_income * $density_info_resources[RES_CRYSTAL];
  $planet['deuterium_perhour'] = SN::$config->deuterium_basic_income * $density_info_resources[RES_DEUTERIUM];

  $RetValue = SN::db_ins_record(LOC_PLANET,
    "`name` = '{$planet['name']}', `id_owner` = '{$planet['id_owner']}', `last_update` = '{$planet['last_update']}', `image` = '{$planet['image']}',
      `galaxy` = '{$planet['galaxy']}', `system` = '{$planet['system']}', `planet` = '{$planet['planet']}', `planet_type` = '{$planet['planet_type']}', `position_original` = '{$planet['position_original']}',
      `diameter` = '{$planet['diameter']}', `field_max` = '{$planet['field_max']}', `field_max_original` = '{$planet['field_max_original']}',
      `density` = '{$planet['density']}', `density_index` = '{$planet['density_index']}',
      `temp_min` = '{$planet['temp_min']}', `temp_max` = '{$planet['temp_max']}', `temp_min_original` = '{$planet['temp_min_original']}', `temp_max_original` = '{$planet['temp_max_original']}',
      `metal` = '{$planet['metal']}', `metal_perhour` = '{$planet['metal_perhour']}', `metal_max` = '{$planet['metal_max']}',
      `crystal` = '{$planet['crystal']}', `crystal_perhour` = '{$planet['crystal_perhour']}', `crystal_max` = '{$planet['crystal_max']}',
      `deuterium` = '{$planet['deuterium']}', `deuterium_perhour` = '{$planet['deuterium_perhour']}', `deuterium_max` = '{$planet['deuterium_max']}'"
  );

  return is_array($RetValue) ? $RetValue['id'] : false; // OK
}

/**
 * uni_create_moon.php
 *
 * UNI: Create moon record
 *
 * V2.1  - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
 *   [~] Renamed CreateOneMoonRecord to uni_create_moon
 *   [-] Removed unsed $MoonID parameter from call
 *   [~] PCG1 compliant
 * V2.0  - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] Deep rewrite to rid of using `galaxy` and `lunas` tables greatly reduce numbers of SQL-queries
 * @version   1.1
 * @copyright 2008
 */

/**
 * @param        $pos_galaxy
 * @param        $pos_system
 * @param        $pos_planet
 * @param        $user_id
 * @param int    $size <p><b>0</b> - random moon size</p>
 * @param bool   $update_debris
 * @param array  $options ['name' => (str), 'image' => (str)]
 *
 * @return array
 */
function uni_create_moon($pos_galaxy, $pos_system, $pos_planet, $user_id, $size = 0, $update_debris = true, $options = []) {
  global $lang;

  $moon_row = [];
  $moon = DBStaticPlanet::db_planet_by_gspt($pos_galaxy, $pos_system, $pos_planet, PT_MOON, false, 'id');
  if (empty($moon['id'])) {
    $moon_planet = DBStaticPlanet::db_planet_by_gspt($pos_galaxy, $pos_system, $pos_planet, PT_PLANET, true, '`id`, `temp_min`, `temp_max`, `name`, `debris_metal`, `debris_crystal`');

    if ($moon_planet['id']) {
      $base_storage_size = BASE_STORAGE_SIZE;

      empty($size) ? $size = Universe::moonSizeRandom() : false;

      $temp_min = $moon_planet['temp_min'] - rand(10, 45);
      $temp_max = $temp_min + 40;

      $moon_name = !empty($options['name']) ? $options['name'] : "{$moon_planet['name']} {$lang['sys_moon']}";
      $moon_name_safe = db_escape($moon_name);

      $field_max = ceil($size / 1000);

      $moon_image = !empty($options['image']) ? $options['image'] : 'mond';

      $moon_row = SN::db_ins_record(LOC_PLANET,
        "`id_owner` = '{$user_id}', `parent_planet` = '{$moon_planet['id']}', `name` = '{$moon_name_safe}', `last_update` = " . SN_TIME_NOW . ", `image` = '{$moon_image}',
          `galaxy` = '{$pos_galaxy}', `system` = '{$pos_system}', `planet` = '{$pos_planet}', `planet_type` = " . PT_MOON . ",
          `diameter` = '{$size}', `field_max` = '{$field_max}', `density` = 2500, `density_index` = 2, `temp_min` = '{$temp_min}', `temp_max` = '{$temp_max}',
          `metal` = '0', `metal_perhour` = '0', `metal_max` = '{$base_storage_size}',
          `crystal` = '0', `crystal_perhour` = '0', `crystal_max` = '{$base_storage_size}',
          `deuterium` = '0', `deuterium_perhour` = '0', `deuterium_max` = '{$base_storage_size}'"
      );

      if ($update_debris) {
        $debris_spent = round($size / 1000 * Universe::moonPercentCostInDebris());
        $metal_spent = round(min($moon_planet['debris_metal'], $debris_spent * mt_rand(50 * 1000, 75 * 1000) / (100 * 1000))); // Trick for higher mt_rand resolution
        $crystal_spent = min($moon_planet['debris_crystal'], $debris_spent - $metal_spent);
        $metal_spent = min($moon_planet['debris_metal'], $debris_spent - $crystal_spent); // Need if crystal less then their part
        DBStaticPlanet::db_planet_set_by_id($moon_planet['id'], "`debris_metal` = GREATEST(0, `debris_metal` - {$metal_spent}), `debris_crystal` = GREATEST(0, `debris_crystal` - {$crystal_spent})");
      }
    }
  }

  return $moon_row;
}

/*
 *
 * @function SetSelectedPlanet
 *
 * @history
 *
 * 4 - copyright (c) 2014 by Gorlum for http://supernova.ws
 *   [!] Full rewrote from scratch
 * 3 - copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *   [+] Added handling case when current_planet does not exists or didn't belong to user
 *   [+] Moved from SetSelectedPlanet.php
 *   [+] Function now return
 *   [~] Complies with PCG1
 * 2 - copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *   [~] Security checked for SQL-injection
 * 1 - copyright 2008 By Chlorel for XNova
 *
 */
function SetSelectedPlanet(&$user) {
  $planet_row['id'] = $user['current_planet'];

  // Пытаемся переключить на новую планету
  if (($selected_planet = sys_get_param_id('cp')) && $selected_planet != $user['current_planet']) {
    $planet_row = DBStaticPlanet::db_planet_by_id_and_owner($selected_planet, $user['id'], false, 'id');
  } else {
    $planet_row = DBStaticPlanet::db_planet_by_id($planet_row['id']);
  }

  // Если новая планета не найдена или было переключения - проверяем текущую выбранную планету
  if (!isset($planet_row['id'])) // || $planet_row['id'] != $user['current_planet']
  {
    $planet_row = DBStaticPlanet::db_planet_by_id_and_owner($user['current_planet'], $user['id'], false, 'id');
    // Если текущей планеты не существует - выставляем Столицу
    if (!isset($planet_row['id'])) {
      $planet_row = DBStaticPlanet::db_planet_by_id_and_owner($user['id_planet'], $user['id'], false, 'id');
      // Если и столицы не существует - значит что-то очень не так с записью пользователя
      if (!isset($planet_row['id'])) {
        global $debug;
        $debug->error("User ID {$user['id']} has Capital planet {$user['id_planet']} but this planet does not exists", 'User record error', 502);
      }
    }
  }

  // Если производилось переключение планеты - делаем запись в юзере
  if ($user['current_planet'] != $planet_row['id']) {
    db_user_set_by_id($user['id'], "`current_planet` = '{$planet_row['id']}'");
    $user['current_planet'] = $planet_row['id'];
  }

  return $user['current_planet'];
}

// ----------------------------------------------------------------------------------------------------------------
function uni_render_coordinates($from, $prefix = '') {
  return "[{$from[$prefix . 'galaxy']}:{$from[$prefix . 'system']}:{$from[$prefix . 'planet']}]";
}

function uni_render_planet($from) {
  return "{$from['name']} [{$from['galaxy']}:{$from['system']}:{$from['planet']}]";
}

function uni_render_planet_full($from, $prefix = '', $html_safe = true, $include_id = false) {
  global $lang;

  if (!$from['id']) {
    $result = $lang['sys_planet_expedition'];
  } else {
    $from_planet_id = $include_id ? (
      'ID {' . ($from['id'] ? $from['id'] : ($from[$prefix . 'planet_id'] ? $from[$prefix . 'planet_id'] : 0)) . '} '
    ) : '';

    $from_planet_type = $from['planet_type'] ? $from['planet_type'] : ($from[$prefix . 'type'] ? $from[$prefix . 'type'] : 0);
    $from_planet_type = ($from_planet_type ? ' ' . $lang['sys_planet_type_sh'][$from_planet_type] : '');

    $result = $from_planet_id . uni_render_coordinates($from, $prefix) . $from_planet_type . ($from['name'] ? ' ' . $from['name'] : '');
    $result = $html_safe ? HelperString::htmlEncode($result, HTML_ENCODE_PREFORM | HTML_ENCODE_SPACE) : $result;
  }

  return $result;
}

/**
 * @param \Planet\Planet $from
 *
 * @return string
 */
function uni_render_coordinates_planet_object($from) {
  return is_object($from) ? "[{$from->galaxy}:{$from->system}:{$from->planet}]" : '[-:-:-]';
}


/**
 * @param \Planet\Planet $from
 * @param bool           $html_safe
 * @param bool           $include_id
 *
 * @return mixed|null|string
 */
function uni_render_planet_object_full($from, $html_safe = true, $include_id = false) {
  if (empty($from->id)) {
    $result = SN::$lang['sys_planet_expedition'];
  } else {
    $from_planet_id = $include_id ? (
      'ID {' . ($from->id ? $from->id : 0) . '} '
    ) : '';

    $from_planet_type = isset($from->planet_type) ? $from->planet_type : 0;
    $from_planet_type = ($from_planet_type ? ' ' . SN::$lang['sys_planet_type_sh'][$from_planet_type] : '');

    $result = $from_planet_id . uni_render_coordinates_planet_object($from) . $from_planet_type . (isset($from->name) ? ' ' . $from->name : '');
    $result = $html_safe ? HelperString::htmlEncode($result, HTML_ENCODE_PREFORM | HTML_ENCODE_SPACE) : $result;
  }

  return $result;
}

function uni_render_coordinates_url($from, $prefix = '', $page = 'galaxy.php') {
  return $page . (strpos($page, '?') === false ? '?' : '&') . "galaxy={$from[$prefix . 'galaxy']}&system={$from[$prefix . 'system']}&planet={$from[$prefix . 'planet']}";
}

function uni_render_coordinates_href($from, $prefix = '', $mode = 0, $fleet_type = '') {
  return '<a href="' . uni_render_coordinates_url($from, $prefix, "galaxy.php?mode={$mode}") . '"' . ($fleet_type ? " {$fleet_type}" : '') . '>' . uni_render_coordinates($from, $prefix) . '</a>';
}

function uni_get_time_to_jump($moon_row) {
  $jump_gate_level = mrc_get_level($user, $moon_row, STRUC_MOON_GATE);

  return $jump_gate_level ? max(0, $moon_row['last_jump_time'] + abs(60 * 60 / $jump_gate_level) - SN_TIME_NOW) : 0;
}

function uni_coordinates_valid($coordinates, $prefix = '') {
  global $config;

  // array_walk($coordinates, 'intval');
  $coordinates["{$prefix}galaxy"] = intval($coordinates["{$prefix}galaxy"]);
  $coordinates["{$prefix}system"] = intval($coordinates["{$prefix}system"]);
  $coordinates["{$prefix}planet"] = intval($coordinates["{$prefix}planet"]);

  return
    isset($coordinates["{$prefix}galaxy"]) && $coordinates["{$prefix}galaxy"] > 0 && $coordinates["{$prefix}galaxy"] <= $config->game_maxGalaxy &&
    isset($coordinates["{$prefix}system"]) && $coordinates["{$prefix}system"] > 0 && $coordinates["{$prefix}system"] <= $config->game_maxSystem &&
    isset($coordinates["{$prefix}planet"]) && $coordinates["{$prefix}planet"] > 0 && $coordinates["{$prefix}planet"] <= $config->game_maxPlanet;
}

function uni_planet_teleport_check($user, $planetrow, $new_coordinates = null) {
  global $lang, $config;

  try {
    if ($planetrow['planet_teleport_next'] && $planetrow['planet_teleport_next'] > SN_TIME_NOW) {
      throw new exception($lang['ov_teleport_err_cooldown'], ERR_ERROR);
    }

    if (mrc_get_level($user, false, RES_DARK_MATTER) < $config->planet_teleport_cost) {
      throw new exception($lang['ov_teleport_err_no_dark_matter'], ERR_ERROR);
    }

    // TODO: Replace quick-check with using gathered flying fleet data
//    $incoming = doquery("SELECT COUNT(*) AS incoming FROM {{fleets}} WHERE
//      (fleet_start_galaxy = {$planetrow['galaxy']} and fleet_start_system = {$planetrow['system']} and fleet_start_planet = {$planetrow['planet']})
//      or
//      (fleet_end_galaxy = {$planetrow['galaxy']} and fleet_end_system = {$planetrow['system']} and fleet_end_planet = {$planetrow['planet']})", true);
//    if(!empty($incoming['incoming'])) {
//      throw new exception($lang['ov_teleport_err_fleet'], ERR_ERROR);
//    }
    if (DbFleetStatic::fleet_count_incoming($planetrow['galaxy'], $planetrow['system'], $planetrow['planet'])) {
      throw new exception($lang['ov_teleport_err_fleet'], ERR_ERROR);
    }

    //$incoming = doquery("SELECT COUNT(*) AS incoming FROM {{iraks}} WHERE fleet_end_galaxy = {$planetrow['galaxy']} and fleet_end_system = {$planetrow['system']} and fleet_end_planet = {$planetrow['planet']}", true);
    //if($incoming['incoming']) {
    //  throw new exception($lang['ov_teleport_err_fleet'], ERR_ERROR);
    //}

    if (is_array($new_coordinates)) {
      $new_coordinates['planet_type'] = PT_PLANET;
      $incoming = DBStaticPlanet::db_planet_by_vector($new_coordinates, '', true, 'id');
      if ($incoming['id']) {
        throw new exception($lang['ov_teleport_err_destination_busy'], ERR_ERROR);
      }
    }

    $response = array(
      'result'  => ERR_NONE,
      'message' => '',
    );
  } catch (exception $e) {
    $response = array(
      'result'  => $e->getCode(),
      'message' => $e->getMessage(),
    );
  }

  return $response;
}
