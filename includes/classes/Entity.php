<?php

/**
 * Class Entity
 *
 * @property int|float $dbId Buddy record DB ID
 */
class Entity implements \Common\IMagicProperties, \Common\IEntity {
  const ENTITY_DB_ID_INCLUDE = true;
  const ENTITY_DB_ID_EXCLUDE = true;

  /**
   * Link to DB which used by this Entity
   *
   * @var db_mysql $dbStatic
   * deprecated - replace with container ID like 'db' or 'dbAuth'
   */
  protected static $dbStatic;
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
   * @var \Common\IPropertyContainer $_container
   */
  protected $_container;
  protected static $_containerName = 'V2PropertyContainer';

  /**
   * Property list and description
   *
   * propertyName => array(
   *    P_DB_FIELD => 'dbFieldName', - directly converts property to field and vice versa
   * )
   *
   * @var array[]
   */
  protected static $_properties = array();

//  /**
//   * @var array
//   */
//  protected static $_propertyToField = array();
//  protected static $_propertyImportExportIsBind = false;

  /**
   * Service to work with rows
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected static $rowOperator;

//  protected function bindFieldExport(&$propertyData, $propertyName) {
//    if (!empty($propertyData[P_DB_ROW_EXPORT])) {
//      return;
//    }
//
//    // Last resort - binding export function to DB field name
//    if (!empty($propertyData[P_DB_FIELD])) {
//      // Property is mapped 1-to-1 to field
//      /**
//       * @param static $that
//       */
//      // Alas! No bindTo() method in 5.3 closures! So we should use what we have
//      // Also no auto bound of $this until 5.4
//      $propertyData[P_DB_ROW_EXPORT] = function ($that, &$row) use ($propertyName, $propertyData) {
//        $row[$propertyData[P_DB_FIELD]] = $that->$propertyName;
//      };
//    }
//  }

//  protected function bindFieldImport(&$propertyData, $propertyName) {
//    if (!empty($propertyData[P_DB_ROW_IMPORT])) {
//      return;
//    }
//
//    // Last resort - binding import function to DB field name
//    if (!empty($propertyData[P_DB_ROW_METHOD_IMPORT_V2])) {
////      $funcName = $propertyData[P_DB_ROW_METHOD_IMPORT_V2];
//      $propertyData[P_DB_ROW_IMPORT] = array($this, $propertyData[P_DB_ROW_METHOD_IMPORT_V2]);
////      function ($that, &$row) use ($propertyName, $propertyData, $funcName) {
////        $that->$propertyName = \Common\Types::castAs(
////          !empty($propertyData[P_DB_TYPE]) ? $propertyData[P_DB_TYPE] : TYPE_EMPTY,
////          $row[$propertyData[P_DB_FIELD]]
////        );
////      };
//    } elseif (!empty($propertyData[P_DB_FIELD])) {
//      // Property is mapped 1-to-1 to field
//      /**
//       * @param static $that
//       */
//      // Alas! No bindTo() method in 5.3 closures! So we should use what we have
//      // Also no auto bound of $this until 5.4
//      $propertyData[P_DB_ROW_IMPORT] = function ($that, &$row) use ($propertyName, $propertyData) {
//        $that->$propertyName = \Common\Types::castAs(
//          !empty($propertyData[P_DB_TYPE]) ? $propertyData[P_DB_TYPE] : TYPE_EMPTY,
//          $row[$propertyData[P_DB_FIELD]]
//        );
//      };
//    }
//  }

//  /**
//   * Fills property-to-field table which used to generate result array
//   */
//  protected function fillPropertyToField() {
//    if (static::$_propertyImportExportIsBind) {
//      return;
//    }
//
//    // Filling property-to-field relation array
//    foreach (static::$_properties as $propertyName => &$propertyData) {
//      $this->bindFieldExport($propertyData, $propertyName);
//      $this->bindFieldImport($propertyData, $propertyName);
//    }
//
//    static::$_propertyImportExportIsBind = true;
//  }

  /**
   * Entity constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    empty(static::$dbStatic) && !empty($gc->db) ? static::$dbStatic = $gc->db : false;
    static::$rowOperator = $gc->dbRowOperator;

    $this->_container = new static::$_containerName();
    $this->_container->setProperties(static::$_properties);

//    $this->fillPropertyToField();
  }


//  /**
//   * Invoke row transformation operation on object
//   *
//   * Uses in to save/load data from DB row into/from object
//   *
//   * @param array  $row
//   * @param string $operation
//   * @param string $desc
//   *
//   * @throws Exception
//   *
//   * @deprecated
//   */
//  protected function rowInvokeOperation(&$row, $operation, $desc) {
//    foreach (static::$_properties as $propertyName => $propertyData) {
//      if (is_callable($propertyData[$operation])) {
//        // Some black magic here
//        // Closure is a class - so have __invoke() magic method
//        // It means we can invoke it by directly call __invoke()
//        // $propertyData[$operation]->__invoke($this, $row);
//        // However for a sake of uniformity may be we should consider use call_user_func
//        call_user_func($propertyData[$operation], $this, $row);
//      } else {
//        throw new \Exception('There is no valid DB [' . $operation . '] row operation for ' . get_called_class() . '::' . $propertyName);
//      }
//    }
//  }

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importRow($row) {
    $this->_container->importRow($row);
  }

  /**
   * Export data from object properties into DB row for further use
   *
   * @param bool $withDbId - Should dbId too be returned. Useful for INSERT statements
   *
   * @return array
   */
  protected function exportDbRow($withDbId = self::ENTITY_DB_ID_INCLUDE) {
    $row = $this->_container->exportRow();

    if ($withDbId == self::ENTITY_DB_ID_EXCLUDE) {
      unset($row[$this->getIdFieldName()]);
    }

    return $row;
  }

  /**
   * @return array
   */
  public function exportRowWithoutId() {
    return $this->exportDbRow(self::ENTITY_DB_ID_EXCLUDE);
  }

  /**
   * @return array
   */
  public function exportRowWithId() {
    return $this->exportDbRow(self::ENTITY_DB_ID_INCLUDE);
  }

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName() {
    return static::$tableName;
  }

  /**
   * @return string
   */
  public function getIdFieldName() {
    return static::$idField;
  }

  /**
   * @return bool
   */
  public function isContainerEmpty() {
    return $this->_container->isEmpty();
  }

  /**
   * @return bool
   */
  public function isNew() {
    return empty($this->dbId);
  }

  /**
   * @return db_mysql
   */
  public function getDbStatic() {
    return static::$dbStatic;
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
