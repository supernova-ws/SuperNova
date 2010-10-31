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
define('CACHER_XCACHE'  ,  1);

define('CACHER_LOCK_WAIT', 5); // maximum cacher wait for table unlock in seconds. Can be float

// max timeout cacher can sleep in waiting for unlockDefault = 10000 ms = 0.01s
// really it will sleep mr_rand(100, CACHER_LOCK_SLEEP)
define('CACHER_LOCK_SLEEP', 10000);

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
  protected $prefix;

  protected static $cacheObject;

  public function __construct($prefIn = 'CACHE_', $init_mode = false)
  {
    if( !($init_mode === false || $init_mode === CACHER_NO_CACHE || ($init_mode === CACHER_XCACHE && extension_loaded('xcache')) ))
    {
      throw new UnexpectedValueException('Wrong work mode or current mode does not supported on your server');
    }

    $this->prefix = $prefIn;
    if ( extension_loaded('xcache') && ($init_mode === CACHER_XCACHE || $init_mode === false) )
    {
      if(self::$mode === CACHER_NOT_INIT)
      {
        self::$mode = CACHER_XCACHE;
      }
    }
    else
    {
      if(self::$mode === CACHER_NOT_INIT)
      {
        self::$mode = CACHER_NO_CACHE;
        if(!self::$data)
        {
         self::$data = array();
        }
      }
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
        $this->prefix = $value;
      break;

      default:
        switch (self::$mode)
        {
          case CACHER_NO_CACHE:
            self::$data[$this->prefix.$name] = $value;
          break;

          case CACHER_XCACHE:
            xcache_set($this->prefix.$name, $value);
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
        return $this->prefix;
      break;

      default:
        switch (self::$mode)
        {
          case CACHER_NO_CACHE:
            return self::$data[$this->prefix.$name];
          break;

          case CACHER_XCACHE:
            return xcache_get($this->prefix.$name);
          break;

        };
    };
  }

  public function __isset($name)
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        return isset(self::$data[$this->prefix.$name]);
      break;

      case CACHER_XCACHE:
        return xcache_isset($this->prefix.$name);
      break;

    };
  }

  public function __unset($name)
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        unset(self::$data[$this->prefix.$name]);
      break;

      case CACHER_XCACHE:
        xcache_unset($this->prefix.$name);
      break;

    };
  }

  public function unset_by_prefix($prefix_unset = '')
  {
    switch (self::$mode)
    {
      case CACHER_NO_CACHE:
        array_walk(self::$data, create_function('&$v,$k,$p', 'if(strpos($k, $p) === 0)$v = NULL;'), $this->prefix.$prefix_unset);
        return true;
      break;

      case CACHER_XCACHE:
        if(!function_exists('xcache_unset_by_prefix'))
        {
          return false;
        }
        return xcache_unset_by_prefix($this->prefix.$prefix_unset);
      break;
    };
  }
  // -------------------------------------------------------------------------
  // End of low-level functions
  // -------------------------------------------------------------------------

  protected function make_element_name($args, $diff = 0)
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
      $retVal = NULL;
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
        return dump(self::$data, $this->prefix);
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
  protected $table_name;
  protected $sql_index_field;
  protected $sql_value_field;

  protected $defaults = array();

  public function __construct($gamePrefix = 'sn_', $table_name = 'table')
  {
    parent::__construct("{$gamePrefix}{$table_name}_");
    $this->table_name = $table_name;

    $this->sql_index_field = "{$table_name}_name";
    $this->sql_value_field = "{$table_name}_value";

    if(!$this->_DB_LOADED)
    {
      $this->db_loadAll();
    }
  }

  public static function getInstance($gamePrefix = 'sn_', $table_name = '')
  {
    if (!isset(self::$cacheObject))
    {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, $table_name);
    }
    return self::$cacheObject;
  }

  public function db_loadItem($index)
  {
    if($index)
    {
      $qry = doquery("SELECT `{$this->sql_value_field}` FROM `{{table}}` WHERE `{$this->sql_index_field}` = '{$index}';", $this->table_name, true);
      $this->$index = $qry[$this->sql_value_field];

      return $qry[$this->sql_value_field];
    };
  }

  public function db_loadAll()
  {
    $this->loadDefaults();

    $query = doquery('SELECT * FROM {{table}};', $this->table_name);
    while ( $row = mysql_fetch_assoc($query) )
    {
      $this->$row[$this->sql_index_field] = $row[$this->sql_value_field];
    }

    $this->_DB_LOADED = true;
  }

  public function loadDefaults()
  {
    foreach($this->defaults as $defName => $defValue)
    {
      $this->$defName = $defValue;
    }
  }

  public function db_saveAll()
  {
    $toSave = array();
    foreach($this->defaults as $field => $value)
    {
      $toSave[$field] = NULL;
    }

    $this->db_saveItem($toSave);
  }

  public function db_saveItem($item_list, $value = NULL)
  {
    if($item_list)
    {
      if(!is_array($item_list))
      {
        $item_list = array($item_list => $value);
      }

      foreach($item_list as $item_name => $item_value)
      {
        if($item_name)
        {
          if($item_value !== NULL)
          {
            $this->$item_name = $item_value;
          }
          else
          {
            $item_value = $this->$item_name;
          }

          $qry .= " ('{$item_name}', '{$item_value}'),";
        }
      }

      $qry = substr($qry, 0, -1);
      $qry = "REPLACE INTO `{{table}}` (`{$this->sql_index_field}`, `{$this->sql_value_field}`) VALUES {$qry};";
      doquery($qry, $this->table_name);
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
    'fleet_speed'            => 1,
    'url_forum'              => '/forum/',
    'url_rules'              => '/rules.php',
    'url_dark_matter'        => '/dark_matter_get.php',
    'initial_fields'         => 163,
    'LastSettedGalaxyPos'    => 1,
    'LastSettedPlanetPos'    => 1,
    'LastSettedSystemPos'    => 1,
    'metal_basic_income'     => 40,
    'noobprotection'         => 1,
    'noobprotectionmulti'    => 5,
    'noobprotectiontime'     => 5000,
    'resource_multiplier'    => 1,
    'urlaubs_modus_erz'      => 0,
    'users_amount'           => 0,

    // Noob protection
    'game_noob_points'       => 5000, // Below this point user threated as noob. 0 to disable
    'game_noob_factor'       => 5,    // Multiplier to divide "stronger" and "weaker" users

    // Game global settings
    'game_name'  => 'SuperNova', // Server name (would be on banners and on top of left menu)
    'game_mode'  => 0,           // 0 - SuperNova, 1 - oGame
    'game_speed' => 1,           // Game speed

    // Universe size
    'game_maxGalaxy' => '5',
    'game_maxSystem' => '199',
    'game_maxPlanet' => '15',

    'game_adminEmail' => 'root@localhost',    // Admin's email to show to users
    'game_counter' => 1,  // Does built-in page hit counter is on?

    // Defaults
    'game_default_language' => 'ru',
    'game_default_skin'     => 'skins/EpicBlue/',
    'game_default_template' => 'OpenGame',

    'game_disable'         => 1,
    'game_disable_reason'  => 'SuperNova is in maintenance mode! Please return later!',

    'game_user_changename' => 0, // Is user allowed to change name after registration?

    'int_format_date'      => 'd.m.Y', // Date default format
    'int_format_time'      => 'H:i:s', // Time default format

    'game_news_overview'   => 3,    // How much last news to show in Overview page
    'game_news_actual'     => 259200, // How long announcement would be marked as "New". In seconds. Default - 3 days

    'player_max_colonies'  => 9, // Max player planet count (NOT including main planet)

    // Interface - UserBanner
    'int_banner_showInOverview'  => 1,
    'int_banner_background'      => 'design/images/banner.png',
    'int_banner_URL'             => '/banner.php?type=banner',
    'int_banner_fontUniverse'    => 'cristal.ttf',
    'int_banner_fontRaids'       => 'klmnfp2005.ttf',
    'int_banner_fontInfo'        => 'terminator.ttf',

    // Interface - UserBar
    'int_userbar_showInOverview' => 1,
    'int_userbar_background'     => 'design/images/userbar.png',
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
    'var_stat_update'     => 0,
    'var_stat_update_end' => 0,
    'var_stat_update_msg' => 'Update never started',
    'stats_schedule' => 'd@04:00:00',

    'var_db_update'     => 0,  // Time of last DB update
    'var_db_update_end' => 0,  // Time when last DB update should end. Need to prevent duplicate update

  );

  public function __construct($gamePrefix = 'sn_')
  {
    parent::__construct($gamePrefix, 'config');
  }

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
































/*
New test metaclass for handling DB table caching
*/

class class_db_cache extends classCache
{
  protected $tables = array(
    'users' => array('name' => 'users', 'id' => 'id', 'index' => 'users_INDEX', 'count' => 'users_COUNT', 'lock' => 'users_LOCK', 'callback' => 'cb_users', 'ttl' => 0),
    'config' => array('name' => 'config', 'id' => 'config_name', 'index' => 'config_INDEX', 'count' => 'config_COUNT', 'lock' => 'config_LOCK', 'callback' => 'cb_config', 'ttl' => 0),
  );

  public function __construct($gamePrefix = 'sn_')
  {
    parent::__construct("{$gamePrefix}dbcache_");
  }

  public function cache_add($table_name = 'table', $id_field = 'id', $ttl = 0, $force_load = false)
  {
    $table['name']     = $table_name;
    $table['id']       = $id_field;
    $table['index']    = "{$table_name}_INDEX";
    $table['count']    = "{$table_name}_COUNT";
    $table['lock']     = "{$table_name}_LOCK";
    $table['callback'] = "cb_{$table_name}";
    $table['ttl']      = $ttl;

    $this->tables[$table_name] = $table;
    // here we can load table data from DB - fields and indexes
    // $force_reload would show should we need to reload table data from DB
  }

  // multilock
  protected function table_lock($table_name, $wait_if_locked = false, $lock = NULL)
  {
    $lock_field_name = $this->tables['$table_name']['lock'];

    $lock_wait_start = microtime(true);
    while($wait_if_locked && !$this->$lock_field_name && (microtime(true) - $lock_wait_start <= CACHER_LOCK_WAIT))
    {
      usleep(mt_rand(100, CACHER_LOCK_SLEEP));
    }

    $result = (!$this->$lock_field_name) XOR ($lock === NULL);
    if($result && $lock)
    {
      $this->$lock_field_name = $lock;
    }

    return $result;
  }


  /*
    Magic start here. __call magic method will transform name of call to table name and handle all caching & db-related stuff
    If there is such row in cache it will be returned. Otherwise it will read from DB, cached and returned
    I.e. class_db_cache->table_name() call mean that all request will be done with records (cached or DB) in `table_name` table
    __call interpets last argument as optional boolean parameter $force.
    In read operations $force === true will tell cacher not to use cached data but load records from DB
    In write operations $force === true will tell cacher immidiatly store new data to DB not relating on internal mechanics

    __call have several forms

    Form 1:
      __call($id, [$force]) - "SELECT * FROM {{table}} WHERE id_field = $id"

    Form 2: (not implemented yet)
      __call('get', $condition, [$force]) - "SELECT * FROM {{table}} WHERE $condition"

    Form 3: (not implemented yet)
      __call('set', $data, [$condition], [$force]) - "UPDATE {{table}} SET $row = $data WHERE $condition"

    Form 4: (not implemented yet)
      __call('add', $data, [$condition], [$force]) - "UPDATE {{table}} SET $row = $row + $data WHERE $condition"
  */

  public function __call($name, $arguments)
  {
    $main_argument = $arguments[0];

    switch($main_argument)
    {
      case 'get':
        // it might be SELECT
      break;

      case 'set':
        // it might be UPDATE
      break;

      default:
        // it might be SELECT * FROM {{table}} WHERE id = $main_argument;
        return $this->get_item($name, $main_argument, $arguments[1]);
      break;
    }
  }

  public function get_item($table_name, $id, $force_reload = false)
  {
    $internal_name = "{$table_name}_{$id}";

    if(isset($this->$internal_name) && !$force_reload)
    {
      // pdump("{$id} - returning stored data");

      return $this->$internal_name;
    }
    else
    {
      // pdump("{$id} - asking DB");

      return $this->db_loadItem($table_name, $id);
    }
  }

  public function db_loadItem($table_name, $id)
  {
    // If no table_name or no index - there is no such element
    if(!$id && !$table_name)
    {
      return NULL;
    }

    $result = $this->db_loadItems($table_name, '*', "`{$this->tables[$table_name]['id']}` = '{$id}'", 1);
    if($result)
    {
      return $result[$id];
    }
    else
    {
      $this->del_item($table_name, $id);
      return NULL;
    }
  }

  public function db_loadItems($table_name, $fields = '*', $condition = '', $limits = '')
  {
    if(!$fields)
    {
      $fields = '*';
    }

    if($condition)
    {
      $condition = " WHERE {$condition}";
    }

    if($limits)
    {
      $limits = " LIMIT {$limits}";
    }

    $query = doquery("SELECT {$fields} FROM `{{{$table_name}}}`{$condition}{$limits};");

    $table = $this->tables[$table_name];

    $index = $this->$table['index'];
    $count = $this->$table['count'];

    $result = NULL;

    while ( $row = mysql_fetch_assoc($query) )
    {
      /*
      foreach($row as $index => &$value)
      {
        if(is_numeric($value))
        {
          $value = floatval($value);

          //if((double)intval($value) === $value)
          //{
          //  $value = intval($value);
          //}
        }
      }
      */

      $item_id = $row[$table['id']];
      $item_name = "{$table['name']}_{$item_id}";
      if(!isset($this->$item_name))
      {
        $count++;
      }

      // Loading element to cache
      $this->$item_name = $row;
      // Also loading element to returning set
      $result[$item_id] = $row;

      // Internal work
      // Indexing element for fast search
      $index[$item_id] = true;
    }

    if($result)
    {
      $this->$table['index'] = $index;
      $this->$table['count'] = $count;
    }

    return $result;
  }

  public function db_loadAll($table_name)
  {
    $this->unset_by_prefix("{$table_name}_");
    $this->db_loadItems($table_name);
  }

  public function del_item($table_name, $id, $force_db_remove = false)
  {
    $internal_name = "{$table_name}_{$id}";

    if(isset($this->$internal_name))
    {
      $table = $this->tables[$table_name];

      $index = $this->$table['index'];
      unset($index[$id]);
      $this->$table['index'] = $index;

      $this->$table['count']--;
    }

    if($force_db_remove)
    {
      doquery("DELETE FROM {{{$table_name}}} WHERE `{$table['id']}` = '{$id}';");
    }
  }

/*
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

      foreach($index as $item_name => &$value)
      {
        if($value !== NULL)
        {
          $this->$item_name = $value;
        }
        else
        {
          $value = $this->$item_name;
        }

        $qry .= " ('{$item_name}', '{$value}'),";
      }

      $qry = substr($qry, 0, -1);
      $qry = "REPLACE INTO `{{table}}` (`{$this->sql_index_field}`, `{$this->sql_value_field}`) VALUES {$qry};";
      doquery($qry, $this->table_name);
    };
  }
*/
}
?>