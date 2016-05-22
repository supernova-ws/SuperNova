<?php

/**
 * Class DbSqlPrepare
 */
class DbSqlPrepare {
  const COMMENT_PLACEHOLDER = ':__COMMENT__';
  const PARAM_PREG = '#(\:.+?\b)#im';

  public $query = '';
  public $values = array();

  public $comment = '';

  public $queryPrepared = '';
  public $paramsPrepared = array();
  public $valuesPrepared = array();
  public $valueTypesPrepared = '';
  /**
   * @var mysqli_stmt $statement
   */
  public $statement;

//  /**
//   * @var ReflectionMethod
//   */
//  public static $bindParamMethod;

  /**
   * @var self[]
   */
  protected static $statements = array();

  /**
   * DbSqlPrepare constructor.
   *
   * @param string $statement
   * @param array  $values
   */
  public function __construct($statement, $values = array()) {
//    if(empty(static::$bindParamMethod)) {
//      $ref = new ReflectionClass('mysqli_stmt');
//      static::$bindParamMethod = $ref->getMethod("bind_param");
//    }

    $this->query = trim($statement);
    $this->values = $values;
  }

  /**
   * @param string $statement
   * @param array  $values
   *
   * @return static
   *
   */
  public static function build($statement, $values = array()) {
    return new static($statement, $values);
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

  public function __toString() {
//    $result = str_replace(array_keys($this->tables), $this->tables, $this->query);
    $result = str_replace(array_keys($this->values), $this->values, $this->query);

    return $result;
  }

  public function compileMySqlI() {
//    print($this->statement);

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

//      pdump($this->valuesUsed, '$this->valuesUsed0');

      // Using $matches array as keys for used values array
//     $this->valuesUsed = array_combine($matches, array_fill(0, count($matches), null));

      // Now filling found parms with it values
      // We can't use param names as keys 'cause same param can be met twice
      // So one key would overwrite another and total number of valuesUsed will be less then actual values used
      foreach ($this->paramsPrepared as $key => &$value) {
        if (!key_exists($value, $this->values)) {
          // Throw exception if not key found in statement values list
          throw new Exception('DbSqlPrepare::compile() - values array has no match for statement params');
        }
//        $value = array(
//          'param' => $value,
//          'value' => $this->values[$value],
//        );
        // Reference need for call mysqli::bind_param later in execute() method
        $this->valuesPrepared[$key] = &$this->values[$value];

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


//      foreach ($this->valuesUsed as $key => &$value) {
//        if (!key_exists($key, $this->values)) {
//          // Throw exception if not key found in statement values list
//          throw new Exception('DbSqlPrepare::compile() - values array has no match for statement params');
//        }
//        $value = $this->values[$key];
//      }
//


//      pdump($matches, '$matches0');
//      $this->valuesUsed = array_intersect_key($this->values, $matches);
//      pdump($matches, '$matches2 ');

      // Replacing actual param names with '?' - for mysqli_prepare
      $this->queryPrepared = preg_replace(self::PARAM_PREG, '?', $this->queryPrepared);

//      pdump($this->paramsPrepared, '$this->paramsUsed');
//      pdump($this->valuesPrepared, '$this->valuesPrepared');
//      pdump($this->valueTypesPrepared, '$valueTypes');
//
//      print($this->queryPrepared);
//      print($questioned);
    };

//    pdie();

    return $this;
  }

  public function getResult() {
    return $this->statement->get_result();
  }

  /**
   * @param db_mysql $db
   *
   * @return DbSqlPrepare
   * @throws Exception
   */
  public function statementGet($db) {
    $md5 = md5($this->queryPrepared);

    if (empty(static::$statements[$md5])) {
      if (!(static::$statements[$md5] = $db->db_prepare($this->queryPrepared))) {
        throw new Exception('doQueryPhase1 - can not prepare statement');
      }
      if (count($this->valuesPrepared)) {
        $params = array_merge(array(&$this->valueTypesPrepared), $this->valuesPrepared);
        // static::$bindParamMethod->invokeArgs($this->statement, $params);
        call_user_func_array(array(static::$statements[$md5], 'bind_param'), $params);
      }
    }
    $this->statement = static::$statements[$md5];

    return $this;
  }

  /**
   * @return $this
   */
  public function execute() {
//pdump($this->valuesPrepared);
//    if (count($this->valuesPrepared)) {
//      $params = array_merge(array(&$this->valueTypesPrepared), $this->valuesPrepared);
////      static::$bindParamMethod->invokeArgs($this->statement, $params);
//      call_user_func_array(array($this->statement, 'bind_param'), $params);
//    }

    $this->statement->execute();

    return $this;
  }

}
