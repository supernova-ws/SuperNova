<?php

function flt_travel_data($user_row, $from, $to, $fleet_array, $speed_percent = 10)
{
  if($from['galaxy'] != $to['galaxy'])
  {
    $distance = abs($from['galaxy'] - $to['galaxy']) * 20000;
  }
  elseif($from['system'] != $to['system'])
  {
    $distance = abs($from['system'] - $to['system']) * 5 * 19 + 2700;
  }
  elseif($from['planet'] != $to['planet'])
  {
    $distance = abs($from['planet'] - $to['planet']) * 5 + 1000;
  }
  else
  {
    $distance = 5;
  }

  $consumption = 0;
  $duration = 0;

  $game_fleet_speed = get_fleet_speed();
  $fleet_speed = flt_fleet_speed($user_row, $fleet_array);
  if(!empty($fleet_array) && $fleet_speed && $game_fleet_speed)
  {
    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;
    $real_speed = $speed_percent * sqrt($fleet_speed);

    $duration = max(1, round((35000 / $speed_percent * sqrt($distance * 10 / $fleet_speed) + 10) / $game_fleet_speed));

    foreach($fleet_array as $ship_id => $ship_count)
    {
      if (!$ship_id || !$ship_count)
      {
        continue;
      }

      $single_ship_data = get_ship_data($ship_id, $user_row);
      $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

      $consumption += $single_ship_data['consumption'] * $ship_count * pow($real_speed / sqrt($single_ship_data['speed']) / 10 + 1, 2);
    }

    $consumption = round($distance * $consumption / 35000) + 1;
  }

  return array('fleet_speed' => $fleet_speed,'distance' => $distance, 'duration' => $duration, 'consumption' => $consumption);
}

function get_ship_data($ship_id, $user)
{
  global $sn_data;

  $ship_data = array();
  if(in_array($ship_id, $sn_data['groups']['fleet']))
  {
    foreach($sn_data[$ship_id]['engine'] as $engine_info)
    {
      if($user[$sn_data[$engine_info['tech']]['name']] >= $engine_info['min_level'])
      {
        $ship_data = $engine_info;
      }
    }
    $ship_data['speed'] = floor(mrc_modify_value($user, false, array(MRC_NAVIGATOR, $ship_data['tech']), $ship_data['speed']));

    $ship_data['capacity'] = $sn_data[$ship_id]['capacity'];
  }

  return $ship_data;
}

function flt_fleet_speed($user, $fleet)
{
  global $sn_data;

  if (!is_array($fleet))
  {
    $fleet = array($fleet => 1);
  }

  $speeds = array();
  if(!empty($fleet))
  {
    foreach ($fleet as $ship_id => $amount)
    {
      if($amount && in_array($ship_id, $sn_data['groups']['fleet']))
      {
        $single_ship_data = get_ship_data($ship_id, $user);
        $speeds[] = $single_ship_data['speed'];
      }
    }
  }

  return empty($speeds) ? 0 : min($speeds);
}

function get_fleet_speed()
{
  return $GLOBALS['config']->fleet_speed;
}

?>
