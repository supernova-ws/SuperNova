<?php
/**
 * Created by Gorlum 01.12.2017 6:00
 */

namespace Bonus;

/**
 * Class BonusDescription
 *
 * One bonus description
 *
 * @package Bonus
 */
class BonusDescription {

  /**
   * How bonus should be applied to base value
   *
   * @var int $bonusType
   */
  public $bonusType = BONUS_NONE;
  /**
   * Amount which would adjust base value
   *
   * @var int|float $bonusValue
   */
  public $bonusValue = 0;

  /**
   * Bonus unit location
   *
   * Needs to distinguish, say, units on Planet and in Fleet
   *
   * @var int $location
   */
  public $location = LOC_NONE;

  /**
   * Which bonus should be used as base to adjust base value
   *
   * @var int $bonusId
   */
  public $bonusId = 0;

  /**
   * Bonus should be applied only if base value is non-zero
   *
   * @var bool $ifBaseNonZero
   */
  public $ifBaseNonZero = true;

  /**
   * BonusDescription constructor.
   *
   * @param int  $bonusId - Unit ID from which base value should be retrieved
   * @param int  $location - Location of unit in context
   * @param bool $ifBaseNonZero - Bonus should applied only if base value is not empty when true
   */
  public function __construct($bonusId, $location, $ifBaseNonZero) {
    $this->location = $location;
    $this->bonusId = $bonusId;
    $this->ifBaseNonZero = $ifBaseNonZero;

    $unitInfo = getUnitInfo($bonusId);

    // BONUS_ADD used as default
    $this->bonusType = isset($unitInfo[P_BONUS_TYPE]) ? $unitInfo[P_BONUS_TYPE] : BONUS_ADD;
    // In conjunction with default BONUS_ADD comes bonus value 0
    $this->bonusValue = isset($unitInfo[P_BONUS_VALUE]) ? $unitInfo[P_BONUS_VALUE] :
      ($this->bonusType == BONUS_ABILITY ? 1 : 0);
  }


  /**
   * Calculates how much should be added to base value
   *
   * @param float|int    $bonusAmount
   * @param ValueBonused $baseValue
   *
   * @return float|int
   */
  public function calcFromValue($bonusAmount, ValueBonused $baseValue) {
    if($baseValue->base == 0 && $this->ifBaseNonZero == BonusCatalog::VALUE_NON_ZERO) {
      return 0;
    }

    switch($this->bonusType) {
      case BONUS_ABILITY:
        $result = $bonusAmount ? 1 : 0;
      break;

      case BONUS_ADD:
        $result = $bonusAmount * $this->bonusValue;
      break;

      case BONUS_PERCENT:
        $result = $baseValue->base * $bonusAmount * $this->bonusValue / 100;
// TODO - проследить, что бы ниже не было отрицательных значений
//            $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
//            $value += $base_value * $mercenary_level * $mercenary_bonus / 100;
      break;

      case BONUS_MULTIPLY:
        $result = $this->bonusValue * $bonusAmount;
      break;

      default:
        $result = 0;
      break;
    }

    return $result;
  }

}
