<?php
/**
 * Created by Gorlum 01.12.2017 5:23
 */

namespace Bonus;

/**
 * Class BonusListAtom
 *
 * Lists all bonuses for one value
 *
 * @package Bonus
 */
class BonusListAtom implements \Countable {
  /**
   * @var int $bonusId
   */
  public $bonusId;

  /**
   * [(int)$sourceUnitSnId => (class)BonusAtom]
   *
   * @var BonusAtom[] $bonusAtoms
   */
  protected $bonusAtoms = [];

  /**
   * BonusListAtom constructor.
   *
   * @param int $bonusId
   */
  public function __construct($bonusId) {
    $this->bonusId = $bonusId;
  }

  /**
   * @return BonusAtom[]
   */
  public function getBonusAtoms() {
    return $this->bonusAtoms;
  }

  /**
   * Add bonus from specified unit to current bonus list
   *
   * @param int  $baseBonusId - Unit ID from which base value should be retrieved
   * @param bool $ifBaseNonZero - Bonus should applied only if base value is not empty when true
   */
  public function addUnit($baseBonusId, $ifBaseNonZero = BonusAtom::RETURN_IF_BASE_NOT_ZERO) {
    // ToDo - exception on existing (duplicate) bonus ID?
    $this->bonusAtoms[$baseBonusId] = BonusFactory::build($baseBonusId, $ifBaseNonZero);

    uasort($this->bonusAtoms, [$this, 'bonusSort']);
  }


  protected function bonusSort(BonusAtom $a, BonusAtom $b) {
    static $bonusOrder = [BonusAtom::class, BonusAtomAbility::class, BonusAtomAdd::class, BonusAtomPercent::class, BonusAtomMultiply::class];

    $indexA = (int)array_search(get_class($a), $bonusOrder);
    $indexB = (int)array_search(get_class($b), $bonusOrder);

    return $indexA == $indexB ? 0 : ($indexA > $indexB ? +1 : -1);
  }

  /**
   * Count elements of an object
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   * @since 5.1.0
   */
  public function count() {
    return count($this->bonusAtoms);
  }

}
