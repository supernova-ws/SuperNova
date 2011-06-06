<?php
/*
 * Partial copyright (c) 2009-2011 by Gorlum for http://supernova.ws

Based on original code:
#############################################################################
#  Filename: MissionCaseDestruction.php
#  Create date: Saturday, April 05, 2008    15:51:35
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
function flt_mission_destroy($mission_data)
{
  $result = flt_mission_attack($mission_data);

  if (empty($result) || $result['won'] != 1)
  {
    return $result;
  }

  $fleet_row          = $mission_data['fleet'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$destination_planet || !is_array($destination_planet))
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return;
  }

  global $lang;

  $MoonSize      = $destination_planet['diameter'];
  $MoonName      = $destination_planet['name'];

  $RipsKilled = 0;
  $MoonDestroyed = 0;

  foreach($result['rw'][count($result['rw'])-1]['attackers'] as $fleet)
  {
    foreach($fleet['detail'] as $shipID => $shipNum)
    {
      $Rips += ($shipID == SHIP_DEATH_STAR) ? $shipNum : 0;
    }
  }

  if($Rips>0)
  {
    $MoonDestChance = min(99, round((100 - sqrt($MoonSize)) * sqrt($Rips)));
    $RipDestChance = round(sqrt($MoonSize) / 2);
    $UserChance = mt_rand(1, 100);
    if (($UserChance > 0) AND ($UserChance <= $MoonDestChance))
    {
      $RipsKilled = 0;
      $MoonDestroyed = 1;
    }
    elseif (($UserChance > 0) AND ($UserChance <= $RipDestChance))
    {
      $RipsKilled = 1;
      $MoonDestroyed = 0;
    }
  }


  if ($MoonDestroyed == 1)
  {
    doquery("DELETE FROM {{planets}} WHERE `id` ='{$destination_planet['id']}';");

    $message  = $lang['sys_moon_destroyed'];
  }
  elseif($RipsKilled == 1)
  {
//TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! А нужно удалять все флоты !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row["fleet_id"]}';");
    $message  = $lang['sys_rips_destroyed'];
  }
  else
  {
    $message  = $lang['sys_rips_come_back'];
  }

  $message .= "<br><br>";
  $message .= $lang['sys_chance_moon_destroy'].intval($MoonDestChance)."%. <br>".$lang['sys_chance_rips_destroy'].intval($RipDestChance)."%";

  msg_send_simple_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );
  msg_send_simple_message ( $destination_planet['id_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );

  return $result;
}

?>
