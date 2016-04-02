<?php

/**
 * Class Fleet
 *
 * @property int dbId
 * @property int playerOwnerId
 * @property int group_id
 * @property int mission_type
 * @property int target_owner_id
 * @property int is_returning
 *
 * @property int time_launch
 * @property int time_arrive_to_target
 * @property int time_mission_job_complete
 * @property int time_return_to_source
 *
 * @property int fleet_start_planet_id
 * @property int fleet_start_galaxy
 * @property int fleet_start_system
 * @property int fleet_start_planet
 * @property int fleet_start_type
 *
 * @property int fleet_end_planet_id
 * @property int fleet_end_galaxy
 * @property int fleet_end_system
 * @property int fleet_end_planet
 * @property int fleet_end_type
 *
 */
class Fleet extends UnitContainer {


  // DBRow inheritance *************************************************************************************************

  /**
   * Table name in DB
   *
   * @var string
   */
  protected static $_table = 'fleets';
  /**
   * Name of ID field in DB
   *
   * @var string
   */
  protected static $_dbIdFieldName = 'fleet_id';
  /**
   * DB_ROW to Class translation scheme
   *
   * @var array
   */
  protected static $_properties = array(
    'dbId'          => array(
      P_DB_FIELD => 'fleet_id',
    ),
    'playerOwnerId' => array(
      P_METHOD_EXTRACT => 'ownerExtract',
      P_METHOD_INJECT  => 'ownerInject',
//      P_DB_FIELD => 'fleet_owner',
    ),
    'mission_type'  => array(
      P_DB_FIELD   => 'fleet_mission',
      P_FUNC_INPUT => 'intval',
    ),

    'target_owner_id' => array(
      P_DB_FIELD => 'fleet_target_owner',
    ),
    'group_id'        => array(
      P_DB_FIELD => 'fleet_group',
    ),
    'is_returning'    => array(
      P_DB_FIELD   => 'fleet_mess',
      P_FUNC_INPUT => 'intval',
    ),

    'shipCount' => array(
      P_DB_FIELD  => 'fleet_amount',
// TODO - CHECK !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//      P_FUNC_OUTPUT => 'get_ship_count',
//      P_DB_FIELDS_LINKED => array(
//        'fleet_amount',
//      ),
      P_READ_ONLY => true,
    ),

    'time_launch' => array(
      P_DB_FIELD => 'start_time',
    ),

    'time_arrive_to_target'     => array(
      P_DB_FIELD => 'fleet_start_time',
    ),
    'time_mission_job_complete' => array(
      P_DB_FIELD => 'fleet_end_stay',
    ),
    'time_return_to_source'     => array(
      P_DB_FIELD => 'fleet_end_time',
    ),

    'fleet_start_planet_id' => array(
      P_DB_FIELD   => 'fleet_start_planet_id',
      P_FUNC_INPUT => 'nullIfEmpty',
    ),

    'fleet_start_galaxy' => array(
      P_DB_FIELD => 'fleet_start_galaxy',
    ),
    'fleet_start_system' => array(
      P_DB_FIELD => 'fleet_start_system',
    ),
    'fleet_start_planet' => array(
      P_DB_FIELD => 'fleet_start_planet',
    ),
    'fleet_start_type'   => array(
      P_DB_FIELD => 'fleet_start_type',
    ),

    'fleet_end_planet_id' => array(
      P_DB_FIELD   => 'fleet_end_planet_id',
      P_FUNC_INPUT => 'nullIfEmpty',
    ),
    'fleet_end_galaxy'    => array(
      P_DB_FIELD => 'fleet_end_galaxy',
    ),
    'fleet_end_system'    => array(
      P_DB_FIELD => 'fleet_end_system',
    ),
    'fleet_end_planet'    => array(
      P_DB_FIELD => 'fleet_end_planet',
    ),
    'fleet_end_type'      => array(
      P_DB_FIELD => 'fleet_end_type',
    ),

    'resource_list' => array(
      P_METHOD_EXTRACT   => 'resourcesExtract',
      P_METHOD_INJECT    => 'resourcesInject',
      P_DB_FIELDS_LINKED => array(
        'fleet_resource_metal',
        'fleet_resource_crystal',
        'fleet_resource_deuterium',
      ),
    ),
  );


  // UnitContainer inheritance *****************************************************************************************
  /**
   * Type of this location
   *
   * @var int $locationType
   */
  protected static $locationType = LOC_FLEET;


  // New properties ****************************************************************************************************
  /**
   * `fleet_owner`
   *
   * @var int
   */
  protected $_playerOwnerId = 0;
  /**
   * `fleet_group`
   *
   * @var int
   */
  protected $_group_id = 0;

  /**
   * `fleet_mission`
   *
   * @var int
   */
  protected $_mission_type = 0;

  /**
   * `fleet_target_owner`
   *
   * @var int
   */
  protected $_target_owner_id = null;

  /**
   * @var array
   */
  protected $resource_list = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );


  /**
   * `fleet__mess` - Флаг возвращающегося флота
   *
   * @var int
   */
  protected $_is_returning = 0;
  /**
   * `start_time` - Время отправления - таймштамп взлёта флота из точки отправления
   *
   * @var int $_time_launch
   */
  protected $_time_launch = 0; // `start_time` = SN_TIME_NOW
  /**
   * `fleet_start_time` - Время прибытия в точку миссии/время начала выполнения миссии
   *
   * @var int $_time_arrive_to_target
   */
  protected $_time_arrive_to_target = 0; // `fleet_start_time` = SN_TIME_NOW + $time_travel
  /**
   * `fleet_end_stay` - Время окончания миссии в точке назначения
   *
   * @var int $_time_mission_job_complete
   */
  protected $_time_mission_job_complete = 0; // `fleet_end_stay`
  /**
   * `fleet_end_time` - Время возвращения флота после окончания миссии
   *
   * @var int $_time_return_to_source
   */
  protected $_time_return_to_source = 0; // `fleet_end_time`


  protected $_fleet_start_planet_id = null;
  protected $_fleet_start_galaxy = 0;
  protected $_fleet_start_system = 0;
  protected $_fleet_start_planet = 0;
  protected $_fleet_start_type = PT_ALL;

  protected $_fleet_end_planet_id = null;
  protected $_fleet_end_galaxy = 0;
  protected $_fleet_end_system = 0;
  protected $_fleet_end_planet = 0;
  protected $_fleet_end_type = PT_ALL;

  // Missile properties
  public $missile_target = 0;

  // Fleet event properties
  public $fleet_start_name = '';
  public $fleet_end_name = '';
  public $ov_label = '';
  public $ov_this_planet = '';
  public $event_time = 0;

  protected $resource_delta = array();
  protected $resource_replace = array();


//


  protected $allowed_missions = array();
  protected $allowed_planet_types = array(
    // PT_NONE => PT_NONE,
    PT_PLANET => PT_PLANET,
    PT_MOON   => PT_MOON,
    PT_DEBRIS => PT_DEBRIS
  );

  // TODO - Move to Player
  public $dbOwnerRow = array();
  public $dbSourcePlanetRow = array();

  /**
   * GSPT coordinates of target
   *
   * @var Vector
   */
  public $targetVector = array();
  /**
   * Target planet row
   *
   * @var array
   */
  public $dbTargetRow = array();


  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  // TODO - REMOVE! TEMPORARY UNTIL THERE BE FULLLY FUNCTIONAL Player CLASS AND FLEETS WOULD BE LOCATED ON PLANET OR PLAYER!!!!!
//  public function getPlayerOwnerId() {
//    return $this->_dbId;
//  }

  /**
   * Fleet constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->allowed_missions = sn_get_groups('missions');
  }

  /**
   * @param array $playerRow
   * @param array $planetRow
   *
   * @return array
   */
  public function tplRenderAvailableShips($playerRow, $planetRow) {
    $record_index = 0;
    $ship_list = array();
    foreach(sn_get_groups('fleet') as $n => $unit_id) {
      $unit_level = mrc_get_level($playerRow, $planetRow, $unit_id, false, true);
      if($unit_level <= 0) {
        continue;
      }
      $ship_data = get_ship_data($unit_id, $playerRow);
      $ship_list[$unit_id] = array(
        '__INDEX'          => $record_index++,
        'ID'               => $unit_id,
        'NAME'             => classLocale::$lang['tech'][$unit_id],
        'AMOUNT'           => $unit_level,
        'AMOUNT_TEXT'      => pretty_number($unit_level),
        'CONSUMPTION'      => $ship_data['consumption'],
        'CONSUMPTION_TEXT' => pretty_number($ship_data['consumption']),
        'SPEED'            => $ship_data['speed'],
        'SPEED_TEXT'       => pretty_number($ship_data['speed']),
        'CAPACITY'         => $ship_data['capacity'],
        'CAPACITY_TEXT'    => pretty_number($ship_data['capacity']),
      );
    }

    return $ship_list;
  }

  /**
   * @param $ship_list
   */
  public function tplSortShipList(&$ship_list) {
    $fleet_ship_sort = classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT];
    $fleet_ship_sort_inverse = classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE];
    if($fleet_ship_sort || $fleet_ship_sort_inverse != PLAYER_OPTION_SORT_ORDER_PLAIN) {
      switch($fleet_ship_sort) {
        case PLAYER_OPTION_SORT_NAME:
          $fleet_ship_sort_field = 'NAME';
        break;
        case PLAYER_OPTION_SORT_SPEED:
          $fleet_ship_sort_field = 'SPEED';
        break;
        case PLAYER_OPTION_SORT_COUNT:
          $fleet_ship_sort_field = 'AMOUNT';
        break;
        case PLAYER_OPTION_SORT_ID:
          $fleet_ship_sort_field = 'ID';
        break;
        default:
          $fleet_ship_sort_field = '__INDEX';
        break;
      }
      $fleet_ship_sort_inverse_a = $fleet_ship_sort_inverse ? -1 : 1;
      usort($ship_list, function ($a, $b) use ($fleet_ship_sort_field, $fleet_ship_sort_inverse_a) {
        return $a[$fleet_ship_sort_field] < $b[$fleet_ship_sort_field] ? -1 * $fleet_ship_sort_inverse_a : (
        $a[$fleet_ship_sort_field] > $b[$fleet_ship_sort_field] ? 1 * $fleet_ship_sort_inverse_a : 0
        );
      });
    }
  }

  public function isEmpty() {
    return !$this->resourcesGetTotal() && !$this->shipsGetTotal();
  }

//  public function getPlayerOwnerId() {
//    return $this->playerOwnerId;
//  }

  /**
   * Initializes Fleet from user params and posts it to DB
   */
  public function dbInsert() {
    // WARNING! MISSION TIMES MUST BE SET WITH set_times() method!
    // TODO - more checks!
    if(empty($this->_time_launch)) {
      die('Fleet time not set!');
    }

    parent::dbInsert();
  }


  /* FLEET DB ACCESS =================================================================================================*/

  /**
   * LOCK - Lock all records which can be used with mission
   *
   * @param $mission_data
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function dbLockFlying(&$mission_data) {
    // Тупо лочим всех юзеров, чьи флоты летят или улетают с координат отбытия/прибытия $fleet_row
    // Что бы делать это умно - надо учитывать fleet__mess во $fleet_row и в таблице fleets

    $fleet_id_safe = idval($this->_dbId);

    return doquery(
    // Блокировка самого флота
      "SELECT 1 FROM {{fleets}} AS f " .

      // Блокировка всех юнитов, принадлежащих этому флоту
      "LEFT JOIN {{unit}} as unit ON unit.unit_location_type = " . static::$locationType . " AND unit.unit_location_id = f.fleet_id " .

      // Блокировка всех прилетающих и улетающих флотов, если нужно
      // TODO - lock fleets by COORDINATES
      ($mission_data['dst_fleets'] ? "LEFT JOIN {{fleets}} AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих прилетающим и улетающим флотам - ufd = unit_fleet_destination
      ($mission_data['dst_fleets'] ? "LEFT JOIN {{unit}} AS ufd ON ufd.unit_location_type = " . static::$locationType . " AND ufd.unit_location_id = fd.fleet_id " : '') .

      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{users}} AS ud ON ud.id = f.fleet_target_owner " : '') .
      // Блокировка всех юнитов, принадлежащих владельцу планеты-цели
      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{unit}} AS unit_player_dest ON unit_player_dest.unit_player_id = ud.id " : '') .
      // Блокировка планеты-цели
      ($mission_data['dst_planet'] ? "LEFT JOIN {{planets}} AS pd ON pd.id = f.fleet_end_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих планете-цели - НЕ НУЖНО. Уже залочили ранее, как принадлежащие игроку-цели
//      ($mission_data['dst_planet'] ? "LEFT JOIN {{unit}} AS upd ON upd.unit_location_type = " . LOC_PLANET . " AND upd.unit_location_id = pd.id " : '') .


      ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{users}} AS us ON us.id = f.fleet_owner " : '') .
      // Блокировка всех юнитов, принадлежащих владельцу флота
      ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{unit}} AS unit_player_src ON unit_player_src.unit_player_id = us.id " : '') .
      // Блокировка планеты отправления
      ($mission_data['src_planet'] ? "LEFT JOIN {{planets}} AS ps ON ps.id = f.fleet_start_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих планете с которой юниты были отправлены - НЕ НУЖНО. Уже залочили ранее, как принадлежащие владельцу флота
//      ($mission_data['src_planet'] ? "LEFT JOIN {{unit}} AS ups ON ups.unit_location_type = " . LOC_PLANET . " AND ups.unit_location_id = ps.id " : '') .

      "WHERE f.fleet_id = {$fleet_id_safe} GROUP BY 1 FOR UPDATE"
    );
  }

  /**
   * Lock all fields that belongs to operation
   *
   * @param $dbId
   *
   * @internal param DBLock $dbRow - Object that accumulates locks
   *
   */
  // TODO = make static
  public function dbGetLockById($dbId) {
    doquery(
    // Блокировка самого флота
      "SELECT 1 FROM {{fleets}} AS FLEET0 " .
      // Lock fleet owner
      "LEFT JOIN {{users}} as USER0 on USER0.id = FLEET0.fleet_owner " .
      // Блокировка всех юнитов, принадлежащих этому флоту
      "LEFT JOIN {{unit}} as UNIT0 ON UNIT0.unit_location_type = " . LOC_FLEET . " AND UNIT0.unit_location_id = FLEET0.fleet_id " .

      // Без предварительной выборки неизвестно - куда летит этот флот.
      // Поэтому надо выбирать флоты, чьи координаты прибытия ИЛИ отбытия совпадают с координатами прибытия ИЛИ отбытия текущего флота.
      // Получаем матрицу 2х2 - т.е. 4 подзапроса.
      // При блокировке всегда нужно выбирать И лпанету, И луну - поскольку при бое на орбите луны обломки падают на орбиту планеты.
      // Поэтому тип планеты не указывается

      // Lock fleet heading to destination planet. Only if FLEET0.fleet_mess == 0
      "LEFT JOIN {{fleets}} AS FLEET1 ON
        FLEET1.fleet_mess = 0 AND FLEET0.fleet_mess = 0 AND
        FLEET1.fleet_end_galaxy = FLEET0.fleet_end_galaxy AND
        FLEET1.fleet_end_system = FLEET0.fleet_end_system AND
        FLEET1.fleet_end_planet = FLEET0.fleet_end_planet
      " .
      // Блокировка всех юнитов, принадлежащих этим флотам
      "LEFT JOIN {{unit}} as UNIT1 ON UNIT1.unit_location_type = " . LOC_FLEET . " AND UNIT1.unit_location_id = FLEET1.fleet_id " .
      // Lock fleet owner
      "LEFT JOIN {{users}} as USER1 on USER1.id = FLEET1.fleet_owner " .

      "LEFT JOIN {{fleets}} AS FLEET2 ON
        FLEET2.fleet_mess = 1   AND FLEET0.fleet_mess = 0 AND
        FLEET2.fleet_start_galaxy = FLEET0.fleet_end_galaxy AND
        FLEET2.fleet_start_system = FLEET0.fleet_end_system AND
        FLEET2.fleet_start_planet = FLEET0.fleet_end_planet
      " .
      // Блокировка всех юнитов, принадлежащих этим флотам
      "LEFT JOIN {{unit}} as UNIT2 ON
        UNIT2.unit_location_type = " . LOC_FLEET . " AND
        UNIT2.unit_location_id = FLEET2.fleet_id
      " .
      // Lock fleet owner
      "LEFT JOIN {{users}} as USER2 on
        USER2.id = FLEET2.fleet_owner
      " .

      // Lock fleet heading to source planet. Only if FLEET0.fleet_mess == 1
      "LEFT JOIN {{fleets}} AS FLEET3 ON
        FLEET3.fleet_mess = 0 AND FLEET0.fleet_mess = 1 AND
        FLEET3.fleet_end_galaxy = FLEET0.fleet_start_galaxy AND
        FLEET3.fleet_end_system = FLEET0.fleet_start_system AND
        FLEET3.fleet_end_planet = FLEET0.fleet_start_planet
      " .
      // Блокировка всех юнитов, принадлежащих этим флотам
      "LEFT JOIN {{unit}} as UNIT3 ON
        UNIT3.unit_location_type = " . LOC_FLEET . " AND
        UNIT3.unit_location_id = FLEET3.fleet_id
      " .
      // Lock fleet owner
      "LEFT JOIN {{users}} as USER3 on USER3.id = FLEET3.fleet_owner " .

      "LEFT JOIN {{fleets}} AS FLEET4 ON
        FLEET4.fleet_mess = 1   AND FLEET0.fleet_mess = 1 AND
        FLEET4.fleet_start_galaxy = FLEET0.fleet_start_galaxy AND
        FLEET4.fleet_start_system = FLEET0.fleet_start_system AND
        FLEET4.fleet_start_planet = FLEET0.fleet_start_planet
      " .
      // Блокировка всех юнитов, принадлежащих этим флотам
      "LEFT JOIN {{unit}} as UNIT4 ON
        UNIT4.unit_location_type = " . LOC_FLEET . " AND
        UNIT4.unit_location_id = FLEET4.fleet_id
      " .
      // Lock fleet owner
      "LEFT JOIN {{users}} as USER4 on
        USER4.id = FLEET4.fleet_owner
      " .


      // Locking start planet
      "LEFT JOIN {{planets}} AS PLANETS5 ON
        FLEET0.fleet_mess = 1 AND
        PLANETS5.galaxy = FLEET0.fleet_start_galaxy AND
        PLANETS5.system = FLEET0.fleet_start_system AND
        PLANETS5.planet = FLEET0.fleet_start_planet
      " .
      // Lock planet owner
      "LEFT JOIN {{users}} as USER5 on
        USER5.id = PLANETS5.id_owner
      " .
      // Блокировка всех юнитов, принадлежащих этой планете
      "LEFT JOIN {{unit}} as UNIT5 ON
        UNIT5.unit_location_type = " . LOC_PLANET . " AND
        UNIT5.unit_location_id = PLANETS5.id
      " .


      // Locking destination planet
      "LEFT JOIN {{planets}} AS PLANETS6 ON
        FLEET0.fleet_mess = 0 AND
        PLANETS6.galaxy = FLEET0.fleet_end_galaxy AND
        PLANETS6.system = FLEET0.fleet_end_system AND
        PLANETS6.planet = FLEET0.fleet_end_planet
      " .
      // Lock planet owner
      "LEFT JOIN {{users}} as USER6 on
        USER6.id = PLANETS6.id_owner
      " .
      // Блокировка всех юнитов, принадлежащих этой планете
      "LEFT JOIN {{unit}} as UNIT6 ON
        UNIT6.unit_location_type = " . LOC_PLANET . " AND
        UNIT6.unit_location_id = PLANETS6.id
      " .
      "WHERE FLEET0.fleet_id = {$dbId} GROUP BY 1 FOR UPDATE"
    );
  }


  public function dbRowParse($db_row) {
    parent::dbRowParse($db_row); // TODO: Change the autogenerated stub
    $player = new Player();
    $player->dbLoad($db_row['fleet_owner']);
    $this->setLocatedAt($player);
  }

  /* FLEET HELPERS =====================================================================================================*/
  /**
   * Forcibly returns fleet before time outs
   */
  public function commandReturn() {
    $ReturnFlyingTime = ($this->_time_mission_job_complete != 0 && $this->_time_arrive_to_target < SN_TIME_NOW ? $this->_time_arrive_to_target : SN_TIME_NOW) - $this->_time_launch + SN_TIME_NOW + 1;

    $this->markReturned();

    // Считаем, что флот уже долетел TODO
    $this->time_arrive_to_target = SN_TIME_NOW;
    // Убираем флот из группы
    $this->group_id = 0;
    // Отменяем работу в точке назначения
    $this->time_mission_job_complete = 0;
    // TODO - правильно вычслять время возвращения - по проделанному пути, а не по старому времени возвращения
    $this->time_return_to_source = $ReturnFlyingTime;

    // Записываем изменения в БД
    $this->dbSave();

    if($this->_group_id) {
      // TODO: Make here to delete only one AKS - by adding aks_fleet_count to AKS table
      db_fleet_aks_purge();
    }
  }


  /**
   * @return array
   */
  public function target_coordinates_without_type() {
    return array(
      'galaxy' => $this->_fleet_end_galaxy,
      'system' => $this->_fleet_end_system,
      'planet' => $this->_fleet_end_planet,
    );
  }

  /**
   * @return array
   */
  public function target_coordinates_typed() {
    return array(
      'galaxy' => $this->_fleet_end_galaxy,
      'system' => $this->_fleet_end_system,
      'planet' => $this->_fleet_end_planet,
      'type'   => $this->_fleet_end_type,
    );
  }

  /**
   * @return array
   */
  public function launch_coordinates_typed() {
    return array(
      'galaxy' => $this->_fleet_start_galaxy,
      'system' => $this->_fleet_start_system,
      'planet' => $this->_fleet_start_planet,
      'type'   => $this->_fleet_start_type,
    );
  }


  /**
   * Sets object fields for fleet return
   */
  public function markReturned() {
    // TODO - Проверка - а не возвращается ли уже флот?
    $this->is_returning = 1;
  }

  public function isReturning() {
    return 1 == $this->_is_returning;
  }

  public function markReturnedAndSave() {
    $this->markReturned();
    $this->dbSave();
  }

  /**
   * Parses extended unit_array which can include not only ships but resources, captains etc
   *
   * @param $unit_array
   */
  // TODO - separate shipList and unitList
  public function unitsSetFromArray($unit_array) {
    if(empty($unit_array) || !is_array($unit_array)) {
      return;
    }
    foreach($unit_array as $unit_id => $unit_count) {
      $unit_count = floatval($unit_count);
      if(!$unit_count) {
        continue;
      }

      if($this->isShip($unit_id)) {
        $this->unitList->unitSetCount($unit_id, $unit_count);
      } elseif($this->isResource($unit_id)) {
        $this->resource_list[$unit_id] = $unit_count;
      } else {
        throw new Exception('Trying to pass to fleet non-resource and non-ship ' . var_export($unit_array, true), ERR_ERROR);
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
    $this->_time_launch = $flight_departure;

    $this->_time_arrive_to_target = $this->_time_launch + $time_to_travel + $group_sync_delta_time;
    $this->_time_mission_job_complete = $time_on_mission ? $this->_time_arrive_to_target + $time_on_mission : 0;
    $this->_time_return_to_source = ($this->_time_mission_job_complete ? $this->_time_mission_job_complete : $this->_time_arrive_to_target) + $time_to_travel;
  }


  public function parse_missile_db_row($missile_db_row) {
//    $this->_reset();

    if(empty($missile_db_row) || !is_array($missile_db_row)) {
      return;
    }

//      $planet_start = db_planet_by_vector($irak_original, 'fleet_start_', false, 'name');
//      $irak_original['fleet_start_name'] = $planet_start['name'];
    $this->missile_target = $missile_db_row['primaer'];

    $this->_dbId = -$missile_db_row['id'];
    $this->_playerOwnerId = $missile_db_row['fleet_owner'];
    $this->_mission_type = MT_MISSILE;

    $this->_target_owner_id = $missile_db_row['fleet_target_owner'];

    $this->_group_id = 0;
    $this->_is_returning = 0;

    $this->_time_launch = 0; // $irak['start_time'];
    $this->_time_arrive_to_target = 0; // $irak['fleet_start_time'];
    $this->_time_mission_job_complete = 0; // $irak['fleet_end_stay'];
    $this->_time_return_to_source = $missile_db_row['fleet_end_time'];

    $this->_fleet_start_planet_id = !empty($missile_db_row['fleet_start_planet_id']) ? $missile_db_row['fleet_start_planet_id'] : null;
    $this->_fleet_start_galaxy = $missile_db_row['fleet_start_galaxy'];
    $this->_fleet_start_system = $missile_db_row['fleet_start_system'];
    $this->_fleet_start_planet = $missile_db_row['fleet_start_planet'];
    $this->_fleet_start_type = $missile_db_row['fleet_start_type'];

    $this->_fleet_end_planet_id = !empty($missile_db_row['fleet_end_planet_id']) ? $missile_db_row['fleet_end_planet_id'] : null;
    $this->_fleet_end_galaxy = $missile_db_row['fleet_end_galaxy'];
    $this->_fleet_end_system = $missile_db_row['fleet_end_system'];
    $this->_fleet_end_planet = $missile_db_row['fleet_end_planet'];
    $this->_fleet_end_type = $missile_db_row['fleet_end_type'];

    $this->unitList->unitSetCount(UNIT_DEF_MISSILE_INTERPLANET, $missile_db_row['fleet_amount']);
  }


  /**
   * @param $from
   */
  public function set_start_planet($from) {
    $this->fleet_start_planet_id = intval($from['id']) ? $from['id'] : null;
    $this->fleet_start_galaxy = $from['galaxy'];
    $this->fleet_start_system = $from['system'];
    $this->fleet_start_planet = $from['planet'];
    $this->fleet_start_type = $from['planet_type'];
  }

  /**
   * @param $to
   */
  public function set_end_planet($to) {
    $this->target_owner_id = intval($to['id_owner']) ? $to['id_owner'] : 0;
    $this->fleet_end_planet_id = intval($to['id']) ? $to['id'] : null;
    $this->fleet_end_galaxy = $to['galaxy'];
    $this->fleet_end_system = $to['system'];
    $this->fleet_end_planet = $to['planet'];
    $this->fleet_end_type = $to['planet_type'];
  }

  /**
   * @param Vector $to
   */
  public function setTargetFromVectorObject($to) {
    $this->_fleet_end_galaxy = $to->galaxy;
    $this->_fleet_end_system = $to->system;
    $this->_fleet_end_planet = $to->planet;
    $this->_fleet_end_type = $to->type;
  }

  /**
   * @param array $db_row
   */
  protected function ownerExtract(array &$db_row) {
    $player = new Player();
    $player->dbLoad($db_row['fleet_owner']);
    $this->setLocatedAt($player);
  }

  /**
   * @param array $db_row
   */
  protected function ownerInject(array &$db_row) {
    $db_row['fleet_owner'] = $this->getPlayerOwnerId();
  }




  // UnitList/Ships access ***************************************************************************************************

  // TODO - перекрывать пожже - для миссайл-флотов и дефенс-флотов
  protected function isShip($unit_id) {
    return UnitShip::is_in_group($unit_id);
  }

  /**
   * Set unit count of $unit_id to $unit_count
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int $unit_id
   * @param int $unit_count
   */
  public function shipSetCount($unit_id, $unit_count = 0) {
    $this->shipAdjustCount($unit_id, $unit_count, true);
  }

  /**
   * Adjust unit count of $unit_id by $unit_count - or just replace value
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int  $unit_id
   * @param int  $unit_count
   * @param bool $replace_value
   */
  public function shipAdjustCount($unit_id, $unit_count = 0, $replace_value = false) {
    $this->unitList->unitAdjustCount($unit_id, $unit_count, $replace_value);
  }

  public function shipGetCount($unit_id) {
    return $this->unitList->unitGetCount($unit_id);
  }

  public function shipsCountApplyLossMultiplier($ships_lost_multiplier) {
    $this->unitList->unitsCountApplyLossMultiplier($ships_lost_multiplier);
  }

  /**
   * Returns ship list in fleet
   */
  public function shipsGetArray() {
    return $this->unitList->unitsGetArray();
  }

  public function shipsGetTotal() {
    return $this->unitList->unitsCount();
  }

  public function shipsGetCapacity() {
    return $this->unitList->unitsCapacity();
  }

  public function shipsGetHoldFree() {
    return max(0, $this->shipsGetCapacity() - $this->resourcesGetTotal());
  }

  public function shipsGetTotalById($ship_id) {
    return $this->unitList->unitsCountById($ship_id);
  }

  /**
   * Возвращает ёмкость переработчиков во флоте
   *
   * @param array $recycler_info
   *
   * @return int
   *
   * @version 41a6.76
   */
  public function shipsGetCapacityRecyclers(array $recycler_info) {
    $recyclers_incoming_capacity = 0;
    $fleet_data = $this->shipsGetArray();
    foreach($recycler_info as $recycler_id => $recycler_data) {
      $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
    }

    return $recyclers_incoming_capacity;
  }

  /**
   * Restores fleet or resources to planet
   *
   * @param bool $start
   * @param int  $result
   *
   * @return int
   */
  // TODO - split to functions
  public function shipsLand($start = true, &$result = CACHE_NOTHING) {
    sn_db_transaction_check(true);

    // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
    if($this->isEmpty()) {
      return $result;
    }

    $coordinates = $start ? $this->launch_coordinates_typed() : $this->target_coordinates_typed();

    // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
    // TODO Проверить от многократного срабатывания !!!
    // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
    // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!

    // Узнаем ИД владельца планеты.
    // С блокировкой, поскольку эта функция может быть вызвана только из менеджера летящих флотов.
    // А там уже всё заблокировано как надо и повторная блокировка не вызовет дедлок.
    $planet_arrival = db_planet_by_vector($coordinates, '', true);
    // Блокируем пользователя
    // TODO - вообще-то нам уже известен пользователь в МЛФ - так что можно просто передать его сюда
    $user = db_user_by_id($planet_arrival['id_owner'], true);

    // TODO - Проверка, что планета всё еще существует на указанных координатах, а не телепортировалась, не удалена хозяином, не уничтожена врагом
    // Флот, который возвращается на захваченную планету, пропадает
    // Ship landing is possible only to fleet owner's planet
    if($this->getPlayerOwnerId() == $planet_arrival['id_owner']) {
      $db_changeset = array();

      $fleet_array = $this->shipsGetArray();
      foreach($fleet_array as $ship_id => $ship_count) {
        if($ship_count) {
          $db_changeset['unit'][] = sn_db_unit_changeset_prepare($ship_id, $ship_count, $user, $planet_arrival['id']);
        }
      }

      // Adjusting ship amount on planet
      if(!empty($db_changeset)) {
        db_changeset_apply($db_changeset);
      }

      // Restoring resources to planet
      $this->resourcesUnload($start, $result);
    }

    $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

    $result = RestoreFleetToPlanet($this, $start, $result);

    $this->dbDelete();

    return $result;
  }


  // Resources access ***************************************************************************************************

  /**
   * Extracts resources value from db_row
   *
   * @param array $db_row
   *
   * @internal param Fleet $that
   * @version 41a6.76
   */
  protected function resourcesExtract(array &$db_row) {
    $this->resource_list = array(
      RES_METAL     => !empty($db_row['fleet_resource_metal']) ? floor($db_row['fleet_resource_metal']) : 0,
      RES_CRYSTAL   => !empty($db_row['fleet_resource_crystal']) ? floor($db_row['fleet_resource_crystal']) : 0,
      RES_DEUTERIUM => !empty($db_row['fleet_resource_deuterium']) ? floor($db_row['fleet_resource_deuterium']) : 0,
    );
  }

  protected function resourcesInject(array &$db_row) {
    $db_row['fleet_resource_metal'] = $this->resource_list[RES_METAL];
    $db_row['fleet_resource_crystal'] = $this->resource_list[RES_CRYSTAL];
    $db_row['fleet_resource_deuterium'] = $this->resource_list[RES_DEUTERIUM];
  }

  /**
   * Set current resource list from array of units
   *
   * @param array $resource_list
   */
  public function resourcesSet($resource_list) {
    if(!empty($this->propertiesAdjusted['resource_list'])) {
      throw new PropertyAccessException('Property "resource_list" already was adjusted so no SET is possible until dbSave in ' . get_called_class() . '::unitSetResourceList', ERR_ERROR);
    }
    $this->resourcesAdjust($resource_list, true);
  }

  /**
   * Updates fleet resource list with deltas
   *
   * @param $resource_delta_list
   */
  public function resourcesAdjust($resource_delta_list, $replace_value = false) {
    !is_array($resource_delta_list) ? $resource_delta_list = array() : false;

    foreach($resource_delta_list as $resource_id => $unit_delta) {
      if(!UnitResourceLoot::is_in_group($resource_id) || !($unit_delta = floor($unit_delta))) {
        // Not a resource or no resources - continuing
        continue;
      }

      if($replace_value) {
        $this->resource_list[$resource_id] = $unit_delta;
      } else {
        $this->resource_list[$resource_id] += $unit_delta;
        // Preparing changes
        $this->resource_delta[$resource_id] += $unit_delta;
        $this->propertiesAdjusted['resource_list'] = 1;
      }

      // Check for negative unit value
      if($this->resource_list[$resource_id] < 0) {
        // TODO
        throw new Exception('Resource ' . $resource_id . ' will become negative in ' . get_called_class() . '::unitAdjustResourceList', ERR_ERROR);
      }
    }
  }

  public function resourcesGetTotal() {
    return empty($this->resource_list) || !is_array($this->resource_list) ? 0 : array_sum($this->resource_list);
  }

  /**
   * @param array $rate
   *
   * @return float
   */
  public function resourcesGetTotalInMetal(array $rate) {
    return
      $this->resource_list[RES_METAL] * $rate[RES_METAL]
      + $this->resource_list[RES_CRYSTAL] * $rate[RES_CRYSTAL] / $rate[RES_METAL]
      + $this->resource_list[RES_DEUTERIUM] * $rate[RES_DEUTERIUM] / $rate[RES_METAL];
  }

  /**
   * Returns resource list in fleet
   */
  // TODO
  public function resourcesGetList() {
    return $this->resource_list;
  }

  public function resourcesReset() {
    $this->resourcesSet(array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    ));
  }

  /**
   * Restores fleet or resources to planet
   *
   * @param bool $start
   * @param bool $only_resources
   * @param int  $result
   *
   * @return int
   */
  public function resourcesUnload($start = true, &$result = CACHE_NOTHING) {
    sn_db_transaction_check(true);

    // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
    if(!$this->resourcesGetTotal()) {
      return $result;
    }

    $coordinates = $start ? $this->launch_coordinates_typed() : $this->target_coordinates_typed();

    // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
    // TODO Проверить от многократного срабатывания !!!
    // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
    // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!


    // Узнаем ИД владельца планеты.
    // С блокировкой, поскольку эта функция может быть вызвана только из менеджера летящих флотов.
    // А там уже всё заблокировано как надо и повторная блокировка не вызовет дедлок.
    $planet_arrival = db_planet_by_vector($coordinates, '', true);

    // TODO - Проверка, что планета всё еще существует на указанных координатах, а не телепортировалась, не удалена хозяином, не уничтожена врагом

    // Restoring resources to planet
    if($this->resourcesGetTotal()) {
      $fleet_resources = $this->resourcesGetList();
      db_planet_set_by_id($planet_arrival['id'],
        "`metal` = `metal` + '{$fleet_resources[RES_METAL]}', `crystal` = `crystal` + '{$fleet_resources[RES_CRYSTAL]}', `deuterium` = `deuterium` + '{$fleet_resources[RES_DEUTERIUM]}'");
    }

    $this->resourcesReset();
    $this->markReturned();

    $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

    return $result;
  }


  protected function isResource($unit_id) {
    return UnitResourceLoot::is_in_group($unit_id);
  }


  /**
   * @param array  $dbPlayerRow
   * @param array  $dbPlanetRow
   * @param Vector $targetVector
   *
   */
  public function initDefaults($dbPlayerRow, $dbPlanetRow, $targetVector, $mission, $ships, $fleet_group_mr) {
    $objFleet5Player = new Player();
    $objFleet5Player->dbRowParse($dbPlayerRow);
    $this->setLocatedAt($objFleet5Player);

    $this->mission_type = $mission;

    $this->dbOwnerRow = $dbPlayerRow;

    $this->set_start_planet($dbPlanetRow);
    $this->dbSourcePlanetRow = $dbPlanetRow;

    $this->setTargetFromVectorObject($targetVector);
    $this->targetVector = $targetVector;
//pdump($targetVector);pdie();
    $this->populateTargetPlanet();

    $this->unitsSetFromArray($ships);

    $this->_group_id = $fleet_group_mr;

//    $this->restrictTargetTypeByMission();
  }

  protected function populateTargetPlanet() {
    $targetPlanetCoords = $this->targetVector;
    if($this->mission_type != MT_NONE) {
      $this->restrictTargetTypeByMission();

      // TODO - Нельзя тут просто менять тип планеты или координат!
      // If current planet type is not allowed on mission - switch planet type
      if(empty($this->allowed_planet_types[$this->targetVector->type])) {
        $targetPlanetCoords->type = reset($this->allowed_planet_types);
      }
    }

    $this->dbTargetRow = db_planet_by_vector_object($targetPlanetCoords);
  }

  protected function restrictTargetTypeByMission() {
    if($this->_mission_type == MT_MISSILE) {
      $this->allowed_planet_types = array(PT_PLANET => PT_PLANET);
    } elseif($this->_mission_type == MT_COLONIZE || $this->_mission_type == MT_EXPLORE) {
      // TODO - PT_NONE
      $this->allowed_planet_types = array(PT_PLANET => PT_PLANET);
    } elseif($this->_mission_type == MT_RECYCLE) {
      $this->allowed_planet_types = array(PT_DEBRIS => PT_DEBRIS);
    } elseif($this->_mission_type == MT_DESTROY) {
      $this->allowed_planet_types = array(PT_MOON => PT_MOON);
    } else {
      $this->allowed_planet_types = array(PT_PLANET => PT_PLANET, PT_MOON => PT_MOON);
    }
  }

  /**
   */
  public function fleetPage0Prepare() {
    global $template_result;
    $template_result += array(
      'thisgalaxy'      => $this->dbSourcePlanetRow['galaxy'],
      'thissystem'      => $this->dbSourcePlanetRow['system'],
      'thisplanet'      => $this->dbSourcePlanetRow['planet'],
      'thisplanet_type' => $this->dbSourcePlanetRow['planet_type'],

      'galaxy'         => $this->targetVector->galaxy,
      'system'         => $this->targetVector->system,
      'planet'         => $this->targetVector->planet,
      'planet_type'    => $this->targetVector->type,
      'target_mission' => $this->_mission_type,
      'MISSION_NAME'   => $this->_mission_type ? classLocale::$lang['type_mission'][$this->_mission_type] : '',

      'MT_COLONIZE' => MT_COLONIZE,
    );

//    pdump($this->targetVector->type);pdie();
  }

  /**
   */
  public function fleetPage0() {
    global $template_result;

    lng_include('overview');

    if(empty($this->dbSourcePlanetRow)) {
      message(classLocale::$lang['fl_noplanetrow'], classLocale::$lang['fl_error']);
    }

    $template = gettemplate('fleet0', true);

    $ship_list = $this->tplRenderAvailableShips($this->dbOwnerRow, $this->dbSourcePlanetRow);
    $this->tplSortShipList($ship_list);

    foreach($ship_list as $ship_data) {
      $template->assign_block_vars('ships', $ship_data);
    }

    foreach(classLocale::$lang['player_option_fleet_ship_sort'] as $sort_id => $sort_text) {
      $template->assign_block_vars('ship_sort_list', array(
        'VALUE' => $sort_id,
        'TEXT'  => $sort_text,
      ));
    }

    /**
     * @var Player $playerOwner
     */
    $playerOwner = $this->getLocatedAt();

    $template->assign_vars(array(
      'FLEET_SHIP_SORT'         => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT],
      'FLEET_SHIP_SORT_INVERSE' => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE],

      'FLYING_FLEETS' => $playerOwner->fleetsFlying(),
      'MAX_FLEETS'    => $playerOwner->fleetsMax(),
      'FREE_FLEETS'   => $playerOwner->fleetsMax() - $playerOwner->fleetsFlying(),

      'FLYING_EXPEDITIONS' => $playerOwner->expeditionsFlying(),
      'MAX_EXPEDITIONS'    => $playerOwner->expeditionsMax(),
      'FREE_EXPEDITIONS'   => $playerOwner->expeditionsMax() - $playerOwner->expeditionsFlying(),

      'COLONIES_CURRENT' => $playerOwner->coloniesCurrent(),
      'COLONIES_MAX'     => $playerOwner->coloniesMax(),

      'TYPE_NAME' => classLocale::$lang['fl_planettype'][$this->targetVector->type],

//      'ShipList' => $ShipList,

      'speed_factor' => flt_server_flight_speed_multiplier(),

      'PLANET_RESOURCES' => pretty_number($this->dbSourcePlanetRow['metal'] + $this->dbSourcePlanetRow['crystal'] + $this->dbSourcePlanetRow['deuterium']),
      'PLANET_DEUTERIUM' => pretty_number($this->dbSourcePlanetRow['deuterium']),

      'PLAYER_OPTION_FLEET_SHIP_SELECT_OLD'       => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SELECT_OLD],
      'PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED'       => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED],
      'PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY'    => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY],
      'PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION' => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION],
    ));

    $template->assign_recursive($template_result);
    display($template, classLocale::$lang['fl_title']);
  }

  /**
   * @param $planet_type
   *
   */
  public function fleetPage1($planet_type) {
    global $note_priority_classes, $template_result;

    $template = gettemplate('fleet1', true);


    foreach($this->allowed_planet_types as $possible_planet_type_id) {
      $template->assign_block_vars('possible_planet_type_id', array(
        'ID'         => $possible_planet_type_id,
        'NAME'       => classLocale::$lang['sys_planet_type'][$possible_planet_type_id],
        'NAME_SHORT' => classLocale::$lang['sys_planet_type_sh'][$possible_planet_type_id],
      ));
    }

    $template_route = array(
      'START_TYPE_TEXT_SH' => classLocale::$lang['sys_planet_type_sh'][$this->dbSourcePlanetRow['planet_type']],
      'START_COORDS'       => uni_render_coordinates($this->dbSourcePlanetRow),
      'START_NAME'         => $this->dbSourcePlanetRow['name'],
    );
    if(!empty($this->dbTargetRow)) {
      $template_route += array(
        'END_TYPE_TEXT_SH' => classLocale::$lang['sys_planet_type_sh'][$this->dbTargetRow['planet_type']],
        'END_COORDS'       => uni_render_coordinates($this->dbTargetRow),
        'END_NAME'         => $this->dbTargetRow['name'],
      );
    }
    $template->assign_block_vars('fleets', $template_route);

    $page = '';
    $fleet = array();
    $this->unitList->unitsRender($page, $fleet, $template);

    if(empty($fleet['fleetarray'])) {
      message(classLocale::$lang['fl_err_no_ships'], classLocale::$lang['fl_error'], 'fleet' . DOT_PHP_EX, 5);
    }

    // Building list of shortcuts
    $query = db_note_list_select_by_owner_and_planet($this->dbOwnerRow);
    while($shortcut = db_fetch($query)) {
      $template->assign_block_vars('shortcut', array(
        'NAME'           => $shortcut['title'],
        'GALAXY'         => $shortcut['galaxy'],
        'SYSTEM'         => $shortcut['system'],
        'PLANET'         => $shortcut['planet'],
        'PRIORITY'       => $shortcut['priority'],
        'PRIORITY_CLASS' => $note_priority_classes[$shortcut['priority']],
        'TYPE'           => $shortcut['planet_type'],
        'TYPE_PRINT'     => classLocale::$lang['fl_shrtcup'][$shortcut['planet_type']],
      ));
    }

    // Building list of own planets & moons
    $colonies = db_planet_list_sorted($this->dbOwnerRow);
    if(count($colonies) > 1) {
      foreach($colonies as $row) {
        if($row['id'] == $this->dbSourcePlanetRow['id']) {
          continue;
        }

        $template->assign_block_vars('colonies', array(
          'NAME'       => $row['name'],
          'GALAXY'     => $row['galaxy'],
          'SYSTEM'     => $row['system'],
          'PLANET'     => $row['planet'],
          'TYPE'       => $row['planet_type'],
          'TYPE_PRINT' => classLocale::$lang['fl_shrtcup'][$row['planet_type']],
        ));
      }
    }

    $aks_madnessred = db_acs_get_list();
    while($row = db_fetch($aks_madnessred)) {
      $members = explode(',', $row['eingeladen']);
      foreach($members as $a => $b) {
        if($b == $this->dbOwnerRow['id']) {
          $template->assign_block_vars('acss', array(
            'ID'         => $row['id'],
            'NAME'       => $row['name'],
            'GALAXY'     => $row['galaxy'],
            'SYSTEM'     => $row['system'],
            'PLANET'     => $row['planet'],
            'TYPE'       => $row['planet_type'],
            'TYPE_PRINT' => classLocale::$lang['fl_shrtcup'][$row['planet_type']],
          ));
        }
      }
    }

    $template->assign_vars(array(
      'usedfleet' => str_rot13(base64_encode(serialize($fleet['fleetarray']))),

      'speed_factor'    => flt_server_flight_speed_multiplier(),
      "t{$planet_type}" => 'SELECTED',

      'ships' => str_rot13(base64_encode(serialize($ships))),

      'fleet_speed'    => flt_fleet_speed($this->dbOwnerRow, $fleet['fleetarray']),
      'fleet_capacity' => $fleet['capacity'],

      'PLANET_DEUTERIUM' => pretty_number($this->dbSourcePlanetRow['deuterium']),

      'PAGE_HINT' => classLocale::$lang['fl_page1_hint'],
    ));

    $template->assign_recursive($template_result);
    display($template, classLocale::$lang['fl_title']);
  }

  public function restrictToKnownSpace() {
    if(!$this->targetVector->isInKnownSpace()) {
      throw new Exception('FLIGHT_VECTOR_BEYOND_SYSTEM', FLIGHT_VECTOR_BEYOND_SYSTEM);
    }
  }

  public function restrictToPlanet($errorCode) {
    if($this->targetVector->type != PT_PLANET) {
      throw new Exception($errorCode, $errorCode);
    }
  }

  public function restrictToNoMissiles() {
    $missilesAttack = $this->unitList->unitsCountById(UNIT_DEF_MISSILE_INTERPLANET);
    $missilesDefense = $this->unitList->unitsCountById(UNIT_DEF_MISSILE_INTERCEPTOR);
    if($missilesAttack + $missilesDefense == $this->shipsGetTotal()) {
      throw new Exception('FLIGHT_SHIPS_NO_MISSILES', FLIGHT_SHIPS_NO_MISSILES);
    }
  }

  public function restrictToAttackMissiles($missilesAttack) {
    if($missilesAttack != $this->shipsGetTotal()) {
      throw new Exception('FLIGHT_SHIPS_ONLY_MISSILES', FLIGHT_SHIPS_ONLY_MISSILES);
    }
  }

  public function restrictToSelf() {
    if($this->dbTargetRow['id'] != $this->getPlayerOwnerId()) {
      throw new Exception('FLIGHT_VECTOR_ONLY_OWN', FLIGHT_VECTOR_ONLY_OWN);
    }
  }

  public function restrictToOther() {
    if($this->dbTargetRow['id'] == $this->getPlayerOwnerId()) {
      throw new Exception('FLIGHT_VECTOR_ONLY_OTHER', FLIGHT_VECTOR_ONLY_OTHER);
    }
  }

  public function restrictToNotOnlySpies() {
    if($this->unitList->unitsCountById(SHIP_SPY) == $this->shipsGetTotal()) {
      throw new Exception('FLIGHT_SHIPS_NOT_ONLY_SPIES', FLIGHT_SHIPS_NOT_ONLY_SPIES);
    }
  }

  public function restrictMissionMissile() {
    $missilesAttack = $this->unitList->unitsCountById(UNIT_DEF_MISSILE_INTERPLANET);
    $missilesDefense = $this->unitList->unitsCountById(UNIT_DEF_MISSILE_INTERCEPTOR);
    if($this->_mission_type == MT_MISSILE) {
      $this->restrictToAttackMissiles($missilesAttack);
      $this->restrictToKnownSpace();
      $this->restrictToOther();
      $this->restrictToPlanet(FLIGHT_MISSION_MISSILE_ONLY_PLANET);

      $this->allowed_missions = array(MT_MISSILE => MT_MISSILE,);

      throw new Exception('FLIGHT_ALLOWED', FLIGHT_ALLOWED);
    }

    if($missilesAttack + $missilesDefense > 0) {
      throw new Exception('FLIGHT_SHIPS_NO_MISSILES', FLIGHT_SHIPS_NO_MISSILES);
    }

    unset($this->allowed_missions[MT_MISSILE]);
    // No missile attack beyond this point
  }

  protected function restrictMissionExplore() {
    // Is it exploration - fleet sent beyond of system?
    if(!$this->targetVector->isInKnownSpace()) {
      $this->restrictToNotOnlySpies();
      $this->restrictToNoMissiles();

      $this->allowed_missions = array(MT_EXPLORE => MT_EXPLORE,);

      throw new Exception('FLIGHT_ALLOWED', FLIGHT_ALLOWED);
    }

    unset($this->allowed_missions[MT_EXPLORE]);
    // No exploration beyond this point
  }

  protected function restrictToColonizer() {
    // Colonization fleet should have at least one colonizer
    if(!$this->unitList->unitsCountById(SHIP_COLONIZER) <= 0) {
      throw new Exception('FLIGHT_SHIPS_NO_COLONIZER', FLIGHT_SHIPS_NO_COLONIZER);
    }
  }

  protected function restrictMissionColonize() {
    // Is it colonization - fleet sent to empty place?
    if(empty($this->dbTargetRow)) {
      // Only planet can be destination for colonization
      $this->restrictToPlanet(FLIGHT_MISSION_COLONIZE_NOT_EMPTY);
      $this->restrictToColonizer();

      $this->allowed_missions = array(MT_COLONIZE => MT_COLONIZE,);

      throw new Exception('FLIGHT_ALLOWED', FLIGHT_ALLOWED);
    }
    unset($this->allowed_missions[MT_COLONIZE]);
    // No colonization beyond this point
  }

  protected function restrictToUniverse() {
    return $this->targetVector->isInUniverse();
  }

  protected function restrictToMovable() {
    if(!$this->unitList->unitsIsAllMovable($this->dbOwnerRow)) {
      throw new Exception('FLIGHT_SHIPS_UNMOVABLE', FLIGHT_SHIPS_UNMOVABLE);
    }
  }

  protected function restrictToFleetUnits() {
    if(!$this->unitList->unitsInGroup(sn_get_groups(array('fleet', 'missile')))) {
      throw new Exception('FLIGHT_SHIPS_UNIT_WRONG', FLIGHT_SHIPS_UNIT_WRONG);
    }
  }

  public function restrictMissionRecycle(){
    if($this->targetVector->type == PT_DEBRIS) {
      $recyclers = 0;
      foreach(sn_get_groups('flt_recyclers') as $recycler_id) {
        $recyclers += $this->unitList->unitsCountById($recycler_id);
      }

      if($recyclers <= 0) {
        throw new Exception('FLIGHT_SHIPS_NO_RECYCLERS', FLIGHT_SHIPS_NO_RECYCLERS);
      }

      $this->allowed_missions = array(MT_RECYCLE => MT_RECYCLE,);

      throw new Exception('FLIGHT_ALLOWED', FLIGHT_ALLOWED);
    }

    unset($this->allowed_missions[MT_RECYCLE]);
    // No recycling beyond this point
  }

  public function restrictMission() {
    if($this->targetVector->isEqualToPlanet($this->dbSourcePlanetRow)) {
      throw new Exception('FLIGHT_VECTOR_SAME_SOURCE', FLIGHT_VECTOR_SAME_SOURCE);
    }

    // Only ships and missiles can be sent to mission
    $this->restrictToFleetUnits();

    // Only units with engines can fly - no other units like satellites
    $this->restrictToMovable();

    // No mission could fly beyond Universe - i.e. with wrong Galaxy and/or System coordinates
    $this->restrictToUniverse();

    // No missions except MT_MISSILE should have any missiles in fleet
    $this->restrictMissionMissile();

    // No missions except MT_EXPLORE could target coordinates beyond known system
    $this->restrictMissionExplore();

    // Beyond this point all mission address only known space
    $this->restrictToKnownSpace();

    // No missions except MT_COLONIZE could target empty coordinates
    $this->restrictMissionColonize();

    // No missions except MT_RECYCLE could target debris
    $this->restrictMissionRecycle();

    // TODO - later then

    // If no ACS group is shown - then it can't be an ACS attack
    if(empty($this->_group_id)) {
      unset($this->allowed_missions[MT_ACS]);
    }

    // Checking target owner
    if($this->dbTargetRow['id'] != $this->getPlayerOwnerId()) {
      // Relocate can be done only on owner's planet/moon
      unset($this->allowed_missions[MT_RELOCATE]);
    } else {
      // Spying can't be done on owner's planet/moon
      unset($this->allowed_missions[MT_SPY]);
      // Attack can't be done on owner's planet/moon
      unset($this->allowed_missions[MT_ATTACK]);
      // ACS can't be done on owner's planet/moon
      unset($this->allowed_missions[MT_ACS]);
      // Destroy can't be done on owner's moon
      unset($this->allowed_missions[MT_DESTROY]);
    }

    // If no Reapers (i.e. Death Star) in fleet - then mission Moon Destroy is unaccessible
    if($this->targetVector->type == PT_MOON && $this->unitList->unitsCountById(SHIP_HUGE_DEATH_STAR) <= 0) {
      unset($this->allowed_missions[MT_DESTROY]);
    }
  }

  public function fleetPage2($speed_percent) {
    global $template_result;

    try {
      $this->restrictMission();
    } catch(Exception $e) {
      // TODO - MESSAGE BOX
      pdie(classLocale::$lang['fl_attack_error'][$e->getCode()]);
    }

    // If mission is not set - setting first mission from allowed
    if(!$this->_mission_type && is_array($this->allowed_missions)) {
      $this->_mission_type = reset($this->allowed_missions);
    }

    ksort($this->allowed_missions);

    $travel_data = $this->flt_travel_data($speed_percent);

    $template = gettemplate('fleet2', true);

    foreach($this->allowed_missions as $key => $value) {
      $template->assign_block_vars('missions', array(
        'ID'   => $key,
        'NAME' => classLocale::$lang['type_mission'][$key],
      ));
    };

    $fleetarray = is_array($fleetarray) ? $fleetarray : array();

    $template_route = array(
      'START_TYPE_TEXT_SH' => classLocale::$lang['sys_planet_type_sh'][$planetrow['planet_type']],
      'START_COORDS'       => uni_render_coordinates($planetrow),
      'START_NAME'         => $planetrow['name'],
    );

    if(!empty($TargetPlanet)) {
      $template_route += array(
        'END_TYPE_TEXT_SH' => classLocale::$lang['sys_planet_type_sh'][$TargetPlanet['planet_type']],
        'END_COORDS'       => uni_render_coordinates($TargetPlanet),
        'END_NAME'         => $TargetPlanet['name'],
      );
    }

    $template->assign_block_vars('fleets', $template_route);

    $sn_groups_fleet = sn_get_groups('fleet');
    foreach($fleetarray as $ship_id => $ship_count) {
      if(in_array($ship_id, $sn_groups_fleet) && $ship_count) {
//      $ship_base_data = get_ship_data($ship_id, $user);
        $template->assign_block_vars('fleets.ships', array(
          'ID'          => $ship_id,
          'AMOUNT'      => $ship_count,
          'AMOUNT_TEXT' => pretty_number($ship_count),
//        'CONSUMPTION' => $ship_base_data['consumption'],
//        'SPEED'       => $ship_base_data['speed'],
          'NAME'        => classLocale::$lang['tech'][$ship_id],
        ));
      }
    }

    $fleet_capacity = 0;
    foreach($fleetarray as $Ship => $Count) {
      $fleet_capacity += get_unit_param($Ship, P_CAPACITY) * $Count;
    }

    $max_duration = $this->_mission_type == MT_EXPLORE ? get_player_max_expedition_duration($user) :
      (isset($missiontype[MT_HOLD]) ? 12 : 0);
    if($max_duration) {
      $config_game_speed_expedition = ($this->_mission_type == MT_EXPLORE && classSupernova::$config->game_speed_expedition ? classSupernova::$config->game_speed_expedition : 1);
      for($i = 1; $i <= $max_duration; $i++) {
        $template->assign_block_vars('duration', array(
          'ID'   => $i,
          'TIME' => pretty_time(ceil($i * 3600 / $config_game_speed_expedition)),
        ));
      }
    }

    $sn_group_resources = sn_get_groups('resources');
    for($i = 0; $i < 3; $i++) {
      $amount = $planetrow[$sn_group_resources[$i]] - ($i == 2 ? $travel_data['consumption'] : 0);
      $template->assign_block_vars('resources', array(
        'ID'        => $i,
        'ON_PLANET' => floor($amount),
        'TEXT'      => pretty_number($amount),
        'NAME'      => classLocale::$lang['sys_' . $sn_group_resources[$i]],
      ));
    }

    if(sn_module::$sn_module['unit_captain']->manifest['active'] && ($captain = sn_module::$sn_module['unit_captain']->unit_captain_get($planetrow['id'])) && $captain['unit_location_type'] == LOC_PLANET) {
      $template->assign_vars(array(
        'CAPTAIN_ID'     => $captain['unit_id'],
        'CAPTAIN_LEVEL'  => $captain['captain_level'],
        'CAPTAIN_SHIELD' => $captain['captain_shield'],
        'CAPTAIN_ARMOR'  => $captain['captain_armor'],
        'CAPTAIN_ATTACK' => $captain['captain_attack'],
      ));
    }

    $template->assign_vars(array(
      'planet_metal'     => floor($this->dbTargetRow['metal']),
      'planet_crystal'   => floor($this->dbTargetRow['crystal']),
      'planet_deuterium' => floor($this->dbTargetRow['deuterium'] - $travel_data['consumption']),

      'fleet_capacity' => $fleet_capacity - $travel_data['consumption'],
      'usedfleet'      => $_POST['usedfleet'],

      'speedallsmin' => sys_get_param_float('speedallsmin'),
      'speed'        => sys_get_param_int('speed'),

      'fleet_group'   => sys_get_param_id('fleet_group'),
      'acs_target_mr' => sys_get_param_str('acs_target_mr'),

      'MAX_DURATION' => $max_duration,

      'IS_TRANSPORT_MISSIONS' => $is_transport_missions,

      'PLAYER_COLONIES_CURRENT' => get_player_current_colonies($user),
      'PLAYER_COLONIES_MAX'     => get_player_max_colonies($user),
    ));

    $template->assign_recursive($template_result);
    display($template, classLocale::$lang['fl_title']);
  }


  /**
   * @param int $speed_percent
   *
   * @return array
   */
  protected function flt_travel_data($speed_percent = 10) {
    $distance = $this->targetVector->distanceFromCoordinates($this->dbSourcePlanetRow);

    return $this->unitList->travelData($speed_percent, $distance, $this->dbOwnerRow);
  }

}
