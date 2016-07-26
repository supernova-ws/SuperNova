<?php

/**
 * Class Entity
 *
 * @property int|float $dbId Buddy record DB ID
 */
class Entity {
  protected static $tableName = '_table';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected static $idField = 'id';
  /**
   * @var PropertyHider
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
    $this->row = $row;
    // TODO - $row can be empty
    if ($this->getIdFieldName() != 0) {
      $this->setDbId($row[$this->getIdFieldName()]);
    }
  }

  /**
   * Compiles object data into db row
   *
   * @param bool $withDbId - Should dbId too be returned. Usefull for INSERT statements
   *
   * @return array
   */
  public function getRow($withDbId = true) {
    $row = $this->row;
    if (!$withDbId) {
      unset($row[$this->getIdFieldName()]);
    }

    return $row;
  }

  public function setDbId($value) {
    $this->dbId = $value;
  }

  /**
   * @return int|float|string
   */
  public function getDbId() {
    return $this->dbId;
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
