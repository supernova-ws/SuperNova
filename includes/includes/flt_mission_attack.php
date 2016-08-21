<?php

/*
  copyright © 2009-2016 Gorlum for http://supernova.ws
*/

// Used by game_skirmish
use Mission\Mission;

/**
 * @param Mission $objMission
 * @param array   $mission_data
 */
function flt_mission_attack($objMission) {
  $objFleet = $objMission->fleet;

  if($objFleet->shipsGetTotal() <= 0) {
    return;
  }

  $destination_user = $objMission->dst_user;

  if(
    // Нет данных о планете назначения или её владельце
    empty($destination_user)
    ||
    !is_array($destination_user)
    ||
    // "Уничтожение" не на луну
    ($objFleet->mission_type == MT_DESTROY && $objFleet->fleet_end_type != PT_MOON)
  ) {
    $objFleet->markReturnedAndSave();

    return;
  }

  UBE::flt_mission_attack($objMission);
}
