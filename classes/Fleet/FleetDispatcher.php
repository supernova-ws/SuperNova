<?php
/**
 * Created by Gorlum 15.06.2017 4:12
 */

namespace Fleet;

use Core\Scheduler\Lock;
use SN;
use debug;
use classConfig;
use Core\GlobalContainer;
use DBAL\OldDbChangeSet;
use Planet\DBStaticPlanet;

/**
 * Class Fleet\FleetDispatcher
 *
 */
class FleetDispatcher {
  const TASK_COMPLETE = 0;
  const TASK_TERMINATED = 1;
  const TASK_ALREADY_LOCKED = -1;
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

  /**
   * List of timed events
   *
   * [
   *   'start'
   *   'finish'
   * ]
   *
   * @var float[] $timers
   */
  protected $timers;


  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->gameConfig = $gc->config;
    $this->debug      = $gc->debug;
  }

  /**
   * @deprecated
   */
  public function dispatch() {
    if (
      SN::$options[PAGE_OPTION_FLEET_UPDATE_SKIP]
      ||
      SN::gameIsDisabled()
      ||
      !$this->getLockOld()
    ) {
      return;
    }

    $this->flt_flying_fleet_handler();

    $this->releaseLock();

    set_time_limit(60);
  }


  /**
   * @return bool
   * @deprecated
   */
  protected function getLockOld() {
    sn_db_transaction_start();

    // Watchdog timer
    if ($this->gameConfig->db_loadItem('fleet_update_lock')) {
//      var_dump($this->gameConfig->db_loadItem('fleet_update_lock'));
//      var_dump(SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock));
//      if (SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock) <= mt_rand(90, 120)) {
      if (SN_TIME_NOW - strtotime($this->gameConfig->fleet_update_lock) <= mt_rand(20, 40)) {
        sn_db_transaction_rollback();

        return false;
      } else {
        $this->debug->warning('Fleet dispatcher was locked too long - watchdog unlocked', 'FFH Error', 504);
      }
    }

    $this->gameConfig->db_saveItem('fleet_update_last', SN_TIME_SQL);
    $this->gameConfig->db_saveItem('fleet_update_lock', SN_TIME_SQL);
    sn_db_transaction_commit();

    return true;
  }

  /**
   * @deprecated
   */
  protected function releaseLock() {
    sn_db_transaction_start();
    $this->gameConfig->db_saveItem('fleet_update_lock', '');
    sn_db_transaction_commit();
  }

  /**
   * Gets active fleets on current tick for Flying Fleet Handler
   *
   * @return array
   */
  protected function fleet_list_current_tick() {
    return DbFleetStatic::db_fleet_list(
      "
    (`fleet_start_time` <= " . SN_TIME_NOW . " AND `fleet_mess` = 0)
    OR
    (`fleet_end_stay` <= " . SN_TIME_NOW . " AND `fleet_end_stay` > 0 AND `fleet_mess` = 0)
    OR
    (`fleet_end_time` <= " . SN_TIME_NOW . ")"
      , DB_SELECT_PLAIN
    );
  }


  // ------------------------------------------------------------------
  public function flt_flying_fleet_handler() {
    /*

    [*] Нужно ли заворачивать ВСЕ в одну транзакцию?
        С одной стороны - да, что бы данные были гарантированно на момент снапшота
        С другой стороны - нет, потому что при большой активности это все будет блокировать слишком много рядов, да и таймаут будет большой для ожидания всего разлоченного
        Стоит завернуть каждую миссию отдельно? Это сильно увеличит количество запросов, зато так же сильно снизит количество блокировок.

        Resume: НЕТ! Надо оставить все в одной транзакции! Так можно будет поддерживать consistency кэша. Там буквально сантисекунды блокировки

    [*] Убрать кэшированние данных о пользователях и планета. Офигенно освободит память - проследить!
        НЕТ! Считать, скольким флотам нужна будет инфа и кэшировать только то, что используется больше раза!
        Заодно можно будет исключить перересчет очередей/ресурсов - сильно ускорит дело!
        Особенно будет актуально, когда все бонусы будут в одной таблице
        Ну и никто не заставляет как сейчас брать ВСЕ из таблицы - только по полям. Гемор, но не сильный - сделать запрос по группам sn_data
        И писать в БД только один раз результат

    [*] Нужно ли на этом этапе знать полную информацию о флотах?
        Заблокировать флоты можно и неполным запросом. Блокировка флотов - это не страшно. Ну, не пройдет одна-две отмены - так никто и не гарантировал реалтайма!
        С одной стороны - да, уменьшит количество запросов
        С другой стооны - расход памяти
        Все равно надо будет знать полную инфу о флоте в момент обработки

    [*] Сделать тестовую БД для расчетов

    [*] Но не раньше, чем переписать все миссии

    */
//    $this->log_file('Dispatch started');

    // Trying to acquire lock for current task
    $runLock = $this->buildLock();
    if (!$runLock->attemptLock()) {
//      $this->log_file('Dispatch stopped: lock ' . $runLock->isLocked() .'s' );
      return self::TASK_ALREADY_LOCKED;
    }
    register_shutdown_function(function () use ($runLock) {
//      $this->log_file('Shutting down');
      $timeLock = $runLock->isLocked();
      if ($timeLock > 0 || $timeLock === 0) {
        $runLock->unLock(true);
        $this->logTermination(0, 0, 0, 0, 0);
//        $this->log_file('UNLOCKING');
      }

//      $this->log_file(SN::$gc->config->pass()->fleet_update_run_lock);
//      $this->log_file('ALL RELEASED');
    });

    $result = self::TASK_COMPLETE;

    set_time_limit(max(3, SN::$gc->config->fleet_update_max_run_time - 3));

    $workBegin = microtime(true);
//log_file('Начинаем обсчёт флотов');

//log_file('Обсчёт ракет');
    sn_db_transaction_start();
    coe_o_missile_calculate();
    sn_db_transaction_commit();

    $fleet_list       = array();
    $fleet_event_list = array();
    $missions_used    = array();

    $fleet_list_current_tick = $this->fleet_list_current_tick();
    foreach ($fleet_list_current_tick as $fleet_row) {
//      set_time_limit(15);

      // TODO - Унифицировать код с темплейтным разбором эвентов на планете!
      $fleet_list[$fleet_row['fleet_id']]         = $fleet_row;
      $missions_used[$fleet_row['fleet_mission']] = 1;
      if ($fleet_row['fleet_start_time'] <= SN_TIME_NOW && $fleet_row['fleet_mess'] == 0) {
        $fleet_event_list[] = array(
          'fleet_row'   => &$fleet_list[$fleet_row['fleet_id']],
          'fleet_time'  => $fleet_list[$fleet_row['fleet_id']]['fleet_start_time'],
          'fleet_event' => EVENT_FLT_ARRIVE,
        );
      }

      if ($fleet_row['fleet_end_stay'] > 0 && $fleet_row['fleet_end_stay'] <= SN_TIME_NOW && $fleet_row['fleet_mess'] == 0) {
        $fleet_event_list[] = array(
          'fleet_row'   => &$fleet_list[$fleet_row['fleet_id']],
          'fleet_time'  => $fleet_list[$fleet_row['fleet_id']]['fleet_end_stay'],
          'fleet_event' => EVENT_FLT_ACOMPLISH,
        );
      }

      if ($fleet_row['fleet_end_time'] <= SN_TIME_NOW) {
        $fleet_event_list[] = array(
          'fleet_row'   => &$fleet_list[$fleet_row['fleet_id']],
          'fleet_time'  => $fleet_list[$fleet_row['fleet_id']]['fleet_end_time'],
          'fleet_event' => EVENT_FLT_RETURN,
        );
      }
    }

//    set_time_limit(5);

//log_file('Сортировка и подгрузка модулей');
    uasort($fleet_event_list, array($this, 'flt_flyingFleetsSort'));
//  unset($fleets_query);

// TODO: Грузить только используемые модули из $missions_used
    $mission_files = array(
      MT_ATTACK  => 'flt_mission_attack',
      MT_AKS     => 'flt_mission_attack',
      // MT_DESTROY => 'flt_mission_destroy.php',
      MT_DESTROY => 'flt_mission_attack',

      MT_TRANSPORT => 'flt_mission_transport',
      MT_RELOCATE  => 'flt_mission_relocate',
      MT_HOLD      => 'flt_mission_hold',
      MT_SPY       => '',
      MT_COLONIZE  => 'flt_mission_colonize',
      MT_RECYCLE   => 'flt_mission_recycle',
//    MT_MISSILE => 'flt_mission_missile.php',
      MT_EXPLORE   => 'flt_mission_explore',
    );
    foreach ($missions_used as $mission_id => $cork) {
      if (empty($mission_files[$mission_id])) {
        continue;
      }

      require_once(SN_ROOT_PHYSICAL . "includes/includes/{$mission_files[$mission_id]}" . DOT_PHP_EX);
    }

//log_file('Обработка миссий');
    $lastEventBegin  = microtime(true);
    $lastMission     = MT_NONE;
    $eventsProcessed = 0;
    $lastEvent       = EVENT_FLEET_NONE;

    $sn_groups_mission = sn_get_groups('missions');
    foreach ($fleet_event_list as $fleet_event) {
      $lastEventEnd = microtime(true);
      // Watchdog timer
      // If flying fleet handler works more then 10 seconds - stopping it
      // Let next run handle rest of fleets
      $workTime = microtime(true) - $workBegin;
      if ($workTime > GAME_FLEET_HANDLER_MAX_TIME) {
        $this->logTermination($workTime, $eventsProcessed, $lastMission, $lastEvent, $lastEventEnd - $lastEventBegin);

        $result = self::TASK_TERMINATED;
        break;
      }

      // TODO: Указатель тут потом сделать
      // TODO: СЕЙЧАС НАДО ПРОВЕРЯТЬ ПО БАЗЕ - А ЖИВОЙ ЛИ ФЛОТ?!
      $fleet_row = $fleet_event['fleet_row'];
      if (!$fleet_row) {
        // Fleet was destroyed in course of previous actions
        continue;
      }

      $lastEventBegin = microtime(true);
      $lastMission    = $fleet_row['fleet_mission'];
      $lastEvent      = $fleet_event['fleet_event'];
      $eventsProcessed++;

//log_file('Миссия');
      // TODO Обернуть всё в транзакции. Начинать надо заранее, блокируя все таблицы внутренним локом SELECT 1 FROM {{users}}
      sn_db_transaction_start();
      // а текущее время
      SN::$gc->config->db_saveItem('fleet_update_last', date(FMT_DATE_TIME_SQL, time()));

      $mission_data = $sn_groups_mission[$fleet_row['fleet_mission']];
      // Формируем запрос, блокирующий сразу все нужные записи

      DbFleetStatic::db_fleet_lock_flying($fleet_row['fleet_id'], $mission_data);

//    $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE fleet_id = {$fleet_row['fleet_id']} FOR UPDATE", true);
      $fleet_row = DbFleetStatic::db_fleet_get($fleet_row['fleet_id']);
      if (!$fleet_row || empty($fleet_row)) {
        // Fleet was destroyed in course of previous actions
        sn_db_transaction_commit();
        continue;
      }

      if ($fleet_event['fleet_event'] == EVENT_FLT_RETURN) {
        // Fleet returns to planet
        RestoreFleetToPlanet($fleet_row, true, false, true);
        sn_db_transaction_commit();
        continue;
      }

      if ($fleet_event['fleet_event'] == EVENT_FLT_ARRIVE && $fleet_row['fleet_mess'] != 0) {
        // При событии EVENT_FLT_ARRIVE флот всегда должен иметь fleet_mess == 0
        // В противном случае это означает, что флот уже был обработан ранее - например, при САБе
        sn_db_transaction_commit();
        continue;
      }

      // TODO: Здесь тоже указатели
      // TODO: Кэширование
      // TODO: Выбирать только нужные поля

      // шпионаж не дает нормальный ID fleet_end_planet_id 'dst_planet'
      $mission_data = array(
        'fleet'       => &$fleet_row,
        'dst_user'    => $mission_data['dst_user'] || $mission_data['dst_planet'] ? db_user_by_id($fleet_row['fleet_target_owner'], true) : null,
        // TODO 'dst_planet' => $mission_data['dst_planet'] ? db_planet_by_id($fleet_row['fleet_end_planet_id'], true) : null,
        'dst_planet'  => $mission_data['dst_planet'] ? DBStaticPlanet::db_planet_by_vector($fleet_row, 'fleet_end_', true, '`id`, `id_owner`, `name`') : null,
        'src_user'    => $mission_data['src_user'] || $mission_data['src_planet'] ? db_user_by_id($fleet_row['fleet_owner'], true) : null,
        // TODO 'src_planet' => $mission_data['src_planet'] ? db_planet_by_id($fleet_row['fleet_start_planet_id'], true) : null,
        'src_planet'  => $mission_data['src_planet'] ? DBStaticPlanet::db_planet_by_vector($fleet_row, 'fleet_start_', true, '`id`, `id_owner`, `name`') : null,
        'fleet_event' => $fleet_event['fleet_event'],
      );

      if ($mission_data['dst_planet']) {
        // $mission_data['dst_planet'] = sys_o_get_updated($mission_data['dst_user'], $mission_data['dst_planet']['id'], $fleet_row['fleet_start_time']);
        if ($mission_data['dst_planet']['id_owner']) {
          $mission_data['dst_planet'] = sys_o_get_updated($mission_data['dst_planet']['id_owner'], $mission_data['dst_planet']['id'], $fleet_row['fleet_start_time']);
        }
        $mission_data['dst_user']   = $mission_data['dst_user'] ? $mission_data['dst_planet']['user'] : null;
        $mission_data['dst_planet'] = $mission_data['dst_planet']['planet'];
      }

      switch ($fleet_row['fleet_mission']) {
        // Для боевых атак нужно обновлять по САБу и по холду - таки надо возвращать данные из обработчика миссий!
        case MT_AKS:
        case MT_ATTACK:
        case MT_DESTROY:
          $attack_result  = flt_mission_attack($mission_data);
          $mission_result = CACHE_COMBAT;
        break;

        /*
        case MT_DESTROY:
          $attack_result = flt_mission_destroy($mission_data);
          $mission_result = CACHE_COMBAT;
        break;
        */

        case MT_TRANSPORT:
          $mission_result = flt_mission_transport($mission_data);
        break;

        case MT_HOLD:
          $mission_result = flt_mission_hold($mission_data);
        break;

        case MT_RELOCATE:
          $mission_result = flt_mission_relocate($mission_data);
        break;

        case MT_EXPLORE:
          $mission_result = flt_mission_explore($mission_data);
//          $theMission = \Fleet\MissionExplore::buildFromArray($mission_data);
//          $theMission->process();
//
//          unset($theMission);
        break;

        case MT_RECYCLE:
          $mission_result = flt_mission_recycle($mission_data);
        break;

        case MT_COLONIZE:
          $mission_result = flt_mission_colonize($mission_data);
        break;

        case MT_SPY:
          require_once(SN_ROOT_PHYSICAL . 'includes/includes/coe_simulator_helpers.php');

          $theMission = MissionEspionage::buildFromArray($mission_data);
          $theMission->flt_mission_spy();

          unset($theMission);
        break;

        case MT_MISSILE:  // Missiles !!
        break;

//      default:
//        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
//      break;
      }
      sn_db_transaction_commit();
    }

//    set_time_limit(30); // TODO - Optimize

    $runLock->unLock(true);

//    $that->log_file('Dispatch finished - NORMAL SHUTDOWN');

    return $result;

//  log_file('Закончили обсчёт флотов');
  }


  /**
   * @param array $fleet_row
   * @param bool  $start
   * @param bool  $only_resources
   * @param bool  $safe_fleet
   * @param mixed $result
   *
   * @return int
   * @deprecated
   */
  public function sn_RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false, &$result) {
    sn_db_transaction_check(true);

    $result = CACHE_NOTHING;
    if (!is_array($fleet_row)) {
      return $result;
    }

    $prefix = $start ? 'start' : 'end';

    // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
    // TODO Проеверить от многократного срабатывания !!!
    // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1", true);
    // Узнаем ИД владельца планеты - без блокировки
    // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!
    $user_id = DBStaticPlanet::db_planet_by_vector($fleet_row, "fleet_{$prefix}_", false, 'id_owner');
    $user_id = $user_id['id_owner'];
    // Блокируем пользователя
    $user = db_user_by_id($user_id, true);
    // Блокируем планету
    $planet_arrival = DBStaticPlanet::db_planet_by_vector($fleet_row, "fleet_{$prefix}_", true);
    // Блокируем флот
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1 FOR UPDATE;", true);

    // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
    if (!$fleet_row || !is_array($fleet_row) || ($fleet_row['fleet_mess'] == 1 && $only_resources)) {
      return $result;
    }

    // Флот, который возвращается на захваченную планету, пропадает
    if ($start && $fleet_row['fleet_mess'] == 1 && $planet_arrival['id_owner'] != $fleet_row['fleet_owner']) {
      DbFleetStatic::db_fleet_delete($fleet_row['fleet_id']);

      return $result;
    }

    $db_changeset = array();
    if (!$only_resources) {
      DbFleetStatic::db_fleet_delete($fleet_row['fleet_id']);

      if ($fleet_row['fleet_owner'] == $planet_arrival['id_owner']) {
        $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);
        foreach ($fleet_array as $ship_id => $ship_count) {
          if ($ship_count) {
            $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($ship_id, $ship_count, $user, $planet_arrival['id']);
          }
        }
      } else {
        return CACHE_NOTHING;
      }
    } else {
      $fleet_set = array(
        'fleet_resource_metal'     => 0,
        'fleet_resource_crystal'   => 0,
        'fleet_resource_deuterium' => 0,
        'fleet_mess'               => 1,
      );
      DbFleetStatic::fleet_update_set($fleet_row['fleet_id'], $fleet_set);
    }

    if (!empty($db_changeset)) {
      OldDbChangeSet::db_changeset_apply($db_changeset);
    }

    DBStaticPlanet::db_planet_set_by_id($planet_arrival['id'],
      "`metal` = `metal` + '{$fleet_row['fleet_resource_metal']}', `crystal` = `crystal` + '{$fleet_row['fleet_resource_crystal']}', `deuterium` = `deuterium` + '{$fleet_row['fleet_resource_deuterium']}'");
    $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

    return $result;
  }

  /**
   * @param $workTime
   * @param $eventsProcessed
   * @param $lastMissionId
   * @param $lastEventId
   * @param $lastEventLength
   */
  public function logTermination($workTime, $eventsProcessed, $lastMissionId, $lastEventId, $lastEventLength) {
    SN::$debug->warning(sprintf(
      'Flying fleet handler works %1$s (> %2$s) seconds - skip rest. Processed %3$d events. Last event: mission %4$s event %6$s (%5$ss)',
      number_format($workTime, 4),
      GAME_FLEET_HANDLER_MAX_TIME,
      $eventsProcessed,
      $lastMissionId ? SN::$lang['type_mission'][$lastMissionId] : '!TERMINATED BY TIMEOUT!',
      number_format($lastEventLength, 4),
      $lastEventId ? SN::$lang['fleet_events'][$lastEventId] : '!TERMINATED BY TIMEOUT!'
    ),
      'FFH Warning',
      504
    );
  }

  /**
   * @return Lock
   */
  public function buildLock() {
    $runLock = new Lock($this->gc, classConfig::FLEET_UPDATE_RUN_LOCK, SN::$gc->config->fleet_update_max_run_time, 1, classConfig::DATE_TYPE_UNIX);

    return $runLock;
  }


  /**
   * Sort flying fleets events by time+event
   *
   * @param $a
   * @param $b
   *
   * @return int
   */
  protected function flt_flyingFleetsSort($a, $b) {
    return
      // Сравниваем время флотов - кто раньше, тот и первый обрабатывается
      $a['fleet_time'] > $b['fleet_time'] ? 1 : ($a['fleet_time'] < $b['fleet_time'] ? -1 :
        // Если время - одинаковое, сравниваем события флотов
        // Если события - одинаковые, то флоты равны
        ($a['fleet_event'] == $b['fleet_event'] ? 0 :
          // Если события разные - первыми считаем прибывающие флоты
          ($a['fleet_event'] == EVENT_FLT_ARRIVE ? 1 : ($b['fleet_event'] == EVENT_FLT_ARRIVE ? -1 :
            // Если нет прибывающих флотов - дальше считаем флоты, которые закончили миссию
            ($a['fleet_event'] == EVENT_FLT_ACOMPLISH ? 1 : ($b['fleet_event'] == EVENT_FLT_ACOMPLISH ? -1 :
              // Если нет флотов, закончивших задание - остались возвращающиеся флоты, которые равны между собой
              // TODO: Добавить еще проверку по ID флота и/или времени запуска - что бы обсчитывать их в порядке запуска
              (
              0 // Вообще сюда доходить не должно - будет отсекаться на равенстве событий
              )
            ))
          ))
        )
      );
  }

  /**
   * @param string $msg
   */
  public function log_file($msg) {
    static $handler;

    if (!$handler) {
      $handler = fopen('event.log', 'a+');
    }

    fwrite($handler, date(FMT_DATE_TIME_SQL, time()) . ' ' . $msg . "\r\n");
  }

}
