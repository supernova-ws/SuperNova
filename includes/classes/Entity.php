<?php

class Entity {

  /**
   * @var db_mysql|null $dbStatic
   */
  public static $dbStatic = null;
  public static $tableName = '_table';
  public static $idField = 'id';
  public static $_containerName = 'PropertyHiderInArray';

  /**
   * @var array $row
   */
  protected $row = array();

  /**
   * @var PropertyHiderInArray
   */
  public $_container;

  /**
   * @var int|float|string $dbId
   */
  protected $dbId = 0;


  /**
   * Buddy constructor.
   *
   * @param \Pimple\GlobalContainer $c
   */
  public function __construct($c) {
    empty(static::$dbStatic) && !empty($c->db) ? static::$dbStatic = $c->db : false;

    $this->_container = new static::$_containerName();
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
    return empty($this->row[static::$idField]);
  }

  /**
   * @param array $row
   */
  public function setRow($row) {
    $this->row = $row;
    // TODO - $row can be empty
    if (!empty(static::$idField)) {
      $this->setDbId($row[static::$idField]);
    }
  }

  /**
   * Compiles object data into db row
   *
   * @return array
   */
  public function getRow() {
    return $this->row;
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

}
