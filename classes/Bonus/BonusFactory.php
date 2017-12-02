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

  /**
   * @param int  $sourceUnitId - Unit ID from which base value should be retrieved
   * @param int  $location - Location of unit in context
   * @param bool $ifBaseNonZero - Bonus should applied only if base value is not empty when true
   *
   * @return BonusDescription
   */
  public static function build($sourceUnitId, $location, $ifNotEmpty) {
    return new BonusDescription($sourceUnitId, $location, $ifNotEmpty);
  }

}
