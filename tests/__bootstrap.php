<?php

define('INSIDE', true);

// Эти три строки должны быть В ЭТОМ ФАЙЛЕ, ПО ЭТОМУ ПУТИ и ПЕРЕД ЭТИМ ИНКЛЮДОМ!!!
$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('tests/__bootstrap.php', '', $sn_root_physical);
define('SN_ROOT_PHYSICAL', $sn_root_physical);
// define('SN_ROOT_PHYSICAL_STR_LEN', mb_strlen($sn_root_physical));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen($sn_root_physical));
global $phpbb_root_path;
$phpbb_root_path = SN_ROOT_PHYSICAL; // Это нужно для работы PTL

require_once SN_ROOT_PHYSICAL . 'includes/constants.php';

// echo 'bootstrap';
//print($sn_root_physical);

empty($classRoot) ? $classRoot = SN_ROOT_PHYSICAL . 'includes/classes/' : false;
spl_autoload_register(function ($class) use ($classRoot) {
  $class = str_replace('\\', '/', $class);
  if (file_exists($classRoot . $class . '.php')) {
    require_once $classRoot . $class . '.php';
  } elseif (file_exists($classRoot . 'UBE/' . $class . '.php')) {
    require_once $classRoot . 'UBE/' . $class . '.php';
  }
});

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

  return $method->invokeArgs($object, $parameters);
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