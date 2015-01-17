<?php

/**
 * db.php
 * Previously mysql.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

if(!defined('INSIDE'))
{
  die();
}

define('DB_MYSQL_TRANSACTION_SERIALIZABLE', 'SERIALIZABLE');
define('DB_MYSQL_TRANSACTION_REPEATABLE_READ', 'REPEATABLE READ');
define('DB_MYSQL_TRANSACTION_READ_COMMITTED', 'READ COMMITTED');
define('DB_MYSQL_TRANSACTION_READ_UNCOMMITTED', 'READ UNCOMMITTED');

function security_watch_user_queries($query)
{
  // TODO Заменить это на новый логгер
  global $config, $is_watching, $user, $debug;

  if(!$is_watching && $config->game_watchlist_array && in_array($user['id'], $config->game_watchlist_array))
  {
    {
      if(!preg_match('/^(select|commit|rollback|start transaction)/i', $query))
      {
        $is_watching = true;
        $msg = "\$query = \"{$query}\"\n\r";
        if(!empty($_POST))
        {
          $msg .= "\n\r" . dump($_POST,'$_POST');
        }
        if(!empty($_GET))
        {
          $msg .= "\n\r" . dump($_GET,'$_GET');
        }
        $debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
        $is_watching = false;
      }
    }
  }
}

function security_query_check_bad_words($query)
{
  global $user, $dm_change_legit, $mm_change_legit;

  switch(true)
  {
    case stripos($query, 'RUNCATE TABL') != false:
    case stripos($query, 'ROP TABL') != false:
    case stripos($query, 'ENAME TABL') != false:
    case stripos($query, 'REATE DATABAS') != false:
    case stripos($query, 'REATE TABL') != false:
    case stripos($query, 'ET PASSWOR') != false:
    case stripos($query, 'EOAD DAT') != false:
    case stripos($query, 'RPG_POINTS') != false && stripos(trim($query), 'UPDATE ') === 0 && !$dm_change_legit:
    case stripos($query, 'METAMATTER') != false && stripos(trim($query), 'UPDATE ') === 0 && !$mm_change_legit:
    case stripos($query, 'AUTHLEVEL') != false && $user['authlevel'] < 3 && stripos($query, 'SELECT') !== 0:
      $report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
      $report .= ">Database Inforamation\n";
      $report .= "\tID - ".$user['id']."\n";
      $report .= "\tUser - ".$user['username']."\n";
      $report .= "\tAuth level - ".$user['authlevel']."\n";
      $report .= "\tAdmin Notes - ".$user['adminNotes']."\n";
      $report .= "\tCurrent Planet - ".$user['current_planet']."\n";
      $report .= "\tUser IP - ".$user['user_lastip']."\n";
      $report .= "\tUser IP at Reg - ".$user['ip_at_reg']."\n";
      $report .= "\tUser Agent- ".$_SERVER['HTTP_USER_AGENT']."\n";
      $report .= "\tCurrent Page - ".$user['current_page']."\n";
      $report .= "\tRegister Time - ".$user['register_time']."\n";
      $report .= "\n";

      $report .= ">Query Information\n";
      $report .= "\tQuery - ".$query."\n";
      $report .= "\n";

      $report .= ">\$_SERVER Information\n";
      $report .= "\tIP - ".$_SERVER['REMOTE_ADDR']."\n";
      $report .= "\tHost Name - ".$_SERVER['HTTP_HOST']."\n";
      $report .= "\tUser Agent - ".$_SERVER['HTTP_USER_AGENT']."\n";
      $report .= "\tRequest Method - ".$_SERVER['REQUEST_METHOD']."\n";
      $report .= "\tCame From - ".$_SERVER['HTTP_REFERER']."\n";
      $report .= "\tPage is - ".$_SERVER['SCRIPT_NAME']."\n";
      $report .= "\tUses Port - ".$_SERVER['REMOTE_PORT']."\n";
      $report .= "\tServer Protocol - ".$_SERVER['SERVER_PROTOCOL']."\n";

      $report .= "\n--------------------------------------------------------------------------------------------------\n";

      $fp = fopen(SN_ROOT_PHYSICAL . 'badqrys.txt', 'a');
      fwrite($fp, $report);
      fclose($fp);

      $message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';
      die($message);
    break;
  }
}

function sn_db_connect()
{
  global $link, $debug;

  if($link)
  {
    return true;
  }

  require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

  $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']) or $debug->error(mysql_error(),'DB Error - cannot connect to server');

  mysql_query("/*!40101 SET NAMES 'utf8' */") or die('Error: ' . mysql_error());
  mysql_select_db($dbsettings['name']) or $debug->error(mysql_error(), 'DB error - cannot find DB on server');
  // mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or die('Error: ' . mysql_error());
  mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or die('Error: ' . mysql_error());
  unset($dbsettings);

  return true;
}

function doquery($query, $table = '', $fetch = false, $skip_query_check = false)
{
  global $numqueries, $link, $debug, $sn_cache, $config, $db_prefix;

  if(!is_string($table))
  {
    $fetch = $table;
  }

  if(!$link)
  {
    sn_db_connect();
  }

  $query = trim($query);
  security_watch_user_queries($query);
  $skip_query_check or security_query_check_bad_words($query);

  $sql = $query;
  if(!(strpos($sql, '{{') === false) )
  {
    foreach($sn_cache->tables as $tableName)
    {
      $sql = str_replace("{{{$tableName}}}", $db_prefix.$tableName, $sql);
    }
  }

  if($config->debug)
  {
    $numqueries++;
    $arr = debug_backtrace();
    $file = end(explode('/',$arr[0]['file']));
    $line = $arr[0]['line'];
    $debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$file($line)</th><th>$table</th><th>$fetch</th></tr>");
  }

  if(defined('DEBUG_SQL_COMMENT'))
  {
    $backtrace = debug_backtrace();
    $sql_comment = $debug->compact_backtrace($backtrace, defined('DEBUG_SQL_COMMENT_LONG'));
    //    pdump($backtrace[0]);
    //    pdump($backtrace[1]);
    //    print("<hr/>");
    //    foreach($backtrace as $a_trace)
    //    {
    //      if(!in_array($a_trace['function'], array('doquery', 'db_query', 'db_get_record_list'))) break;
    //    }
    //    // $a_trace = $backtrace[1]['function'] == 'db_query' ? $backtrace[2] : $backtrace[1];
    //    $function =
    //      ($a_trace['type']
    //        ? ($a_trace['type'] == '->'
    //          ? "({$a_trace['class']})" . get_class($a_trace['object'])
    //          : $a_trace['class']
    //        ) . $a_trace['type']
    //        : ''
    //      ) . $a_trace['function'] . '()';
    //
    //    $file = str_replace(SN_ROOT_PHYSICAL, '', str_replace('\\', '/', $a_trace['file']));
    //
    //    $transaction_id = classSupernova::db_transaction_check(false) ? classSupernova::$transaction_id : classSupernova::$transaction_id++;
    //
    //    $sql = "/* {$function} '{$file}' Line {$a_trace['line']} tID {$transaction_id} */ " . $sql;

    $sql_commented = '/* ' . implode("<br />", $sql_comment) . '<br /> */ ' . preg_replace("/\s+/", ' ', $sql);
    if(defined('DEBUG_SQL_ONLINE'))
    {
      $debug->warning($sql_commented, 'SQL Debug', LOG_DEBUG_SQL);
    }

    if(defined('DEBUG_SQL_ERROR'))
    {
      array_unshift($sql_comment, preg_replace("/\s+/", ' ', $sql));
      $debug->add_to_array($sql_comment);
      // $debug->add_to_array($sql_comment . preg_replace("/\s+/", ' ', $sql));
    }
    $sql = $sql_commented;
  }

  $sqlquery = mysql_query($sql) or $debug->error(mysql_error()."<br />$sql<br />",'SQL Error');

  return $fetch ? mysql_fetch_assoc($sqlquery) : $sqlquery;
}

function db_change_units_perform($query, $tablename, $object_id)
{
  $query = implode(',', $query);
  if($query && $object_id)
  {
    return classSupernova::db_upd_record_by_id($tablename == 'users' ? LOC_USER : LOC_PLANET, $object_id, $query);
    // return doquery("UPDATE {{{$tablename}}} SET {$query} WHERE `id` = '{$object_id}' LIMIT 1;");
  }
}

// TODO: THIS FUNCTION IS OBSOLETE AND SHOULD BE REPLACED!
// TODO - ТОЛЬКО ДЛЯ РЕСУРСОВ
// $unit_list should have unique entrances! Recompress non-uniq entrances before pass param!
function db_change_units(&$user, &$planet, $unit_list = array(), $query = null)
{
  $query = is_array($query) ? $query : array(
    LOC_USER => array(),
    LOC_PLANET => array(),
  );

  $group = sn_get_groups('resources_loot');

  foreach($unit_list as $unit_id => $unit_amount)
  {
    if(!in_array($unit_id, $group))
    {
    // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: db_change_units() вызван для не-ресурсов!</h1>');
      pdump(debug_backtrace());
      die('db_change_units() вызван для не-ресурсов!');
    }

    if(!$unit_amount)
    {
      continue;
    }

    $unit_db_name = pname_resource_name($unit_id);

    $unit_location = sys_get_unit_location($user, $planet, $unit_id);

    // Changing value in object
    switch($unit_location)
    {
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
function sn_db_perform($table, $values, $type = 'insert', $options = false)
{
  $mass_perform = false;

  $field_set = '';
  $value_set = '';

  switch($type)
  {
    case 'delete':
      $query = 'DELETE FROM';
    break;

    case 'insert':
      $query = 'INSERT INTO';
      if(isset($options['__multi']))
      {
        // Here we generate mass-insert set
        break;
      }
    case 'update':
      if(!$query)
      {
        $query = 'UPDATE';
      }

      foreach($values as $field => &$value)
      {
        $value_type = gettype($value);
        if ($value_type == 'string')
        {
          $value = "'" . mysql_real_escape_string($value) . "'";
        }
        $value = "`{$field}` = {$value}";
      }
      $field_set = 'SET ' . implode(', ', $values);
    break;

  };

  $query .= " {$table} {$field_set}";
  return doquery($query);
}

function sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id = null)
{
  return classSupernova::db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id);
}
function db_changeset_apply($db_changeset)
{
  return classSupernova::db_changeset_apply($db_changeset);
}
function sn_db_transaction_check($transaction_should_be_started = null)
{
  return classSupernova::db_transaction_check($transaction_should_be_started);
}
function sn_db_transaction_start($level = '')
{
  return classSupernova::db_transaction_start($level);
}
function sn_db_transaction_commit()
{
  return classSupernova::db_transaction_commit();
}
function sn_db_transaction_rollback()
{
  return classSupernova::db_transaction_rollback();
}

require_once('db/db_queries.php');
