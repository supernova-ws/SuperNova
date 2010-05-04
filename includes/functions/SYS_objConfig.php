<?php
class objCache {
  protected static $cacheObject;

  // -1 - not initialized
  // 0 - no cache - array() used
  // 1 - xCache
  protected static $mode = -1;
  protected static $data;
  protected static $prefix;

  private function __construct($prefIn = 'CACHE_')
  {
    self::$prefix = $prefIn;
    if ( extension_loaded('xcache')){
      self::$mode = 1;
    }else{
      self::$mode = 0;
      self::$data = array();
    };
  }

  public final function __clone()
  {
      // throw new BadMethodCallException("Clone is not allowed");
  }

  public static function getInstance($prefIn = 'CACHE_')
  {
    if (!isset(self::$cacheObject)) {
      self::$cacheObject = new objCache($prefIn);
    }
    return self::$cacheObject;
  }

  public static function getMode()
  {
    return self::$mode;
  }

  public function __set($name, $value)
  {
    if($name == 'prefix'){
      self::$prefix = $value;
    }else{
      switch (self::$mode) {
        case 0:
          self::$data[self::$prefix.$name] = $value;
          break;
        case 1:
          xcache_set(self::$prefix.$name, $value);
          break;
      };
    }
  }

  public function __get($name)
  {
    if($name == 'prefix'){
      return self::$prefix;
    }else{
      switch (self::$mode) {
        case 0:
          return self::$data[self::$prefix.$name];
        case 1:
          return xcache_get(self::$prefix.$name);
      };
    };
  }

  public function __isset($name)
  {
    switch (self::$mode) {
      case 0:
        return isset(self::$data[self::$prefix.$name]);
        break;
      case 1:
        return xcache_isset(self::$prefix.$name);
        break;
    };
  }

  public function __unset($name)
  {
    switch (self::$mode) {
      case 0:
        unset(self::$data[self::$prefix.$name]);
        break;
      case 1:
        xcache_unset(self::$prefix.$name);
        break;
    };
  }

  public function dumpData(){
    pdump(self::$data);
  }

  public function getPrefix(){
    return self::$prefix;
  }
}

class objConfig extends objCache {
  private static $defaults = array(
    'BannerOverviewFrame' => 1,
    'BuildLabWhileRun' => 0,
    'close_reason' => "SuperNova is in maintenance mode! Please return later!",
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
    'OverviewBanner' => 1,
    'OverviewClickBanner' => "",
    'OverviewExternChat' => 0,
    'OverviewExternChatCmd' => "",
    'OverviewNewsFrame' => "1",
    'OverviewNewsText' => "Welcome to SuperNova!",
    'resource_multiplier' => 1,
    'urlaubs_modus_erz' => 0,
    'users_amount' => 0,

    'int_banner_showInOverview' => 1,
    'int_banner_background' => "images/bann.png",
    'int_banner_URL' => "/banner.php",

    'int_userBar_showInOverview' => 1,
    'int_userBar_background' => "images/userbar.png",
    'int_userBar_URL' => "/banner.php"
  );


  public static function getInstance()
  {
    if (!isset(self::$cacheObject)) {
      self::$cacheObject = new objConfig;
    }
    return self::$cacheObject;
  }

  private function __construct()
  {
    parent::__construct('config_');
    $query = doquery("SELECT * FROM {{table}}",'config');
    while ( $row = mysql_fetch_assoc($query) ) {
      $this->$row['config_name'] = $row['config_value'];
    }
    foreach(self::$defaults as $defName => $defValue)
      if(!isset($this->$defName))
        $this->$defName = $defValue;
  }
}
?>