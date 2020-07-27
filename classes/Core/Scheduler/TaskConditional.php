<?php
/**
 * Created by Gorlum 08.02.2020 15:31
 */

namespace Core\Scheduler;

use DBAL\db_mysql;
use Core\GlobalContainer;
use classConfig;
use SN;

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
   * @param GlobalContainer|null $gc
   */
  public function __construct($gc = null) {
    if (!is_object($gc)) {
      $this->gc = SN::$gc;
    } else {
      $this->gc = $gc;
    }
    $this->db     = $this->gc->db;
    $this->config = $this->gc->config;
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
      return false;
    }

    // Checking task lock
    if (is_object($this->lock) && !$this->lock->attemptLock(function () {
        return $this->proceedLockExpiration();
      }, SN_TIME_NOW)) {

      return false;
    }

    if ($this->condition()) {
      $this->updateTaskLastRunTime(SN_TIME_NOW);
      // Performing task
      $result = $this->task();

      $this->updateTaskLastRunTime(time());
    } else {
      $result = false;
    }

    // Unlocking task if any
    if (is_object($this->lock)) {
      $this->lock->unLock(true);
    }

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
