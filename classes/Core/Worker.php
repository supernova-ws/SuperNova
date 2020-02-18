<?php
/**
 * Created by Gorlum 18.02.2020 15:23
 */

namespace Core;

class Worker {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var callable[] $workers
   */
  protected $workers = [];

  /**
   * Detaches script from incoming request
   *
   * Calling side is released and script continues it's execution
   */
  public static function detachIncomingRequest() {
    // Some dark magic to terminate incoming connection but still keep run a script

    ob_end_clean();
    ignore_user_abort(true);
    ob_start();
    header("Connection: close");
    header("Content-Length: " . ob_get_length());
    ob_end_flush();
    flush();

  }

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  public function __call($name, $arguments) {
    $result = null;
    if (!empty($this->workers[$name]) && is_callable($this->workers[$name])) {
      $result = call_user_func_array($this->workers[$name], $arguments);
    }

    return $result;
  }

  public function registerWorker($name, $callable) {
    $this->workers[$name] = $callable;

    return $this;
  }

}
