<?php

function art_use(&$user, &$planetrow, $unit_id)
{
  global $sn_data, $lang;

  if(!in_array($unit_id, $sn_data['groups']['artifacts']))
  {
    return;
  }

  sn_db_transaction_start();
  $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", '', true);

  $artifact_list = sys_unit_str2arr($user['player_artifact_list']);
  if($artifact_list[$unit_id])
  {
    switch($unit_id)
    {
      case ART_LHC:
        $has_moon = doquery("SELECT `id` FROM `{{planets}}` WHERE parent_planet = {$planetrow['id']} LIMIT 1;", true);
        if($planetrow['planet_type'] == PT_PLANET && !$has_moon['id'])
        {
          $artifact_list[$unit_id]--;
          $moon_chance = uni_calculate_moon_chance($planetrow['debris_metal'] + $planetrow['debris_crystal']);
          $random = mt_rand(1, 100);
          if($random <= $moon_chance)
          {
            $new_moon_name = uni_create_moon($planetrow['galaxy'], $planetrow['system'], $planetrow['planet'], $user['id'], $moon_chance);
            $message = sprintf($lang['art_lhc_moon_create'], $new_moon_name, uni_render_coordinates($planetrow));
          }
          else
          {
            $message = $lang['art_lhc_moon_fail'];
          }
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_ADMIN, $lang['art_lhc_from'], $lang['art_lhc_subj'], $message);
        }
        else
        {
          $message = $lang['art_lhc_moon_exists'];
        }
      break;

      case ART_RCD_SMALL:
      case ART_RCD_MEDIUM:
      case ART_RCD_LARGE:
        $planetrow = doquery("SELECT * FROM {{planets}} WHERE `id` = {$planetrow['id']} LIMIT 1 FOR UPDATE;", true);
        if($planetrow['planet_type'] != PT_PLANET)
        {
          $message = $lang['art_rcd_err_moon'];
          break;
        }

        if($planetrow['que'])
        {
          $message = $lang['art_rcd_err_moon'];
          break;
        }

        $artifact_list[$unit_id]--;
        $artifact_deploy = &$sn_data[$unit_id]['deploy'];

        $deployment_str = '';
        $sectors_used = 0;
        foreach($artifact_deploy as $deploy_unit_id => $deploy_unit_level)
        {
          $deploy_unit_name = $sn_data[$deploy_unit_id]['name'];
          $sectors_used += max(0, $deploy_unit_level - $planetrow[$deploy_unit_name]);
          $deployment_str .= ",`{$deploy_unit_name}` = GREATEST(`{$deploy_unit_name}`, {$deploy_unit_level})";
        }

        if($sectors_used == 0)
        {
          $message = $lang['art_rcd_err_no_sense'];
          break;
        }
        doquery("UPDATE {{planets}} SET `field_current` = `field_current` + {$sectors_used}{$deployment_str} WHERE `id` = {$planetrow['id']} LIMIT 1;");
        $message = sprintf($lang['art_rcd_ok'], $lang['tech'][$unit_id], $planetrow['name'], uni_render_coordinates($planetrow));
        msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_rcd_subj'], $lang['art_rcd_subj'], $message);
      break;

      case ART_HEURISTIC_CHIP:
        que_get_que($global_que, QUE_RESEARCH, $user['id'], $planetrow['id'], true);
        if(isset($global_que[QUE_RESEARCH][0][0]['que_time_left']) && $global_que[QUE_RESEARCH][0][0]['que_time_left'] > 0)
        {
          $old_time = $global_que[QUE_RESEARCH][0][0]['que_time_left'];
          $global_que[QUE_RESEARCH][0][0]['que_time_left'] = max(0, $global_que[QUE_RESEARCH][0][0]['que_time_left'] - PERIOD_HOUR);
          $artifact_list[$unit_id]--;
          doquery("UPDATE {{que}} SET `que_time_left` = {$global_que[QUE_RESEARCH][0][0]['que_time_left']} WHERE `que_id` = {$global_que[QUE_RESEARCH][0][0]['que_id']} LIMIT 1;");
          $message = sprintf($lang['art_heurestic_chip_ok'], $lang['tech'][$global_que[QUE_RESEARCH][0][0]['que_unit_id']], $global_que[QUE_RESEARCH][0][0]['que_unit_level'], $old_time - $global_que[QUE_RESEARCH][0][0]['que_time_left']);
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_heurestic_chip_subj'], $lang['art_heurestic_chip_subj'], $message);
        }
        else
        {
          $message = $lang['art_heurestic_chip_no_research'];
        }
      break;

      case ART_NANO_BUILDER:
        $planetrow = doquery("SELECT * FROM {{planets}} WHERE `id` = {$planetrow['id']} LIMIT 1 FOR UPDATE;", true);
        $que = eco_que_process($user, $planetrow, 0);
        $que_item = &$que['que'][QUE_STRUCTURES][0];
        if(isset($que_item['TIME']) && $que_item['TIME'] > 0)
        {
          $old_time = $que_item['TIME'];
          $que_item['TIME'] = max(0, $que_item['TIME'] - PERIOD_HOUR);
          $artifact_list[$unit_id]--;
          $que_item['STRING'] = "{$que_item['ID']},{$que_item['AMOUNT']},{$que_item['TIME']},{$que_item['MODE']},{$que_item['QUE']};";
          $query_string = '';
          foreach($que['que'][QUE_STRUCTURES] as $value)
          {
            $query_string .= $value['STRING'];
          }
          doquery("UPDATE {{planets}} SET `que` = '{$query_string}' WHERE `id` = {$planetrow['id']} LIMIT 1;");
          $message = sprintf($lang['art_nano_builder_ok'], $que_item['MODE'] == BUILD_CREATE ? $lang['art_nano_builder_build'] : $lang['art_nano_builder_destroy'], $lang['tech'][$que_item['ID']], $que_item['AMOUNT'], $planetrow['name'], uni_render_coordinates($planetrow), $old_time - $que_item['TIME']);
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_nano_builder_subj'], $lang['art_nano_builder_subj'], $message);
        }
        else
        {
          $message = $lang['art_nano_builder_no_que'];
        }
      break;

    }
    $artifact_list = sys_unit_arr2str($artifact_list);
    if($artifact_list != $user['player_artifact_list'])
    {
      doquery("UPDATE {{users}} SET `player_artifact_list` = '{$artifact_list}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    }
  }
  else
  {
    $message = $lang['art_err_no_artifact'];
  }

  sn_db_transaction_commit();
  message($message, "{$lang['tech'][UNIT_ARTIFACTS]} - {$lang['tech'][$unit_id]}", 'artifacts.' . PHP_EX . '#' . $unit_id, 5); // <br /><a href=\"artifacts." . PHP_EX . "#{$unit_id}><\""
}
