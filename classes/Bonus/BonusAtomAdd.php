<?php
/**
 * Created by Gorlum 02.12.2017 12:04
 */

namespace Bonus;


class BonusAtomAdd extends BonusAtom {

  public function adjustValue($bonusAmount, ValueBonused $value) {
    $result = 0;
    if (!$this->isReturnNothing($value)) {
      $result = $bonusAmount * $this->power;

      $value->value += $result;
    }

    return $result;
  }

}
