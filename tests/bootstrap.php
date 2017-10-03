<?php

define('INSIDE', true);

define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());

define('SN_ROOT_PHYSICAL', str_replace(array('\\', '//'), '/', dirname(__DIR__) . '/'));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen(SN_ROOT_PHYSICAL)); // mb_strlen ???

require_once __DIR__ . '/includes/test_constants.php';
require_once __DIR__ . '/includes/test_functions.php';

require_once SN_ROOT_PHYSICAL . 'includes/constants.php';
require_once SN_ROOT_PHYSICAL . 'includes/general.php';

empty($classRoot) ? $classRoot = SN_ROOT_PHYSICAL . 'classes/' : false;
spl_autoload_register(function ($class) use ($classRoot) {
  $class = str_replace('\\', '/', $class);
  if (file_exists($classRoot . $class . '.php')) {
    require_once $classRoot . $class . '.php';
  } elseif (file_exists($classRoot . 'UBE/' . $class . '.php')) {
    require_once $classRoot . 'UBE/' . $class . '.php';
  } elseif (file_exists(SN_ROOT_PHYSICAL . 'tests/' . $class . '.php')) {
    require_once SN_ROOT_PHYSICAL . 'tests/' . $class . '.php';
  }

  if(class_exists($class, false) && method_exists($class, '_constructorStatic')) {
    $class::_constructorStatic();
  }
});
