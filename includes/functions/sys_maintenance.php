<?php

function sys_maintenance() {
  $bashing_time_limit = SN_TIME_NOW - classSupernova::$config->fleet_bashing_scope;

  // TODO: Move here some cleaning procedures from admin/maintenance.php
  // TODO: Add description of operation to log it
  $queries = array(
    // Cleaning outdated records from bashing table
    array(
      'query'         => "DELETE FROM {{bashing}} WHERE bashing_time < {$bashing_time_limit};",
      'result'        => false,
      'error'         => '',
      'affected_rows' => 0,
    ),
    // Cleaning ACS table from empty records
    array(
      'callable'      => 'db_fleet_aks_purge',
      'result'        => false,
      'error'         => '',
      'affected_rows' => 0,
    ),
  );

  foreach($queries as &$query) {
    if(!empty($query['query'])) {
      $query['result'] = doquery($query['query']);
    } elseif(!empty($query['callable']) && is_callable($query['callable'])) {
      call_user_func($query['callable']);
    }
    $query['error'] = classSupernova::$db->db_error();
    $query['affected_rows'] = classSupernova::$db->db_affected_rows();
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

function sys_schedule_get_prev_run($scheduleList, $recorded_run = SN_TIME_NOW, $return_next_run = false) {
//  static $in_seconds = array(PERIOD_YEAR, PERIOD_MONTH, PERIOD_DAY, PERIOD_HOUR, PERIOD_MINUTE, 1);
//  static $date_part_names = array( 'years', 'days', 'months', 'hours', 'minutes', 'seconds', );
  static $date_part_names_reverse = array('seconds', 'minutes', 'hours', 'days', 'months', 'years',);

  $possible_schedules = array();

//  pdump($recorded_run, '$recorded_run');
  $recorded_run = strtotime($recorded_run);
//  pdump($recorded_run, '$recorded_run');

  $prev_run_array = getdate($recorded_run);
  $prev_run_array = array($prev_run_array['seconds'], $prev_run_array['minutes'], $prev_run_array['hours'], $prev_run_array['mday'], $prev_run_array['mon'], $prev_run_array['year']);
  $today_array = getdate(SN_TIME_NOW);
  $today_array = array($today_array['seconds'], $today_array['minutes'], $today_array['hours'], $today_array['mday'], $today_array['mon'], $today_array['year']);
//  pdump($prev_run_array);
  $scheduleList = explode(',', $scheduleList);
  array_walk($scheduleList, function (&$schedule) use ($prev_run_array, $today_array, $date_part_names_reverse, &$possible_schedules) {
    // pdump($schedule);
    $schedule = array('schedule_array' => array_reverse(explode(':', trim($schedule))));

    $interval = $date_part_names_reverse[count($schedule['schedule_array'])];
    /*
    while(count($schedule) < 6) {
      array_unshift($schedule, 0);
    }
    */
    // pdump($schedule);

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
    /*
        $schedule['string']['recorded_string'] = ("{$schedule['array']['recorded'][5]}-{$schedule['array']['recorded'][4]}-{$schedule['array']['recorded'][3]} {$schedule['array']['recorded'][2]}:{$schedule['array']['recorded'][1]}:{$schedule['array']['recorded'][0]}");
        $schedule['string']['next_recorded_string'] = $schedule['string']['recorded_string'] . ' +1 ' . $interval;
        $schedule['string']['prev_recorded_string'] = $schedule['string']['recorded_string'] . ' -1 ' . $interval;

        $schedule['timestamp']['recorded'] = strtotime($schedule['string']['recorded_string']);
        $schedule['timestamp']['next_recorded'] = strtotime($schedule['string']['next_recorded_string']);
        $schedule['timestamp']['prev_recorded'] = strtotime($schedule['string']['prev_recorded_string']);
    */

    // $schedule['string']['today_string'] = ("{$schedule['array']['now'][5]}-{$schedule['array']['now'][4]}-{$schedule['array']['now'][3]} {$schedule['array']['now'][2]}:{$schedule['array']['now'][1]}:{$schedule['array']['now'][0]}");
    /*
    foreach($schedule as $index => &$schedule_part) {
      // $schedule_part = intval($schedule_part) * $in_seconds[$index];
      $schedule_part = intval($schedule_part);
    }
    */
  });

//  pdump($possible_schedules);
  ksort($possible_schedules);
//  pdump($possible_schedules);

  // sort($scheduleList);
  // pdump($scheduleList, '$scheduleList');

  $prev_run = 0;
  $next_run = 0;
  foreach($possible_schedules as $timestamp => $string_date) {
    $prev_run = SN_TIME_NOW >= $timestamp ? $timestamp : $prev_run;
    $next_run = SN_TIME_NOW < $timestamp && !$next_run ? $timestamp : $next_run;
//    pdump($schedule, '$schedule ' . date(FMT_DATE_TIME_SQL, $schedule));
//    pdump($prev_run, '$prev_run ' . date(FMT_DATE_TIME_SQL, $prev_run));
  }

//  pdump($prev_run, '$prev_run ' . date(FMT_DATE_TIME_SQL, $prev_run));
//  pdump($next_run, '$next_run ' . date(FMT_DATE_TIME_SQL, $next_run));

  return $return_next_run ? $next_run : $prev_run;
  /*
    static $date_part_names = array( 'years', 'days', 'months', 'hours', 'minutes', 'seconds', );


    // If no $timeNow defined - using current time
    $now = $now ? $now : SN_TIME_NOW;

    // $lastRun should be always not greater then $timeNow!
    $prev_run = $prev_run > $now ? $now - 1 : $prev_run;

    // Parsing shedule list
    // $next_run = $lastRun;
    $next_scheduled = 0;
    $last_scheduled = 0;
    $schedules = explode(',', $scheduleList);
    foreach($schedules as $schedule)
    {
      // pdump($schedule);
      if(!preg_match(SCHEDULER_PREG2, $schedule, $matches)){
        continue;
      }
      array_shift($matches);

      // Преобразовываем расписание в секунды
      $date_interval = '';
      foreach($date_part_names as $part_index => $date_part)
      {
        $date_interval .= intval($matches[$part_index]) ? intval($matches[$part_index]) . ' ' . $date_part . ' ' : '';
      }
      // pdump($date_interval);

      $date_interval = strtotime($date_interval, 0);
      if(!$date_interval)
      {
        continue;
      }

      // pdump(date(FMT_DATE_TIME, $date_interval), $date_interval);

      // Высчитываем предыдущий запуск по этому расписанию
      $last_this_schedule = $now - ($now - $prev_run) % $date_interval;
      // Находим, какой апдейт ближе к текущей дате
      if($last_scheduled < $last_this_schedule)
      {
        $last_scheduled = $last_this_schedule;
      }
      // pdump(date(FMT_DATE_TIME, $last_scheduled), '$last_scheduled');

      // Находим следующий апдейт по текущему расписанию
      $last_this_schedule = $last_this_schedule + $date_interval;
      // Находим какой следующий запуск ближе к текущей дате
      if(!$next_scheduled || $last_this_schedule < $next_scheduled)
      {
        $next_scheduled = $last_this_schedule;
      }
      // pdump(date(FMT_DATE_TIME, $next_scheduled), '$next_scheduled');
    }

    // Если $runMissed И мы пропустили этот апдейт - возвращаем значение прошлого апдейта
    // Если НЕ ранмиссед ИЛИ мы НЕ пропустили этот апдейт - возвращаем значение следующего апдейта
    return $return_next_schedule ?  $next_scheduled : $last_scheduled;
  */
}
