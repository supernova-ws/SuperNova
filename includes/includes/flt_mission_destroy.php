<?php

use Fleet\DbFleetStatic;

require_once('includes/includes/flt_mission_attack.php');

/*
  copyright © 2009-2014 Gorlum for http://supernova.ws
*/
function flt_mission_destroy($mission_data) {
  $fleet_row = $mission_data['fleet'];
  $destination_planet = $mission_data['dst_planet'];
  if(!$destination_planet || !is_array($destination_planet) || $destination_planet['planet_type'] != PT_MOON) {
    DbFleetStatic::fleet_send_back($fleet_row);

    return CACHE_FLEET;
  }

  $combat_data = flt_mission_attack($mission_data);

  return $combat_data;
}
