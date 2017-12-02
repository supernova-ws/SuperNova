<?php
/**
 * Created by Gorlum 01.12.2017 5:23
 */

namespace Bonus;

use \classSupernova;

/**
 * Class BonusListAtom
 *
 * Describes all bonuses for one value
 *
 * @package Bonus
 */
class BonusListAtom {

  /**
   * @var int $bonusId
   */
  public $bonusId;

  /**
   * [(int)$sourceUnitSnId] => (class)BonusAtom
   *
   * @var BonusAtom[] $bonusDescriptions
   */
  protected $bonusDescriptions = [];

  /**
   * BonusListAtom constructor.
   *
   * @param int $bonusId
   */
  public function __construct($bonusId) {
    $this->bonusId = $bonusId;
  }

  /**
   * Add bonus from specified unit to current bonus list
   *
   * @param int  $baseBonusId - Unit ID from which base value should be retrieved
   * @param bool $ifNotEmpty - Bonus should applied only if base value is not empty when true
   */
  public function addUnit($baseBonusId, $ifNotEmpty = BonusCatalog::VALUE_NON_ZERO) {
    // ToDo - exception on existing (duplicate) bonus ID?
    $this->bonusDescriptions[$baseBonusId] = BonusFactory::build($baseBonusId, $ifNotEmpty);
  }

  /**
   * Calculates real bonus values within supplied context
   *
   * @param ValueBonused $value
   *
   * @return array
   */
  public function apply(ValueBonused $value) {
    uasort($this->bonusDescriptions, [$this, 'bonusSort']);

    $result = [BONUS_NONE => $value->base];
    foreach ($this->bonusDescriptions as $unitId => $description) {
      $amount = classSupernova::$gc->valueStorage->getValue($unitId, $value->context);

      $result[$unitId] = $description->adjustValue($amount, $value);
    }

// TODO - проследить, что бы ниже не было отрицательных значений
//            $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
//            $value += $base_value * $mercenary_level * $mercenary_bonus / 100;

    return $result;
  }

  protected function bonusSort(BonusAtom $a, BonusAtom $b) {
    static $bonusOrder = [BonusAtom::class, BonusAtomAbility::class, BonusAtomAdd::class, BonusAtomPercent::class, BonusAtomMultiply::class];
    $indexA = (int)array_search($a, $bonusOrder);
    $indexB = (int)array_search($b, $bonusOrder);

    return $indexA == $indexB ? 0 : ($indexA > $indexB ? +1 : -1);
  }

}
