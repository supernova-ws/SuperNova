<?php
/**
 * Created by Gorlum 01.12.2017 8:34
 */

namespace Bonus;

use \SN;

class ValueBonused {

  public $base = 0;
  public $value = 0;
  public $snId = 0;
  public $context = [];


  /**
   * @var BonusListAtom $bonusList
   */
  protected $bonusList;

  /**
   * [(int)$sourceUnitSnId] => (float)bonusValue
   *
   * @var float[] $bonusValues
   */
  public $bonusValues = [];

  /**
   * @var BonusCatalog $bonusCatalog
   */
  protected $bonusCatalog;

  protected $calculated = false;

  /**
   * ValueBonused constructor.
   *
   * @param int       $unitSnId
   * @param int|float $baseValue
   * @param array     $context
   */
  public function __construct($unitSnId, $baseValue, $context = []) {
    $this->snId = $unitSnId;

    $this->base = $baseValue;
    $this->value = $this->base;
    $this->bonusValues = [];

    $this->bonusCatalog = \SN::$gc->bonusCatalog;
  }


  /**
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   *
   * @return float|int
   */
  public function getValue($context = []) {
    // Context can differ. However - it shouldn't
    if ($this->calculated && $this->context == $context) {
      return $this->value;
    }


    $this->context = $context;
    $this->value = $this->base;
    $this->bonusValues = [];
    $this->calculated = true;

    $this->bonusList = $this->bonusCatalog->getBonusListAtom($this->snId);
    if (!$this->bonusList instanceof BonusListAtom) {
      return $this->base;
    }

    $this->applyBonuses($context);

    return $this->value;
  }

  /**
   * Calculates real bonus values within supplied context
   *
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   */
  protected function applyBonuses($context = []) {
    $this->bonusValues = [BONUS_NONE => $this->base];
    if (!$this->bonusList instanceof BonusListAtom) {
      return;
    }

    foreach ($this->bonusList->getBonusAtoms() as $unitId => $bonusAtom) {
      $amount = SN::$gc->valueStorage->getValue($unitId, $context);

      $this->bonusValues[$unitId] = $bonusAtom->adjustValue($this->value, $amount, $this->base);
    }

// TODO - проследить, что бы ниже не было отрицательных значений
//            $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
//            $value += $base_value * $mercenary_level * $mercenary_bonus / 100;
  }

}
