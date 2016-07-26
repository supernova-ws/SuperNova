<?php

/**
 * Class PropertyHider - Hides properties from visibility
 *
 * - Property access - via property names
 * - Allowed property name is an index in static $_properties() array
 * - Property XXX can have 4 accessories:
 *    - getXXX - getter
 *    - setXXX - setter. Setting property is logging in $propertiesChanged array
 *    - adjustXXX - adjusts value. Delta from original value is holding in $propertiesAdjusted array
 *    - deltaXXX - calculates delta from original value
 * - Accessories can have any visibility
 * - Public accessories should take care setting elements of $propertiesChanged/$propertiesAdjusted arrays if needed
 *
 *
 * Implements new set of methods adjXXX
 * - adjXXX method returns value of property adjusted by $diff
 */
abstract class PropertyHider extends stdClass {

  /**
   * Getting value
   */
  const ACTION_GET = 'get';
  /**
   * Setting value
   */
  const ACTION_SET = 'set';
  /**
   * Adjusting value
   */
  const ACTION_ADJUST = 'adjust';
  /**
   * Calculating value
   */
  const ACTION_DELTA = 'delta';

  /**
   * Property list
   *
   * @var array[]
   */
  protected $_properties = array();

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

  /**
   * @param array $properties
   */
  public function setProperties($properties) {
    // TODO - reset internals??
    $this->_properties = $properties;
  }

  public function getProperties() {
    return $this->_properties;
  }

  /**
   * PropertyHider constructor.
   */
  public function __construct() {
  }

  protected function checkPropertyExists($name) {
    if (!array_key_exists($name, $this->_properties)) {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] not exists', ERR_ERROR);
    }
  }

  protected function checkOverwriteAdjusted($name) {
    if (array_key_exists($name, $this->propertiesAdjusted)) {
      throw new PropertyAccessException('Property [' . get_called_class() . '::' . $name . '] already was adjusted so no SET is possible until dbSave', ERR_ERROR);
    }
  }

  /**
   * Method checks if action is available for named property
   *
   * @param string $name
   * @param string $action
   *
   * @return bool
   */
  abstract protected function isPropertyActionAvailable($name, $action = '');
  /**
   * Internal method that make real changes to property value
   * May be override in child class
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed
   */
  abstract protected function setProperty($name, $value);
  /**
   * Internal method that make really reads property value
   * May be override in child class
   *
   * @param string $name
   * @param mixed  $value - ignored. Used for compatibility
   *
   * @return mixed
   */
  abstract protected function getProperty($name, $value = null);
  /**
   * Magic method that checks if named property is set
   * May be override in child class
   *
   * @param $name
   *
   * @return bool
   */
  abstract public function __isset($name);

  /**
   * Directly adjusts value without Adjuster
   *
   * @param string $name
   * @param mixed  $diff
   *
   * @return mixed
   */
  protected function adjustProperty($name, $diff) {
    return $this->propertyMethodResult($name, $diff, 'adjustProperty');
  }

  /**
   * Directly adjusts value Delta for properties without Adjuster
   *
   * @param string $name
   * @param mixed  $diff
   *
   * @return mixed
   */
  protected function deltaProperty($name, $diff) {
    return $this->propertyMethodResult($name, $diff, 'delta');
  }

  /**
   * Performs '$action' on property with $name - possible with $value
   *
   * Universal method for call getter, setter, adjuster or delta calculator
   *
   * @param string $action
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed|null
   * @throws ExceptionPropertyNotExists
   */
  protected function actionProperty($action, $name, $value) {
    $result = null;
    // Now deciding - will we call a protected setter or will we work with protected property
    // Todo - on init recalc all method_exists
    if (method_exists($this, $methodName = $action . ucfirst($name))) {
      // If method exists - just calling it
      // TODO - should return TRUE if value changed or FALSE otherwise
      $result = call_user_func_array(array($this, $methodName), array($value));
    } elseif ($this->isPropertyActionAvailable($name, $action)) {
      // No setter exists - works directly with protected property
      $result = $this->{$action . 'Property'}($name, $value);
    } else {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] does not have ' . $action . 'ter/property to ' . $action, ERR_ERROR);
    }

    return $result;
  }

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

    $result = $this->actionProperty('get', $name, null);

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
    $result = $this->actionProperty('set', $name, $value);

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
    $this->checkOverwriteAdjusted($name);
    $this->checkPropertyExists($name);

    return $this->_setUnsafe($name, $value);
  }


  /**
   * @param string $name
   * @param int    $diff
   *
   * @return int
   */
  protected function adjustPropertyInteger($name, $diff) {
    return intval($this->$name) + intval($diff);
  }

  /**
   * @param string $name
   * @param float  $diff
   *
   * @return float
   */
  protected function adjustPropertyDouble($name, $diff) {
    return floatval($this->$name) + floatval($diff);
  }

  /**
   * @param string $name
   * @param string $diff
   *
   * @return string
   */
  protected function adjustPropertyString($name, $diff) {
    return (string)$this->$name . (string)$diff;
  }

  /**
   * @param string $name
   * @param array  $diff
   *
   * @return array
   */
  protected function adjustPropertyArray($name, $diff) {
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
  protected function deltaInteger($name, $diff) {
    return (int)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, 0) + (int)$diff;
  }

  /**
   * @param string $name
   * @param float  $diff
   *
   * @return float
   */
  protected function deltaDouble($name, $diff) {
    return (float)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, 0.0) + (float)$diff;
  }

  /**
   * @param string $name
   * @param string $diff
   *
   * @return string
   */
  protected function deltaString($name, $diff) {
    return (string)HelperArray::keyExistsOr($this->propertiesAdjusted, $name, '') . (string)$diff;
  }

  /**
   * @param string $name
   * @param array  $diff
   *
   * @return array
   */
  protected function deltaArray($name, $diff) {
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
   * @param string $prefix
   *
   * @return mixed
   * @throws ExceptionTypeUnsupported
   */
  protected function propertyMethodResult($name, $diff, $prefix = '') {
    $type = gettype($this->$name);
    // Capitalizing type name
    $methodName = explode(' ', $type);
    array_walk($methodName, 'DbSqlHelper::UCFirstByRef');
    $methodName = $prefix . implode('', $methodName);

    if (!method_exists($this, $methodName)) {
      throw new ExceptionTypeUnsupported('Type "' . $type . '" is unsupported in PropertyHider::propertyMethodResult');
    }

    return call_user_func(array($this, $methodName), $name, $diff);
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

    $result = $this->actionProperty('adjust', $name, $diff);

    // Invoking property setter
    $this->_setUnsafe($name, $result);

    // Initializing value of adjustment
    if (!array_key_exists($name, $this->propertiesAdjusted)) {
      $this->propertiesAdjusted[$name] = null;
    }

    $this->propertiesAdjusted[$name] = $this->actionProperty('delta', $name, $diff);

    return $this->$name;
  }

}
