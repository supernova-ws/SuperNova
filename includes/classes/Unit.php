<?php

/**
 * Class Unit
 *
 * @property int $unitId
 * @property int $count
 * @method int getCount() - TODO - DEPRECATED - не существует, но используется в UBE
 * @method int getUnitId()
 * @method int getType()
 * @method int getTimeStart()
 * @method int getTimeFinish()
 * @see Unit::__get()
 *
 */
class Unit extends DBRowLocation {

  /**
   * Type of this location
   *
   * @var int $locationType
   */
  protected static $locationType = LOC_UNIT_NUMERIC;


  // DBRow inheritance *************************************************************************************************

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
  protected static $_properties = array(
    'dbId'          => array(
      P_DB_FIELD => 'unit_id',
    ),

    // Location data is taken from container
    'playerOwnerId' => array(
      P_DB_FIELD      => 'unit_player_id',
      P_METHOD_INJECT => 'injectLocation',
      P_READ_ONLY     => true,
    ),
//    'locationType' => array(
//      P_DB_FIELD  => 'unit_location_type',
//      P_READ_ONLY => true,
//    ),
//    'locationDbId' => array(
//      P_DB_FIELD  => 'unit_location_id',
//      P_READ_ONLY => true,
//    ),

    'type'   => array(
      P_DB_FIELD   => 'unit_type',
      P_FUNC_INPUT => 'intval',
    ),
    'unitId' => array(
      P_DB_FIELD   => 'unit_snid',
      P_METHOD_SET => 'setUnitId',
      P_FUNC_INPUT => 'intval',
    ),
    'count'  => array(
      P_DB_FIELD   => 'unit_level',
      P_FUNC_INPUT => 'floatval',
    ),

    'timeStart'  => array(
      P_DB_FIELD    => 'unit_time_start',
      P_FUNC_INPUT  => 'sqlStringToUnixTimeStamp',
      P_FUNC_OUTPUT => 'unixTimeStampToSqlString',
    ),
    'timeFinish' => array(
      P_DB_FIELD    => 'unit_time_finish',
      P_FUNC_INPUT  => 'sqlStringToUnixTimeStamp',
      P_FUNC_OUTPUT => 'unixTimeStampToSqlString',
    ),

    'capacity' => array(
      P_READ_ONLY => true,
    ),
  );

  // New statics *******************************************************************************************************

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


  // Properties from fields ********************************************************************************************
  protected $_unitId = 0;
  // TODO - Type is extracted on-the-fly from $info
  protected $_type = 0;

  protected $_count = 0;

  // Internal properties ***********************************************************************************************

  protected $_timeStart = 0;
  protected $_timeFinish = 0;

  /**
   * Passport info per unit
   *
   * @var array $info
   */
  public $info = array();

  /**
   * @var Bonus $unit_bonus
   */
  public $unit_bonus = null;


  // DBRow inheritance *************************************************************************************************

  public function __construct() {
    parent::__construct();
    $this->unit_bonus = new Bonus();
  }

  // TODO - пустой так же если нет locatedAt
  // или DBID
  // или locationType == LOC_NONE
  // Но тогда оверврайт в UBEUnit наверное
  public function isEmpty() {
    return $this->_count <= 0 || $this->getLocatedAtDbId() == 0 || $this->getLocatedAtType() == LOC_NONE;
  }


  protected function getCapacity() {
    return !empty($this->info['capacity']) ? intval($this->info['capacity']) : 0;
  }

  // New statics *******************************************************************************************************

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


  // Properties from fields ********************************************************************************************

  public function setUnitId($unitId) {
    // TODO - Reset combat stats??
    $this->_unitId = $unitId;

    if($this->_unitId) {
      $this->info = get_unit_param($this->_unitId);
      $this->_type = $this->info[P_UNIT_TYPE];
    } else {
      $this->info = array();
      $this->_type = 0;
    }
  }

  protected function setCount($value) {
    // TODO - Reset combat stats??
    if($value < 0) {
      classSupernova::$debug->error('Can not set Unit::$count to negative value');
    }
    $this->_count = $value;
//    $this->propertiesChanged['count'] = true;

    return $this->_count;
  }

  /**
   * @param int $value
   *
   * @return int
   */
  // TODO - some calcs ??????
  public function adjustCount($value) {
    $this->count += $value;
    $this->propertiesAdjusted['count'] += $value;

    return $this->_count;
  }

  /**
   * Extracts resources value from db_row
   *
   * @param array $db_row
   *
   * @internal param Unit $that
   * @version 41a6.77
   */
  protected function injectLocation(array &$db_row) {
    $db_row['unit_player_id'] = $this->getPlayerOwnerId();
    $db_row['unit_location_type'] = $this->getLocatedAtType();
    $db_row['unit_location_id'] = $this->getLocatedAtDbId();
  }

  // TODO - __GET, __SET, __IS_NULL, __EMPTY - короче, магметоды
  // А еще нужны методы для вытаскивания ЧИСТОГО и БОНУСНОГО значений
  // Магметоды вытаскивают чистые значения. А если нам нужны бонусные - вытаскивают их спецметоды ??? Хотя бонусные вроде используются чаще...
  // Наоборот - для совместимости с MRC_GET_LEVEL()
  // Да не - чистые. Там уже всё совсем будет иначе и совместимост с MRC_GET_LEVEL() не требуется


  // TODO - DEBUG
  public function zeroDbId() {
    $this->_dbId = 0;
  }

  /**
   * Lock all fields that belongs to operation
   *
   * @param DBLock $dbId - Object that accumulates locks
   *
   */
  public function dbGetLockById($dbId) {
//    "LEFT JOIN {{users}} as u on u.id = {{unit}}.unit_player_id";
    $dbId->addPlayerLock('unit', 'unit_player_id');
    // TODO: Implement dbGetLock() method.
  }

}
