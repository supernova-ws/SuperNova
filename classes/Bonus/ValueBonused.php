<?php
/**
 * Created by Gorlum 01.12.2017 8:34
 */

namespace Bonus;


class ValueBonused {

  public $base = 0;
  public $value = 0;
  public $snId = 0;
  public $context = [];

  /**
   * [(int BONUS_xxx)bonusType][(int)$sourceUnitSnId] => (float)bonusValue
   *
   * @var float[][] $bonusValues
   */
  public $bonusValues = [];

  /**
   * @var BonusCatalog $bonusCatalog
   */
  protected $bonusCatalog;

  /**
   * ValueBonused constructor.
   *
   * @param int       $unitSnId
   * @param int|float $baseValue
   * @param array     $context
   */
  public function __construct($unitSnId, $baseValue, $context = []) {
    $this->base = $baseValue;
    $this->value = $this->base;
    $this->snId = $unitSnId;

    $this->bonusCatalog = \classSupernova::$gc->bonusCatalog;
  }

  public function calc($context = []) {
    // Context can differ. However - it shouldn't
    $this->context = $context;
    $this->value = $this->base;
    $this->bonusValues = [];

    $bonuses = $this->bonusCatalog->getBonusDescriptions($this->snId);
    if(!$bonuses) {
      return $this->base;
    }

    $this->bonusValues = $bonuses->calcBonusValues($this);

    // Summing up those bonuses which can be summarized
    $justSum = $this->bonusValues;
    unset($justSum[BONUS_MULTIPLY]);
    $this->value = 0;
    foreach($justSum as $bonusList) {
      $this->value += array_reduce($bonusList, function($result, $bonus) {return $result + $bonus;}, 0);
    }

    // Multiplying summarized value by multiplication bonuses
    if(!empty($this->bonusValues[BONUS_MULTIPLY])) {
      foreach($this->bonusValues[BONUS_MULTIPLY] as $multiplier) {
        $this->value *= $multiplier;
      }
    }

    return $this->value;
  }

}
