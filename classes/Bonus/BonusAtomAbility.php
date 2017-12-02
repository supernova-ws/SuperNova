<?php
/**
 * Created by Gorlum 02.12.2017 11:59
 */

namespace Bonus;


class BonusAtomAbility extends BonusAtom {

  public function adjustValue($bonusAmount, ValueBonused $value) {
    $result = 0;
    if (!$this->isReturnNothing($value)) {
      $result = $bonusAmount ? 1 : 0;

      $value->value = $value->value || $result ? 1 : 0;
    }

    return $result;
  }

}
