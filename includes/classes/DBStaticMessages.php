<?php

class DBStaticMessages {

  public static function msg_send_simple_message($owners, $sender, $timestamp, $message_type, $from, $subject, $text, $escaped = false, $force = false) {
    global $user, $sn_message_class_list;

    if (!$owners) {
      return;
    }

    $timestamp = $timestamp ? $timestamp : SN_TIME_NOW;
    $sender = intval($sender);
    if (!is_array($owners)) {
      $owners = array($owners);
    }

    if (!$escaped) {
      $from = db_escape($from);
      $subject = db_escape($subject);
      $text = db_escape($text);
    }

    $text_unescaped = stripslashes(str_replace(array('\r\n', "\r\n"), "<br />", $text));

    $message_class = $sn_message_class_list[$message_type];
    $message_class_email = $message_class['email'];
    $message_class_switchable = $message_class['switchable'];
    $message_class_name = $message_class['name'];

    $message_class_name_total = $sn_message_class_list[MSG_TYPE_NEW]['name'];

    if ($owners[0] == '*') {
      if ($user['authlevel'] < 3) {
        return false;
      }
      // TODO Добавить $timestamp - рассылка может быть и отсроченной
      // TODO Добавить $sender - рассылка может быть и от кого-то
      DBStaticMessages::db_message_insert_all($message_type, $from, $subject, $text);
      $owners = array();
    } else {
      $insert_values = array();
      $insert_template = "('%u'," . str_replace('%', '%%', " '{$sender}', '{$timestamp}', '{$message_type}', '{$from}', '{$subject}', '{$text}')");

      foreach ($owners as $owner) {
        if ($user['id'] != $owner) {
          $owner_row = DBStaticUser::db_user_by_id($owner);
        } else {
          $owner_row = $user;
        }
        sys_user_options_unpack($owner_row);

        if ($force || !$message_class_switchable || $owner_row["opt_{$message_class_name}"]) {
          $insert_values[] = sprintf($insert_template, $owner);
        }

        if ($message_class_email && classSupernova::$config->game_email_pm && $owner_row["opt_email_{$message_class_name}"]) {
          @$result = mymail($owner_row['email'], $subject, $text_unescaped, '', true);
        }
      }

      if (empty($insert_values)) {
        return;
      }

      DBStaticMessages::db_message_insert($insert_values);
    }
    DBStaticUser::db_user_list_set_mass_mail($owners, "`{$message_class_name}` = `{$message_class_name}` + 1, `{$message_class_name_total}` = `{$message_class_name_total}` + 1");

    if (in_array($user['id'], $owners) || $owners[0] == '*') {
      $user[$message_class_name]++;
      $user[$message_class_name_total]++;
    }
  }

  public static function msg_ali_send($message, $subject, $ally_rank_id = 0, $ally_id = 0) {
    global $user;

    $ally_id = $ally_id ? $ally_id : $user['ally_id'];

    $sendList = array();
    $list = '';
    $query = DBStaticUser::db_user_list(
      "ally_id = '{$ally_id}'" . ($ally_rank_id >= 0 ? " AND ally_rank_id = {$ally_rank_id}" : ''),
      false, 'id, username');
    foreach ($query as $u) {
      $sendList[] = $u['id'];
      $list .= "<br>{$u['username']} ";
    }

    static::msg_send_simple_message($sendList, $user['id'], SN_TIME_NOW, MSG_TYPE_ALLIANCE, $user['username'], $subject, $message, true);

    return $list;
  }


  public static function msgSendFromAdmin($owners, $subject, $text, $escaped = false, $force = false) {
    static::msg_send_simple_message($owners, 0, SN_TIME_NOW, MSG_TYPE_ADMIN, classLocale::$lang['sys_administration'], $subject, $text, $escaped, $force);
  }

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