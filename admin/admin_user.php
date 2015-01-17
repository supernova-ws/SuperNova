<?php

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage($lang['adm_err_denied']);
}

lng_include('admin');

$user_id = sys_get_param_id('uid');
if(!($user_row = db_user_by_id($user_id))) {
  AdminMessage(sprintf($lang['adm_dm_user_none'], $user_id));
}

$template = gettemplate('admin/admin_user', true);

$temp = doquery("SELECT browser_user_agent FROM {{security_browser}} WHERE `browser_id` = {$user_row['user_last_browser_id']}", true);
$user_row['browser_user_agent'] = $temp['browser_user_agent'];

$formats = array(
  'sys_time_human_system' => array(
    'register_time',
    'onlinetime',
    'ally_register_time',
    'news_lastread',
    'banaday',
    'vacation',
    'vacation_next',
    'deltime',
    'que_processed',
    'user_time_measured',
  ),
  'pretty_number' => array(
    'metal',
    'crystal',
    'deuterium',
    'dark_matter_total',
    'metamatter',
    'metamatter_total',

    'player_rpg_explore_xp',
    'player_rpg_explore_level',
    'lvl_minier',
    'xpminier',
    'player_rpg_tech_xp',
    'player_rpg_tech_level',
    'lvl_raid',
    'xpraid',
    'raids',
    'raidsloose',
    'raidswin',
    'total_rank',
    'total_points',  ),
);
foreach($formats as $callable => $field_list) {
  foreach($field_list as $field_name) {
    $user_row[$field_name] = call_user_func($callable, $user_row[$field_name]);
  }
}

//$user_row['register_time'] = sys_time_human_system($user_row['register_time']);
//$user_row['onlinetime'] = sys_time_human_system($user_row['onlinetime']);
//$user_row['ally_register_time'] = sys_time_human_system($user_row['ally_register_time']);
//$user_row['news_lastread'] = sys_time_human_system($user_row['news_lastread']);
//
//$user_row['banaday'] = sys_time_human_system($user_row['banaday']);
//$user_row['vacation'] = sys_time_human_system($user_row['vacation']);
//$user_row['vacation_next'] = sys_time_human_system($user_row['vacation_next']);
//$user_row['deltime'] = sys_time_human_system($user_row['deltime']);
//
//$user_row['que_processed'] = sys_time_human_system($user_row['que_processed']);
//$user_row['user_time_measured'] = sys_time_human_system($user_row['user_time_measured']);


$template->assign_vars($user_row);
display($template, htmlentities("[{$user_row['id']}] {$user_row['username']}", ENT_QUOTES, 'UTF-8'), false, '', true);
