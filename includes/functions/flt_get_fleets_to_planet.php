<?php
function flt_get_fleets_to_planet($planet)
{
  global $user;

  if(!$planet)
  {
    return $planet;
  }

  $sql_fleets = doquery(
    "SELECT * FROM {{fleets}}
      WHERE
        (fleet_start_galaxy = {$planet['galaxy']} AND fleet_start_system = {$planet['system']} AND fleet_start_planet = {$planet['planet']} AND fleet_start_type = {$planet['planet_type']} AND fleet_mess = 1)
        OR
        (fleet_end_galaxy = {$planet['galaxy']} AND fleet_end_system = {$planet['system']} AND fleet_end_planet = {$planet['planet']} AND fleet_end_type = {$planet['planet_type']} AND fleet_mess = 0)
    ");
  $fleet_list['total'] = mysql_num_rows($sql_fleets);

  while ($fleet = mysql_fetch_assoc($sql_fleets))
  {
    if($fleet['fleet_owner'] == $user['id'])
    {
      $fleet_ownage = 'own';
    }
    else
    {
      switch($fleet['fleet_mission'])
      {
        case MT_ATTACK:
        case MT_AKS:
        case MT_DESTROY:
        case MT_MISSILE:
          $fleet_ownage = 'enemy';
        break;

        default:
          $fleet_ownage = 'neutral';
        break;

      }
    }

    $fleet_list[$fleet_ownage][] = $fleet;
    $fleet_list["{$fleet_ownage}_count"]++;
    $fleet_list["{$fleet_ownage}_metal"] += $fleet['fleet_resource_metal'];
    $fleet_list["{$fleet_ownage}_crystal"] += $fleet['fleet_resource_crystal'];
    $fleet_list["{$fleet_ownage}_deuterium"] += $fleet['fleet_resource_deuterium'];
  }

  return $fleet_list;
}
?>