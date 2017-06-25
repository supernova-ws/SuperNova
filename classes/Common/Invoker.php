<?php
/**
 * Created by Gorlum 21.08.2016 13:33
 */

namespace Common;

/**
 * Class Invoker
 *
 * Invoker incapsulates callable until PHP 5.4+
 *
 * Supports method callable (2 element array), function callable (string) and lambda-functions
 *
 * @package Common
 * @deprecated
 */

class Invoker {

  protected $callable;

  /**
   * Invoker constructor.
   *
   * @param callable|null $callable
   */
  public function __construct($callable) {
    $this->callable = $callable;
  }

  /**
   * @param mixed $callable
   *
   * @return static
   */
  public static function build($callable) {
    if (is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
      return new static($callable);
    } elseif (is_string($callable) && function_exists($callable)) {
      return new static($callable);
    } elseif (is_callable($callable)) {
      return new static($callable);
    } else {
      return new static(null);
    }
  }

  public function __invoke() {
    return is_callable($this->callable) ? call_user_func_array($this->callable, func_get_args()) : null;
  }

}
