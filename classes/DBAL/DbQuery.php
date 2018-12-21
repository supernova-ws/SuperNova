<?php
/**
 * Created by Gorlum 07.08.2016 2:36
 */

namespace DBAL;

use \HelperArray;
use DBAL\db_mysql;
use \SN;

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

  const DB_INSERT_PLAIN = 0;
  const DB_INSERT_REPLACE = 1;
  const DB_INSERT_IGNORE = 2;

  const DB_RECORDS_ALL = false;
  const DB_RECORD_ONE = true;

  const DB_SHARED = false;
  const DB_FOR_UPDATE = true;

  /**
   * @var db_mysql $db
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
   * Contains field names integer keyed
   *
   * For SELECT {fields} FROM
   * For INSERT/REPLACE {fields} UPDATE ...
   *
   * @var array $fields
   */
  protected $fields = array();
  protected $where = array();
  protected $whereDanger = array();

  /**
   * Contain array of values - fielded or not
   *
   * For INSERT/REPLACE ... SET, UPDATE ... SET - contains fieldName => value
   * For INSERT/REPLACE ... VALUES - contains values[][]
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

  protected $forUpdate = false;

  /**
   * DbQuery constructor.
   *
   * @param  null|\DBAL\db_mysql $db
   */
  // TODO - $db should be supplied externally
  public function __construct($db = null) {
    $this->db = empty($db) ? SN::$gc->db : $db;
  }

  /**
   * @param null|db_mysql $db
   *
   * @return static
   */
  public static function build($db = null) {
    return new static($db);
  }


  public function select() {
    $this->build = array();

    $this->buildCommand(self::SELECT);
    $this->build[] = ' *';
    $this->build[] = " FROM " . $this->quoteTable($this->table);
    $this->buildWhere();
    $this->buildLimit();
    $this->buildForUpdate();

    return $this->__toString();
  }

  public function delete() {
    $this->build = array();

    $this->buildCommand(self::DELETE);
    $this->buildWhere();
    $this->buildLimit();

    return $this->__toString();
  }

  public function update() {
    $this->build = array();

    $this->buildCommand(self::UPDATE);
    $this->buildSetFields();
    $this->buildWhere();
    $this->buildLimit();

    return $this->__toString();
  }

  /**
   * @param int $replace
   *
   * @return string
   */
  protected function setInsertCommand($replace) {
    switch ($replace) {
      case self::DB_INSERT_IGNORE:
        $result = self::INSERT_IGNORE;
      break;
      case self::DB_INSERT_REPLACE:
        $result = self::REPLACE;
      break;
      default:
        $result = self::INSERT;
      break;
    }

    return $result;
  }

  /**
   * @param int  $replace
   * @param bool $forceSingleInsert
   *
   * @return bool
   */
  public function doInsert($replace = self::DB_INSERT_PLAIN, $forceSingleInsert = false) {
    return doquery($this->insert($replace, $forceSingleInsert));
  }

  public function doUpdate() {
    return doquery($this->update());
  }

  public function doUpdateDb() {
    return $this->db->doquery($this->update());
  }

  /**
   * @return array|bool|\mysqli_result|null
   * @deprecated
   */
  public function doDelete() {
    return doquery($this->delete());
  }

  // TODO - Do something with delete when there is no records
  public function doDeleteDb() {
    return $this->db->doquery($this->delete());
  }

  /**
   * @return array|bool|\mysqli_result|null
   */
  public function doSelect() {
    return doquery($this->select());
  }

  /**
   * @return array|null
   */
  public function doSelectFetch() {
    return doquery($this->select(), true);
  }

  /**
   * @param int  $replace
   * @param bool $forceSingleInsert
   *
   * @return string
   */
  public function insert($replace = self::DB_INSERT_PLAIN, $forceSingleInsert = false) {
    $this->build = array();

    $this->buildCommand($this->setInsertCommand($replace));

    if (!$forceSingleInsert && is_array($this->fields) && !empty($this->fields)) {
      // If there are fields - it's batch insert... unless it forced single insert
      $this->build[] = " (";
      $this->buildFieldNames(); // used $this->fields
      $this->build[] = ") VALUES ";
      $this->buildValuesVector(); // $this->valuesDanger + $this->values
    } else {
      // Otherwise - it's single field insert
      $this->buildSetFields();
    }

    return $this->__toString();
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
  public function setOneRow($oneRow = self::DB_RECORD_ONE) {
    $this->isOneRow = $oneRow;

    return $this;
  }

  /**
   * @param bool $forUpdate - DB_FOR_UPDATE || DB_SHARED
   *
   * @return $this
   */
  public function setForUpdate($forUpdate = self::DB_FOR_UPDATE) {
    $this->forUpdate = $forUpdate;

    return $this;
  }

  /**
   * Set values for a query
   *
   * Values used for INSERT/REPLACE ... SET queries as one-dimension array and INSERT/REPLACE ... VALUES as two-dimension array
   *
   * @param array|array[] $values - [(str)name => (mixed)value] | [ [(str)name => (mixed)value] ]
   *
   * @return $this
   */
  public function setValues($values = []) {
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
   */
  public function setWhereArrayDanger($whereArrayDanger = array()) {
    HelperArray::merge($this->whereDanger, $whereArrayDanger, HelperArray::MERGE_PHP);

    return $this;
  }


  /**
   * Wrapper for db_escape()
   *
   * @param mixed $string
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
  public function quote($fieldName) {
    return "`" . $this->escape((string)$fieldName) . "`";
  }

  /**
   * Make "adjustment" string like `$fieldValue` = `$fieldValue` + ('$fieldName')
   * Quotes needs for negative values
   *
   * @param mixed      $fieldValue
   * @param int|string $fieldName
   *
   * @return string
   */
  public function makeAdjustString($fieldValue, $fieldName) {
    return is_int($fieldName)
      ? $this->makeValueSafe($fieldValue)
      : (
        ($fieldNameQuoted = $this->quote($fieldName))
        . " = "
        . $fieldNameQuoted
        . " + ("
        . $this->makeValueSafe($fieldValue)
        . ")"
      );
  }

  /**
   * Make "equal" string like `$fieldValue` = '$fieldName'
   *
   * @param mixed      $fieldValue
   * @param int|string $fieldName - field name. Is this param is integer - no field name added
   *
   * @return string
   */
  public function makeFieldEqualValue($fieldValue, $fieldName) {
    return is_int($fieldName)
      ? $this->makeValueSafe($fieldValue)
      : ($this->quote($fieldName) . " = " . $this->makeValueSafe($fieldValue));
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

  /**
   * Makes value safe for using in SQL query
   *
   * @param mixed $value
   *
   * @return int|string
   */
  public function makeValueSafe($value) {
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
   * @param $command
   */
  protected function buildCommand($command) {
    switch ($this->command = $command) {
      case self::UPDATE:
        $this->build[] = $this->command . " " . $this->quoteTable($this->table);
      break;

      case self::DELETE:
        $this->build[] = $this->command . " FROM " . $this->quoteTable($this->table);
      break;

      case self::REPLACE:
      case self::INSERT_IGNORE:
      case self::INSERT:
        $this->build[] = $this->command . " INTO " . $this->quoteTable($this->table);
      break;

      case self::SELECT:
        $this->build[] = $this->command;
      break;
    }
  }

  // UPDATE/INSERT ... SET field = value, ...
  protected function buildSetFields() {
    $safeFields = array();
    // Sets overwritten by Adjusts
    if ($safeValuesDanger = implode(',', $this->valuesDanger)) {
      $safeFields[] = &$safeValuesDanger;
    }
    if ($safeFieldsEqualValues = implode(',', HelperArray::map($this->values, array($this, 'makeFieldEqualValue'), true))) {
      $safeFields[] = &$safeFieldsEqualValues;
    }
    if ($safeAdjustDanger = implode(',', $this->adjustDanger)) {
      $safeFields[] = &$safeAdjustDanger;
    }
    if ($safeAdjust = implode(',', HelperArray::map($this->adjust, array($this, 'makeAdjustString'), true))) {
      $safeFields[] = &$safeAdjust;
    }
    $safeFieldsString = implode(',', $safeFields);

    if (!empty($safeFieldsString)) {
      $this->build[] = ' SET ';
      $this->build[] = $safeFieldsString;
    }
  }

  // INSERT ... VALUES
  /**
   * Compiles fields list into string list along with quoting fieldnames with "`" symbol
   */
  protected function buildFieldNames() {
    $this->build[] = implode(',', HelperArray::map($this->fields, array($this, 'quote')));
  }

  /**
   * Vector values is for batch INSERT/REPLACE
   */
  // TODO - CHECK!
  protected function buildValuesVector() {
    $compiled = array();

    if (!empty($this->valuesDanger)) {
      $compiled = $this->valuesDanger;
    }

    foreach ($this->values as $valuesVector) {
      $compiled[] = '(' . implode(',', HelperArray::map($valuesVector, array($this, 'makeValueSafe'))) . ')';
    }

    $this->build[] = implode(',', $compiled);
  }


  protected function buildWhere() {
    $safeWhere = implode(
      ' AND ',
      $this->whereDanger +
      HelperArray::map($this->where, array($this, 'makeFieldEqualValue'), true)
    );

    if (!empty($safeWhere)) {
      $this->build[] = " WHERE {$safeWhere}";
    }
  }

  protected function buildLimit() {
    if ($this->isOneRow == self::DB_RECORD_ONE) {
      $this->build[] = ' LIMIT 1';
    }
  }

  protected function buildForUpdate() {
    if ($this->forUpdate == self::DB_FOR_UPDATE) {
      $this->build[] = ' FOR UPDATE';
    }
  }

  public function __toString() {
    return implode('', $this->build);
  }

}
