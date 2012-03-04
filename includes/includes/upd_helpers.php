<?php

if(!defined('IN_UPDATE'))
{
  die('Trying to call update helpers externally!');
}

function upd_do_query($query, $no_log = false)
{
  global $update_tables;

  upd_add_more_time();
  if(!$no_log)
  {
    upd_log_message("Performing query '{$query}'");
  }

  $db_prefix = sn_db_connect($query);
  if(!(strpos($query, '{{') === false))
  {
    foreach($update_tables as $tableName => $cork)
    {
      $query = str_replace("{{{$tableName}}}", $db_prefix.$tableName, $query);
    }
  }

  $result = mysql_query($query) or
    die('Query error for ' . $query . ': ' . mysql_error());

  return $result;
}

function upd_check_key($key, $default_value, $condition = false)
{
  global $config;

  $config->db_loadItem($key);
  if($condition || !isset($config->$key))
  {
    upd_add_more_time();
    if(!$GLOBALS['sys_log_disabled'])
    {
      upd_log_message("Updating config key '{$key}' with value '{$default_value}'");
    }
    $config->db_saveItem($key, $default_value);
  }
  else
  {
    $config->db_saveItem($key);
  }
}

function upd_log_version_update()
{
  doquery('START TRANSACTION;');
  upd_add_more_time();
  upd_log_message("Detected outdated version {$GLOBALS['new_version']}. Upgrading...");
}

function upd_add_more_time($time = 0)
{
  global $config, $time_now;

  $time = $time ? $time : ($config->upd_lock_time ? $config->upd_lock_time : 30);

  if(!$GLOBALS['sys_log_disabled'])
  {
    $config->db_saveItem('var_db_update_end', $time_now + $time);
  }
  set_time_limit($time);
}

function upd_log_message($message)
{
  if($GLOBALS['sys_log_disabled'])
  {
//    print("{$message}<br />");
  }
  else
  {
    $GLOBALS['upd_log'] .= "{$message}\r\n";
    $GLOBALS['debug']->warning($message, 'Database Update', 103);
  }
}

function upd_unset_table_info($table_name)
{
  global $update_tables, $update_indexes, $update_foreigns;

  if(isset($update_tables[$table_name]))
  {
    unset($update_tables[$table_name]);
  }

  if(isset($update_indexes[$table_name]))
  {
    unset($update_indexes[$table_name]);
  }

  if(isset($update_foreigns[$table_name]))
  {
    unset($update_foreigns[$table_name]);
  }

}

function upd_load_table_info($prefix_table_name, $prefixed = true)
{
  global $config, $update_tables, $update_indexes, $update_foreigns, $db_name;

  $tableName = $prefixed ? str_replace($config->db_prefix, '', $prefix_table_name) : $prefix_table_name;
  $prefix_table_name = $prefixed ? $prefix_table_name : $config->db_prefix . $prefix_table_name;

  upd_unset_table_info($tableName);

  $q1 = doquery("SHOW COLUMNS FROM {$prefix_table_name};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = doquery("SHOW INDEX FROM {$prefix_table_name};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
  }

  $q1 = doquery("select * FROM information_schema.KEY_COLUMN_USAGE where TABLE_SCHEMA = '{$db_name}' AND TABLE_NAME = '{$prefix_table_name}' AND REFERENCED_TABLE_NAME is not null;");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $table_referenced = str_replace($config->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

    $update_foreigns[$tableName][$r1['CONSTRAINT_NAME']] .= "{$r1['COLUMN_NAME']},{$table_referenced},{$r1['REFERENCED_COLUMN_NAME']};";
  }
}

function upd_alter_table($table, $alters, $condition = true)
{
  global $config;

  if(!$condition)
  {
    return;
  }

  upd_add_more_time();
  $alters_print = is_array($alters) ? dump($alters) : $alters;
  upd_log_message("Altering table '{$table}' with alterations {$alters_print}");

  if(!is_array($alters))
  {
    $alters = array($alters);
  }

  $qry = "ALTER TABLE {$config->db_prefix}{$table} " . implode(',', $alters) . ';';

  $result = mysql_query($qry);
  $error = mysql_error();
  if($error)
  {
    die("Altering error for table `{$table}`: {$error}<br />{$alters_print}");
  }

//  if(strpos('RENAME TO', strtoupper(implode(',', $alters))) === false)
  {
    upd_load_table_info($table, false);
  }

  return $result;
}

function upd_drop_table($table_name)
{
  global $config;

  mysql_query("DROP TABLE IF EXISTS {$config->db_prefix}{$table_name};");

  upd_unset_table_info($table_name);
}

function upd_create_table($table_name, $declaration)
{
  global $config, $update_tables;

  if(!$update_tables[$table_name])
  {
    doquery('set foreign_key_checks = 0;');
    $result = mysql_query("CREATE TABLE IF NOT EXISTS `{$config->db_prefix}{$table_name}` {$declaration}");
    $error = mysql_error();
    if($error)
    {
      die("Creating error for table `{$table_name}`: {$error}<br />" . dump($declaration));
    }
    doquery('set foreign_key_checks = 1;');
    upd_load_table_info($table_name, false);
    sys_refresh_tablelist($config->db_prefix);
  }

  return $result;
}

?>