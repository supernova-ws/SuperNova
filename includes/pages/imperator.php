<?php

/**
 * imperator.php
 *
 * Player's information
 *
 * @version 2.0 copyright (c) 2010-2012 by Gorlum for http://supernova.ws
 */

use DBStatic\DBStaticUser;

classSupernova::$sn_mvc['i18n']['imperator'] = array(
  'overview' => 'overview',
);

classSupernova::$sn_mvc['view']['imperator'][] = 'sn_imperator_view';

function sn_imperator_view($template = null) {
  global $user;

  $stat_fields = array(
    'stat_date' => 'STAT_DATE',

    // 'stat_code' => 'STAT_CODE',
    'total_rank' => 'TOTAL_RANK',
    'total_points' => 'TOTAL_POINTS',
    //'total_count' => 'TOTAL_COUNT',
    'tech_rank' => 'TECH_RANK',
    'tech_points' => 'TECH_POINTS',
    //'tech_count' => 'TECH_COUNT',
    'build_rank' => 'BUILD_RANK',
    'build_points' => 'BUILD_POINTS',
    //'build_count' => 'BUILD_COUNT',
    'defs_rank' => 'DEFS_RANK',
    'defs_points' => 'DEFS_POINTS',
    //'defs_count' => 'DEFS_COUNT',
    'fleet_rank' => 'FLEET_RANK',
    'fleet_points' => 'FLEET_POINTS',
    //'fleet_count' => 'FLEET_COUNT',
    'res_rank' => 'RES_RANK',
    'res_points' => 'RES_POINTS',
    // 'res_count' => 'RES_COUNT',
  );

  $user_id = sys_get_param_id('int_user_id', $user['id']);

  $user_data = ($same_user = $user_id == $user['id']) ? $user : DBStaticUser::db_user_by_id($user_id);

//  if($user_id == $user['id']) {
//    $user_data = &$user;
//    $same_user = true;
//  } else {
//    $user_data = db_user_by_id($user_id);
//    $same_user = false;
//  }

  if(!$user_data) {
    message(classLocale::$lang['imp_imperator_none'], classLocale::$lang['sys_error'], 'index.php', 10);
    die();
  }

  $template = gettemplate('imperator', $template);
  $StatRecord = db_stat_get_by_user($user_id);

  $stat_array = array();
  $query = db_stat_get_by_user2($user_id);
  $stat_count = classSupernova::$db->db_affected_rows();
  while($row = db_fetch($query)) {
    foreach($stat_fields as $field_db_name => $field_template_name) {
      // $stat_count - $row['stat_code'] - для реверсирования ID статы в JS
      $stat_array[$field_template_name]['DATA'][$stat_count - $row['stat_code']] = $row[$field_db_name];
    }
  }

  $stat_array_date = $stat_array['STAT_DATE'];
  empty($stat_array_date['DATA']) ? $stat_array_date['DATA'] = array() : false;
  foreach($stat_array_date['DATA'] as $key => $value) {
    $template->assign_block_vars('stat_date', array(
      'ID' => $key,
      'VALUE' => $value,
      'TEXT' => date(FMT_DATE_TIME, $value),
    ));
  }
  // $stat_count = count($stat_array_date['DATA']);
  // pdump($stat_array_date);


  unset($stat_array['STAT_DATE']);
  $template_data = array();
  foreach($stat_array as $stat_type => &$stat_type_data) {
    $reverse_min_max = strpos($stat_type, '_RANK') !== false;
    $stat_type_data['MIN'] = $reverse_min_max ? max($stat_type_data['DATA']) : min($stat_type_data['DATA']);
    $stat_type_data['MAX'] = $reverse_min_max ? min($stat_type_data['DATA']) : max($stat_type_data['DATA']);
    $stat_type_data['AVG'] = \Common\snMath::average($stat_type_data['DATA']);
    foreach($stat_type_data['DATA'] as $key => $value) {
      // $stat_type_data['PERCENT'][$key] = $stat_type_data['MAX'] - $value ? ($stat_type_data['MAX'] - $stat_type_data['MIN']) / ($stat_type_data['MAX'] - $value) : 100;
      $stat_type_data['PERCENT'][$key] = ($stat_type_data['MAX'] - $value ? ($value - $stat_type_data['MIN']) / ($stat_type_data['MAX'] - $stat_type_data['MIN']) : 1) * 100;
      $template_data[$stat_type][$key]['ID'] = $key;
      $template_data[$stat_type][$key]['VALUE'] = $value;
      $template_data[$stat_type][$key]['DELTA'] = ($reverse_min_max ? $stat_type_data['MIN']  - $value : $value - $stat_type_data['MAX']);
      $template_data[$stat_type][$key]['PERCENT'] = $stat_type_data['PERCENT'][$key];

//$template_data[$stat_type][$key]['PERCENT'] = $key ? $stat_type_data['PERCENT'][$key] : 50; // TODO DEBUG
    }
  }
  // pdump($stat_array['RES_POINTS']);

  foreach($template_data as $stat_type => $stat_type_data) {
    $template->assign_block_vars('stat', array(
      'TYPE' => $stat_type,
      'TEXT' => classLocale::$lang['imp_stat_types'][$stat_type],
      'MIN' => $stat_array[$stat_type]['MIN'],
      'MAX' => $stat_array[$stat_type]['MAX'],
      'AVG' => $stat_array[$stat_type]['AVG'],
    ));
    foreach($stat_type_data as $stat_entry) {
      $template->assign_block_vars('stat.entry', $stat_entry);
    }
  }


  // pdump($template_data);

  if($same_user) {
    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);
    rpg_level_up($user, RPG_TECH);
    rpg_level_up($user, RPG_EXPLORE);
  }


  $template->assign_vars(array(
    'USERS_TOTAL'          => classSupernova::$config->users_amount,

    'USER_ID'              => $user_id,
    'user_username'        => player_nick_render_to_html($user_data, true),
    // 'user_gender'             => $user_data['gender'] == 'F' ? 'female' : 'male',
    'USER_AVATAR'          => $user_data['avatar'],
    'VACATION'             => $user_data['vacation'],
    'GENDER_TEXT'          => classLocale::$lang['sys_gender_list'][$user_data['gender']],

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

    'STAT_COUNT'           => $stat_count,
    'STAT_SPAN'            => $stat_count + 1,

    'SAME_USER'            => $same_user,
  ));

  return parsetemplate($template);
}
