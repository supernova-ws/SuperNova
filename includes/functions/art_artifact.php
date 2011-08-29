<?php

function art_use(&$user, &$planetrow, $unit_id)
{
  global $sn_data, $lang;

  doquery("START TRANSACTION;");
  $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", '', true);

  $artifact_list = sys_unit_str2arr($user['player_artifact_list']);
  if(in_array($unit_id, $sn_data['groups']['artifacts']) && $artifact_list[$unit_id])
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
          msg_send_simple_message($user['id'], 0, 0, MSG_TYPE_ADMIN, $lang['art_lhc_from'], $lang['art_lhc_subj'], $message, $escaped = false);
        }
        else
        {
          $message = $lang['art_lhc_moon_exists'];
        }
        message($message, "{$lang['tech'][ART_ARTIFACTS]} - {$lang['tech'][$unit_id]}", 'artifacts.' . PHP_EX, 10);
      break;
    }
    $artifact_list = sys_unit_arr2str($artifact_list);
    doquery("UPDATE {{users}} SET `player_artifact_list` = '{$artifact_list}' WHERE `id` = '{$user['id']}' LIMIT 1;");
  }

  doquery("COMMIT;");
}

?>