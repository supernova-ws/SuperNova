<?php
/**
 * Created by Gorlum 24.03.2018 21:54
 */

namespace Meta\Economic;


use SN;

class BuildDataStatic {

  /**
   * @param float $prevDivisor - because this is Hooker prev result variable should be declared
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
    if(empty($capitalUnitGroups)) {
      $capitalUnitGroups = sn_get_groups(sn_get_groups(GROUP_CAPITAL_BUILDING_BONUS_GROUPS));
    }

    if (
      // If planet is capital
      $user['id_planet'] == $planet['id']
      &&
      SN::$gc->config->planet_capital_building_rate > 0
      &&
      in_array($unit_id, $capitalUnitGroups)
    ) {
      $capitalTimeDivisor = SN::$gc->config->planet_capital_building_rate;
    } else {
      $capitalTimeDivisor = 1;
    }

    return $capitalTimeDivisor;
  }

}
