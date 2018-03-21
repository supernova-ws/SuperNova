<?php
/**
 * Created by Gorlum 15.06.2017 5:07
 */

namespace Core;


use Core\GlobalContainer;

class Watchdog {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var \classConfig $config
   */
  protected $config;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
    $this->config = $this->gc->config;
  }

  /**
   * @param string   $configName - config record name
   * @param int      $timeDiff - interval from SN_TIME_NOW in seconds
   * @param callable $callable - function to call when condition is met
   * @param int      $configType - type of config record - unixtime or Sql timestamp
   * @param bool     $forceLoad - should config value be read from DB
   */
  public function checkConfigTimeDiff($configName, $timeDiff, $callable, $configType = WATCHDOG_TIME_UNIX, $forceLoad = false) {
    $configValue = $forceLoad ? $this->config->db_loadItem($configName) : $this->config[$configName];
    $configType == WATCHDOG_TIME_SQL ? $configValue = strtotime($configValue, SN_TIME_NOW) : false;

    if(SN_TIME_NOW - $configValue > $timeDiff) {
      $callable();
    }

  }

}
