<?php
class classCache {
  // -1 - not initialized
  // 0 - no cache - array() used
  // 1 - xCache
  protected static $mode = -1;
  protected static $data;
  protected static $prefix;

  protected static $cacheObject;

  protected function __construct($prefIn = 'CACHE_') {
    self::$prefix = $prefIn;
    if ( extension_loaded('xcache') ){
      self::$mode = 1;
    }else{
      self::$mode = 0;
      self::$data = array();
    };
  }

  public static function getInstance($prefIn = 'CACHE_') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($prefIn);
    }
    return self::$cacheObject;
  }

  public final function __clone() {
    // throw new BadMethodCallException("Clone is not allowed");
  }

  public function __set($name, $value) {
    switch ($name){
      case '_PREFIX':
        self::$prefix = $value;
        break;
      default:
        switch (self::$mode) {
          case 0:
            self::$data[self::$prefix.$name] = $value;
            break;
          case 1:
            xcache_set(self::$prefix.$name, $value);
            break;
        };
        break;
    };
  }

  public function __get($name) {
    switch ($name){
      case '_PREFIX':
        return self::$prefix;
        break;
      case '_MODE':
        return self::$mode;
        break;
      default:
        switch (self::$mode) {
          case 0:
            return self::$data[self::$prefix.$name];
          case 1:
            return xcache_get(self::$prefix.$name);
        };
    };
  }

  public function __isset($name) {
    switch (self::$mode) {
      case 0:
        return isset(self::$data[self::$prefix.$name]);
        break;
      case 1:
        return xcache_isset(self::$prefix.$name);
        break;
    };
  }

  public function __unset($name) {
    switch (self::$mode) {
      case 0:
        unset(self::$data[self::$prefix.$name]);
        break;
      case 1:
        xcache_unset(self::$prefix.$name);
        break;
    };
  }

  private function make_element_name($args, $diff = 0){
    $num_args = count($args);

    if($num_args<1)
      return false;

    $aName = array();

    for($i = 0; $i <= $num_args - 1 - $diff; $i++){
      $name .= '[' . $args[$i] . ']';
      array_unshift($aName, $name);
    }

    return $aName;
  }

  public function array_set(){
    $args = func_get_args();
    $name = $this->make_element_name($args, 1);

    if(!$name) return NULL;

    if($this->$name[0] === NULL){
      for($i = count($name) - 1; $i > 0; $i--){
        $cName = $name[$i] . '_COUNT';
        $cName1 = $name[$i-1] . '_COUNT';
        if($this->$cName1 == NULL || $i == 1)
          $this->$cName++;
      }
    }

    $this->$name[0] = $args[count($args) - 1];
    return true;
  }

  public function array_get(){
    $name = $this->make_element_name(func_get_args());
    if(!$name) return NULL;
    return $this->$name[0];
  }

  public function array_count(){
    $name = $this->make_element_name(func_get_args());
    if(!$name) return 0;
    $cName = $name[0] . "_COUNT";
    $retVal = $this->$cName;
    if(!$retVal) $retVal = 0;
    return $retVal;
  }

  public function array_unset(){
    $name = $this->make_element_name(func_get_args());
    if(!$name) return false;
    $this->unset_by_prefix($name[0]);

    for($i = 1; $i < count($name); $i++){
      $cName = $name[$i] . "_COUNT";
      $cName1 = $name[$i-1] . "_COUNT";

      if($i == 1 || $this->$cName1 === NULL){
        $this->$cName--;
        if($this->$cName <= 0)
          unset($this->$cName);
      }
    }
    return true;
  }

  public function unset_by_prefix($prefix_unset = '') {
    switch (self::$mode) {
      case 0:
        array_walk(self::$data, create_function('&$v,$k,$p', 'if(strpos($k, $p) === 0)$v = NULL;'), self::$prefix.$prefix_unset);
        return true;
        break;
      case 1:
        if(!function_exists('xcache_unset_by_prefix')) return false;
        return xcache_unset_by_prefix(self::$prefix.$prefix_unset);
        break;
    };
  }

  public function dumpData() {
    switch (self::$mode) {
      case 0:
        return dump(self::$data, self::$prefix);
        break;
      default:
        return false;
        break;
    };
  }

  public function reset(){
    $this->unset_by_prefix();

    $this->_INITIALIZED = false;
  }

  public function init($reInit = false){
    $this->_INITIALIZED = true;
  }

  public function isInitialized(){
    return $this->_INITIALIZED;
  }
}

// -- Persistent object - saves itself to DB
class classPersistent extends classCache {
  protected $internalName;
  protected $sqlTableName;
  protected $sqlSelectAll;
  protected $sqlInsert;
  protected $sqlUpdate;
  protected $sqlFieldName;
  protected $sqlValueName;

  protected $defaults = array();

  protected function __construct($gamePrefix = 'sn_', $internalName = '', $tableName = '') {
    parent::__construct($gamePrefix.$internalName.'_');
    $this->internalName = $internalName;

    if(!$tableName) $tableName = $internalName;
    $this->sqlTableName = $tableName;
    $this->sqlSelectAll = "SELECT * FROM {{table}};";
    $this->sqlFieldName = $internalName.'_name';
    $this->sqlValueName = $internalName.'_value';

    if(!$this->_DB_LOADED)
      $this->db_loadAll();
  }

  public static function getInstance($gamePrefix = 'sn_', $internalName = '') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, $internalName);
    }
    return self::$cacheObject;
  }

  public function loadDefaults(){
    foreach($this->defaults as $defName => $defValue){
      $this->$defName = $defValue;
    }
  }

  public function db_loadAll(){
    $this->loadDefaults();

    $query = doquery($this->sqlSelectAll, $this->sqlTableName);
    while ( $row = mysql_fetch_assoc($query) ) {
      $this->$row[$this->sqlFieldName] = $row[$this->sqlValueName];
    }

    $this->_DB_LOADED = true;
  }

  public function db_saveItem($name, $value = NULL){
    if($name){
      if($value !== NULL)
        $this->$name = $value;

      doquery("REPLACE INTO {{table}} SET `{$this->sqlValueName}` = '{$this->$name}', `{$this->sqlFieldName}` = '{$name}';", $this->sqlTableName);
    };
  }

  public function db_loadItem($name){
    if($name){
      $qry = doquery("SELECT `{$this->sqlValueName}` FROM {{table}} WHERE `{$this->sqlFieldName}` = '{$name}';", $this->sqlTableName, true);
      $this->$name = $qry[$this->sqlValueName];

      return $this->$name;
    };
  }
}

class classConfig extends classPersistent {
  protected $defaults = array(
    'BannerOverviewFrame' => 1,
    'BuildLabWhileRun' => 0,
    'COOKIE_NAME' => "SuperNova",
    'crystal_basic_income' => 20,
    'debug' => 0,
    'Defs_Cdr' => 30,
    'deuterium_basic_income' => 0,
    'energy_basic_income' => 0,
    'Fleet_Cdr' => 30,
    'fleet_speed' => 2500,
    'ForumUserBarFrame' => 1,
    'forum_url' => "/forum/",
    'game_disable' => 0,
    'game_disable_reason' => "SuperNova is in maintenance mode! Please return later!",
    'game_name' => "SuperNova",
    'game_speed' => 2500,
    'initial_fields' => 163,
    'LastSettedGalaxyPos' => 0,
    'LastSettedPlanetPos' => 0,
    'LastSettedSystemPos' => 0,
    'metal_basic_income' => 40,
    'noobprotection' => 1,
    'noobprotectionmulti' => 5,
    'noobprotectiontime' => 5000,
    'resource_multiplier' => 1,
    'urlaubs_modus_erz' => 0,
    'users_amount' => 0,
    'game_adminEmail' => '',

    // Chat settings
    'chat_timeout' => 900, // in seconds. Default = 15 min

    // Game global settings
    'game_mode' => '0', // 0 - SuperNova, 1 - oGame
    'game_date_withTime' => 'd.m.Y h:i:s',

    'game_maxGalaxy' => '9',
    'game_maxSystem' => '499',
    'game_maxPlanet' => '15',

    // Interface - UserBanner
    'int_banner_showInOverview' => 1,
    'int_banner_background' => "images/banner.png",
    'int_banner_URL' => "/banner.php?type=banner",
    'int_banner_fontUniverse' => "cristal.ttf",
    'int_banner_fontRaids' => "klmnfp2005.ttf",
    'int_banner_fontInfo' => "terminator.ttf",

    // Interface - UserBar
    'int_userbar_showInOverview' => 1,
    'int_userbar_background' => "images/userbar.png",
    'int_userbar_URL' => "/banner.php?type=userbar",
    'int_userbar_font' => "arialbd.ttf",

    // Chat - formatting message for Admin
    'chat_admin_msgFormat' => '[c=purple]$2[/c]',

    //Roleplay system
    'rpg_cost_trader'    => 1,     // Trader trades between resources
    'rpg_cost_scraper'   => 1,     // Scrapper buys ship for fraction of cost
    'rpg_cost_stockman'  => 1,     // Stockman resells ship that was scrapped
    'rpg_cost_banker'    => 1,     // Banker can hold some resources
    'rpg_cost_exchange'  => 1,     // Exchange allows resource trade between players
    'rpg_cost_pawnshop'  => 1,     // You can get loan in pawnshop

    'rpg_exchange_metal' => 1,
    'rpg_exchange_crystal' => 2,
    'rpg_exchange_deuterium' => 4,
    'rpg_exchange_darkMatter' => 100000,

    'rpg_scrape_metal' => 0.75,
    'rpg_scrape_crystal' => 0.50,
    'rpg_scrape_deuterium' => 0.25,

    // Economy
    'eco_stockman_fleet' => '',

    // Statistic
    'stats_lastUpdated' => '0',
    'stats_schedule' => 'd@04:00:00',
  );

  public static function getInstance($gamePrefix = 'sn_') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, 'config');
    }
    return self::$cacheObject;
  }
}

class classVariables extends classPersistent {
  protected $defaults = array(
  );

  public static function getInstance($gamePrefix = 'sn_') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, 'var', 'variables');
    }
    return self::$cacheObject;
  }
}
?>