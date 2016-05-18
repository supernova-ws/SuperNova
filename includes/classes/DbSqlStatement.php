<?php

//pdump(DBStaticUser::getMaxId());
//pdump(DBStaticUser::getRecordById(67));
//pdump(DBStaticUser::filterIdListStringRepack('2,3,5,67'));


/**
 * Class DbSqlStatement
 *
 * @method static DbSqlStatement fields(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement where(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement group(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement order(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement having(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 *
 */
class DbSqlStatement {

  const SELECT = 'SELECT';

  protected static $allowedOperations = array(
    self::SELECT,
  );

  /**
   * @var db_mysql $db
   */
  protected $db;

  public $operation = '';

  public $table = '';
  public $alias = '';

  public $idField = '';

  /**
   * @var array
   */
  public $fields = array();

  public $where = array();
  public $group = array();
  public $order = array();

  public $having = array();


  /**
   * @var array
   *  [0] - row_count
   *  [1] - offset
   *    Used {LIMIT row_count [OFFSET offset]} syntax
   */
  // TODO - separate offset and row_count
  public $limit = 0;
  public $offset = 0;

  public $fetchOne = false;
  public $forUpdate = false;
  public $skipLock = false;

  /**
   * @param db_mysql|null $db
   * @param string        $className
   *
   * @return DbSqlStatement
   */
  public static function build($db = null, $className = '') {
    $result = new self($db);
    if (!empty($className) && is_string($className)) {
      $result->getParamsFromStaticClass($className);
    }

    return $result;
  }

  /**
   * DbSqlStatement constructor.
   *
   * @param db_mysql|null $db
   */
  public function __construct($db = null) {
    $this->db = (!empty($db) && $db instanceof db_mysql) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
  }

  /**
   * Resets statement
   *
   * @param bool $full
   *
   * @return $this
   */
  // TODO - UNITTEST
  protected function _reset($full = true) {
    if ($full) {
      $this->operation = '';
      $this->table = '';
      $this->alias = '';
      $this->idField = '';
    }

    $this->fields = array();
    $this->where = array();
    $this->group = array();
    $this->order = array();
    $this->having = array();

    $this->limit = 0;
    $this->offset = 0;

    $this->fetchOne = false;
    $this->forUpdate = false;
    $this->skipLock = false;

    return $this;
  }

  /**
   * @param string $fieldName
   *
   * @return $this
   */
  public function setIdField($fieldName) {
    $this->idField = $fieldName;

    return $this;
  }

  /**
   * @param string $alias
   *
   * @return $this
   */
  public function fromAlias($alias) {
    $this->alias = $alias;

    return $this;
  }

  /**
   * @param int $limit
   *
   * @return $this
   */
  public function limit($limit) {
    $this->limit = $limit;

    return $this;
  }

  /**
   * @param int $offset
   *
   * @return $this
   */
  public function offset($offset) {
    $this->offset = $offset;

    return $this;
  }


  /**
   * @param string $tableName
   * @param string $alias
   *
   * @return $this
   */
  public function from($tableName, $alias = '') {
    $this->table = $tableName;
    $this->fromAlias($alias);

    return $this;
  }

  /**
   * @param string $params
   *
   * @return $this
   */
  public function getParamsFromStaticClass($params) {
    if (is_string($params) && $params && class_exists($params)) {
      $this->from($params::$_table);
      $this->setIdField($params::$_idField);
    }

    return $this;
  }


  /**
   * @return self
   */
  public function select() {
    $this->operation = DbSqlStatement::SELECT;
    if (empty($this->fields)) {
      $this->fields = array('*');
    }

    return $this;
  }

  public function __call($name, $arguments) {
    // TODO: Implement __call() method.
    if (in_array($name, array('fields', 'where', 'group', 'order', 'having'))) {
      array_unshift($arguments, '');
      $arguments[0] = &$this->$name;
      call_user_func_array('HelperArray::merge', $arguments);
    }

    return $this;
  }

  /**
   * Make statement fetch only one record
   *
   * @return $this
   */
  public function fetchOne($fetchOne = true) {
    $this->fetchOne = $fetchOne;

    return $this;
  }

  /**
   * @return $this
   */
  public function forUpdate($forUpdate = true) {
    $this->forUpdate = $forUpdate;

    return $this;
  }

  /**
   * @return $this
   */
  public function skipLock($skipLock = true) {
    $this->skipLock = $skipLock;

    return $this;
  }


  /**
   * @return string
   * @throws ExceptionDbOperationEmpty
   * @throws ExceptionDbOperationRestricted
   */
  public function __toString() {
    if (empty($this->operation)) {
      throw new ExceptionDbOperationEmpty();
    }

    if (!in_array($this->operation, self::$allowedOperations)) {
      throw new ExceptionDbOperationRestricted();
    }

    $result = '';
    $result .= $this->stringEscape($this->operation);

    $result .= ' ' . $this->selectFieldsToString($this->fields);

    $result .= ' FROM';
    $result .= ' `{{' . $this->stringEscape($this->table) . '}}`';
    $result .= !empty($this->alias) ? ' AS `' . $this->stringEscape($this->alias) . '`' : '';

    // TODO - fields should be escaped !!
    $result .= !empty($this->where) ? ' WHERE ' . implode(' AND ', $this->where) : '';

    // TODO - fields should be escaped !!
    $result .= !empty($this->group) ? ' GROUP BY ' . implode(',', $this->group) : '';

    // TODO - fields should be escaped !!
    $result .= !empty($this->order) ? ' ORDER BY ' . implode(',', $this->order) : '';

    // TODO - fields should be escaped !!
    $result .= !empty($this->having) ? ' HAVING ' . implode(' AND ', $this->having) : '';

    // TODO - fields should be escaped !!
    $limit = $this->fetchOne ? 1 : $this->limit;
    $result .= !empty($limit)
      ? ' LIMIT ' . $limit . (!empty($this->offset) ? ' OFFSET ' . $this->offset : '')
      : '';

    $result .=
      // forUpdate flag forces select with row locking - didn't look at skipLock flag
      $this->forUpdate
      ||
      // Also row locked when transaction is up and skipLock flag is not set
      (classSupernova::db_transaction_check(false) && !$this->skipLock) ? ' FOR UPDATE' : '';

    // TODO - protect from double escape!

    return $result;
  }

  /**
   * @param array|mixed $fields
   *
   * @return string
   * @throws ExceptionDBFieldEmpty
   */
  protected function selectFieldsToString($fields) {
    HelperArray::makeArrayRef($fields);

    $result = array();
    foreach ($fields as $fieldName) {
      $string = $this->processField($fieldName);
      if ($string !== '') {
        $result[] = $string;
      }
    }

    if (empty($result)) {
      throw new ExceptionDBFieldEmpty();
    }

    return implode(',', $result);
  }

  /**
   * @param mixed $fieldName
   *
   * @return string
   */
  protected function processField($fieldName) {
    if (is_bool($fieldName)) {
      $result = (string)intval($fieldName);
    } elseif (is_null($fieldName)) {
      $result = 'NULL';
    } elseif ($fieldName === '*') {
      $result = '*';
    } else {
      $result = $this->processFieldDefault($fieldName);
    }

    return $result;
  }

  /**
   * @param mixed $fieldName
   *
   * @return string
   */
  protected function processFieldDefault($fieldName) {
    $result = (string)$fieldName;
    if (
      $result != ''
      &&
      // Literals plays as they are - they do properly format by itself
      !($fieldName instanceof DbSqlLiteral)
      &&
      // Numeric need no escaping
      !is_numeric($fieldName)
    ) {
      // Other should be formatted
      $result = '`' . $this->stringEscape($result) . '`';
    }

    return $result;
  }

  /**
   * @param $string
   *
   * @return mixed|string
   */
  protected function stringEscape($string) {
    return
      method_exists($this->db, 'db_escape')
        ? $this->db->db_escape($string)
        : str_replace('`', '\`', addslashes($string));
  }

}
