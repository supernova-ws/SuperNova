<?php

/**
 * imperator.php
 *
 * Player's information
 *
 * @version 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 */

$sn_i18n['pages']['imperator'] = array(
  'overview' => 'overview',
);

$sn_mvc['view']['imperator'][] = 'sn_imperator_view';

function sn_imperator_view($template = null)
{
  global $template_result, $config, $lang, $user, $time_now;

  $template = gettemplate('imperator', $template);
  $StatRecord = doquery("SELECT * FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", '', true);

  // -----------------------------------------------------------------------------------------------
  // News Frame ...
  if ($config->game_news_overview)
  {
    nws_render($template, "WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<={$time_now}", $config->game_news_overview);
  }

  $template->assign_vars(array(
    'USERS_TOTAL'          => $config->users_amount,

    'USER_ID'              => $user['id'],
    'user_username'        => render_player_nick($user, true),//$user['username'],
    'user_sex'             => $user['sex'] == 'F' ? 'female' : 'male',
    'USER_AVATAR'          => $user['avatar'],

    'NEW_MESSAGES'         => $user['new_message'],
    'REGISTRATION_DATE'    => date(FMT_DATE_TIME, $user['register_time']),

    'builder_xp'           => pretty_number($user['xpminier']),
    'builder_lvl'          => pretty_number($user['lvl_minier']),
    'builder_lvl_up'       => pretty_number(rpg_get_miner_xp($user['lvl_minier']+1)),
    'raid_xp'              => pretty_number($user['xpraid']),
    'raid_lvl'             => pretty_number($user['lvl_raid']),
    'raid_lvl_up'          => pretty_number(rpg_get_raider_xp($user['lvl_raid']+1)),
    'raids'                => pretty_number($user['raids']),
    'raidswin'             => pretty_number($user['raidswin']),
    'raidsloose'           => pretty_number($user['raidsloose']),
    'tech_xp'              => pretty_number($user['player_rpg_tech_xp']),
    'tech_lvl'             => pretty_number($user['player_rpg_tech_level']),
    'tech_lvl_up'          => pretty_number(rpg_get_tech_xp($user['player_rpg_tech_level']+1)),
    'build_points'         => pretty_number( $StatRecord['build_points'] ),
    'tech_points'          => pretty_number( $StatRecord['tech_points'] ),
    'fleet_points'         => pretty_number( $StatRecord['fleet_points'] ),
    'defs_points'          => pretty_number( $StatRecord['defs_points'] ),
    'res_points'           => pretty_number( $StatRecord['res_points'] ),
    'total_points'         => pretty_number( $StatRecord['total_points'] ),
    'user_rank'            => $StatRecord['total_rank'],
    'RANK_DIFF'            => $StatRecord['total_old_rank'] - $StatRecord['total_rank'],

    'GAME_NEWS_OVERVIEW'   => $config->game_news_overview,
  ));

  return parsetemplate($template);
}

?>
