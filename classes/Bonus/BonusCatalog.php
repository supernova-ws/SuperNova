<?php
/**
 * Created by Gorlum 01.12.2017 4:31
 */

namespace Bonus;
use Common\GlobalContainer;


/**
 * Class BonusCatalog
 *
 * Bonus catalog - list of all possible modifiers for all bonus types
 *
 * @package Bonus
 */
class BonusCatalog {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->loadDefaults();
  }

  protected function loadDefaults() {

  }

  /**
   * @param int $bonusTypeId
   * @param int $snId
   *
   * @return int
   */
  public function getBonusDescription($bonusTypeId, $snId = 0) {
    return 0;
  }

}
