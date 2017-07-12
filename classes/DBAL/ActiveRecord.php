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
 * Represent table in DB/one record in DB. Breaking SRP with joy!
 *
 * @property int|string $id - Record ID name would be normalized to 'id'
 *
 * @package DBAL
 */
abstract class ActiveRecord extends AccessLogged {
  const IGNORE_PREFIX = 'Record';
  const ID_PROPERTY_NAME = 'id';

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

  /**
   * Property name translations to field names
   *
   * Cached structure
   *
   * @var string[] $_propertiesToFields
   */
  private static $_propertiesToFields = [];

  // AR's service fields
  protected $_isNew = true;

  /**
   * Get table name
   *
   * @return string
   */
  public static function tableName() {
    if (empty(static::$_tableName)) {
      static::$_tableName = static::calcTableName();
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
   * @param array $fields List of field values [$propertyName => $propertyValue]
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
   * Finds records by property - equivalent of SELECT ... WHERE ... AND ...
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return bool|\mysqli_result
   */
  public static function find($propertyFilter) {
    if (!is_array($propertyFilter)) {
      $propertyFilter = [self::ID_PROPERTY_NAME => $propertyFilter];
    }

    $dbq = static::prepareDbQuery();
    if (!empty($propertyFilter)) {
      $dbq->setWhereArray(static::translateNames($propertyFilter, static::PROPERTIES_TO_FIELDS));
    }

    return $dbq->doSelect();
  }

  /**
   * Gets first ActiveRecord by $where
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return static|bool
   */
  public static function findFirst($propertyFilter) {
    $record = static::build(static::translateNames(static::findRecord($propertyFilter)));
    if (is_object($record)) {
      $record->_isNew = false;
    }

    return $record;
  }

  /**
   * Gets all ActiveRecords by $where
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return array|static[] - [(int) => static]
   */
  public static function findAll($propertyFilter) {
    return static::fromRecordList(static::findRecordsAll($propertyFilter));
  }

  /**
   * Gets all ActiveRecords by $where - array indexed by record IDs
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return array|static[] - [$record_db_id => static]
   */
  public static function findAllIndexed($propertyFilter) {
    return static::fromRecordList(static::findRecordsAllIndexed($propertyFilter));
  }

  /**
   * Gets first record by $where
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return string[] - [$field_name => $field_value]
   */
  public static function findRecord($propertyFilter) {
    $result = empty($dbq = static::find($propertyFilter)) ? [] : static::db()->db_fetch($dbq);

    return empty($result) ? [] : $result;
  }

  /**
   * Gets all records by $where
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$property_name => $property_value]
   *
   * @return string[][] - [(int) => [$field_name => $field_value]]
   */
  public static function findRecordsAll($propertyFilter) {
    return empty($dbq = static::find($propertyFilter)) ? [] : $dbq->fetch_all(MYSQL_ASSOC);
  }

  /**
   * Gets all records by $where - array indexes is a record IDs
   *
   * @param array|mixed $propertyFilter - ID of record to find OR [$property_name => $property_value]
   *
   * @return string[][] - [$record_db_id => [$field_name => $field_value]]
   */
  public static function findRecordsAllIndexed($propertyFilter) {
    $result = [];
    if (!empty($dbq = static::find($propertyFilter))) {
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
   * @param array $properties
   */
  public function fromProperties(array $properties) {
    foreach ($properties as $name => $value) {
      $this->__set($name, $value);
    }

    $this->defaultValues();
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

    $this->acceptChanges();

    $fields = static::findRecord($recordId);
    if (empty($fields)) {
      return false;
    }

    $this->fromFields($fields);
    $this->_isNew = false;

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

    $this->defaultValues();
    $fields = static::translateNames($this->values, self::PROPERTIES_TO_FIELDS);

    if (!($result = static::prepareDbQuery()
      ->setValues($fields)
      ->doInsert())
    ) {
      return false;
    }

    $this->id = \classSupernova::$db->db_insert_id();
    $this->acceptChanges();
    $this->_isNew = false;

//    return $this->reload();
    return true;
  }

  /**
   * @return array|bool|\mysqli_result|null
   */
  public function update() {
    if (empty($this->_changes) && empty($this->_deltas)) {
      return true;
    }

    $this->defaultValues();

    if (!($result = static::prepareDbQuery()
      ->setValues(empty($this->_changes) ? [] : static::translateNames($this->_changes, self::PROPERTIES_TO_FIELDS))
      ->setAdjust(empty($this->_deltas) ? [] : static::translateNames($this->_deltas, self::PROPERTIES_TO_FIELDS))
      ->setWhereArray([static::$_primaryIndexField => $this->id])
      ->doUpdate())
    ) {
      return false;
    }

    $this->acceptChanges();

    return true;
  }

  public function acceptChanges() {
    parent::acceptChanges();
    $this->_isNew = empty($this->id);
  }


  /**
   * Translate field name to property name
   *
   * @param $fieldName
   *
   * @return string
   */
  protected static function getPropertyName($fieldName) {
    return empty(static::$_fieldsToProperties[$fieldName]) ? $fieldName : static::$_fieldsToProperties[$fieldName];
  }

  protected static function getFieldName($propertyName) {
    $fieldName = array_search($propertyName, static::$_fieldsToProperties);

    return $fieldName === false ? $propertyName : $fieldName;
  }

  /**
   * Converts property-indexed value array to field-indexed via translation table
   *
   * @param array $names
   * @param bool  $fieldToProperties - translation direction:
   *    - self::FIELDS_TO_PROPERTIES - field to props.
   *    - self::PROPERTIES_TO_FIELDS - prop to fields
   *
   * @return array
   */
  protected static function translateNames(array $names, $fieldToProperties = self::FIELDS_TO_PROPERTIES) {
    $translations = $fieldToProperties == self::FIELDS_TO_PROPERTIES ? static::$_fieldsToProperties : array_flip(static::$_fieldsToProperties);

    $result = [];
    foreach ($names as $name => $value) {
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
   * @return string - table name in DB
   *
   */
  protected static function calcTableName() {
    $temp = explode('\\', get_called_class());
    $className = end($temp);
    if (strpos($className, static::IGNORE_PREFIX) === 0) {
      $className = substr($className, strlen(static::IGNORE_PREFIX));
    }

    return \HelperString::camelToUnderscore($className);
  }

  /**
   * @return DbQuery
   */
  protected static function prepareDbQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

  /**
   * @param array $records - array of DB records [(int) => [$name => $value]]
   * @param bool  $doNameTranslation - should names be translated (true - for field records, false - for property records)
   *
   * @return array|static[]
   */
  protected static function fromRecordList($records, $doNameTranslation = true) {
    $result = [];
    if (is_array($records) && !empty($records)) {
      foreach ($records as $key => $recordArray) {
        if (!is_array($recordArray) || empty($recordArray)) {
          continue;
        }

        $doNameTranslation ? $recordArray = static::translateNames($recordArray) : false;

        $theRecord = static::build($recordArray);
        if (is_object($theRecord)) {
          $theRecord->_isNew = false;
          $result[$key] = $theRecord;
        }
      }
    }

    return $result;
  }


  /**
   * @param array $fields List of field values [$fieldName => $fieldValue]
   */
  protected function fromFields(array $fields) {
    $this->fromProperties(static::translateNames($fields, self::FIELDS_TO_PROPERTIES));
  }

  protected function defaultValues() {
    $tableFieldList = $this->db()->schema()->getTableSchema(static::tableName())->fields;
    foreach ($tableFieldList as $fieldName => $fieldData) {
      if (array_key_exists($propertyName = static::getPropertyName($fieldName), $this->values)) {
        continue;
      }

      // Skipping auto increment fields
      if (strpos($fieldData['Extra'], 'auto_increment') !== false) {
        continue;
      }

      if ($fieldData['Type'] == 'timestamp' && $fieldData['Default'] == 'CURRENT_TIMESTAMP') {
        $this->__set($propertyName, SN_TIME_SQL);
        continue;
      }

      $this->__set($propertyName, $fieldData['Default']);
    }
  }

}
