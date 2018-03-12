<?php

function sys_maintenance()
{
  global $config;

  $bashing_time_limit = SN_TIME_NOW - $config->fleet_bashing_scope;

  // TODO: Move here some cleaning procedures from admin/maintenance.php
  // TODO: Add description of operation to log it
  $queries = array(
    // Cleaning outdated records from bashing table
    array('query' => "DELETE FROM `{{bashing}}` WHERE bashing_time < {$bashing_time_limit};", 'result' => false, 'error' => '', 'affected_rows' => 0),
    // Cleaning ACS table from empty records
    array('query' => 'DELETE FROM `{{aks}}` WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`);', 'result' => false, 'error' => '', 'affected_rows' => 0),
    // Cleaning destroyed planets & moons which outlives it's time
    array('query' => "DELETE FROM `{{planets}}` WHERE `id_owner` = 0 AND `destruyed` < UNIX_TIMESTAMP();", 'result' => false, 'error' => '', 'affected_rows' => 0),
  );

  foreach($queries as &$query)
  {
    $query['result'] = doquery($query['query']);
    $query['error']  = SN::$db->db_error();
    $query['affected_rows']  = SN::$db->db_affected_rows();
  }

  return $queries;
}

// define('SCHEDULER_PREG2', '/^(?:(\w\@))?(?:(?:(?:(?:(?:(\d*)-)?(\d*)-)?(?:(\d*)\ ))?(?:(\d*):))?(?:(\d*):))?(\d*)?$/i');

// format: [<m|w|d|h|m|s>@]<time>
// first param: m - monthly, w - weekly, d - daily, h - hourly, i - minutly, s - secondly
// second param: [<months>-[<days|weeks> [<hours>:[<minutes>:]]]<seconds>
//        valid: '10' - runtime every 10 s
//        valid: '05:' or '05:00' - runtime every 5 m
//        valid: '02::' or '02:00:' or '02:00:00' - runtime every 2 h
//        etc

/*
 * Формат
 *
 * 1. Y-M-D H:I:S  - указывает раз в сколько времени должна запускаться задача и где Y, M, D, H, I, S - соответственно количество лет, месяцев, дней, часов, минут, секунд, определяющих интервал
 * TODO: 2. [<m|w|d|h|m|s>@]<time>
 */

function sys_schedule_get_prev_run($scheduleList, $recorded_run = SN_TIME_NOW, $return_next_run = false)
{
  static $date_part_names_reverse = array('seconds', 'minutes', 'hours', 'days', 'months', 'years',);

  $possible_schedules = array();

  $recorded_run = strtotime($recorded_run);

  $prev_run_array = getdate($recorded_run);
  $prev_run_array = array($prev_run_array['seconds'],$prev_run_array['minutes'],$prev_run_array['hours'],$prev_run_array['mday'],$prev_run_array['mon'],$prev_run_array['year']);
  $today_array = getdate(SN_TIME_NOW);
  $today_array = array($today_array['seconds'],$today_array['minutes'],$today_array['hours'],$today_array['mday'],$today_array['mon'],$today_array['year']);
  $scheduleList = explode(',', $scheduleList);
  array_walk($scheduleList, function(&$schedule) use ($prev_run_array, $today_array, $date_part_names_reverse, &$possible_schedules) {
    $schedule = array('schedule_array' => array_reverse(explode(':', trim($schedule))));

    $interval = $date_part_names_reverse[count($schedule['schedule_array'])];

    foreach($prev_run_array as $index => $date_part) {
      $schedule['array']['recorded'][$index] = isset($schedule['schedule_array'][$index]) ? intval($schedule['schedule_array'][$index]) : $date_part;
      $schedule['array']['now'][$index] = isset($schedule['schedule_array'][$index]) ? intval($schedule['schedule_array'][$index]) : $today_array[$index];
    }
    if($schedule['array']['recorded'] == $schedule['array']['now']) {
      unset($schedule['array']['now']);
    }

    foreach($schedule['array'] as $name => $array) {
      $schedule['string'][$name] = "{$array[5]}-{$array[4]}-{$array[3]} {$array[2]}:{$array[1]}:{$array[0]}";
      $schedule['string'][$name . '_next'] = $schedule['string'][$name] . ' +1 ' . $interval;
      $schedule['string'][$name . '_prev'] = $schedule['string'][$name] . ' -1 ' . $interval;
    }

    foreach($schedule['string'] as $string) {
      $timestamp = strtotime($string);
      $schedule['timestamp'][$timestamp] = $possible_schedules[$timestamp] = date(FMT_DATE_TIME_SQL, strtotime($string));
    }
  });

  ksort($possible_schedules);

  $prev_run = 0;
  $next_run = 0;
  foreach($possible_schedules as $timestamp => $string_date) {
    $prev_run = SN_TIME_NOW >= $timestamp ? $timestamp : $prev_run;
    $next_run = SN_TIME_NOW < $timestamp && !$next_run ? $timestamp : $next_run;
  }

  return $return_next_run ? $next_run : $prev_run;
}
