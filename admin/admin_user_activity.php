<?php
/** @noinspection SqlResolve */

ini_set('memory_limit', '512M');

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

//messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);


$userId = sys_get_param_id('id');

$template = SnTemplate::gettemplate('admin/admin_user_activity');
visualize($userId);
$template->assign_recursive($template_result);
SnTemplate::display($template, "Активность игрока [{$userId}] {$template_result['USER_NAME']}");

function visualize($userId) {
  global $template_result;

  $activityPeriod = PERIOD_HOUR * 1;
//  $activityPeriod = PERIOD_MINUTE_10;

  $userIdSafe = round(floatval($userId));

  $iter = SN::$gc->db->selectIterator(
    "SELECT c.visit_time, c.visit_length, c.counter_id
    FROM `{{counter}}` as c
    where 
          user_id={$userIdSafe}
          AND visit_time > '2018-01-01'
    order by visit_time desc;"
  );

  $user = SN::$db->doQueryAndFetch("SELECT `username` FROM `{{users}}` WHERE `id` = {$userIdSafe}");

  $template_result += [
    'RECORDS'   => count($iter),
    'USER_ID'   => $userId,
    'USER_NAME' => $user['username'],
  ];

  if (!count($iter)) {
    return;
  }

  $from = null;
  $to   = null;

  $perHour = [];
  foreach ($iter as $record) {
    empty($to) ? $to = $record['visit_time'] : false;

    $from = $record['visit_time'];

    $time      = strtotime($record['visit_time']);
    $hourStart = floor($time / $activityPeriod) * $activityPeriod;

    $length = $record['visit_length'];
    $length == 0 ? $length = 1 : false;

    do {
      $leftOfThisHour = $hourStart + $activityPeriod - $time;

      if ($length < $leftOfThisHour) {
        $spendOnThisHour = $length;
        $length          = 0;
      } else {
        $spendOnThisHour = $leftOfThisHour;
        $length          -= $leftOfThisHour;
      }
      $perHour[$hourStart] += $spendOnThisHour;

      $hourStart += $activityPeriod;
    } while ($length > 0);

  }

  $template_result += [
    'PERIOD' => $activityPeriod,

    'DATE_FROM' => $from,
    'DATE_TO'   => $to,
  ];

  ksort($perHour);

  end($perHour);
  $lastHour = key($perHour);

  reset($perHour);
  $firstHour = key($perHour);
  $thisHour  = $firstHour;

  do {
    if (empty($perHour[$thisHour])) {
      $perHour[$thisHour] = 0;
    }
    $thisHour += $activityPeriod;
  } while ($thisHour < $lastHour);

  krsort($perHour);

  end($perHour);
  $lastHour = key($perHour);

  $dayOpened  = null;
  $toTemplate = [];
  foreach ($perHour as $hour => $length) {
    $openDay  = false;
    $closeDay = false;

    if (!$dayOpened) {
      $openDay   = true;
      $dayOpened = 1;
    }

    if ($dayOpened && (date('H', $hour) == 0 || $hour == $lastHour)) {
      $closeDay  = true;
      $dayOpened = 0;
    }

    $lengthPercent = $length / $activityPeriod * 100;
    $toTemplate[]  = [
      'TIME'           => date(FMT_TIME, $hour),
      'LENGTH'         => $length,
      'LENGTH_PERCENT' => $lengthPercent > 100 ? 100 : $lengthPercent,
      'MINUTES'        => round($length / PERIOD_MINUTE, 1), // unused?
      'TIME_CLASS'     => $length ? 'present' : 'none',

      'OPEN_DAY'  => $openDay,
      'CLOSE_DAY' => $closeDay,
      'DATE'      => date(FMT_DATE, $hour),
      'DAY_CLASS' => in_array(date('w', $hour), [0, 6]) ? 'weekend' : '',
    ];

    $template_result['.']['hourly'] = $toTemplate;
  }

}
