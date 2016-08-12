<?php

namespace Common;

use Exception;

/**
 * Interface IContainerAccessors
 *
 * Adds accessors support to IMagicAccess
 *
 * @package Common
 */
interface IContainerAccessors extends IMagicAccess {

  /**
   * Direct access to parent class setter
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed
   */
  public function setDirect($name, $value);

  /**
   * Direct access to parent class getter
   *
   * @param string $name
   *
   * @return mixed
   */
  public function getDirect($name);

  /**
   * Is container have no data
   *
   * @return bool
   */
  public function isEmpty();

  /**
   * Assign accessor to a named variable
   *
   * Different accessors have different signatures - you should look carefully before assigning accessor
   *
   * @param string   $varName
   * @param string   $type - getter/setter/importer/exporter/etc
   * @param callable $callable
   *
   * @throws Exception
   */
  public function assignAccessor($varName, $type, $callable);

}
