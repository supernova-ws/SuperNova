<?php

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

lng_include('admin');

$user_id = sys_get_param_id('uid');
if(!($user_row = db_user_by_id($user_id))) {
  AdminMessage(sprintf(classLocale::$lang['adm_dm_user_none'], $user_id));
}

$template = gettemplate('admin/admin_user', true);

if(!empty($user_row['user_last_browser_id'])) {
  $user_row['browser_user_agent'] = db_browser_agent_get_by_id($user_row['user_last_browser_id']);
}

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

$template->assign_vars($user_row);
display($template, htmlentities("[{$user_row['id']}] {$user_row['username']}", ENT_QUOTES, 'UTF-8'), false, '', true);
