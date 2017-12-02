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
   * @var BonusListAtom $bonusDescription
   */
  public $bonusDescription;

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

    $this->bonusDescription = $this->bonusCatalog->getBonusDescriptions($this->snId);
    if(!$this->bonusDescription) {
      return $this->base;
    }

    $this->bonusValues = $this->bonusDescription->apply($this);

    return $this->value;
  }

}
