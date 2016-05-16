<?php

class DBStaticMessages {

// Messages *************************************************************************************************************
  public static function db_message_list_get_last_20($user, $recipient_id) {
    return doquery(
      "SELECT * FROM {{messages}}
        WHERE
          `message_type` = '" . MSG_TYPE_PLAYER . "' AND
          ((`message_owner` = '{$user['id']}' AND `message_sender` = '{$recipient_id}')
          OR
          (`message_sender` = '{$user['id']}' AND `message_owner` = '{$recipient_id}')) ORDER BY `message_time` DESC LIMIT 20;");
  }

  public static function db_message_list_delete($user, $query_add) {
    doquery("DELETE FROM `{{messages}}` WHERE `message_owner` = '{$user['id']}'{$query_add};");
  }

  public static function db_message_list_outbox_by_user_id($user_id) {
    // return ($user_id = intval($user_id))
    return ($user_id = idval($user_id))
      ? doquery("SELECT {{messages}}.message_id, {{messages}}.message_owner, {{users}}.id AS message_sender, {{messages}}.message_time,
          {{messages}}.message_type, {{users}}.username AS message_from, {{messages}}.message_subject, {{messages}}.message_text
       FROM
         {{messages}} LEFT JOIN {{users}} ON {{users}}.id = {{messages}}.message_owner WHERE `message_sender` = '{$user_id}' AND `message_type` = 1
       ORDER BY `message_time` DESC;")
      : false;
  }

  public static function db_message_list_by_owner_and_string($user, $SubSelectQry) {
    return doquery("SELECT * FROM {{messages}} WHERE `message_owner` = '{$user['id']}' {$SubSelectQry} ORDER BY `message_time` DESC;");
  }

  public static function db_message_count_by_owner_and_type($user) {
    return doquery("SELECT message_owner, message_type, COUNT(message_owner) AS message_count FROM {{messages}} WHERE `message_owner` = {$user['id']} GROUP BY message_owner, message_type ORDER BY message_owner ASC, message_type;");
  }

  public static function db_message_count_outbox($user) {
    $row = doquery("SELECT COUNT(message_sender) AS message_count FROM {{messages}} WHERE `message_sender` = '{$user['id']}' AND message_type = 1 GROUP BY message_sender;", true);

    return intval($row['message_count']);
  }

  public static function db_message_list_admin_by_type($int_type_selected, $StartRec) {
    return doquery("SELECT
  message_id as `ID`,
  message_from as `FROM`,
  message_owner as `OWNER_ID`,
  u.username as `OWNER_NAME`,
  message_text as `TEXT`,
  FROM_UNIXTIME(message_time) as `TIME`
FROM
  {{messages}} AS m
  LEFT JOIN {{users}} AS u ON u.id = m.message_owner " .
      ($int_type_selected >= 0 ? "WHERE `message_type` = {$int_type_selected} " : '') .
      "ORDER BY
  `message_id` DESC
LIMIT
  {$StartRec}, 25;");
  }

  public static function db_message_insert_all($message_type, $from, $subject, $text) {
    return doquery($QryInsertMessage = 'INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) ' .
      "SELECT `id`, 0, unix_timestamp(now()), {$message_type}, '{$from}', '{$subject}', '{$text}' FROM {{users}}");
  }

  /**
   * @param $int_type_selected
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_message_count_by_type($int_type_selected) {
    $page_max = doquery('SELECT COUNT(*) AS `max` FROM {{messages}}' . ($int_type_selected >= 0 ? " WHERE `message_type` = {$int_type_selected};" : ''), true);

    return $page_max;
  }

  /**
   * @param $message_delete
   */
  public static function db_message_list_delete_set($message_delete) {
    doquery("DELETE FROM {{messages}} WHERE `message_id` in ({$message_delete});");
  }

  /**
   * @param $delete_date
   * @param $int_type_selected
   */
  public static function db_message_list_delete_by_date($delete_date, $int_type_selected) {
    doquery("DELETE FROM {{messages}} WHERE message_time <= UNIX_TIMESTAMP('{$delete_date}')" . ($int_type_selected >= 0 ? " AND `message_type` = {$int_type_selected}" : ''));
  }

  /**
   * @param $insert_values
   */
  public static function db_message_insert($insert_values) {
    doquery('INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) ' .
      'VALUES ' . implode(',', $insert_values));
  }


}