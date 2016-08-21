<?php
/**
 * Created by Gorlum 21.08.2016 13:33
 */

namespace Common;


class Invoker {

  protected $callable;

  public function __construct($callable) {
    $this->callable = $callable;
  }

  public static function build($callable) {
    if(is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
      return new static($callable);
    } else {
      return false;
    }
  }

  public function __invoke() {
    return call_user_func_array($this->callable, func_get_args());
  }

}
