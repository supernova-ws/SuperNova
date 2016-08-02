<?php

/**
 * Handles DB operations on row level
 *
 * Class screens ordinary CRUD tasks providing high-level IDBRow interface.
 * - advanced DB data parsing to class properties with type conversion;
 * - safe storing with string escape function;
 * - smart class self-storing procedures;
 * - smart DB row partial update;
 * - delta updates for numeric DB fields on demand;
 * - managed external access to properties - READ/WRITE, READ-ONLY, READ-PROHIBITED;
 * - virtual properties - with only setter/getter and no corresponding real property;
 * - dual external access to protected data via property name or via getter/setter - including access to virtual properties;
 *
 *
 * No properties should be directly exposed to public
 * All property modifications should pass through __call() method to made partial update feature work;
 * All declared internal properties should start with '_' following by one of the indexes from $_properties static
 *
 * method int getDbId()
 * @property int dbId
 */
abstract class DBRow extends PropertyHiderInObject implements IDbRow {
  // TODO
  /**
   * Should be this object - (!) not class - cached
   * There exists tables that didn't need to cache rows - logs as example
   * And there can be special needs to not cache some class instances when stream-reading many rows i.e. fleets in stat calculation
   *
   * @var bool $_cacheable
   */
  public $_cacheable = true; //
  // TODO
  /**
   * БД для доступа к данным
   *
   * @var db_mysql $db
   */
  protected static $db = null;
  /**
   * Table name in DB
   *
   * @var string
   */
  protected static $_table = '';
  /**
   * Name of ID field in DB
   *
   * @var string
   */
  protected static $_dbIdFieldName = 'id';

  /**
   * DB_ROW to Class translation scheme
   *
   * @var array
   */
  protected $_properties = array(
    'dbId' => array(
      P_DB_FIELD => 'id',
    ),
  );

  /**
   * Object list that should mimic object DB operations - i.e. units on fleet
   *
   * @var IDbRow[]
   */
  protected $triggerDbOperationOn = array(); // Not a static - because it's an object array

  /**
   * @var int
   */
  protected $_dbId = 0;

  /**
   * Flag to skip lock on current Load operation
   *
   * @var bool
   */
  protected $lockSkip = false;


  /**
   * @param db_mysql|null $db
   */
  public static function setDb($db = null) {
    if(empty($db) || !($db instanceof db_mysql)) {
      $db = null;
    }
    static::$db = !empty($db) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
  }

  public static function getDb() {
    return static::$db;
  }

  /**
   * @param string $tableName
   */
  public static function setTable($tableName) {
    static::$_table = $tableName;
  }

  public static function getTable() {
    return static::$_table;
  }

  /**
   * @param string $dbIdFieldName
   */
  public static function setIdFieldName($dbIdFieldName) {
    static::$_dbIdFieldName = $dbIdFieldName;
  }

  public static function getIdFieldName() {
    return static::$_dbIdFieldName;
  }

  // Some magic ********************************************************************************************************

  /**
   * DBRow constructor.
   *
   * @param db_mysql|null $db
   */
  public function __construct($db = null) {
    parent::__construct();
    if (empty($db)) {
      $db = static::getDb();
    }

    static::setDb($db);
  }



  // IDBrow Implementation *********************************************************************************************

  /**
   * Loading object from DB by primary ID
   *
   * @param int  $dbId
   * @param bool $lockSkip
   *
   * @return
   */
  public function dbLoad($dbId, $lockSkip = false) {
    $dbId = idval($dbId);
    if ($dbId <= 0) {
      classSupernova::$debug->error(get_called_class() . '::' . __METHOD__ . ' $dbId not positive = ' . $dbId);

      return;
    }

    $this->_dbId = $dbId;
    $this->lockSkip = $lockSkip;
    // TODO - Use classSupernova::$db_records_locked
    if (false && !$lockSkip && sn_db_transaction_check(false)) {
      $this->dbGetLockById($this->_dbId);
    }

    $db_row = classSupernova::$db->doSelectFetch("SELECT * FROM `{{" . static::$_table . "}}` WHERE `" . static::$_dbIdFieldName . "` = " . $this->_dbId . " LIMIT 1 FOR UPDATE;");
    if (empty($db_row)) {
      return;
    }

    $this->dbRowParse($db_row);
    $this->lockSkip = false;
  }

  /**
   * Lock all fields that belongs to operation
   *
   * @param int $dbId
   *
   * @return
   * param DBLock $dbRow - Object that accumulates locks
   *
   */
  abstract public function dbGetLockById($dbId);

  /**
   * Saving object to DB
   * This is meta-method:
   * - if object is new - then it inserted to DB;
   * - if object is empty - it deleted from DB;
   * - otherwise object is updated in DB;
   */
  // TODO - perform operations only if properties was changed
  public function dbSave() {
    if ($this->isNew()) {
      // No DB_ID - new unit
      if ($this->isEmpty()) {
        classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - object is empty on ' . get_called_class() . '::dbSave');
      }
      $this->dbInsert();
    } else {
      // DB_ID is present
      if ($this->isEmpty()) {
        $this->dbDelete();
      } else {
        if (!sn_db_transaction_check(false)) {
          classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - transaction should always be started on ' . get_called_class() . '::dbUpdate');
        }
        $this->dbUpdate();
      }
    }

    if (!empty($this->triggerDbOperationOn)) {
      foreach ($this->triggerDbOperationOn as $item) {
        $item->dbSave();
      }
    }

    $this->propertiesChanged = array();
    $this->propertiesAdjusted = array();
  }



  // CRUD **************************************************************************************************************

  /**
   * Inserts record to DB
   *
   * @return int|string
   */
  // TODO - protected
  public function dbInsert() {
    if (!$this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - record db_id is not empty on ' . get_called_class() . '::dbInsert');
    }
    $this->_dbId = $this->db_field_set_create($this->dbMakeFieldSet());

    if (empty($this->_dbId)) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - error saving record ' . get_called_class() . '::dbInsert');
    }

    return $this->_dbId;
  }

  /**
   * Updates record in DB
   */
  // TODO - protected
  public function dbUpdate() {
    // TODO - Update
    if ($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbUpdate');
    }
    $this->db_field_update($this->dbMakeFieldSet(true));
  }

  /**
   * Deletes record from DB
   */
  // TODO - protected
  public function dbDelete() {
    if ($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbDelete');
    }
    classSupernova::$gc->db->doDeleteRowWhere(static::$_table, array(static::$_dbIdFieldName => $this->_dbId));
    $this->_dbId = 0;
    // Обо всём остальном должен позаботиться контейнер
  }

  /**
   * Является ли запись новой - т.е. не имеет своей записи в БД
   *
   * @return bool
   */
  public function isNew() {
    return $this->_dbId == 0;
  }

  /**
   * Является ли запись пустой - т.е. при исполнении _dbSave должен быть удалён
   *
   * @return bool
   */
  abstract public function isEmpty();

  // Other Methods *****************************************************************************************************

//  /**
//   * Resets object to zero state
//   * @see DBRow::dbLoad()
//   *
//   * @return void
//   */
//  protected function _reset() {
//    $this->dbRowParse(array());
//  }

  /**
   * Парсит запись из БД в поля объекта
   *
   * @param array $db_row
   */
  public function dbRowParse(array $db_row) {
    foreach ($this->_properties as $property_name => &$property_data) {
      // Advanced values extraction procedure. Should be used when at least one of following rules is matched:
      // - one field should translate to several properties;
      // - one property should be filled according to several fields;
      // - property filling requires some lookup in object values;
      if (!empty($property_data[P_METHOD_EXTRACT]) && is_callable(array($this, $property_data[P_METHOD_EXTRACT]))) {
        call_user_func_array(array($this, $property_data[P_METHOD_EXTRACT]), array(&$db_row));
        continue;
      }

      // If property is read-only - doing nothing
      if (!empty($property_data[P_READ_ONLY])) {
        continue;
      }

      // Getting field value as base only if $_properties has 1-to-1 relation to object property
      $value = !empty($property_data[P_DB_FIELD]) && isset($db_row[$property_data[P_DB_FIELD]]) ? $db_row[$property_data[P_DB_FIELD]] : null;

      // Making format conversion from string ($db_row default type) to property type
      !empty($property_data[P_FUNC_INPUT]) && is_callable($property_data[P_FUNC_INPUT]) ? $value = call_user_func($property_data[P_FUNC_INPUT], $value) : false;

      // If there is setter for this field - using it. Setters is always a methods of $THIS
      if (!empty($property_data[P_METHOD_SET]) && is_callable(array($this, $property_data[P_METHOD_SET]))) {
        call_user_func(array($this, $property_data[P_METHOD_SET]), $value);
      } else {
        $this->{$property_name} = $value;
      }
    }
  }

  /**
   * Делает из свойств класса массив db_field_name => db_field_value
   *
   * @return array
   */
  protected function dbMakeFieldSet($isUpdate = false) {
    $array = array();

    foreach (static::$_properties as $property_name => &$property_data) {
      // TODO - on isUpdate add only changed/adjusted properties

      if (!empty($property_data[P_METHOD_INJECT]) && is_callable(array($this, $property_data[P_METHOD_INJECT]))) {
        call_user_func_array(array($this, $property_data[P_METHOD_INJECT]), array(&$array));
        continue;
      }

      // Skipping properties which have no corresponding field in DB
      if (empty($property_data[P_DB_FIELD])) {
        continue;
      }

      // Checking - is property was adjusted or changed
      if ($isUpdate && array_key_exists($property_name, $this->propertiesAdjusted)) {
        // For adjusted property - take value from propertiesAdjusted array
        // TODO - differ how treated conversion to string for changed and adjusted properties
        $value = $this->propertiesAdjusted[$property_name];
      } else {
        // Getting property value. Optionally getter is invoked by __get()
        $value = $this->{$property_name};
      }

      // If need some conversion to DB format - doing it
      !empty($property_data[P_FUNC_OUTPUT]) && is_callable($property_data[P_FUNC_OUTPUT])
        ? $value = call_user_func($property_data[P_FUNC_OUTPUT], $value) : false;
      !empty($property_data[P_METHOD_OUTPUT]) && is_callable(array($this, $property_data[P_METHOD_OUTPUT]))
        ? $value = call_user_func(array($this, $property_data[P_METHOD_OUTPUT]), $value) : false;

      $array[$property_data[P_DB_FIELD]] = $value;
    }

    return $array;
  }

  /**
   * Check if DB field changed on property change and if it changed - returns name of property which triggered change
   *
   * @param string $fieldName
   *
   * @return string|false
   */
  protected function isFieldChanged($fieldName) {
    $isFieldChanged = false;
    foreach ($this->propertiesChanged as $propertyName => $cork) {
      $propertyScheme = static::$_properties[$propertyName];
      if (!empty($propertyScheme[P_DB_FIELDS_LINKED])) {
        foreach ($propertyScheme[P_DB_FIELDS_LINKED] as $linkedFieldName) {
          if ($linkedFieldName == $fieldName) {
            $isFieldChanged = $propertyName;
            break 2;
          }
        }
      }
      if (!empty($propertyScheme[P_DB_FIELD]) && $propertyScheme[P_DB_FIELD] == $fieldName) {
        $isFieldChanged = $propertyName;
        break;
      }
    }

    return $isFieldChanged;
  }

  /**
   * @param array $field_set
   *
   * @return int|string
   */
  protected function db_field_set_create(array $field_set) {
    !sn_db_field_set_is_safe($field_set) ? $field_set = sn_db_field_set_make_safe($field_set) : false;
    sn_db_field_set_safe_flag_clear($field_set);

    $values = implode(',', $field_set);
    $fields = implode(',', array_keys($field_set));

    $result = 0;
    if (classSupernova::$db->doInsert("INSERT INTO `{{" . static::$_table . "}}` ({$fields}) VALUES ({$values});")) {
      $result = classSupernova::$db->db_insert_id();
    }

    return $result;
  }

  /**
   * @param array $field_set
   *
   * @return array|bool|mysqli_result|null
   */
  // TODO - UPDATE ONLY CHANGED FIELDS
  protected function db_field_update(array $field_set) {
    !sn_db_field_set_is_safe($field_set) ? $field_set = sn_db_field_set_make_safe($field_set) : false;
    sn_db_field_set_safe_flag_clear($field_set);

    $set = array();
    foreach ($field_set as $fieldName => $value) {
      if (!($changedProperty = $this->isFieldChanged($fieldName))) {
        continue;
      }

      // TODO - separate sets from adjusts
      if (array_key_exists($changedProperty, $this->propertiesAdjusted)) {
        $value = "`{$fieldName}` + ($value)"; // braces for negative values
      }

      $set[] = "`{$fieldName}` = $value";
    }
    $set_string = implode(',', $set);

//pdump($set_string, get_called_class());

    return empty($set_string)
      ? true
      : classSupernova::$db->doUpdate("UPDATE `{{" . static::$_table . "}}` SET {$set_string} WHERE `" . static::$_dbIdFieldName . "` = " . $this->_dbId);
  }

}
