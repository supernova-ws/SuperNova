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
  const REPLACE = 'REPLACE';
  const INSERT = 'INSERT';
  const INSERT_IGNORE = 'INSERT IGNORE';
  const UPDATE = 'UPDATE';
  const DELETE = 'DELETE';

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

  /**
   * Contains field names
   *
   * For SELECT fields FROM
   * For INSERT/REPLACE fields UPDATE ...
   *
   * @var array $fields
   */
  protected $fields = array();
  protected $where = array();
  protected $whereDanger = array();

  /**
   * Contain array of values - fielded or not
   *
   * For INSERT/REPLACE ... SET contains fieldName => value
   * For INSERT/REPLACE ... UPDATE contains values[][]
   *
   * @var array
   */
  protected $values = array();
  /**
   * Contain array of DANGER values for batch INSERT/REPLACE
   *
   * @var string[]
   */
  protected $valuesDanger = array();
  protected $adjust = array();
  protected $adjustDanger = array();


  /**
   * Variable for incremental query build
   *
   * @var string[] $build
   */
  protected $build = array();

  protected $isOneRow = false;

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
   * @param null|db_mysql $db
   *
   * @return static
   */
  public static function build($db = null) {
    return new static($db);
  }

  protected function buildCommand() {
    switch ($this->command) {
      case static::UPDATE:
        $this->build[] = $this->command . " " . $this->quoteTable($this->table);
      break;

      case static::DELETE:
        $this->build[] = $this->command . " FROM " . $this->quoteTable($this->table);
      break;

      case static::REPLACE:
      case static::INSERT_IGNORE:
      case static::INSERT:
        $this->build[] = $this->command . " INTO " . $this->quoteTable($this->table);
      break;
    }
  }

  protected function buildWhere() {
    $safeWhere = implode(
      ' AND ',
    // TODO - remove onlyDanger with $this->where
      $this->onlyDanger($this->whereDanger) + $this->onlyDanger($this->where) + $this->fieldEqValue($this->where)
    );

    if (!empty($safeWhere)) {
      $this->build[] = " WHERE {$safeWhere}";
    }
  }

  protected function buildLimit() {
    if ($this->isOneRow == DB_RECORD_ONE) {
      $this->build[] = ' LIMIT 1';
    }
  }


  protected function buildFieldsSet() {
    $safeFields = array();
    // Sets overwritten by Adjusts
    if ($safeFieldsEqualValues = implode(',', $this->fieldEqValue($this->values))) {
      $safeFields[] = &$safeFieldsEqualValues;
    }
    if ($safeAdjust = implode(',', $this->safeFieldsAdjust($this->adjust) + $this->onlyDanger($this->adjustDanger))) {
      $safeFields[] = &$safeAdjust;
    }
    $safeFieldsString = implode(',', $safeFields);

    if (!empty($safeFieldsString)) {
      $this->build[] = ' SET ';
      $this->build[] = $safeFieldsString;
    }
  }



  protected function buildFieldNames() {
    $this->build[] = implode(',', $this->safeFields($this->fields));
  }

  // ONLY FOR SCALAR VALUES!!!
  protected function buildValuesScalar() {
    $this->build[] = implode(',', $this->safeFields($this->values));
  }

  /**
   * Vector values is for batch INSERT/REPLACE
   */
  // TODO - CHECK!
  protected function buildValuesVector() {
    $compiled = array();

    foreach ($this->values as $valuesVector) {
      $compiled[] = '(' . implode(',', $this->safeFields($valuesVector)) . ')';
    }

    $this->build[] = implode(',', $compiled);
  }

  protected function buildValuesDanger() {
    $this->build[] = implode(',', $this->valuesDanger);
  }





  public function delete() {
    $this->build = array();

    $this->command = static::DELETE;
    $this->buildCommand();
    $this->buildWhere();
    $this->buildLimit();

    return implode('', $this->build);
  }

  public function insertSet($replace) {
    $this->build = array();

    switch ($replace) {
      case DB_INSERT_IGNORE:
        $this->command = static::INSERT_IGNORE;
      break;
      case DB_INSERT_REPLACE:
        $this->command = static::REPLACE;
      break;
      default:
        $this->command = static::INSERT;
      break;
    }

    $this->buildCommand();
    $this->buildFieldsSet();

    return implode('', $this->build);
  }

  public function insertBatch($replace) {
    $this->build = array();

    switch ($replace) {
      case DB_INSERT_IGNORE:
        $this->command = static::INSERT_IGNORE;
      break;
      case DB_INSERT_REPLACE:
        $this->command = static::REPLACE;
      break;
      default:
        $this->command = static::INSERT;
      break;
    }

    $this->buildCommand();
    $this->build[] = " (";
    $this->buildFieldNames();
    $this->build[] = ") VALUES ";
    $this->buildValuesDanger();

    return implode('', $this->build);
  }

  public function update() {
    $this->build = array();

    $this->command = static::UPDATE;
    $this->buildCommand();
    $this->buildFieldsSet();
    $this->buildWhere();
    $this->buildLimit();

    return implode('', $this->build);
  }





  /**
   * @param $table
   *
   * @return $this
   */
  public function setTable($table) {
    $this->table = $table;

    return $this;
  }

  /**
   * @param bool $oneRow - DB_RECORDS_ALL || DB_RECORD_ONE
   *
   * @return $this
   */
  public function setOneRow($oneRow = DB_RECORDS_ALL) {
    $this->isOneRow = $oneRow;

    return $this;
  }

  /**
   * @param array $values
   *
   * @return $this
   */
  public function setValues($values = array()) {
    HelperArray::merge($this->values, $values, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * @param array $values
   *
   * @return $this
   */
  public function setValuesDanger($values = array()) {
    HelperArray::merge($this->valuesDanger, $values, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * @param array $values
   *
   * @return $this
   */
  public function setAdjust($values = array()) {
    HelperArray::merge($this->adjust, $values, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * @param array $values
   *
   * @return $this
   */
  public function setAdjustDanger($values = array()) {
    HelperArray::merge($this->adjustDanger, $values, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * @param array $fields
   *
   * @return $this
   */
  public function setFields($fields = array()) {
    HelperArray::merge($this->fields, $fields, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * Merges WHERE array as array_merge()
   *
   * @param array $whereArray
   *
   * @return $this
   */
  public function setWhereArray($whereArray = array()) {
    HelperArray::merge($this->where, $whereArray, HelperArray::MERGE_PHP);

    return $this;
  }

  /**
   * Sets DANGER array - where values should be escaped BEFORE entering DBAL
   *
   * Deprecated - all values should pass through DBAL
   *
   * @param array $whereArrayDanger
   *
   * @return $this
   * @deprecated
   */
  public function setWhereArrayDanger($whereArrayDanger = array()) {
    HelperArray::merge($this->whereDanger, $whereArrayDanger, HelperArray::MERGE_PHP);

    return $this;
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

  protected function escapeEmulator($value) {
    // Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
    return str_replace(
      array("\\", "\0", "\n", "\r", "'", "\"", "\z",),
      array('\\\\', '\0', '\n', '\r', '\\\'', '\"', '\z',),
      $value
    );
  }

  /**
   * Escaping string value and quoting it
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
   * Quote table name with `{{ }}`
   *
   * @param mixed $tableName
   *
   * @return string
   */
  protected function quoteTable($tableName) {
    return "`{{" . $this->escape((string)$tableName) . "}}`";
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
   * Make list of DANGER items from clauses - WHERE for ex
   *
   * This function is DANGER! It takes numeric indexes which translate to direct SQL string which can lead to SQL injection!
   *
   * @param array $where - array WHERE clauses which will not pass through SAFE filter
   *
   * @return array
   */
  protected function onlyDanger($where) {
    $result = array();

    if (!is_array($where) || empty($where)) {
      return $result;
    }

    foreach ($where as $key => $fieldValue) {
      // Integer $fieldName means "leave as is" - for expressions and already processed fields
      if (is_int($key)) {
        $result[$key] = $fieldValue;
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

  // TODO - redo as callable usage with array_map/array_walk
  public function safeFields($fields) {
    $result = array();

    if (!is_array($fields) || empty($fields)) {
      return $result;
    }

    // For now $key is INTEGERS
    foreach ($fields as $key => $value) {
      $result[$key] = $this->quote($value);
    }

    return $result;
  }

  /**
   * Make fields adjustment safe. FUNCTION IS NOT DANGER
   *
   * Convert "key => value" pair to string "`key` = `key` + (value)"
   *
   * @param array $fields - array of pair $fieldName => $fieldValue
   *
   * @return array
   */
  protected function safeFieldsAdjust($fields) {
    $result = array();

    if (!is_array($fields) || empty($fields)) {
      return $result;
    }

    foreach ($fields as $fieldName => $fieldValue) {
      // Integer $fieldName means "leave as is" - for expressions and already processed fields
      if (is_string($fieldName)) {
        $result[$fieldName] = "`{$fieldName}` = `{$fieldName}` + (" . $this->castAsDbValue($fieldValue) . ")";
      }
    }

    return $result;
  }

}
