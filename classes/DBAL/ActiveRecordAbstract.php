<?php
/**
 * Created by Gorlum 12.07.2017 10:27
 */

namespace DBAL;

use Common\AccessLogged;
use Core\GlobalContainer;
use Common\Exceptions\DbalFieldInvalidException;

/**
 * Class ActiveRecordAbstract
 * @package DBAL
 *
 * Adds some DB functionality
 */
abstract class ActiveRecordAbstract extends AccessLogged {
  const FIELDS_TO_PROPERTIES = true;
  const PROPERTIES_TO_FIELDS = false;

  const IGNORE_PREFIX = 'Record';

  /**
   * @var \DBAL\db_mysql $db
   */
  protected static $db;
  /**
   * Table name for current Active Record
   *
   * Can be predefined in class or calculated in run-time
   *
   * ALWAYS SHOULD BE OVERRIDEN IN CHILD CLASSES!
   *
   * @var string $_tableName
   */
  protected static $_tableName = '';
  /**
   * Field name translations to property names
   *
   * @var string[] $_fieldsToProperties
   */
  protected static $_fieldsToProperties = [];

  /**
   * @var bool $_forUpdate
   */
  protected static $_forUpdate = DbQuery::DB_SHARED;

  // AR's service fields
  /**
   * Is this field - new field?
   *
   * @var bool $_isNew
   */
  protected $_isNew = true;

  protected $_isDeleted = false;


  /**
   * Get table name
   *
   * @return string
   */
  public static function tableName() {
    empty(static::$_tableName) ? static::$_tableName = static::calcTableName() : false;

    return static::$_tableName;
  }

  /**
   * @param \DBAL\db_mysql $db
   */
  public static function setDb(\DBAL\db_mysql $db) {
    static::$db = $db;
  }

  /**
   * Get DB
   *
   * @return \DBAL\db_mysql
   */
  public static function db() {
    empty(static::$db) ? static::$db = \SN::services()->db : false;

    return static::$db;
  }

  /**
   * Instate ActiveRecord from array of field values - even if it is empty
   *
   * @param array $properties List of field values [$propertyName => $propertyValue]
   *
   * @return static
   */
  public static function buildEvenEmpty(array $properties = []) {
    $record = new static();
    if (!empty($properties)) {
      $record->clear();
      $record->fromProperties($properties);
    }

    return $record;
  }

  /**
   * Instate ActiveRecord from array of field values
   *
   * @param array $properties List of field values [$propertyName => $propertyValue]
   *
   * @return static|bool
   */
  public static function build(array $properties = []) {
    if (!is_array($properties) || empty($properties)) {
      return false;
    }

    return static::buildEvenEmpty($properties);
  }

  /**
   * Set flag "for update"
   *
   * @param bool $forUpdate - DbQuery::DB_FOR_UPDATE | DbQuery::DB_SHARED
   */
  public static function setForUpdate($forUpdate = DbQuery::DB_FOR_UPDATE) {
    static::$_forUpdate = $forUpdate;
  }

  /**
   * Finds records by property - equivalent of SELECT ... WHERE ... AND ...
   *
   * @param array $propertyFilter - [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return bool|\mysqli_result
   */
  public static function find($propertyFilter) {
    $dbq = static::dbPrepareQuery();
    if (!empty($propertyFilter)) {
      $dbq->setWhereArray(static::translateNames($propertyFilter, static::PROPERTIES_TO_FIELDS));
    }

    if (static::$_forUpdate == DbQuery::DB_FOR_UPDATE) {
      $dbq->setForUpdate();
      // Restoring default forUpdate state
      static::$_forUpdate = DbQuery::DB_SHARED;
    }

    return $dbq->doSelect();
  }

  /**
   * Gets first record by $where
   *
   * @param array $propertyFilter - [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return string[] - [$field_name => $field_value]
   */
  public static function findRecordFirst($propertyFilter) {
    $result = empty($mysqliResult = static::find($propertyFilter)) ? [] : $mysqliResult->fetch_assoc();

    // Secondary check - for fetch_assoc() result
    return empty($result) ? [] : $result;
  }

  /**
   * Gets all records by $where
   *
   * @param array $propertyFilter - [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return array[] - [(int) => [$field_name => $field_value]]
   */
  public static function findRecordsAll($propertyFilter) {
    return empty($mysqliResult = static::find($propertyFilter)) ? [] : $mysqliResult->fetch_all(MYSQLI_ASSOC);
  }

  /**
   * Gets first ActiveRecord by $where
   *
   * @param array $propertyFilter - [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return static|bool
   */
  public static function findFirst($propertyFilter) {
    $record = false;
    $fields = static::findRecordFirst($propertyFilter);
    if (!empty($fields)) {
      $record = static::build(static::translateNames($fields, self::FIELDS_TO_PROPERTIES));
      if (is_object($record)) {
        $record->_isNew = false;
      }
    }

    return $record;
  }

  /**
   * Gets all ActiveRecords by $where
   *
   * @param array $propertyFilter - [$propertyName => $propertyValue]. Pass [] to find all records in table
   *
   * @return array|static[] - [(int) => static]
   */
  public static function findAll($propertyFilter) {
    return static::fromRecordList(static::findRecordsAll($propertyFilter));
  }


  /**
   * ActiveRecord constructor.
   *
   * @param \Core\GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);
    $this->defaultValues();
  }

  /**
   * @return bool
   */
  // TODO - do a check that all fields present in stored data. I.e. no empty fields with no defaults
  public function insert() {
    if ($this->isEmpty()) {
      return false;
    }
    if (!$this->_isNew) {
      return false;
    }

    $this->defaultValues();

    if (!$this->dbInsert()) {
      return false;
    }

    $this->accept();
    $this->_isNew = false;

    return true;
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
   * Get default value for field
   *
   * @param string $propertyName
   *
   * @return mixed
   */
  public function getDefault($propertyName) {
    $fieldName = self::getFieldName($propertyName);

    return
      isset(static::dbGetFieldsDescription()[$fieldName]->Default)
        ? static::dbGetFieldsDescription()[$fieldName]->Default
        : null;
  }

  /**
   * Returns default value if original value not set
   *
   * @param string $propertyName
   *
   * @return mixed
   */
  public function __get($propertyName) {
    return $this->__isset($propertyName) ? parent::__get($propertyName) : $this->getDefault($propertyName);
  }

  public function __set($propertyName, $value) {
    $this->shieldName($propertyName);
    parent::__set($propertyName, $value);
  }


  /**
   * Calculate table name by class name and fills internal property
   *
   * Namespaces does not count - only class name taken into account
   * Class name converted from CamelCase to underscore_name
   * Prefix "Record" is ignored - can be override
   *
   * Examples:
   * Class \Namespace\ClassName will map to table `class_name`
   * Class \NameSpace\RecordLongName will map to table `long_name`
   *
   * Can be overriden to provide different name
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
   * Get table fields description
   *
   * @return DbFieldDescription[]
   */
  protected static function dbGetFieldsDescription() {
    return static::db()->schema()->getTableSchema(static::tableName())->fields;
  }

  /**
   * Prepares DbQuery object for further operations
   *
   * @return DbQuery
   */
  protected static function dbPrepareQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

  /**
   * Is there translation for this field name to property name
   *
   * @param string $fieldName
   *
   * @return bool
   */
  protected static function haveTranslationToProperty($fieldName) {
    return !empty(static::$_fieldsToProperties[$fieldName]);
  }

  /**
   * Check if field exists
   *
   * @param string $fieldName
   *
   * @return bool
   */
  protected static function haveField($fieldName) {
    return !empty(static::dbGetFieldsDescription()[$fieldName]);
  }

  /**
   * Returns field name by property name
   *
   * @param string $propertyName
   *
   * @return string Field name for property or '' if not field
   */
  protected static function getFieldName($propertyName) {
    $fieldName = array_search($propertyName, static::$_fieldsToProperties);
    if (
      // No translation found for property name
      $fieldName === false
      &&
      // AND Property name is not among translatable field names
      !static::haveTranslationToProperty($propertyName)
      &&
      // AND field name exists
      static::haveField($propertyName)
    ) {
      // Returning property name as field name
      $fieldName = $propertyName;
    }

    return $fieldName === false ? '' : $fieldName;
  }

  /**
   * Does property exists?
   *
   * @param string $propertyName
   *
   * @return bool
   */
  protected static function haveProperty($propertyName) {
    return !empty(static::getFieldName($propertyName));
  }

  /**
   * Translate field name to property name
   *
   * @param string $fieldName
   *
   * @return string Property name for field if field exists or '' otherwise
   */
  protected static function getPropertyName($fieldName) {
    return
      // If there translation of field name = returning translation result
      static::haveTranslationToProperty($fieldName)
        ? static::$_fieldsToProperties[$fieldName]
        // No, there is no translation
        // Is field exists in table? If yes - returning field name as property name
        : (static::haveField($fieldName) ? $fieldName : '');
  }

  /**
   * Converts property-indexed value array to field-indexed via translation table
   *
   * @param array $names
   * @param bool  $fieldToProperties - translation direction:
   *                                 - self::FIELDS_TO_PROPERTIES - field to props
   *                                 - self::PROPERTIES_TO_FIELDS - prop to fields
   *
   * @return array
   */
  // TODO - Throw exception on incorrect field
  protected static function translateNames(array $names, $fieldToProperties = self::FIELDS_TO_PROPERTIES) {
    $result = [];
    foreach ($names as $name => $value) {
      $exists = $fieldToProperties == self::FIELDS_TO_PROPERTIES ? static::haveField($name) : static::haveProperty($name);
      if (!$exists) {
        continue;
      }

      $name =
        $fieldToProperties == self::FIELDS_TO_PROPERTIES
          ? static::getPropertyName($name)
          : static::getFieldName($name);
      $result[$name] = $value;
    }

    return $result;
  }

  /**
   * Makes array of object from field/property list array
   *
   * Empty records and non-records (non-subarrays) are ignored
   * Function maintains record indexes
   *
   * @param array[] $records           - array of DB records [(int) => [$name => $value]]
   * @param bool    $fieldToProperties - should names be translated (true - for field records, false - for property records)
   *
   * @return array|static[]
   */
  protected static function fromRecordList($records, $fieldToProperties = self::FIELDS_TO_PROPERTIES) {
    $result = [];
    if (is_array($records) && !empty($records)) {
      foreach ($records as $key => $recordArray) {
        if (!is_array($recordArray) || empty($recordArray)) {
          continue;
        }

        $fieldToProperties === self::FIELDS_TO_PROPERTIES
          ? $recordArray = static::translateNames($recordArray, self::FIELDS_TO_PROPERTIES)
          : false;

        $theRecord = static::build($recordArray);
        if (is_object($theRecord)) {
          $theRecord->_isNew = false;
//          if(!empty($theRecord->id)) {
//            $key = $theRecord->id;
//          }
          $result[$key] = $theRecord;
        }
      }
    }

    return $result;
  }

  protected function defaultValues() {
    foreach (static::dbGetFieldsDescription() as $fieldName => $fieldData) {
      if (array_key_exists($propertyName = static::getPropertyName($fieldName), $this->values)) {
        continue;
      }

      // Skipping auto increment fields
      if (strpos($fieldData->Extra, SN_SQL_EXTRA_AUTO_INCREMENT) !== false) {
        continue;
      }

      if ($fieldData->Type == SN_SQL_TYPE_NAME_TIMESTAMP && $fieldData->Default == SN_SQL_DEFAULT_CURRENT_TIMESTAMP) {
        $this->__set($propertyName, SN_TIME_SQL);
        continue;
      }

      $this->__set($propertyName, $fieldData->Default);
    }
  }

  /**
   * Set AR properties from array of PROPERTIES
   *
   * DOES NOT override existing values
   * DOES set default values for empty fields
   *
   * @param array $properties
   */
  protected function fromProperties(array $properties) {
    foreach ($properties as $name => $value) {
      $this->__set($name, $value);
    }

    $this->defaultValues();
  }

  /**
   * Set AR properties from array of FIELDS
   *
   * DOES NOT override existing values
   * DOES set default values for empty fields
   *
   * @param array $fields List of field values [$fieldName => $fieldValue]
   */
  protected function fromFields(array $fields) {
    $this->fromProperties(static::translateNames($fields, self::FIELDS_TO_PROPERTIES));
  }

  /**
   * @return bool
   */
  protected function dbInsert() {
    return
      static::dbPrepareQuery()
        ->setValues(static::translateNames($this->values, self::PROPERTIES_TO_FIELDS))
        ->doInsert();
  }

  /**
   * Protects object from setting non-existing property
   *
   * @param string $propertyName
   *
   * @throws DbalFieldInvalidException
   */
  protected function shieldName($propertyName) {
    if (!self::haveProperty($propertyName)) {
      throw new DbalFieldInvalidException(sprintf(
        '{{{ Свойство \'%1$s\' не существует в ActiveRecord \'%2$s\' }}}', $propertyName, get_called_class()
      ));
    }
  }

}
