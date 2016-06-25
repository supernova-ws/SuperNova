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
   * @var string[]
   */
  protected $propertiesChanged = array();
  /**
   * List of property names->$delta that was adjusted since last DB operation - and then need to be processed as Deltas
   *
   * @var string[]
   */
  protected $propertiesAdjusted = array();

  /**
   * @param array $properties
   */
  public static function setProperties($properties) {
    // TODO - reset internals??
    static::$_properties = $properties;
  }

  public static function getProperties() {
    return static::$_properties;
  }

  /**
   * PropertyHider constructor.
   */
  public function __construct() {
  }

//  protected function checkForGetSet($name, $left3) {
//    // If method is not getter or setter OR property name not exists in $_properties - raising exception
//    // Descendants can catch this Exception to make own __call magic
//    if ($left3 != 'get' && $left3 != 'set') {
//      throw new ExceptionNotGetterOrSetter('Magic call is not getter or setter ' . get_called_class() . '::' . $name, ERR_ERROR);
//    }
//  }
//
//  protected function checkForPropertyExists($name, $propertyName) {
//    if (empty(static::$_properties[$propertyName])) {
//      throw new ExceptionPropertyNotExists('Property ' . $propertyName . ' not exists when calling getter/setter ' . get_called_class() . '::' . $name, ERR_ERROR);
//    }
//  }

//  protected function checkForDoubleAdjust($name, $left3, $propertyName) {
//    if ($left3 == 'set' && array_key_exists($propertyName, $this->propertiesAdjusted)) {
//      throw new PropertyAccessException('Property ' . $propertyName . ' already was adjusted so no SET is possible until dbSave in ' . get_called_class() . '::' . $name, ERR_ERROR);
//    }
//  }

//  /**
//   * Handles getters and setters
//   *
//   * @param string $name
//   * @param array  $arguments
//   *
//   * @return mixed
//   * @throws ExceptionNotGetterOrSetter
//   * @throws ExceptionPropertyNotExists
//   * @throws PropertyAccessException
//   */
//  protected function callGetSet($name, $arguments) {
//    $this->checkForGetSet($name, $left3 = substr($name, 0, 3));
//    $this->checkForPropertyExists($name, $propertyName = lcfirst(substr($name, 3)));
//    // TODO check for read-only
//    $this->checkForDoubleAdjust($name, $left3, $propertyName);
//
//    $result = null;
//
//    // Now deciding - will we call a protected setter or will we work with protected property
//    if (method_exists($this, $name)) {
//      // If method exists - just calling it
//      $result = call_user_func_array(array($this, $name), $arguments);
//    } else {
//      // No getter/setter exists - works directly with protected property
//      if ($left3 === 'set') {
//        $this->{'_' . $propertyName} = $arguments[0];
//      } elseif ($left3 === 'get') {
//        $result = $this->{'_' . $propertyName};
//      }
//    }
//
//    if ($left3 === 'set') {
//      $this->propertiesChanged[$propertyName] = true;
//    }
//
//    return $result;
//  }

//  public function __call($name, $arguments) {
//    $this->callGetSet($name, $arguments);
//  }

  protected function checkPropertyExists($name) {
    if (!array_key_exists($name, static::$_properties)) {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] not exists when accessing via __get/__set', ERR_ERROR);
    }
  }

  protected function checkOverwriteAdjusted($name) {
    if (array_key_exists($name, $this->propertiesAdjusted)) {
      throw new PropertyAccessException('Property [' . get_called_class() . '::' . $name . '] already was adjusted so no SET is possible until dbSave', ERR_ERROR);
    }
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

    $result = null;
    // Now deciding - will we call a protected setter or will we work with protected property
    if (method_exists($this, $methodName = 'get' . ucfirst($name))) {
      // If method exists - just calling it
      $result = call_user_func_array(array($this, $methodName), array());
    } elseif (property_exists($this, $propertyName = '_' . $name)) {
      // No getter exists - works directly with protected property
      $result = $this->$propertyName;
    } else {
      throw new ExceptionPropertyNotExists('Property [' . get_called_class() . '::' . $name . '] does not have getter/property to get', ERR_ERROR);
    }

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

    $result = null;
    // Now deciding - will we call a protected setter or will we work with protected property
    if (method_exists($this, $methodName = 'set' . ucfirst($name))) {
      // If method exists - just calling it
      // TODO - should return TRUE if value changed or FALSE otherwise
      $result = call_user_func_array(array($this, $methodName), array($value));
    } elseif (property_exists($this, $propertyName = '_' . $name)) {
      // No setter exists - works directly with protected property
//      if($result = $this->$propertyName !== $value) {
        $this->$propertyName = $value;
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
   * Adjust property value with $diff
   * Adjusted values put into DB with UPDATE query
   * Adjuster callback adjXXX() should return new value which will be propagated via ___set()
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
      $newValue = $this->$name + $diff;
    }

    // Invoking property setter
    $this->$name = $newValue;
//    $this->___set($name, $newValue);

    // Initializing value of adjustment
    if (!array_key_exists($name, $this->propertiesAdjusted)) {
      $this->propertiesAdjusted[$name] = null;
    }

    // Adding diff to adjustment accumulator
    if (method_exists($this, $methodName = 'adj' . ucfirst($name) . 'Diff')) {
      call_user_func_array(array($this, $methodName), array($diff));
    } else {
      // TODO - property type checks
      $this->propertiesAdjusted[$name] += $diff;
    }

    return $this->$name;
  }

}
