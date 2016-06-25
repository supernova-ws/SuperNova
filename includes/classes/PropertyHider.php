<?php

/**
 * Class PropertyHider
 *
 * Hides properties from visibility
 * Implements new set of methods adjXXX
 * - adjXXX method returns value of property adjusted by $diff
 */
class PropertyHider extends stdClass {

  /**
   * Property list
   *
   * @var array[]
   */
  protected static $_properties = array();

  /**
   * List of property names that was changed since last DB operation
   *
   * @var boolean[]
   */
  protected $propertiesChanged = array();
  /**
   * List of property names->$delta that was adjusted since last DB operation - and then need to be processed as Deltas
   *
   * @var array
   */
  protected $propertiesAdjusted = array();

  //+
  /**
   * @param array $properties
   */
  public static function setProperties($properties) {
    // TODO - reset internals??
    static::$_properties = $properties;
  }

  //+
  public static function getProperties() {
    return static::$_properties;
  }

  //+
  /**
   * PropertyHider constructor.
   */
  public function __construct() {
  }

  //+
  protected function checkPropertyExists($name) {
    if (!array_key_exists($name, static::$_properties)) {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] not exists', ERR_ERROR);
    }
  }

  //+
  protected function checkOverwriteAdjusted($name) {
    if (array_key_exists($name, $this->propertiesAdjusted)) {
      throw new PropertyAccessException('Property [' . get_called_class() . '::' . $name . '] already was adjusted so no SET is possible until dbSave', ERR_ERROR);
    }
  }

  //+
  private function getPhysicalPropertyName($name) {
    return '_' . $name;
  }

  //+
  protected function isPropertyDeclared($name) {
    return property_exists($this, $this->getPhysicalPropertyName($name));
  }

  //+
  /**
   * Getter with support of protected methods
   *
   * @param $name
   *
   * @return mixed
   * @throws ExceptionPropertyNotExists
   */
  public function __get($name) {
    $this->checkPropertyExists($name);

    $result = null;
    // Now deciding - will we call a protected setter or will we work with protected property
    if (method_exists($this, $methodName = 'get' . ucfirst($name))) {
      // If method exists - just calling it
      $result = call_user_func_array(array($this, $methodName), array());
    } elseif ($this->isPropertyDeclared($name)) {
      // No getter exists - works directly with protected property
      $result = $this->{$this->getPhysicalPropertyName($name)};
    } else {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] does not have getter/property to get', ERR_ERROR);
    }

    return $result;
  }

  //+
  /**
   * Unsafe setter - w/o checking if the property was already adjusted
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed|null
   * @throws ExceptionPropertyNotExists
   */
  protected function _setUnsafe($name, $value) {
    $result = null;
    // Now deciding - will we call a protected setter or will we work with protected property
    if (method_exists($this, $methodName = 'set' . ucfirst($name))) {
      // If method exists - just calling it
      // TODO - should return TRUE if value changed or FALSE otherwise
      $result = call_user_func_array(array($this, $methodName), array($value));
    } elseif ($this->isPropertyDeclared($name)) {
      // No setter exists - works directly with protected property
//      if($result = $this->$propertyName !== $value) {
      $this->{$this->getPhysicalPropertyName($name)} = $value;
//      }
    } else {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] does not have setter/property to set', ERR_ERROR);
    }

    // TODO - should be primed only if value changed
//    if($result) {
    $this->propertiesChanged[$name] = true;

//    }

    return $result;
  }

  /**
   * Setter wrapper with support of protected properties/methods
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed|null
   * @throws ExceptionPropertyNotExists
   */
  // TODO - сеттер должен параллельно изменять значение db_row - for now...
  // TODO - Проверка, а действительно ли данные были изменены?? Понадобится определение типов - разные типы сравниваются по-разному
  public function __set($name, $value) {
    $this->checkPropertyExists($name);
    $this->checkOverwriteAdjusted($name);

    return $this->_setUnsafe($name, $value);
  }


  /**
   * @param string $name
   * @param int    $diff
   *
   * @return int
   */
  protected function _adjustValueInteger($name, $diff) {
    return intval($this->$name) + intval($diff);
  }

  /**
   * @param string $name
   * @param float  $diff
   *
   * @return float
   */
  protected function _adjustValueDouble($name, $diff) {
    return floatval($this->$name) + floatval($diff);
  }

  /**
   * @param string $name
   * @param string $diff
   *
   * @return string
   */
  protected function _adjustValueString($name, $diff) {
    return (string)$this->$name . (string)$diff;
  }

  /**
   * @param string $name
   * @param array $diff
   *
   * @return array
   */
  protected function _adjustValueArray($name, $diff) {
    $copy = (array)$this->$name;
    HelperArray::merge($copy, (array)$diff, HelperArray::MERGE_PHP);
    return $copy;
  }

  /**
   * @param string $name
   * @param int    $diff
   *
   * @return int
   */
  protected function _adjustValueIntegerDiff($name, $diff) {
    return (int)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, 0) + (int)$diff;
  }

  /**
   * @param string $name
   * @param float  $diff
   *
   * @return float
   */
  protected function _adjustValueDoubleDiff($name, $diff) {
    return (float)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, 0.0) + (float)$diff;
  }

  /**
   * @param string $name
   * @param string $diff
   *
   * @return string
   */
  protected function _adjustValueStringDiff($name, $diff) {
    return (string)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, '') . (string)$diff;
  }

  /**
   * @param string $name
   * @param array $diff
   *
   * @return array
   */
  protected function _adjustValueArrayDiff($name, $diff) {
    $copy = (array)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, array());
    HelperArray::merge($copy, $diff, HelperArray::MERGE_PHP);
    return $copy;
  }

  /**
   * Get adjusted value by callback with generated name
   * Support types: "integer", "double", "string", "array"
   * Throws exception on: "boolean", "object", "resource", "NULL", "unknown type",
   *
   * @param string $name
   * @param mixed  $diff
   * @param string $suffix
   *
   * @return mixed
   * @throws ExceptionTypeUnsupported
   */
  protected function propertyMethodResult($name, $diff, $suffix = '') {
    // TODO - property type checks
    // Capitalizing type name
    $type = explode(' ', gettype($this->$name));
    array_walk($type, 'DbSqlHelper::UCFirstByRef');
    $type = implode('', $type);

    if (!method_exists($this, $methodName = '_adjustValue' . $type . $suffix)) {
      throw new ExceptionTypeUnsupported();
    }

    return call_user_func(array($this, $methodName), $name, $diff);
  }

  /**
   * Directly adjusts value without Adjuster
   *
   * @param string $name
   * @param mixed  $diff
   *
   * @return mixed
   */
  protected function _adjustValue($name, $diff) {
    return $this->propertyMethodResult($name, $diff);
  }

  /**
   * Directly adjusts value DIFF without Adjuster
   *
   * @param string $name
   * @param mixed  $diff
   *
   * @return mixed
   */
  protected function _adjustValueDiff($name, $diff) {
    return $this->propertyMethodResult($name, $diff, 'Diff');
  }

  /**
   * Adjust property value with $diff
   * Adjusted values put into DB with UPDATE query
   * Adjuster callback adjXXX() should return new value which will be propagated via __set()
   * Optionally there can be DIFF-adjuster adjXXXDiff() for complex types
   *
   * @param string $name
   * @param mixed  $diff
   *
   * @return mixed
   */
  public function __adjust($name, $diff) {
    $this->checkPropertyExists($name);

    // Now deciding - will we call a protected setter or will we work with protected property
    if (method_exists($this, $methodName = 'adj' . ucfirst($name))) {
      // If method exists - just calling it
      // Method returns new adjusted value
      $newValue = call_user_func_array(array($this, $methodName), array($diff));
    } else {
      // No adjuster exists - works directly with protected property
      // TODO - property type checks
      $newValue = $this->_adjustValue($name, $diff);
    }

    // Invoking property setter
    $this->_setUnsafe($name, $newValue);

    // Initializing value of adjustment
    if (!array_key_exists($name, $this->propertiesAdjusted)) {
      $this->propertiesAdjusted[$name] = null;
    }

    // Adding diff to adjustment accumulator
    if (method_exists($this, $methodName = 'adj' . ucfirst($name) . 'Diff')) {
      call_user_func_array(array($this, $methodName), array($diff));
    } else {
      // TODO - property type checks
      $this->propertiesAdjusted[$name] = $this->_adjustValueDiff($name, $diff);
    }

    return $this->$name;
  }

}
