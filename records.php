<?php

/**
 * records.php
 *
 * 2.0 - Full rewrite by Gorlum for http://supernova.ws
 * 1.4st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.4
 * @copyright 2008 by Chlorel for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(HIDE_BUILDING_RECORDS)
{
  return;
}

$template = gettemplate('records', true);

$user_skip_list_data = sys_stat_get_user_skip_list();
$user_skip_list = empty($user_skip_list_data) ? '' : (' AND p.id_owner NOT IN (' . implode(',', $user_skip_list_data) . ')');
$user_skip_list_un = empty($user_skip_list_data) ? '' : (' AND un.unit_player_id NOT IN (' . implode(',', $user_skip_list_data) . ')');

$user_skip_list_unit = empty($user_skip_list_data) ? '' : (' AND unit_player_id NOT IN (' . implode(',', $user_skip_list_data) . ')');

$show_groups = array(
  UNIT_TECHNOLOGIES => 'tech',
  UNIT_STRUCTURES => 'structures',
  UNIT_STRUCTURES_SPECIAL => 'structures',
  UNIT_SHIPS => 'fleet',
  UNIT_DEFENCE => 'defense',
);

$user_name_cache = array();

foreach($show_groups as $unit_group_id => $mode)
{
  $template->assign_block_vars('records', array(
    'UNIT' => $lang['tech'][$unit_group_id],
    'COUNT' => in_array($unit_group_id, array(UNIT_STRUCTURES, UNIT_STRUCTURES_SPECIAL, UNIT_TECHNOLOGIES)) ? $lang['sys_level_max'] : $lang['sys_quantity_total'],
    'HEADER' => true,
  ));
  $unit_group = get_unit_param('techtree', $unit_group_id); // TODO - REWRITE!!!!

  foreach($unit_group as $unit_id)
  {
    $unit_name = &$lang['tech'][$unit_id];
    if($unit_name)
    {
      if($unit_group_id == UNIT_SHIPS || $unit_group_id == UNIT_DEFENCE)
      {
        $data_row = doquery ($q =
          "SELECT unit_player_id, username, sum(unit_level) as unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          GROUP BY unit_player_id
          ORDER BY sum(unit_level) DESC, unit_player_id
          LIMIT 1;"
        , true);
      }
      else
      {
        $data_row = doquery (
          "SELECT unit_player_id, username, unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          ORDER BY unit_level DESC, unit_id
          LIMIT 1;"
        , true);
      }

      if($data_row)
      {
        /*
        if(!$data_row['username'] && !$user_name_cache[$data_row['id_owner']])
        {
          $user_name = doquery("SELECT `username` FROM {{users}} WHERE `id` = {$data_row['id_owner']} LIMIT 1", true);
          $user_name_cache[$data_row['id_owner']] = $user_name['username'];
        }
        $data_row['username'] = $data_row['username'] ? $data_row['username'] : $user_name_cache[$data_row['id_owner']];
        */

        $template->assign_block_vars('records', array(
          'UNIT' => $unit_name,
          'USER' => $data_row['username'] ? js_safe_string($data_row['username']) : $lang['rec_rien'],
          'COUNT' => $data_row['unit_level'] ? pretty_number($data_row['unit_level']) : $lang['rec_rien'],
        ));
      }
    }
  }
}

display($template, $lang['rec_title']);
