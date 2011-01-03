<?php

function tpl_parse_planet($planet, $que = false)
{
  global $lang, $config, $time_now;

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

  $building_build = explode(',', $planet['b_building_id']);
  $building_build_tip = $building_build[0] ? $lang[tech][$building_build[0]] : '';

  $result = array(
    'ID'            => $planet['id'],
    'NAME'          => $planet['name'],
    'IMAGE'         => $planet['image'],

    'GALAXY'        => $planet['galaxy'],
    'SYSTEM'        => $planet['system'],
    'PLANET'        => $planet['planet'],
    'TYPE'          => $planet['planet_type'],
    'COORDINATES'   => INT_makeCoordinates($planet),

    'BUILDING'      => int_buildCounter($planet, 'building', $planet['id']),
    'BUILDING_TIP'  => $building_build_tip,
    'BUILDING_ID'   => $building_build[0],
    'TECH'          => $planet['b_tech'] ? $lang['tech'][$planet['b_tech_id']] . ' ' . pretty_time($planet['b_tech'] - $time_now) : 0, //date(FMT_DATE_TIME, $planet['b_tech'])
    'HANGAR'        => $hangar_build_tip,
    'hangar_que'    => $hangar_que,

    'FILL'          => min(100, floor($planet['field_current'] / eco_planet_fields_max($planet) * 100)),

    'FLEET_OWN'     => $fleet_list['own']['count'],
    'FLEET_ENEMY'   => $fleet_list['enemy']['count'],
    'FLEET_NEUTRAL' => $fleet_list['neutral']['count'],

    'fleet_list'    => $fleet_list,
  );

  $que_item = $que['que'][QUE_STRUCTURES][0];
  if($que_item)
  {
    $result['BUILDING_ID']  = $que_item['ID'];
    $result['BUILDING_TIP'] = $que_item['NAME'];
    $result['BUILDING']     = int_buildCounter($planet, 'building', $planet['id'], $que);
  }

  $fleet_list = flt_get_fleets_to_planet($planet);

  return $result;
}

?>
