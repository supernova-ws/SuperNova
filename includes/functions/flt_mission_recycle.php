<?php

/**
 * MissionCaseRecycling.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_recycle($fleet_row)
{
  global $pricelist, $lang;

  if ($fleet_row['fleet_mess'] != 0)
  {
    if ($fleet_row['fleet_end_time'] <= $time_now)
    {
      $Message         = sprintf( $lang['sys_tran_mess_owner'],
            $StartName, GetStartAdressLink($fleet_row, ''),
            pretty_number($fleet_row['fleet_resource_metal']), $lang['Metal'],
            pretty_number($fleet_row['fleet_resource_crystal']), $lang['Crystal'],
            pretty_number($fleet_row['fleet_resource_deuterium']), $lang['Deuterium'] );
      SendSimpleMessage ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_end_time'], 4, $lang['sys_mess_spy_control'], $lang['sys_mess_fleetback'], $Message);
      return RestoreFleetToPlanet($fleet_row, true);
    }
    return CACHE_NOTHING;
  }
  else
  {
    if ($fleet_row['fleet_start_time'] > $time_now)
    {
      return CACHE_NOTHING;
    }
  }

  $QrySelectGalaxy  = "SELECT * FROM `{{table}}` WHERE ";
  $QrySelectGalaxy .= "`galaxy` = '{$fleet_row['fleet_end_galaxy']}' AND ";
  $QrySelectGalaxy .= "`system` = '{$fleet_row['fleet_end_system']}' AND ";
  $QrySelectGalaxy .= "`planet` = '{$fleet_row['fleet_end_planet']}' AND ";
  $QrySelectGalaxy .= "`planet_type` = 1 ";
  $QrySelectGalaxy .= "LIMIT 1;";
  $TargetGalaxy     = doquery( $QrySelectGalaxy, 'planets', true);

  $FleetRecord         = explode(";", $fleet_row['fleet_array']);
  $RecyclerCapacity    = 0;
  $OtherFleetCapacity  = 0;
  foreach ($FleetRecord as $Item => $Group) {
    if ($Group != '') {
      $Class        = explode (",", $Group);
      if ($Class[0] == 209) {
        $RecyclerCapacity   += $pricelist[$Class[0]]["capacity"] * $Class[1];
      } else {
        $OtherFleetCapacity += $pricelist[$Class[0]]["capacity"] * $Class[1];
      }
    }
  }

  $IncomingFleetGoods = $fleet_row["fleet_resource_metal"] + $fleet_row["fleet_resource_crystal"] + $fleet_row["fleet_resource_deuterium"];
  if ($IncomingFleetGoods > $OtherFleetCapacity) {
    $RecyclerCapacity -= ($IncomingFleetGoods - $OtherFleetCapacity);
  }

  if (($TargetGalaxy["debris_metal"] + $TargetGalaxy["debris_crystal"]) <= $RecyclerCapacity) {
    $RecycledGoods["metal"]   = $TargetGalaxy["debris_metal"];
    $RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
  } else {
    if (($TargetGalaxy["debris_metal"]   > $RecyclerCapacity / 2) AND
      ($TargetGalaxy["debris_crystal"] > $RecyclerCapacity / 2)) {
      $RecycledGoods["metal"]   = $RecyclerCapacity / 2;
      $RecycledGoods["crystal"] = $RecyclerCapacity / 2;
    } else {
      if ($TargetGalaxy["debris_metal"] > $TargetGalaxy["debris_crystal"]) {
        $RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
        if ($TargetGalaxy["debris_metal"] > ($RecyclerCapacity - $RecycledGoods["crystal"])) {
          $RecycledGoods["metal"] = $RecyclerCapacity - $RecycledGoods["crystal"];
        } else {
          $RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
        }
      } else {
        $RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
        if ($TargetGalaxy["debris_crystal"] > ($RecyclerCapacity - $RecycledGoods["metal"])) {
          $RecycledGoods["crystal"] = $RecyclerCapacity - $RecycledGoods["metal"];
        } else {
          $RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
        }
      }
    }
  }
  $NewCargo['Metal']     = $fleet_row["fleet_resource_metal"]   + $RecycledGoods["metal"];
  $NewCargo['Crystal']   = $fleet_row["fleet_resource_crystal"] + $RecycledGoods["crystal"];
  $NewCargo['Deuterium'] = $fleet_row["fleet_resource_deuterium"];

  $QryUpdateGalaxy  = "UPDATE `{{table}}` SET ";
  $QryUpdateGalaxy .= "`debris_metal` = `debris_metal` - '".$RecycledGoods["metal"]."', ";
  $QryUpdateGalaxy .= "`debris_crystal` = `debris_crystal` - '".$RecycledGoods["crystal"]."' ";
  $QryUpdateGalaxy .= "WHERE ";
  $QryUpdateGalaxy .= "`galaxy` = '".$fleet_row['fleet_end_galaxy']."' AND ";
  $QryUpdateGalaxy .= "`system` = '".$fleet_row['fleet_end_system']."' AND ";
  $QryUpdateGalaxy .= "`planet` = '".$fleet_row['fleet_end_planet']."' AND ";
  $QryUpdateGalaxy .= "`planet_type` = 1 ";
  $QryUpdateGalaxy .= "LIMIT 1;";
  doquery( $QryUpdateGalaxy, 'planets');

  $Message = sprintf($lang['sys_recy_gotten'], pretty_number($RecycledGoods["metal"]), $lang['Metal'], pretty_number($RecycledGoods["crystal"]), $lang['Crystal']);
  SendSimpleMessage ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], 4, $lang['sys_mess_spy_control'], $lang['sys_recy_report'], $Message);

  $QryUpdateFleet  = "UPDATE {{table}} SET ";
        $QryUpdateFleet .= "`fleet_resource_metal` = '".$NewCargo['Metal']."', ";
  $QryUpdateFleet .= "`fleet_resource_crystal` = '".$NewCargo['Crystal']."', ";
  $QryUpdateFleet .= "`fleet_resource_deuterium` = '".$NewCargo['Deuterium']."', ";
  $QryUpdateFleet .= "`fleet_mess` = '1' ";
        $QryUpdateFleet .= "WHERE ";
  $QryUpdateFleet .= "`fleet_id` = '{$fleet_row['fleet_id']}' ";
        $QryUpdateFleet .= "LIMIT 1;";
  doquery( $QryUpdateFleet, 'fleets');

  return CACHE_FLEET | CACHE_PLANET_DST;
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 Mise en module initiale

?>
