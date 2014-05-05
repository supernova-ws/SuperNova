<?php

function art_use(&$user, &$planetrow, $unit_id)
{
  global $lang;

  if(!in_array($unit_id, sn_get_groups('artifacts')))
  {
    return;
  }

  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true);

  $unit_level = $artifact_level_old = mrc_get_level($user, array(), $unit_id, true);
  if($unit_level > 0)
  {
    $db_changeset = array();
    switch($unit_id)
    {
      case ART_LHC:
      case ART_HOOK_SMALL:
      case ART_HOOK_MEDIUM:
      case ART_HOOK_LARGE:
        $has_moon = db_planet_by_parent($planetrow['id'], true, '`id`');
        if($planetrow['planet_type'] == PT_PLANET && !$has_moon['id'])
        {
          $unit_level--;
          $moon_chance = $unit_id == ART_LHC ? uni_calculate_moon_chance($planetrow['debris_metal'] + $planetrow['debris_crystal']) : (
            $unit_id == ART_HOOK_MEDIUM ? mt_rand(1100, 8999) : ($unit_id == ART_HOOK_SMALL ? 1100 : 8999)
          );
          $random = $unit_id == ART_LHC ? mt_rand(1, 100) : $moon_chance;
          if($random <= $moon_chance)
          {
            $new_moon_name = uni_create_moon($planetrow['galaxy'], $planetrow['system'], $planetrow['planet'], $user['id'], $moon_chance);
            $message = sprintf($lang['art_moon_create'][$unit_id], $new_moon_name, uni_render_coordinates($planetrow), pretty_number($moon_chance));
          }
          else
          {
            $message = $lang['art_lhc_moon_fail'];
          }
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_ADMIN, $lang['art_lhc_from'], $lang['art_lhc_subj'], $message);
        }
        else
        {
          $message = $lang['art_moon_exists'];
        }
      break;

      case ART_RCD_SMALL:
      case ART_RCD_MEDIUM:
      case ART_RCD_LARGE:
        $planetrow = db_planet_by_id($planetrow['id'], true);
        if($planetrow['planet_type'] != PT_PLANET)
        {
          $message = $lang['art_rcd_err_moon'];
          break;
        }

        $que = que_get(QUE_STRUCTURES, $user['id'], $planetrow['id'], false);
        if(!empty($que['items']))
        {
          $message = $lang['art_rcd_err_que'];
          break;
        }

        $artifact_deploy = get_unit_param($unit_id, P_DEPLOY);

        // $deployment_str = '';
        $sectors_used = 0;
        foreach($artifact_deploy as $deploy_unit_id => $deploy_unit_level)
        {
          if(!($levels_deployed = max(0, $deploy_unit_level - mrc_get_level($user, $planetrow, $deploy_unit_id, true, true))))
            continue;
          $sectors_used += $levels_deployed;
          $db_changeset['unit'][] = sn_db_unit_changeset_prepare($deploy_unit_id, $levels_deployed, $user, $planetrow['id']);
          //$deploy_unit_name = get_unit_param($deploy_unit_id, P_NAME);
          //$deployment_str .= ",`{$deploy_unit_name}` = GREATEST(`{$deploy_unit_name}`, {$deploy_unit_level})";
        }

        if($sectors_used == 0)
        {
          $message = $lang['art_rcd_err_no_sense'];
          break;
        }
        $unit_level--;
        db_planet_set_by_id($planetrow['id'], "`field_current` = `field_current` + {$sectors_used}");
        $message = sprintf($lang['art_rcd_ok'], $lang['tech'][$unit_id], $planetrow['name'], uni_render_coordinates($planetrow));
        msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_rcd_subj'], $lang['art_rcd_subj'], $message);
      break;

      case ART_HEURISTIC_CHIP:
        $que = que_get(QUE_RESEARCH, $user['id'], $planetrow['id'], true);
        $que_item = &$que['ques'][QUE_RESEARCH][$user['id']][0][0];

        if(isset($que_item) && $que_item['que_time_left'] > 0)
        {
          $unit_level--;
          $old_time = $que_item['que_time_left'];
          $que_item['que_time_left'] = $que_item['que_time_left'] > PERIOD_HOUR ? ceil($que_item['que_time_left'] / 2) : 0;
          doquery("UPDATE {{que}} SET `que_time_left` = {$que_item['que_time_left']} WHERE `que_id` = {$que_item['que_id']} LIMIT 1;");
          $message = sprintf($lang['art_heurestic_chip_ok'], $lang['tech'][$que_item['que_unit_id']], $que_item['que_unit_level'], sys_time_human($old_time - $que_item['que_time_left']));
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_heurestic_chip_subj'], $lang['art_heurestic_chip_subj'], $message);
        }
        else
        {
          $message = $lang['art_heurestic_chip_no_research'];
        }
      break;

      case ART_NANO_BUILDER:
        $planetrow = db_planet_by_id($planetrow['id'], true);
        $que = que_get(QUE_STRUCTURES, $user['id'], $planetrow['id'], true);
        $que_item = &$que['ques'][QUE_STRUCTURES][$user['id']][$planetrow['id']][0];
        //pdump($que_item);

        if(isset($que_item) && $que_item['que_time_left'] > 0)
        {
          $unit_level--;
          $old_time = $que_item['que_time_left'];
          $que_item['que_time_left'] = $que_item['que_time_left'] > PERIOD_HOUR ? ceil($que_item['que_time_left'] / 2) : 0;
          doquery("UPDATE {{que}} SET `que_time_left` = {$que_item['que_time_left']} WHERE `que_id` = {$que_item['que_id']} LIMIT 1;");
          $message = sprintf($lang['art_nano_builder_ok'], $que_item['que_unit_mode'] == BUILD_CREATE ? $lang['art_nano_builder_build'] : $lang['art_nano_builder_destroy'],
            $lang['tech'][$que_item['que_unit_id']], $que_item['que_unit_level'], $planetrow['name'], uni_render_coordinates($planetrow), sys_time_human($old_time - $que_item['que_time_left'])
          );
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_nano_builder_subj'], $lang['art_nano_builder_subj'], $message);
        }
        else
        {
          $message = $lang['art_nano_builder_no_que'];
        }
      break;

    }
    if($unit_level != $artifact_level_old)
    {
      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, $unit_level - $artifact_level_old, $user);
      sn_db_changeset_apply($db_changeset);
    }
  }
  else
  {
    $message = $lang['art_err_no_artifact'];
  }

  sn_db_transaction_commit();
  message($message, "{$lang['tech'][UNIT_ARTIFACTS]} - {$lang['tech'][$unit_id]}", 
    ($request_uri = sys_get_param_str_raw('REQUEST_URI')) ? $request_uri : ('artifacts.' . PHP_EX . '#' . $unit_id),
  5);
}
