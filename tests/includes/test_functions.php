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

  $reflection = new \ReflectionClass($object);
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
