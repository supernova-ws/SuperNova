<?php

// Эти три строки должны быть В ЭТОМ ФАЙЛЕ, ПО ЭТОМУ ПУТИ и ПЕРЕД ЭТИМ ИНКЛЮДОМ!!!
$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('tests/__bootstrap.php', '', $sn_root_physical);
define('SN_ROOT_PHYSICAL', $sn_root_physical);
// define('SN_ROOT_PHYSICAL_STR_LEN', mb_strlen($sn_root_physical));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen($sn_root_physical));
$phpbb_root_path = SN_ROOT_PHYSICAL; // Это нужно для работы PTL


// echo 'bootstrap';
//print($sn_root_physical);

spl_autoload_register(function ($class) {
  if (file_exists(SN_ROOT_PHYSICAL . 'includes/classes/' . $class . '.php')) {
    require_once SN_ROOT_PHYSICAL . 'includes/classes/' . $class . '.php';
  }
});

spl_autoload_register(function ($class) {
  if (file_exists(SN_ROOT_PHYSICAL . 'includes/classes/UBE/' . $class . '.php')) {
    require_once SN_ROOT_PHYSICAL . 'includes/classes/UBE/' . $class . '.php';
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
