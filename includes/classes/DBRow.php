<?php

/**
 * Class DBRow
 *
 * method int getDbId()
 * @property int dbId
 */
abstract class DBRow implements IDbRow {
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
  protected static $_scheme = array(
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
   * List of property names that was changed since last DB operation
   *
   * @var string[]
   */
  protected $propertiesChanged = array();
  /**
   * List of property names->$delta that was adjusted since last DB operation - and then need to be processed as Deltas
   *
   * @var string[]
   */
  protected $propertiesAdjusted = array();

  /**
   * @var int
   */
  protected $_dbId = 0;


  // Some magic ********************************************************************************************************

  public function __construct() {
    static::$db = classSupernova::$db;
  }

  /**
   * Getter with support of protected methods
   *
   * @param $name
   *
   * @return mixed
   */
  public function __get($name) {
    // Property value can be get from protected property or getter
    if(method_exists($this, 'get' . ucfirst($name))) {
      // Checking for getter
      return call_user_func(array($this, 'get' . ucfirst($name)));
    } else {
      // Checking for property
      if(property_exists($this, '_' . $name)) {
        return $this->{'_' . $name};
      } elseif(!property_exists($this, $name)) {
        classSupernova::$debug->error('Property [' . $name . '] not exists in class ' . get_called_class() . '::__get');
      }

      return $this->$name;
    }
  }

  /**
   * Setter with support of protected properties/methods
   *
   * @param $name
   * @param $value
   */
  // TODO - сеттер должен параллельно изменять значение db_row - for now...
  public function __set($name, $value) {
    // Property value can be get from protected property or getter
    if(method_exists($this, 'set' . ucfirst($name))) {
      // Checking for getter
      call_user_func(array($this, 'set' . ucfirst($name)), $value);
    } else {
      // Checking for hidden property existence
      if(property_exists($this, '_' . $name)) {
        $this->{'_' . $name} = $value;
      } else {
        // Checking for property existence
        if(!property_exists($this, $name)) {
          classSupernova::$debug->error('Property ' . $name . ' not exists in class ' . get_called_class() . '::__set');
        }
        $this->$name = $value;
      }
    }
    $this->propertiesChanged[$name] = true;
  }



  // IDBrow Implementation *********************************************************************************************

  /**
   * Loading object from DB by primary ID
   *
   * @param int $dbId
   */
  public function dbLoad($dbId) {
    $this->_reset();

    $dbId = idval($dbId);
    if($dbId <= 0) {
      classSupernova::$debug->error(get_called_class() . '::dbLoad $dbId not positive = ' . $dbId);

      return;
    }

    $db_row = doquery("SELECT * FROM `{{" . static::$_table . "}}` WHERE `" . static::$_dbIdFieldName . "` = " . $dbId . " LIMIT 1 FOR UPDATE;", true);
    if(empty($db_row)) {
      return;
    }

    $this->dbRowParse($db_row);
  }

  /**
   * Saving object to DB
   * This is meta-method:
   * - if object is new - then it inserted to DB;
   * - if object is empty - it deleted from DB;
   * - otherwise object is updated in DB;
   */
  // TODO - perform operations only if properties was changed
  public function dbSave() {
    if($this->isNew()) {
      // No DB_ID - new unit
      if($this->isEmpty()) {
        classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - object is empty on ' . get_called_class() . '::dbSave');
      }
      $this->dbInsert();
    } else {
      // DB_ID is present
      if($this->isEmpty()) {
        $this->dbDelete();
      } else {
        if(!sn_db_transaction_check(false)) {
          classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - transaction should always be started on ' . get_called_class() . '::dbUpdate');
        }
        $this->dbUpdate();
      }
    }

    if(!empty($this->triggerDbOperationOn)) {
      foreach($this->triggerDbOperationOn as $item) {
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
  public function dbInsert() {
    if(!$this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - record db_id is not empty on ' . get_called_class() . '::dbInsert');
    }
    $this->dbId = $this->db_field_set_create($this->dbMakeFieldSet());

    if(empty($this->dbId)) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - error saving record ' . get_called_class() . '::dbInsert');
    }

    return $this->dbId;
  }

  /**
   * Updates record in DB
   */
  public function dbUpdate() {
    // TODO - Update
    if($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbUpdate');
    }
    $this->db_field_update($this->dbMakeFieldSet(true));
  }

  /**
   * Deletes record from DB
   */
  public function dbDelete() {
    if($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbDelete');
    }
    doquery("DELETE FROM {{" . static::$_table . "}} WHERE `" . static::$_dbIdFieldName . "` = " . $this->dbId);
    $this->dbId = 0;
    // Обо всём остальном должен позаботиться контейнер
  }

  // Other Methods *****************************************************************************************************

  /**
   * Resets object to zero state
   * @see DBRow::dbLoad()
   *
   * @return void
   */
  protected function _reset() {
    $this->dbRowParse(array());
  }

  /**
   * Парсит запись из БД в поля объекта
   *
   * @param array $db_row
   */
  public function dbRowParse(array $db_row) {
    // Пока - простейший вариант. В более сложном - нужно конвертеры ИЗ db_row и В db_row - с преобразованием типов
    foreach(static::$_scheme as $property_name => &$property_data) {
      // Advanced values extraction procedure. Used when at least one of following rules is matched:
      // - one field should translate to several properties;
      // - one property should be filled according to several fields;
      // - property filling requires some lookup in object values;
      if(!empty($property_data[P_METHOD_EXTRACT]) && is_callable(array($this, $property_data[P_METHOD_EXTRACT]))) {
        call_user_func_array(array($this, $property_data[P_METHOD_EXTRACT]), array(&$db_row));
        continue;
      }

      // If property is read-only - doing nothing
      if(!empty($property_data[P_READ_ONLY])) {
        continue;
      }

      // Getting field value as base only if $_scheme has 1-to-1 relation to object property
      $value = !empty($property_data[P_DB_FIELD]) && isset($db_row[$property_data[P_DB_FIELD]]) ? $db_row[$property_data[P_DB_FIELD]] : null;
      // Making format conversion from string ($db_row default type) to property type
      !empty($property_data[P_FUNC_INPUT]) && is_callable($property_data[P_FUNC_INPUT]) ? $value = call_user_func($property_data[P_FUNC_INPUT], $value) : false;

      // If there is setter for this field - using it. Setters is always a methos of THIS
      if(!empty($property_data[P_METHOD_SET]) && is_callable(array($this, $property_data[P_METHOD_SET]))) {
        call_user_func(array($this, $property_data[P_METHOD_SET]), $value);
      } else {
        $this->{$property_name} = $value;
      }
    }
  }

  /**
   * Делает из свойств класса массив db_field_name => db_field_value
   *
   *
   *
   * @return array
   */
  protected function dbMakeFieldSet($isUpdate = false) {
    $array = array();

    foreach(static::$_scheme as $property_name => &$property_data) {
      // TODO - on isUpdate add only changed/adjusted properties

      if(!empty($property_data[P_METHOD_INJECT]) && is_callable(array($this, $property_data[P_METHOD_INJECT]))) {
        call_user_func_array(array($this, $property_data[P_METHOD_INJECT]), array(&$array));
        continue;
      }

      // Skipping properties which have no corresponding field in DB
      if(empty($property_data[P_DB_FIELD])) {
        continue;
      }

      // Checking - is property was adjusted or changed
      if($isUpdate && array_key_exists($property_name, $this->propertiesAdjusted)) {
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
    if(classSupernova::db_query("INSERT INTO `{{" . static::$_table . "}}` ({$fields}) VALUES ({$values});")) {
      $result = db_insert_id();
    }

    return $result;
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
    foreach($this->propertiesChanged as $propertyName => $cork) {
      $propertyScheme = static::$_scheme[$propertyName];
      if(!empty($propertyScheme[P_DB_FIELDS_LINKED])) {
        foreach($propertyScheme[P_DB_FIELDS_LINKED] as $linkedFieldName) {
          if($linkedFieldName == $fieldName) {
            $isFieldChanged = $propertyName;
            break 2;
          }
        }
      }
      if(!empty($propertyScheme[P_DB_FIELD]) && $propertyScheme[P_DB_FIELD] == $fieldName) {
        $isFieldChanged = $propertyName;
        break;
      }
    }

    return $isFieldChanged;
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
//pdump($field_set);
//pdump($this->propertiesAdjusted);
    foreach($field_set as $fieldName => $value) {
      if(!($changedProperty = $this->isFieldChanged($fieldName))) {
        continue;
      }
//pdump($changedProperty);
      // TODO - separate sets from adjusts
      if(array_key_exists($changedProperty, $this->propertiesAdjusted)) {
        $value = "`{$fieldName}` + ($value)"; // braces for negative values
      }

      $set[] = "`{$fieldName}` = $value";
    }
    $set_string = implode(',', $set);
//pdump($set_string, get_called_class());

    return empty($set_string)
      ? true
      : classSupernova::db_query("UPDATE `{{" . static::$_table . "}}` SET {$set_string} WHERE `" . static::$_dbIdFieldName . "` = " . $this->dbId);
  }

  /**
   * Является ли запись новой - т.е. не имеет своей записи в БД
   *
   * @return bool
   */
  public function isNew() {
    return $this->dbId == 0;
  }

  /**
   * Является ли запись пустой - т.е. при исполнении _dbSave должен быть удалён
   *
   * @return bool
   */
  abstract public function isEmpty();

}
