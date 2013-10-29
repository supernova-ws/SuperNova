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
        return xcache_isset($this->prefix.$name) && ($this->__get($name) !== null);
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
      $qry = doquery("SELECT `{$this->sql_value_field}` FROM `{{{$this->table_name}}}` WHERE `{$this->sql_index_field}` = '{$index}';", '', true);
      $this->$index = $qry[$this->sql_value_field];

      return $qry[$this->sql_value_field];
    };
  }

  public function db_loadAll()
  {
    $this->loadDefaults();

    $query = doquery("SELECT * FROM {{{$this->table_name}}};");
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
      $qry = "REPLACE INTO `{{{$this->table_name}}}` (`{$this->sql_index_field}`, `{$this->sql_value_field}`) VALUES {$qry};";
      doquery($qry);
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

    // SEO meta
    'adv_conversion_code_payment'  => '',
    'adv_conversion_code_register' => '',
    'adv_seo_meta_description'     => '',
    'adv_seo_meta_keywords'        => '',

    // Advert banner
    'advGoogleLeftMenuIsOn'        => 0,
    'advGoogleLeftMenuCode'        => '(Place here code for banner)',

    // Alliance bonus calculations
    'ali_bonus_algorithm'          => 0,  // Bonus calculation algorithm
    'ali_bonus_brackets'           => 10, // Brackets count for ALI_BONUS_BY_RANK
    'ali_bonus_brackets_divisor'   => 10,// Bonus divisor for ALI_BONUS_BY_RANK
    'ali_bonus_divisor'            => 10000000, // Rank divisor for ALI_BONUS_BY_POINTS
    'ali_bonus_members'            => 10, // Minumum alliace size to start using bonus

    'allow_buffing'                => 0, // Disable buffing check for TRANSPORT missions
    'ally_help_weak'               => 0, // Allow strong players to HOLD on weak co-ally planets

    // User avatar and alliance logo
    'avatar_max_height'            => 128, // Maximum height
    'avatar_max_width'             => 128, // Maximum width

    'BuildLabWhileRun'             => 0,

    // Chat settings
    // Nick highliting
    'chat_highlight_developer'     => '<span class=\"nick_developer\">$1</span>', // Developer nick
    'chat_highlight_admin'         => '<span class=\"nick_admin\">$1</span>', // Admin nick
    'chat_highlight_moderator'     => '<span class=\"nick_moderator\">$1</span>', // Moderator nick
    'chat_highlight_operator'      => '<span class=\"nick_operator\">$1</span>', // Operator nick
    'chat_highlight_premium'       => '<span class=\"nick_premium\">$1</span>', // Premium nick
    // Other chat settings
    'chat_refresh_rate'            => 5, // in seconds. Chat AJAX refresh rate
    'chat_timeout'                 => 900, // in seconds. Default = 15 min

    'COOKIE_NAME'                  => 'SuperNova',
    'crystal_basic_income'         => 20,
    'debug'                        => 0,
    'Defs_Cdr'                     => 30,
    'deuterium_basic_income'       => 0,
    'eco_scale_storage'            => 1,
    'eco_stockman_fleet'           => '', // Black Market - Starting amount of s/h ship merchant to sell
    'empire_mercenary_base_period' => PERIOD_MONTH, // Base
    'empire_mercenary_temporary'   => 0, // Temporary empire-wide mercenaries
    'energy_basic_income'          => 0,

    // Bashing protection settings
    'fleet_bashing_attacks'        => 3,      // Max amount of attack per wave - 3 by default
    'fleet_bashing_interval'       => 1800,   // Maximum interval between attacks when they still count as one wave - 30m by default
    'fleet_bashing_scope'          => 86400,  // Interval on which bashing waves counts - 24h by default
    'fleet_bashing_war_delay'      => 43200,  // Delay before start bashing after declaring war to alliance - 12h by default
    'fleet_bashing_waves'          => 3,      // Max amount of waves per day - 3 by default

    'Fleet_Cdr'                    => 30,
    'fleet_speed'                  => 1,

    'game_adminEmail'              => 'root@localhost',    // Admin's email to show to users
    'game_counter'                 => 0,  // Does built-in page hit counter is on?
    // Defaults
    'game_default_language'        => 'ru',
    'game_default_skin'            => 'skins/EpicBlue/',
    'game_default_template'        => 'OpenGame',

    'game_disable'                 => 1,
    'game_disable_reason'          => 'SuperNova is in maintenance mode! Please return later!',
    'game_email_pm'                => 0, // Is allowed forwarding messages from PM to e-mail?
    // Universe size
    'game_maxGalaxy'               => 5,
    'game_maxSystem'               => 199,
    'game_maxPlanet'               => 15,
    // Game global settings
    'game_mode'                    => 0,           // 0 - SuperNova, 1 - oGame
    'game_name'                    => 'SuperNova', // Server name (would be on banners and on top of left menu)

    'game_news_actual'             => 259200, // How long announcement would be marked as "New". In seconds. Default - 3 days
    'game_news_overview'           => 3,    // How much last news to show in Overview page
    // Noob protection
    'game_noob_factor'             => 5,    // Multiplier to divide "stronger" and "weaker" users
    'game_noob_points'             => 5000, // Below this point user threated as noob. 0 to disable

    'game_speed'                   => 1, // Game speed
    'game_user_changename'         => 2, // Is user allowed to change name after registration?
    'game_user_changename_cost'    => 100000, // Change name cost for paid changename

    'initial_fields'               => 163,

    // Interface - UserBanner
    'int_banner_background'        => 'design/images/banner.png',
    'int_banner_fontInfo'          => 'terminator.ttf',
    'int_banner_fontRaids'         => 'klmnfp2005.ttf',
    'int_banner_fontUniverse'      => 'cristal.ttf',
    'int_banner_showInOverview'    => 1,
    'int_banner_URL'               => '/banner.php?type=banner',

    'int_format_date'              => 'd.m.Y', // Date default format
    'int_format_time'              => 'H:i:s', // Time default format

    // Interface - UserBar
    'int_userbar_background'       => 'design/images/userbar.png',
    'int_userbar_font'             => 'arialbd.ttf',
    'int_userbar_showInOverview'   => 1,
    'int_userbar_URL'              => '/banner.php?type=userbar',

    'LastSettedGalaxyPos'          => 1,
    'LastSettedPlanetPos'          => 1,
    'LastSettedSystemPos'          => 1,

    'metal_basic_income'           => 40,

    'payment_currency_default'      => 'UAH', // Default currency for payments
    'payment_currency_exchange_dm_' => 2500,             // Exchange rate for 1 UAH to Dark Matter
    'payment_currency_exchange_eur' => 0.09259259259259, // Exchange rate for 1 UAH to EUR
    'payment_currency_exchange_rub' => 4.0,              // Exchange rate for 1 UAH to RUB
    'payment_currency_exchange_usd' => 0.125,            // Exchange rate for 1 UAH to USD
    'payment_currency_exchange_wme' => 0.0952380952381,  // Exchange rate for 1 UAH to WME
    'payment_currency_exchange_wmr' => 4.1,              // Exchange rate for 1 UAH to WMR
    'payment_currency_exchange_wmu' => 1.05,             // Exchange rate for 1 UAH to WMU
    'payment_currency_exchange_wmz' => 0.126582278481,   // Exchange rate for 1 UAH to WMZ

    'payment_lot_price'             => 1,     // Lot price in default currency
    'payment_lot_size'              => 2500,  // Lot size. Also service as minimum amount of DM that could be bought with one transaction

    'planet_teleport_cost'         => 50000, // 
    'planet_teleport_timeout'      => 86400, // 

    'player_delete_time'           => 3888000, // 
    'player_max_colonies'          => 9, // Max player planet count (NOT including main planet)
    'player_vacation_time'         => PERIOD_WEEK, // 
    'player_vacation_timeout'      => PERIOD_WEEK, // 

    // Quests
    'quest_total'                  => 0, // Total number of quests

    'resource_multiplier'          => 1,

    //Roleplay system
    'rpg_bonus_divisor'            => 10,    // Amount of DM referral shoud get for partner have 1 DM bonus
    'rpg_bonus_minimum'            => 10000, // Minimum DM ammount for starting paying bonuses to affiliate

    // Black Market - General
    'rpg_cost_banker'              =>  1000, // Banker can hold some resources
    'rpg_cost_exchange'            =>  1000, // Exchange allows resource trade between players
    'rpg_cost_info'                => 10000, // Infotrader sells valuable information about users, alliances and universe
    'rpg_cost_pawnshop'            =>  1000, // You can get loan in pawnshop
    'rpg_cost_scraper'             =>  1000, // Scrapper buys ship for fraction of cost
    'rpg_cost_stockman'            =>  1000, // Stockman resells ship that was scrapped
    'rpg_cost_trader'              =>  1000, // Trader trades between resources

    // Black Market - Resource exachange rates
    'rpg_exchange_metal'           =>   1,
    'rpg_exchange_crystal'         =>   2,
    'rpg_exchange_deuterium'       =>   4,
    'rpg_exchange_darkMatter'      => 400,

    'rpg_flt_explore'              => 1000, // DM reward for finding Supernova in expedition

    // Black Market - Scraper rates for ship pre resource
    'rpg_scrape_crystal'           => 0.50,
    'rpg_scrape_deuterium'         => 0.25,
    'rpg_scrape_metal'             => 0.75,

    'server_que_length_hangar'     => '5', //
    'server_que_length_research'   => '1', //
    'server_que_length_structures' => '5', //

    'server_start_date'            => '', //

    'server_updater_check_auto'    => 0, // Server autocheck version
    'server_updater_check_last'    => 0, // Server last check time
    'server_updater_check_period'  => PERIOD_DAY, // Server autocheck period
    'server_updater_check_result'  => SNC_VER_NEVER, // Server last check result
    'server_updater_id'            => 0, // Server ID on update server
    'server_updater_key'           => '', // Server key on update server

    'stats_hide_admins'            => 1,
    'stats_hide_player_list'       => '',
    'stats_hide_pm_link'           => 0,
    'stats_schedule'               => 'd@04:00:00',

    'tpl_minifier'                 => 0, // Template minifier

    'uni_price_galaxy'             => 10000,
    'uni_price_system'             => 1000,

    'upd_lock_time'                => 60, // How long update will lock table. Also update increment time when it requires

    'url_dark_matter'              => '',
    'url_faq'                      => '',
    'url_forum'                    => '',
    'url_rules'                    => '',

    'users_amount'                 => 1,

    'user_birthday_celebrate'      => 0, // When last time celebrations (i.e. giftgiving) was made
    'user_birthday_gift'           => 0, // User birthday gift
    'user_birthday_range'          => PERIOD_MONTH, // How far in past can be user birthday for giving him gift

    'user_vacation_disable'        => 0,

    'var_db_update'                => 0, // Time of last DB update
    'var_db_update_end'            => 0, // Time when last DB update should end. Need to prevent duplicate update

    'var_news_last'                => 0, // Last news post time

    // Statistic
    'var_stat_update'              => 0,
    'var_stat_update_end'          => 0,
    'var_stat_update_msg'          => 'Update never started',

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
      __call($id, [$force]) - "SELECT * FROM {table} WHERE id_field = $id"

    Form 2: (not implemented yet)
      __call('get', $condition, [$force]) - "SELECT * FROM {table} WHERE $condition"

    Form 3: (not implemented yet)
      __call('set', $data, [$condition], [$force]) - "UPDATE {table} SET $row = $data WHERE $condition"

    Form 4: (not implemented yet)
      __call('add', $data, [$condition], [$force]) - "UPDATE {table} SET $row = $row + $data WHERE $condition"
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
        // it might be SELECT * FROM {table} WHERE id = $main_argument;
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
      $qry = "REPLACE INTO `{{{$this->table_name}}}` (`{$this->sql_index_field}`, `{$this->sql_value_field}`) VALUES {$qry};";
      doquery($qry);
    };
  }
*/
}

?>
