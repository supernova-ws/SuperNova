<?php

/**
 * Class DBRow
 */
abstract class DBRow {
  public $db_id = 0;
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
  protected static $_scheme = array();

  public function __construct() {
  }

  /**
   * Является ли запись новой - т.е. не имеет своей записи в БД
   *
   * @return bool
   */
  public function isNew() {
    return $this->db_id == 0;
  }

  /**
   * Является ли запись пустой - т.е. при исполнении _dbSave должен быть удалён
   *
   * @return bool
   */
  abstract public function isEmpty();

  /**
   * @return array
   */
  public function dbMakeFieldSet() {
    $array = array();

    // Пока - простейший вариант. В более сложном - нужно конвертеры ИЗ db_row и В db_row - с преобразованием типов
    foreach(static::$_scheme as $property_name => &$property_data) {
      if(empty($property_data[P_DB_FIELD])) {
        continue;
      }

      $value = $this->{$property_name};
      !empty($property_data[P_FUNC_OUTPUT]) && is_callable($property_data[P_FUNC_OUTPUT]) ? $value = call_user_func($property_data[P_FUNC_OUTPUT], $value) : false;

      $array[$property_data[P_DB_FIELD]] = $value;
    }

    return $array;
  }

  public function dbRowParse($db_row) {
    // Пока - простейший вариант. В более сложном - нужно конвертеры ИЗ db_row и В db_row - с преобразованием типов
    foreach(static::$_scheme as $property_name => &$property_data) {
      $value = null;
      if(!empty($property_data[P_DB_FIELD])) {
        $value = $db_row[$property_data[P_DB_FIELD]];
      }
      !empty($property_data[P_FUNC_INPUT]) && is_callable($property_data[P_FUNC_INPUT]) ? $value = call_user_func($property_data[P_FUNC_INPUT], $value) : false;

      if(!empty($property_data[P_FUNC_SET]) && is_callable($property_data[P_FUNC_SET])) {
        call_user_func($property_data[P_FUNC_SET], $value);
      } else {
        $this->{$property_name} = $value;
      }
    }
  }

  public function dbSave() {
    if($this->isNew()) {
      // No DB_ID - new unit
      if($this->isEmpty()) {
        classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit is empty on dbSave');
      }
      $this->dbInsertSet();
    } else {
      // DB_ID is present
      if($this->isEmpty()) {
        $this->dbDelete();
      } else {
        $this->dbUpdate();
      }
    }
  }

  public function dbInsertSet() {
    $this->db_id = $this->db_field_set_create($this->dbMakeFieldSet());
  }

  public function dbUpdate() {
    // TODO - Update
    if($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbUpdate');
    }
    $this->db_field_update($this->dbMakeFieldSet());
  }

  public function dbDelete() {
    if($this->isNew()) {
      classSupernova::$debug->error(__FILE__ . ':' . __LINE__ . ' - unit db_id is empty on dbDelete');
    }
    doquery("DELETE FROM {{" . static::$_table . "}} WHERE `" . static::$_dbIdFieldName . "` = " . $this->db_id);
    $this->db_id = 0;
    // Обо всём остальном должен позаботиться контейнер
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
   * @param array $field_set
   *
   * @return array|bool|mysqli_result|null
   */
  protected function db_field_update(array $field_set) {
    !sn_db_field_set_is_safe($field_set) ? $field_set = sn_db_field_set_make_safe($field_set) : false;
    sn_db_field_set_safe_flag_clear($field_set);

    $set = array();
    foreach($field_set as $key => $value) {
      $set[] = "{$key} = $value";
    }
    $set_string = implode(',', $set);

    return classSupernova::db_query("UPDATE `{{" . static::$_table . "}}` SET {$set_string} WHERE `" . static::$_dbIdFieldName . "` = " . $this->db_id);
  }

}
