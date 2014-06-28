<?php

/**
 * stat.php
 *
 * 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 *   [!] Full rewrote
*/

function stat_tpl_assign(&$template, $selected, $array_name, $array, $sn_group_stat_common)
{
  global $who, $lang;

  // $sn_group_stat_common = sn_get_groups('STAT_COMMON');
  foreach($array as $key => $value)
  {
    if($array_name == 'type' && $who == 2 && !in_array($key, $sn_group_stat_common)) // $key > 6 &&
    {
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

$template = gettemplate('stat_statistics', true);
stat_tpl_assign($template, $who, 'subject', array(
  1 => array('header' => $lang['stat_player']),
  2 => array('header' => $lang['stat_allys']),
), $sn_group_stat_common);

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

$record_count = $who == 1 ? db_user_count() : db_ally_count();

$page_count = floor($record_count / 100);
$pages = array();
for($i = 0; $i <= $page_count; $i++)
{
  $first_element = $i * 100 + 1;
  $last_element = $first_element + 99;
  $pages[$first_element] = array(
    'header' => "{$first_element}-{$last_element}",
  );
}

$range = $range > $record_count ? $record_count : $range;
stat_tpl_assign($template, $range, 'range', $pages, $sn_group_stat_common);

$is_common_stat = in_array($type, $sn_group_stat_common);
$start = floor($range / 100 % 100) * 100;
$query = db_stat_list_statistic($who, $is_common_stat, $Rank, $start);
while ($row = mysql_fetch_assoc($query))
{
  $row_stat = array(
      'ID' => $row['id'],
      'RANK'        => $row['rank'],
      'RANK_CHANGE' => $row['rank_old'] - $row['rank'],
      'POINTS' => pretty_number($row['points']),
  );

  if($who == 1)
  {
    $row_stat['BIRTHDAY'] = date(FMT_DATE, $row['nearest_birthday']);
    $row_stat['BIRTHDAY_TODAY'] = $row_stat['BIRTHDAY'] == date(FMT_DATE, $time_now);
    $row_stat['SEX'] = $row['sex'];
    $row_stat['ALLY_NAME'] = $row['ally_name'];
    $row_stat['ALLY_ID'] = $row['ally_id'];
    $row_stat['NAME'] = render_player_nick($row, array('icons' => true));
  }
  else
  {
    $row_stat['MEMBERS'] = $row['ally_members'];
    $row_stat['POINTS_PER_MEMBER'] = pretty_number(floor($row['points'] / $row['ally_members']));
    $row_stat['NAME'] = $row['name'];
  }

  $template->assign_block_vars('stat', $row_stat);
}

$template->assign_vars(array(
  'REFRESH_DATE' => $config->var_stat_update ? date(FMT_DATE_TIME, $config->var_stat_update + $time_diff) : '',
  'NEXT_DATE' => sys_schedule_get_prev_run($config->stats_schedule, $config->var_stat_update, SN_TIME_NOW) ?
      date(FMT_DATE_TIME, sys_schedule_get_prev_run($config->stats_schedule, $config->var_stat_update, SN_TIME_NOW) + $time_diff)
      : '',
  'RANGE' => $range,
  'SUBJECT' => $who,
  'TYPE' => $type,
  'USER_ALLY' => $user['ally_id'],
  'USER_ID' => $user['id'],
  'STATS_HIDE_PM_LINK' => $config->stats_hide_pm_link,
));

display($template, $lang['stat_header'], !empty($user), '', false, !empty($user));
