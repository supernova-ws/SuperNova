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

if(!empty($planet_ids))
{ // begin processing parameters
  $query = '';
  foreach($planet_ids as $planet_id)
  {
    $planet_id = intval($planet_id);
    $query .= $planet_id ? "{$planet_id}," : '';
  }

  if($query)
  {
    foreach($sn_data['groups']['flt_transports'] as $transport_id)
    {
      $transports[$sn_data[$transport_id]['name']] = $sn_data[$transport_id]['capacity'];
      $name_to_id[$sn_data[$transport_id]['name']] = $transport_id;
    }
    arsort($transports);//pdump($transports);

    $query = "SELECT * FROM `{{planets}}` WHERE `id_owner` = '{$user['id']}' AND `destruyed` = 0 AND `id` IN ({$query}-1);";
    $planets_db_list = doquery($query);
    while($planet_db_data = mysql_fetch_assoc($planets_db_list))
    { // begin planet loop
      $planet_id = $planet_db_data['id'];
      $planet_data = &$planets[$planet_id];

      $planet_data['ships'] = array();
      $planet_ship_list = &$planet_data['ships'];
      //$planet_ship_list = &$planets[$planet_id];

      foreach($transports as $ship_id => $ship_capacity)
      {
        $planet_ship_list[$ship_id]['capacity'] = $planet_db_data[$ship_id] * $ship_capacity;
        //$planet_ship_list[$ship_id]['id'] = ;
      }pdump($planet_data, "Ships on planet {$planet_id}");print('<hr>');

      reset($planet_ship_list);
      $no_more_ships = false;

      foreach($sn_data['groups']['resources_loot'] as $resource_id)
      { // begin planet resource loop
        $resource_db_name = $sn_data[$resource_id]['name'];

        $resource_amount = floor($planet_db_data[$resource_db_name]);

        while($resource_amount > 0 && !$no_more_ships)
        { // begin ship fill loop
          $planet_ship = current($planet_ship_list);
          while($planet_ship['capacity'] <= 0 && !$no_more_ships)
          { // begin search transport with free cargo
            if(next($planet_ship_list) === false)
            {
              $no_more_ships = true;
              break;
            }
            else
            {
              $planet_ship = current($planet_ship_list);
            }
          } // begin search transport with free cargo

          if($no_more_ships)
          {
          	break;
          }

          $planet_ship_key = key($planet_ship_list);
//pdump($resource_amount, $resource_db_name . ' amount');
//pdump($planet_ship_list[$planet_ship_key]['capacity'], $planet_ship_key . ' capacity');

          $can_take = min($resource_amount, $planet_ship['capacity']);
//pdump($can_take, $planet_ship_key . ' will take');

          $resource_amount -= $can_take;

          $planet_ship_list[$planet_ship_key]['capacity'] -= $can_take;
          $planet_ship_list[$planet_ship_key]['taken'] += $can_take;

          $planet_data['resources'][$resource_db_name] += $can_take;

          $planet_data['resources_taken_total'] += $can_take;

          $planet_data['fleet'][$name_to_id[$planet_ship_key]] = ceil($planet_ship_list[$planet_ship_key]['taken'] / $transports[$planet_ship_key]);

//pdump($resource_amount, $resource_db_name . ' amount LEFT');
//pdump($planet_ship_list[key($planet_ship_list)]['capacity'], key($planet_ship_list) . ' capacity LEFT');
//print('<hr>');
        } // end ship fill loop

// Высчитывать объем топлива и вычитать из общего количества ресурсов!
// Менять количество кораблей после этого обратным счетом

        $planet_data['resources_left'][$resource_db_name] = $resource_amount;

      } // end resource loop
    } // end planet loop
    pdump($planets);
  }
} // end processing parameters

$template = gettemplate('fleet5', true);
// Building list of own planets & moons
$colonies = SortUserPlanets ($user, $planetrow);
if (mysql_num_rows($colonies) > 1)
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
    ));
  }
};

$template->assign_vars(array(
  'acss1'           => $aks_fleets_mr, // just data to print

  'usedfleet'       => str_rot13(base64_encode(serialize($fleet['fleetarray']))),

  'speed_factor'     => $SpeedFactor,
  "t{$planet_type}" => 'SELECTED',

  'target_mission'  => $target_mission,

  'ships'           => str_rot13(base64_encode(serialize($ships))),

//  'fleet_speed'     => round(min(GetFleetMaxSpeed($fleet['fleetarray'], false, $user))),
//  'fleet_capacity'  => $fleet['capacity'],

  'PAGE_HINT'       => $lang['fl_page1_hint'],
));

display(parsetemplate($template, $parse), $lang['fl_title']);

?>
