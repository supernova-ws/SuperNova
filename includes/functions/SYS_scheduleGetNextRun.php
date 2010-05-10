<?php

// format: [<m|w|d|h|m|s>@]<time>
// first param: m - monthly, w - weekly, d - daily, h - hourly, m - minutly, s - secondly
// second param: [<months>-[<days|weeks> [<hours>:[<minutes>:]]]<seconds>
//        valid: '10' - runtime every 10 s
//        valid: '05:' or '05:00' - runtime every 5 m
//        valid: '02::' or '02:00:' or '02:00:00' - runtime every 2 h
//        etc
// ToDo: m, w, d

function SYS_scheduleGetNextRun($scheduleList, $lastRun = 0, $timeNow = 0){
  if(!$timeNow) $timeNow = time();

  $last = date_parse($lastRun);
  $now  = date_parse($timeNow);

  $schedules = explode(',', $scheduleList);

  foreach($schedules as $schedule){
    $thisSchedule = explode('@', $schedule);
    if(count($thisSchedule) == 1) array_unshift($thisSchedule, 'd');

  }
}

?>