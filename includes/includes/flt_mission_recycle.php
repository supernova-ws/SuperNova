<?php

/**
 * Fleet mission "Recycle"
 *
 * @param $mission_data Mission
 *
 * @return int
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_recycle($mission_data) {
  $objFleet = $mission_data->fleet;
  $destination_planet = &$mission_data->dst_planet;
  if(empty($destination_planet['id'])) {
    $objFleet->markReturned();
    $objFleet->dbSave();

    return CACHE_FLEET;
  }

  $RecyclerCapacity = 0;
  $OtherFleetCapacity = 0;

  $fleet_array = $objFleet->shipsGetArray();
  foreach($fleet_array as $unit_id => $unit_count) {
    if(in_array($unit_id, sn_get_groups('fleet'))) {
      $capacity = get_unit_param($unit_id, P_CAPACITY) * $unit_count;
      if(in_array($unit_id, sn_get_groups('flt_recyclers'))) {
        $RecyclerCapacity += $capacity;
      } else {
        $OtherFleetCapacity += $capacity;
      }
    }
  }

  $fleet_resources_amount = $objFleet->resourcesGetTotal();
  if($fleet_resources_amount > $OtherFleetCapacity) {
    // Если во флоте есть другие корабли И количество ресурсов больше, чем их ёмкость трюмов - значит часть этих ресурсов лежит в трюмах переработчиков
    // Уменьшаем ёмкость переработчиков на указанную величину
    $RecyclerCapacity -= ($fleet_resources_amount - $OtherFleetCapacity);
  }

  $resources_recycled = array();
  if(($destination_planet["debris_metal"] + $destination_planet["debris_crystal"]) <= $RecyclerCapacity) {
    $resources_recycled[RES_METAL] = $destination_planet["debris_metal"];
    $resources_recycled[RES_CRYSTAL] = $destination_planet["debris_crystal"];
  } else {
    if(($destination_planet["debris_metal"] > $RecyclerCapacity / 2) &&
      ($destination_planet["debris_crystal"] > $RecyclerCapacity / 2)
    ) {
      $resources_recycled[RES_METAL] = $RecyclerCapacity / 2;
      $resources_recycled[RES_CRYSTAL] = $RecyclerCapacity / 2;
    } else {
      if($destination_planet["debris_metal"] > $destination_planet["debris_crystal"]) {
        $resources_recycled[RES_CRYSTAL] = $destination_planet["debris_crystal"];
        if($destination_planet["debris_metal"] > ($RecyclerCapacity - $resources_recycled[RES_CRYSTAL])) {
          $resources_recycled[RES_METAL] = $RecyclerCapacity - $resources_recycled[RES_CRYSTAL];
        } else {
          $resources_recycled[RES_METAL] = $destination_planet["debris_metal"];
        }
      } else {
        $resources_recycled[RES_METAL] = $destination_planet["debris_metal"];
        if($destination_planet["debris_crystal"] > ($RecyclerCapacity - $resources_recycled[RES_METAL])) {
          $resources_recycled[RES_CRYSTAL] = $RecyclerCapacity - $resources_recycled[RES_METAL];
        } else {
          $resources_recycled[RES_CRYSTAL] = $destination_planet["debris_crystal"];
        }
      }
    }
  }

  DBStaticPlanet::db_planet_set_by_gspt($destination_planet['galaxy'], $destination_planet['system'], $destination_planet['planet'], PT_PLANET,
    "`debris_metal` = `debris_metal` - '{$resources_recycled[RES_METAL]}', `debris_crystal` = `debris_crystal` - '{$resources_recycled[RES_CRYSTAL]}'"
  );

  $Message = sprintf(
    classLocale::$lang['sys_recy_gotten'],
    pretty_number($resources_recycled[RES_METAL]), classLocale::$lang['Metal'],
    pretty_number($resources_recycled[RES_CRYSTAL]), classLocale::$lang['Crystal']
  );
  DBStaticMessages::msg_send_simple_message(
    $objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_RECYCLE,
    classLocale::$lang['sys_mess_spy_control'], classLocale::$lang['sys_recy_report'], $Message
  );

  $objFleet->resourcesAdjust($resources_recycled);
  $objFleet->markReturned();
  $objFleet->dbSave();

  return CACHE_FLEET | CACHE_PLANET_DST;
}
