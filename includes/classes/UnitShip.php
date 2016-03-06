<?php

/**
 * Class UnitShip
 */
class UnitShip extends Unit {

  /**
   * @var string
   */
  protected static $_sn_group_name = 'fleet';
  /**
   * @var array
   */
  protected static $_group_unit_id_list = array();

}

UnitShip::_init();
