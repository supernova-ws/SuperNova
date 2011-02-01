<?php
function flt_get_fleets_to_planet($planet, $fleet_db_list = 0)
{
  global $user, $sn_data;
  $sn_groups = &$sn_data['groups'];

  if(!$planet && !$fleet_db_list)
  {
    return $planet;
  }

  if($fleet_db_list === 0)
  {
    $sql_fleets = doquery(
      "SELECT * FROM {{fleets}} WHERE
        (fleet_start_galaxy = {$planet['galaxy']} AND fleet_start_system = {$planet['system']} AND fleet_start_planet = {$planet['planet']} AND fleet_start_type = {$planet['planet_type']} AND fleet_mess = 1)
        OR
        (fleet_end_galaxy = {$planet['galaxy']} AND fleet_end_system = {$planet['system']} AND fleet_end_planet = {$planet['planet']} AND fleet_end_type = {$planet['planet_type']} AND fleet_mess = 0);"
    );
    $fleet_db_list = array();
    while ($fleet = mysql_fetch_assoc($sql_fleets))
    {
      $fleet_db_list[] = $fleet;
    }
  }

//  while ($fleet = mysql_fetch_assoc($sql_fleets))
  foreach($fleet_db_list as $fleet)
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

    if($fleet['fleet_mess'] == 1 || ($fleet['fleet_mess'] == 0 && $fleet['fleet_mission'] == MT_RELOCATE) || ($fleet['fleet_target_owner'] != $user['id']))
    {
      $fleet_sn = flt_expand($fleet);
      foreach($fleet_sn as $ship_id => $ship_amount)
      {
        if(in_array($ship_id, $sn_groups['fleet']))
        {
          $fleet_list[$fleet_ownage]['total'][$ship_id] += $ship_amount;
        }
      }
    }

    $fleet_list[$fleet_ownage]['count']++;
    $fleet_list[$fleet_ownage]['amount'] += $fleet['fleet_amount'];
    $fleet_list[$fleet_ownage]['total'][RES_METAL] += $fleet['fleet_resource_metal'];
    $fleet_list[$fleet_ownage]['total'][RES_CRYSTAL] += $fleet['fleet_resource_crystal'];
    $fleet_list[$fleet_ownage]['total'][RES_DEUTERIUM] += $fleet['fleet_resource_deuterium'];
  }

  return $fleet_list;
}

?>
