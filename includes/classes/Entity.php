<?php

class Entity {

  /**
   * @var db_mysql|null $dbStatic
   */
  public static $dbStatic = null;
  public static $tableName = '_table';
  public static $idField = 'id';

  /**
   * @var array $row
   */
  public $row = array();


  /**
   * Buddy constructor.
   *
   * @param \Pimple\GlobalContainer $c
   */
  public function __construct($c) {
    empty(static::$dbStatic) && !empty($c->db) ? static::$dbStatic = $c->db : false;
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
    $query = array();
    foreach($this->row as $fieldName => $fieldValue) {
      $fieldValue = self::$dbStatic->db_escape($fieldValue);
      $query[] = "`{$fieldName}` = '{$fieldValue}'";
    }

    $query = implode(',', $query);
    if(empty($query)) {
      // TODO Exceptiion
      return 0;
    }

    self::$dbStatic->doquery("INSERT INTO `{{" . static::$tableName . "}}` SET " . $query);

    // TODO Exceptiion if result is empty
    return $this->row[static::$idField] = self::$dbStatic->db_insert_id();
  }

  public function isEmpty() {
    return empty($this->row);
  }

  public function isNew() {
    return empty($this->row[static::$idField]);
  }

}
