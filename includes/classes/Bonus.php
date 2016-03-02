<?php

/**
 * Class Bonus
 */
class Bonus {

  protected static $_bonus_group = array();

  protected $grants = array();
  protected $recieves = array();

  public static function _init() {
    static::$_bonus_group = sn_get_groups(P_BONUS_VALUE);
    empty(static::$_bonus_group) ? static::$_bonus_group = array() : false;
  }

}

Bonus::_init();
