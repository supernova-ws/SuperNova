<?php
/**
 * Created by Gorlum 02.12.2017 11:40
 */

namespace Bonus;


/**
 * Class BonusFactory
 *
 * Bonus factory - generate appropriate class depending on unit
 *
 * @package Bonus
 */
class BonusFactory {
  public static $classByType = [
    BONUS_ABILITY  => BonusAtomAbility::class,
    BONUS_ADD      => BonusAtomAdd::class,
    BONUS_PERCENT  => BonusAtomPercent::class,
    BONUS_MULTIPLY => BonusAtomMultiply::class,
  ];

  /**
   * @param int  $sourceUnitId - Unit ID from which base value should be retrieved
   * @param bool $ifBaseNonZero
   *
   * @return BonusAtom
   */
  public static function build($sourceUnitId, $ifBaseNonZero) {
    $bonusType = BonusAtom::calcBonusType(getUnitInfo($sourceUnitId));
    $bonusClass = isset(static::$classByType[$bonusType]) ? static::$classByType[$bonusType] : BonusAtom::class;

    return new $bonusClass($sourceUnitId, $ifBaseNonZero);
  }

}
