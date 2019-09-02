<?php

use Alliance\DBStaticAlly;

/**
 * stat.php
 *
 * 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 *   [!] Full rewrote
*/

function stat_tpl_assign(&$template, $selected, $array_name, $array, $sn_group_stat_common) {
  global $who, $lang;

  // $sn_group_stat_common = sn_get_groups('STAT_COMMON');
  foreach($array as $key => $value) {
    if($array_name == 'type' && $who == 2 && !in_array($key, $sn_group_stat_common)) {
      continue;
    }

    $header = isset($value['header']) ? $value['header'] : $lang['stat_type'][$key];

    $template->assign_block_vars($array_name, array(
      'ID'       => $key,
      'HEADER'   => $header,
      'SELECTED' => $key == $selected,
    ));
  }
}

$allow_anonymous = true;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('stat');

$sn_group_stat_common = sn_get_groups('STAT_COMMON');
$who = sys_get_param_int('who', 1);
$type = sys_get_param_int('type');
$type = $who != 1 && !in_array($type, $sn_group_stat_common) ? 1 : $type;
$range = sys_get_param_int('range', 1);
$source = sys_get_param_str('source');

$template = SnTemplate::gettemplate('stat_statistics', true);

$subject_list = array(
  1 => array('header' => $lang['stat_player']),
);
if(!$source) {
  $subject_list[2] = array('header' => $lang['stat_allys']);
}
stat_tpl_assign($template, $who, 'subject', $subject_list, $sn_group_stat_common);

$stat_types = array(
   STAT_TOTAL => array(
     'type' => 'total',
   ),

   STAT_FLEET => array(
     'type' => 'fleet',
   ),

   STAT_TECH => array(
     'type' => 'tech',
   ),

   STAT_BUILDING => array(
     'type' => 'build',
   ),

   STAT_DEFENSE => array(
     'type' => 'defs',
   ),

   STAT_RESOURCE => array(
     'type' => 'res',
   ),

   STAT_RAID_TOTAL => array(
     'type' => 'raids',
   ),

   STAT_RAID_WON => array(
     'type' => 'raidswin',
   ),

   STAT_RAID_LOST => array(
     'type' => 'raidsloose',
   ),

  STAT_LVL_BUILDING => array(
     'type' => 'lvl_minier',
  ),

  STAT_LVL_TECH => array(
     'type' => 'player_rpg_tech_level',
  ),

  STAT_LVL_RAID => array(
     'type' => 'lvl_raid',
  ),
);
stat_tpl_assign($template, $type, 'type', $stat_types, $sn_group_stat_common);

$Rank = $stat_types[$type]['type'];

$is_common_stat = in_array($type, $sn_group_stat_common);
$start = floor($range / 100 % 100) * 100;
$query = db_stat_list_statistic($who, $is_common_stat, $Rank, $start, $source);

// TODO - Не работает, если игроков на Блице > 100
$record_count = $source ? db_num_rows($query) : ($who == 1 ? db_user_count() : DBStaticAlly::db_ally_count());

$page_count = floor($record_count / 100);
$pages = array();
for($i = 0; $i <= $page_count; $i++) {
  $first_element = $i * 100 + 1;
  $last_element = $first_element + 99;
  $pages[$first_element] = array(
    'header' => "{$first_element}-{$last_element}",
  );
}

$range = $range > $record_count ? $record_count : $range;
stat_tpl_assign($template, $range, 'range', $pages, $sn_group_stat_common);

while ($row = db_fetch($query)) {
  $row_stat = array(
    'ID' => $row['id'],
    'RANK'        => $row['rank'],
    'RANK_CHANGE' => $row['rank_old'] ? $row['rank_old'] - $row['rank'] : 0,
    'POINTS' => HelperString::numberFloorAndFormat($row['points']),
  );

  if($who == 1) {
    $row_stat['ALLY_NAME'] = $row['ally_name'];
    $row_stat['ALLY_ID'] = $row['ally_id'];
    empty($row['username']) ? $row['username'] = $row['name'] : false;
    $row_stat['NAME'] = player_nick_render_to_html($row, [
      'icons' => empty($source),
      'color' => empty($source),
    ]);


//      // TODO - Добавлять реальное имя игрока на Блице для закрытого раунда
  } else {
    $row_stat['MEMBERS'] = $row['ally_members'];
    $row_stat['POINTS_PER_MEMBER'] = HelperString::numberFloorAndFormat(floor($row['points'] / $row['ally_members']));
    $row_stat['NAME'] = $row['name'];
  }

  $template->assign_block_vars('stat', $row_stat);
}

$next_run = sys_schedule_get_prev_run(SN::$config->stats_schedule, SN::$config->var_stat_update, true);
$template->assign_vars(array(
  'REFRESH_DATE' => SN::$config->var_stat_update ? date(FMT_DATE_TIME, strtotime(SN::$config->var_stat_update) + SN_CLIENT_TIME_DIFF) : '',
  'NEXT_DATE' => $next_run ? date(FMT_DATE_TIME, $next_run + SN_CLIENT_TIME_DIFF) : '',
  'RANGE' => $range,
  'SUBJECT' => $who,
  'TYPE' => $type,
  'USER_ALLY' => $user['ally_id'],
  // TODO - Для блица - вытаскивать blitz_player_id и подсвечивать пользователя на блице
  'USER_ID' => $source ? 0 : $user['id'],
  'SOURCE' => $source,
  'STATS_HIDE_PM_LINK' => SN::$config->stats_hide_pm_link || $source,
));

SnTemplate::display($template, $lang['stat_header']);
