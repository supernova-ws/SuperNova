<?php
/**
 * Created by Gorlum 18.03.2018 17:53
 */

namespace Core;


use Common\Hooker\Pimp;
use Meta\Economic\BuildDataStatic;
use Que\QueUnitStatic;
use template;

/**
 * Class SnPimp
 *
 * Used to declare commonly known methods to make it easier for IDEs
 *
 * @package Core
 *
 * @method void tpl_render_topnav(array &$user = null, array $planetrow, template $template) - Add some elements to ResourceBar
 * @method array que_unit_make_sql(int $unit_id, array $user, array $planet = [], array $build_data = [], float $unit_level = 0, float $unit_amount = 1, int $build_mode = BUILD_CREATE)
 * @method float getStructuresTimeDivisor(array $user, array $planet, int $unit_id, array $unit_data)
 *
 * @method void|string allyInfoView(callable $c = null) - renders extra elements on Alliance internal main page
 * @method void|string allyInternalMainModel(callable $c = null) - extra model on main Alliance page
 */
class SnPimp extends Pimp {
  const MODE_NORMAL = 0; // Normal mode - call hooker
  const MODE_ADD = 1; // Add mode - syntax sugar - add named function
  const MODE_NAME = 2; // Name mode - syntax sugar - return function name

  protected $mode = self::MODE_NORMAL;
  protected $registerOrder = Pimp::ORDER_AS_IS;

  public function __construct(GlobalContainer $gc) {
    parent::__construct($gc);

    /** @noinspection PhpParamsInspection */
    $this->add()->tpl_render_topnav($t = 'sn_tpl_render_topnav', [], null);
    /** @noinspection PhpParamsInspection */
    $this->add()->que_unit_make_sql([QueUnitStatic::class, 'que_unit_make_sql'], []);
    /** @noinspection PhpParamsInspection */
    $this->add()->getStructuresTimeDivisor([BuildDataStatic::class, 'getStructuresTimeDivisor'], [], 0, []);
  }

  /**
   * Changes pimp mode to "ADD"
   *
   * A bit of syntax sugar
   * Allows constructions like $this->add()->methodName($callable, ...)
   * Helps to maintain uniformity of method names throw the code
   *
   * @return $this
   * @throws \Exception
   */
  public function add($order = Pimp::ORDER_AS_IS) {
    if ($this->mode != static::MODE_NORMAL) {
      throw new \Exception('Pimp::add() - mode already set');
    }

    $this->mode = static::MODE_ADD;
    $this->registerOrder = $order;

    return $this;
  }

  /**
   * Changes pimp mode to "NAME"
   *
   * Calling pimp method in NAME mode will return name of called method
   *
   * A bit of syntax sugar
   * Allows constructions like $this->name()->methodName(...)
   * Helps to maintain uniformity of method names throw the code
   * Used mainly as array indexes
   *
   * @return $this
   * @throws \Exception
   */
  public function name() {
    if ($this->mode != static::MODE_NORMAL) {
      throw new \Exception('Pimp::name() - mode already set');
    }

    $this->mode = static::MODE_NAME;

    return $this;
  }

  /**
   * @param $name
   * @param $arguments
   *
   * @return static|mixed|null
   */
  public function __call($name, $arguments) {
    if ($this->mode == static::MODE_NORMAL) {
      return parent::__call($name, $arguments);
    }

    if ($this->mode == static::MODE_ADD) {
      $this->register($name, reset($arguments), $this->registerOrder);
      $this->mode = static::MODE_NORMAL;
      $this->registerOrder = Pimp::ORDER_AS_IS;

      return $this;
    }

    if ($this->mode == static::MODE_NAME) {
      $this->mode = static::MODE_NORMAL;

      return $name;
    }

    return null;
  }

  public function getHooker($name) {
    return !empty($this->hookers[$name]) ? $this->hookers[$name] : null;
  }

}
