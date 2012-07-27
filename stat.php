<?php

/**
 * stat.php
 *
 * 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 *   [!] Full rewrote
*/

function stat_tpl_assign(&$template, $selected, $array_name, $array)
{
  global $who, $sn_data, $lang;

  foreach($array as $key => $value)
  {
    if($array_name == 'type' && $who == 2 && !in_array($key, $sn_data['groups']['STAT_COMMON'])) // $key > 6 && 
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

$parse = $lang;
$who = sys_get_param_int('who', 1);
$type = sys_get_param_int('type');
$type = $who != 1 && !in_array($type, $sn_data['groups']['STAT_COMMON']) ? 1 : $type;
$range = sys_get_param_int('range', 1);

$template = gettemplate('stat_statistics', true);
stat_tpl_assign($template, $who, 'subject', array(
  1 => array('header' => $lang['stat_player']),
  2 => array('header' => $lang['stat_allys']),
));

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
stat_tpl_assign($template, $type, 'type', $stat_types);

$Rank = $stat_types[$type]['type'];

if($who == 1)
{
  $record_count = doquery("SELECT COUNT(*) AS `count` FROM {{users}} WHERE `deltime` = '0' and user_as_ally IS NULL", '', true);
}
else
{
  $record_count = doquery("SELECT COUNT(*) AS `count` FROM {{alliance}}", '', true);
}

$record_count = $record_count['count'];
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
stat_tpl_assign($template, $range, 'range', $pages);
$start = floor($range / 100 % 100) * 100;

if($who == 1)
{
  if(in_array($type, $sn_data['groups']['STAT_COMMON']))
  {
//      @rownum:=@rownum+1 rownum, subject.id, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.username as name, subject.ally_name, subject.ally_id, subject.sex, UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
    $query_str = 
    "SELECT
      @rownum:=@rownum+1 rownum, subject.id, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.username as name, subject.*, UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
    FROM
      (SELECT @rownum:={$start}) r,
      {{statpoints}} as sp
      LEFT JOIN {{users}} AS subject ON subject.id = sp.id_owner
      LEFT JOIN {{statpoints}} AS sp_old ON sp_old.id_owner = subject.id AND sp_old.`stat_type` = 1 AND sp_old.`stat_code` = 2
    WHERE
      sp.`stat_type` = 1 AND sp.`stat_code` = 1
    ORDER BY
      sp.`{$Rank}_rank`, subject.id
    LIMIT
      ". $start .",100;";
  }
  else
  {
//      @rownum:=@rownum+1 AS rank, subject.id, @rownum as rank_old, subject.{$Rank} as points, subject.username as name, subject.ally_name, subject.ally_id, subject.sex, UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
    $query_str = 
    "SELECT
      @rownum:=@rownum+1 AS rank, subject.id, @rownum as rank_old, subject.{$Rank} as points, subject.username as name, subject.*, UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
    FROM
      (SELECT @rownum:={$start}) r,
      {{users}} AS subject
    WHERE
      subject.user_as_ally is null
    ORDER BY
      subject.{$Rank} DESC, subject.id
    LIMIT
      ". $start .",100;";
  }
}
else
{
  $query_str = 
  "SELECT 
    @rownum:=@rownum+1 as rownum, subject.id, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.ally_name as name, subject.ally_tag, subject.ally_members
  FROM
    (SELECT @rownum:={$start}) r,
    {{statpoints}} AS sp
    LEFT JOIN {{alliance}} AS subject ON subject.id = sp.id_ally
    LEFT JOIN {{statpoints}} AS sp_old ON sp_old.id_ally = subject.id AND sp_old.`stat_type` = 2 AND sp_old.`stat_code` = 2
  WHERE
    sp.`stat_type` = 2 AND sp.`stat_code` = 1
  ORDER BY
    sp.`{$Rank}_rank`, subject.id
  LIMIT
    ". $start .",100;";
}

$query = doquery($query_str);

while ($row = mysql_fetch_assoc($query))
{
  $row_stat = array(
      'ID' => $row['id'],
//      'RANK'        => $row['rownum'] + $start,
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
  'REFRESH_DATE' => date(FMT_DATE_TIME, $config->var_stat_update),
  'RANGE' => $range,
  'SUBJECT' => $who,
  'TYPE' => $type,
  'USER_ALLY' => $user['ally_id'],
  'USER_ID' => $user['id'],
));

display($template, $lang['stat_header'], $IsUserChecked, '', false, $IsUserChecked);

?>
