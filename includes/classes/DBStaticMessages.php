<?php

class DBStaticMessages {
  public static $snMessageClassList = array(
    MSG_TYPE_NEW       => array(
      'name'       => 'new_message',
      'switchable' => false,
      'email'      => false,
    ),
    MSG_TYPE_ADMIN     => array(
      'name'       => 'msg_admin',
      'switchable' => false,
      'email'      => true,
    ),
    MSG_TYPE_PLAYER    => array(
      'name'       => 'mnl_joueur',
      'switchable' => false,
      'email'      => true,
    ),
    MSG_TYPE_ALLIANCE  => array(
      'name'       => 'mnl_alliance',
      'switchable' => false,
      'email'      => true,
    ),
    MSG_TYPE_SPY       => array(
      'name'       => 'mnl_spy',
      'switchable' => true,
      'email'      => true,
    ),
    MSG_TYPE_COMBAT    => array(
      'name'       => 'mnl_attaque',
      'switchable' => true,
      'email'      => true,
    ),
    MSG_TYPE_TRANSPORT => array(
      'name'       => 'mnl_transport',
      'switchable' => true,
      'email'      => true,
    ),
    MSG_TYPE_RECYCLE   => array(
      'name'       => 'mnl_exploit',
      'switchable' => true,
      'email'      => true,
    ),
    MSG_TYPE_EXPLORE   => array(
      'name'       => 'mnl_expedition',
      'switchable' => true,
      'email'      => true,
    ),
    //     97 => 'mnl_general',
    MSG_TYPE_QUE       => array(
      'name'       => 'mnl_buildlist',
      'switchable' => true,
      'email'      => true,
    ),
    MSG_TYPE_OUTBOX    => array(
      'name'       => 'mnl_outbox',
      'switchable' => false,
      'email'      => false,
    ),
  );
  public static $snMessageGroup = array(
    'switchable' => array(MSG_TYPE_SPY, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
    'email'      => array(MSG_TYPE_SPY, MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT,
      MSG_TYPE_ADMIN, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
  );

  /**
   * @param mixed|array $owners
   * @param integer     $sender
   * @param integer     $timestamp
   * @param integer     $message_type
   * @param string      $from
   * @param string      $subject
   * @param string      $text
   * @param bool        $escaped
   * @param bool        $force
   */
  public static function msg_send_simple_message($owners, $sender, $timestamp, $message_type, $from, $subject, $text, $escaped = false, $force = false) {
    global $user;

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

    $message_class = static::$snMessageClassList[$message_type];
    $message_class_email = $message_class['email'];
    $message_class_switchable = $message_class['switchable'];
    $message_class_name = $message_class['name'];

    $message_class_name_total = static::$snMessageClassList[MSG_TYPE_NEW]['name'];

    if ($owners[0] == '*') {
      if ($user['authlevel'] < 3) {
        return false;
      }
      // TODO Добавить $timestamp - рассылка может быть и отсроченной
      // TODO Добавить $sender - рассылка может быть и от кого-то
      static::db_message_insert_all($message_type, $from, $subject, $text);
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

      static::db_message_insert($insert_values);
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


  /**
   * @param mixed|array $owners
   * @param string      $subject
   * @param string      $text
   * @param bool        $escaped
   * @param bool        $force
   */
  public static function msgSendFromAdmin($owners, $subject, $text, $escaped = false, $force = false) {
    static::msg_send_simple_message($owners, 0, SN_TIME_NOW, MSG_TYPE_ADMIN, classLocale::$lang['sys_administration'], $subject, $text, $escaped, $force);
  }

  /**
   * @param        $senderPlayerId
   * @param        $senderPlayerNameAndCoordinates
   * @param mixed  $recipientId
   * @param string $subject
   * @param string $text
   */
  public static function msgSendFromPlayer($senderPlayerId, $senderPlayerNameAndCoordinates, $recipientId, $subject, $text) {
    static::msg_send_simple_message(
      $recipientId,
      $senderPlayerId,
      SN_TIME_NOW,
      MSG_TYPE_PLAYER,
      $senderPlayerNameAndCoordinates,
      $subject,
      $text,
      false,
      false
    );
  }

  /**
   * @param \Buddy\BuddyRoutingParams $cBuddy
   * @param string                    $localeSubjectId
   * @param string                    $localeTextId
   */
  public static function msgSendFromPlayerBuddy($cBuddy, $localeSubjectId, $localeTextId) {
    static::msgSendFromPlayer(
      $cBuddy->playerId,
      $cBuddy->playerNameAndCoordinates,
      $cBuddy->newFriendIdSafe,
      classLocale::$lang[$localeSubjectId],
      sprintf(classLocale::$lang[$localeTextId], $cBuddy->playerName)
    );
  }


  /**
   * @param array $player
   *
   * @return template
   */
  public static function messageWrite($player) {
    $error_list = array();

    $recipientId = sys_get_param_id('id');
    $recipient_name_unescaped = sys_get_param_str_unsafe('recipient_name');
    $subject_unsafe = sys_get_param_str_unsafe('subject');

    if (!empty($recipientId)) {
      $recipient_row = DBStaticUser::db_user_by_id($recipientId);
    }

    if (empty($recipient_row)) {
      $recipient_row = DBStaticUser::db_user_by_username($recipient_name_unescaped);
    }

    if (!empty($recipient_row)) {
      $recipientId = $recipient_row['id'];
      $recipient_name_unescaped = $recipient_row['username'];
    } else {
      $recipientId = 0;
      $recipient_name_unescaped = '';
    }

    if ($recipientId == $player['id']) {
      $error_list[] = array('MESSAGE' => classLocale::$lang['msg_err_self_send'], 'STATUS' => ERR_ERROR);
    }

    $re = 0;
    while (strpos($subject_unsafe, classLocale::$lang['msg_answer_prefix']) !== false) {
      $subject_unsafe = substr($subject_unsafe, strlen(classLocale::$lang['msg_answer_prefix']));
      $re++;
    }
    $re ? $subject_unsafe = classLocale::$lang['msg_answer_prefix'] . $subject_unsafe : false;

    $subject_unsafe = $subject_unsafe ? $subject_unsafe : classLocale::$lang['msg_subject_default'];

    $textUnsafe = sys_get_param_str_unsafe('text');
    if (sys_get_param_str('msg_send')) {
      if (!$recipientId) {
        $error_list[] = array('MESSAGE' => classLocale::$lang['msg_err_player_not_found'], 'STATUS' => ERR_ERROR);
      }

      if (empty($textUnsafe)) {
        $error_list[] = array('MESSAGE' => classLocale::$lang['msg_err_no_text'], 'STATUS' => ERR_ERROR);
      }

      if (empty($error_list)) {
        $error_list[] = array('MESSAGE' => classLocale::$lang['msg_not_message_sent'], 'STATUS' => ERR_NONE);

        static::msgSendFromPlayer($player['id'], DBStaticUser::renderNameAndCoordinates($player), $recipientId, $subject_unsafe, $textUnsafe);

        $textUnsafe = '';
      }
    }

    $template = gettemplate('msg_message_compose', true);
    $template->assign_vars(array(
      'RECIPIENT_ID'   => $recipientId,
      'RECIPIENT_NAME' => htmlspecialchars($recipient_name_unescaped),
      'SUBJECT'        => htmlspecialchars($subject_unsafe),
      'TEXT'           => htmlspecialchars($textUnsafe),
    ));

    foreach ($error_list as $error_message) {
      $template->assign_block_vars('result', $error_message);
    }

    $message_query = static::db_message_list_get_last_20($player, $recipientId);
    static::messageRenderList(MSG_TYPE_OUTBOX, $template, $message_query);

    return $template;
  }


  /**
   * @param array  $player
   * @param string $current_class
   */
  public static function messageDelete($player, $current_class) {
    $message_range = sys_get_param_str('message_range');
    $marked_message_list = sys_get_param('mark', array());

    $query_add = '';
    switch ($message_range) {
      case 'unchecked':
      case 'checked':
        if ($message_range == 'checked' && empty($marked_message_list)) {
          break;
        }

        foreach ($marked_message_list as &$messageId) {
          $messageId = idval($messageId);
        }

        $query_add = implode(',', $marked_message_list);
        if ($query_add) {
          $query_add = "IN ({$query_add})";
          if ($message_range == 'unchecked') {
            $query_add = "NOT {$query_add}";
          }
          $query_add = " AND `message_id` {$query_add}";
        }

      case 'class':
        if ($current_class != MSG_TYPE_OUTBOX && $current_class != MSG_TYPE_NEW) {
          $query_add .= " AND `message_type` = {$current_class}";
        }
      case 'all':
        $query_add = $query_add ? $query_add : true;
      break;
    }

    if ($query_add) {
      $query_add = $query_add === true ? '' : $query_add;
      static::db_message_list_delete($player, $query_add);
    }
  }

  /**
   * @param array $player
   * @param int   $current_class
   *
   * @return template
   */
  public static function messageShow(&$player, $current_class) {
    if ($current_class == MSG_TYPE_OUTBOX) {
      $message_query = static::db_message_list_outbox_by_user_id($player['id']);
    } else {
      if ($current_class == MSG_TYPE_NEW) {
        $SubUpdateQry = array();
        foreach (static::$snMessageClassList as $message_class_id => $message_class) {
          if ($message_class_id != MSG_TYPE_OUTBOX) {
            $SubUpdateQry[] = "`{$message_class['name']}` = '0'";
            $player[$message_class['name']] = 0;
          }
        }
        $SubUpdateQry = implode(',', $SubUpdateQry);
      } else {
        $classFieldNameCurrent = static::$snMessageClassList[$current_class]['name'];
        $classFieldNameNew = static::$snMessageClassList[MSG_TYPE_NEW]['name'];
        $SubUpdateQry = "`{$classFieldNameCurrent}` = '0', `{$classFieldNameNew}` = `{$classFieldNameNew}` - '{$player[$classFieldNameCurrent]}'";
        $SubSelectQry = "AND `message_type` = '{$current_class}'";

        $player[static::$snMessageClassList[MSG_TYPE_NEW]['name']] -= $player[static::$snMessageClassList[$current_class]['name']];
        $player[static::$snMessageClassList[$current_class]['name']] = 0;
      }

      DBStaticUser::db_user_set_by_id($player['id'], $SubUpdateQry);
      $message_query = static::db_message_list_by_owner_and_string($player, $SubSelectQry);
    }

    if (sys_get_param_int('return')) {
      header('Location: messages.php');
      die();
    }

    $template = gettemplate('msg_message_list', true);
    static::messageRenderList($current_class, $template, $message_query);

    $current_class_text = classLocale::$lang['msg_class'][$current_class];

    $template->assign_vars(array(
      "MESSAGE_CLASS"      => $current_class,
      "MESSAGE_CLASS_TEXT" => $current_class_text,
    ));

    return $template;
  }


  /**
   * @param int      $current_class
   * @param template $template
   * @param          $message_query
   */
  function messageRenderList($current_class, $template, $message_query) {
    while ($message_row = db_fetch($message_query)) {
      $template->assign_block_vars('messages', array(
        'ID'   => $message_row['message_id'],
        'DATE' => date(FMT_DATE_TIME, $message_row['message_time'] + SN_CLIENT_TIME_DIFF),
        'FROM' => htmlspecialchars($message_row['message_from']),
        'SUBJ' => htmlspecialchars($message_row['message_subject']),
        'TEXT' => in_array($message_row['message_type'], array(MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE)) && $message_row['message_sender']
          ? nl2br(htmlspecialchars($message_row['message_text']))
          : nl2br($message_row['message_text']),

        'FROM_ID'        => $message_row['message_sender'],
        'SUBJ_SANITIZED' => htmlspecialchars($message_row['message_subject']),
        'STYLE'          => $current_class == MSG_TYPE_OUTBOX
          ? static::$snMessageClassList[MSG_TYPE_OUTBOX]['name']
          : static::$snMessageClassList[$message_row['message_type']]['name'],
      ));
    }
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
    $user_id = idval($user_id);
    if (empty($user_id)) {
      return false;
    }

    return doquery("SELECT {{messages}}.message_id, {{messages}}.message_owner, {{users}}.id AS message_sender, {{messages}}.message_time,
          {{messages}}.message_type, {{users}}.username AS message_from, {{messages}}.message_subject, {{messages}}.message_text
       FROM
         {{messages}} LEFT JOIN {{users}} ON {{users}}.id = {{messages}}.message_owner WHERE `message_sender` = '{$user_id}' AND `message_type` = 1
       ORDER BY `message_time` DESC;");
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
