<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;

use Common\AccessLogged;
use Common\GlobalContainer;


/**
 * Class ActiveRecord
 *
 * @property int|string $id - Record ID name would be normalized to 'id'
 *
 * @package DBAL
 */
abstract class ActiveRecord extends AccessLogged {
  const IGNORE_PREFIX = 'Record';

  const FIELDS_TO_PROPERTIES = true;
  const PROPERTIES_TO_FIELDS = false;

  /**
   * Autoincrement index field name in DB
   * Would be normalized to 'id' ($id class property)
   *
   * @var string $_primaryIndexField
   */
  protected static $_primaryIndexField = 'id';
  protected static $_tableName = '';

  /**
   * Field name translations to property names
   *
   * @var string[] $_fieldsToProperties
   */
  protected static $_fieldsToProperties = [];

  // AR's service fields
  protected $_isNew = true;

  /**
   * Get table name
   *
   * @return string
   */
  public static function tableName() {
    if (empty(static::$_tableName)) {
      static::tableFromClass();
    }

    return static::$_tableName;
  }

  /**
   * Get used DB
   *
   * @return \db_mysql
   */
  public static function db() {
    return \classSupernova::$db;
  }

  /**
   * Instate ActiveRecord from array of field values
   *
   * @param string[] $fields List of field values [$fieldName => $fieldValue]
   *
   * @return static|bool
   */
  public static function build(array $properties = []) {
    if (!is_array($properties) || empty($properties)) {
      return false;
    }

    $record = new static();
    $record->fromProperties($properties);

    return $record;
  }

  /**
   * Finds records by params in DB
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]. Pass [] to find all records in DB
   *
   * @return bool|\mysqli_result
   */
  public static function find($where) {
    if (!is_array($where)) {
      $where = [static::$_primaryIndexField => $where];
    }

    $dbq = static::prepareDbQuery();
    if (!empty($where)) {
      $dbq->setWhereArray($where);
    }

    return $dbq->doSelect();
  }

  /**
   * Gets first ActiveRecord by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return static|bool
   */
  public static function findOne($where) {
    $record = static::build(static::translateNames(static::findOneArray($where)));
    if (!empty($record)) {
      $record->_isNew = false;
    }

    return $record;
  }

  /**
   * Gets all ActiveRecords by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array|static[] - [(int) => static]
   */
  public static function findAll($where) {
    return static::fromArrayList(static::findAllArray($where));
  }

  /**
   * Gets all ActiveRecords by $where - array indexed by record IDs
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array|static[] - [$record_db_id => static]
   */
  public static function findAllIndexed($where) {
    return static::fromArrayList(static::findAllIndexedArray($where));
  }

  /**
   * Gets first record by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[] - [$field_name => $field_value]
   */
  public static function findOneArray($where) {
    $result = empty($dbq = static::find($where)) ? [] : static::db()->db_fetch($dbq);

    return empty($result) ? [] : $result;
  }

  /**
   * Gets all records by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[][] - [(int) => [$field_name => $field_value]]
   */
  public static function findAllArray($where) {
    return empty($dbq = static::find($where)) ? [] : $dbq->fetch_all(MYSQL_ASSOC);
  }

  /**
   * Gets all records by $where - array indexes is a record IDs
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[][] - [$record_db_id => [$field_name => $field_value]]
   */
  public static function findAllIndexedArray($where) {
    $result = [];
    if (!empty($dbq = static::find($where))) {
      while ($row = static::db()->db_fetch($dbq)) {
        $result[$row[static::$_primaryIndexField]] = $row;
      }
    }

    return $result;
  }

  /**
   * ActiveRecord constructor.
   *
   * @param GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);
  }

  /**
   * Normalize array
   *
   * Basically - uppercase all field names to make it use in PTL
   * Can be override by descendants to make more convinient, clear and robust indexes
   *
   * @return array
   */
  public function ptlArray() {
    $result = [];
    foreach ($this->values as $key => $value) {
      $result[strtoupper(\HelperString::camelToUnderscore($key))] = $value;
    }

    return $result;
  }

  /**
   * Reload current record from ID
   *
   * @return bool
   */
  public function reload() {
    $recordId = $this->id;
    if (empty($recordId)) {
      return false;
    }

    $this->_isNew = true;
    $this->flush();

    $this->_isNew = false;
    $fields = static::findOneArray($this->id);
    if (empty($fields)) {
      return false;
    }

    $this->fromFields($fields);

    return true;
  }

  /**
   * @return array|bool|\mysqli_result|null
   */
  public function insert() {
    if ($this->isEmpty()) {
      return false;
    }
    if (!$this->_isNew) {
      return false;
    }


    if (!($result = static::prepareDbQuery()
      ->setValues(static::translateNames($this->values, self::PROPERTIES_TO_FIELDS))
      ->doInsert())
    ) {
      return false;
    }

    $this->id = \classSupernova::$db->db_insert_id();

    return $this->reload();
  }

  /**
   * @return array|bool|\mysqli_result|null
   */
  public function update() {
    if (empty($this->_changes) && empty($this->_deltas)) {
      return true;
    }

    if (!($result = static::prepareDbQuery()
      ->setValues(empty($this->_changes) ? [] : static::translateNames($this->_changes, self::PROPERTIES_TO_FIELDS))
      ->setAdjust(empty($this->_deltas) ? [] : static::translateNames($this->_deltas, self::PROPERTIES_TO_FIELDS))
      ->setWhereArray([static::$_primaryIndexField => $this->id])
      ->doUpdate())
    ) {
      return false;
    }

    return $this->reload();
  }



  /**
   * Converts property-indexed value array to field-indexed via translation table
   *
   * @param array $propertyNamed
   * @param bool  $fieldToProperties - translation direction: true - field to props. false - prop to fields
   *
   * @return array
   */
  protected static function translateNames(array $propertyNamed, $fieldToProperties = self::FIELDS_TO_PROPERTIES) {
    $translations = $fieldToProperties == self::FIELDS_TO_PROPERTIES ? static::$_fieldsToProperties : array_flip(static::$_fieldsToProperties);

    $result = [];
    foreach ($propertyNamed as $name => $value) {
      if (!empty($translations[$name])) {
        $name = $translations[$name];
      }
      $result[$name] = $value;
    }

    return $result;
  }

  /**
   * Calculate table name by class name and fills internal property
   *
   * Namespaces does not count - only class name taken into account
   * Class name converted from CamelCase to underscore_name
   * Prefix "Table" is ignored - can be override
   *
   * Examples:
   * Class \Namespace\ClassName will map to table `class_name`
   * Class \NameSpace\TableLongName will map to table `long_name`
   *
   * Can be override to provide different name
   *
   */
  protected static function tableFromClass() {
    $temp = explode('\\', get_called_class());
    $className = end($temp);
    if (strpos($className, static::IGNORE_PREFIX) === 0) {
      $className = substr($className, strlen(static::IGNORE_PREFIX));
    }
    static::$_tableName = \HelperString::camelToUnderscore($className);
  }

  /**
   * @return DbQuery
   */
  protected static function prepareDbQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

  /**
   * @param array $records
   *
   * @return array|static[]
   */
  protected static function fromArrayList($records) {
    if (is_array($records) && !empty($records)) {
      foreach ($records as &$record) {
        if (!is_array($record) || empty($record)) {
          continue;
        }

        $record = static::build($record);
        $record->_isNew = false;
      }
    } else {
      $records = [];
    }

    return $records;
  }


  /**
   * @param string[] $fields List of field values [$fieldName => $fieldValue]
   */
  protected function fromFields(array $fields) {
    $this->fromProperties(static::translateNames($fields, self::FIELDS_TO_PROPERTIES));
  }

  /**
   * @param array $properties
   */
  public function fromProperties(array $properties) {
    foreach ($properties as $name => $value) {
      $this->__set($name, $value);
    }
  }

}
