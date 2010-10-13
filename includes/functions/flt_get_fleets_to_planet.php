<?php
function flt_get_fleets_to_planet($planet)
{
  global $user, $sn_data;

  if(!$planet)
  {
    return $planet;
  }

  $sql_fleets = doquery(
    "SELECT * FROM {{fleets}}
      WHERE
        (fleet_start_galaxy = {$planet['galaxy']} AND fleet_start_system = {$planet['system']} AND fleet_start_planet = {$planet['planet']} AND fleet_start_type = {$planet['planet_type']} AND fleet_mess = 1)
        OR
        (fleet_end_galaxy =  AND fleet_end_system = {$planet['system']} AND fleet_end_planet = {$planet['planet']} AND fleet_end_type = {$planet['planet_type']} AND fleet_mess = 0)
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

    $fleet_list[$fleet_ownage]['fleets'][$fleet['fleet_id']] = $fleet;

    if($fleet['fleet_mess'] == 1 || ($fleet['fleet_mess'] == 0 && ($fleet['fleet_mission'] == MT_RELOCATE)))
    {
      // then this fleet would stay
      $fleet_ships = explode(';',$fleet['fleet_array']);
      foreach(explode(';',$fleet['fleet_array']) as $ship_data)
      {
        if($ship_data)
        {
          $ship_data = explode(',', $ship_data);
          $fleet_list[$fleet_ownage][$ship_data[0]] += $ship_data[1];
        }
      }
    }

    $fleet_list[$fleet_ownage]['count']++;
    $fleet_list[$fleet_ownage]['metal'] += $fleet['fleet_resource_metal'];
    $fleet_list[$fleet_ownage]['crystal'] += $fleet['fleet_resource_crystal'];
    $fleet_list[$fleet_ownage]['deuterium'] += $fleet['fleet_resource_deuterium'];
  }

  return $fleet_list;
}
?>