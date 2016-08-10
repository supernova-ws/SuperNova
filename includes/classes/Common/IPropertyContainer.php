<?php

namespace Common;

use Exception;

interface IPropertyContainer extends IMagicAccess {
  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty();

  /**
   * Set properties data from external source
   *
   * 'propertyName' => array(
   *    P_DB_FIELD => 'fieldNameInDb',
   * )
   *
   * @param array $properties
   */
  public function setProperties($properties);

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


  /**
   * Clears only properties which declared in $properties array
   */
  public function clearProperties();
}
