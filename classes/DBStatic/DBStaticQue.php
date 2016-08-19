<?php

namespace DBStatic;
use classSupernova;
use mysqli_result;

class DBStaticQue {

  /*
   * С $for_update === true эта функция должна вызываться только из транзакции! Все соответствующие записи в users и planets должны быть уже блокированы!
   *
   * $que_type
   *   !$que_type - все очереди
   *   QUE_XXXXXX - конкретная очередь по планете
   * $user_id - ID пользователя
   * $planet_id
   *   $que_type == QUE_RESEARCH - игнорируется
   *   null - обработка очередей планет не производится
   *   false/0 - обрабатываются очереди всех планет по $user_id
   *   (integer) - обрабатываются локальные очереди для планеты. Нужно, например, в обработчике флотов
   *   иначе - $que_type для указанной планеты
   * $for_update - true == нужно блокировать записи
   *
   * TODO Работа при !$user_id
   * TODO Переформатировать вывод данных, что бы можно было возвращать данные по всем планетам и юзерам в одном запросе: добавить подмассивы 'que', 'planets', 'players'
   *
   */
  public static function db_que_list_by_type_location($user_id, $planet_id = null, $que_type = false, $for_update = false) {
    if (!$user_id) {
      pdump(debug_backtrace());
      die('No user_id for que_get_que()');
    }

    $ques = array();

    $query = array();

    if ($user_id = idval($user_id)) {
      $query[] = "`que_player_id` = {$user_id}";
    }

    if ($que_type == QUE_RESEARCH || $planet_id === null) {
      $query[] = "`que_planet_id` IS NULL";
    } elseif ($planet_id) {
      $query[] = "(`que_planet_id` = {$planet_id}" . ($que_type ? '' : ' OR que_planet_id IS NULL') . ")";
    }
    if ($que_type) {
      $query[] = "`que_type` = {$que_type}";
    }

    $ques['items'] = classSupernova::$gc->cacheOperator->db_get_record_list(LOC_QUE, implode(' AND ', $query));

    return que_recalculate($ques);
  }


  public static function db_que_list_stat() {
    return classSupernova::$db->doSelect("SELECT que_player_id, sum(que_unit_amount) AS que_unit_amount, que_unit_price FROM `{{que}}` GROUP BY que_player_id, que_unit_price;");
  }

  public static function db_que_set_time_left_by_id($que_id, $que_time_left) {
    return classSupernova::$gc->cacheOperator->db_upd_record_by_id(
      LOC_QUE,
      $que_id,
      array(
        'que_time_left' => $que_time_left,
      ),
      array()
    );
  }

  /**
   * @param array $set
   *
   * @return array|bool|false|mysqli_result|null
   */
  public static function db_que_set_insert($set) {
    return classSupernova::$gc->cacheOperator->db_ins_record(LOC_QUE, $set);
  }

  public static function db_que_delete_by_id($que_id) {
    return classSupernova::$gc->cacheOperator->db_del_record_by_id(LOC_QUE, $que_id);
  }

  public static function db_que_planet_change_owner($planet_id, $new_owner_id) {
    return classSupernova::$db->doUpdateTableSet(
      TABLE_QUE,
      array(
        'que_player_id' => $new_owner_id,
      ),
      array(
        'que_planet_id' => $planet_id,
      )
    );
  }

  public static function db_que_research_change_origin($planet_id, $new_planet_id) {
    return classSupernova::$db->doUpdateTableSet(
      TABLE_QUE,
      array(
        'que_planet_id_origin' => $new_planet_id,
      ),
      array(
        'que_planet_id_origin' => $planet_id,
      )
    );
  }

}