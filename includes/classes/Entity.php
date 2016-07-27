<?php

/**
 * Class Entity
 *
 * @property int|float $dbId Buddy record DB ID
 */
class Entity implements \Common\IMagicProperties {
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected static $tableName = '_table';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected static $idField = 'id';
  /**
   * Container for property values
   *
   * @var PropertyHider $_container
   */
  protected $_container;
  protected static $_containerName = 'PropertyHiderInArray';

  /**
   * Property list
   *
   * @var array
   */
  protected static $_properties = array();

  /**
   * @var array
   */
  protected static $_propertyToField = array();

  /**
   * @var db_mysql|null $dbStatic
   */
  public static $dbStatic = null;

  /**
   * Fills property-to-field table which used to generate result array
   */
  protected function fillPropertyToField() {
    if (!empty(static::$_propertyToField)) {
      return;
    }

    // Filling property-to-filed relation array
    foreach (static::$_properties as $propertyName => &$propertyData) {
      // Property is mapped 1-to-1 to field
      if (!empty($propertyData[P_DB_FIELD])) {
        $fieldName = $propertyData[P_DB_FIELD];
        /**
         * @param static $that
         */
        // Alas! No bindTo() method in 5.3 closures! So we should use what we have
        $propertyData[P_DB_ROW_EXPORT] = function ($that, &$row) use ($propertyName, $fieldName) {
          $row[$fieldName] = $that->$propertyName;
        };
        $propertyData[P_DB_ROW_IMPORT] = function ($that, &$row) use ($propertyName, $fieldName) {
          // TODO: Here should be some conversions to property type
          $that->$propertyName = $row[$fieldName];
        };
      }

    }
  }

  /**
   * Entity constructor.
   *
   * @param \Pimple\GlobalContainer $gc
   */
  public function __construct($gc) {
    empty(static::$dbStatic) && !empty($gc->db) ? static::$dbStatic = $gc->db : false;

    $this->_container = new static::$_containerName();
    $this->_container->setProperties(static::$_properties);

    $this->fillPropertyToField();
  }


  public function getTableName() {
    return static::$tableName;
  }

  public function getIdFieldName() {
    return static::$idField;
  }

  public function load($recordId) {
    classSupernova::$gc->dbRowOperator->getById($this, $recordId);
  }

  // TODO - move to reader ????????
  public function delete() {
    return classSupernova::$gc->dbRowOperator->deleteById($this);
  }

  /**
   * @return int|string
   */
  // TODO - move to reader ????????
  public function insert() {
    return classSupernova::$gc->dbRowOperator->insert($this);
  }

  public function isContainerEmpty() {
    return $this->_container->isContainerEmpty();
  }

  public function isNew() {
    return $this->getIdFieldName() != 0;
  }

  /**
   * Invoke row transformation operation on object
   *
   * Uses in to save/load data from DB row into/from object
   *
   * @param array  $row
   * @param string $operation
   * @param string $desc
   *
   * @throws Exception
   */
  protected function rowInvokeOperation(&$row, $operation, $desc) {
    foreach (static::$_properties as $propertyName => $propertyData) {
      if (is_callable($propertyData[$operation])) {
        // Some black magic here
        // Closure is a class - so have __invoke() magic method
        // It means we can invoke it by directly call __invoke()
        $propertyData[$operation]->__invoke($this, $row);
        // TODO - however for a sake of uniformity may be we should consider use call_user_func
//      call_user_func($propertyData[P_DB_ROW_EXPORT], $this);
      } else {
        throw new \Exception('There is no valid DB row ' . $desc . ' for ' . get_called_class() . '::' . $propertyName);
      }
    }
  }

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importDbRow($row) {
    $this->rowInvokeOperation($row, P_DB_ROW_IMPORT, 'IMPORTER');
  }

  /**
   * Export data from object properties into DB row for further use
   *
   * @param bool $withDbId - Should dbId too be returned. Useful for INSERT statements
   *
   * @return array
   */
  public function exportDbRow($withDbId = true) {
    $row = array();

    $this->rowInvokeOperation($row, P_DB_ROW_EXPORT, 'EXPORTER');

    if (!$withDbId) {
      unset($row[$this->getIdFieldName()]);
    }

    return $row;
  }


  public function __get($name) {
    return $this->_container->$name;
  }

  public function __set($name, $value) {
    $this->_container->$name = $value;
  }

  public function __isset($name) {
    return isset($this->_container->$name);
  }

  public function __unset($name) {
    unset($this->_container->$name);
  }

}
