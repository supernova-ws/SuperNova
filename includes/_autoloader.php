<?php

/**
 * Created by Gorlum 11.06.2017 8:38
 */

define('SN_CLASS_ROOT_PHYSICAL', SN_ROOT_PHYSICAL . 'classes/');
spl_autoload_register(function ($class) {
  $class = str_replace('\\', '/', $class);
  if (file_exists(SN_CLASS_ROOT_PHYSICAL . $class . '.php')) {
    require_once SN_CLASS_ROOT_PHYSICAL . $class . '.php';
  } elseif (file_exists(SN_CLASS_ROOT_PHYSICAL . 'UBE/' . $class . '.php')) {
    require_once SN_CLASS_ROOT_PHYSICAL . 'UBE/' . $class . '.php';
  }

  if(class_exists($class, false) && method_exists($class, '_constructorStatic')) {
    $class::_constructorStatic();
  }
});
