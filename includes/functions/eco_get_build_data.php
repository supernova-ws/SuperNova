<?php

/**
 * eco_get_build_data.php
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 * @version 1.0
 */

function eco_get_lab_max_effective_level(&$user, $lab_require)
{
  global $sn_data;

  if($user['user_as_ally'])
  {
    $lab_level = doquery("SELECT ally_members AS effective_level FROM {{alliance}} WHERE id = {$user['user_as_ally']} LIMIT 1", true);
  }
  else
  {
    $tech_intergalactic = mrc_get_level($user, false, TECH_RESEARCH);
    $lab_db_name = $sn_data[STRUC_LABORATORY]['name'];
    $nanolab_db_name = $sn_data[STRUC_LABORATORY_NANO]['name'];

    $bonus = mrc_get_level($user, false, UNIT_PREMIUM);
    $lab_require = $lab_require > $bonus ? $lab_require - $bonus : 1;
    $tech_intergalactic = $tech_intergalactic + 1;
    $lab_level = doquery(
      "SELECT SUM(lab) AS effective_level
        FROM
        (
          SELECT (IF({$lab_db_name} > 0, {$lab_db_name} + {$bonus}, 0)) * POW(2, IF({$nanolab_db_name} > 0, {$nanolab_db_name} + {$bonus}, 0)) AS lab
            FROM {{planets}}
              WHERE id_owner='{$user['id']}' AND {$lab_db_name} + {$bonus} >= {$lab_require}
              ORDER BY lab DESC
              LIMIT {$tech_intergalactic}
        ) AS subquery;", '', true);
  }

  return $lab_level['effective_level'] ? $lab_level['effective_level'] : 1;
}

function eco_get_build_data(&$user, $planet, $unit_id, $unit_level = 0, $only_cost = false)
{
  global $sn_data, $config;

  $rpg_exchange_deuterium = $config->rpg_exchange_deuterium;

  $sn_groups = &$sn_data['groups'];
  $unit_data = &$sn_data[$unit_id];
  $unit_db_name = &$unit_data['name'];


  $unit_factor = $unit_data['cost']['factor'] ? $unit_data['cost']['factor'] : 1;
  $price_increase = pow($unit_factor, $unit_level);

  $can_build   = $unit_data['max'] ? $unit_data['max'] : 1000000000000;
  $can_destroy = 1000000000000;
  foreach($unit_data['cost'] as $resource_id => $resource_amount)
  {
    if($resource_id === 'factor')
    {
      continue;
    }

    $resource_cost = $resource_amount * $price_increase;
    if(!$resource_cost)
    {
      continue;
    }

    $cost[BUILD_CREATE][$resource_id] = floor($resource_cost);
    $cost[BUILD_DESTROY][$resource_id] = floor($resource_cost / 2);

    if(in_array($resource_id, $sn_groups['resources_loot']))
    {
      $time += $resource_cost * $config->__get("rpg_exchange_{$sn_data[$resource_id]['name']}") / $rpg_exchange_deuterium;
      $resource_got = $planet[$sn_data[$resource_id]['name']];
    }
    elseif($resource_id == RES_DARK_MATTER)
    {
      $resource_got = $user[$sn_data[$resource_id]['name']];
    }
    elseif($resource_id == RES_ENERGY)
    {
      $resource_got = max(0, $planet['energy_max'] - $planet['energy_used']);
    }
    else
    {
      $resource_got = 0;
    }

    $can_build = min($can_build, $resource_got / $cost[BUILD_CREATE][$resource_id]);
    $can_destroy = min($can_destroy, $resource_got / $cost[BUILD_DESTROY][$resource_id]);
  }

  $can_build = $can_build > 0 ? floor($can_build) : 0;
  $cost['CAN'][BUILD_CREATE]  = $can_build;

  $can_destroy = $can_destroy > 0 ? floor($can_destroy) : 0;
  $cost['CAN'][BUILD_DESTROY] = $can_destroy;

  if($only_cost)
  {
    return $cost;
  }

  $time = $time * 60 * 60 / get_game_speed() / 2500;

  $cost['RESULT'][BUILD_CREATE] = BUILD_ALLOWED;
  if(isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = mrc_get_level($user, $planet, $require_id);

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
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_ROBOT) + 1);
    $mercenary = MRC_ENGINEER;
    $cost['RESULT'][BUILD_DESTROY] = $planet[$unit_db_name] ? ($cost['CAN'][BUILD_DESTROY] ? BUILD_ALLOWED : BUILD_NO_RESOURCES) : BUILD_NO_UNITS;
  }
  elseif(in_array($unit_id, $sn_groups['tech']))
  {
    $lab_level = eco_get_lab_max_effective_level($user, intval($unit_data['require'][STRUC_LABORATORY]));
    $time = $time / $lab_level;
    $mercenary = MRC_ACADEMIC;
  }
  elseif(in_array($unit_id, $sn_groups['defense']))
  {
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1) ;
    $mercenary = MRC_FORTIFIER;
  }
  elseif(in_array($unit_id, $sn_groups['fleet']))
  {
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1);
    $mercenary = MRC_ENGINEER;
  }

  if($mercenary)
  {
    $time = $time / mrc_modify_value($user, $planet, $mercenary, 1);
  }

  $time = ($time >= 1) ? $time : (in_array($unit_id, $sn_groups['governors']) ? 0 : 1);
  $cost[RES_TIME][BUILD_CREATE]  = floor($time);
  $cost[RES_TIME][BUILD_DESTROY] = $time <= 1 ? 1 : floor($time / 2);

  return $cost;
}

function eco_can_build_unit($user, $planet, $unit_id)
{
  global $sn_data;

  $accessible = BUILD_ALLOWED;
  if(isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      if(mrc_get_level($user, $planet, $require_id) < $require_level)
      {
        $accessible = BUILD_REQUIRE_NOT_MEET;
        break;
      }
    }
  }

  return $accessible;
}

// TODO: This function is deprecated and should be replaced!
function eco_unit_busy($user, $planet, $que, $unit_id)
{
  global $config;

  $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
  $lab_busy    = $user['que'] && !$config->BuildLabWhileRun;

  switch($unit_id)
  {
    case STRUC_FACTORY_HANGAR:
      $return = $hangar_busy;
    break;

    case STRUC_LABORATORY:
    case STRUC_LABORATORY_NANO:
      $return = $lab_busy;
    break;

    default:
      $return = false;
    break;
  }

//  return (($unit_id == STRUC_LABORATORY || $unit_id == STRUC_LABORATORY_NANO) && $lab_busy) || ($unit_id == STRUC_FACTORY_HANGAR && $hangar_busy);
  return $return;
}

?>
