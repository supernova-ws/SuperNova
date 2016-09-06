<?php

namespace DBStatic;
use classSupernova;
use mysqli_result;

class DBStaticFleetBashing {

  /**
   * @param $user
   * @param $planet_dst
   * @param $time_limit
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_bashing_list_get($user, $planet_dst, $time_limit) {
    $query = classSupernova::$db->doSelect("SELECT bashing_time FROM {{bashing}} WHERE bashing_user_id = {$user['id']} AND bashing_planet_id = {$planet_dst['id']} AND bashing_time >= {$time_limit};");

    return $query;
  }


  /**
   * @param $bashing_list
   */
  public static function db_bashing_insert($bashing_list) {
    classSupernova::$db->doInsertValues(TABLE_BASHING, $bashing_list, array('bashing_user_id', 'bashing_planet_id', 'bashing_time'));
  }

}
