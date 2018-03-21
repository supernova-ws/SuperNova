<?php
/**
 * Created by Gorlum 19.08.2016 20:35
 */

namespace Common;

/**
 * AccessorsV2 storage
 *
 * V2 will use magic methods - i.e. all accessors name composing would be made in callers
 *
 * TODO - make magic method access to accessors ????????
 *
 * @package Common
 */
class AccessorsV2 implements Interfaces\IContainer {

  /**
   * Array of accessors - getters/setters/etc
   *
   * @var callable[]
   */
  protected $accessors = array();

  /**
   * @var bool[]
   */
  protected $shared = array();

  /**
   * Result of shared function execution
   *
   * @var array
   */
  protected $executed = array();

  /**
   * Assign accessor to a named variable
   *
   * Different accessors have different signatures - you should look carefully before assigning accessor
   *
   * @param string   $functionName
   * @param callable $callable
   *
   * @throws \Exception
   */
  public function __set($functionName, $callable) {
    if (!is_callable($callable)) {
      throw new \Exception(
        'Error assigning callable in '
        . get_called_class()
        . "::set()! Callable labeled [{$functionName}] is not a callable or not accessible in this scope"
      );
    }

    // Converting method array-callable to closure

//    // This commented code require PHP 5.4+ !!!!!!!!!!
//    if (is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
//      $method = new \ReflectionMethod($callable[0], $callable[1]);
//      $callable = $method->getClosure($callable[0]);
//    }
    if ((is_array($callable) || is_string($callable)) && ($invoker = Invoker::build($callable))) {
      $callable = $invoker;
    }

    $this->accessors[$functionName] = $callable;
  }

  /**
   * Made shared function - i.e. function called only once
   *
   * @param $functionName
   * @param $callable
   */
  public function share($functionName, $callable) {
    $this->$functionName = $callable;
    $this->shared[$functionName] = true;
  }

  /**
   * @param string $functionName
   *
   * @return bool
   */
  public function __isset($functionName) {
    return isset($this->accessors[$functionName]);
  }

  public function __unset($functionName) {
    unset($this->accessors[$functionName]);
    unset($this->shared[$functionName]);
    unset($this->executed[$functionName]);
  }

  /**
   * Gets accessor for later use
   *
   * @param string $accessor
   *
   * @param string $varName
   *
   * @return callable|null
   */
  public function __get($functionName) {
    return isset($this->$functionName) ? $this->accessors[$functionName] : null;
  }

  /**
   * @param string $functionName
   * @param array  $params
   *
   * @return mixed
   * @throws \Exception
   */
  public function __call($functionName, $params) {
    if (!isset($this->$functionName)) {
      throw new \Exception("No [{$functionName}] accessor found on " . get_called_class() . "::" . __METHOD__);
    }

    if (!isset($this->shared[$functionName]) || !array_key_exists($functionName, $this->executed)) {
      $this->executed[$functionName] = call_user_func_array($this->accessors[$functionName], $params);
    }

    return $this->executed[$functionName];
  }


  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->accessors);
  }

  /**
   * Clears container contents
   */
  public function clear() {
    $this->accessors = array();
    $this->shared = array();
    $this->executed = array();
  }

}
