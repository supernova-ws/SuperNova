<?php
/**
 * Created by Gorlum 18.06.2017 13:24
 */

namespace Core;

/**
 * Class Autoloader
 *
 * One of core class to supply autoload facilities to the engine
 *
 * @package Core
 */
class Autoloader {
  const P_FOLDER = 'P_FOLDER';
  const P_PREFIX = 'P_PREFIX';

  /**
   * @var string[][] $folders - [[P_FOLDER => (str)$absoluteFolder, P_PREFIX => (str)$prefixToIgnore]]
   */
  protected static $folders = [];

  protected static $autoloaderRegistered = false;

  protected static function _constructorStatic() {
    if(!static::$autoloaderRegistered) {
      spl_autoload_register(array(__CLASS__, 'autoloader'));
      static::$autoloaderRegistered = true;
    }
  }

  /**
   * @param string $class - Fully-qualified path with namespaces
   */
  public static function autoloader($class) {
    static::_constructorStatic();

    foreach(static::$folders as $data) {
      $theClassFile = $class;

      if($data[static::P_PREFIX] && strrpos($class, $data[static::P_PREFIX]) !== false) {
        $theClassFile = substr($class, strlen($data[static::P_PREFIX]));
      }

      $classFullFileName = str_replace('\\', '/', $data[static::P_FOLDER] . $theClassFile) . DOT_PHP_EX;
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
   * @param string $classPrefix - PHP class prefix to ignore. Can be whole namespace or part of it
   */
  public static function register($absoluteClassRoot, $classPrefix = '') {
    static::_constructorStatic();

    $absoluteClassRoot = str_replace('\\', '/', SN_ROOT_PHYSICAL . $absoluteClassRoot);

    if(!($absoluteClassRoot = realpath($absoluteClassRoot))) {
      // TODO - throw new \Exception("There is some error when installing autoloader for '{$absoluteClassRoot}' class prefix '{$classPrefix}'");
      return;
    }
    $absoluteClassRoot = str_replace('\\', '/', $absoluteClassRoot) . '/';

    if($classPrefix && strrpos($classPrefix, 1) != '\\') {
      $classPrefix .= '\\';
    }

    static::$folders[] = [
      static::P_FOLDER => $absoluteClassRoot,
      static::P_PREFIX => $classPrefix,
    ];
  }

//  /**
//   * @param string $relativeClassRoot - relative path to root class folder from game root (where index.php lies)
//   * @param string $classPrefix - PHP class prefix to ignore. Can be whole namespace or part of it
//   */
//  public static function registerRelative($relativeClassRoot, $classPrefix = '') {
//    static::register(SN_ROOT_PHYSICAL . $relativeClassRoot, $classPrefix);
//  }

  public static function reset() {
    static::$folders = [];
  }

}
