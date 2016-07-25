<?php

/**
 * Class DbSqlLiteral
 */
class DbSqlLiteral extends DbSqlAware {

  const SQL_LITERAL_ALIAS_NONE = null;
  const SQL_LITERAL_ALIAS_AUTO = '';

  /**
   * ALWAYS STRING!
   *
   * @var string $literal
   */
  public $literal = '';

  /**
   * DbSqlLiteral constructor.
   *
   * @param db_mysql|null $db
   * @param string        $literal
   */
  public function __construct($db = null, $literal = '') {
    parent::__construct($db);
  }

  /**
   * @param mixed $value
   *
   * @return $this
   */
  public function literal($value) {
    $this->literal = (string)$value;

    return $this;
  }
//
//  public static function __callStatic($name, $arguments) {
//    // method_exists(get_called_class(), $name)
//    return call_user_func_array(array(static::build(), '_' .$name), $arguments);
//  }

  /**
   * Renders single argument function
   * - supports autobuild alias
   * - supports direct alias
   * - supports dotted field names like 'table.field'
   * - supports all fields with '*' and 'table.*'
   *
   * @param string      $functionName
   * @param string      $field
   *  - '*' - all fields
   *  - string - quoted as field name
   * @param null|string $alias
   * - DbSqlLiteral::SQL_LITERAL_ALIAS_NONE: no alias
   * - DbSqlLiteral::SQL_LITERAL_ALIAS_AUTO: alias from $field
   * - other value: using supplied alias
   *
   * @return $this
   */
  public function buildSingleArgument($functionName, $field = '*', $alias = self::SQL_LITERAL_ALIAS_NONE) {
    if ($alias === self::SQL_LITERAL_ALIAS_AUTO) {
      $alias = $this->aliasFromField($functionName, $field);
    }

    $this->literal = strtoupper($functionName) . '(' . $this->quoteField($field) . ')';

    if (self::SQL_LITERAL_ALIAS_NONE !== $alias && !empty($alias)) {
      $this->literal .= ' AS `' . $alias . '`';
    }

    return $this;
  }
  /**
   * @param string      $field
   * @param null|string $alias
   *
   * @return static
   *
   * @see buildSingleArgument
   */
  public function max($field = '*', $alias = self::SQL_LITERAL_ALIAS_NONE) {
    return $this->buildSingleArgument('max', $field, $alias);
  }

  /**
   * @param string      $field
   * @param null|string $alias
   *
   * @return $this
   *
   * @see buildSingleArgument
   */
  public function count($field = '*', $alias = self::SQL_LITERAL_ALIAS_NONE) {
    return $this->buildSingleArgument('count', $field, $alias);
  }

  /**
   * @param string      $field
   * @param null|string $alias
   *
   * @return $this
   *
   * @see buildSingleArgument
   */
  public function sum($field = '*', $alias = self::SQL_LITERAL_ALIAS_NONE) {
    return $this->buildSingleArgument('sum', $field, $alias);
  }

  /**
   * @param string      $field
   * @param null|string $alias
   *
   * @return $this
   *
   * @see buildSingleArgument
   */
  public function isNull($field = '*', $alias = self::SQL_LITERAL_ALIAS_NONE) {
    $functionName = 'isNull';

    if ($alias === self::SQL_LITERAL_ALIAS_AUTO) {
      $alias = $this->aliasFromField($functionName, $field);
    }

    $this->literal = $this->quoteField($field) . ' IS NULL';

    if (!empty($alias)) {
      $this->literal .= ' AS `' . $alias . '`';
    }

    return $this;
  }


  /**
   * @return string
   */
  public function __toString() {
    return $this->literal;
  }

}
