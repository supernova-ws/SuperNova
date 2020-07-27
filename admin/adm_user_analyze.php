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

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

// define('SESSION_INTERRUPT', 15*60); // Можно увеличить до 4 часов - никито не может сидеть 2 суток с перерывом менее 4 часов
// define('SUSPICIOUS_LONG', 2 * 60*60); // Тогда это увеличиваем до, скажем суток - и там смотрим

define('SESSION_INTERRUPT', 1 * 60*60); // Можно увеличить до 4 часов - никито не может сидеть 2 суток с перерывом менее 4 часов
define('SUSPICIOUS_LONG', 16 * 60*60); // Тогда это увеличиваем до, скажем суток - и там смотрим


function check_suspicious(&$session, &$session_list_last_id, &$row) {
  $session[2] = $session[1] - $session[0];
  if($session[2] > SUSPICIOUS_LONG)
  {
    $session[2] = pretty_time($session[2]);
    $session[0] = date(FMT_DATE_TIME_SQL, $session[0]);
    $session[1] = date(FMT_DATE_TIME_SQL, $session[1]);
    $session_list_last_id[] = $session;
  }
  //$row ?
  $session = array(
//    0 => $row['time'], // start
//    1 => $row['time'], // end
    0 => $row['visit_time'], // start
    1 => $row['visit_time'], // end
  )
   //: false
   ;
}

$session_list = array();
$query = doquery("SELECT `visit_time`, user_id FROM {{counter}} where user_id <> 0 and visit_time > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY)) order by user_id, visit_time;");
$session = array();
if($row = db_fetch($query)) {
  $session = array(
    0 => strtotime($row['visit_time']), // start
    1 => strtotime($row['visit_time']), // end
  );
  $last_id = $row['user_id'];
}
while($row = db_fetch($query)) {
  $row['visit_time'] = strtotime($row['visit_time']);
  if($last_id == $row['user_id']) {
    // Тот же юзер
    if($row['visit_time'] - $session[1] <= SESSION_INTERRUPT) { // Та же сессия
      $session[1] = $row['visit_time'];
    } else {
      // Новая сессия
//      check_suspicious($session, $session_list[$last_id], $row);
      $session[2] = $session[1] - $session[0];
      if($session[2] > SUSPICIOUS_LONG)
      {
        $session[2] = pretty_time($session[2]);
        $session[0] = date(FMT_DATE_TIME_SQL, $session[0]);
        $session[1] = date(FMT_DATE_TIME_SQL, $session[1]);
        $session_list[$last_id][] = $session;
      }
      $session = array(
        0 => $row['visit_time'], // start
        1 => $row['visit_time'], // end
      );
    }
  } else {
//    check_suspicious($session, $session_list[$last_id], $row);
    $session[2] = $session[1] - $session[0];
      if($session[2] > SUSPICIOUS_LONG)
      {
        $session[2] = pretty_time($session[2]);
        $session[0] = date(FMT_DATE_TIME_SQL, $session[0]);
        $session[1] = date(FMT_DATE_TIME_SQL, $session[1]);
        $session_list[$last_id][] = $session;
      }
    $session = array(
      0 => $row['visit_time'], // start
      1 => $row['visit_time'], // end
    );
    $last_id = $row['user_id'];
  }
}

if($last_id) {
  // check_suspicious($session, $session_list[$last_id], $row = array('time' => 0));
  $session[2] = $session[1] - $session[0];

  if($session[2] > SUSPICIOUS_LONG)
  {
    $session[2] = pretty_time($session[2]);
    $session[0] = date(FMT_DATE_TIME_SQL, $session[0]);
    $session[1] = date(FMT_DATE_TIME_SQL, $session[1]);
    $session_list[$last_id][] = $session;
  }
}

print("<table border='1'>");
print("<tr>");
print("<td>ID</td><td>Username</td><td>Start</td><td>End</td><td>Length</td>");
print("<td>Last online</td>");
print("</tr>");
foreach($session_list as $user_id => $value) {
  $user_record = doquery("SELECT `username`, onlinetime FROM {{users}} WHERE id = {$user_id};", true);
  foreach($value as $interval_data) {
    print("<tr>");
    print("<td>{$user_id}</td><td>{$user_record['username']}</td><td>{$interval_data[0]}</td><td>{$interval_data[1]}</td><td>{$interval_data[2]}</td>");
    print("<td>" . date(FMT_DATE_TIME_SQL, $user_record['onlinetime']) . "</td>");
    print("</tr>");
  }
}
print("</table>");
