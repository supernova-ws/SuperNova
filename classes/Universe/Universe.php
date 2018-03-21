<?php
/**
 * Created by Gorlum 13.02.2018 12:45
 */

namespace Universe;

use \Fleet\RecordFleet;

class Universe {

  const MOON_MIN_SIZE = 1100;
  const MOON_MAX_SIZE = 8999;

  const MOON_DEBRIS_MIN = 1000000;    // Minimum amount of debris to span a moon
  const MOON_CHANCE_MIN_PERCENT = 1;  // Minimum chance to span a moon
  const MOON_CHANCE_MAX_PERCENT = 30; // Maximum chance to span a moon

  /**
   * Calc moon chance from debris
   *
   * @param float $debrisTotal
   *
   * @return float
   */
  public static function moonCalcChanceFromDebris($debrisTotal) {
    $moon_chance = $debrisTotal / static::moonPercentCostInDebris();

    $moon_chance = $moon_chance < static::MOON_CHANCE_MIN_PERCENT ? 0 : $moon_chance;
    $moon_chance = $moon_chance > static::MOON_CHANCE_MAX_PERCENT ? static::MOON_CHANCE_MAX_PERCENT : $moon_chance;

    return $moon_chance;
  }

  /**
   * Roll moon size from rolled fracture
   *
   * @param float $rolledChance
   *
   * @return int
   */
  protected static function moonRollSizeSecondary($rolledChance) {
    $minSize = max(static::MOON_MIN_SIZE, $rolledChance * 100 + 1000);
    $maxSize = min(static::MOON_MAX_SIZE, $rolledChance * 200 + 2999);

    return mt_rand($minSize, $maxSize);
  }

  /**
   * Roll moon size from debris amount
   *
   * @param int|float $debrisTotal
   *
   * @return int
   */
  public static function moonRollSize($debrisTotal) {
    $roll = mt_rand(1, 100);
    if ($roll <= static::moonCalcChanceFromDebris($debrisTotal)) {
      $moonSize = Universe::moonRollSizeSecondary($roll);
    } else {
      $moonSize = 0;
    }

    return $moonSize;
  }


  /**
   * Real cost of 1% moon creation size in debris
   *
   * @return float|int
   */
  public static function moonPercentCostInDebris() {
    return game_resource_multiplier(true) * static::MOON_DEBRIS_MIN;
  }

  /**
   * Max debris cost for max sized moon
   *
   * @return float|int
   */
  public static function moonMaxDebris() {
    return static::MOON_CHANCE_MAX_PERCENT * static::moonPercentCostInDebris();
  }

  /**
   * Random moon size within allowed limits
   *
   * @return int
   */
  public static function moonSizeRandom() {
    return mt_rand(static::MOON_MIN_SIZE, static::MOON_MAX_SIZE);
  }


  /**
   * Return fleets heading to specified location
   *
   * @param int|null $galaxy
   * @param int|null $system
   * @param int|null $planet
   * @param bool     $fromHold - Should be fleets on active Hold mission returned too
   */
  public static function fleetsReturn($galaxy = null, $system = null, $planet = null, $type = null, $fromHold = true) {
    $filter = [];
    $galaxy ? $filter['fleet_end_galaxy'] = $galaxy : false;
    $galaxy ? $filter['fleet_end_system'] = $system : false;
    $galaxy ? $filter['fleet_end_planet'] = $planet : false;
    $galaxy ? $filter['fleet_end_type'] = $type : false;
    $fleetsResult = RecordFleet::findAll($filter);
    foreach ($fleetsResult as $fleetRecord) {
      if ($fleetRecord->fleet_mess == FLEET_STATUS_RETURNING) {
        continue;
      }

      if ($fleetRecord->fleet_mission == MT_HOLD) {
        if (!$fromHold) {
          continue;
        }

        // Changing end time
        $fleetRecord->fleet_end_stay = SN_TIME_NOW;
      }

      $fleetRecord->fleet_mess = FLEET_STATUS_RETURNING;

      $fleetRecord->update();
    }
  }

}
