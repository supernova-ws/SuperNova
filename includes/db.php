<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

/**
 * @version   2015-04-11 11:47:49 39b14.2
 * @copyright 2008-2015 Gorlum for Project "SuperNova.WS"
 */

if (!defined('INSIDE')) {
  die();
}

require_once('db/db_queries.php');

/**
 * @param $query
 * @param $tableName
 * @param $object_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_change_units_perform($query, $tableName, $object_id) {
  $query = implode(',', $query);
  if ($query && $object_id) {
    return SN::db_upd_record_by_id($tableName == 'users' ? LOC_USER : LOC_PLANET, $object_id, $query);
  }

  return null;
}

/**
 * @param $user
 * @param $planet
 * @param $unit_list
 * @param $query
 *
// TODO: THIS FUNCTION IS OBSOLETE AND SHOULD BE REPLACED!
// TODO - ТОЛЬКО ДЛЯ РЕСУРСОВ
// $unit_list should have unique entrances! Recompress non-uniq entrances before pass param!
 * @return void
 */

function db_change_units(&$user, &$planet, $unit_list = [], $query = null) {
  $query = is_array($query) ? $query : [
    LOC_USER   => [],
    LOC_PLANET => [],
  ];

  $group = sn_get_groups('resources_loot');

  foreach ($unit_list as $unit_id => $unit_amount) {
    if (!in_array($unit_id, $group)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: db_change_units() вызван для не-ресурсов!</h1>');
      pdump(debug_backtrace());
      die('db_change_units() вызван для не-ресурсов!');
    }

    if (!$unit_amount) {
      continue;
    }

    $unit_db_name = pname_resource_name($unit_id);

    $unit_location = sys_get_unit_location($user, $planet, $unit_id);

    // Changing value in object
    switch ($unit_location) {
      case LOC_USER:
        $user[$unit_db_name] += $unit_amount;
      break;
      case LOC_PLANET:
        $planet[$unit_db_name] += $unit_amount;
      break;
    }

    $unit_amount                     = $unit_amount < 0 ? $unit_amount : "+{$unit_amount}"; // Converting positive unit_amount to string '+unit_amount'
    $query[$unit_location][$unit_id] = "`{$unit_db_name}`=`{$unit_db_name}`{$unit_amount}";
  }

  db_change_units_perform($query[LOC_USER], 'users', $user['id']);
  db_change_units_perform($query[LOC_PLANET], 'planets', $planet['id']);
}

/* Pass-through functions ******************************************************************************************* */
/**
 * @param        $query
 * @param string $table
 * @param bool   $fetch
 * @param bool   $skip_query_check
 *
 * @return array|bool|mysqli_result|null
 *
 * @deprecated
 */
function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
  if (!is_string($table)) {
    $fetch = $table;
  }

  return SN::$db->doquery($query, $fetch, $skip_query_check);
}

/**
 * @param $query
 *
 * @return array|null
 *
 * @deprecated
 */
function db_fetch(&$query) {
  return SN::$db->db_fetch($query);
}

/**
 * @param $unescaped_string
 *
 * @return string
 *
 * @deprecated
 */
function db_escape($unescaped_string) {
  return SN::$db->db_escape($unescaped_string);
}
