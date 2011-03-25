<?php

/**
 * eco_get_build_data.php
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 * @version 1.0
 */

function eco_get_build_data($user, $planet, $unit_id, $unit_level = 0)
{
  global $sn_data;

  $sn_groups = $sn_data['groups'];

  $unit_data = $sn_data[$unit_id];
  $unit_db_name = $unit_data['name'];
  $unit_factor = $unit_data['factor'] ? $unit_data['factor'] : 1;

  $price_increase = pow($unit_factor, $unit_level);
  $can_build   = 1000000000000;
  foreach($unit_data['cost'] as $resource_id => $resource_amount)
  {
    $resource_cost = $resource_amount * $price_increase;
    $cost[BUILD_CREATE][$resource_id] = floor($resource_cost);
    $cost[BUILD_DESTROY][$resource_id] = floor($resource_cost / 2);

    if($resource_id != RES_ENERGY && $resource_id != RES_DARK_MATTER)
    {
      if($resource_cost != 0)
      {
        $can_build = min($can_build, $planet[$sn_data[$resource_id]['name']] / $resource_cost);
      }
      $time += $resource_cost;
    }
  }
  $cost['CAN'][BUILD_DESTROY] = floor($can_build * 2);
  $cost['CAN'][BUILD_CREATE]  = floor($can_build);
  $time = $time * 60 * 60 / get_game_speed() / 2500;

  $mercenary = 0;
  if (in_array($unit_id, $sn_groups['structures']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[15]['name']]) / ($planet[$sn_data[14]['name']] + 1);
    $mercenary = MRC_ARCHITECT;
  }
  elseif (in_array($unit_id, $sn_groups['tech']))
  {
    $tech_intergalactic = $user[$sn_data[123]['name']];
    if ( $tech_intergalactic < 1 )
    {
      $time = $time * pow(0.5, $planet[$sn_data[35]['name']]) / (($planet[$sn_data[31]['name']] + 1) * 2);
    }
    else
    {
      $lab_db_name = $sn_data[31]['name'];
      $lab_require = intval($unit_data['require'][31]);
      $tech_intergalactic = $tech_intergalactic + 1;

      $inves = doquery("SELECT SUM(`{$lab_db_name}`) AS `laboratorio`
        FROM
        (
          SELECT `{$lab_db_name}`
            FROM `{{planets}}`
            WHERE `id_owner` = '{$user['id']}' AND `{$lab_db_name}` >= {$lab_require}
            ORDER BY `{$lab_db_name}` DESC
            LIMIT {$tech_intergalactic}
        ) AS subquery;", '', true);
      $time = $time / (($inves['laboratorio'] + 1) * 2) * pow(0.5, $planet[$sn_data[35]['name']]);

      /*
      $inves = doquery(
        "SELECT SUM(lab) AS effective_level
          FROM
          (
            SELECT ({$lab_db_name} + 1) * 2 / pow(0.5, {$sn_data[35]['name']}) AS lab
              FROM {{planets}}
                WHERE id_owner='{$user['id']}' AND {$lab_db_name} >= {$lab_require}
                ORDER BY {$lab_db_name} DESC
                LIMIT {$tech_intergalactic}
          ) AS subquery;", '', true);
      $time = $time * 1 / $inves['effective_level'];
      */
    }
    $mercenary = MRC_ACADEMIC;
  }
  elseif (in_array($unit_id, $sn_groups['defense']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[15]['name']]) / ($planet[$sn_data[21]['name']] + 1) ;
    $mercenary = MRC_FORTIFIER;
  }
  elseif (in_array($unit_id, $sn_groups['fleet']))
  {
    $time = $time * pow(0.5, $planet[$sn_data[15]['name']]) / ($planet[$sn_data[21]['name']] + 1);
    $mercenary = MRC_CONSTRUCTOR;
  }

  if($mercenary)
  {
    $time = mrc_modify_value($user, $planet, $mercenary, $time);
  }

  $time = ($time >= 2) ? $time : 2;
  $cost[BUILD_CREATE][RES_TIME]  = floor($time);
  $cost[BUILD_DESTROY][RES_TIME] = floor($time / 2);

  return $cost;
}

?>
