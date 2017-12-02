<?php
/**
 * Created by Gorlum 02.12.2017 12:08
 */

namespace Bonus;


class BonusAtomMultiply extends BonusAtom {

  public function adjustValue($bonusAmount, ValueBonused $value) {
    $result = 0;
    if (!$this->isReturnNothing($value)) {
      $valueOld = $value->value;

      $value->value *= $this->power * $bonusAmount;

      $result = $value->value - $valueOld;
    }

    return $result;
  }

}
