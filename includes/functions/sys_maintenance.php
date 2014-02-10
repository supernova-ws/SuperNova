<?php

function sys_maintenance()
{
  global $config, $time_now;

  $bashing_time_limit = $time_now - $config->fleet_bashing_scope;

  // TODO: Move here some cleaning procedures from admin/maintenance.php
  // TODO: Add description of operation to log it
  $queries = array(
    // Cleaning outdated records from bashing table
    array('query' => "DELETE FROM {{bashing}} WHERE bashing_time < {$bashing_time_limit};", 'result' => false, 'error' => '', 'affected_rows' => 0),
    // Cleaning ACS table from empty records
    array('query' => 'DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});', 'result' => false, 'error' => '', 'affected_rows' => 0),
  );

  foreach($queries as &$query)
  {
    $query['result'] = doquery($query['query']);
    $query['error']  = mysql_error();
    $query['affected_rows']  = mysql_affected_rows();
  }

  return $queries;
}

// format: d@<time>

// format: [<m|w|d|h|m|s>@]<time>
// first param: m - monthly, w - weekly, d - daily, h - hourly, i - minutly, s - secondly
// second param: [<months>-[<days|weeks> [<hours>:[<minutes>:]]]<seconds>
//        valid: '10' - runtime every 10 s
//        valid: '05:' or '05:00' - runtime every 5 m
//        valid: '02::' or '02:00:' or '02:00:00' - runtime every 2 h
//        etc
// ToDo: m, w, d

function sys_schedule_get_next_run($scheduleList, $lastRun = 0, $timeNow = 0, $runMissed = true){
  $dtf = 'Y-m-d H:i:s';
//  $dtf = 'd H:i:s';

//pdump($scheduleList);
//pdump(date($dtf, $timeNow), 'timeNow');
//pdump(date($dtf, $lastRun), 'lastRun');
//pdump($runMissed, 'runMissed');
  $lastRun = $lastRun ? $lastRun : 0;

  $dateFields = array( 'seconds', 'minutes', 'hours', 'mday', 'mon', 'year' );

  // If no $timeNow defined - using current time
  if(!$timeNow) $timeNow = time();

  // $lastRun should be always not greater then $timeNow!
  if($lastRun>$timeNow)
    $lastRun = $timeNow - 1;

  // Parsing shedule list
  $schedules = explode(',', $scheduleList);
  foreach($schedules as $schIndex => $schedule){
    // Parsing specific schedule
    $thisSchedule = explode('@', $schedule);

    // If no @-sign - it means 'once per day' i.e. 'd'
    if(count($thisSchedule) == 1)
      array_unshift($thisSchedule, 'd');

    // Calculating schedule interval in seconds
    switch($thisSchedule[0]){
      case 'd':
        $scheduledInterval = 24*60*60;
        break;
    }
    if(!$lastRun) $lastRun = $timeNow - 2*$scheduledInterval;

    // Checking - if current schedule correct
    if(preg_match(SCHEDULER_PREG,$thisSchedule[1],$matches)){
      $matches = array_reverse($matches);

      //parsing $lastRun
      $last = getdate($lastRun);

      // Applying schedule to parsed $lastRun
      for($i=0; $i<6; $i++)
        if($matches[$i] != '')
          $last[$dateFields[$i]] = $matches[$i];
      // Making new date with correct schedule
      $lastRun = mktime( $last['hours'], $last['minutes'], $last['seconds'], $last['mon'], $last['mday'], $last['year']);

      // Calculating previous schedule time
      $lastRun += $scheduledInterval * floor(($timeNow - $lastRun)/$scheduledInterval);

      if(!$nextRun)
        $nextRun = $lastRun + $scheduledInterval * intval(!$runMissed);

      if(($runMissed && $lastRun>$nextRun && $lastRun<=$timeNow)||(!$runMissed && $lastRun<$nextRun && $lastRun>=$timeNow))
          $nextRun = $lastRun;

    };
  }

  return($nextRun);
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

function sys_schedule_get_prev_run($scheduleList, $prev_run = 0, $now = SN_TIME_NOW, $return_next_schedule = false)
{
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
    if(!preg_match(SCHEDULER_PREG2,$schedule,$matches)){
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
}
