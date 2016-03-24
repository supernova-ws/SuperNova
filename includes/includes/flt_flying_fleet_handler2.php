<?php

/**
 * @function RestoreFleetToPlanet
 *
 * @version 1.0
 * @copyright 2008 Chlorel for XNova
 */

/*
@function RestoreFleetToPlanet

$fleet_row      = enregistrement de flotte
$start          = true  - planete de depart
                = false - planete d'arrivГ©e
$only_resources = true - store only resources
                = false - store fleet too
returns         = bitmask for recaching
*/

// ------------------------------------------------------------------
// unit_captain overrides
/**
 * Handled by:
 *    - unit_captain
 * Overriden by:
 *    - none
 *
 * @param Fleet $objFleet
 * @param bool  $start
 * @param bool  $only_resources
 * @param null  $result
 *
 * @return mixed
 */
function RestoreFleetToPlanet(&$objFleet, $start = true, $only_resources = false, $result = null) { return sn_function_call(__FUNCTION__, array(&$objFleet, $start, $only_resources, &$result)); }

// ------------------------------------------------------------------
function flt_flyingFleetsSort($a, $b) {
  // Сравниваем время флотов - кто раньше, тот и первый обрабатывается
  return $a['fleet_time'] > $b['fleet_time'] ? 1 : ($a['fleet_time'] < $b['fleet_time'] ? -1 :
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

function log_file($msg) {
  static $handler;

  if(!$handler) {
    $handler = fopen('event.log', 'a+');
  }

  fwrite($handler, date(FMT_DATE_TIME_SQL, time()) . ' ' . $msg . "\r\n");
}

// ------------------------------------------------------------------
function flt_flying_fleet_handler($skip_fleet_update = false) {
print('<div style="color: red; font-size: 300%">Fleet handler is disabled</div>');
return;
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
  global $config, $debug;

  if($config->game_disable != GAME_DISABLE_NONE || $skip_fleet_update) {
    return;
  }

  sn_db_transaction_start();
  if($config->db_loadItem('game_disable') != GAME_DISABLE_NONE || SN_TIME_NOW - strtotime($config->db_loadItem('fleet_update_last')) <= $config->fleet_update_interval) {
    sn_db_transaction_rollback();

    return;
  }


  // Watchdog timer
  if($config->db_loadItem('fleet_update_lock')) {
    if(defined('DEBUG_FLYING_FLEETS')) {
      $random = 0;
    } else {
      $random = mt_rand(240, 300);
    }

    if(SN_TIME_NOW - strtotime($config->fleet_update_lock) <= $random) {
      sn_db_transaction_rollback();

      return;
    } else {
      $debug->warning('Flying fleet handler was locked too long - watchdog unlocked', 'FFH Error', 504);
    }
  }

  $config->db_saveItem('fleet_update_lock', SN_TIME_SQL);
  $config->db_saveItem('fleet_update_last', SN_TIME_SQL);
  sn_db_transaction_commit();

//log_file('Начинаем обсчёт флотов');

//log_file('Обсчёт ракет');
  sn_db_transaction_start();
  coe_o_missile_calculate();
  sn_db_transaction_commit();

  $fleet_event_list = array();
  $missions_used = array();

  $objFleetList = FleetList::dbGetFleetListCurrentTick();
  foreach($objFleetList->_container as $objFleet) {
    set_time_limit(15);
    // TODO - Унифицировать код с темплейтным разбором эвентов на планете!
    $missions_used[$objFleet->mission_type] = 1;
    if($objFleet->time_arrive_to_target <= SN_TIME_NOW && !$objFleet->isReturning()) {
      $fleet_event_list[] = array(
        'object'      => $objFleet,
        'fleet_time'  => $objFleet->time_arrive_to_target,
        'fleet_event' => EVENT_FLT_ARRIVE,
      );
    }

    if($objFleet->time_mission_job_complete > 0 && $objFleet->time_mission_job_complete <= SN_TIME_NOW && !$objFleet->isReturning()) {
      $fleet_event_list[] = array(
        'object'      => $objFleet,
        'fleet_time'  => $objFleet->time_mission_job_complete,
        'fleet_event' => EVENT_FLT_ACOMPLISH,
      );
    }

    if($objFleet->time_return_to_source <= SN_TIME_NOW) {
      $fleet_event_list[] = array(
        'object'      => $objFleet,
        'fleet_time'  => $objFleet->time_return_to_source,
        'fleet_event' => EVENT_FLT_RETURN,
      );
    }
  }

//log_file('Сортировка и подгрузка модулей');
  uasort($fleet_event_list, 'flt_flyingFleetsSort');

// TODO: Грузить только используемые модули из $missions_used
  $mission_files = array(
    MT_ATTACK    => 'flt_mission_attack',
    MT_AKS       => 'flt_mission_attack',
    MT_DESTROY   => 'flt_mission_attack',
    MT_TRANSPORT => 'flt_mission_transport',
    MT_RELOCATE  => 'flt_mission_relocate',
    MT_HOLD      => 'flt_mission_hold',
    MT_SPY       => 'flt_mission_spy',
    MT_COLONIZE  => 'flt_mission_colonize',
    MT_RECYCLE   => 'flt_mission_recycle',
//    MT_MISSILE => 'flt_mission_missile.php',
    MT_EXPLORE   => 'flt_mission_explore',
  );
  foreach($missions_used as $mission_id => $cork) {
    require_once(SN_ROOT_PHYSICAL . "includes/includes/{$mission_files[$mission_id]}" . DOT_PHP_EX);
  }

//log_file('Обработка миссий');
  $sn_groups_mission = sn_get_groups('missions');
  foreach($fleet_event_list as $fleet_event) {
    // TODO: Указатель тут потом сделать
    // TODO: СЕЙЧАС НАДО ПРОВЕРЯТЬ ПО БАЗЕ - А ЖИВОЙ ЛИ ФЛОТ?!
    $fleet_row = $fleet_event['fleet_row'];
    if(empty($fleet_event['object'])) {
      // Fleet was destroyed in course of previous actions
      continue;
    }

    /**
     * @var Fleet $objFleet
     */
    $objFleet = $fleet_event['object'];
//    $objFleet = new Fleet();
//    $objFleet->parse_db_row($fleet_row);

    // TODO Обернуть всё в транзакции. Начинать надо заранее, блокируя все таблицы внутренним локом SELECT 1 FROM {{users}}
    sn_db_transaction_start();
    $config->db_saveItem('fleet_update_last', SN_TIME_SQL);


    $mission_data = $sn_groups_mission[$objFleet->mission_type];

    // Формируем запрос, блокирующий сразу все нужные записи
    $objFleet->dbLockFlying($mission_data);

    $objFleet->dbLoad($objFleet->dbId);

    if(!$objFleet->dbId) {
      // Fleet was destroyed in course of previous actions
      sn_db_transaction_commit();
      continue;
    }

    if($fleet_event['fleet_event'] == EVENT_FLT_RETURN) {
      // Fleet returns to planet
      $objFleet->RestoreFleetToPlanet(true, false);
      sn_db_transaction_commit();
      continue;
    }

    if($fleet_event['fleet_event'] == EVENT_FLT_ARRIVE && $objFleet->isReturning()) {
      // При событии EVENT_FLT_ARRIVE флот всегда должен иметь fleet_mess == 0
      // В противном случае это означает, что флот уже был обработан ранее - например, при САБе
      sn_db_transaction_commit();
      continue;
    }

    // TODO: Здесь тоже указатели
    // TODO: Кэширование
    // TODO: Выбирать только нужные поля

    $objMission = new Mission();
    $objMission->fleet = $objFleet;
    $objMission->src_user = $mission_data['src_user'] || $mission_data['src_planet'] ? db_user_by_id($objFleet->playerOwnerId, true) : null;
    $objMission->src_planet = $mission_data['src_planet'] ? db_planet_by_vector($objFleet->launch_coordinates_typed(), '', true, '`id`, `id_owner`, `name`') : null;
    $objMission->dst_user = $mission_data['dst_user'] || $mission_data['dst_planet'] ? db_user_by_id($objFleet->target_owner_id, true) : null;
    // шпионаж не дает нормальный ID fleet_end_planet_id 'dst_planet'
    $objMission->dst_planet = $mission_data['dst_planet'] ? db_planet_by_vector($objFleet->target_coordinates_typed(), '', true, '`id`, `id_owner`, `name`') : null;
    $objMission->fleet_event = $fleet_event['fleet_event'];

    if($objMission->dst_planet && $objMission->dst_planet['id_owner']) {
      $update_result = sys_o_get_updated($objMission->dst_planet['id_owner'], $objMission->dst_planet['id'], $objFleet->time_arrive_to_target);
      $objMission->dst_user = !empty($objMission->dst_user) ? $update_result['user'] : null;
      $objMission->dst_planet = $update_result['planet'];
    }

    switch($objFleet->mission_type) {
      // Для боевых атак нужно обновлять по САБу и по холду - таки надо возвращать данные из обработчика миссий!
      case MT_AKS:
      case MT_ATTACK:
      case MT_DESTROY:
        $attack_result = flt_mission_attack($objMission); // Partially
        $mission_result = CACHE_COMBAT;
      break;

      case MT_TRANSPORT:
        $mission_result = flt_mission_transport($objMission); // OK
      break;

      case MT_HOLD:
        $mission_result = flt_mission_hold($objMission); // OK
      break;

      case MT_RELOCATE:
        $mission_result = flt_mission_relocate($objMission); // OK
      break;

      case MT_EXPLORE:
        $mission_result = flt_mission_explore($objMission); // OK
      break;

      case MT_RECYCLE:
        $mission_result = flt_mission_recycle($objMission); // OK
      break;

      case MT_COLONIZE:
        $mission_result = flt_mission_colonize($objMission); // OK
      break;

      case MT_SPY:
        $mission_result = flt_mission_spy($objMission); // OK
      break;

      case MT_MISSILE:  // Missiles !!
      break;

//      default:
//        doquery("DELETE FROM `{{_fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
//      break;
    }
    sn_db_transaction_commit();
  }
  sn_db_transaction_start();
  $config->db_saveItem('fleet_update_last', SN_TIME_SQL);
  $config->db_saveItem('fleet_update_lock', '');
  sn_db_transaction_commit();

//  log_file('Закончили обсчёт флотов');
}
