<?php
/**
 * Created by Gorlum 02.12.2017 12:07
 */

namespace Bonus;


class BonusAtomPercent extends BonusAtom {

  // TODO - pick MINIMUM value from unit description

  /**
   * @inheritdoc
   */
  protected function calcAdjustment(&$currentValue, $bonusAmount, $baseValue) {
    $result = $baseValue * $bonusAmount * ($this->power / 100);

    $currentValue += $result;

    return $result;
  }


}
