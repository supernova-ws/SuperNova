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

  public function isEmpty() {
    return empty($this->row);
  }

}
