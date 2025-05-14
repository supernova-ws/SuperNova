<?php
/** Created by Gorlum 27.10.2024 18:43 */

namespace Fleet;

use Core\Scheduler\Lock;
use SN;

class FleetWatchdog {
  /** @var $workBegin */
  public static $workBegin;


  /** @var float $eventStartedAt */
  public static $eventStartedAt = SN_TIME_NOW;
//  /** @var float $lastEventEnd */
//  public static $lastEventEnd = SN_TIME_NOW;

  /** @var int $currentMission */
  public static $currentMission = MT_NONE;
  /** @var int $eventsProcessed */
  public static $eventsProcessed = 0;
  /** @var int $currentEvent */
  public static $currentEvent = EVENT_FLEET_NONE;
  /** @var int $processedIPR Processed IPR on this run */
  public static $processedIPR = -1;

  const TASK_COMPLETE = 0;
  const CONTINUE_EXECUTION = 2;
  const TASK_TOO_LONG = 1;
  const TASK_ALREADY_LOCKED = -1;
  const LOCK_ACQUIRED = -2;
  const FLEET_IS_EMPTY = 'FLEET_EMPTY';

  const EVENT_DISPATCH_STARTED = 'EVENT_DISPATCH_STARTED';
  /** @var Lock $runLock */
  private static $runLock;


  public function __construct() {
    // Dispatch started
    self::$workBegin = microtime(true);

    self::$eventStartedAt  = self::$workBegin;
    self::$currentMission  = MT_NONE;
    self::$currentEvent    = EVENT_FLEET_NONE;
    self::$eventsProcessed = 0;
  }

  /**
   * @return int
   */
  public function acquireLock() {
    // Trying to acquire lock for current task
    self::$runLock = $runLock = SN::$gc->fleetDispatcher->buildLock();
    if (!$runLock->attemptLock()) {
      return self::TASK_ALREADY_LOCKED;
    }

    register_shutdown_function(function () use ($runLock) {
//      $this->log_file('Shutting down');
      $timeLock = $runLock->isLocked();
      if ($timeLock > 0 || $timeLock === 0) {
        $runLock->unLock(true);
        $this->logTermination();
//        $this->log_file('UNLOCKING');
      }
//      $this->log_file(SN::$gc->config->pass()->fleet_update_run_lock);
//      $this->log_file('ALL RELEASED');
    });

    return self::LOCK_ACQUIRED;
  }

  public function begin(FleetDispatchEvent $fleetEvent) {
    // Watchdog timer
    // If flying fleet handler works more than `fleet_update_dispatch_time` seconds - stopping it
    // Let next run handle rest of fleets
// var_dump(microtime(true), self::$workBegin, (microtime(true) - self::$workBegin) >= SN::$config->fleet_update_dispatch_time, $fleetEvent->fleet);
    if ((microtime(true) - self::$workBegin) >= SN::$config->fleet_update_dispatch_time) {
      $this->logTermination();

      return FleetWatchdog::TASK_TOO_LONG;
    }

    if (empty($fleetEvent->fleet)) {
      // Fleet was destroyed in course of previous actions
      return FleetWatchdog::FLEET_IS_EMPTY;
    }

    self::$processedIPR = $fleetEvent::$processedIPR;

    self::$eventStartedAt = microtime(true);
    self::$currentMission = !empty($fleetEvent->fleet[FleetDispatcher::F_FLEET_MISSION]) ? $fleetEvent->fleet[FleetDispatcher::F_FLEET_MISSION] : MT_NONE;
    self::$currentEvent   = !empty($fleetEvent->event) ? $fleetEvent->event : MT_NONE;
    self::$eventsProcessed++;

    return FleetWatchdog::CONTINUE_EXECUTION;
  }

  /**
   */
  public function logTermination() {
    SN::$debug->warning(
      $this->getTerminationMessage(),
      'FFH Warning',
      504
    );
  }

  public function unlock() {
    self::$runLock->unLock(true);
  }

  /**
   * @return string
   */
  public function getTerminationMessage() {
    return sprintf(
      'Flying fleet handler works %1$s seconds (> %2$s) - skipping rest. Processed %8$d IPRs, %3$d / %7$d events. Last event: mission %4$s event %6$s (%5$ss)',
      number_format(microtime(true) - self::$workBegin, 4),
      SN::$config->fleet_update_dispatch_time,
      self::$eventsProcessed,
      !empty(SN::$lang['type_mission'][self::$currentMission]) ? SN::$lang['type_mission'][self::$currentMission] : '!TERMINATED BY TIMEOUT!',
      number_format(microtime(true) - self::$eventStartedAt, 4),
      !empty(SN::$lang['fleet_events'][self::$currentEvent]) ? SN::$lang['fleet_events'][self::$currentEvent] : '!TERMINATED BY TIMEOUT!',
      count(FleetDispatcher::$fleet_event_list),
      self::$processedIPR
    );
  }

}
