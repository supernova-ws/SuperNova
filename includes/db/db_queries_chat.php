<?php

// Chat *************************************************************************************************************
function db_chat_player_list_online($chat_refresh_rate, $ally_add) {
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
function db_chat_message_insert($user_id, $nick, $ally_id, $message, $chat_message_sender_name = '', $chat_message_recipient_id = 0, $chat_message_recipient_name = '') {
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
function db_chat_message_count_by_ally($alliance) {
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
function db_chat_message_get_page($alliance, $where_add, $start_row, $page_limit) {
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
function db_chat_player_update_invisibility($chat_directive, $user) {
  doquery("UPDATE {{chat_player}} SET `chat_player_invisible` = {$chat_directive} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");
}

/**
 * @param $temp
 * @param $chat_player_subject
 */
function db_chat_player_update_unmute($temp, $chat_player_subject) {
  doquery("UPDATE {{chat_player}} SET `chat_player_muted` = 0, `chat_player_mute_reason` = '{$temp}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
}

/**
 * @param $date_compiled
 * @param $chat_command_parsed_two
 * @param $chat_player_subject
 */
function db_chat_player_update_mute($date_compiled, $chat_command_parsed_two, $chat_player_subject) {
  doquery("UPDATE {{chat_player}} SET `chat_player_muted` = {$date_compiled}, `chat_player_mute_reason` = '{$chat_command_parsed_two[4]}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
}

/**
 * @param $delete
 */
function db_chat_message_delete($delete) {
  doquery("DELETE FROM {{chat}} WHERE `messageid`={$delete};");
}

function db_chat_message_purge() {
  doquery("DELETE FROM `{{chat}}`;");
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_chat_message_get_last_25() {
  $query = doquery("SELECT * FROM `{{chat}}` ORDER BY messageid DESC LIMIT 25;");

  return $query;
}
