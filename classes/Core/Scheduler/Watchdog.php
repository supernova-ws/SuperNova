<?php
/**
 * Created by Gorlum 15.06.2017 5:07
 */

namespace Core\Scheduler;

use Exception;
use classConfig;
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

  /**
   * @var TaskConditional[] $taskList
   */
  protected $taskList = [];

  /**
   * Watchdog constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc     = $gc;
    $this->config = $this->gc->config;
  }

  /**
   * @param TaskConditional $task
   * @param string          $name Optional. Task name
   */
  public function register(TaskConditional $task, $name = '') {
    if (empty($name)) {
      $this->taskList[] = $task;
    } else {
      $this->taskList[$name] = $task;
    }
  }

  /**
   * @param $name
   *
   * @return TaskConditional|null
   */
  public function getTask($name) {
    return !empty($this->taskList[$name]) ? $this->taskList[$name] : null;
  }

  public function execute() {
    foreach ($this->taskList as $task) {
      try {
        $task();
      } catch (Exception $e) {
      }
    }
  }

  /**
   * @param string   $configName - config record name
   * @param int      $timeDiff   - interval from SN_TIME_NOW in seconds
   * @param callable $callable   - function to call when condition is met
   * @param int      $configType - type of config record - unixtime or Sql timestamp
   * @param bool     $forceLoad  - should config value be read from DB
   *
   * @deprecated
   */
  public function checkConfigTimeDiff($configName, $timeDiff, $callable, $configType = classConfig::DATE_TYPE_UNIX, $forceLoad = false) {
    $configValue = $forceLoad ? $this->config->db_loadItem($configName) : $this->config[$configName];
    $configType == classConfig::DATE_TYPE_SQL_STRING ? $configValue = strtotime($configValue, SN_TIME_NOW) : false;

    if (SN_TIME_NOW - $configValue > $timeDiff) {
      $callable();
    }

  }

}
