<?php
/**
 * Created by Gorlum 17.02.2018 23:55
 */

namespace Common;

/**
 * Class OutcomeManager
 *
 * Manage outcomes for a bunch of random cases
 *
 * @package Common
 */
class OutcomeManager implements \Countable {

  /**
   * Possible outcomes
   *
   * @var array $outcomes
   */
  protected $outcomes = [];

  /**
   * Chances for outcome to roll
   *
   * @var int[] $chances
   */
  protected $chances = [];


  public function __construct() {
  }

  /**
   * Roll element from predefined array
   *
   * @param iterable|array $iterable - [P_CHANCE => (int), ...(payload)]
   *
   * @return mixed|null
   */
  public static function rollArray($iterable) {
    if (!is_iterable($iterable) || empty($iterable)) {
      return null;
    }

    $manager = new static();

    foreach ($iterable as $element) {
      if (!is_array($element) || empty($element[P_CHANCE])) {
        continue;
      }
      $manager->add($element, $element[P_CHANCE]);
    }

    $result = $manager->rollOutcome();
    unset($manager);

    return $result;
  }

  /**
   * Adds outcome to internal array
   *
   * @param mixed $outcome - Outcome which can be selected
   * @param int   $chance - Chance of this particular outcome. Should be integer. Sum of all chances can be above mt_getrandmax()
   */
  public function add($outcome, $chance) {
    $this->outcomes[] = $outcome;
    $this->chances[] = $chance;
  }

  /**
   * Removed outcome from list
   *
   * @param mixed     $outcome - Outcome to be removed
   * @param bool|null $strict - Strict search flag
   */
  public function remove($outcome, $strict = null) {
    if (($index = array_search($outcome, $this->outcomes, $strict)) !== false) {
      unset($this->outcomes[$index]);
      unset($this->chances[$index]);
    }
  }


  /**
   * Get max range of all current outcomes to use as maximum in mt_rand()
   *
   * @return float|int
   */
  public function getMaxRange() {
    return array_sum($this->chances);
  }

  /**
   * Get outcome by rolled chance
   *
   * @param int $rolled - Rolled chance
   *
   * @return mixed|null - (null) if no outcomes
   */
  public function getOutcome($rolled) {
    foreach ($this->chances as $index => $chance) {
      if ($rolled <= $chance) {
        break;
      }
      $rolled -= $chance;
    }

    return array_key_exists($index, $this->outcomes) ? $this->outcomes[$index] : null;
  }

  /**
   * All-in-one function
   *
   * Code can use this function if it don't want to know what number was rolled
   *
   * @return mixed|null
   */
  public function rollOutcome() {
    return $this->getOutcome(mt_rand(1, $this->getMaxRange()));
  }

  /**
   * Count elements of an object
   * @link  http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   * @since 5.1.0
   */
  public function count() {
    return count($this->outcomes);
  }

}
