<?php
/**
 * Created by Gorlum 02.12.2017 11:59
 */

namespace Bonus;


class BonusAtomAbility extends BonusAtom {

  /**
   * @inheritdoc
   */
  protected function calcAdjustment(&$currentValue, $bonusAmount, $baseValue) {
    $result = $bonusAmount ? 1 : 0;

    $currentValue = $currentValue || $result ? 1 : 0;

    return $result;
  }

}
