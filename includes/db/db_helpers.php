<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 26.12.2015
 * Time: 17:19
 */

use Planet\DBStaticPlanet;

/**
 * Normalize and make ID safe
 *
 * @param $db_row
 *
 * @return float|int
 */
function db_normalize_id($db_row, $field_name = 'id') {
  return idval(is_array($db_row) && !empty($db_row[$field_name]) ? $db_row[$field_name] : $db_row);
}

/**
 * Makes set safe
 *
 * @param array $set
 * @param bool $delta - Is it delta set?
 *
 * @return string
 */
function db_set_make_safe_string($set, $delta = false) {
  $set_safe = array();
  foreach($set as $field => $value) {
    if(empty($field)) {
      continue;
    }

    $field = '`' . db_escape($field) . '`';
    $new_value = $value;
    if($value === null) {
      $new_value = 'NULL';
    } elseif(is_string($value) && (string)($new_value = floatval($value)) != (string)$value) {
      // non-float
      $new_value = '"' . db_escape($value) . '"';
    } elseif($delta) {
      // float and DELTA-set
      $new_value = "{$field} + ({$new_value})";
    }
    $set_safe[] = "{$field} = {$new_value}";
  }

  $set_safe = implode(',', $set_safe);

  return $set_safe;
}

/**
 * Converts IRAK table record to FLEET one
 *
 * @param array $missile_db_list
 * @param array $fleet_db_list
 */
function missile_list_convert_to_fleet(&$missile_db_list, &$fleet_db_list) {
  // Missile attack
  foreach($missile_db_list as $irak) {
    if($irak['fleet_end_time'] >= SN_TIME_NOW) {
      $irak['fleet_start_type'] = PT_PLANET;
      $planet_start = DBStaticPlanet::db_planet_by_vector($irak, 'fleet_start_', false, 'name');
      $irak['fleet_id'] = -$irak['id'];
      $irak['fleet_mission'] = MT_MISSILE;
      $irak['fleet_array'] = UNIT_DEF_MISSILE_INTERPLANET . ",{$irak['fleet_amount']};";
      $irak['fleet_start_name'] = $planet_start['name'];
    }
    $fleet_db_list[] = $irak;
  }
}

/**
 * Get current DB patch version
 *
 * @return int|null
 */
function dbPatchGetCurrent() {
  return SN::$db->selectValue("SELECT MAX(`id`) FROM {{server_patches}}");
}