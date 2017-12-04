<?php
/**
 * Created by Gorlum 02.12.2017 12:04
 */

namespace Bonus;


class BonusAtomAdd extends BonusAtom {

  /**
   * @inheritdoc
   */
  protected function calcAdjustment(&$currentValue, $bonusAmount, $baseValue) {
    $result = $bonusAmount * $this->power;

    $currentValue += $result;

    return $result;
  }

}
