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

$show_groups = array(
  UNIT_STRUCTURES => 'structures',
  UNIT_STRUCTURES_SPECIAL => 'structures',
  UNIT_SHIPS => 'fleet',
  UNIT_DEFENCE => 'defense',
  UNIT_TECHNOLOGIES => 'tech',
);

$user_name_cache = array();

foreach($show_groups as $unit_group_id => $mode)
{
  $template->assign_block_vars('records', array(
    'UNIT' => $lang['tech'][$unit_group_id],
    'COUNT' => in_array($unit_id, array(UNIT_STRUCTURES, UNIT_STRUCTURES_SPECIAL, UNIT_TECHNOLOGIES)) ? $lang['sys_level'] : $lang['sys_quantity'],
    'HEADER' => true,
  ));
  $unit_group = &$sn_data['techtree'][$unit_group_id];


  foreach($unit_group as $unit_id)
  {
    $unit_name = &$lang['tech'][$unit_id];
    if($unit_name && $sn_data[$unit_id]['name'])
    {
      $unit_db_name = $sn_data[$unit_id]['name'];
      $data_row = false;
      if(in_array($unit_id, sn_get_groups(array('structures', 'fleet', 'defense'))))
      {
        $data_row = doquery (
        "SELECT `{$unit_db_name}` AS `current`, p.id_owner
        FROM {{planets}} AS p
        WHERE p.`id_owner` != '0' {$user_skip_list}
        ORDER BY p.{$unit_db_name} DESC, p.id_owner
        LIMIT 1;", true);
      }
      elseif(in_array($unit_id, sn_get_groups('tech')))
      {
        $data_row = doquery (
        "SELECT
          u.`username`, u.id, `unit_level` AS `current`
        FROM
          {{unit}} AS un
          JOIN {{users}} AS u ON u.id = un.unit_player_id AND user_as_ally IS NULL
        WHERE un.`unit_snid` = {$unit_id} {$user_skip_list_un}
        ORDER BY `unit_level` DESC, `unit_id`
        LIMIT 1;", true);
      }

      if($data_row)
      {
        if(!$data_row['username'] && !$user_name_cache[$data_row['id_owner']])
        {
          $user_name = doquery("SELECT `username` FROM {{users}} WHERE `id` = {$data_row['id_owner']} LIMIT 1", true);
          $user_name_cache[$data_row['id_owner']] = $user_name['username'];
        }
        $data_row['username'] = $data_row['username'] ? $data_row['username'] : $user_name_cache[$data_row['id_owner']];

        $template->assign_block_vars('records', array(
          'UNIT' => $unit_name,
          'USER' => $data_row['current'] ? $data_row['username'] : $lang['rec_rien'],
          'COUNT' => $data_row['current'] ? pretty_number($data_row['current']) : $lang['rec_rien'],
        ));
      }
    }
  }
}

display($template, $lang['rec_title']);
