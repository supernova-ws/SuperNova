<?php
/**
 * Created by Gorlum 08.02.2020 15:31
 */

namespace Core\Scheduler;

use DBAL\db_mysql;
use Core\GlobalContainer;
use classConfig;
//use Timer;

/**
 * Scheduled task is something that need to be done withing certain schedule
 * Supports task locks
 *
 * @package Core
 */
class TaskConditional {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;
  /**
   * @var db_mysql
   */
  protected $db;
  /**
   * @var classConfig
   */
  protected $config;

  /**
   * Name of config field to monitor
   *
   * @var string $configName
   */
  protected $configName = '';
  /**
   * Force load config value from DB each check
   *
   * @var bool $forceConfigLoad
   */
  protected $forceConfigLoad = true;

  /**
   * Lock for a task
   *
   * @var null|Lock $lock
   */
  protected $lock = null;
  /**
   * Force to process task if lock not lifted but expired
   *
   * @var bool
   */
  protected $actionOnExpiredLock = Lock::LOCK_EXPIRED_IGNORE;

  /**
   * TaskConditional constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->gc     = $gc;
    $this->db     = $gc->db;
    $this->config = $gc->config;
  }

  /**
   * Invoking task
   *
   * Checking lock (if applicable) and task interval, getting lock, performing task, releasing lock
   *
   * @return bool|mixed Return FALSE if task not yet possible to execute or task() result
   */
  public function __invoke() {
    if (!$this->isTaskAllowed()) {
      // If no interval specified or no config field specified - nothing to do

//var_dump('TASK: missconfigured');
      return false;
    }
//Timer::mark('start');
    // Checking task lock
    if (is_object($this->lock) && !$this->lock->attemptLock(function () {
        return $this->proceedLockExpiration();
      }, SN_TIME_NOW)) {

//var_dump('TASK: Task still locked');
      return false;
    }
//Timer::mark('lock processed');

    if ($this->condition()) {
      $this->updateTaskLastRunTime(SN_TIME_NOW);
//Timer::mark('Time updated');
      // Performing task
      $result = $this->task();

//var_dump('TASK: performing task');
//if (isset($_GET['test'])) {
//  var_dump('TASK: Holding task...');
//  sleep(20);
//}

      $this->updateTaskLastRunTime(time());
//Timer::mark('Time updated-2');
    } else {
//var_dump('TASK: condition not met - Nothing to do');

      $result = false;
    }


    // Unlocking task if any
    if (is_object($this->lock)) {
//var_dump('TASK: unlocking');
      $this->lock->unLock(true);
    }
//Timer::mark('Unlocked');

//var_dump(Timer::getLog());

    return $result;
  }

  /**
   * Fast pre-checks if task allowed at all in current state
   *
   * @return bool
   */
  protected function isTaskAllowed() {
    return !empty($this->configName);
  }

  /**
   * This function called if task have a lock and lock grace period expired
   * Here can be added some checks and even recovery procedures for expired lock
   *
   * @return bool TRUE if task can proceed even on lock expiration time
   */
  protected function proceedLockExpiration() {
    return $this->actionOnExpiredLock;
  }

  /**
   * This is primary condition to check
   *
   * @return bool
   */
  protected function condition() {
    return false;
  }

  /**
   * Task to execute
   *
   * @return bool
   */
  protected function task() {
    return true;
  }

  /**
   * @param int $time
   */
  protected function updateTaskLastRunTime($time) {
    $this->db->transactionWrap(
      function () use ($time) {
        $this->config->dateWrite($this->configName, $time, classConfig::DATE_TYPE_SQL_STRING);
      }
    );
  }

}
