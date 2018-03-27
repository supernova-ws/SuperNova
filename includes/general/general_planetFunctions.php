<?php

use Planet\DBStaticPlanet;

/**
 * Created by Gorlum 04.12.2017 5:04
 */

// PLANET FUNCTIONS ----------------------------------------------------------------------------------------------------------------
function eco_planet_fields_max($planet) {
  return $planet['field_max'] + ($planet['planet_type'] == PT_PLANET ? mrc_get_level($user, $planet, STRUC_TERRAFORMER) * 5 : (mrc_get_level($user, $planet, STRUC_MOON_STATION) * 3));
}

function GetPhalanxRange($phalanx_level) {
  return $phalanx_level > 1 ? pow($phalanx_level, 2) - 1 : 0;
}

/**
 * @param array $planet
 *
 * @return bool
 */
function CheckAbandonPlanetState(&$planet) {
  if ($planet['destruyed'] && $planet['destruyed'] <= SN_TIME_NOW) {
    DBStaticPlanet::db_planet_delete_by_id($planet['id']);

    return true;
  }

  return false;
}

function can_capture_planet() { $result = null; return sn_function_call('can_capture_planet', array(&$result)); }

function sn_can_capture_planet(&$result) {
  return $result = false;
}
