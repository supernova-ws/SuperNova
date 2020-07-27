<?php
/**
 * Created by Gorlum 24.03.2018 21:54
 */

namespace Meta\Economic;


use SN;

class BuildDataStatic {

  /**
   * @param float $prevDivisor - because this is Hooker's client so prev result variable should be declared
   * @param array $user
   * @param array $planet
   * @param int   $unit_id
   * @param array $unit_data
   *
   * @return float
   */
  public static function getStructuresTimeDivisor($prevDivisor, $user, $planet, $unit_id, $unit_data) {
    $result = 1;
    if (in_array($unit_id, sn_get_groups('structures'))) {
      $result = pow(2, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) * (mrc_get_level($user, $planet, STRUC_FACTORY_ROBOT) + 1);
    } elseif (in_array($unit_id, sn_get_groups('defense'))) {
      $result = pow(2, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) * (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1);
    } elseif (in_array($unit_id, sn_get_groups('fleet'))) {
      $result = pow(2, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) * (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1);
    } elseif (in_array($unit_id, sn_get_groups('tech'))) {
      $result = eco_get_lab_max_effective_level($user, intval($unit_data[P_REQUIRE][STRUC_LABORATORY]));
    }

    return $result;
  }

  /**
   * @param int $unit_id
   *
   * @return float
   */
  public static function getMercenaryTimeDivisor($user, $planet, $unit_id) {
    if (in_array($unit_id, sn_get_groups('structures'))) {
      $mercenary = MRC_ENGINEER;
    } elseif (in_array($unit_id, sn_get_groups('tech'))) {
      $mercenary = MRC_ACADEMIC;
    } elseif (in_array($unit_id, sn_get_groups('defense'))) {
      $mercenary = MRC_FORTIFIER;
    } elseif (in_array($unit_id, sn_get_groups('fleet'))) {
      $mercenary = MRC_ENGINEER;
    } else {
      $mercenary = 0;
    }

    return $mercenary ? mrc_modify_value($user, $planet, $mercenary, 1) : 1;
  }

  /**
   * @param array $user
   * @param array $planet
   * @param int   $unit_id
   *
   * @return float|int
   */
  public static function getCapitalTimeDivisor($user, $planet, $unit_id) {
    static $capitalUnitGroups;
    empty($capitalUnitGroups) ? $capitalUnitGroups = sn_get_groups(sn_get_groups(GROUP_CAPITAL_BUILDING_BONUS_GROUPS)) : false;

    if (
      // If planet is capital
      $user['id_planet'] == $planet['id']
      &&
      // There is capital building rate set
      SN::$gc->config->planet_capital_building_rate > 0
      &&
      // Unit is subject to Capital bonus
      in_array($unit_id, $capitalUnitGroups)
    ) {
      $capitalTimeDivisor = SN::$gc->config->planet_capital_building_rate;
    } else {
      $capitalTimeDivisor = 1;
    }

    return $capitalTimeDivisor;
  }

  /**
   * @param array $user
   * @param array $planet
   * @param int   $unit_id
   * @param array $cost
   *
   * @return float - Time need to destroy current level of unit in seconds
   */
  public static function getDestroyStatus($user, $planet, $unit_id, $cost) {
    static $groupStructures;
    empty($groupStructures) ? $groupStructures = sn_get_groups('structures') : false;

    $result = !in_array($unit_id, $groupStructures) ? BUILD_INDESTRUCTABLE :
      (!mrc_get_level($user, $planet, $unit_id, false, true) ? BUILD_NO_UNITS :
        (
        !($cost['CAN'][BUILD_DESTROY]) ? BUILD_NO_RESOURCES :
          ($cost['RESULT'][BUILD_CREATE] == BUILD_UNIT_BUSY ? BUILD_UNIT_BUSY : BUILD_ALLOWED)
        )
      );

    return $result;
  }

  /**
   * @param array $user
   * @param array $planet
   * @param array $cost
   *
   * @return float
   */
  public static function getAutoconvertCount($user, $planet, $cost) {
    static $groupResourcesLoot;
    empty($groupResourcesLoot) ? $groupResourcesLoot = sn_get_groups('resources_loot') : false;

    $cost_in_metal          = 0;
    $planetResourcesInMetal = 0;
    foreach ($groupResourcesLoot as $resource_id) {
      $planetResourcesInMetal += floor(get_unit_cost_in([$resource_id => mrc_get_level($user, $planet, $resource_id)]));
      !empty($cost[BUILD_CREATE][$resource_id])
        ? $cost_in_metal += get_unit_cost_in([$resource_id => $cost[BUILD_CREATE][$resource_id]])
        : false;
    }

    return floor($planetResourcesInMetal / $cost_in_metal);
  }

  /**
   * @param $user
   * @param $planet
   * @param $unit_data
   * @param $unit_level
   *
   * @return array
   */
  public static function getBasicData(&$user, $planet, $unit_data, $unit_level) {
    static $groupResourcesLoot;
    empty($groupResourcesLoot) ? $groupResourcesLoot = sn_get_groups('resources_loot') : false;

    $cost = [
      P_OPTIONS => [
        P_TIME_RAW => 0,
      ],
    ];

    $unit_factor      = !empty($unit_data[P_COST][P_FACTOR]) ? $unit_data[P_COST][P_FACTOR] : 1;
    $levelPriceFactor = pow($unit_factor, $unit_level);

    $only_dark_matter = 0;
    $canDestroyAmount = 1000000000000;
    $canBuildAmount   = !empty($unit_data[P_MAX_STACK]) ? $unit_data[P_MAX_STACK] : 1000000000000;
    foreach ($unit_data[P_COST] as $resourceId => $costResource) {
      if ($resourceId === P_FACTOR || !($levelPrice = ceil($costResource * $levelPriceFactor))) {
        continue;
      }

      $only_dark_matter = $only_dark_matter ? $only_dark_matter : $resourceId;

      $cost[BUILD_CREATE][$resourceId]  = $levelPrice;
      $cost[BUILD_DESTROY][$resourceId] = ceil($levelPrice / 2);

      if (in_array($resourceId, $groupResourcesLoot)) {
        $cost[P_OPTIONS][P_TIME_RAW] += get_unit_cost_in([$resourceId => $levelPrice], RES_DEUTERIUM);
      }

      $resource_got = !empty($user)
        ? BuildDataStatic::eco_get_resource_on_location($user, $planet, $resourceId, $groupResourcesLoot)
        : 0;

      $canBuildAmount   = min($canBuildAmount, floor($resource_got / $cost[BUILD_CREATE][$resourceId]));
      $canDestroyAmount = min($canDestroyAmount, floor($resource_got / $cost[BUILD_DESTROY][$resourceId]));
    }
    $cost['CAN'][BUILD_CREATE]           = $canBuildAmount > 0 ? floor($canBuildAmount) : 0;
    $cost['CAN'][BUILD_DESTROY]          = $canDestroyAmount > 0 ? floor($canDestroyAmount) : 0;
    $cost[P_OPTIONS][P_ONLY_DARK_MATTER] = $only_dark_matter == RES_DARK_MATTER;

    return $cost;
  }

  /**
   * Special function to get resource on location
   *
   * @param array $user
   * @param array $planet
   * @param int   $resource_id
   * @param array $groupResourcesLoot
   *
   * @return float
   */
  public static function eco_get_resource_on_location($user, $planet, $resource_id, $groupResourcesLoot) {
    if (in_array($resource_id, $groupResourcesLoot)) {
      $resource_got = mrc_get_level($user, $planet, $resource_id);
    } elseif ($resource_id == RES_DARK_MATTER) {
      $resource_got = mrc_get_level($user, [], $resource_id);
    } elseif ($resource_id == RES_ENERGY) {
      $resource_got = max(0, $planet['energy_max'] - $planet['energy_used']);
    } else {
      $resource_got = 0;
    }

    return $resource_got;
  }

  /**
   * @param array $user
   * @param array $planet
   * @param int   $unit_id
   * @param float $costCanBuildCreate
   *
   * @return int|mixed
   */
  public static function getBuildStatus(&$user, $planet, $unit_id, $costCanBuildCreate) {
    $result = eco_can_build_unit($user, $planet, $unit_id);

    // Additional check for resources. If no units can be built - it means that there are not enough resources
    return $result == BUILD_ALLOWED && $costCanBuildCreate < 1 ? BUILD_NO_RESOURCES : $result;
  }


}
