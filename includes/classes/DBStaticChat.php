<?php

class DBStaticChat {

  // Chat *************************************************************************************************************
  public static function db_chat_player_list_online($chat_refresh_rate, $ally_add) {
    $sql_date = SN_TIME_NOW - $chat_refresh_rate * 2;

    return doquery(
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
   * @param $nick
   * @param $ally_id
   * @param $message
   * @param $chat_message_sender_name
   * @param $chat_message_recipient_id
   * @param $chat_message_recipient_name
   */
  public static function db_chat_message_insert($user_id, $nick, $ally_id, $message, $chat_message_sender_name = '', $chat_message_recipient_id = 0, $chat_message_recipient_name = '') {
    doquery(
      "INSERT INTO
          {{chat}}
        SET
          `chat_message_sender_id` = {$user_id},
          `user` = '{$nick}',
          `ally_id` = '{$ally_id}',
          `message` = '{$message}',
          `timestamp` = " . SN_TIME_NOW . ",
          `chat_message_sender_name` = '{$chat_message_sender_name}',
          `chat_message_recipient_id` = {$chat_message_recipient_id},
          `chat_message_recipient_name` = '{$chat_message_recipient_name}'"
    );
  }

  /**
   * @param $alliance
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_message_count_by_ally($alliance) {
    $rows = doquery("SELECT count(1) AS CNT FROM {{chat}} WHERE ally_id = '{$alliance}';", true);

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
    $query = doquery(
      "SELECT c.*, u.authlevel
      FROM
        {{chat}} AS c
        LEFT JOIN {{users}} AS u ON u.id = c.chat_message_sender_id
      WHERE c.chat_message_recipient_id IS NULL AND c.ally_id = '{$alliance}' {$where_add} ORDER BY messageid DESC LIMIT {$start_row}, {$page_limit};");

    return $query;
  }

  /**
   * @param $chat_directive
   * @param $user
   */
  public static function db_chat_player_update_invisibility($chat_directive, $user) {
    doquery("UPDATE {{chat_player}} SET `chat_player_invisible` = {$chat_directive} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");
  }

  /**
   * @param $temp
   * @param $chat_player_subject
   */
  public static function db_chat_player_update_unmute($temp, $chat_player_subject) {
    doquery("UPDATE {{chat_player}} SET `chat_player_muted` = 0, `chat_player_mute_reason` = '{$temp}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
  }

  /**
   * @param $date_compiled
   * @param $chat_command_parsed_two
   * @param $chat_player_subject
   */
  public static function db_chat_player_update_mute($date_compiled, $chat_command_parsed_two, $chat_player_subject) {
    doquery("UPDATE {{chat_player}} SET `chat_player_muted` = {$date_compiled}, `chat_player_mute_reason` = '{$chat_command_parsed_two[4]}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
  }

  /**
   * @param $delete
   */
  public static function db_chat_message_delete($delete) {
    classSupernova::$db->doDelete("DELETE FROM {{chat}} WHERE `messageid`={$delete};");
  }

  public static function db_chat_message_purge() {
    classSupernova::$db->doDelete("DELETE FROM `{{chat}}`;");
  }

  /**
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_message_get_last_25() {
    $query = doquery("SELECT * FROM `{{chat}}` ORDER BY messageid DESC LIMIT 25;");

    return $query;
  }


  /**
   * @param $player_id
   * @param $fields
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_player_get($player_id, $fields) {
    return doquery("SELECT {$fields} FROM {{chat_player}} WHERE `chat_player_player_id` = {$player_id} LIMIT 1", true);
  }


  /**
   * @param $player_id
   */
  public static function db_chat_player_insert($player_id) {
    doquery("INSERT INTO {{chat_player}} SET `chat_player_player_id` = {$player_id}");
  }


  /**
   * @param $user
   */
  public static function db_chat_player_update($user) {
    doquery("UPDATE {{chat_player}} SET `chat_player_refresh_last` = " . SN_TIME_NOW . " WHERE `chat_player_player_id` = {$user['id']} LIMIT 1;");
  }


  /**
   * @param $alliance
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_chat_list_select_advanced($alliance, $user) {
    $rows = doquery("SELECT count(1) AS CNT
          FROM {{chat}}
          WHERE
          (
            (ally_id = '{$alliance}' AND `chat_message_recipient_id` IS NULL) OR
            (ally_id = 0 AND `chat_message_recipient_id` = {$user['id']}) OR
            (ally_id = 0 AND `chat_message_sender_id` = {$user['id']} AND `chat_message_recipient_id` IS NOT NULL) OR
            (ally_id = 0 AND `chat_message_sender_id` IS NULL AND `chat_message_recipient_id` IS NULL)
          )
        ", true);

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
    $query = doquery(
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
    $activity_row = doquery("SELECT `chat_player_id` FROM {{chat_player}} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1", true);

    return $activity_row;
  }


  /**
   * @param $user
   */
  public static function db_chat_player_update_activity($user) {
    doquery("UPDATE {{chat_player}} SET `chat_player_activity` = '" . classSupernova::$db->db_escape(SN_TIME_SQL) . "' WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");
  }

}