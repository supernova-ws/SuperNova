<?php

/**
 * @version 2015-04-11 11:47:49 39b14.2
 * @copyright 2008-2015 Gorlum for Project "SuperNova.WS"
 */

if(!defined('INSIDE')) {
  die();
}

require_once('db/db_queries.php');

function db_change_units_perform($query, $tablename, $object_id) {
  $query = implode(',', $query);
  if($query && $object_id) {
    return classSupernova::db_upd_record_by_id($tablename == 'users' ? LOC_USER : LOC_PLANET, $object_id, $query);
    // return doquery("UPDATE {{{$tablename}}} SET {$query} WHERE `id` = '{$object_id}' LIMIT 1;");
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
  $mass_perform = false;

  $field_set = '';
  $value_set = '';

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
        if ($value_type == 'string') {
          $value = "'" . db_escape($value) . "'";
        }
        $value = "`{$field}` = {$value}";
      }
      $field_set = 'SET ' . implode(', ', $values);
      break;

  };

  $query .= " {$table} {$field_set}";
  return doquery($query);
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
function db_field_set_create($table_name, $field_set) {
  !sn_db_field_set_is_safe($field_set) ? $field_set = sn_db_field_set_make_safe($field_set) : false;
  sn_db_field_set_safe_flag_clear($field_set);

  $values = implode(',', $field_set);
  $fields = implode(',', array_keys($field_set));

  return classSupernova::db_query("INSERT INTO `{{{$table_name}}}` ($fields) VALUES ($values);");
}


function sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id = null) {
  return classSupernova::db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id);
}
function db_changeset_apply($db_changeset) {
  return classSupernova::db_changeset_apply($db_changeset);
}

function sn_db_transaction_check($transaction_should_be_started = null) {
  return classSupernova::db_transaction_check($transaction_should_be_started);
}
function sn_db_transaction_start($level = '') {
  return classSupernova::db_transaction_start($level);
}
function sn_db_transaction_commit() {
  return classSupernova::db_transaction_commit();
}
function sn_db_transaction_rollback() {
  return classSupernova::db_transaction_rollback();
}




function db_error() {
  return classSupernova::$db->db_error();
}
function sn_db_connect() {
  return classSupernova::$db->sn_db_connect();
}
function sn_db_disconnect() {
  return classSupernova::$db->db_disconnect();
}
function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
  return classSupernova::$db->doquery($query, $table, $fetch, $skip_query_check);
}
function db_fetch(&$query) {
  return classSupernova::$db->db_fetch($query);
}
function db_fetch_row(&$query) {
  return classSupernova::$db->db_fetch_row($query);
}
function db_escape($unescaped_string) {
  return classSupernova::$db->db_escape($unescaped_string);
}
function db_insert_id() {
  return classSupernova::$db->db_insert_id();
}
function db_num_rows(&$result) {
  return classSupernova::$db->db_num_rows($result);
}
function db_affected_rows() {
  return classSupernova::$db->db_affected_rows();
}
// Информационные функции
function db_get_client_info() {
  return classSupernova::$db->db_get_client_info();
}
function db_get_server_info() {
  return classSupernova::$db->db_get_server_info();
}
function db_get_host_info() {
  return classSupernova::$db->db_get_host_info();
}
function db_server_stat() {
  return classSupernova::$db->db_get_server_stat();
}
function db_get_table_list($db_prefix) {
  return classSupernova::$db->db_get_table_list($db_prefix);
}


// Deprecated
function security_watch_user_queries($query) {
  die('You should not use security_watch_user_queries()! Report to admin');
//  // TODO Заменить это на новый логгер

//  global $config, $is_watching, $user, $debug;
//
//  if(!$is_watching && $config->game_watchlist_array && in_array($user['id'], $config->game_watchlist_array))
//  {
//    if(!preg_match('/^(select|commit|rollback|start transaction)/i', $query)) {
//      $is_watching = true;
//      $msg = "\$query = \"{$query}\"\n\r";
//      if(!empty($_POST)) {
//        $msg .= "\n\r" . dump($_POST,'$_POST');
//      }
//      if(!empty($_GET)) {
//        $msg .= "\n\r" . dump($_GET,'$_GET');
//      }
//      $debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
//      $is_watching = false;
//    }
//  }
}
// Deprecated
function security_query_check_bad_words($query) {
  die('You should not use security_query_check_bad_words()! Report to admin');
//  global $user, $dm_change_legit, $mm_change_legit;
//
//  switch(true) {
//    case stripos($query, 'RUNCATE TABL') != false:
//    case stripos($query, 'ROP TABL') != false:
//    case stripos($query, 'ENAME TABL') != false:
//    case stripos($query, 'REATE DATABAS') != false:
//    case stripos($query, 'REATE TABL') != false:
//    case stripos($query, 'ET PASSWOR') != false:
//    case stripos($query, 'EOAD DAT') != false:
//    case stripos($query, 'RPG_POINTS') != false && stripos(trim($query), 'UPDATE ') === 0 && !$dm_change_legit:
//    case stripos($query, 'METAMATTER') != false && stripos(trim($query), 'UPDATE ') === 0 && !$mm_change_legit:
//    case stripos($query, 'AUTHLEVEL') != false && $user['authlevel'] < 3 && stripos($query, 'SELECT') !== 0:
//      $report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
//      $report .= ">Database Inforamation\n";
//      $report .= "\tID - ".$user['id']."\n";
//      $report .= "\tUser - ".$user['username']."\n";
//      $report .= "\tAuth level - ".$user['authlevel']."\n";
//      $report .= "\tAdmin Notes - ".$user['adminNotes']."\n";
//      $report .= "\tCurrent Planet - ".$user['current_planet']."\n";
//      $report .= "\tUser IP - ".$user['user_lastip']."\n";
//      $report .= "\tUser IP at Reg - ".$user['ip_at_reg']."\n";
//      $report .= "\tUser Agent- ".$_SERVER['HTTP_USER_AGENT']."\n";
//      $report .= "\tCurrent Page - ".$user['current_page']."\n";
//      $report .= "\tRegister Time - ".$user['register_time']."\n";
//      $report .= "\n";
//
//      $report .= ">Query Information\n";
//      $report .= "\tQuery - ".$query."\n";
//      $report .= "\n";
//
//      $report .= ">\$_SERVER Information\n";
//      $report .= "\tIP - ".$_SERVER['REMOTE_ADDR']."\n";
//      $report .= "\tHost Name - ".$_SERVER['HTTP_HOST']."\n";
//      $report .= "\tUser Agent - ".$_SERVER['HTTP_USER_AGENT']."\n";
//      $report .= "\tRequest Method - ".$_SERVER['REQUEST_METHOD']."\n";
//      $report .= "\tCame From - ".$_SERVER['HTTP_REFERER']."\n";
//      $report .= "\tPage is - ".$_SERVER['SCRIPT_NAME']."\n";
//      $report .= "\tUses Port - ".$_SERVER['REMOTE_PORT']."\n";
//      $report .= "\tServer Protocol - ".$_SERVER['SERVER_PROTOCOL']."\n";
//
//      $report .= "\n--------------------------------------------------------------------------------------------------\n";
//
//      $fp = fopen(SN_ROOT_PHYSICAL . 'badqrys.txt', 'a');
//      fwrite($fp, $report);
//      fclose($fp);
//
//      $message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';
//      die($message);
//    break;
//  }
}
