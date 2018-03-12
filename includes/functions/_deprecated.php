<?php
/**
 * Created by Gorlum 15.06.2017 4:32
 */

// ------------------------------------------------------------------
/**
 * @param array $fleet_row
 * @param bool  $start
 * @param bool  $only_resources
 * @param bool  $safe_fleet
 *
 * @return mixed
 * @deprecated
 */
function RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false){return sn_function_call('RestoreFleetToPlanet', array(&$fleet_row, $start, $only_resources, $safe_fleet, &$result));}
/**
 * @param array $fleet_row
 * @param bool  $start
 * @param bool  $only_resources
 * @param bool  $safe_fleet
 * @param mixed $result
 *
 * @return int
 * @deprecated
 */
function sn_RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false, &$result) {
  return SN::$gc->fleetDispatcher->sn_RestoreFleetToPlanet($fleet_row, $start, $only_resources, $safe_fleet, $result);
}
