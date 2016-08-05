<?php

function sn_user_birthday_celebrate() {
  sn_db_transaction_start();

  foreach (DBStaticUser::db_user_list_to_celebrate(classSupernova::$config->user_birthday_range) as $row) {
    $username_unescaped = $row['username'];
    $row['username'] = db_escape($row['username']);
    rpg_points_change(
      $row['id'],
      RPG_BIRTHDAY,
      classSupernova::$config->user_birthday_gift,
      "Birthday gift for user {$row['username']} ID {$row['id']} on his birthday on {$row['user_birthday']}. Gift last gaved at {$row['user_birthday_celebrated']}"
    );
    DBStaticUser::db_user_set_by_id($row['id'], "`user_birthday_celebrated` = '{$row['current_birthday']}'");

    $message = sprintf(
      classLocale::$lang['sys_birthday_message'],
      $username_unescaped,
      $row['current_birthday'],
      classSupernova::$config->user_birthday_gift,
      classLocale::$lang['sys_dark_matter_sh']
    );
    DBStaticMessages::msgSendFromAdmin($row['id'], classLocale::$lang['sys_birthday'], $message, true);
  }

  classSupernova::$config->db_saveItem('user_birthday_celebrate', SN_TIME_NOW);
  sn_db_transaction_commit();
}
