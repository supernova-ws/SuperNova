<?php
/**
 * Created by Gorlum 18.03.2018 16:31
 */

namespace Common\Hooker;

class Hooker {
  /**
   * @var Pimp $pimp
   */
  protected $pimp;

  /**
   * @var callable[][] $clients
   */
  protected $clients;

  /**
   * Hooker constructor.
   *
   * @param Pimp $pimp
   */
  public function __construct($pimp) {
    $this->pimp = $pimp;
  }

  /**
   * @param callable $callable
   * @param int      $order
   */
  public function addClient($callable, $order = Pimp::ORDER_AS_IS) {
    // TODO - override

    if (empty($this->clients[$order])) {
      $this->clients[$order] = [];

      // Rearranging by order
      asort($this->clients);
    }

    $this->clients[$order][] = $callable;
  }

  /**
   * @param array|mixed $arguments
   *
   * @return null|mixed
   */
  public function serve($arguments) {
    $result = null;

    if (!is_array($arguments)) {
      $arguments = [$arguments];
    }
    // Adding link to $result
    $arguments = array_merge([&$result], $arguments);
    if (!empty($this->clients)) {
      foreach ($this->clients as $order => $callables) {
        foreach ($callables as $callable) {
          if (is_callable($callable)) {
            $result = call_user_func_array($callable, $arguments);
          }
        }
      }
    }

    return $result;
  }

  /**
   * Alias for $this->serve()
   *
   * @return mixed|null
   * @see Hooker::serve()
   */
  public function __invoke() {
    // Why it's returns array of params???? Dirty hack here
    return $this->serve(func_get_args()[0]);
  }

}
