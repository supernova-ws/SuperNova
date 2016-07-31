<?php

/**
 * Class UBEUnitList
 *
 * Hints for IDE - inherited from ArrayAccessV2
 * @method UBEUnit offsetGet($offset)
 * @property UBEUnit[] $_container
 *
 *
 * Invoked via ArrayAccessV2::__call
 *
 * @method void addBonus(Bonus $bonus)
 * @see UBEUnit::addBonus
 *
 * @method void prepare_for_next_round(bool $is_simulator)
 * @see UBEUnit::prepare_for_next_round
 */
class UBEUnitList extends UnitList {

  /**
   * @return UBEUnit
   *
   */
  public function _createElement() {
    return new UBEUnit();
  }

  /**
   * @return float
   */
  public function unitCountLost() {
    return $this->getSumProperty('units_lost');
  }

  /**
   * @return int
   */
  public function unitCountReapers() {
    return $this->unitsCountById(SHIP_HUGE_DEATH_STAR);
  }

}
