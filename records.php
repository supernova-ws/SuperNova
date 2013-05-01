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

$user_skip_list = sys_stat_get_user_skip_list();
$user_skip_list = empty($user_skip_list) ? '' : ' AND u.id NOT IN (' . implode(',', $user_skip_list) . ')';

$show_groups = array(
  UNIT_STRUCTURES => 'structures',
  UNIT_STRUCTURES_SPECIAL => 'structures',
  UNIT_SHIPS => 'fleet',
  UNIT_DEFENCE => 'defense',
  UNIT_TECHNOLOGIES => 'tech',
);

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
      if(in_array($unit_id, array_merge($sn_data['groups']['structures'], $sn_data['groups']['fleet'], $sn_data['groups']['defense'])))
      {
        $data_row = doquery ("SELECT `username`, `{$unit_db_name}` AS `current` FROM {{planets}} AS p JOIN {{users}} AS u ON u.id = p.id_owner WHERE `{$unit_db_name}` = (
        SELECT MAX(`{$unit_db_name}`) FROM {{planets}} AS p LEFT JOIN {{users}} AS u ON u.id = p.id_owner WHERE `id_owner` != 0 {$user_skip_list}) AND `id_owner` != '0' {$user_skip_list} ORDER BY u.`id` LIMIT 1;", true);
      }
      elseif(in_array($unit_id, $sn_data['groups']['tech']))
      {
//        $data_row = doquery ("SELECT `username`, `{$unit_db_name}` AS `current` FROM {{users}} AS u WHERE u.`{$unit_db_name}` = (SELECT MAX(`{$unit_db_name}`) FROM {{users}} AS u WHERE user_as_ally is null {$user_skip_list}) AND user_as_ally is null {$user_skip_list} ORDER BY `id` LIMIT 1;", true);
        $data_row = doquery ("SELECT `username`, `unit_level` AS `current` FROM {{unit}} as un LEFT JOIN {{users}} AS u ON u.id = un.unit_player_id WHERE un.`unit_snid` = {$unit_id} AND user_as_ally is null {$user_skip_list} ORDER BY `unit_level` DESC, `unit_id` LIMIT 1;", true);
      }

      if($data_row)
      {
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

?>
