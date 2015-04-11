<?php

/*
 * simple mysql wrapper
 *
 */

define('DB_MYSQL_TRANSACTION_SERIALIZABLE', 'SERIALIZABLE');
define('DB_MYSQL_TRANSACTION_REPEATABLE_READ', 'REPEATABLE READ');
define('DB_MYSQL_TRANSACTION_READ_COMMITTED', 'READ COMMITTED');
define('DB_MYSQL_TRANSACTION_READ_UNCOMMITTED', 'READ UNCOMMITTED');


// Совсем низкоуровневые процедуры - на них должна быть реакция
function __db_error($link = null) {
  return $link ? mysql_error($link) : mysql_error();
}
function __db_query($query_string, $link = null) {
  return $link ? mysql_query($query_string, $link) : mysql_query($query_string);
}
function __db_connect(&$link, $dbsettings) {
  global $debug;

  if(!$link) {
    // TODO !!!!!! DEBUG -> error!!!!
    $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']) or $debug->error(__db_error(),'DB Error - cannot connect to server');

    __db_query("/*!40101 SET NAMES 'utf8' */") or die('Error: ' . __db_error());
    mysql_select_db($dbsettings['name']) or $debug->error(__db_error(), 'DB error - cannot find DB on server');
    // mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or die('Error: ' . __db_error());
    __db_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or die('Error: ' . __db_error());
    unset($dbsettings);
  }

  return true;
}
function __db_disconnect($link = null) {
  return $link ? mysql_close($link) : mysql_close();
}

// Среднеуровневые врапперы - для абстрагирования от типа БД
function db_fetch(&$query) {
  return mysql_fetch_assoc($query);
}
function db_fetch_row(&$query) {
  return mysql_fetch_row($query);
}

function db_escape($unescaped_string, $link = null) {
  return $link ? mysql_real_escape_string($unescaped_string, $link) : mysql_real_escape_string($unescaped_string);
}

function db_insert_id($link = null) {
  return $link ? mysql_insert_id($link) : mysql_insert_id();
}

function db_num_rows(&$result) {
  return mysql_num_rows($result);
}
function db_affected_rows($link = null) {
  return $link ? mysql_affected_rows($link) : mysql_affected_rows();
}

function db_get_client_info() {
  return mysql_get_client_info();
}
function db_get_server_info($link = null) {
  return $link ? mysql_get_server_info($link) : mysql_get_server_info();
}
function db_get_host_info($link = null) {
  return $link ? mysql_get_host_info($link) : mysql_get_host_info();
}
function db_server_stat($link = null) {
  return $link ? mysql_stat($link) : mysql_stat();
}
