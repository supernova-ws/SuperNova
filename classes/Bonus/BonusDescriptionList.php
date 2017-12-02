<?php
/**
 * Created by Gorlum 01.12.2017 5:23
 */

namespace Bonus;

use \classSupernova;

/**
 * Class BonusDescriptionList
 *
 * Describes all bonuses for one value
 *
 * @package Bonus
 */
class BonusDescriptionList {

  /**
   * @var int $bonusId
   */
  public $bonusId;

  /**
   * [(int BONUS_xxx)bonusType][(int)$sourceUnitSnId] => BonusDescription
   *
   * @var BonusDescription[][] $bonusDescriptions
   */
  public $bonusDescriptions = [
    BONUS_ABILITY  => [], // Some ability
    BONUS_ADD      => [], // Add
    BONUS_PERCENT  => [], // Percent on base value
    BONUS_MULTIPLY => [], // Multiply by value
//    BONUS_PERCENT_CUMULATIVE => [], // Cumulative percent on base value
//    BONUS_PERCENT_DEGRADED   => [], // Bonus amount degraded with increase as pow(bonus, level) (?)
  ];

  /**
   * BonusDescriptionList constructor.
   *
   * @param int $bonusId
   */
  public function __construct($bonusId) {
    $this->bonusId = $bonusId;
  }

  /**
   * Add bonus from specified unit to current bonus list
   *
   * @param int  $baseBonusId - Unit ID from which base value should be retrieved
   * @param int  $location - Location of unit in context
   * @param bool $ifNotEmpty - Bonus should applied only if base value is not empty when true
   */
  public function addUnit($baseBonusId, $location, $ifNotEmpty = BonusCatalog::VALUE_NON_ZERO) {
    $unitInfo = getUnitInfo($baseBonusId);

    // Detecting location if not specified
    if($location == LOC_AUTODETECT) {
      $location = !empty($unitInfo[P_LOCATION]) ? $unitInfo[P_LOCATION] : LOC_NONE;
    }

    $bonusType = isset($unitInfo[P_BONUS_TYPE]) ? $unitInfo[P_BONUS_TYPE] : BONUS_ADD;

    // ToDo - exception on existing bonus ID?
    $this->bonusDescriptions[$bonusType][$baseBonusId] = BonusFactory::build($baseBonusId, $location, $ifNotEmpty);
  }


  /**
   * Calculates real bonus values within supplied context
   *
   * @param ValueBonused $value
   *
   * @return array
   */
  public function calcBonusValues(ValueBonused $value) {
    $result = [BONUS_NONE => [BONUS_NONE => $value->base]];
    foreach($this->bonusDescriptions as $bonusType => $listOfDescriptions) {
      foreach($listOfDescriptions as $unitId => $description) {
        $unitLevel = classSupernova::$gc->valueStorage->getValue($unitId, $value->context);

        $result[$bonusType][$unitId] = $description->calcFromValue($unitLevel, $value);
      }
    }

// TODO - проследить, что бы ниже не было отрицательных значений
//            $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
//            $value += $base_value * $mercenary_level * $mercenary_bonus / 100;

    return $result;
  }

  // unused. Do I really need this>
  public function mergeList(BonusDescriptionList $list) {
    foreach($list as $bonusType => $bonusesList) {
      foreach($bonusesList as $bonusId => $bonus) {
        $this->bonusDescriptions[$bonusType][$bonusId] = $bonus;
      }
    }
  }

}
