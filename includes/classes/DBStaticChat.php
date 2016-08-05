<?php

class DBStaticChat {

  // Chat *************************************************************************************************************
  public static function db_chat_player_list_online($chat_refresh_rate, $ally_add) {
    $sql_date = SN_TIME_NOW - $chat_refresh_rate * 2;

    return classSupernova::$db->doSelect(
      "SELECT u.*, cp.*
    FROM {{chat_player}} AS cp
      JOIN {{users}} AS u ON u.id = cp.chat_player_player_id
    WHERE
      `chat_player_refresh_last` >= '{$sql_date}'
      AND (`banaday` IS NULL OR `banaday` <= " . SN_TIME_NOW . ")
      {$ally_add}
    ORDER BY authlevel DESC, `username`");
  }

  /**
   * @param $user_id
   * @param $nickUnsafe
   * @param $ally_id
   * @param $message_unsafe
   * @param $chat_message_sender_name_unsafe
   * @param $chat_message_recipient_id
   * @param $chat_message_recipient_name_unsafe
   */
  public static function db_chat_message_insert($user_id, $nickUnsafe, $ally_id, $message_unsafe, $chat_message_sender_name_unsafe = '', $chat_message_recipient_id = 0, $chat_message_recipient_name_unsafe = '') {
    classSupernova::$db->doInsertSet(TABLE_CHAT, array(
      'chat_message_sender_id'      => $user_id,
      'user'                        => $nickUnsafe,
      'ally_id'                     => $ally_id,
      'message'                     => $message_unsafe,
      'timestamp'                   => SN_TIME_NOW,
      'chat_message_sender_name'    => $chat_message_sender_name_unsafe,
      'chat_message_recipient_id'   => $chat_message_recipient_id,
      'chat_message_recipient_name' => $chat_message_recipient_name_unsafe,
    ));
  }

  /**
   * @param $alliance
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_message_count_by_ally($alliance) {
    $rows = classSupernova::$db->doSelectFetch("SELECT count(1) AS CNT FROM {{chat}} WHERE ally_id = '{$alliance}';");

    return $rows;
  }

  /**
   * @param $alliance
   * @param $where_add
   * @param $start_row
   * @param $page_limit
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_message_get_page($alliance, $where_add, $start_row, $page_limit) {
    $query = classSupernova::$db->doSelect(
      "SELECT c.*, u.authlevel
      FROM
        {{chat}} AS c
        LEFT JOIN {{users}} AS u ON u.id = c.chat_message_sender_id
      WHERE c.chat_message_recipient_id IS NULL AND c.ally_id = '{$alliance}' {$where_add} ORDER BY messageid DESC LIMIT {$start_row}, {$page_limit};");

    return $query;
  }

  /**
   * @param $chat_directive
   * @param $userId
   */
  public static function db_chat_player_update_invisibility($chat_directive, $userId) {
    classSupernova::$db->doUpdateRowWhere(
      TABLE_CHAT_PLAYER,
      array(
        'chat_player_invisible' => $chat_directive,
      ),
      array(
        'chat_player_player_id' => $userId,
      )
    );
  }

  /**
   * @param $reasonUnsafe
   * @param $chat_player_subject_id
   */
  public static function db_chat_player_update_unmute($reasonUnsafe, $chat_player_subject_id) {
    classSupernova::$db->doUpdateRowWhere(
      TABLE_CHAT_PLAYER,
      array(
        'chat_player_muted'       => 0,
        'chat_player_mute_reason' => $reasonUnsafe,
      ),
      array(
        'chat_player_player_id' => $chat_player_subject_id,
      )
    );
  }

  /**
   * @param $date_compiled
   * @param $reasonUnsafe
   * @param $chat_player_subject_id
   */
  public static function db_chat_player_update_mute($date_compiled, $reasonUnsafe, $chat_player_subject_id) {
    classSupernova::$db->doUpdateRowWhere(
      TABLE_CHAT_PLAYER,
      array(
        'chat_player_muted'       => $date_compiled,
        'chat_player_mute_reason' => $reasonUnsafe,
      ),
      array(
        'chat_player_player_id' => $chat_player_subject_id,
      )
    );
  }

  /**
   * @param $delete
   */
  public static function db_chat_message_delete($delete) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_CHAT, array('messageid' => $delete));
  }

  public static function db_chat_message_purge() {
    classSupernova::$db->doDeleteComplex("DELETE FROM `{{chat}}`;");
  }

  /**
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_message_get_last_25() {
    $query = classSupernova::$db->doSelect("SELECT * FROM `{{chat}}` ORDER BY messageid DESC LIMIT 25;");

    return $query;
  }


  /**
   * @param $player_id
   * @param $fields
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_player_get($player_id, $fields) {
    return classSupernova::$db->doSelectFetch("SELECT {$fields} FROM {{chat_player}} WHERE `chat_player_player_id` = {$player_id} LIMIT 1");
  }


  /**
   * @param $player_id
   */
  public static function db_chat_player_insert($player_id) {
    classSupernova::$db->doInsertSet(TABLE_CHAT_PLAYER, array(
      'chat_player_player_id' => $player_id,
    ));
  }


  /**
   * @param $userId
   */
  public static function db_chat_player_update($userId) {
    classSupernova::$db->doUpdateRowWhere(
      TABLE_CHAT_PLAYER,
      array(
        'chat_player_refresh_last' => SN_TIME_NOW,
      ),
      array(
        'chat_player_player_id' => $userId,
      )
    );
  }


  /**
   * @param $alliance
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_list_select_advanced($alliance, $user) {
    $rows = classSupernova::$db->doSelectFetch("SELECT count(1) AS CNT
          FROM {{chat}}
          WHERE
          (
            (ally_id = '{$alliance}' AND `chat_message_recipient_id` IS NULL) OR
            (ally_id = 0 AND `chat_message_recipient_id` = {$user['id']}) OR
            (ally_id = 0 AND `chat_message_sender_id` = {$user['id']} AND `chat_message_recipient_id` IS NOT NULL) OR
            (ally_id = 0 AND `chat_message_sender_id` IS NULL AND `chat_message_recipient_id` IS NULL)
          )
        ");

    return $rows;
  }


  /**
   * @param $alliance
   * @param $user
   * @param $where_add
   * @param $start_row
   * @param $page_limit
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_list_get_with_users($alliance, $user, $where_add, $start_row, $page_limit) {
    $query = classSupernova::$db->doSelect(
      "SELECT c.*, u.authlevel
        FROM
          {{chat}} AS c
          LEFT JOIN {{users}} AS u ON u.id = c.chat_message_sender_id
        WHERE
          (
            (c.ally_id = '{$alliance}' AND `chat_message_recipient_id` IS NULL) OR
            (c.ally_id = 0 AND `chat_message_recipient_id` = {$user['id']}) OR
            (c.ally_id = 0 AND `chat_message_sender_id` = {$user['id']} AND `chat_message_recipient_id` IS NOT NULL) OR
            (c.ally_id = 0 AND `chat_message_sender_id` IS NULL AND `chat_message_recipient_id` IS NULL)
          )
          {$where_add}
        ORDER BY messageid DESC
        LIMIT {$start_row}, {$page_limit}");

    return $query;
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_player_select_id($user) {
    $activity_row = classSupernova::$db->doSelectFetch("SELECT `chat_player_id` FROM {{chat_player}} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");

    return $activity_row;
  }


  /**
   * @param $userId
   */
  public static function db_chat_player_update_activity($userId) {
    classSupernova::$db->doUpdateRowWhere(
      TABLE_CHAT_PLAYER,
      array(
        'chat_player_activity' => SN_TIME_SQL,
      ),
      array(
        'chat_player_player_id' => $userId,
      )
    );
  }

}