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
   * @param string $string
   *
   * @return mixed|string
   */
  protected function stringEscape($string) {
    return
      method_exists($this->db, 'db_escape')
        ? $this->db->db_escape($string)
        : str_replace('`', '\`', addslashes($string));
  }

  /**
   * Quotes string by ref as field - except '*'
   *
   * @param string &$string
   */
  protected function quoteStringAsFieldByRef(&$string) {
    $string = $this->stringEscape($string);
    if ((string)$string && '*' != $string) {
      $string = '`' . $string . '`';
    }
  }

  /**
   * Escapes field
   *
   * @param string $string
   *
   * @return string
   */
  protected function makeFieldFromString($string) {
    // Checking if string is just a '*' - to skip some code
    if ($string != '*') {
      $temp = explode('.', $string);
      array_walk($temp, array($this, 'quoteStringAsFieldByRef'));
      $string = implode('.', $temp);
    }

    return $string;
  }

  /**
   * @param string $functionName
   * @param string $field
   *
   * @return string
   */
  protected function makeAliasFromField($functionName, $field) {
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
