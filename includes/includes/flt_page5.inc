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

if(SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

function flt_build_gathering($resources_taken = '')
{
  sn_db_transaction_start();

  global $time_now, $planetrow, $user, $lang;
  // Caching sn_data names for quick access
  $lang_sys_planet_type = $lang['sys_planet_type'];
  $lang_fl_attack_error = $lang['fl_attack_error'];

  $planet_list = array();

  if(is_array($resources_taken))
  {
    $query = implode(',', array_keys($resources_taken));
    $query = " AND `destruyed` = 0 AND `id` IN ({$query})";
  }
  else
  {
    $query = '';
  }

  foreach(sn_get_groups('flt_transports') as $transport_id)
  {
    /*
    $transport_info = get_unit_param($transport_id);
    $transports[$transport_info[P_NAME]] = $transport_info[P_CAPACITY];
    $name_to_id[$transport_info[P_NAME]] = $transport_id;
    */
    $transports[$transport_id] = get_unit_param($transport_id, P_CAPACITY);
    // $name_to_id[$transport_info[P_NAME]] = $transport_id;
  }
  arsort($transports);

  $planets_db_list = db_planet_list_sorted($user, $planetrow['id'], '*', $query);
  // while($planet_db_data = mysql_fetch_assoc($planets_db_list))
  foreach($planets_db_list as $planet_db_data)
  { // begin planet loop
    if(!$query)
    {
      $resources_taken[$planet_db_data['id']] = 1;
    }
    $planet_db_data = sys_o_get_updated($user, $planet_db_data, $time_now, true);
    $planet_db_data = $planet_db_data['planet'];

    $planet_id = $planet_db_data['id'];

    $planet_list[$planet_id] = array(
      'ID'             => $planet_id,
      'NAME'           => $planet_db_data['name'],
      'GALAXY'         => $planet_db_data['galaxy'],
      'SYSTEM'         => $planet_db_data['system'],
      'PLANET'         => $planet_db_data['planet'],
      'TYPE'           => $planet_db_data['planet_type'],
      'TYPE_PRINT'     => $lang_sys_planet_type[$planet_db_data['planet_type']],
      'METAL'          => floor($planet_db_data['metal']),
      'CRYSTAL'        => floor($planet_db_data['crystal']),
      'DEUTERIUM'      => floor($planet_db_data['deuterium']),
      'METAL_TEXT'     => pretty_number($planet_db_data['metal']),
      'CRYSTAL_TEXT'   => pretty_number($planet_db_data['crystal']),
      'DEUTERIUM_TEXT' => pretty_number($planet_db_data['deuterium']),
      'PLANET_DB_DATA' => $planet_db_data
    );

    $planet_data = &$planet_list[$planet_id];

    $planet_resources = 0;
    foreach(sn_get_groups('resources_loot') as $resource_id)
    {
      if($resources_taken[$planet_id] == 1 || $resources_taken[$planet_id][$resource_id])
      {
        $planet_resources += floor(mrc_get_level($user, $planet_db_data, $resource_id, true, true)); // $planet_db_data[get_unit_param($resource_id, P_NAME)]);
      }
    }
    $planet_data['RESOURCES'] = $planet_resources;
    $planet_data['RESOURCES_TEXT'] = pretty_number($planet_resources);

    $fleet_capacity = 0;
    $ship_loadout = array();
    $fleet = array();
    foreach($transports as $ship_id => $ship_capacity)
    {
      if($ship_count = mrc_get_level($user, $planet_db_data, $ship_id, true, true))
      {
        $ship_loadout[$ship_id]['capacity'] = $ship_count * $ship_capacity;
        $ship_loadout[$ship_id]['taken'] = 0;
        $fleet_capacity += $ship_loadout[$ship_id]['capacity'];
      }
    }
    $planet_data['FLEET_CAPACITY'] = $fleet_capacity;
    $planet_data['FLEET_CAPACITY_TEXT'] = pretty_number($fleet_capacity, true, -$planet_resources);

    $will_take = min($planet_resources, $fleet_capacity);

    foreach($ship_loadout as $ship_id => &$planet_ship)
    {
      $can_take = min($will_take, $planet_ship['capacity']);
      if($can_take <= 0)
      {
        continue;
      }
      $planet_ship['capacity'] -= $can_take;
      $planet_ship['taken'] += $can_take;
      $fleet[$ship_id] = ceil($planet_ship['taken'] / $transports[$ship_id]);

      $will_take -= $can_take;
      if ($will_take <= 0)
      {
        break;
      }
    }

    if(!empty($fleet))
    {
      $travel_data = flt_travel_data($user, $planetrow, $planet_db_data, $fleet, 10);
      $planet_data['FLEET_SPEED'] = $travel_data['fleet_speed'];
      $planet_data['DISTANCE']    = $travel_data['distance'];
      $planet_data['DURATION']    = $travel_data['duration'];
      $planet_data['CONSUMPTION'] = $travel_data['consumption'];

      if(floor(mrc_get_level($user, $planet_db_data, RES_DEUTERIUM, true)) >= $planet_data['CONSUMPTION'])
      {
        $will_take = min($planet_resources, $fleet_capacity) - $planet_data['CONSUMPTION'];

        foreach(sn_get_groups('resources_loot') as $resource_id)
        {
          if($resources_taken[$planet_id] != 1 && !$resources_taken[$planet_id][$resource_id])
          {
            continue;
          }

          $resource_amount = floor(mrc_get_level($user, $planet_db_data, $resource_id, true, true));

          $fleet[$resource_id] = min($will_take, $resource_amount);
          $will_take -= $resource_amount;

          if($will_take <= 0)
          {
            break;
          }
        }
        $result = ATTACK_ALLOWED;
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

    $planet_data['MESSAGE'] = $lang_fl_attack_error[$result];
    $planet_data['RESULT']  = $result;
    $planet_data['FLEET']   = $fleet;
  } // end planet loop

  sn_db_transaction_commit();

  return $planet_list;
}

$template = gettemplate('fleet5', true);

$resources_taken = sys_get_param('resources');

if(!empty($resources_taken))
{ // begin processing parameters
  $planet_list = flt_build_gathering($resources_taken);

  foreach($planet_list as $planet_id => $planet_data)
  {
    if($planet_data['RESULT'] == ATTACK_ALLOWED)
    {
      $planet_data['RESULT'] = flt_t_send_fleet($user, $planet_data['PLANET_DB_DATA'], $planetrow, $planet_data['FLEET'], MT_TRANSPORT);
    }

    $planet_data['MESSAGE'] = $lang['fl_attack_error'][$planet_data['RESULT']];

    $template->assign_block_vars('results', $planet_data);
    if(!empty($planet_data['FLEET']) && $planet_data['RESULT'] == ATTACK_ALLOWED)
    {
      foreach($planet_data['FLEET'] as $unit_id => $amount)
      {
        $template->assign_block_vars('results.units', array(
          'ID'     => $unit_id,
          'NAME'   => $lang['tech'][$unit_id],
          'AMOUNT' => $amount
        ));
      }
    }
  }
} // end processing parameters

// Building list of own planets & moons
$planet_list = flt_build_gathering();
foreach($planet_list as $planet_data)
{
  $planet_data['DURATION'] = $planet_data['DURATION'] ? pretty_time($planet_data['DURATION']) : $lang['flt_no_fuel'];
  $template->assign_block_vars('colonies', $planet_data);
}

$template->assign_vars(array(
  'PAGE_HINT'      => $lang['fl_page5_hint'],
  'METAL_NEED'     => pretty_number(max(0, -sys_get_param_float('metal'))),
  'CRYSTAL_NEED'   => pretty_number(max(0, -sys_get_param_float('crystal'))),
  'DEUTERIUM_NEED' => pretty_number(max(0, -sys_get_param_float('deuterium'))),
));

tpl_set_resource_info($template, $planetrow, array());

display($template, $lang['fl_title']);
