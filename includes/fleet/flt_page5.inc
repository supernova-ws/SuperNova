<?php
/**
* flt_page5.inc
*
* @package fleet
* @version 1
*
* Revision History
* ================
*    1 - copyright (c) 2009-2011 by Gorlum for http://supernova.ws
*      [!] Write from scratch
*
**/

$planet_ids = sys_get_param('planets');

$template = gettemplate('fleet5', true);

if(!empty($planet_ids))
{ // begin processing parameters
  $query = '';
  foreach($planet_ids as $planet_id)
  {
    $planet_id = intval($planet_id);
    $query .= $planet_id ? "{$planet_id}," : '';
  }

  if($query)
  { // begin processing query
    foreach($sn_data['groups']['flt_transports'] as $transport_id)
    {
      $transports[$sn_data[$transport_id]['name']] = $sn_data[$transport_id]['capacity'];
      $name_to_id[$sn_data[$transport_id]['name']] = $transport_id;
    }
    arsort($transports);

    // Caching $sn_data names for quick access
    $deuterium_db_name = $sn_data[RES_DEUTERIUM]['name'];

    $planets = array();

    $query = substr($query, 0, -1);
    $query = "SELECT * FROM `{{planets}}` WHERE `id_owner` = '{$user['id']}' AND `destruyed` = 0 AND `id` IN ({$query});";
    $planets_db_list = doquery($query);
    while($planet_db_data = mysql_fetch_assoc($planets_db_list))
    { // begin planet loop
      $planet_id = $planet_db_data['id'];

      $ship_loadout = array();
      $fleet = array();

      $fleet_capacity = 0;
      foreach($transports as $ship_id => $ship_capacity)
      {
        if($planet_db_data[$ship_id])
        {
          $ship_loadout[$ship_id]['capacity'] = $planet_db_data[$ship_id] * $ship_capacity;
          $fleet_capacity += $planet_db_data[$ship_id] * $ship_capacity;
        }
      }

      $planet_resources = 0;
      foreach($sn_data['groups']['resources_loot'] as $resource_id)
      {
        $planet_resources += floor($planet_db_data[$sn_data[$resource_id]['name']]);
      }

      $will_take = min($planet_resources, $fleet_capacity);

      foreach($ship_loadout as $planet_ship_key => &$planet_ship)
      {
        $can_take = min($will_take, $planet_ship['capacity']);
        $planet_ship['capacity'] -= $can_take;
        $planet_ship['taken'] += $can_take;
        $fleet[$name_to_id[$planet_ship_key]] = ceil($planet_ship['taken'] / $transports[$planet_ship_key]);

        $will_take -= $can_take;
        if ($will_take <= 0)
        {
          break;
        }
      }

      if(!empty($fleet))
      {
        $distance      = GetTargetDistance ( $planetrow['galaxy'], $planet_db_data['galaxy'], $planetrow['system'], $planet_db_data['system'], $planetrow['planet'], $planet_db_data['planet'] );
        $fleet_speed   = min(GetFleetMaxSpeed ($fleet, 0, $user));
        $duration      = GetMissionDuration ( 10, $fleet_speed, $distance, $SpeedFactor);
        $consumption   = GetFleetConsumption ( $fleet, $SpeedFactor, $duration, $distance, $fleet_speed, $user);

        if(floor($planet_db_data[$deuterium_db_name]) >= $consumption)
        {
          $will_take = min($planet_resources, $fleet_capacity) - $consumption;

          foreach($sn_data['groups']['resources_loot'] as $resource_id)
          {
            $resource_db_name = $sn_data[$resource_id]['name'];

            $resource_amount = floor($planet_db_data[$resource_db_name]);

            $fleet[$resource_id] = min($will_take, $resource_amount);
            $will_take -= $resource_amount;

            if($will_take <= 0)
            {
              break;
            }
          }
          $result = flt_send_fleet($user, $planet_db_data, $planetrow, $fleet, MT_TRANSPORT);
        }
        else
        {
          $result = ATTACK_NO_FUEL;
        }
      }
      else
      {
        $result = ATTACK_NO_FLEET;
      }


      $template->assign_block_vars('results', array(
        'ID'         => $planet_id,
        'NAME'       => $planet_db_data['name'],
        'GALAXY'     => $planet_db_data['galaxy'],
        'SYSTEM'     => $planet_db_data['system'],
        'PLANET'     => $planet_db_data['planet'],
        'TYPE_PRINT' => $lang['sys_planet_type'][$planet_db_data['planet_type']],
        'MESSAGE' => $lang['fl_attack_error'][$result],
      ));
      if($fleet && $result == ATTACK_ALLOWED)
      {
        foreach($fleet as $unit_id => $amount)
        {
          $template->assign_block_vars('results.units', array(
            'ID'     => $unit_id,
            'NAME'   => $lang['tech'][$unit_id],
            'AMOUNT' => $amount
          ));
        }
      }
    } // end planet loop
  }
} // end processing parameters

// Building list of own planets & moons
$colonies = SortUserPlanets ($user, $planetrow, ', metal, crystal, deuterium');
if (mysql_num_rows($colonies) >= 1)
{
  while ($row = mysql_fetch_array($colonies))
  {
    $template->assign_block_vars('colonies', array(
      'ID'         => $row['id'],
      'NAME'       => $row['name'],
      'GALAXY'     => $row['galaxy'],
      'SYSTEM'     => $row['system'],
      'PLANET'     => $row['planet'],
      'TYPE'       => $row['planet_type'],
      'TYPE_PRINT' => $lang['sys_planet_type'][$row['planet_type']],
      'METAL'      => pretty_number($row['metal']),
      'CRYSTAL'    => pretty_number($row['crystal']),
      'DEUTERIUM'  => pretty_number($row['deuterium']),
    ));
  }
};

$template->assign_vars(array(
  'PAGE_HINT'       => $lang['fl_page5_hint'],
));

display(parsetemplate($template, $parse), $lang['fl_title']);

?>
