<?php
/**
 * Created by Gorlum 24.03.2018 19:45
 */

namespace Que;

use Planet\DBStaticPlanet;

class QueUnitStatic {

  /**
   * @param array $prevSqlBlock
   * @param int   $unit_id
   * @param array $user
   * @param array $planet
   * @param array $build_data
   * @param int   $unit_level
   * @param int   $unit_amount
   * @param int   $build_mode
   *
   * @return array
   */
  public static function que_unit_make_sql($prevSqlBlock = [], $unit_id, $user = array(), $planet = array(), $build_data, $unit_level = 0, $unit_amount = 1, $build_mode = BUILD_CREATE) {
    // TODO Унифицировать проверки

    // TODO que_process() тут

    sn_db_transaction_check(true);

    $build_mode = $build_mode == BUILD_CREATE ? BUILD_CREATE : BUILD_DESTROY;

    // TODO: Some checks
    db_change_units($user, $planet, array(
      RES_METAL     => -$build_data[$build_mode][RES_METAL] * $unit_amount,
      RES_CRYSTAL   => -$build_data[$build_mode][RES_CRYSTAL] * $unit_amount,
      RES_DEUTERIUM => -$build_data[$build_mode][RES_DEUTERIUM] * $unit_amount,
    ));

    $que_type = que_get_unit_que($unit_id);
    $planet_id_origin = $planet['id'] ? floatval($planet['id']) : null;
    $planet_id = $que_type == QUE_RESEARCH ? null : $planet_id_origin;
    if (is_numeric($planet_id)) {
      DBStaticPlanet::db_planet_set_by_id($planet_id, "`que_processed` = UNIX_TIMESTAMP(NOW())");
    } elseif (is_numeric($user['id'])) {
      db_user_set_by_id($user['id'], '`que_processed` = UNIX_TIMESTAMP(NOW())');
    }

    $resource_list = sys_unit_arr2str($build_data[$build_mode]);

    $result = [
      'que_player_id'        => $user['id'],
      'que_planet_id'        => $planet_id,
      'que_planet_id_origin' => $planet_id_origin,
      'que_type'             => $que_type,
      'que_time_left'        => $build_data[RES_TIME][$build_mode],
      'que_unit_id'          => $unit_id,
      'que_unit_amount'      => $unit_amount,
      'que_unit_mode'        => $build_mode,
      'que_unit_level'       => $unit_level,
      'que_unit_time'        => $build_data[RES_TIME][$build_mode],
      'que_unit_price'       => $resource_list,
      'que_unit_one_time_raw' => $build_data[P_OPTIONS][P_TIME_RAW],
    ];

    return $result;
  }

}
