<?php

/**
 * flt_mission_transport.php
 *
 * @version 2.0 return cacher result
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_transport($fleet_row)
{
/*
  // flt_mission_transport
  $Message = sprintf($lang['sys_tran_mess_back'], $StartName, GetStartAdressLink($fleet_row, ''));
  SendSimpleMessage($StartOwner, '', $fleet_row['fleet_end_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_fleetback'], $Message);
*/

  global $lang;

  $QryStartPlanet   = "SELECT * FROM {{planets}} WHERE ";
  $QryStartPlanet  .= "`galaxy` = '{$fleet_row['fleet_start_galaxy']}' AND ";
  $QryStartPlanet  .= "`system` = '{$fleet_row['fleet_start_system']}' AND ";
  $QryStartPlanet  .= "`planet` = '{$fleet_row['fleet_start_planet']}' AND ";
  $QryStartPlanet  .= "`planet_type` = '{$fleet_row['fleet_start_type']}' LIMIT 1;";
  $StartPlanet      = doquery( $QryStartPlanet, '', true);
  $StartName        = $StartPlanet['name'];
  $StartOwner       = $StartPlanet['id_owner'];

  $QryTargetPlanet  = "SELECT * FROM {{planets}} WHERE ";
  $QryTargetPlanet .= "`galaxy` = '{$fleet_row['fleet_end_galaxy']}' AND ";
  $QryTargetPlanet .= "`system` = '{$fleet_row['fleet_end_system']}' AND ";
  $QryTargetPlanet .= "`planet` = '{$fleet_row['fleet_end_planet']}' AND ";
  $QryTargetPlanet .= "`planet_type` = '{$fleet_row['fleet_end_type']}' LIMIT 1;";
  $TargetPlanet     = doquery( $QryTargetPlanet, '', true);
  $TargetName       = $TargetPlanet['name'];
  $TargetOwner      = $TargetPlanet['id_owner'];

  $Message = sprintf( $lang['sys_tran_mess_owner'],
              $TargetName, GetTargetAdressLink($fleet_row, ''),
              $fleet_row['fleet_resource_metal'], $lang['Metal'],
              $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
              $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
  SendSimpleMessage ( $StartOwner, '', $fleet_row['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if ($TargetOwner <> $StartOwner)
  {
    $Message = sprintf( $lang['sys_tran_mess_user'],
                $StartName, GetStartAdressLink($fleet_row, ''),
                $TargetName, GetTargetAdressLink($fleet_row, ''),
                $fleet_row['fleet_resource_metal'], $lang['Metal'],
                $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
                $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
    SendSimpleMessage ( $TargetOwner, '', $fleet_row['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  return RestoreFleetToPlanet($fleet_row, false, true);
}

?>
