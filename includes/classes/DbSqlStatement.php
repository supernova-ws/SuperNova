<?php

//pdump(DBStaticUser::getMaxId());
//pdump(DBStaticUser::getRecordById(67));
//pdump(DBStaticUser::filterIdListStringRepack('2,3,5,67'));


/**
 * Class DbSqlStatement
 *
 * @method static DbSqlStatement fields(mixed $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement join(mixed $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement where(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement groupBy(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement orderBy(array $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 * @method static DbSqlStatement having(mixed $value, int $mergeStrategy = HelperArray::ARRAY_REPLACE)
 *
 */
class DbSqlStatement extends DbSqlAware {

  const SELECT = 'SELECT';

  protected static $allowedOperations = array(
    self::SELECT,
  );

  public $operation = '';

  public $table = '';
  public $alias = '';

  public $idField = '';

  /**
   * @var array
   */
  public $fields = array();

  public $join = array();

  public $where = array();
  public $groupBy = array();
  public $orderBy = array();
  public $having = array();

  public $limit = 0;
  public $offset = 0;

  public $fetchOne = false;
  public $forUpdate = false;
  public $skipLock = false;

  protected $_compiledQuery = array();

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
   * @return $this
   */
  public function select() {
    $this->operation = DbSqlStatement::SELECT;
//    if (empty($this->fields) && $initFields) {
//      $this->fields = array('*');
//    }

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
   * @param mixed ...
   *
   * @return $this
   */
  public function field() {
    $arguments = func_get_args();

    // Special case - call method with array of fields
    if(count($arguments) == 1 && is_array($arguments[0])) {
      $arguments = array_shift($arguments);
    }

    foreach($arguments as $arg) {
      $this->fields[] = $arg;
    }

    return $this;
  }

  public function singleFunction($functionName, $field = '*', $alias = DbSqlLiteral::SQL_LITERAL_ALIAS_NONE) {
    return $this->field(DbSqlLiteral::build($this->db)->buildSingleArgument($functionName, $field, $alias));
  }

  public function count($field = '*', $alias = DbSqlLiteral::SQL_LITERAL_ALIAS_NONE) {
    return $this->field(DbSqlLiteral::build($this->db)->count($field, $alias));
  }

  public function isNull($field = '*', $alias = DbSqlLiteral::SQL_LITERAL_ALIAS_NONE) {
    return $this->field(DbSqlLiteral::build($this->db)->isNull($field, $alias));
  }

  public function __call($name, $arguments) {
    if (in_array($name, array('fields', 'join', 'where', 'groupBy', 'orderBy', 'having'))) {
//      array_unshift($arguments, '');
//      $arguments[0] = &$this->$name;
//      call_user_func_array('HelperArray::merge', $arguments);
      HelperArray::merge($this->$name, $arguments[0], !empty($arguments[1]) ? $arguments[1] : HelperArray::ARRAY_REPLACE);
    }
    // TODO - make all setters protected ??
//    elseif(method_exists($this, $name)) {
//      call_user_func_array(array($this, $name), $arguments);
//    }

    return $this;
  }

  /**
   * @param int $limit
   *
   * @return $this
   */
  public function limit($limit) {
    $this->limit = is_numeric($limit) ? $limit : 0;

    return $this;
  }

  /**
   * @param int $offset
   *
   * @return $this
   */
  public function offset($offset) {
    $this->offset = is_numeric($offset) ? $offset : 0;

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
   * @param string $className
   *
   * @return $this
   */
  public function getParamsFromStaticClass($className) {
    if (is_string($className) && $className && class_exists($className)) {
      $this->from($className::$_table);
      $this->setIdField($className::$_idField);
    }

    return $this;
  }

  /**
   * @param db_mysql|null $db
   * @param string        $className
   *
   * @return static
   */
  public static function build($db = null, $className = '') {
    /**
     * @var static $result
     */
    $result = parent::build($db);
    if (!empty($className) && is_string($className)) {
      $result->getParamsFromStaticClass($className);
    }

    return $result;
  }

  /**
   * Resets statement
   *
   * @param bool $full
   *
   * @return static
   */
  protected function _reset($full = true) {
    if ($full) {
      $this->operation = '';
      $this->table = '';
      $this->alias = '';
      $this->idField = '';
    }

    $this->fields = array();
    $this->where = array();
    $this->groupBy = array();
    $this->orderBy = array();
    $this->having = array();

    $this->limit = 0;
    $this->offset = 0;

    $this->fetchOne = false;
    $this->forUpdate = false;
    $this->skipLock = false;

    return $this;
  }


  /**
   * @param array $array
   *
   * @return array
   */
  protected function arrayEscape(&$array) {
    $result = array();
    foreach ($array as $key => &$value) {
      $result[$key] = $this->stringEscape($value);
    }

    return $result;
  }

  protected function compileOperation() {
    if (empty($this->operation)) {
      throw new ExceptionDbOperationEmpty();
    }

    if (!in_array($this->operation, self::$allowedOperations)) {
      throw new ExceptionDbOperationRestricted();
    }

    $this->_compiledQuery[] = $this->stringEscape($this->operation);
  }

  protected function compileSubject() {
    $this->_compiledQuery[] = $this->selectFieldsToString($this->fields);
  }

  protected function compileFrom() {
    $this->_compiledQuery[] = 'FROM `{{' . $this->stringEscape($this->table) . '}}`';
    if (!empty($this->alias)) {
      $this->_compiledQuery[] = 'AS `' . $this->stringEscape($this->alias) . '`';
    }
  }

  protected function compileJoin() {
    !empty($this->join) ? $this->_compiledQuery[] = implode(' ', $this->join) : false;
  }

  protected function compileWhere() {
    // TODO - fields should be escaped !!
    !empty($this->where) ? $this->_compiledQuery[] = 'WHERE ' . implode(' AND ', $this->where) : false;
  }

  protected function compileGroupBy() {
    // TODO - fields should be escaped !!
//    !empty($this->groupBy) ? $this->_compiledQuery[] = 'GROUP BY ' . implode(',', $this->arrayEscape($this->groupBy)) : false;
    !empty($this->groupBy) ? $this->_compiledQuery[] = 'GROUP BY ' . $this->selectFieldsToString($this->groupBy) : false;
  }

  protected function compileOrderBy() {
    // TODO - fields should be escaped !!
    !empty($this->orderBy) ? $this->_compiledQuery[] = 'ORDER BY ' . implode(',', $this->arrayEscape($this->orderBy)) : false;
  }

  protected function compileHaving() {
    // TODO - fields should be escaped !!
    !empty($this->having) ? $this->_compiledQuery[] = 'HAVING ' . implode(' AND ', $this->having) : false;
  }

  protected function compileLimit() {
    // TODO - fields should be escaped !!
    if ($limit = $this->fetchOne ? 1 : $this->limit) {
      $this->_compiledQuery[] = 'LIMIT ' . $limit . (!empty($this->offset) ? ' OFFSET ' . $this->offset : '');
    }
  }

  protected function compileForUpdate() {
    $this->_compiledQuery[] =
      // forUpdate flag forces select with row locking - didn't look at skipLock flag
      $this->forUpdate
      ||
      // Also row locked when transaction is up and skipLock flag is not set
      (classSupernova::db_transaction_check(false) && !$this->skipLock) ? 'FOR UPDATE' : '';
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
    } elseif (is_numeric($fieldName)) {
      $result = $fieldName;
    } elseif (is_null($fieldName)) {
      $result = 'NULL';
    } else {
      // Field has other type - string or should be convertible to string
      $result = (string)$fieldName;
      if (!$fieldName instanceof DbSqlLiteral) {
        $result = $this->makeFieldFromString($fieldName);
      }
    }

    return $result;
  }


  /**
   * @return string
   * @throws ExceptionDbOperationEmpty
   * @throws ExceptionDbOperationRestricted
   */
  public function __toString() {
    $this->_compiledQuery = array();

    $this->compileOperation();
    $this->compileSubject();
    $this->compileFrom();
    $this->compileJoin();
    $this->compileWhere();
    $this->compileGroupBy();
    $this->compileOrderBy();
    $this->compileHaving();
    $this->compileLimit();
    $this->compileForUpdate();

    return implode(' ', $this->_compiledQuery);
  }

}
