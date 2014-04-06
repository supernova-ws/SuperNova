<?php

/**
 * MissionCaseRecycling.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_recycle(&$mission_data)
{
  $fleet_row          = &$mission_data['fleet'];
  $destination_planet = &$mission_data['dst_planet'];

  if(!$fleet_row)
  {
    return CACHE_NOTHING;
  }

  if(!isset($destination_planet['id']))
  {
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    flt_send_back($mission_data['fleet']);
    return CACHE_FLEET;
  }

  global $lang;

  $RecyclerCapacity    = 0;
  $OtherFleetCapacity  = 0;
  $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);
  foreach($fleet_array as $unit_id => $unit_count)
  {
    if(in_array($unit_id, sn_get_groups('fleet')))
    {
      $capacity = get_unit_param($unit_id, P_CAPACITY) * $unit_count;
      if(in_array($unit_id, sn_get_groups('flt_recyclers')))
      {
        $RecyclerCapacity += $capacity;
      }
      else
      {
        $OtherFleetCapacity += $capacity;
      }
    }
  }

  $IncomingFleetGoods = $fleet_row["fleet_resource_metal"] + $fleet_row["fleet_resource_crystal"] + $fleet_row["fleet_resource_deuterium"];
  if($IncomingFleetGoods > $OtherFleetCapacity)
  {
    $RecyclerCapacity -= ($IncomingFleetGoods - $OtherFleetCapacity);
  }

  if(($destination_planet["debris_metal"] + $destination_planet["debris_crystal"]) <= $RecyclerCapacity)
  {
    $RecycledGoods["metal"]   = $destination_planet["debris_metal"];
    $RecycledGoods["crystal"] = $destination_planet["debris_crystal"];
  }
  else
  {
    if (($destination_planet["debris_metal"]   > $RecyclerCapacity / 2) AND
      ($destination_planet["debris_crystal"] > $RecyclerCapacity / 2))
      {
      $RecycledGoods["metal"]   = $RecyclerCapacity / 2;
      $RecycledGoods["crystal"] = $RecyclerCapacity / 2;
    }
    else
    {
      if ($destination_planet["debris_metal"] > $destination_planet["debris_crystal"])
      {
        $RecycledGoods["crystal"] = $destination_planet["debris_crystal"];
        if ($destination_planet["debris_metal"] > ($RecyclerCapacity - $RecycledGoods["crystal"]))
        {
          $RecycledGoods["metal"] = $RecyclerCapacity - $RecycledGoods["crystal"];
        }
        else
        {
          $RecycledGoods["metal"] = $destination_planet["debris_metal"];
        }
      }
      else
      {
        $RecycledGoods["metal"] = $destination_planet["debris_metal"];
        if ($destination_planet["debris_crystal"] > ($RecyclerCapacity - $RecycledGoods["metal"]))
        {
          $RecycledGoods["crystal"] = $RecyclerCapacity - $RecycledGoods["metal"];
        }
        else
        {
          $RecycledGoods["crystal"] = $destination_planet["debris_crystal"];
        }
      }
    }
  }
  $NewCargo['Metal']     = $fleet_row["fleet_resource_metal"]   + $RecycledGoods["metal"];
  $NewCargo['Crystal']   = $fleet_row["fleet_resource_crystal"] + $RecycledGoods["crystal"];
  $NewCargo['Deuterium'] = $fleet_row["fleet_resource_deuterium"];

  $QryUpdateGalaxy  = "UPDATE `{{planets}}` SET `debris_metal` = `debris_metal` - '{$RecycledGoods['metal']}', `debris_crystal` = `debris_crystal` - '{$RecycledGoods['crystal']}' ";
  $QryUpdateGalaxy .= "WHERE `planet_type` = 1 AND `galaxy` = '{$fleet_row['fleet_end_galaxy']}' AND `system` = '{$fleet_row['fleet_end_system']}' AND `planet` = '{$fleet_row['fleet_end_planet']}' LIMIT 1;";
  doquery( $QryUpdateGalaxy);

  $Message = sprintf($lang['sys_recy_gotten'], pretty_number($RecycledGoods["metal"]), $lang['Metal'], pretty_number($RecycledGoods["crystal"]), $lang['Crystal']);
  msg_send_simple_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_RECYCLE, $lang['sys_mess_spy_control'], $lang['sys_recy_report'], $Message);

  $QryUpdateFleet  = "UPDATE {{fleets}} SET `fleet_mess` = 1,`fleet_resource_metal` = '{$NewCargo['Metal']}',`fleet_resource_crystal` = '{$NewCargo['Crystal']}',`fleet_resource_deuterium` = '{$NewCargo['Deuterium']}' ";
  $QryUpdateFleet .= "WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;";
  doquery( $QryUpdateFleet);

  return CACHE_FLEET | CACHE_PLANET_DST;
}
