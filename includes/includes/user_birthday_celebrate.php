<?php

function sn_user_birthday_celebrate()
{
  global $config, $lang;

  sn_db_transaction_start();

  $query = db_user_list_to_celebrate($config->user_birthday_range);

  while($row = db_fetch($query))
  {
    $row['username'] = db_escape($row['username']);
    rpg_points_change($row['id'], RPG_BIRTHDAY, $config->user_birthday_gift, "Birthday gift for user {$row['username']} ID {$row['id']} on his birthday on {$row['user_birthday']}. Gift last gaved at {$row['user_birthday_celebrated']}");
    db_user_set_by_id($row['id'], "`user_birthday_celebrated` = '{$row['current_birthday']}'");
    msg_send_simple_message($row['id'], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_birthday'], sprintf($lang['sys_birthday_message'], $row['username'], $row['current_birthday'], $config->user_birthday_gift, $lang['sys_dark_matter_sh']), true, true);
  }

  $config->db_saveItem('user_birthday_celebrate', SN_TIME_NOW);
  sn_db_transaction_commit();
}
