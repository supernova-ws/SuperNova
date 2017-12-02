<?php
/**
 * Created by Gorlum 01.12.2017 6:00
 */

namespace Bonus;

/**
 * Class BonusAtom
 *
 * Info about one single bonus - atomic one
 *
 * @package Bonus
 */
class BonusAtom {
  /**
   * Unit SN ID to use as base to adjust base value
   *
   * @var int $snId
   */
  public $snId = 0;

  /**
   * Bonus power is a value which will additionally adjust value amount
   *
   * Used for BONUS_PERCENT mainly
   *
   * @var int|float $power
   */
  public $power = 0;

  /**
   * Flag that bonus should be applied only if base value is not zero
   *
   * @var bool $ifBaseNonZero
   */
  public $ifBaseNonZero = true;

  /**
   * Calculates bonus type basing on provided unit info
   *
   * @param array|null $unitInfo
   *
   * @return int
   */
  public static function calcBonusType($unitInfo) {
    // BONUS_ADD used as default
    return isset($unitInfo[P_BONUS_TYPE]) ? $unitInfo[P_BONUS_TYPE] : BONUS_ADD;
  }

  /**
   * Calculates bonus power based on provided unit info
   *
   * Needs for BONUS_PERCENT, for example
   *
   * @param array|null $unitInfo
   *
   * @return int
   */
  public static function calcBonusPower($unitInfo) {
    return isset($unitInfo[P_BONUS_VALUE]) ? $unitInfo[P_BONUS_VALUE] :
      (static::calcBonusType($unitInfo) == BONUS_ABILITY ? 1 : 0);
  }

  /**
   * BonusAtom constructor.
   *
   * @param int  $bonusId - Unit ID from which base value should be retrieved
   * @param bool $ifBaseNonZero - Bonus should applied only if base value is not empty when true
   */
  public function __construct($bonusId, $ifBaseNonZero) {
    $this->snId = $bonusId;
    $this->ifBaseNonZero = $ifBaseNonZero;

    // In conjunction with default BONUS_ADD comes bonus value 0
    $this->power = static::calcBonusPower(getUnitInfo($bonusId));
  }


  protected function isReturnNothing(ValueBonused $baseValue) {
    return $baseValue->base == 0 && $this->ifBaseNonZero == BonusCatalog::VALUE_NON_ZERO;
  }

  /**
   * Calculates how much should be added to base value
   *
   * @param float|int    $bonusAmount
   * @param ValueBonused $value
   *
   * @return float|int
   */
  public function adjustValue($bonusAmount, ValueBonused $value) {
    return 0;
  }

}
