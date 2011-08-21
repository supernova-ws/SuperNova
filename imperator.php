<?php

/**
 * imperator.php
 *
 * Player's information
 *
 * @version 1.0 copyright (c) 2010 by Gorlum for http://supernova.ws
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('affilates');
lng_include('overview');

$template = gettemplate('imperator', true);

// SuperNova's banner for users to use
if ($config->int_banner_showInOverview)
{
  $delimiter = strpos($config->int_banner_URL, '?') ? '&' : '?';
  $template->assign_vars(array(
    'BANNER_URL' => SN_ROOT_VIRTUAL . "{$config->int_banner_URL}{$delimiter}id={$user['id']}",
  ));
}

// SuperNova's userbar to use on forums
if ($config->int_userbar_showInOverview)
{
  $delimiter = strpos($config->int_userbar_URL, '?') ? '&' : '?';

  $template->assign_vars(array(
    'USERBAR_URL' => SN_ROOT_VIRTUAL . "{$config->int_userbar_URL}{$delimiter}id={$user['id']}",
  ));
}

$StatRecord = doquery("SELECT * FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", '', true);

$ile                           = $StatRecord['total_old_rank'] - $StatRecord['total_rank'];
if ($ile >= 1)
{
  $parse['ile']              = "<font color=lime>+" . $ile . "</font>";
}
elseif ($ile < 0)
{
  $parse['ile']              = "<font color=red>-" . $ile . "</font>";
}
elseif ($ile == 0)
{
  $parse['ile']              = "<font color=lightblue>" . $ile . "</font>";
}

$day_of_week = $lang['weekdays'][date('w')];
$day         = date('d');
$month       = $lang['months'][date('m')];
$year        = date('Y');
$hour        = date('H');
$min         = date('i');
$sec         = date('s');

//Подсчет кол-ва онлайн и кто онлайн
$time = $time_now - 15*60;
$OnlineUsersNames2 = doquery("SELECT `username` FROM {{users}} WHERE `onlinetime`>'{$time}'");

/*
//Последние сообщения чата.
$mess = doquery("SELECT `user`,`message` FROM {{chat}} WHERE `ally_id` = '0' ORDER BY `messageid` DESC LIMIT 5");
$msg = '<table>';
while ($result = mysql_fetch_assoc($mess)) {
  //$str = substr($result['message'], 0, 85);
  $str = $result['message'];
  $usr = $result['user'];
  $msg .= "<tr><td align=\"left\">".$usr.":</td><td>".$str."</td></tr>";
}
$msg .= '</table>';
*/
// -----------------------------------------------------------------------------------------------
// News Frame ...
if ($config->game_news_overview)
{
  nws_render($template, "WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<={$time_now}", $config->game_news_overview);
}

$template->assign_vars(array(
  'TIME_NOW'             => $time_now,
  'TIME_TEXT'            => "$day_of_week, $day $month $year {$lang['ov_of_year']},",

  'USERS_ONLINE'         => mysql_num_rows($OnlineUsersNames2),
  'USERS_TOTAL'          => $config->users_amount,

  'USER_ID'              => $user['id'],
  'USER_AUTHLEVEL'       => $user['authlevel'],
  'user_username'        => $user['username'],

  'NEW_MESSAGES'         => $user['new_message'],
  'REGISTRATION_DATE'    => date(FMT_DATE_TIME, $user['register_time']),

  'builder_xp'           => pretty_number($user['xpminier']),
  'builder_lvl'          => pretty_number($user['lvl_minier']),
  'builder_lvl_up'       => pretty_number(rpg_get_miner_xp($user['lvl_minier'])),
  'raid_xp'              => pretty_number($user['xpraid']),
  'raid_lvl'             => pretty_number($user['lvl_raid']),
  'raid_lvl_up'          => pretty_number(rpg_get_raider_xp($user['lvl_raid'])),
  'raids'                => pretty_number($user['raids']),
  'raidswin'             => pretty_number($user['raidswin']),
  'raidsloose'           => pretty_number($user['raidsloose']),
  'user_points'          => pretty_number( $StatRecord['build_points'] ),
  'user_fleet'           => pretty_number( $StatRecord['fleet_points'] ),
  'player_points_tech'   => pretty_number( $StatRecord['tech_points'] ),
  'user_defs_points'     => pretty_number( $StatRecord['defs_points'] ),
  'total_points'         => pretty_number( $StatRecord['total_points'] ),
  'user_rank'            => $StatRecord['total_rank'],
  'RANK_DIFF'            => $StatRecord['total_old_rank'] - $StatRecord['total_rank'],

  'GAME_NEWS_OVERVIEW'   => $config->game_news_overview,

  //'LastChat'       => CHT_messageParse($msg),
));

display(parsetemplate($template, $parse), "{$lang['imp_imperator']} {$user['username']}");
?>