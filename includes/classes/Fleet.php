<?php

/**
 * Class Fleet
 */
class Fleet {
  /**
   * `fleet_id`
   *
   * @var int
   */
  public $db_id = 0;
  /**
   * `fleet_owner`
   *
   * @var int
   */
  public $owner_id = 0;
  /**
   * `fleet_group`
   *
   * @var int
   */
  public $group_id = 0;

  /**
   * `fleet_mission`
   *
   * @var int
   */
  public $mission_type = 0;

  /**
   * `fleet_target_owner`
   *
   * @var int
   */
  public $target_owner_id = null;

  /**
   * @var array
   */
  protected $unit_list = array();
  /**
   * @var array
   */
  protected $resource_list = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );


  /**
   * `fleet_mess` - Флаг возвращающегося флота
   *
   * @var int
   */
  public $is_returning = 0;
  /**
   * `start_time` - Время отправления - таймштамп взлёта флота из точки отправления
   *
   * @var int $time_launch
   */
  public $time_launch = 0; // `start_time` = SN_TIME_NOW
  /**
   * `fleet_start_time` - Время прибытия в точку миссии/время начала выполнения миссии
   *
   * @var int $time_arrive_to_target
   */
  public $time_arrive_to_target = 0; // `fleet_start_time` = SN_TIME_NOW + $time_travel
  /**
   * `fleet_end_stay` - Время окончания миссии в точке назначения
   *
   * @var int $time_mission_job_complete
   */
  public $time_mission_job_complete = 0; // `fleet_end_stay`
  /**
   * `fleet_end_time` - Время возвращения флота после окончания миссии
   *
   * @var int $time_return_to_source
   */
  public $time_return_to_source = 0; // `fleet_end_time`


  public $fleet_start_planet_id = null;
  public $fleet_start_galaxy = 0;
  public $fleet_start_system = 0;
  public $fleet_start_planet = 0;
  public $fleet_start_type = PT_ALL;

  public $fleet_end_planet_id = null;
  public $fleet_end_galaxy = 0;
  public $fleet_end_system = 0;
  public $fleet_end_planet = 0;
  public $fleet_end_type = PT_ALL;

  // Missile properties
  public $missile_target = 0;

  // Fleet event properties
  public $fleet_start_name = '';
  public $fleet_end_name = '';
  public $ov_label = '';
  public $ov_this_planet = '';
  public $event_time = 0;

//  public $fleet_resource_metal = 0;
//  public $fleet_resource_crystal = 0;
//  public $fleet_resource_deuterium = 0;

  /**
   * Delta unit changes for DB
   *
   * @var array $ship_delta
   */
  protected $ship_delta = array();
  /**
   * Direct changes to unit - without any DELTAs
   *
   * @var array
   */
  protected $ship_replace = array();

  protected $resource_delta = array();
  protected $resource_replace = array();

  /**
   * Changes to non-unit and non-resource fields
   *
   * @var array $core_field_set_list
   */
  protected $core_field_set_list = array();

//
//

  public function __construct() {
    $this->_reset();
  }


  /**
   * Парсит строку юнитов в array(ID => AMOUNT)
   *
   * @param array $fleet_row
   *
   * @return array
   */
  public static function static_proxy_string_to_array($fleet_row) {
    return sys_unit_str2arr($fleet_row['fleet_array']);
  }

  public function parse_fleet_string($fleet_string) {
    return sys_unit_str2arr($fleet_string);
  }


  /**
   * Sends fleet back
   *
   * @param $fleet_row
   *
   * @return array|bool|mysqli_result|null
   */
  // TODO - заменять на mark_fleet_as_returned()
  public static function static_fleet_send_back(&$fleet_row) {
    $fleet_id = round(!empty($fleet_row['fleet_id']) ? $fleet_row['fleet_id'] : $fleet_row);
    if(!$fleet_id) {
      return false;
    }

    $result = Fleet::static_fleet_update_set($fleet_id, array(
      'fleet_mess' => 1,
    ));

    $fleet_row['fleet_mess'] = 1;

    return $result;
  }

  /* FLEET CRUD ========================================================================================================*/

  /**
   * READ - Gets fleet record by ID
   *
   * @param int $fleet_id
   *
   * @return array|false
   */
  public static function static_db_fleet_get($fleet_id) {
    $fleet_id_safe = idval($fleet_id);
    $result = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1 FOR UPDATE;", true);

    return is_array($result) ? $result : false;
  }
  /* FLEET HELPERS =====================================================================================================*/

  /**
   * Forcibly returns fleet before time outs
   */
  public function fleet_command_return() {
    $fleet_id = idval($this->db_id);

    $ReturnFlyingTime = ($this->time_mission_job_complete != 0 && $this->time_arrive_to_target < SN_TIME_NOW ? $this->time_arrive_to_target : SN_TIME_NOW) - $this->time_launch + SN_TIME_NOW + 1;

    $this->time_arrive_to_target = SN_TIME_NOW;
    $this->group_id = 0;
    $this->time_mission_job_complete = 0;
    $this->time_return_to_source = $ReturnFlyingTime;
    $this->is_returning = 1;

    $fleet_set_update = array(
      'fleet_start_time' => $this->time_arrive_to_target,
      'fleet_group'      => $this->group_id,
      'fleet_end_stay'   => $this->time_mission_job_complete,
      'fleet_end_time'   => $this->time_return_to_source,
      'fleet_mess'       => $this->is_returning,
//      'fleet_target_owner' => $this->owner_id, // $user['id'],
    );
    Fleet::static_fleet_update_set($fleet_id, $fleet_set_update);

    if($this->group_id) {
      // TODO: Make here to delete only one AKS - by adding aks_fleet_count to AKS table
      db_fleet_aks_purge();
    }
  }

  /**
   * @return array
   */
  public function target_coordinates_without_type() {
    return array(
      'galaxy' => $this->fleet_end_galaxy,
      'system' => $this->fleet_end_system,
      'planet' => $this->fleet_end_planet,
    );
  }

  /**
   * @return array
   */
  public function target_coordinates_typed() {
    return array(
      'galaxy' => $this->fleet_end_galaxy,
      'system' => $this->fleet_end_system,
      'planet' => $this->fleet_end_planet,
      'type'   => $this->fleet_end_type,
    );
  }

  /**
   * @return array
   */
  public function launch_coordinates_typed() {
    return array(
      'galaxy' => $this->fleet_start_galaxy,
      'system' => $this->fleet_start_system,
      'planet' => $this->fleet_start_planet,
      'type'   => $this->fleet_start_type,
    );
  }


  /**
   * UPDATE - Updates fleet record by ID with SET
   *
   * @param int    $fleet_id
   * @param string $set_safe_string
   *
   * @return array|bool|mysqli_result|null
   */
  protected static function static_db_fleet_update_set_safe_string($fleet_id, $set_safe_string) {
    $fleet_id_safe = idval($fleet_id);
    if(!empty($fleet_id_safe) && !empty($set_safe_string)) {
      $result = doquery("UPDATE `{{fleets}}` SET {$set_safe_string} WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1;");
    } else {
      $result = false;
    }

    return $result;
  }

  /**
   * Updates fleet record by ID with SET
   *
   * @param int   $fleet_id
   * @param array $set - REPLACE-set, i.e. replacement of existing values
   * @param array $delta - DELTA-set, i.e. changes to existing values
   *
   * @return array|bool|mysqli_result|null
   */
  // TODO - REDO AS METHOD!
  protected static function static_fleet_update_set($fleet_id, $set, $delta = array()) {
    $result = false;

    $fleet_id_safe = idval($fleet_id);
    $set_string_safe = db_set_make_safe_string($set);
    !empty($delta) ? $set_string_safe = implode(',', array($set_string_safe, db_set_make_safe_string($delta, true))) : false;
    if(!empty($fleet_id_safe) && !empty($set_string_safe)) {
      $result = static::static_db_fleet_update_set_safe_string($fleet_id, $set_string_safe);
    }

    return $result;
  }


  /**
   * Sets object fields for fleet return
   */
  public function method_fleet_send_back() {
    $this->core_field_set_list['fleet_mess'] = 1;
    $this->is_returning = 1;
  }


  /**
   * LOCK - Lock all records which can be used with mission
   *
   * @param $mission_data
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function method_db_fleet_lock_flying(&$mission_data) {
//  // Тупо лочим всех юзеров, чьи флоты летят или улетают с координат отбытия/прибытия $fleet_row
//  // Что бы делать это умно - надо учитывать fleet_mess во $fleet_row и в таблице fleets

    $fleet_id_safe = idval($this->db_id);

    return doquery(
    // Блокировка самого флота
      "SELECT 1 FROM {{fleets}} AS f " .

      // Блокировка всех юнитов, принадлежащих этому флоту
      "LEFT JOIN {{unit}} as unit ON unit.unit_location_type = " . LOC_FLEET . " AND unit.unit_location_id = f.fleet_id " .

      // Блокировка всех прилетающих и улетающих флотов, если нужно
      ($mission_data['dst_fleets'] ? "LEFT JOIN {{fleets}} AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id " : '') .

      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{users}} AS ud ON ud.id = f.fleet_target_owner " : '') .
      ($mission_data['dst_planet'] ? "LEFT JOIN {{planets}} AS pd ON pd.id = f.fleet_end_planet_id " : '') .

      ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{users}} AS us ON us.id = f.fleet_owner " : '') .
      ($mission_data['src_planet'] ? "LEFT JOIN {{planets}} AS ps ON ps.id = f.fleet_start_planet_id " : '') .

      "WHERE f.fleet_id = {$fleet_id_safe} GROUP BY 1 FOR UPDATE"
    );
  }


  /**
   * Restores fleet or resources to planet
   *
   * @param      $fleet_row
   * @param bool $start
   * @param bool $only_resources
   * @param bool $safe_fleet
   * @param      $result
   *
   * @return int
   */
  // TODO - split to functions
  public function RestoreFleetToPlanet($start = true, $only_resources = false, $safe_fleet = false, &$result = CACHE_NOTHING) {
    sn_db_transaction_check(true);

    // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
    if(!$this->db_id || ($this->is_returning == 1 && $only_resources)) {
      return $result;
    }

    $coordinates = $start ? $this->launch_coordinates_typed() : $this->target_coordinates_typed();

    // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
    // TODO Проеверить от многократного срабатывания !!!
    // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1", true);
    // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!
    // Узнаем ИД владельца планеты - без блокировки
    $planet_arrival = db_planet_by_vector($coordinates, '', false, 'id_owner');
    // Блокируем пользователя
    $user = db_user_by_id($planet_arrival['id_owner'], true);
    // Блокируем планету
    $planet_arrival = db_planet_by_vector($coordinates, '', true);
    // Блокируем флот
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1 FOR UPDATE;", true);

    // TODO - Проверка, что планета всё еще существует на указанных координатах, а не телепортировалась, не удалена хозяином, не уничтожена врагом
    // Флот, который возвращается на захваченную планету, пропадает
    if($start && $this->is_returning == 1 && $planet_arrival['id_owner'] != $this->owner_id) {
      $result = RestoreFleetToPlanet($this, $start, $only_resources, $result);

      $this->method_db_delete_this_fleet();

      return $result;
    }

//pdump($planet_arrival);
    if(!$only_resources) {
      // Landing ships
      $db_changeset = array();

      if($this->owner_id == $planet_arrival['id_owner']) {
        $fleet_array = $this->get_unit_list();
        foreach($fleet_array as $ship_id => $ship_count) {
          if($ship_count) {
            $db_changeset['unit'][] = sn_db_unit_changeset_prepare($ship_id, $ship_count, $user, $planet_arrival['id']);
          }
        }

        // Adjusting ship amount on planet
        if(!empty($db_changeset)) {
          db_changeset_apply($db_changeset);
        }
      }
    } else {
      $this->set_zero_cargo();
      $this->method_fleet_send_back();
      $this->flush_changes_to_db();
    }

    // Restoring resources to planet
    if($this->get_resources_amount() != 0) {
      $fleet_resources = $this->get_resource_list();
      db_planet_set_by_id($planet_arrival['id'],
        "`metal` = `metal` + '{$fleet_resources[RES_METAL]}', `crystal` = `crystal` + '{$fleet_resources[RES_CRYSTAL]}', `deuterium` = `deuterium` + '{$fleet_resources[RES_DEUTERIUM]}'");
    }

    if(!$only_resources) {
      $this->method_db_delete_this_fleet();
    }

    $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

    return RestoreFleetToPlanet($this, $start, $only_resources, $result);
  }


  /**
   * READ - Gets fleet record by ID
   *
   * @param int $fleet_id
   *
   * @return array|false
   */
  public function db_fleet_get_by_id($fleet_id) {
    $this->_reset();

    $fleet_id_safe = idval($fleet_id);

    $fleet_row = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1 FOR UPDATE;", true);
    if(!empty($fleet_row['fleet_id'])) {
      $this->parse_db_row($fleet_row);
    }

    return is_array($fleet_row) ? $fleet_row : false;
  }


  /**
   * DELETE
   *
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function method_db_delete_this_fleet() {
    $fleet_id_safe = idval($this->db_id);
    if(!empty($fleet_id_safe)) {
      $result = doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1;");
    } else {
      $result = false;
    }

    $this->_reset();

    return $result;
  }
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


  /**
   * @param $acs_id
   * @param $mission_id
   */
  // TODO - safe IDs with check via possible fleets
  public function group_acs_set($acs_id, $mission_id) {
    $this->group_id = $acs_id;
    $this->mission_type = $mission_id;

    $this->core_field_set_list['fleet_group'] = $this->group_id;
    $this->core_field_set_list['fleet_mission'] = $this->mission_type;
  }

  public function ship_count_by_id($ship_id) {
    return !empty($this->unit_list[$ship_id]) ? $this->unit_list[$ship_id] : 0;
  }

  /**
   * Saves all changes in object to DB
   *
   * @return array|bool|mysqli_result|null
   */
  public function flush_changes_to_db() {
    $result_changeset = array();

    // Готовим дельту. ДЕЛЬТА ВСЕГДА ДОЛЖНА ИДТИ ПЕРВОЙ И ЧТО БЫ В СЛУЧАЕ ДУБЛИКАТОВ БЫТЬ ПЕРЕЗАПИСАНОЙ СЕТОМ!!!
    $field_delta_changes = array();
    // Сейчас это у нас только ресурсы
    $field_delta_changes = array_merge(
      $field_delta_changes,
      ResourceLoot::convert_id_to_field_name($this->resource_delta, 'fleet_resource_')
    );
    $field_delta_string_safe = db_set_make_safe_string($field_delta_changes, true);
    !empty($field_delta_string_safe) ? $result_changeset[] = $field_delta_string_safe : false;


    // Теперь готовим REPLACE
    // Берем все изменения основных полей
    $field_replace_changes = $this->core_field_set_list;

    // Добавляем изменения в кораблях. Поскольку у нас корабли сейчас хранятся одной строкой и в списке кораблей - всегда актуальные данные, то берем именно их
    // Добавляем их только, если у нас что-то изменилось в дельте или сете
    // И добавляем их по вышеуказанной причине именно в REPLACE
    if(!empty($this->ship_delta) || !empty($this->ship_replace)) {
      $field_replace_changes['fleet_array'] = $this->make_fleet_string();
    }

    // Добавляем REPLACE ресурсов
    if(!empty($this->resource_replace)) {
      $field_replace_changes = array_merge(
        $field_replace_changes,
        ResourceLoot::convert_id_to_field_name($this->resource_replace, 'fleet_resource_')
      );
    }

    $field_replace_string_safe = db_set_make_safe_string($field_replace_changes);
    !empty($field_replace_string_safe) ? $result_changeset[] = $field_replace_string_safe : false;

    $result_changeset_string_safe = implode(',', $result_changeset);
    $result = static::static_db_fleet_update_set_safe_string($this->db_id, $result_changeset_string_safe);

    // if($result) // TODO - Вставить обработку ошибок
    $this->_reset_update();

    return $result;
  }

  /**
   * Временная функция, устанавливающая DB_ID текущего флота
   *
   * @param $fleet_id
   */
  // TODO - НЕЛЬЗЯ ТАК ДЕЛАТЬ! ЛИБО ФЛОТ УЖЕ СУЩЕСТВУЕТ - И ЕСТЬ ИД ЗАПИСИ, ЛИБО ЕГО ЕЩЕ НЕТ - И ТОГДА ИД РАВНО НУЛЮ!
  public function set_db_id($fleet_id) {
    $this->db_id = idval($fleet_id);
  }


  public function mark_fleet_as_returned() {
    // TODO - Проверка - а не возвращается ли уже флот?
    $this->is_returning = 1;
    $this->core_field_set_list['fleet_mess'] = 1;
  }

  public function mark_fleet_as_returned_and_save() {
    $this->mark_fleet_as_returned();
    $this->flush_changes_to_db();
  }

  /**
   * Updates ship list with deltas
   *
   * @param array $unit_delta_list
   */
  public function update_units($unit_delta_list) {
    !is_array($unit_delta_list) ? $unit_delta_list = array() : false;

    foreach($unit_delta_list as $unit_id => $unit_delta) {
      if(!Ship::is_in_group($unit_id) || !($unit_delta = floor($unit_delta))) {
        // Not a ship - continuing
        continue;
      }

      $this->unit_list[$unit_id] += $unit_delta; //      empty($this->ship_list[$unit_id]) ? $this->ship_list[$unit_id] = 0 : false;
      // Check for negative unit value
      if($this->unit_list[$unit_id] < 0) { //      if($unit_delta < 0 && $this->ship_list[$unit_id] < 0) {
        // TODO
        die('$unit_delta is less then ship amount  in ' . __FUNCTION__);
      }
      // Pending DELTA changes
      $this->ship_delta[$unit_id] += $unit_delta;
    }
  }

  /**
   * Replaces current unit list from array of units
   *
   * @param array $unit_list
   */
  public function replace_ships($unit_list) {
    // TODO - Resets also delta and changes?!
    $this->_reset_ships();

    !is_array($unit_list) ? $unit_list = array() : false;

    foreach($unit_list as $unit_id => $unit_count) {
      if(!Ship::is_in_group($unit_id) || !($unit_count = floor($unit_count))) {
        // Not a ship - continuing
        continue;
      }

      // Check for negative unit value
      if($unit_count < 0) {
        // TODO
        die('$unit_count can not be negative in ' . __FUNCTION__);
      }
      $this->unit_list[$unit_id] = $unit_count;
      // Pending REPLACE changes
      $this->ship_replace[$unit_id] = $unit_count;
    }
  }


  /**
   * Updates fleet resource list with deltas
   *
   * @param $resource_delta_list
   */
  public function update_resources($resource_delta_list) {
    !is_array($resource_delta_list) ? $resource_delta_list = array() : false;

    foreach($resource_delta_list as $resource_id => $unit_delta) {
      if(!ResourceLoot::is_in_group($resource_id) || !($unit_delta = floor($unit_delta))) {
        // Not a resource or no resources - continuing
        continue;
      }

      $this->resource_list[$resource_id] += $unit_delta;

      // Check for negative unit value
      if($this->resource_list[$resource_id] < 0) {
        // TODO
        die('$unit_delta is less then resource amount in ' . __FILE__ . ' ' . __FUNCTION__ . ' ' . __LINE__);
      }

      // Preparing changes
      $this->resource_delta[$resource_id] += $unit_delta;
    }
  }

  /**
   * Set current resource list from array of units
   *
   * @param array $resource_list
   */
  public function replace_resources($resource_list) {
    // TODO - Resets also delta and changes?!
    $this->_reset_resources();

    !is_array($resource_list) ? $resource_list = array() : false;

    foreach($resource_list as $resource_id => $unit_count) {
      if(!ResourceLoot::is_in_group($resource_id) || !($unit_count = floor($unit_count))) {
        // Not a resource or zero resource - continuing
        continue;
      }

      // Check for negative unit value
      if($unit_count < 0) {
        // TODO
        die('$unit_count can not be negative in ' . __FUNCTION__);
      }
      $this->resource_list[$resource_id] = $unit_count;

      // Preparing changes
      $this->resource_replace[$resource_id] = $unit_count;
    }
  }

  public function set_zero_cargo() {
    $this->replace_resources(array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    ));
  }


  /**
   * Parses extended unit_array which can include not only ships but resources, captains etc
   *
   * @param $unit_array
   */
  protected function parse_unit_array($unit_array) {
    foreach($unit_array as $unit_id => $unit_count) {
      $unit_count = floatval($unit_count);
      if(!$unit_count) {
        continue;
      }

      if(Ship::is_in_group($unit_id)) {
        $this->unit_list[$unit_id] = $unit_count;
      } elseif(ResourceLoot::is_in_group($unit_id)) {
        $this->resource_list[$unit_id] = $unit_count;
      }
    }
  }

  /**
   * Sets fleet timers based on flight duration, time on mission (HOLD/EXPLORE) and fleet departure time.
   *
   * @param int $time_to_travel - flight duration in seconds
   * @param int $time_on_mission - time on mission in seconds
   * @param int $group_sync_delta_time - delta time to adjust fleet arrival time if fleet is a part of group (i.e. ACS)
   * @param int $flight_departure - fleet departure from source planet timestamp. Allows to send fleet in future or in past
   */
  public function set_times($time_to_travel, $time_on_mission = 0, $group_sync_delta_time = 0, $flight_departure = SN_TIME_NOW) {
    $this->time_launch = $flight_departure;

    $this->time_arrive_to_target = $this->time_launch + $time_to_travel + $group_sync_delta_time;
    $this->time_mission_job_complete = $time_on_mission ? $this->time_arrive_to_target + $time_on_mission : 0;
    $this->time_return_to_source = ($this->time_mission_job_complete ? $this->time_mission_job_complete : $this->time_arrive_to_target) + $time_to_travel;
  }

  /**
   * Initializes Fleet from user params and posts it to DB
   *
   * @param     $owner_id
   * @param     $unit_array
   * @param     $mission_type
   * @param     $from
   * @param     $to
   * @param int $fleet_group
   *
   * @return int|string
   */
  public function create_and_send($owner_id, $unit_array, $mission_type, $from, $to, $fleet_group = 0) {
//    $this->_reset();

    $this->mission_type = $mission_type;
    $this->group_id = $fleet_group;

    $this->owner_id = $owner_id;
    $this->target_owner_id = intval($to['id_owner']) ? $to['id_owner'] : 0;

    // Filling $ship_list and $resource_list, also fills $amount
    $this->parse_unit_array($unit_array);

    $this->fleet_start_planet_id = intval($from['id']) ? $from['id'] : null;
    $this->fleet_start_galaxy = $from['galaxy'];
    $this->fleet_start_system = $from['system'];
    $this->fleet_start_planet = $from['planet'];
    $this->fleet_start_type = $from['planet_type'];

    $this->fleet_end_planet_id = intval($to['id']) ? $to['id'] : null;
    $this->fleet_end_galaxy = $to['galaxy'];
    $this->fleet_end_system = $to['system'];
    $this->fleet_end_planet = $to['planet'];
    $this->fleet_end_type = $to['planet_type'];

    // WARNING! MISSION TIMES MUST BE SET WITH set_times() method!
    if(empty($this->time_launch)) {
      die('Fleet time not set!');
    }

    $this->db_insert();

    return $this->db_id;
  }

  /**
   * Insert fleet into DB
   *
   * @return int|string
   */
  protected function db_insert() {
    $set_safe_string = db_set_make_safe_string($this->make_db_insert_set());

    $db_fleet_id = 0;
    if(!empty($set_safe_string)) {
      doquery("INSERT INTO `{{fleets}}` SET {$set_safe_string}");
      if($db_fleet_id = db_insert_id()) {
        $fleet_row = static::static_db_fleet_get($db_fleet_id);
        if(!empty($fleet_row) && is_array($fleet_row)) {
          $this->parse_db_row($fleet_row);
        } else {
          $db_fleet_id = 0;
        }
      }
    }

    $this->db_id = !empty($db_fleet_id) ? $db_fleet_id : 0;

    return $this->db_id;
  }

  /**
   * Returns ship list in fleet
   */
  public function get_unit_list() {
    return $this->unit_list;
  }

  /**
   * Returns resource list in fleet
   */
  public function get_resource_list() {
    return $this->resource_list;
  }

  /**
   * Удаляет текущий флот из базы
   *
   * @return array|bool|mysqli_result|null
   */
  public function method_db_fleet_delete() {
    $fleet_id_safe = idval($this->db_id);
    if(!empty($fleet_id_safe)) {
      $result = doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1;");
    } else {
      $result = false;
    }

    db_unit_list_delete(0, LOC_FLEET, $this->db_id, 0);

    $this->_reset();

    return $result;
  }

  /**
   * Compiles object for INSERT DB command
   *
   * @return array
   */
  public function make_db_insert_set() {
    $db_set = array(
//      'fleet_id'              => $this->id,
      'fleet_owner'   => $this->owner_id,
      'fleet_mission' => $this->mission_type,

      'fleet_target_owner' => !empty($this->target_owner_id) ? $this->target_owner_id : null,
      'fleet_group'        => $this->group_id,
      'fleet_mess'         => empty($this->is_returning) ? 0 : 1,

      'start_time'       => $this->time_launch,
      'fleet_start_time' => $this->time_arrive_to_target,
      'fleet_end_stay'   => $this->time_mission_job_complete,
      'fleet_end_time'   => $this->time_return_to_source,

      'fleet_start_planet_id' => !empty($this->fleet_start_planet_id) ? $this->fleet_start_planet_id : null,
      'fleet_start_galaxy'    => $this->fleet_start_galaxy,
      'fleet_start_system'    => $this->fleet_start_system,
      'fleet_start_planet'    => $this->fleet_start_planet,
      'fleet_start_type'      => $this->fleet_start_type,

      'fleet_end_planet_id' => !empty($this->fleet_end_planet_id) ? $this->fleet_end_planet_id : null,
      'fleet_end_galaxy'    => $this->fleet_end_galaxy,
      'fleet_end_system'    => $this->fleet_end_system,
      'fleet_end_planet'    => $this->fleet_end_planet,
      'fleet_end_type'      => $this->fleet_end_type,

      'fleet_array'  => $this->make_fleet_string(),
      'fleet_amount' => $this->get_ship_count(),

      'fleet_resource_metal'     => $this->resource_list[RES_METAL],
      'fleet_resource_crystal'   => $this->resource_list[RES_CRYSTAL],
      'fleet_resource_deuterium' => $this->resource_list[RES_DEUTERIUM],
    );

    return $db_set;
  }

  /**
   * Parses record from `fleet` table to object
   *
   * @param array $fleet_row - `fleets` DB record
   */
  public function parse_db_row($fleet_row) {
    $this->_reset();

    if(empty($fleet_row) || !is_array($fleet_row)) {
      return;
    }

    $this->db_id = $fleet_row['fleet_id'];
    $this->owner_id = $fleet_row['fleet_owner'];
    $this->mission_type = $fleet_row['fleet_mission'];

    $this->target_owner_id = $fleet_row['fleet_target_owner'];
    $this->group_id = $fleet_row['fleet_group'];
    $this->is_returning = intval($fleet_row['fleet_mess']);

    $this->time_launch = $fleet_row['start_time'];
    $this->time_arrive_to_target = $fleet_row['fleet_start_time'];
    $this->time_mission_job_complete = $fleet_row['fleet_end_stay'];
    $this->time_return_to_source = $fleet_row['fleet_end_time'];

    $this->fleet_start_planet_id = !empty($fleet_row['fleet_start_planet_id']) ? $fleet_row['fleet_start_planet_id'] : null;
    $this->fleet_start_galaxy = $fleet_row['fleet_start_galaxy'];
    $this->fleet_start_system = $fleet_row['fleet_start_system'];
    $this->fleet_start_planet = $fleet_row['fleet_start_planet'];
    $this->fleet_start_type = $fleet_row['fleet_start_type'];

    $this->fleet_end_planet_id = $fleet_row['fleet_end_planet_id'];
    $this->fleet_end_galaxy = $fleet_row['fleet_end_galaxy'];
    $this->fleet_end_system = $fleet_row['fleet_end_system'];
    $this->fleet_end_planet = $fleet_row['fleet_end_planet'];
    $this->fleet_end_type = $fleet_row['fleet_end_type'];

    $this->unit_list = $this->parse_fleet_string($fleet_row['fleet_array']);

    $this->resource_list = array(
      RES_METAL     => ceil($fleet_row['fleet_resource_metal']),
      RES_CRYSTAL   => ceil($fleet_row['fleet_resource_crystal']),
      RES_DEUTERIUM => ceil($fleet_row['fleet_resource_deuterium']),
    );
  }

  public function parse_missile_db_row($missile_db_row) {
    $this->_reset();

    if(empty($missile_db_row) || !is_array($missile_db_row)) {
      return;
    }

//      $planet_start = db_planet_by_vector($irak_original, 'fleet_start_', false, 'name');
//      $irak_original['fleet_start_name'] = $planet_start['name'];
    $this->missile_target = $missile_db_row['primaer'];

    $this->db_id = -$missile_db_row['id'];
    $this->owner_id = $missile_db_row['fleet_owner'];
    $this->mission_type = MT_MISSILE;

    $this->target_owner_id = $missile_db_row['fleet_target_owner'];

    $this->group_id = 0;
    $this->is_returning = 0;

    $this->time_launch = 0; // $irak['start_time'];
    $this->time_arrive_to_target = 0; // $irak['fleet_start_time'];
    $this->time_mission_job_complete = 0; // $irak['fleet_end_stay'];
    $this->time_return_to_source = $missile_db_row['fleet_end_time'];

    $this->fleet_start_planet_id = !empty($missile_db_row['fleet_start_planet_id']) ? $missile_db_row['fleet_start_planet_id'] : null;
    $this->fleet_start_galaxy = $missile_db_row['fleet_start_galaxy'];
    $this->fleet_start_system = $missile_db_row['fleet_start_system'];
    $this->fleet_start_planet = $missile_db_row['fleet_start_planet'];
    $this->fleet_start_type = $missile_db_row['fleet_start_type'];

    $this->fleet_end_planet_id = !empty($missile_db_row['fleet_end_planet_id']) ? $missile_db_row['fleet_end_planet_id'] : null;
    $this->fleet_end_galaxy = $missile_db_row['fleet_end_galaxy'];
    $this->fleet_end_system = $missile_db_row['fleet_end_system'];
    $this->fleet_end_planet = $missile_db_row['fleet_end_planet'];
    $this->fleet_end_type = $missile_db_row['fleet_end_type'];

    $this->unit_list = array(UNIT_DEF_MISSILE_INTERPLANET => $missile_db_row['fleet_amount']);

//    $this->resource_list = array(
//      RES_METAL     => ceil($irak['fleet_resource_metal']),
//      RES_CRYSTAL   => ceil($irak['fleet_resource_crystal']),
//      RES_DEUTERIUM => ceil($irak['fleet_resource_deuterium']),
//    );
  }

  /**
   * Возвращает ёмкость переработчиков во флоте
   *
   * @param array $recycler_info
   *
   * @return int
   *
   * @version 41a5.4
   */
  public function fleet_recyclers_capacity(array $recycler_info) {
    $recyclers_incoming_capacity = 0;
    $fleet_data = $this->get_unit_list();
    foreach($recycler_info as $recycler_id => $recycler_data) {
      $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
    }

    return $recyclers_incoming_capacity;
  }


  public function make_fleet_string() {
    return sys_unit_arr2str($this->unit_list);
  }

  public function get_ship_count() {
    return array_sum($this->unit_list);
  }

  public function get_resources_amount() {
    return empty($this->resource_list) || !is_array($this->resource_list) ? 0 : array_sum($this->resource_list);
  }

  protected function _reset() {
    $this->db_id = 0;
    $this->owner_id = 0;
    $this->target_owner_id = null;
    $this->mission_type = 0;
//    $this->db_string = '';
    $this->group_id = 0;
    $this->is_returning = 0;

    $this->time_launch = 0; // SN_TIME_NOW
    $this->time_arrive_to_target = 0; // SN_TIME_NOW + $time_travel
    $this->time_mission_job_complete = 0;
    $this->time_return_to_source = 0;

    $this->fleet_start_planet_id = null;
    $this->fleet_start_galaxy = 0;
    $this->fleet_start_system = 0;
    $this->fleet_start_planet = 0;
    $this->fleet_start_type = PT_ALL;

    $this->fleet_end_planet_id = null;
    $this->fleet_end_galaxy = 0;
    $this->fleet_end_system = 0;
    $this->fleet_end_planet = 0;
    $this->fleet_end_type = PT_ALL;
//    $this->fleet_resource_metal = 0;
//    $this->fleet_resource_crystal = 0;
//    $this->fleet_resource_deuterium = 0;

//    $this->ship_count = 0;

    $this->_reset_ships();
    $this->_reset_resources();
    $this->core_field_set_list = array();
  }

  protected function _reset_ships() {
    $this->unit_list = array();
    $this->ship_delta = array();
    $this->ship_replace = array();
  }

  protected function _reset_resources() {
    $this->resource_list = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );
    $this->resource_delta = array();
    $this->resource_replace = array();
  }

  protected function _reset_update() {
    $this->core_field_set_list = array();

    $this->ship_delta = array();
    $this->ship_replace = array();

    $this->resource_delta = array();
    $this->resource_replace = array();
  }

}
