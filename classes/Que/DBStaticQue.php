<?php

namespace Que;
use SN;

class DBStaticQue {

  public static function db_que_list_by_type_location($user_id, $planet_id = null, $que_type = false, $for_update = false) {
    return SN::db_que_list_by_type_location($user_id, $planet_id, $que_type, $for_update);
  }

  public static function db_que_list_stat() {
    return doquery("SELECT que_player_id, sum(que_unit_amount) AS que_unit_amount, que_unit_price FROM `{{que}}` GROUP BY que_player_id, que_unit_price;");
  }

  public static function db_que_set_time_left_by_id($que_id, $que_time_left) {
    return SN::db_upd_record_by_id(LOC_QUE, $que_id, "`que_time_left` = {$que_time_left}");
  }

  public static function db_que_set_insert($set) {
    return SN::db_ins_record(LOC_QUE, $set);
  }

  public static function db_que_delete_by_id($que_id) {
    return SN::db_del_record_by_id(LOC_QUE, $que_id);
  }

  public static function db_que_planet_change_owner($planet_id, $new_owner_id) {
    return doquery("UPDATE {{que}} SET `que_player_id` = {$new_owner_id} WHERE `que_planet_id` = {$planet_id}");
  }

  public static function db_que_research_change_origin($planet_id, $new_planet_id) {
    return doquery("UPDATE {{que}} SET `que_planet_id_origin` = {$new_planet_id} WHERE `que_planet_id_origin` = {$planet_id}");
  }

}