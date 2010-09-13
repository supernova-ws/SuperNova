<?php
/**
*
* @package supernova
* @version $Id$
* @copyright (c) 2009-2010 Gorlum for http://supernova.ws
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
* Defining some constants
*/
define('CACHER_NOT_INIT', -1);
define('CACHER_NO_CACHE',  0);
define('CACHER_XCACHE',    1);

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
* @package supernova
*
*/
class classCache
{
  // CACHER_NOT_INIT - not initialized
  // CACHER_NO_CACHE - no cache - array() used
  // CACHER_XCACHE   - xCache
  protected static $mode = CACHER_NOT_INIT;
  protected static $data;
  protected static $prefix;

  protected static $cacheObject;

  protected function __construct($prefIn = 'CACHE_', $init_mode = false)
  {
    if( !($init_mode === false || $init_mode === CACHER_NO_CACHE || ($init_mode === CACHER_XCACHE && extension_loaded('xcache')) ))
    {
      throw new UnexpectedValueException('Wrong work mode or current mode does not supported on your server');
    }

    self::$prefix = $prefIn;
    if ( extension_loaded('xcache') && ($init_mode === CACHER_XCACHE || $init_mode === false) )
    {
      self::$mode = CACHER_XCACHE;
    }
    else
    {
      self::$mode = CACHER_NO_CACHE;
      self::$data = array();
    };
  }

  public static function getInstance($prefIn = 'CACHE_')
  {
    if (!isset(self::$cacheObject))
    {
      $className = get_class();
      self::$cacheObject = new $className($prefIn);
    }
    return self::$cacheObject;
  }

  public final function __clone()
  {
    // You NEVER need to copy cacher object or siblings
    throw new BadMethodCallException('Clone is not allowed');
  }

  // -------------------------------------------------------------------------
  // Here comes low-level functions - those that directly works with cacher engines
  // -------------------------------------------------------------------------
  public function __set($name, $value)
  {
    switch ($name)
    {
      case '_MODE':
        throw new UnexpectedValueException('You can not change cacher mode on-the-fly!');
      break;

      case '_PREFIX':
        self::$prefix = $value;
      break;

      default:
        switch (self::$mode)
        {
          case CACHER_NO_CACHE:
            self::$data[self::$prefix.$name] = $value;
          break;

          case CACHER_XCACHE:
            xcache_set(self::$prefix.$name, $value);
          break;

        };
      break;
    };
  }

  public function __get($name)
  {
    switch ($name)
    {
      case '_MODE':
        return self::$mode;
      break;

      case '_PREFIX':
        return self::$prefix;
      break;

      default:
        switch (self::$mode)
        {
          case CACHER_NO_CACHE:
            return self::$data[self::$prefix.$name];
          break;

          case CACHER_XCACHE:
            return xcache_get(self::$prefix.$name);
          break;

        };
    };
  }

  public function __isset($name)
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        return isset(self::$data[self::$prefix.$name]);
      break;

      case CACHER_XCACHE:
        return xcache_isset(self::$prefix.$name);
      break;

    };
  }

  public function __unset($name)
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        unset(self::$data[self::$prefix.$name]);
      break;

      case CACHER_XCACHE:
        xcache_unset(self::$prefix.$name);
      break;

    };
  }

  public function unset_by_prefix($prefix_unset = '')
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        array_walk(self::$data, create_function('&$v,$k,$p', 'if(strpos($k, $p) === 0)$v = NULL;'), self::$prefix.$prefix_unset);
        return true;
      break;

      case CACHER_XCACHE:
        if(!function_exists('xcache_unset_by_prefix'))
        {
          return false;
        }
        return xcache_unset_by_prefix(self::$prefix.$prefix_unset);
      break;
    };
  }
  // -------------------------------------------------------------------------
  // End of low-level functions
  // -------------------------------------------------------------------------

  private function make_element_name($args, $diff = 0)
  {
    $num_args = count($args);

    if($num_args<1)
    {
      return false;
    }

    $aName = array();

    for($i = 0; $i <= $num_args - 1 - $diff; $i++)
    {
      $name .= "[{$args[$i]}]";
      array_unshift($aName, $name);
    }

    return $aName;
  }

  public function array_set()
  {
    $args = func_get_args();
    $name = $this->make_element_name($args, 1);

    if(!$name)
    {
      return NULL;
    }

    if($this->$name[0] === NULL)
    {
      for($i = count($name) - 1; $i > 0; $i--)
      {
        $cName = "{$name[$i]}_COUNT";
        $cName1 = "{$name[$i-1]}_COUNT";
        if($this->$cName1 == NULL || $i == 1)
        {
          $this->$cName++;
        }
      }
    }

    $this->$name[0] = $args[count($args) - 1];
    return true;
  }

  public function array_get()
  {
    $name = $this->make_element_name(func_get_args());
    if(!$name)
    {
      return NULL;
    }
    return $this->$name[0];
  }

  public function array_count()
  {
    $name = $this->make_element_name(func_get_args());
    if(!$name)
    {
      return 0;
    }
    $cName = "{$name[0]}_COUNT";
    $retVal = $this->$cName;
    if(!$retVal)
    {
      $retVal = 0;
    }
    return $retVal;
  }

  public function array_unset()
  {
    $name = $this->make_element_name(func_get_args());

    if(!$name)
    {
      return false;
    }
    $this->unset_by_prefix($name[0]);

    for($i = 1; $i < count($name); $i++)
    {
      $cName = "{$name[$i]}_COUNT";
      $cName1 = "{$name[$i-1]}_COUNT";

      if($i == 1 || $this->$cName1 === NULL)
      {
        $this->$cName--;
        if($this->$cName <= 0)
        {
          unset($this->$cName);
        }
      }
    }
    return true;
  }

  public function dumpData()
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        return dump(self::$data, self::$prefix);
      break;

      default:
        return false;
      break;

    };
  }

  public function reset()
  {
    $this->unset_by_prefix();

    $this->_INITIALIZED = false;
  }

  public function init($reInit = false)
  {
    $this->_INITIALIZED = true;
  }

  public function isInitialized()
  {
    return $this->_INITIALIZED;
  }
}

/**
*
* Persistent is extension of class cacher and can save itself to DB
* It's most usefull to hold basic structures as configuration, variables etc
* Persistent pretty smart to handle one-level tables structures a-la "variable_name"+"variable_value"
* Look supernova.sql to learn more
* Also this class can holds default values for variables
*
* @package supernova
*
*/
class classPersistent extends classCache
{
  protected $internalName;
  protected $sqlTableName;
  protected $sqlSelectAll;
  protected $sqlInsert;
  protected $sqlUpdate;
  protected $sql_index_field;
  protected $sql_value_field;

  protected $defaults = array();

  protected function __construct($gamePrefix = 'sn_', $internalName = '', $tableName = '')
  {
    parent::__construct("{$gamePrefix}{$internalName}");
    $this->internalName = $internalName;

    if(!$tableName)
    {
      $tableName = $internalName;
    }
    $this->sqlTableName = $tableName;
    $this->sqlSelectAll = 'SELECT * FROM `{{table}}`;';
    $this->sql_index_field = "{$internalName}_name";
    $this->sqlValueName = "{$internalName}_value";

    if(!$this->_DB_LOADED)
    {
      $this->db_loadAll();
    }
  }

  public static function getInstance($gamePrefix = 'sn_', $internalName = '')
  {
    if (!isset(self::$cacheObject))
    {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, $internalName);
    }
    return self::$cacheObject;
  }

  public function loadDefaults()
  {
    foreach($this->defaults as $defName => $defValue)
    {
      $this->$defName = $defValue;
    }
  }

  public function db_loadAll()
  {
    $this->loadDefaults();

    $query = doquery($this->sqlSelectAll, $this->sqlTableName);
    while ( $row = mysql_fetch_assoc($query) )
    {
      $this->$row[$this->sql_index_field] = $row[$this->sqlValueName];
    }

    $this->_DB_LOADED = true;
  }

  public function db_saveAll()
  {
    $toSave = array();
    foreach($defaults as $field => $value)
    {
      $toSave[$field] = NULL;
    }

    $this->db_saveItem($toSave);
  }

  public function db_saveItem($index, $value = NULL)
  {
    if($index)
    {
      if(!is_array($index))
      {
        $index = array($index => $value);
      }

      foreach($index as $item_index => &$itemValue)
      {
        if($itemValue !== NULL)
        {
          $this->$item_index = $itemValue;
        }
        else
        {
          $itemValue = $this->$item_index;
        }

        $qry .= " ('{$item_index}', '{$itemValue}'),";
      }

      $qry = substr($qry, 0, -1);
      $qry = "REPLACE INTO `{{table}}` (`{$this->sql_index_field}`, `{$this->sqlValueName}`) VALUES {$qry}";
      doquery($qry, $this->sqlTableName);
    };
  }

  public function db_loadItem($index)
  {
    if($index)
    {
      $qry = doquery("SELECT `{$this->sqlValueName}` FROM `{{table}}` WHERE `{$this->sql_index_field}` = '{$index}';", $this->sqlTableName, true);
      $this->$index = $qry[$this->sqlValueName];

      return $qry[$this->sqlValueName];
    };
  }
}

/**
*
* This class is used to handle server configuration
*
* @package supernova
*
*/
class classConfig extends classPersistent
{
  protected $defaults = array(
    'BuildLabWhileRun'       => 0,
    'COOKIE_NAME'            => 'SuperNova',
    'crystal_basic_income'   => 20,
    'debug'                  => 0,
    'Defs_Cdr'               => 30,
    'deuterium_basic_income' => 0,
    'energy_basic_income'    => 0,
    'Fleet_Cdr'              => 30,
    'fleet_speed'            => 2500,
    'forum_url'              => '/forum/',
    'initial_fields'         => 163,
    'LastSettedGalaxyPos'    => 0,
    'LastSettedPlanetPos'    => 0,
    'LastSettedSystemPos'    => 0,
    'metal_basic_income'     => 40,
    'noobprotection'         => 1,
    'noobprotectionmulti'    => 5,
    'noobprotectiontime'     => 5000,
    'resource_multiplier'    => 1,
    'urlaubs_modus_erz'      => 0,
    'users_amount'           => 0,

    // Game global settings
    'game_name'  => 'SuperNova', // Server name (would be on banners and on top of left menu)
    'game_mode'  => '0',         // 0 - SuperNova, 1 - oGame
    'game_speed' => 2500,        // Game speed. 2500 - normal

    // Universe size
    'game_maxGalaxy' => '9',
    'game_maxSystem' => '499',
    'game_maxPlanet' => '15',

    'game_adminEmail' => '',    // Admin's email to show to users

    'game_disable'         => 1,
    'game_disable_reason'  => 'SuperNova is in maintenance mode! Please return later!',

    'game_user_changename' => 0, // Is user allowed to change name after registration?

    'game_date_withTime'   => 'd.m.Y h:i:s', // Date & time global format

    'game_news_overview'   => 3,    // How much last news to show in Overview page
    'game_news_actual'     => 259200, // How long announcement would be marked as "New". In seconds. Default - 3 days

    // Interface - UserBanner
    'int_banner_showInOverview'  => 1,
    'int_banner_background'      => 'images/banner.png',
    'int_banner_URL'             => '/banner.php?type=banner',
    'int_banner_fontUniverse'    => 'cristal.ttf',
    'int_banner_fontRaids'       => 'klmnfp2005.ttf',
    'int_banner_fontInfo'        => 'terminator.ttf',

    // Interface - UserBar
    'int_userbar_showInOverview' => 1,
    'int_userbar_background'     => 'images/userbar.png',
    'int_userbar_URL'            => '/banner.php?type=userbar',
    'int_userbar_font'           => 'arialbd.ttf',

    // Chat settings
    'chat_timeout'         => 900, // in seconds. Default = 15 min
    'chat_admin_msgFormat' => '[c=purple]$2[/c]', // formatting message for Admin

    //Roleplay system
    'rpg_officer'       =>  3, // Cost per officer level
    'rpg_bonus_divisor' => 10, // Amount of DM referral shoud get for partner have 1 DM bonus

    // Black Market - General
    'rpg_cost_trader'    => 1,     // Trader trades between resources
    'rpg_cost_scraper'   => 1,     // Scrapper buys ship for fraction of cost
    'rpg_cost_stockman'  => 1,     // Stockman resells ship that was scrapped
    'rpg_cost_banker'    => 1,     // Banker can hold some resources
    'rpg_cost_exchange'  => 1,     // Exchange allows resource trade between players
    'rpg_cost_pawnshop'  => 1,     // You can get loan in pawnshop

    // Black Market - Resource exachange rates
    'rpg_exchange_metal'      =>      1,
    'rpg_exchange_crystal'    =>      2,
    'rpg_exchange_deuterium'  =>      4,
    'rpg_exchange_darkMatter' => 100000,

    // Black Market - Scraper rates for ship pre resource
    'rpg_scrape_metal'     => 0.75,
    'rpg_scrape_crystal'   => 0.50,
    'rpg_scrape_deuterium' => 0.25,

    // Black Market - Starting amount of s/h ship merchant to sell
    'eco_stockman_fleet' => '',

    // Statistic
    'stats_lastUpdated' => '0',
    'stats_schedule' => 'd@04:00:00',
  );

  public static function getInstance($gamePrefix = 'sn_')
  {
    if (!isset(self::$cacheObject))
    {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, 'config');
    }
    return self::$cacheObject;
  }
}
?>