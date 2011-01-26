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

  global $lang;

  $destination_planet = $mission_data['dst_planet'];

  $MoonSize      = $destination_planet['diameter'];
  $MoonName      = $destination_planet['name'];

  $RipsKilled = 0;
  $MoonDestroyed = 0;

  foreach($result['rw'][count($result['rw'])-1]['attackers'] as $fleet)
  {
    foreach($fleet['detail'] as $shipID => $shipNum)
    {
      $Rips += ($shipID == 214) ? $shipNum : 0;
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

  $fleet_row = $mission_data['fleet'];

  if ($MoonDestroyed == 1)
  {
     doquery("DELETE FROM {{planets}} WHERE `id` ='{$destination_planet['id']}';");

     $message  = $lang['sys_moon_destroyed'];
  }
  elseif($RipsKilled == 1)
  {
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! А нужно удалять все флоты !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row["fleet_id"]}';");
     $message  = $lang['sys_rips_destroyed'];
  }
  else
  {
     $message  = $lang['sys_rips_come_back'];
  }

  $message .= "<br><br>";
  $message .= $lang['sys_chance_moon_destroy'].intval($MoonDestChance)."%. <br>".$lang['sys_chance_rips_destroy'].intval($RipDestChance)."%";

  SendSimpleMessage ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );
  SendSimpleMessage ( $destination_planet['id_owner'], '', $fleet_row['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );

  return $result;
}

?>
