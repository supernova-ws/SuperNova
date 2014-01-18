<?php

/**
 * imperator.php
 *
 * Player's information
 *
 * @version 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 */

$sn_mvc['i18n']['imperator'] = array(
  'overview' => 'overview',
);

$sn_mvc['view']['imperator'][] = 'sn_imperator_view';

function sn_imperator_view($template = null)
{
  global $template_result, $config, $lang, $user, $time_now;

  $user_id = sys_get_param_id('int_user_id', $user['id']);
  if($user_id == $user['id'])
  {
    $user_data = &$user;
    $same_user = true;
  }
  else
  {
    $user_data = doquery("SELECT * FROM {{users}} WHERE `id` = {$user_id}", true);
    $same_user = false;
  }

  if(!$user_data)
  {
    message($lang['imp_imperator_none'], $lang['sys_error'], 'index.php', 10);
    die();
  }

  $template = gettemplate('imperator', $template);
  $StatRecord = doquery("SELECT * FROM {{statpoints}} WHERE `stat_type` = 1 AND `stat_code` = 1 AND `id_owner` = {$user_id};", true);

  if($same_user)
  {
    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);
    rpg_level_up($user, RPG_TECH);
    rpg_level_up($user, RPG_EXPLORE);

    // -----------------------------------------------------------------------------------------------
    // News Frame ...
    if ($config->game_news_overview)
    {
      nws_render($template, "WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<={$time_now}", $config->game_news_overview);
    }
  }


  $template->assign_vars(array(
    'USERS_TOTAL'          => $config->users_amount,

    'USER_ID'              => $user_id,
    'user_username'        => render_player_nick($user_data, true),
    'user_sex'             => $user_data['sex'] == 'F' ? 'female' : 'male',
    'USER_AVATAR'          => $user_data['avatar'],

    'NEW_MESSAGES'         => $user_data['new_message'],
    'REGISTRATION_DATE'    => date(FMT_DATE_TIME, $user_data['register_time']),

    'builder_xp'           => pretty_number($user_data['xpminier']),
    'builder_lvl'          => pretty_number($user_data['lvl_minier']),
    'builder_lvl_st'       => pretty_number(rpg_get_miner_xp($user_data['lvl_minier'])),
    'builder_lvl_up'       => pretty_number(rpg_get_miner_xp($user_data['lvl_minier']+1)),
    'raid_xp'              => pretty_number($user_data['xpraid']),
    'raid_lvl'             => pretty_number($user_data['lvl_raid']),
    'raid_lvl_up'          => pretty_number(rpg_get_raider_xp($user_data['lvl_raid']+1)),
    'raids'                => pretty_number($user_data['raids']),
    'raidswin'             => pretty_number($user_data['raidswin']),
    'raidsloose'           => pretty_number($user_data['raidsloose']),
    'tech_xp'              => pretty_number($user_data['player_rpg_tech_xp']),
    'tech_lvl'             => pretty_number($user_data['player_rpg_tech_level']),
    'tech_lvl_st'          => pretty_number(rpg_get_tech_xp($user_data['player_rpg_tech_level'])),
    'tech_lvl_up'          => pretty_number(rpg_get_tech_xp($user_data['player_rpg_tech_level']+1)),

    'explore_xp'           => pretty_number($user_data['player_rpg_explore_xp']),
    'explore_lvl'          => pretty_number($user_data['player_rpg_explore_level']),
    'explore_lvl_st'       => pretty_number(rpg_get_explore_xp($user_data['player_rpg_explore_level'])),
    'explore_lvl_up'       => pretty_number(rpg_get_explore_xp($user_data['player_rpg_explore_level']+1)),

    'build_points'         => pretty_number( $StatRecord['build_points'] ),
    'tech_points'          => pretty_number( $StatRecord['tech_points'] ),
    'fleet_points'         => pretty_number( $StatRecord['fleet_points'] ),
    'defs_points'          => pretty_number( $StatRecord['defs_points'] ),
    'res_points'           => pretty_number( $StatRecord['res_points'] ),
    'total_points'         => pretty_number( $StatRecord['total_points'] ),
    'user_rank'            => $StatRecord['total_rank'],
    'RANK_DIFF'            => $StatRecord['total_old_rank'] - $StatRecord['total_rank'],

    'GAME_NEWS_OVERVIEW'   => $config->game_news_overview,

    'SAME_USER'            => $same_user,
  ));

  return parsetemplate($template);
}
