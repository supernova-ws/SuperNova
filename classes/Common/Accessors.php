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
   * @var callable[][]
   */
  protected $accessors = array();

  /**
   * Assign accessor to a named variable
   *
   * Different accessors have different signatures - you should look carefully before assigning accessor
   *
   * @param string   $varName
   * @param string   $accessor - type of accessor getter/setter/importer/exporter/etc
   * @param callable $callable
   *
   * @throws \Exception
   */
  public function setAccessor($varName, $accessor, $callable) {
    if (empty($callable)) {
      return;
    } elseif (!is_callable($callable)) {
      throw new \Exception('Error assigning callable in ' . get_called_class() . '::setAccessor()! Callable typed [' . $accessor . '] is not a callable or not accessible in the scope');
    }

    // Converting method array-callable to closure
    if (is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
      $method = new \ReflectionMethod($callable[0], $callable[1]);
      $callable = $method->getClosure($callable[0]);
    }

    $this->accessors[$varName][$accessor] = $callable;
  }

  /**
   * Gets accessor for later use
   *
   * @param string $varName
   * @param string $accessor
   *
   * @return callable|null
   */
  public function getAccessor($varName, $accessor) {
    return isset($this->accessors[$varName][$accessor]) ? $this->accessors[$varName][$accessor] : null;
  }

  /**
   * @param string $varName
   * @param string $accessor
   *
   * @return bool
   */
  public function haveAccessor($varName, $accessor) {
    return isset($this->accessors[$varName][$accessor]);
  }

  /**
   * @param string $varName
   * @param string $accessor
   * @param array  $params
   *
   * @return mixed
   * @throws \Exception
   */
  public function invokeAccessor($varName, $accessor, $params) {
    if (!$this->haveAccessor($varName, $accessor)) {
      throw new \Exception("No [{$accessor}] accessor found for variable [{$varName}] on " . get_called_class() . "::" . __METHOD__);
    }

    return call_user_func_array($this->getAccessor($varName, $accessor), $params);
  }

}
