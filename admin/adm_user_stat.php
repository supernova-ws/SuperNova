<?php

/**
 * adm_payment.php
 *
 * @version 1.0
 * @copyright 2013 by Gorlum for http://supernova.ws
*/
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));


function admin_date_sort($a, $b)
{
  return $a['DATE'] == $b['DATE'] ? 0 : ($a['DATE'] > $b['DATE'] ? 1 : -1);
}

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = SnTemplate::gettemplate('admin/adm_user_stat', true);

$dt_from = sys_get_param_date_sql('dt_from', '2000-01-01');
if(strlen($dt_from) == 4)
{
  $dt_from .= '-01';
}
$dt_to = sys_get_param_date_sql('dt_to', date('Y-m-d', SN_TIME_NOW + PERIOD_DAY));
if(strlen($dt_to) == 4)
{
  $dt_to .= '-01';
}
$sql_date = 
  ($dt_from ? " AND register_time >= UNIX_TIMESTAMP(STR_TO_DATE('{$dt_from}', '%Y-%m-%d %H:%i:%s')) " : '') .
  ($dt_to ? " AND register_time < UNIX_TIMESTAMP(STR_TO_DATE('{$dt_to}', '%Y-%m-%d %H:%i:%s')) " : '');

$min_max_dates = doquery($q = "SELECT min(register_time) AS min_register, max(register_time) AS max_register, avg(onlinetime - register_time) AS avg_play_time,  STR_TO_DATE('{$dt_to}', '%Y-%m-%d %H:%i:%s') FROM {{users}} WHERE 1 " . $sql_date, true);

$interval = $min_max_dates['max_register'] - $min_max_dates['min_register'];

switch(true)
{
  case $interval >= PERIOD_YEAR * 4:
    $sql_group_format = "%Y";
    $sql_date_add = 'year';
  break;

  case $interval > PERIOD_DAY * 32:
    $sql_group_format = "%Y-%m";
    $sql_date_add = 'month';
  break;

  case $interval > PERIOD_DAY:
    $sql_group_format = "%Y-%m-%d";
    $sql_date_add = 'day';
  break;

  default:
    $sql_group_format = "%Y-%m-%d %H:00:00";
    $sql_date_add = 'hour';
    $stop_next = true;
  break;
}

$stat_date = array();
$max_registered = 0;
$max_accounts = 0;

$sql_group_by2 = "DATE_FORMAT(account_register_time, '{$sql_group_format}')";
$sql_date2 =
  ($dt_from ? " AND account_register_time >= (STR_TO_DATE('{$dt_from}', '%Y-%m-%d %H:%i:%s')) " : '') .
  ($dt_to ? " AND account_register_time < (STR_TO_DATE('{$dt_to}', '%Y-%m-%d %H:%i:%s')) " : '');
$query = doquery(
  "SELECT 
    count(*) AS the_count, 
    DATE_FORMAT(account_register_time, '{$sql_group_format}') AS the_date, 
    DATE_FORMAT(account_register_time, '%a') AS DoW, 
    DATE_FORMAT(DATE_ADD(account_register_time, INTERVAL 1 {$sql_date_add}), '{$sql_group_format}') AS date_next
FROM {{account}} WHERE 1 " . $sql_date2 . " GROUP BY {$sql_group_by2}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']] = array(
    'DATE' => $row['the_date'],
    'DOW' => $row['DoW'],
    'DATE_URL' => urlencode($row['the_date']),
    'DATE_NEXT_URL' => urlencode($row['date_next']),
    'ACCOUNTS' => $row['the_count'],
  );

  $max_accounts = max($max_accounts, $row['the_count']);
}

$sql_group_by = "DATE_FORMAT(FROM_UNIXTIME(register_time), '{$sql_group_format}')";
$query = doquery("SELECT count(*) AS the_count, {$sql_group_by} AS the_date, DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(register_time), INTERVAL 1 {$sql_date_add}), '{$sql_group_format}') AS date_next
FROM {{users}} WHERE user_as_ally IS NULL " . $sql_date . " GROUP BY {$sql_group_by}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']]['REGISTERED'] = $row['the_count'];

  $max_registered = max($max_registered, $row['the_count']);
}

$query = doquery("SELECT count(*) AS the_count, {$sql_group_by} AS the_date FROM {{users}} WHERE user_as_ally IS NULL " .
" AND onlinetime <= register_time + " . PERIOD_DAY .
' AND UNIX_TIMESTAMP(NOW()) >= register_time  + ' . PERIOD_DAY .
$sql_date . " GROUP BY {$sql_group_by}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']]['REJECTED'] = $row['the_count'];
}

$query = doquery("SELECT count(*) AS the_count, {$sql_group_by} AS the_date FROM {{users}} WHERE user_as_ally IS NULL " .
" AND onlinetime > register_time + " . PERIOD_DAY .
' AND onlinetime <= register_time + ' . PERIOD_WEEK .
' AND UNIX_TIMESTAMP(NOW()) >= register_time + ' . PERIOD_WEEK .
$sql_date . " GROUP BY {$sql_group_by}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']]['LEAVED'] = $row['the_count'];
}

$query = doquery("SELECT count(*) AS the_count, {$sql_group_by} AS the_date FROM {{users}} WHERE user_as_ally IS NULL " .
' AND UNIX_TIMESTAMP(NOW()) - onlinetime <= ' . PERIOD_DAY .
' AND UNIX_TIMESTAMP(NOW()) - register_time >= ' . PERIOD_DAY .
$sql_date . " GROUP BY {$sql_group_by}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']]['ACTIVE'] = $row['the_count'];
}

$query = doquery("SELECT count(*) AS the_count, {$sql_group_by} AS the_date FROM {{users}} WHERE user_as_ally IS NULL " .
' AND UNIX_TIMESTAMP(NOW()) - onlinetime > ' . PERIOD_DAY .
' AND UNIX_TIMESTAMP(NOW()) - onlinetime <= ' . PERIOD_WEEK .
' AND UNIX_TIMESTAMP(NOW()) - register_time >= ' . PERIOD_WEEK .
$sql_date . " GROUP BY {$sql_group_by}");
while($row = db_fetch($query))
{
  $stat_date[$row['the_date']]['DORMANT'] = $row['the_count'];
}

uasort($stat_date, 'admin_date_sort');

$total = array();
foreach($stat_date as $key => &$value)
{
  $value['TOTAL'] = $value['REJECTED'] + $value['LEAVED'];
  $value['LEAVED_PERCENT'] = $value['REGISTERED'] ? round($value['TOTAL'] / $value['REGISTERED'] * 100) : 0;
  $value['ACTIVE_PERCENT'] = $value['REGISTERED'] ? round($value['ACTIVE'] / $value['REGISTERED'] * 100) : 0;
  $value['DORMANT_PERCENT'] = $value['REGISTERED'] ? round($value['DORMANT'] / $value['REGISTERED'] * 100) : 0;
  foreach($value as $key2 => $value2)
  {
    $total[$key2] += $value2;
  }
  $value['REGISTERED_PERCENT'] = ceil($max_registered ? $value['REGISTERED'] * 100 / $max_registered : 0);
  $template->assign_block_vars('stats', $value);
}
$total['DATE'] = 'Всего';
$total['TH'] = 1;
$total['LEAVED_PERCENT'] = count($stat_date) ? round($total['TOTAL'] / $total['REGISTERED'] * 100) : 0;
$total['ACTIVE_PERCENT'] = count($stat_date) ? round($total['ACTIVE'] / $total['REGISTERED'] * 100) : 0;
$total['DORMANT_PERCENT'] = count($stat_date) ? round($total['DORMANT'] / $total['REGISTERED'] * 100) : 0;
$template->assign_block_vars('stats', $total);

$template->assign_vars(array(
  'AVG_PLAY_TIME' => round($min_max_dates['avg_play_time'] / PERIOD_DAY, 2),
  'STOP_NEXT' => intval($stop_next),
  'INTERVAL' => $sql_date_add,
));

SnTemplate::display($template, $lang['adm_user_stat'], false, '', true);
