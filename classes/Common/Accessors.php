<?php
/**
 * Created by Gorlum 19.08.2016 20:35
 */

namespace Common;

/**
 * Accessors storage
 *
 * TODO - make magic method access to accessors ????????
 *
 * @package Common
 */
class Accessors {

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
   * @param string $accessor
   * @param string $varName
   *
   * @return bool
   */
  public function exists($accessor, $varName) {
    return isset($this->accessors[$accessor . $varName]);
  }

  /**
   * Assign accessor to a named variable
   *
   * Different accessors have different signatures - you should look carefully before assigning accessor
   *
   * @param string   $accessor - type of accessor getter/setter/importer/exporter/etc
   * @param string   $varName
   * @param callable $callable
   *
   * @param bool     $shared
   *
   * @throws \Exception
   */
  public function set($accessor, $varName, $callable, $shared = false) {
    if (empty($callable)) {
      return;
    } elseif (!is_callable($callable)) {
      throw new \Exception('Error assigning callable in ' . get_called_class() . '::set()! Callable typed [' . $accessor . '] is not a callable or not accessible in the scope');
    }

    // Converting method array-callable to closure

//    // This commented code require PHP 5.4+ !!!!!!!!!!
//    if (is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
//      $method = new \ReflectionMethod($callable[0], $callable[1]);
//      $callable = $method->getClosure($callable[0]);
//    }
    if(is_array($callable) && ($invoker = Invoker::build($callable))) {
      $callable = $invoker;
    }

    $functionName = $accessor . $varName;
    $this->accessors[$functionName] = $callable;
    if($shared) {
      $this->shared[$functionName] = true;
    }
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
  public function get($accessor, $varName) {
    return $this->exists($accessor, $varName) ? $this->accessors[$accessor . $varName] : null;
  }

  /**
   * @param string $accessor
   * @param string $varName
   * @param array  $params
   *
   * @return mixed
   * @throws \Exception
   */
  public function execute($accessor, $varName, $params) {
    if (!$this->exists($accessor, $varName)) {
      throw new \Exception("No [{$accessor}] accessor found for variable [{$varName}] on " . get_called_class() . "::" . __METHOD__);
    }

    $functionName = $accessor . $varName;
    if(!isset($this->shared[$functionName]) || !array_key_exists($functionName, $this->executed)) {
      $this->executed[$functionName] = call_user_func_array($this->accessors[$functionName], $params);
    }

    return $this->executed[$functionName];
  }

}
