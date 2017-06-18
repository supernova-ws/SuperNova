<?php
/**
 * Created by Gorlum 18.06.2017 13:24
 */

namespace Core;


class Autoloader {

  /**
   * @var string[] $folders
   */
  protected static $folders = [];

  protected static $autoloaderRegistered = false;

  /**
   * @param string $class - Fully-qualified path with namespaces
   */
  public static function autoloader($class) {
    $classFile = str_replace('\\', '/', $class);
    foreach(static::$folders as $folder) {
      $classFullFileName = str_replace('\\', '/', $folder . $classFile) . DOT_PHP_EX;
      if(file_exists($classFullFileName) && is_file($classFullFileName)) {
        require_once($classFullFileName);
        if(method_exists($class, '_constructorStatic')) {
          $class::_constructorStatic();
        }
      }
    }
  }

  /**
   * @param string $absoluteClassRoot - absolute path to root class folder
   */
  public static function register($absoluteClassRoot) {
    if(!static::$autoloaderRegistered) {
      spl_autoload_register(array(__CLASS__, 'autoloader'));
      static::$autoloaderRegistered = true;
    }

    $absoluteClassRoot = str_replace('\\', '/', $absoluteClassRoot);

    if(!($absoluteClassRoot = realpath($absoluteClassRoot))) {
      return;
    }

    $absoluteClassRoot = str_replace('\\', '/', $absoluteClassRoot) . '/';

    if(!isset(static::$folders[$absoluteClassRoot])) {
      static::$folders[$absoluteClassRoot] = $absoluteClassRoot;
    }
  }

  /**
   * @param string $relativeClassRoot - relative path to root class folder from game root (where index.php lies)
   */
  public static function registerRelative($relativeClassRoot) {
    static::register(SN_ROOT_PHYSICAL . $relativeClassRoot);
  }

  public static function reset() {
    static::$folders = [];
  }

}
