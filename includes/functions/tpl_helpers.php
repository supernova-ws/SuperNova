<?php

// Compare function to sort fleet in time order
function tpl_assign_fleet_compare($a, $b)
{
  if($a['fleet']['OV_THIS_PLANET'] == $b['fleet']['OV_THIS_PLANET'])
  {
    if($a['fleet']['OV_LEFT'] == $b['fleet']['OV_LEFT'])
    {
      return 0;
    }
    return ($a['fleet']['OV_LEFT'] < $b['fleet']['OV_LEFT']) ? -1 : 1;
  }
  else
  {
    return $a['fleet']['OV_THIS_PLANET'] ? -1 : 1;
  }
}

function tpl_assign_fleet(&$template, $fleets, $js_name = 'fleets')
{
  if(!$fleets)
  {
    return;
  }

  usort($fleets, 'tpl_assign_fleet_compare');

  foreach($fleets as $fleet_data)
  {
    $template->assign_block_vars($js_name, $fleet_data['fleet']);

    if($fleet_data['ships'])
    {
      foreach($fleet_data['ships'] as $ship_data)
      {
        $template->assign_block_vars("{$js_name}.ships", $ship_data);
      }
    }
  }
}

// function that parses internal fleet representation (as array(id => count))
function tpl_parse_fleet_sn($fleet, $fleet_id)
{
  global $lang, $time_now, $user, $sn_data;

  $user_data = &$user;

  $return['fleet'] = array(
    'ID'                 => $fleet_id,

    'METAL'              => $fleet[RES_METAL],
    'CRYSTAL'            => $fleet[RES_CRYSTAL],
    'DEUTERIUM'          => $fleet[RES_DEUTERIUM],
  );

  foreach ($fleet as $ship_id => $ship_amount)
  {
    if(in_array($ship_id, $sn_data['groups']['fleet']))
    {
      $single_ship_data = get_ship_data($ship_id, $user_data);
      $return['ships'][$ship_id] = array(
        'ID'          => $ship_id,
        'NAME'        => $lang['tech'][$ship_id],
        'AMOUNT'      => $ship_amount,
        'CONSUMPTION' => $single_ship_data['consumption'],
        'SPEED'       => $single_ship_data['speed'],
        'CAPACITY'    => $single_ship_data['capacity'],
      );
    }
  }

  return $return;
}

function tpl_parse_fleet_db($fleet, $index, $user_data = false)
{
  global $lang, $time_now, $user, $sn_data;

  if(!$user_data)
  {
    $user_data = $user;
  }

  if ($fleet['fleet_mess'] == 0 && $fleet['fleet_mission'] == 2)
  {
    $aks = doquery("SELECT * FROM {{aks}} WHERE id={$fleet['fleet_group']} LIMIT 1;", '', true);
  };

  $spy_level = $user['id'] == $fleet['fleet_owner'] ? 100 : ($user[$sn_data[TECH_SPY]['name']] + $user[$sn_data[MRC_SPY]['name']]);

  $return['fleet'] = array(
    'NUMBER'             => $index,

    'ID'                 => $fleet['fleet_id'],
    'OWNER'              => $fleet['fleet_owner'],
    'TARGET_OWNER'       => $fleet['fleet_target_owner'],

    'MESSAGE'            => $fleet['fleet_mess'],
    'MISSION'            => $fleet['fleet_mission'],
    'MISSION_NAME'       => $lang['type_mission'][$fleet['fleet_mission']],
    'ACS'                => $aks['name'],
    'AMOUNT'             => $spy_level >= 4 ? (pretty_number($fleet['fleet_amount']) . ($fleet['fleet_resource_metal'] + $fleet['fleet_resource_crystal'] + $fleet['fleet_resource_deuterium'] ? '+' : '')) : '?',

    'METAL'              => $spy_level >= 8 ? $fleet['fleet_resource_metal'] : 0,
    'CRYSTAL'            => $spy_level >= 8 ? $fleet['fleet_resource_crystal'] : 0,
    'DEUTERIUM'          => $spy_level >= 8 ? $fleet['fleet_resource_deuterium'] : 0,

    'START_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$fleet['fleet_start_type']],
    'START_COORDS'       => "[{$fleet['fleet_start_galaxy']}:{$fleet['fleet_start_system']}:{$fleet['fleet_start_planet']}]",
    'START_TIME_TEXT'    => date(FMT_DATE_TIME, $fleet['fleet_end_time']),
    'START_LEFT'         => floor($fleet['fleet_end_time'] + 1 - $time_now),
    'START_URL'          => uni_render_coordinates_href($fleet, 'fleet_start_', 3),
    'START_NAME'         => $fleet['fleet_start_name'],

    'END_TYPE_TEXT_SH'   => $lang['sys_planet_type_sh'][$fleet['fleet_end_type']],
    'END_COORDS'         => "[{$fleet['fleet_end_galaxy']}:{$fleet['fleet_end_system']}:{$fleet['fleet_end_planet']}]",
    'END_TIME_TEXT'      => date(FMT_DATE_TIME, $fleet['fleet_start_time']),
    'END_LEFT'           => floor($fleet['fleet_start_time'] + 1 - $time_now),
    'END_URL'            => uni_render_coordinates_href($fleet, 'fleet_end_', 3),
    'END_NAME'           => $fleet['fleet_end_name'],

    'STAY_TIME'          => date(FMT_DATE_TIME, $fleet['fleet_end_stay']),
    'STAY_LEFT'          => floor($fleet['fleet_end_stay'] + 1 - $time_now),

    'OV_LABEL'           => $fleet['ov_label'],
    'OV_TIME_TEXT'       => date(FMT_DATE_TIME, $fleet['ov_time']),
    'OV_LEFT'            => floor($fleet['ov_time'] + 1 - $time_now),
    'OV_THIS_PLANET'     => $fleet['ov_this_planet'],
  );

  $ship_list = explode(';', $fleet['fleet_array']);

  if($spy_level >= 6)
  {
    foreach($ship_list as $ship_record)
    {
      if($ship_record)
      {
        $ship_data = explode(',', $ship_record);
        if($spy_level >= 10)
        {
          $single_ship_data = get_ship_data($ship_data[0], $user_data);
          $return['ships'][$ship_data[0]] = array(
            'ID'          => $ship_data[0],
            'NAME'        => $lang['tech'][$ship_data[0]],
            'AMOUNT'      => $ship_data[1],
            'CONSUMPTION' => $single_ship_data['consumption'],
            'SPEED'       => $single_ship_data['speed'],
            'CAPACITY'    => $single_ship_data['capacity'],
          );
        }
        else
        {
          $return['ships'][$ship_data[0]] = array(
            'ID'          => $ship_id++,
            'NAME'        => $lang['tech'][SHIP_FLEET],
            'AMOUNT'      => $ship_data[1],
            'CONSUMPTION' => 0,
            'SPEED'       => 0,
            'CAPACITY'    => 0,
          );
        }
      }
    }
  }

  return $return;
}

function tpl_parse_planet($planet, $que)
{
  global $lang, $config, $time_now, $sn_data;

  $fleet_list = flt_get_fleets_to_planet($planet);

  $hangar = explode(';', $planet['b_hangar_id']);
  foreach($hangar as $hangar_row)
  {
    if($hangar_row)
    {
      $hangar_row = explode(',', $hangar_row);
      $hangar_que['que'][] = array( 'id' => $hangar_row[0], 'count' => $hangar_row[1]);
      $hangar_que[$hangar_row[0]] += $hangar_row[1];
    }
  }
  $hangar_build_tip = $hangar_que['que'][0]['id'] ? $lang[tech][$hangar_que['que'][0]['id']] : '';

  $result = array(
    'ID'            => $planet['id'],
    'NAME'          => $planet['name'],
    'IMAGE'         => $planet['image'],

    'GALAXY'        => $planet['galaxy'],
    'SYSTEM'        => $planet['system'],
    'PLANET'        => $planet['planet'],
    'TYPE'          => $planet['planet_type'],
    'COORDINATES'   => uni_render_coordinates($planet),

    'METAL_PERCENT'     => $planet['metal_mine_porcent'] * 10,
    'CRYSTAL_PERCENT'   => $planet['crystal_mine_porcent'] * 10,
    'DEUTERIUM_PERCENT' => $planet['deuterium_sintetizer_porcent'] * 10,

    'TECH'          => $planet['b_tech'] ? $lang['tech'][$planet['b_tech_id']] . ' ' . pretty_time($planet['b_tech'] - $time_now) : 0,
    'HANGAR'        => $hangar_build_tip,
    'hangar_que'    => $hangar_que,

    'FIELDS_CUR'    => $planet['field_current'],
    'FIELDS_MAX'    => eco_planet_fields_max($planet),
    'FILL'          => min(100, floor($planet['field_current'] / eco_planet_fields_max($planet) * 100)),

    'FLEET_OWN'     => $fleet_list['own']['count'],
    'FLEET_ENEMY'   => $fleet_list['enemy']['count'],
    'FLEET_NEUTRAL' => $fleet_list['neutral']['count'],

    'fleet_list'    => $fleet_list,

    'PLANET_GOVERNOR_ID' => $planet['PLANET_GOVERNOR_ID'],
    'PLANET_GOVERNOR_NAME' => $lang['tech'][$planet['PLANET_GOVERNOR_ID']],
    'PLANET_GOVERNOR_LEVEL' => $planet['PLANET_GOVERNOR_LEVEL'],
    'PLANET_GOVERNOR_LEVEL_MAX' => $sn_data[$planet['PLANET_GOVERNOR_ID']]['max'],
  );

  if(!empty($que))
  {
    $result['building_que'] = $que['que'][QUE_STRUCTURES];
  }

  return $result;
}

?>
