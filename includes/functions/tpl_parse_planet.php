<?php

function tpl_parse_planet($planet)
{
  global $lang, $config, $time_now;

  $hangar_build = explode(',', $planet['b_hangar_id']);
  $hangar_build = $hangar_build[0] ? $lang[tech][$hangar_build[0]] : '';

  $building_build = explode(',', $planet['b_building_id']);
  $building_build = $building_build[0] ? $lang[tech][$building_build[0]] : '';

  $fleet_list = flt_get_fleets_to_planet($planet);

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
    'BUILDING_TIP'  => $building_build,
    'TECH'          => $planet['b_tech'] ? $lang['tech'][$planet['b_tech_id']] . ' ' . pretty_time($planet['b_tech'] - $time_now) : 0, //date($config->game_date_withTime, $planet['b_tech'])
    'HANGAR'        => $hangar_build,

    'FILL'          => min(100, floor($planet['field_current'] / CalculateMaxPlanetFields($planet) * 100)),

    'FLEET_OWN'     => $fleet_list['own']['count'],
    'FLEET_ENEMY'   => $fleet_list['enemy']['count'],
    'FLEET_NEUTRAL' => $fleet_list['neutral']['count'],

    'fleet_list'    => $fleet_list,
  );

  return $result;
}

?>