<?php

/**
 * Class Unit
 */
class Unit extends DBRow {
  // Inherited from DBRow
  public $db_id = 0;
  /**
   * Table name in DB
   *
   * @var string
   */
  protected static $_table = 'unit';
  /**
   * Name of ID field in DB
   *
   * @var string
   */
  protected static $_dbIdFieldName = 'unit_id';
  /**
   * DB_ROW to Class translation scheme
   *
   * @var array
   */
  protected static $_scheme = array(
    'db_id'        => array(
      P_DB_FIELD => 'unit_id',
//      P_FUNC_INPUT => 'floatval',
    ),
    'ownerId'      => array(
      P_DB_FIELD => 'unit_player_id',
//      P_FUNC_INPUT => 'floatval',
    ),
    'locationType' => array(
      P_DB_FIELD   => 'unit_location_type',
      P_FUNC_INPUT => 'intval',
    ),
    'locationId'   => array(
      P_DB_FIELD => 'unit_location_id',
//      P_FUNC_INPUT => 'floatval',
    ),
    'type'         => array(
      P_DB_FIELD   => 'unit_type',
      P_FUNC_INPUT => 'intval',
    ),
    'unitId'       => array(
      P_DB_FIELD => 'unit_snid',
//      P_FUNC_INPUT => 'floatval',
    ),
    'count'        => array(
      P_DB_FIELD   => 'unit_level',
      P_FUNC_INPUT => 'floatval',
    ),
//    'timeStartSql'  => array(
//      P_DB_FIELD => 'unit_time_start',
//    ),
//    'timeFinishSql' => array(
//      P_DB_FIELD => 'unit_time_finish',
//    ),
    'timeStart'    => array(
      P_DB_FIELD    => 'unit_time_start',
      P_FUNC_INPUT  => 'strtotime',
      P_FUNC_OUTPUT => 'unixTimeStampToSqlString',
    ),
    'timeFinish'   => array(
      P_DB_FIELD    => 'unit_time_finish',
      P_FUNC_INPUT  => 'strtotime',
      P_FUNC_OUTPUT => 'unixTimeStampToSqlString',
    ),
  );

  // Innate statics
  /**
   * @var bool
   */
  protected static $_is_static_init = false;
  /**
   * @var string
   */
  protected static $_sn_group_name = '';
  /**
   * @var array
   */
  protected static $_group_unit_id_list = array();

  public $unitId = 0;
  public $count = 0;
  public $type = 0;

  public $ownerId = 0;
  public $locationType = LOC_NONE;
  public $locationId = 0;

  public $timeStart = 0;
  public $timeFinish = 0;

  /**
   * Passport info per unit
   *
   * @var array $info
   */
  public $info = array();

//  /**
//   * @var array
//   */
//  protected $bonus = array();
  /**
   * @var Bonus $unit_bonus
   */
  public $unit_bonus = null;

  /**
   * Статический иницилизатор. ДОЛЖЕН БЫТЬ ВЫЗВАН ПЕРЕД ИСПОЛЬЗВОАНИЕМ КЛАССА!
   *
   * @param string $group_name
   */
  public static function _init($group_name = '') {
    if(static::$_is_static_init) {
      return;
    }

    if($group_name) {
      static::$_sn_group_name = $group_name;
    }

    if(static::$_sn_group_name) {
      static::$_group_unit_id_list = sn_get_groups(static::$_sn_group_name);
      empty(static::$_group_unit_id_list) ? static::$_group_unit_id_list = array() : false;
    }

  }

  /**
   * Проверяет - принадлежит ли указанный ID юнита данной группе
   *
   * @param int $unit_id
   *
   * @return bool
   */
  public static function is_in_group($unit_id) {
    return isset(static::$_group_unit_id_list[$unit_id]);
  }

  public function __construct() {
    parent::__construct();
    $this->unit_bonus = new Bonus();
  }

  // TODO - __GET, __SET, __IS_NULL, __EMPTY - короче, магметоды
  // А еще нужны методы для вытаскивания ЧИСТОГО и БОНУСНОГО значений
  // Магметоды вытаскивают чистые значения. А если нам нужны бонусные - вытаскивают их спецметоды ??? Хотя бонусные вроде используются чаще...
  // Наоборот - для совместимости с MRC_GET_LEVEL()

//  /**
//   * @param $name
//   */
//  public function __get($name) {
//    // TODO: Implement __get() method.
//  }
//
//  public function __set($name, $value) {
//    // TODO: Implement __set() method.
//  }
//
//  public function __isset($name) {
//    // TODO: Implement __isset() method.
//  }
//
//  public function __unset($name) {
//    // TODO: Implement __unset() method.
//  }

//  /**
//   * Является ли юнит новым - т.е. не имеет своей записи в БД
//   *
//   * @return bool
//   */
//  public function isNew() {
//    return $this->db_id == 0;
//  }

  /**
   * Является ли юнит пустым - т.е. при исполнении _dbSave должен быть удалён
   *
   * @return bool
   */
  public function isEmpty() {
    return $this->count <= 0;
  }

  public function dbRowParse($db_row) {
    parent::dbRowParse($db_row);

    // TODO - делать лукап по локейшену ?

    // Unit specific
    $this->info = get_unit_param($this->unitId);
  }

  public function setUnitId($unitId) {
    // TODO - Reset combat stats??
    $this->unitId = $unitId;

    if($this->unitId) {
      $this->info = get_unit_param($this->unitId);
    } else {
      $this->info = array();
    }
  }

}
