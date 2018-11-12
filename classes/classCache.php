<?php
/**
 *
 * @package supernova
 * @version #43b0#
 * @copyright (c) 2009-2017 Gorlum for http://supernova.ws
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 *
 * Basic cacher class that handles different cache engines
 * It's pretty smart to handle one cache instance for all application instances (if there is PHP-cacher installed)
 * Currently supported only XCache and no-cache (array)
 * With no-cache some advanced features would be unaccessible
 * Cacher works not only with single values. It's also support multidimensional arrays
 * Currently support is a bit limited - for example there is no "walk" function. However basic array abilities supported
 * You should NEVER operate with arrays inside of cacher and should ALWAYS use wrap-up functions
 *
 * @property bool  _INITIALIZED
 * @property array lng_stat_usage - Array for locale strings usage statistics
 * @property array tables
 *
 * @package supernova
 */
class classCache implements ArrayAccess {
  /**
   * CACHER_NOT_INIT - not initialized
   */
  const CACHER_NOT_INIT = -1;
  /**
   * CACHER_NO_CACHE - no cache - array() used
   */
  const CACHER_NO_CACHE = 0;
  /**
   * CACHER_XCACHE   - xCache
   */
  const CACHER_XCACHE = 1;

  /**
   * @var int $mode - cacher mode
   */
  protected static $mode = self::CACHER_NOT_INIT;
  /**
   * @var array $data - Cacher data
   */
  protected static $data;
  /**
   * @var string $prefix - Cacher prefix
   */
  protected $prefix;

  /**
   * @var $cacheObject static - Singleton object
   */
  protected static $cacheObject;

  public function __construct($prefIn = 'CACHE_', $init_mode = false) {
    if (!($init_mode === false || $init_mode === self::CACHER_NO_CACHE || ($init_mode === self::CACHER_XCACHE && extension_loaded('xcache')))) {
      throw new UnexpectedValueException('Wrong work mode or current mode does not supported on your server');
    }

    $this->prefix = $prefIn;
    if (extension_loaded('xcache') && ($init_mode === self::CACHER_XCACHE || $init_mode === false)) {
      if (self::$mode === self::CACHER_NOT_INIT) {
        self::$mode = self::CACHER_XCACHE;
      }
    } else {
      if (self::$mode === self::CACHER_NOT_INIT) {
        self::$mode = self::CACHER_NO_CACHE;
        if (!self::$data) {
          self::$data = array();
        }
      }
    }
  }

  public static function getInstance($prefIn = 'CACHE_', $table_name = '') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($prefIn);
    }

    return self::$cacheObject;
  }

  public final function __clone() {
    // You NEVER need to copy cacher object or siblings
    throw new BadMethodCallException('Clone is not allowed');
  }

  /**
   * @return int
   */
  public function getMode() {
    return self::$mode;
  }

  /**
   * @return string
   */
  public function getPrefix() {
    return $this->prefix;
  }

  /**
   * @param $prefix
   */
  public function setPrefix($prefix) {
    $this->prefix = $prefix;
  }

  // -------------------------------------------------------------------------
  // Here comes low-level functions - those that directly works with cacher engines
  // -------------------------------------------------------------------------
  public function __set($name, $value) {
    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
        self::$data[$this->prefix . $name] = $value;
      break;

      case self::CACHER_XCACHE:
        xcache_set($this->prefix . $name, $value);
      break;
    }
  }

  public function __get($name) {
    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
        return array_key_exists($this->prefix . $name, self::$data) ? self::$data[$this->prefix . $name] : null;
      break;

      case self::CACHER_XCACHE:
        return xcache_get($this->prefix . $name);
      break;
    }

    return null;
  }

  public function __isset($name) {
    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
        return isset(self::$data[$this->prefix . $name]);
      break;

      case self::CACHER_XCACHE:
        return xcache_isset($this->prefix . $name) && ($this->__get($name) !== null);
      break;
    }

    return false;
  }

  public function __unset($name) {
    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
        unset(self::$data[$this->prefix . $name]);
      break;

      case self::CACHER_XCACHE:
        xcache_unset($this->prefix . $name);
      break;
    }
  }

  public function unset_by_prefix($prefix_unset = '') {
    static $array_clear;
    !$array_clear ? $array_clear = function (&$v, $k, $p) {
      strpos($k, $p) === 0 ? $v = null : false;
    } : false;

    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
//        array_walk(self::$data, create_function('&$v,$k,$p', 'if(strpos($k, $p) === 0)$v = NULL;'), $this->prefix.$prefix_unset);
        array_walk(self::$data, $array_clear, $this->prefix . $prefix_unset);

        return true;
      break;

      case self::CACHER_XCACHE:
        if (!function_exists('xcache_unset_by_prefix')) {
          return false;
        }

        set_time_limit(300); // TODO - Optimize
        $result = xcache_unset_by_prefix($this->prefix . $prefix_unset);
        set_time_limit(30); // TODO - Optimize

        return $result;
      break;
    }

    return true;
  }
  // -------------------------------------------------------------------------
  // End of low-level functions
  // -------------------------------------------------------------------------

  protected function make_element_name($args, $diff = 0) {
    $num_args = count($args);

    if ($num_args < 1) {
      return false;
    }

    $name = '';
    $aName = array();
    for ($i = 0; $i <= $num_args - 1 - $diff; $i++) {
      $name .= "[{$args[$i]}]";
      array_unshift($aName, $name);
    }

    return $aName;
  }

  public function array_set() {
    $args = func_get_args();
    $name = $this->make_element_name($args, 1);

    if (!$name) {
      return null;
    }

    if ($this->$name[0] === null) {
      for ($i = count($name) - 1; $i > 0; $i--) {
        $cName = "{$name[$i]}_COUNT";
        $cName1 = "{$name[$i-1]}_COUNT";
        if ($this->$cName1 == null || $i == 1) {
          $this->$cName++;
        }
      }
    }

    $this->$name[0] = $args[count($args) - 1];

    return true;
  }

  public function array_get() {
    $name = $this->make_element_name(func_get_args());
    if (!$name) {
      return null;
    }

    return $this->$name[0];
  }

  public function array_count() {
    $name = $this->make_element_name(func_get_args());
    if (!$name) {
      return 0;
    }
    $cName = "{$name[0]}_COUNT";
    $retVal = $this->$cName;
    if (!$retVal) {
      $retVal = null;
    }

    return $retVal;
  }

  public function array_unset() {
    $name = $this->make_element_name(func_get_args());

    if (!$name) {
      return false;
    }
    $this->unset_by_prefix($name[0]);

    $count = count($name);
    for ($i = 1; $i < $count; $i++) {
      $cName = "{$name[$i]}_COUNT";
      $cName1 = "{$name[$i-1]}_COUNT";

      if ($i == 1 || $this->$cName1 === null) {
        $this->$cName--;
        if ($this->$cName <= 0) {
          unset($this->$cName);
        }
      }
    }

    return true;
  }

  public function dumpData() {
    switch (self::$mode) {
      case self::CACHER_NO_CACHE:
        return dump(self::$data, $this->prefix);
      break;

      default:
        return false;
      break;
    }
  }

  public function reset() {
    $this->unset_by_prefix();

    $this->_INITIALIZED = false;
  }

  public function init($reInit = false) {
    $this->_INITIALIZED = true;
  }

  public function isInitialized() {
    return $this->_INITIALIZED;
  }

  /**
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   *
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   *
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset) {
    return $this->__isset($offset);
  }

  /**
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   *
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   *
   * @return mixed Can return all value types.
   * @since 5.0.0
   */
  public function offsetGet($offset) {
    return $this->__get($offset);
  }

  /**
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   *
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetSet($offset, $value) {
    $this->__set($offset, $value);
  }

  /**
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   *
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset) {
    $this->__unset($offset);
  }

}
