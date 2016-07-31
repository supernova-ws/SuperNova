<?php

/**
 * @version 2015-04-11 11:47:49 39b14.2
 * @copyright 2008-2015 Gorlum for Project "SuperNova.WS"
 */

defined('INSIDE') || die();

require_once('db/db_queries.php');

function db_change_units_perform($query, $tablename, $object_id) {
  $query = implode(',', $query);
  if($query && $object_id) {
    return classSupernova::db_upd_record_by_id($tablename == 'users' ? LOC_USER : LOC_PLANET, $object_id, $query);
  }
}

// TODO: THIS FUNCTION IS OBSOLETE AND SHOULD BE REPLACED!
// TODO - ТОЛЬКО ДЛЯ РЕСУРСОВ
// $unit_list should have unique entrances! Recompress non-uniq entrances before pass param!
function db_change_units(&$user, &$planet, $unit_list = array(), $query = null) {
  $query = is_array($query) ? $query : array(
    LOC_USER => array(),
    LOC_PLANET => array(),
  );

  $group = sn_get_groups('resources_loot');

  foreach($unit_list as $unit_id => $unit_amount) {
    if(!in_array($unit_id, $group)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: db_change_units() вызван для не-ресурсов!</h1>');
      pdump(debug_backtrace());
      die('db_change_units() вызван для не-ресурсов!');
    }

    if(!$unit_amount) {
      continue;
    }

    $unit_db_name = pname_resource_name($unit_id);

    $unit_location = sys_get_unit_location($user, $planet, $unit_id);

    // Changing value in object
    switch($unit_location) {
      case LOC_USER:
        $user[$unit_db_name] += $unit_amount;
        break;
      case LOC_PLANET:
        $planet[$unit_db_name] += $unit_amount;
        break;
    }

    $unit_amount = $unit_amount < 0 ? $unit_amount : "+{$unit_amount}"; // Converting positive unit_amount to string '+unit_amount'
    $query[$unit_location][$unit_id] = "`{$unit_db_name}`=`{$unit_db_name}`{$unit_amount}";
  }

  db_change_units_perform($query[LOC_USER], 'users', $user['id']);
  db_change_units_perform($query[LOC_PLANET], 'planets', $planet['id']);
}
function sn_db_perform($table, $values, $type = 'insert', $options = false) {
  $field_set = '';

  switch($type) {
    case 'delete':
      $query = 'DELETE FROM';
      break;

    case 'insert':
      $query = 'INSERT INTO';
      if(isset($options['__multi'])) {
        // Here we generate mass-insert set
        break;
      }
    case 'update':
      if(!$query) {
        $query = 'UPDATE';
      }

      foreach($values as $field => &$value) {
        $value_type = gettype($value);
        if ($value_type == TYPE_STRING) {
          $value = "'" . db_escape($value) . "'";
        }
        $value = "`{$field}` = {$value}";
      }
      $field_set = 'SET ' . implode(', ', $values);
      break;

  };

  $query .= " {$table} {$field_set}";
  return classSupernova::$db->doExecute($query);
}



function sn_db_field_set_is_safe(&$field_set) {
  return !empty($field_set['__IS_SAFE']);
}
function sn_db_field_set_safe_flag_clear(&$field_set) {
  unset($field_set['__IS_SAFE']);
}
function sn_db_field_set_safe_flag_set(&$field_set) {
  $field_set['__IS_SAFE'] = true;
}
function sn_db_field_set_make_safe($field_set, $serialize = false) {
  if(!is_array($field_set)) {
    die('$field_set is not an array!');
  }

  $result = array();
  foreach($field_set as $field => $value) {
    $field = db_escape(trim($field));
    switch (true) {
      case is_int($value):
      case is_double($value):
        break;

      case is_bool($value):
        $value = intval($value);
        break;

      case is_array($value):
      case is_object($value):
        $serialize ? $value = serialize($value) : die('$value is object or array with no $serialize');

      case is_string($value):
        $value = '"' . db_escape($value) . '"';
        break;

      case is_null($value):
        $value = 'NULL';
        break;

      default:
        die('unsupported operand type');
    }
    $result[$field] = $value;
  }

  sn_db_field_set_safe_flag_set($field_set);

  return $result;
}


function sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id = null) {
  return classSupernova::db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id);
}



function sn_db_transaction_check($transaction_should_be_started = null) {
  return classSupernova::$gc->db->getTransaction()->check($transaction_should_be_started);
}
function sn_db_transaction_start($level = '') {
  return classSupernova::$gc->db->getTransaction()->start($level);
}
function sn_db_transaction_commit() {
  return classSupernova::$gc->db->getTransaction()->commit();
}
function sn_db_transaction_rollback() {
  return classSupernova::$gc->db->getTransaction()->rollback();
}



function db_fetch(&$query) {
  return classSupernova::$gc->db->db_fetch($query);
}
function db_escape($unescaped_string) {
  return classSupernova::$gc->db->db_escape($unescaped_string);
}
