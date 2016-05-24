<?php

/**
 * Class DbSqlPrepare
 */
// TODO - Вааще всё переделать.
// Получение стейтмента по строке - это должен быть статик
// Тогда же должны ребиндится параметры
class DbSqlPrepare {
  const COMMENT_PLACEHOLDER = ':__COMMENT__';
  const PARAM_PREG = '#(\:.+?\b)#im';

  /**
   * SQL text
   *
   * @var string|DbSqlStatement
   */
  public $query = '';
  /**
   * Array of used params :param => value
   * Each param would bind with bind_param() function
   * Used for DML part of query
   *
   * @var array $values
   */
  public $values = array();
  /**
   * Array of used placeholders |param => value
   * Each placeholder would be placed into query with conversion via (string)
   * Used for dynamic queries creation for DDL part
   *
   * @var array $placeholders
   */
  // TODO - make use of it
  public $placeholders = array();

  /**
   * Comment for query
   * Should be quoted with SQL comment quote
   * Will be placed after query itself. Used mainly for debug purposes (should be disabled on production servers)
   *
   * @var string
   */
  // TODO - disable comments in SQL via game config
  public $comment = '';

  // Prepared values for query execution
  public $queryPrepared = '';
  public $paramsPrepared = array();
  public $valuesPrepared = array();
  public $valueTypesPrepared = '';
  /**
   * @var mysqli_stmt $statement
   */
  public $statement;
  /**
   * Flag that params already bound
   * Setting values performed via updating $values property
   *
   * @var bool $isParamsBound
   */
  protected $isParamsBound = false;

//  /**
//   * @var ReflectionMethod
//   */
//  public static $bindParamMethod;

  /**
   * @var mysqli_stmt[]
   */
  protected static $statements = array();

  /**
   * DbSqlPrepare constructor.
   *
   * @param string $query
   * @param array  $values
   */
  public function __construct($query, $values = array()) {
//    if(empty(static::$bindParamMethod)) {
//      $ref = new ReflectionClass('mysqli_stmt');
//      static::$bindParamMethod = $ref->getMethod("bind_param");
//    }

    $this->query = trim($query);
    $this->values = $values;
  }

  /**
   * @param string $query
   * @param array  $values
   *
   * @return static
   *
   */
  public static function build($query, $values = array()) {
    return new static($query, $values);
  }


  public function setQuery($query) {
    $this->query = $query;

    return $this;
  }

  public function commentRemove() {
    if (!empty($this->values[static::COMMENT_PLACEHOLDER])) {
      unset($this->values[static::COMMENT_PLACEHOLDER]);
      $this->query = str_replace(' ' . static::COMMENT_PLACEHOLDER, '', $this->query);
    }
  }

  public function commentAdd($comment) {
    if (empty($this->values[static::COMMENT_PLACEHOLDER])) {
      $this->query .= ' ' . static::COMMENT_PLACEHOLDER;
    }
    $this->values[static::COMMENT_PLACEHOLDER] = $comment;
  }

  /**
   * @param string $comment
   */
  public function comment($comment) {
    if (empty($comment)) {
      $this->commentRemove();
    } else {
      $this->commentAdd($comment);
    }

    return $this;
  }


  // TODO - method to re-set and re-bind values


  public function __toString() {
//    $result = str_replace(array_keys($this->tables), $this->tables, $this->query);
    $result = str_replace(array_keys($this->values), $this->values, $this->query);

    return $result;
  }

  public function compileMySqlI() {
    $this->queryPrepared = $this->query;
    $this->paramsPrepared = array();
    $this->valuesPrepared = array();
    $this->valueTypesPrepared = '';

    if ($variableCount = preg_match_all(self::PARAM_PREG, $this->query, $matches, PREG_PATTERN_ORDER)) {
      $this->paramsPrepared = $matches[0];
      if (in_array(static::COMMENT_PLACEHOLDER, $this->paramsPrepared)) {
        // Removing comment placeholder from statement
        $this->queryPrepared = str_replace(static::COMMENT_PLACEHOLDER, $this->comment, $this->queryPrepared);
        // Removing comment value from values list
        $this->paramsPrepared = array_filter($this->paramsPrepared, function ($value) { return $value != DbSqlPrepare::COMMENT_PLACEHOLDER; });
        // TODO - Add comment value directly to statement
      }

      // Replacing actual param names with '?' - for mysqli_prepare
      $this->queryPrepared = preg_replace(self::PARAM_PREG, '?', $this->queryPrepared);

      // Now filling found params with it values
      // We can't use param names as keys 'cause same param can be met twice
      // So one key would overwrite another and total number of valuesUsed will be less then actual values used
      // TODO - move out of this proc to separate method to allow rebind of params
      foreach ($this->paramsPrepared as $key => &$value) {
        if (!key_exists($value, $this->values)) {
          // Throw exception if not key found in statement values list
          throw new Exception('DbSqlPrepare::compileMySqlI() - values array has no match for statement params');
        }

        // Reference need for call mysqli::bind_param later in bindParam() method
        $this->valuesPrepared[$key] = &$this->values[$value];

        // TODO - move out of this proc to separate method and own loop to allow rebind of params
        // i corresponding variable has type integer
        // d corresponding variable has type double
        // s corresponding variable has type string
        // b corresponding variable is a blob and will be sent in packets
        if (is_int($this->values[$value])) {
          $this->valueTypesPrepared .= 'i';
        } elseif (is_double($this->values[$value])) {
          $this->valueTypesPrepared .= 'd';
        } else {
          $this->valueTypesPrepared .= 's';
        }
      }
    }

    return $this;
  }


  /**
   * @internal param mysqli_stmt $mysqli_stmt
   */
  protected function bindParams() {
    if (count($this->valuesPrepared)) {
      $params = array_merge(array(&$this->valueTypesPrepared), $this->valuesPrepared);
      // static::$bindParamMethod->invokeArgs($this->statement, $params);
      call_user_func_array(array($this->statement, 'bind_param'), $params);
    }
  }

  /**
   * @param db_mysql $db
   *
   * @return DbSqlPrepare
   * @throws Exception
   */
  public function statementGet($db) {
    // TODO - к этому моменту плейсхолдеры под DDL уже должны быть заполнены соответствующими значениями
    // Надо вынести собственно prepared statement в отдельный объект, что бы здесь остались только манипуляции с полями
    $md5 = md5($this->queryPrepared);

    if (empty(static::$statements[$md5])) {
      if (!(static::$statements[$md5] = $db->db_prepare($this->queryPrepared))) {
        throw new Exception('DbSqlPrepare::statementGet() - can not prepare statement');
      }
      $this->statement = static::$statements[$md5];
      $this->bindParams();
    } else {
      // TODO - вот тут фигня. На самом деле нельзя под один и тот же DbSqlPrepare исползовать разные mysqli_stmt
      // С другой стороны - это позволяет реюзать параметры. Так что еще вопрос - фигня ли это....
      $this->statement = static::$statements[$md5];
    }

    return $this;
  }

  /**
   * @return $this
   */
  public function execute() {
    $this->statement->execute();

    return $this;
  }

  public function getResult() {
    return $this->statement->get_result();
  }

}
