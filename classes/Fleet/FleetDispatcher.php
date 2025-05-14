<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */
/** @noinspection PhpDeprecationInspection */

/**
 * Created by Gorlum 15.06.2017 4:12
 */

namespace Fleet;

use Core\Scheduler\Lock;
use DBAL\db_mysql;
use SN;
use debug;
use classConfig;
use Core\GlobalContainer;

/**
 * Class Fleet\FleetDispatcher
 *
 */
class FleetDispatcher {
  const TASK_COMPLETE = 0;
//  const TASK_TERMINATED = 1;

//  const F_FLEET_EVENT = 'fleet_event';
  const F_FLEET_MISSION = 'fleet_mission';
//  /** @var array[] $fleet_list */
//  public static $fleet_list = [];
  /** @var FleetDispatchEvent[] $fleet_event_list */
  public static $fleet_event_list = [];
  /** @var int[] $missions_used */
  public $missions_used = [];
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var classConfig $gameConfig
   */
  protected $gameConfig;

  /**
   * @var debug $debug
   */
  protected $debug;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->gameConfig = $gc->config;
    $this->debug      = $gc->debug;
  }

//  /**
//   * @deprecated
//   */
//  public function dispatch() {
//    if (
//      SN::$options[PAGE_OPTION_FLEET_UPDATE_SKIP]
//      ||
//      SN::gameIsDisabled()
//      ||
//      !$this->getLockOld()
//    ) {
//      return;
//    }
//
//    $this->flt_flying_fleet_handler();
//
//    $this->releaseLock();
//
//    set_time_limit(60);
//  }


  /**
   * @return bool
   * @deprecated
   */
  protected function getLockOld() {
    db_mysql::db_transaction_start();

    // Watchdog timer
    if ($this->gameConfig->db_loadItem('fleet_update_lock')) {
//      var_dump($this->gameConfig->db_loadItem('fleet_update_lock'));
//      var_dump(SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock));
//      if (SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock) <= mt_rand(90, 120)) {
      if (SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock) <= mt_rand(20, 40)) {
        db_mysql::db_transaction_rollback();

        return false;
      } else {
        $this->debug->warning('Fleet dispatcher was locked too long - watchdog unlocked', 'FFH Error', 504);
      }
    }

    $this->gameConfig->db_saveItem('fleet_update_last', SN_TIME_SQL);
    $this->gameConfig->db_saveItem('fleet_update_lock', SN_TIME_SQL);
    db_mysql::db_transaction_commit();

    return true;
  }

  /**
   * @deprecated
   */
  protected function releaseLock() {
    db_mysql::db_transaction_start();
    $this->gameConfig->db_saveItem('fleet_update_lock', '');
    db_mysql::db_transaction_commit();
  }


  // ------------------------------------------------------------------

  /**
   * @return int|int[]
   */
  public function flt_flying_fleet_handler() {
//    $this->log_file('Dispatch started');
    $watchdog = new FleetWatchdog();
    if (($result = $watchdog->acquireLock()) == FleetWatchdog::TASK_ALREADY_LOCKED) {
      return $result;
    }

    $result = ['code' => self::TASK_COMPLETE];

    set_time_limit(max(3, SN::$gc->config->fleet_update_max_run_time - 3));

    //log_file('Начинаем обсчёт флотов');

//    $this->log_file('Обсчёт ракет');
    FleetDispatchEvent::$processedIPR = coe_o_missile_calculate();

    // Filling self::$fleet_event_list with FleetDispatchEvent
    self::$fleet_event_list = $this->getFleetEvents();
    $this->loadMissionFiles();

    $sn_groups_mission = sn_get_groups('missions');
    foreach (self::$fleet_event_list as $fleetEvent) {
      $result['code'] = $watchdog->begin($fleetEvent);
      if ($result['code'] === FleetWatchdog::TASK_TOO_LONG) {
        $result['message'] = $watchdog->getTerminationMessage();
        break;
      } elseif ($result['code'] === FleetWatchdog::FLEET_IS_EMPTY) {
        continue;
      }

      db_mysql::db_transaction_start();
      // Locking further fleet dispatcher tasks
      SN::$gc->config->pass()->fleet_update_last = date(FMT_DATE_TIME_SQL, time());

      // Locking all event-related records
      $fleetEvent->lockEventRecords();

      // Refreshing fleet record
      if (empty($fleetEvent->refreshFleet())) {
        // Fleet was destroyed in course of previous actions
        db_mysql::db_transaction_commit();
        continue;
      } elseif ($fleetEvent->event == EVENT_FLT_RETURN && $fleetEvent->fleet['fleet_mess'] == FLEET_STATUS_RETURNING) {
        // Fleet returns to planet
        RestoreFleetToPlanet($fleetEvent->fleet, true, false, true);
        db_mysql::db_transaction_commit();
        continue;
      } elseif ($fleetEvent->event == EVENT_FLT_ARRIVE && $fleetEvent->fleet['fleet_mess'] != FLEET_STATUS_FLYING) {
        // При событии EVENT_FLT_ARRIVE флот всегда должен иметь fleet_mess == 0 / FLEET_STATUS_FLYING
        // В противном случае это означает, что флот уже был обработан ранее - например, при САБе
        db_mysql::db_transaction_commit();
        continue;
      }

      // From now on we have only events of types [EVENT_FLT_ARRIVE, EVENT_FLT_ACCOMPLISH] and $fleet_row['fleet_mess'] == FLEET_STATUS_FLYING (0)

      // Here we refresh dstPlanetRow (by calling sys_o_get_updated() and using its result - so below this call we will have actual dst planet/dst user records
      // In same vein we refresh srcPlanetRow
      $fleetEvent->refreshMissionData();

      switch ($fleetEvent->missionId) {
        // Для боевых атак нужно обновлять по САБу и по холду - таки надо возвращать данные из обработчика миссий!
        case MT_ATTACK:
        case MT_AKS:
        case MT_DESTROY:
          flt_mission_attack($fleetEvent);
        break;

        case MT_TRANSPORT:
          flt_mission_transport($fleetEvent);
        break;

        case MT_HOLD:
          flt_mission_hold($fleetEvent);
        break;

        case MT_RELOCATE:
          flt_mission_relocate($fleetEvent);
        break;

        case MT_EXPLORE:
          $outcome = new MissionExploreResult();
          $outcome->flt_mission_explore($fleetEvent);
        break;

        case MT_RECYCLE:
          flt_mission_recycle($fleetEvent);
        break;

        case MT_COLONIZE:
          flt_mission_colonize($fleetEvent);
        break;

        case MT_SPY:
          require_once(SN_ROOT_PHYSICAL . 'includes/includes/coe_simulator_helpers.php');

          $theMission = MissionEspionage::buildFromArray($fleetEvent);
          $theMission->flt_mission_spy();

          unset($theMission);
        break;

        case MT_MISSILE:  // Missiles !!
        break;

//      default:
//        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
//      break;
      }
      db_mysql::db_transaction_commit();
    }

    $watchdog->unlock();

//    $that->log_file('Dispatch finished - NORMAL SHUTDOWN');

    return $result;
  }

  /**
   * @param $workTime
   * @param $eventsProcessed
   * @param $lastMissionId
   * @param $lastEventId
   * @param $lastEventLength
   * @param $totalEvents
   */
  public function logTermination($workTime, $eventsProcessed, $lastMissionId, $lastEventId, $lastEventLength, $totalEvents) {
    SN::$debug->warning(sprintf(
      'Flying fleet handler works %1$s (> %2$s) seconds - skip rest. Processed %3$d / %7$d events. Last event: mission %4$s event %6$s (%5$ss)',
      number_format($workTime, 4),
      SN::$config->fleet_update_dispatch_time,
      $eventsProcessed,
      $lastMissionId ? SN::$lang['type_mission'][$lastMissionId] : '!TERMINATED BY TIMEOUT!',
      number_format($lastEventLength, 4),
      $lastEventId ? SN::$lang['fleet_events'][$lastEventId] : '!TERMINATED BY TIMEOUT!',
      $totalEvents
    ),
      'FFH Warning',
      504
    );
  }

  /**
   * @return Lock
   */
  public function buildLock() {
    return new Lock($this->gc, classConfig::FLEET_UPDATE_RUN_LOCK, SN::$gc->config->fleet_update_max_run_time, 1, classConfig::DATE_TYPE_UNIX);
  }

  public function getFleetEvents() {
    $fleet_event_list = [];

    // Gets active fleets on current tick for Flying Fleet Handler
    $fleet_list_current_tick = DbFleetStatic::db_fleet_list(
      "
        (`fleet_start_time` <= " . SN_TIME_NOW . " AND `fleet_mess` = 0)
        OR
        (`fleet_end_stay` <= " . SN_TIME_NOW . " AND `fleet_end_stay` > 0 AND `fleet_mess` = 0)
        OR
        (`fleet_end_time` <= " . SN_TIME_NOW . ")
      ", DB_SELECT_PLAIN
    );

    foreach ($fleet_list_current_tick as $fleet_row) {
      if ($fleet_row['fleet_start_time'] <= SN_TIME_NOW && $fleet_row['fleet_mess'] == 0) {
        $fleet_event_list[] = new FleetDispatchEvent($fleet_row, EVENT_FLT_ARRIVE);
      }

      if ($fleet_row['fleet_end_stay'] > 0 && $fleet_row['fleet_end_stay'] <= SN_TIME_NOW && $fleet_row['fleet_mess'] == 0) {
        $fleet_event_list[] = new FleetDispatchEvent($fleet_row, EVENT_FLT_ACCOMPLISH);
      }

      if ($fleet_row['fleet_end_time'] <= SN_TIME_NOW) {
        $fleet_event_list[] = new FleetDispatchEvent($fleet_row, EVENT_FLT_RETURN);
      }

      $this->missions_used[$fleet_row[self::F_FLEET_MISSION]] = 1;
    }

    FleetDispatchEvent::sortEvents($fleet_event_list);

    return $fleet_event_list;
  }

  /**
   * @return void
   */
  public function loadMissionFiles() {
    $mission_files = [
      MT_ATTACK  => 'flt_mission_attack',
      MT_AKS     => 'flt_mission_attack',
      MT_DESTROY => 'flt_mission_attack',

      MT_TRANSPORT => 'flt_mission_transport',
      MT_RELOCATE  => 'flt_mission_relocate',
      MT_HOLD      => 'flt_mission_hold',
      MT_SPY       => '',
      MT_COLONIZE  => 'flt_mission_colonize',
      MT_RECYCLE   => 'flt_mission_recycle',
      // MT_MISSILE => 'flt_mission_missile.php',
      // MT_EXPLORE   => 'flt_mission_explore',
    ];
    foreach ($this->missions_used as $mission_id => $cork) {
      if (!empty($mission_files[$mission_id])) {
        require_once(SN_ROOT_PHYSICAL . "includes/includes/{$mission_files[$mission_id]}" . DOT_PHP_EX);
      }
    }
  }

  /**
   * @param string $msg
   *
   * @noinspection PhpUnused
   */
  public function log_file($msg) {
    file_put_contents(__DIR__ . '/../../.ffh-event.log', date(FMT_DATE_TIME_SQL, time()) . ' ' . $msg . "\r\n", FILE_APPEND);
  }

}
