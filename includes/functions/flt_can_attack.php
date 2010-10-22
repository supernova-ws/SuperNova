<?php

function flt_can_attack($target_planet, $target_mission)
{
  global $user, $config, $time_now, $sn_data;

  // Checking for no planet
  if(!$target_planet['id_owner'])
  {
    if($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE)
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_NO_TARGET;
    }
  }

  // Got planet. Checking if it is ours
  if($target_planet['id_owner'] == $user['id'])
  {
    if($target_mission == MT_TRANSPORT || $target_mission == MT_RELOCATE)
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_OWN;
    }
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if($target_mission == MT_RELOCATE || $target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE)
  {
    return ATTACK_WRONG_MISSION;
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if($target_mission == MT_HOLD)
  {
    if($target_planet[$sn_data[34]['name']])
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_NO_ALLY_DEPOSIT;
    }
  }

  if($target_mission == MT_RECYCLE)
  {
    if($target_planet['debris_metal'] + $target_planet['debris_crystal'] > 0)
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_NO_DEBRIS;
    }
  }

  $enemy = doquery("SELECT * FROM {{users}} WHERE `id` = '{$target_planet['id_owner']}';", '', true);
  // We cannot attack or send resource to users in VACANCY mode
  if ($enemy['urlaubs_modus'] && $target_mission != MT_RECYCLE)
  {
    return ATTACK_VACANCY;
  }

  // Multi IP protection. Here we need a procedure to check proxies
  if(sys_is_multiaccount($user, $enemy))
  {
    return ATTACK_SAME_IP;
  }

  $user_points = doquery("SELECT total_points FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$user['id']}';", '', true);
  $user_points = $user_points['total_points'];
  $enemy_points = doquery("SELECT total_points FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$enemy['id']}';", '', true);
  $enemy_points = $enemy_points['total_points'];

  // Is it transport? If yes - checking for buffing to prevent mega-alliance destroyer
  if($target_mission == MT_TRANSPORT)
  {
    if($user_points >= $enemy_points)
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_BUFFING;
    }
  }
  // Only aggresive missions passed to this point

  // Is it admin with planet protection?
  if ($target_planet['id_level'] > $user['authlevel'])
  {
    return ATTACK_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if ($enemy['onlinetime'] && $enemy['onlinetime'] < ($time_now - 60*60*24*7))
  {
    return ATTACK_ALLOWED;
  }

  if($enemy_points <= $config->game_noob_points)
  {
    return ATTACK_NOOB;
  }

  if($user_points > $enemy_points * $config->game_noob_factor)
  {
    return ATTACK_NOOB;
  }

/*
  {
    $protectiontime  = $config->noobprotectiontime;
    $protectionmulti = $config->noobprotectionmulti;

    if($enemy_points*)
      if( (
        (($enemy_points * $protectionmulti) < $user_points AND $enemy_points < ($protectiontime * 1000))
        OR
        (($user_points * $protectionmulti) < $enemy_points AND $user_points < ($protectiontime * 1000))
      ) ) message("<font color=\"lime\"><b>".$lang['fl_noob_mess_n']."</b></font>", $lang['fl_noob_title'], "fleet.{$phpEx}", 2);
  }

  if ($protectiontime < 1) {
    $protectiontime = 9999999999999999;
  }
*/
    return ATTACK_ALLOWED;
}

?>
