<?php

function art_use(&$user, &$planetrow, $unit_id)
{
  global $sn_data, $lang;

  if(!in_array($unit_id, $sn_data['groups']['artifacts']))
  {
    return;
  }

  doquery("START TRANSACTION;");
  $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", '', true);

  $artifact_list = sys_unit_str2arr($user['player_artifact_list']);
  if($artifact_list[$unit_id])
  {
    switch($unit_id)
    {
      case ART_LHC:
        $has_moon = doquery("SELECT `id` FROM `{{planets}}` WHERE parent_planet = {$planetrow['id']} LIMIT 1;", '', true);
        if($planetrow['planet_type'] == PT_PLANET && !$has_moon['id'])
        {
          $artifact_list[$unit_id]--;
          $moon_chance = BE_calculateMoonChance($planetrow['debris_metal'] + $planetrow['debris_crystal']);
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
        $planetrow = doquery("SELECT * FROM {{planets}} WHERE `id` = {$planetrow['id']} LIMIT 1 FOR UPDATE;", '', true);
        if($planetrow['planet_type'] == PT_PLANET)
        {
          $artifact_list[$unit_id]--;
          $artifact_deploy = $sn_data[$unit_id]['deploy'];

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
          }
          else
          {
            doquery("UPDATE {{planets}} SET `field_current` = `field_current` + {$sectors_used}{$deployment_str} WHERE `id` = {$planetrow['id']} LIMIT 1;");
            $message = sprintf($lang['art_rcd_ok'], $lang['tech'][$unit_id], $planetrow['name'], uni_render_coordinates($planetrow));
            msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_QUE, $lang['art_rcd_subj'], $lang['art_rcd_subj'], $message);
          }
        }
        else
        {
          $message = $lang['art_rcd_err_moon'];
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

  doquery("COMMIT;");
  message($message, "{$lang['tech'][ART_ARTIFACTS]} - {$lang['tech'][$unit_id]}", 'artifacts.' . PHP_EX, 10);
}

?>
