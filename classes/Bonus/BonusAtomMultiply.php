<?php
/**
 * Created by Gorlum 02.12.2017 12:08
 */

namespace Bonus;


class BonusAtomMultiply extends BonusAtom {

  /**
   * @inheritdoc
   */
  protected function calcAdjustment(&$currentValue, $bonusAmount, $baseValue) {
    $valueOld = $currentValue;

    $currentValue *= $this->power * $bonusAmount;

    $result = $currentValue - $valueOld;

    return $result;
  }

}
