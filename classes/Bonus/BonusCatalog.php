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
  CONST VALUE_NON_ZERO = true;
  CONST VALUE_ANY = false;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * List of bonuses for levels
   *
   * [(int)$snId => Bonus\BonusDescriptionList]
   *
   * @var BonusDescriptionList[] $bonuses
   */
  protected $bonuses = [];

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->loadDefaults();
  }

  protected function loadDefaults() {
//    $this->registerBonus(UNIT_SERVER_SPEED_BUILDING, UNIT_PREMIUM);


//    $this->registerBonus(TECH_SPY, UNIT_PREMIUM);
//    $this->registerBonus(MRC_SPY, UNIT_PREMIUM);
//
    $this->registerBonus(UNIT_PLAYER_EMPIRE_SPY, TECH_SPY, BonusCatalog::VALUE_ANY);
    $this->registerBonus(UNIT_PLAYER_EMPIRE_SPY, MRC_SPY, BonusCatalog::VALUE_ANY);

//    $this->registerBonus(UNIT_FLEET_PLANET_SPY, UNIT_PLAYER_EMPIRE_SPY);
//    $this->registerBonus(UNIT_FLEET_PLANET_SPY, SHIP_SPY, LOC_FLEET);
  }

  /**
   * Register atomic bonus description
   *
   * @param int  $bonusId - ID of value to which bonus is attached
   * @param int  $baseBonusId - ID of unit which value will be used to calculate bonus
   * @param bool $ifNotEmpty - Bonus should applied only if base value is not empty when true
   * @param int  $location - location of unit for bonus calculation. Default - LOC_AUTODETECT: taken from unit info
   */
  public function registerBonus($bonusId, $baseBonusId, $ifNotEmpty = BonusCatalog::VALUE_NON_ZERO, $location = LOC_AUTODETECT) {
    // TODO - also register triggers to invalidate
    if(empty($this->bonuses[$bonusId])) {
      // TODO - lazy loader
      $this->bonuses[$bonusId] = new BonusDescriptionList($bonusId);
    }

    $this->bonuses[$bonusId]->addUnit($baseBonusId, $location, $ifNotEmpty);
  }

  /**
   * @param $bonusId
   *
   * @return BonusDescriptionList|null
   */
  public function getBonusDescriptions($bonusId) {
    return array_key_exists($bonusId, $this->bonuses) ? $this->bonuses[$bonusId] : null;
  }

}
