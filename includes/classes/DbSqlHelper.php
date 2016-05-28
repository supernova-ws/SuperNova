<?php

class DbSqlHelper {

  public static function UCFirstByRef(&$value) {
    $value = ucfirst($value);
  }

  /**
   * Quotes comment with SQL comment statement
   * Make checks for comment limiters in comment
   *
   * @param $comment
   *
   * @return string
   */
  public static function quoteComment($comment) {
    if($comment == '') {
      return '';
    }

    $comment = str_replace(array('/*', '*/'), '__',$comment);

    return "\r\n/*" . $comment . "*/";
  }

}
