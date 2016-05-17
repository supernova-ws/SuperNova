<?php

class DbSqlLiteral {

  public $function = '';

  public function __construct($function = '') {
    $this->function = $function;
  }

  public function __toString() {
    return $this->function;
  }

}
