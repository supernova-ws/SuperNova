<?php
/**
 * Created by Gorlum 02.12.2017 12:07
 */

namespace Bonus;


class BonusAtomPercent extends BonusAtom {

  // TODO - pick MINIMUM value from unit description

  public function adjustValue($bonusAmount, ValueBonused $value) {
    $result = 0;
    if (!$this->isReturnNothing($value)) {
      $result = $value->base * $bonusAmount * $this->power / 100;

      $value->value += $result;
    }

    return $result;
  }

}
