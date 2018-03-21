<?php
/**
 * Created by Gorlum 01.12.2017 4:31
 */

namespace Bonus;

use Core\GlobalContainer;

/**
 * Class BonusCatalog
 *
 * BonusAtom catalog - list of all possible modifiers for all bonus types
 *
 * @package Bonus
 */
class BonusCatalog {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * List of bonuses for levels
   *
   * [(int)$snId => Bonus\BonusListAtom]
   *
   * @var BonusListAtom[] $bonuses
   */
  protected $bonuses = [];

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->loadDefaults();
  }

  protected function loadDefaults() {
    $this->registerBonus(UNIT_PLAYER_EMPIRE_SPY, TECH_SPY, BonusAtom::RETURN_ALWAYS);
    $this->registerBonus(UNIT_PLAYER_EMPIRE_SPY, MRC_SPY, BonusAtom::RETURN_ALWAYS);

//    $this->registerBonus(UNIT_FLEET_PLANET_SPY, UNIT_PLAYER_EMPIRE_SPY);
//    $this->registerBonus(UNIT_FLEET_PLANET_SPY, SHIP_SPY, LOC_FLEET);
  }

  /**
   * Register atomic bonus description
   *
   * @param int  $bonusId - ID of value to which bonus is attached
   * @param int  $baseBonusId - ID of unit which value will be used to calculate bonus
   * @param bool $ifBaseNonZero - Bonus should applied only if base value is not empty when true
   */
  public function registerBonus($bonusId, $baseBonusId, $ifBaseNonZero = BonusAtom::RETURN_IF_BASE_NOT_ZERO) {
    // TODO - also register triggers to invalidate
    if (empty($this->bonuses[$bonusId])) {
      // TODO - lazy loader
      $this->bonuses[$bonusId] = new BonusListAtom($bonusId);
    }

    $this->bonuses[$bonusId]->addUnit($baseBonusId, $ifBaseNonZero);
  }

  /**
   * @param $bonusId
   *
   * @return BonusListAtom|null
   */
  public function getBonusListAtom($bonusId) {
    return array_key_exists($bonusId, $this->bonuses) ? $this->bonuses[$bonusId] : null;
  }

}
