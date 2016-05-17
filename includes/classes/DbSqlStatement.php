<?php

//pdump(DBStaticUser::getMaxId());
//pdump(DBStaticUser::getRecordById(67));


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
  public $limit = array();

  public $fetchOne = false;

  /**
   * @param null   $db
   * @param string $className
   *
   * @return DbSqlStatement
   */
  public static function build($db = null, $className = '') {
    $result = new self($db);
    if(!empty($className) && is_string($className)) {
      $result->getParamsFromStaticClass($className);
    }
    return $result;
  }

  public function __construct($db = null) {
    $this->db = (!empty($db) && $db instanceof db_mysql) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
  }

  /**
   * Resets statement
   *
   * @param bool $full
   *
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
    $this->limit = array();
    $this->fetchOne = false;
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
   * @return $this
   */
  public function select() {
    $this->_reset(false);
    $this->operation = DbSqlStatement::SELECT;
    $this->fields = array('*');

    return $this;
  }

  /**
   * @param array $fields
   *
   * @return $this
   */
  public function fields($fields = array()) {
    $this->fields = $fields;

    return $this;
  }

  /**
   * @param array $where
   *
   * @return $this
   */
  // TODO - fields should be escaped !!
  // TODO - $where should be validated and checked!
  public function where($where = array()) {
    $this->where = $where;

    return $this;
  }

  public function fetchOne() {
    $this->fetchOne = true;
    $this->limit = array(1);

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
    $result .= !empty($this->limit) ? ' LIMIT ' . implode(',', $this->limit) : '';

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
    if (!is_array($fields)) {
      $fields = array($fields);
    }

    $result = array();
    foreach ($fields as $fieldName) {
      switch (true) {
        case $fieldName === '*':
          $result[] = '*';
        break;

        case is_bool($fieldName):
          $result[] = intval($fieldName);
        break;

        case is_numeric($fieldName):
          $result[] = $fieldName;
        break;

        case is_null($fieldName):
          $result[] = 'NULL';
        break;

        default:
          $string = (string)$fieldName;
          if ($string == '') {
            continue;
          }

          if($fieldName instanceof DbSqlLiteral) {
            // Literals plays as they are - they do properly format by itself
            $result[] = $string;
          } else {
            // Other should be formatted
            $result[] = '`' . $this->stringEscape($string) . '`';
          }
      }
    }

    if (empty($result)) {
      throw new ExceptionDBFieldEmpty();
    }

    return implode(',', $result);
  }

  protected function stringEscape($string) {
    return method_exists($this->db, 'db_escape') ? $this->db->db_escape($string) : str_replace('`', '\`', addslashes($string));
  }

}
