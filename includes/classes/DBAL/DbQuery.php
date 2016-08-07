<?php
/**
 * Created by Gorlum 07.08.2016 2:36
 */

namespace DBAL;

use \HelperArray;
use \db_mysql;
use \classSupernova;

/**
 * Class DbQuery
 *
 * New replacement for DbQueryConstructor
 * Simplified version
 * Chained calls - "Fluid interface"
 *
 * @package DBAL
 */
class DbQuery {

  const SELECT = 'SELECT';
  const INSERT = 'INSERT';
  const UPDATE = 'UPDATE';
  const DELETE = 'DELETE';
  const REPLACE = 'REPLACE';

  /**
   * @var db_mysql
   */
  protected $db;

  /**
   * Which command would be performed
   *
   * @var string $command
   */
  protected $command;

  protected $table = '';
  protected $fields = array();
  protected $where = array();
  protected $whereDanger = array();

  /**
   * Variable for increment query build
   *
   * @var string[] $build
   */
  protected $build = array();

  protected $isOneRow = false;

  /**
   * @param null|db_mysql $db
   *
   * @return static
   */
  public static function build($db = null) {
    return new static($db);
  }

  /**
   * DbQuery constructor.
   *
   * @param  null|\db_mysql $db
   */
  // TODO - $db should be supplied externally
  public function __construct($db = null) {
    $this->db = empty($db) ? classSupernova::$gc->db : $db;
  }

  /**
   * Wrapper for db_escape()
   *
   * @param $string
   *
   * @return string
   */
  protected function escape($string) {
    return $this->db->db_escape($string);
  }

  /**
   * Wrapper for db_escape()
   *
   * @param mixed $value
   *
   * @return string
   */
  protected function stringValue($value) {
    return "'" . $this->escape((string)$value) . "'";
  }

  /**
   * Quote mysql DB identifier
   *
   * @param mixed $fieldName
   *
   * @return string
   */
  protected function quote($fieldName) {
    return "`" . $this->escape((string)$fieldName) . "`";
  }

  /**
   * Quote table name with {{ }}
   *
   * @param mixed $tableName
   *
   * @return string
   */
  protected function quoteTable($tableName) {
    return "`{{" . $this->escape((string)$tableName) . "}}`";
  }

  public function table($table) {
    $this->table = $table;

    return $this;
  }

  /**
   * @param bool $oneRow - DB_RECORDS_ALL || DB_RECORD_ONE
   *
   * @return $this
   */
  public function oneRow($oneRow = DB_RECORDS_ALL) {
    $this->isOneRow = ($oneRow == DB_RECORD_ONE);

    return $this;
  }

  /**
   * Merges WHERE array as array_merge()
   *
   * @param array $whereArray
   */
  public function whereArray($whereArray = array()) {
    HelperArray::merge($this->where, $whereArray, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * Merges WHERE array as array_merge()
   *
   * @param array $whereArrayDanger
   * @deprecated
   */
  public function whereArrayDanger($whereArrayDanger = array()) {
    HelperArray::merge($this->whereDanger, $whereArrayDanger, HelperArray::MERGE_PHP);

    return $this;
  }

  protected function castAsDbValue($value) {
    switch (gettype($value)) {
      case TYPE_INTEGER:
      case TYPE_DOUBLE:
        // do nothing
      break;

      case TYPE_BOOLEAN:
        $value = $value ? 1 : 0;
      break;

      case TYPE_NULL:
        $value = 'NULL';
      break;

      case TYPE_EMPTY:
        // No-type defaults to string
        /** @noinspection PhpMissingBreakStatementInspection */
      case TYPE_ARRAY:
        $value = serialize($value);
      // Continuing with serialized array value
      case TYPE_STRING:
      default:
        $value = $this->stringValue($value);
      break;
    }

    return $value;
  }

  /**
   * Make list of DANGER where clauses
   *
   * This function is DANGER! It takes numeric indexes which translate to direct SQL string which can lead to SQL injection!
   *
   * @param array $where - array WHERE clauses which will not pass through SAFE filter
   *
   * @return array
   */
  protected function dangerWhere($where) {
    $result = array();

    if (!is_array($where) || empty($where)) {
      return $result;
    }

    foreach ($where as $fieldName => $fieldValue) {
      // Integer $fieldName means "leave as is" - for expressions and already processed fields
      if (is_int($fieldName)) {
        $result[] = $fieldValue;
      }
    }

    return $result;
  }

  /**
   * Make field list safe. NOT DANGER
   *
   * This function is NOT DANGER
   * Make SQL-safe assignment/equal compare string from (field => value) pair
   *
   * @param array $fieldValues - array of pair $fieldName => $fieldValue
   *
   * @return array
   */
  protected function fieldEqValue($fieldValues) {
    $result = array();

    if (!is_array($fieldValues) || empty($fieldValues)) {
      return $result;
    }

    foreach ($fieldValues as $fieldName => $fieldValue) {
      // Integer $fieldName is DANGER! They skipped there!
      if (!is_int($fieldName)) {
        $result[$fieldName] = $this->quote($fieldName) . " = " . $this->castAsDbValue($fieldValue);
      }
    }

    return $result;
  }

  protected function buildCommand() {
    switch ($this->command) {
      case static::DELETE:
        $this->build[] = static::DELETE . " FROM " . $this->quoteTable($this->table) . ' ';
      break;
    }
  }

  protected function buildWhere() {
    $safeWhere = implode(
      ' AND ',
      $this->dangerWhere($this->whereDanger) + $this->dangerWhere($this->where) + $this->fieldEqValue($this->where)
    );

    if (!empty($safeWhere)) {
      $this->build[] = " WHERE {$safeWhere}";
    }
  }

  protected function buildLimit() {
    if ($this->isOneRow) {
      $this->build[] = ' LIMIT 1';
    }
  }


  public function delete() {
    $this->build = array();

    $this->command = static::DELETE;
    $this->buildCommand();
    $this->buildWhere();
    $this->buildLimit();

    return implode('', $this->build);
  }

}
