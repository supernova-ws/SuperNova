<?php
/**
 * Created by Gorlum 18.03.2018 16:27
 */

namespace Common\Hooker;

use Core\GlobalContainer;

/**
 * Class Pimp
 *
 * Pimp is a hooker manager
 *
 * @package Common\Hooker
 */
class Pimp {
  const ORDER_REPLACE = -PHP_INT_MAX + 1; // Replaces current callback (?)
  const ORDER_FIRST = self::ORDER_REPLACE + 1;
  const ORDER_AS_IS = 0;
  const ORDER_LAST = PHP_INT_MAX;

  /**
   * @var \Core\GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var Hooker[] $hookers
   */
  protected $hookers = [];

  /**
   * Pimp constructor.
   *
   * @param \Core\GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->gc = $gc;
  }

  /**
   * @param string   $hookName
   * @param callable $callable
   * @param int      $order
   */
  public function register($hookName, $callable, $order = Pimp::ORDER_AS_IS) {
    if (empty($this->hookers[$hookName])) {
      $this->hookers[$hookName] = new Hooker($this);
    }

    $this->hookers[$hookName]->addClient($callable, $order);
  }

  /**
   * @param $name
   * @param $arguments
   *
   * @return mixed|null
   */
  public function __call($name, $arguments) {
    return
      !empty($this->hookers[$name])
        ? $this->hookers[$name]->serve($arguments)
        : null;
  }

}
