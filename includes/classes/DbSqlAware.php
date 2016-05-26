<?php

class DbSqlAware {
  /**
   * @var db_mysql $db
   */
  protected $db;

  /**
   * DbSqlAware constructor.
   *
   * @param db_mysql|null $db
   */
  public function __construct($db = null) {
    $this->db = (!empty($db) && $db instanceof db_mysql) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
  }

  /**
   * @param db_mysql|null $db
   *
   * @return static
   */
  public static function build($db = null) {
    return new static($db);
  }

  /**
   * Just escapes string used DB string escape method - or a drop-in replacement addslashes() if not DB specified
   *
   * @param string $string
   *
   * @return mixed|string
   */
  protected function escapeString($string) {
    return
      method_exists($this->db, 'db_escape')
        ? $this->db->db_escape($string)
        : addslashes($string);
  }

  /**
   * Quotes string by ref as DDL ID (i.e. field or table name) - except '*' which not quoted
   *
   * @param string &$string
   */
  protected function quoteFieldSimpleByRef(&$string) {
    $string = $this->escapeString($string);
    if ((string)$string && '*' != $string) {
      $string = '`' . $string . '`';
    }
  }

  /**
   * Escapes field - include fully qualified field like table.field, table.* and mask '*'
   *
   * @param string $string
   *
   * @return string
   */
  protected function quoteField($string) {
    // Checking if string is just a '*' - to skip some code
    if ($string != '*') {
      $temp = explode('.', $string);
      array_walk($temp, array($this, 'quoteFieldSimpleByRef'));
      $string = implode('.', $temp);
    }

    return $string;
  }

  /**
   * Making and alias from fully qualified field name prepending with function name
   *
   * @param string $functionName
   * @param string $field
   *
   * @return string
   */
  protected function aliasFromField($functionName, $field) {
    $alias = strtolower($functionName);

    // Checking if string is just a '*' - to skip some code
    if ($field != '*') {
      $temp = explode('.', $field);
      array_walk($temp, 'DbSqlHelper::UCFirstByRef');
      if (!empty($temp[1]) && $temp[1] == '*') {
        unset($temp[1]);
      }
      $temp = implode('', $temp);
      $alias .= ucfirst($temp);
    } else {
      $alias .= 'Value';
    }

    return $alias;
  }

}
