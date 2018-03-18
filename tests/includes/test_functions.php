<?php
/**
 * Created by Gorlum 16.07.2017 11:46
 */

/**
 * Call protected/private method of a class.
 *
 * @param object|string $object Instantiated object that we will run method on.
 * @param string        $methodName Method name to call
 * @param array         $parameters Array of parameters to pass into method.
 *
 * @return mixed Method return.
 */
function invokeMethod($object, $methodName, array $parameters = array()) {
  //is_object($object) ? $object = get_class($object) : false;

  $reflection = new ReflectionClass($object);
  $method = $reflection->getMethod($methodName);
  $method->setAccessible(true);

  return $method->invokeArgs(is_object($object) ? $object : null, $parameters);
}

/**
 * getPrivateProperty
 *
 * @param string $className
 * @param string $propertyName
 *
 * @return ReflectionProperty
 */
function getPrivateProperty($className, $propertyName) {
  $reflector = new ReflectionClass($className);
  $property = $reflector->getProperty($propertyName);
  $property->setAccessible(true);

  return $property;
}

function getPrivatePropertyValue($object, $propertyName) {
  return getPrivateProperty(get_class($object), $propertyName)->getValue($object);
}

/**
 * Sets a protected property on a given object via reflection
 *
 * @param object $object - instance in which protected value is being modified
 * @param string $property - property on instance being modified
 * @param mixed  $value - new value of the property being modified
 *
 * @return void
 */
function setProtectedProperty($object, $property, $value) {
  $reflection = new ReflectionClass($object);
  $reflection_property = $reflection->getProperty($property);
  $reflection_property->setAccessible(true);
  $reflection_property->setValue($object, $value);
}
