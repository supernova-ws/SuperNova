<?php

use Fleet\DbFleetStatic;
use Fleet\FleetDispatchEvent;

/**
 * @param FleetDispatchEvent $fleetEvent
 *
 * @return int
 */
function flt_mission_hold($fleetEvent) {
  if ($fleetEvent->fleet['fleet_end_stay'] < SN_TIME_NOW) {
    DbFleetStatic::fleet_send_back($fleetEvent->fleet);

    return CACHE_FLEET;
  }

  return CACHE_NOTHING;
}
