<?php

/**
 * eco_get_build_data.php
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 * @version 1.0
 */

function eco_get_build_data($user, $planet, $unit_id, $unit_level = 0, $only_cost = false)
{
  global $sn_data, $config;

  $sn_groups = $sn_data['groups'];

  $unit_data = $sn_data[$unit_id];
  $unit_db_name = $unit_data['name'];
  $unit_factor = $unit_data['cost']['factor'] ? $unit_data['cost']['factor'] : 1;

  $rpg_exchange_deuterium = $config->rpg_exchange_deuterium;

  $price_increase = pow($unit_factor, $unit_level);
  $can_build   = 1000000000000;
  $can_destroy = 1000000000000;
  foreach($unit_data['cost'] as $resource_id => $resource_amount)
  {
    if($resource_id === 'factor')
    {
      continue;
    }
    
    $resource_cost = $resource_amount * $price_increase;
    $res_to_build = $cost[BUILD_CREATE][$resource_id] = floor($resource_cost);
    $res_to_destroy = $cost[BUILD_DESTROY][$resource_id] = floor($resource_cost / 2);
    
    if($only_cost || !$resource_cost)
    {
      continue;
    }

    if(in_array($resource_id, $sn_groups['resources_loot']))
    {
      $can_build = min($can_build, $planet[$sn_data[$resource_id]['name']] / $resource_cost);
      $can_destroy = min($can_destroy, $planet[$sn_data[$resource_id]['name']] / $res_to_destroy);
      $time += $resource_cost * $config->__get("rpg_exchange_{$sn_data[$resource_id]['name']}")/ $rpg_exchange_deuterium;
    }
    elseif($resource_id == RES_DARK_MATTER)
    {
      $can_build = min($can_build, $user[$sn_data[$resource_id]['name']] / $resource_cost) ;
      $can_destroy = min($can_destroy, $user[$sn_data[$resource_id]['name']] / $res_to_destroy);
    }
    elseif($resource_id == RES_ENERGY)
    {
      $can_build = min($can_build, ($planet['energy_max'] - $planet['energy_used']) / $resource_cost);
      $can_destroy = min($can_destroy, ($planet['energy_max'] - $planet['energy_used']) / $res_to_destroy);
    }
  }
 
  if($only_cost)
  {
    return $cost;
  }
   
  $can_build = $can_build > 0 ? floor($can_build) : 0;
  $cost['CAN'][BUILD_CREATE]  = floor($can_build);

  $can_destroy = $can_destroy > 0 ? floor($can_destroy) : 0;
  $cost['CAN'][BUILD_DESTROY] = floor($can_destroy);

  $time = $time * 60 * 60 / get_game_speed() / 2500;

  $cost['RESULT'][BUILD_CREATE] = BUILD_ALLOWED;
  if(isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = isset($planet[$db_name]) ? $planet[$db_name] : (isset($user[$db_name]) ? $user[$db_name] : ($require_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0));

      if($data < $require_level)
      {
        $cost['RESULT'][BUILD_CREATE] = BUILD_REQUIRE_NOT_MEET;
        break;
      }
    }
  }
  $cost['RESULT'][BUILD_CREATE] = $cost['RESULT'][BUILD_CREATE] == BUILD_ALLOWED ? ($cost['CAN'][BUILD_CREATE] ? BUILD_ALLOWED : BUILD_NO_RESOURCES) : $cost['RESULT'][BUILD_CREATE];

  $mercenary = 0;
  $cost['RESULT'][BUILD_DESTROY] = BUILD_INDESTRUCTABLE;
  if(in_array($unit_id, $sn_groups['structures']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[STRUC_FACTORY_NANO]['name']]) / ($planet[$sn_data[STRUC_FACTORY_ROBOT]['name']] + 1);
    $mercenary = MRC_ENGINEER;
    $cost['RESULT'][BUILD_DESTROY] = $planet[$unit_db_name] ? ($cost['CAN'][BUILD_DESTROY] ? BUILD_ALLOWED : BUILD_NO_RESOURCES) : BUILD_NO_UNITS;
  }
  elseif(in_array($unit_id, $sn_groups['tech']))
  {
    $tech_intergalactic = $user[$sn_data[TECH_RESEARCH]['name']];
    if ( $tech_intergalactic < 1 )
    {
      $time = $time * pow(0.5, $planet[$sn_data[STRUC_LABORATORY_NANO]['name']]) / (($planet[$sn_data[STRUC_LABORATORY]['name']] + 1) * 2);
    }
    else
    {
      $lab_db_name = $sn_data[STRUC_LABORATORY]['name'];
      $lab_require = intval($unit_data['require'][STRUC_LABORATORY]);
      $tech_intergalactic = $tech_intergalactic + 1;
/*
      $inves = doquery("SELECT SUM(`{$lab_db_name}`) AS `laboratorio`
        FROM
        (
          SELECT `{$lab_db_name}`
            FROM `{{planets}}`
            WHERE `id_owner` = '{$user['id']}' AND `{$lab_db_name}` >= {$lab_require}
            ORDER BY `{$lab_db_name}` DESC
            LIMIT {$tech_intergalactic}
        ) AS subquery;", '', true);
//      $time = $time / (($inves['laboratorio'] + 1) * 2) * pow(0.5, $planet[$sn_data[STRUC_LABORATORY_NANO]['name']]);
*/
      // TODO: Fix bug with counting building labs/nanolabs
      $inves = doquery(
        "SELECT SUM(lab) AS effective_level
          FROM
          (
            SELECT ({$lab_db_name} + 1) * 2 / pow(0.5, {$sn_data[STRUC_LABORATORY_NANO]['name']}) AS lab
              FROM {{planets}}
                WHERE id_owner='{$user['id']}' AND {$lab_db_name} >= {$lab_require}
                ORDER BY lab DESC
                LIMIT {$tech_intergalactic}
          ) AS subquery;", '', true);
      $time = $time / $inves['effective_level'];
    }
    $mercenary = MRC_ACADEMIC;
  }
  elseif (in_array($unit_id, $sn_groups['defense']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[STRUC_FACTORY_NANO]['name']]) / ($planet[$sn_data[STRUC_FACTORY_HANGAR]['name']] + 1) ;
    $mercenary = MRC_FORTIFIER;
  }
  elseif (in_array($unit_id, $sn_groups['fleet']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[STRUC_FACTORY_NANO]['name']]) / ($planet[$sn_data[STRUC_FACTORY_HANGAR]['name']] + 1);
    $mercenary = MRC_ENGINEER;
  }

  if($mercenary)
  {
    $time = mrc_modify_value($user, $planet, $mercenary, $time);
  }

  $time = ($time >= 2) ? $time : (in_array($unit_id, $sn_groups['governors']) ? 0 : 2);
  $cost[RES_TIME][BUILD_CREATE]  = floor($time);
  $cost[RES_TIME][BUILD_DESTROY] = floor($time / 2);

  return $cost;
}

?>
