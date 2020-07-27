<?php
/**
 * Created by Gorlum 08.02.2020 16:42
 */

namespace Core\Scheduler;

use classConfig;
use Core\GlobalContainer;
use DBAL\db_mysql;

/**
 * Locking mechanic
 *
 * @package Core\Scheduler
 */
class Lock {
  /**
   * Skip task execution if task is locked but lock grace period is expired
   */
  const LOCK_EXPIRED_SKIP_TASK = 0;
  /**
   * Treat lock expiration as previous task fail and process task
   */
  const LOCK_EXPIRED_IGNORE = 1;


  /**
   * @var GlobalContainer $gc
   */
  protected $gc;
  /**
   * @var db_mysql
   */
  protected $db;
  /**
   * @var classConfig $config
   */
  protected $config;

  /**
   * Name for config lock field
   *
   * @var string $configName
   */
  protected $configName = '';
  /**
   * Interval to lock task
   *
   * @var int $maxLockInterval
   */
  protected $maxLockInterval = PERIOD_MINUTE;
  /**
   * Delta for lock to minimize chance of simulate access/lock race
   *
   * @var int $intervalDelta
   */
  protected $intervalDelta = 0;
  protected $lockType = classConfig::DATE_TYPE_SQL_STRING;

  /**
   * Lock constructor.
   *
   * @param GlobalContainer $gc
   * @param string          $configName
   * @param int             $maxLockInterval
   * @param int             $intervalDelta
   * @param int             $lockType
   */
  public function __construct($gc, $configName, $maxLockInterval = PERIOD_MINUTE, $intervalDelta = 10, $lockType = classConfig::DATE_TYPE_SQL_STRING) {
    $this->gc     = $gc;
    $this->db = $this->gc->db;
    $this->config = $this->gc->config;

    $this->configName      = $configName;
    $this->maxLockInterval = $maxLockInterval;
    $this->intervalDelta   = $intervalDelta;
    $this->lockType        = $lockType;
  }

  /**
   * Checking for lock
   *
   * @return int|null Lock time left. Negative if lock time passed. Lock released immediately if SN_TIME_NOW === lockTime
   */
  public function isLocked($time = SN_TIME_NOW) {
    $timeLeft = null;
    if ($this->configName) {
      $lockedUntil = $this->config->dateRead($this->configName, classConfig::DATE_TYPE_UNIX);

      if ($lockedUntil == 0) {
        // There is no current lock - nothing to do
        $timeLeft = null;
      } else {
        $timeLeft = $lockedUntil - $time;
      }
//var_dump('LOCK: ' . ($timeLeft !== null ? "lock present - left {$timeLeft}s" : 'no lock'));
    }

    return $timeLeft;
  }

  /**
   * Placing lock
   */
  public function lock($time = SN_TIME_NOW) {
    $toWrite = $time + mt_rand($this->maxLockInterval - $this->intervalDelta, $this->maxLockInterval + $this->intervalDelta);
    $this->configWrite($toWrite);

    return $this;
  }

  /**
   * Removing lock
   */
  public function unLock($selfTransaction = true) {
    $selfTransaction ? $this->db->transactionStart() : false;
    $this->configWrite(0);
    $selfTransaction ? $this->db->transactionCommit() : false;

    return $this;
  }

  /**
   * @param $data
   */
  protected function configWrite($data) {
    if ($this->configName) {
      $this->config->dateWrite($this->configName, $data, classConfig::DATE_TYPE_SQL_STRING);
    }
  }

  /**
   * Attempt to acquire lock
   *
   * @param callable|null $callable External function to determine how to react on lock expiration. On `null` - task is always unlocked on lock expired
   *
   * @return bool TRUE if lock was placed FALSE if lock was already enabled
   */
  public function attemptLock($callable = null, $time = SN_TIME_NOW) {
    $this->db->transactionStart();
    $lockTimeLeft = $this->isLocked($time);

    // Task is still locked
    if ($lockTimeLeft > 0 || $lockTimeLeft === 0) {
//var_dump('LOCK: Task still locked');
      $this->db->transactionRollback();

      return false;
    }

    // Task lock grace period expired but further processing is locked by caller
    if (
      $lockTimeLeft < 0
      &&
      (
        is_callable($callable)
        &&
        (($q = $callable()) === self::LOCK_EXPIRED_SKIP_TASK)
      )
    ) {
//var_dump('LOCK: Task lock expired but task is skipped');

      // Then we just rolling back transaction and returning 'false'
      $this->db->transactionRollback();

      return false;
    }
//if (
//  $lockTimeLeft < 0){
//  var_dump('LOCK: lock expired - we should re-lock');
//}
//var_dump('LOCK: ' . ($lockTimeLeft > 0 ? 'task still locked' : ($lockTimeLeft < 0 ? 'lock expired, unlocking' : 'not locked')));

    // Placing task lock
    $this->lock();

    $this->db->transactionCommit();

    return true;
  }

}
