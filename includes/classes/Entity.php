<?php

/**
 * Class Entity
 *
 * @property int|float $dbId Buddy record DB ID
 */
class Entity {
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
   * @var db_mysql|null $dbStatic
   */
  public static $dbStatic = null;

  /**
   * @var array $row
   */
  protected $row = array();

  /**
   * Buddy\Buddy constructor.
   *
   * @param \Pimple\GlobalContainer $c
   */
  public function __construct($c) {
    empty(static::$dbStatic) && !empty($c->db) ? static::$dbStatic = $c->db : false;

    $this->_container = new static::$_containerName();
    $this->_container->setProperties(static::$_properties);
  }

  public function getTableName() {
    return static::$tableName;
  }

  public function getIdFieldName() {
    return static::$idField;
  }

  public function load($buddyId) {
    classSupernova::$gc->dbRowOperator->getById($this, $buddyId);
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

  public function isEmpty() {
    return empty($this->row);
  }

  public function isNew() {
    return empty($this->row[$this->getIdFieldName()]);
  }

  /**
   * @param array $row
   */
  public function setRow($row) {
//    $this->row = $row;
    // TODO - $row can be empty
    if ($this->getIdFieldName() != '') {
      $this->dbId = $row[$this->getIdFieldName()];
      unset($row[$this->getIdFieldName()]);
    }
    foreach($row as $fieldName => $fieldValue) {
      $this->$fieldName = $fieldValue;
    }
  }

  /**
   * Compiles object data into db row
   *
   * @param bool $withDbId - Should dbId too be returned. Useful for INSERT statements
   *
   * @return array
   */
  public function getRow($withDbId = true) {
//    $row = $this->row;
    $row = array();
    foreach($this->_container->getProperties() as $fieldName => $cork) {
      $row[$fieldName] = $this->$fieldName;
    }

    if (!$withDbId) {
      unset($row[$this->getIdFieldName()]);
      unset($row['dbId']);
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
