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

function flt_build_gathering($resources_taken)
{
  $transports = $GLOBALS['transports'];
  $sn_data = $GLOBALS['sn_data'];
  $user = $GLOBALS['user'];
  $name_to_id = $GLOBALS['name_to_id'];
  $planetrow = $GLOBALS['planetrow'];
  $SpeedFactor = $GLOBALS['SpeedFactor'];
  $lang_sys_planet_type = $GLOBALS['lang']['sys_planet_type'];
  $lang_fl_attack_error = $GLOBALS['lang']['fl_attack_error'];
  $time_now = $GLOBALS['time_now'];

  $planet_list = array();

  $query = '';
  foreach($resources_taken as $planet_id => $cork)
  {
    $planet_id = intval($planet_id);
    $query .= $planet_id ? "{$planet_id}," : '';
  }

  if($query)
  { // begin processing query
    // Caching $sn_data names for quick access
    $deuterium_db_name = $sn_data[RES_DEUTERIUM]['name'];

    $query = substr($query, 0, -1);
    $query = "SELECT * FROM `{{planets}}` WHERE `id_owner` = '{$user['id']}' AND `destruyed` = 0 AND `id` IN ({$query});";
    $planets_db_list = doquery($query);
    while($planet_db_data = mysql_fetch_assoc($planets_db_list))
    { // begin planet loop
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

      $ship_loadout = array();
      $fleet = array();

      $fleet_capacity = 0;
      foreach($transports as $ship_id => $ship_capacity)
      {
        if($planet_db_data[$ship_id])
        {
          $ship_loadout[$ship_id]['capacity'] = $planet_db_data[$ship_id] * $ship_capacity;
          $ship_loadout[$ship_id]['taken'] = 0;
          $fleet_capacity += $planet_db_data[$ship_id] * $ship_capacity;
        }
      }

      $planet_resources = 0;
      foreach($sn_data['groups']['resources_loot'] as $resource_id)
      {
        if($resources_taken[$planet_id] == 1 || $resources_taken[$planet_id][$resource_id])
        {
          $planet_resources += floor($planet_db_data[$sn_data[$resource_id]['name']]);
        }
      }

      $planet_data['RESOURCES'] = $planet_resources;
      $planet_data['RESOURCES_TEXT'] = pretty_number($planet_resources);
      $planet_data['FLEET_CAPACITY'] = $fleet_capacity;
      $planet_data['FLEET_CAPACITY_TEXT'] = pretty_number($fleet_capacity, true, -$planet_resources);

      $will_take = min($planet_resources, $fleet_capacity);

      foreach($ship_loadout as $planet_ship_key => &$planet_ship)
      {
        $can_take = min($will_take, $planet_ship['capacity']);
        if($can_take <= 0)
        {
          continue;
        }
        $planet_ship['capacity'] -= $can_take;
        $planet_ship['taken'] += $can_take;
        $fleet[$name_to_id[$planet_ship_key]] = ceil($planet_ship['taken'] / $transports[$planet_ship_key]);

        $will_take -= $can_take;
        if ($will_take <= 0)
        {
          break;
        }
      }

      $planet_data['DISTANCE']    = GetTargetDistance ( $planetrow['galaxy'], $planet_db_data['galaxy'], $planetrow['system'], $planet_db_data['system'], $planetrow['planet'], $planet_db_data['planet'] );
      $planet_data['FLEET_SPEED'] = flt_fleet_speed($user, $fleet);
      $planet_data['DURATION']    = GetMissionDuration ( 10, $planet_data['FLEET_SPEED'], $planet_data['DISTANCE'], $SpeedFactor);
      if(!empty($fleet))
      {
        $planet_data['CONSUMPTION'] = GetFleetConsumption ( $fleet, $SpeedFactor, $planet_data['DURATION'], $planet_data['DISTANCE'], $planet_data['FLEET_SPEED'], $user);

        if(floor($planet_db_data[$deuterium_db_name]) >= $planet_data['CONSUMPTION'])
        {
          $will_take = min($planet_resources, $fleet_capacity) - $planet_data['CONSUMPTION'];

          foreach($sn_data['groups']['resources_loot'] as $resource_id)
          {
            if($resources_taken[$planet_id] != 1 && !$resources_taken[$planet_id][$resource_id])
            {
              continue;
            }

            $resource_db_name = $sn_data[$resource_id]['name'];

            $resource_amount = floor($planet_db_data[$resource_db_name]);

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
  }

  return $planet_list;
}

$template = gettemplate('fleet5', true);

$resources_taken = sys_get_param('resources');

foreach($sn_data['groups']['flt_transports'] as $transport_id)
{
  $transports[$sn_data[$transport_id]['name']] = $sn_data[$transport_id]['capacity'];
  $name_to_id[$sn_data[$transport_id]['name']] = $transport_id;
}
arsort($transports);

if(!empty($resources_taken))
{ // begin processing parameters

//pdump($_POST);
  $planet_list = flt_build_gathering($resources_taken);
//pdump($planet_list);
//die();

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

$resources_taken = array();
// Building list of own planets & moons
$colonies = SortUserPlanets ($user, $planetrow, ', metal, crystal, deuterium');
if (mysql_num_rows($colonies) >= 1)
{
  while ($row = mysql_fetch_assoc($colonies))
  {
    $planet_list[] = array(
      'ID'             => $row['id'],
      'NAME'           => $row['name'],
      'GALAXY'         => $row['galaxy'],
      'SYSTEM'         => $row['system'],
      'PLANET'         => $row['planet'],
      'TYPE'           => $row['planet_type'],
      'TYPE_PRINT'     => $lang['sys_planet_type'][$row['planet_type']],
      'METAL'          => $row['metal'],
      'CRYSTAL'        => $row['crystal'],
      'DEUTERIUM'      => $row['deuterium'],
      'METAL_TEXT'     => pretty_number($row['metal']),
      'CRYSTAL_TEXT'   => pretty_number($row['crystal']),
      'DEUTERIUM_TEXT' => pretty_number($row['deuterium']),
    );
    $resources_taken[$row['id']] = 1;
  };

  $planet_list2 = flt_build_gathering($resources_taken);
  foreach($planet_list2 as $planet_data)
  {
    $planet_data['DURATION'] = $planet_data['DURATION'] ? pretty_time($planet_data['DURATION']) : $lang['flt_no_transports'];
    $template->assign_block_vars('colonies', $planet_data);
  }
};

$template->assign_vars(array(
  'PAGE_HINT'      => $lang['fl_page5_hint'],
  'METAL_NEED'     => pretty_number(max(0, -sys_get_param_int('metal'))),
  'CRYSTAL_NEED'   => pretty_number(max(0, -sys_get_param_int('crystal'))),
  'DEUTERIUM_NEED' => pretty_number(max(0, -sys_get_param_int('deuterium'))),
));

display(parsetemplate($template, $parse), $lang['fl_title']);

?>
