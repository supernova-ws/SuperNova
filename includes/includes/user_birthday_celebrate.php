<?php

function sn_user_birthday_celebrate()
{
  global $time_now, $config, $lang;

  doquery("START TRANSACTION;");

  $query = doquery("
    SELECT 
      `id`, `username`, `user_birthday`, `user_birthday_celebrated`
      , CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`
      , DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
    FROM 
      `{{users}}` 
    WHERE
      `user_birthday` IS NOT NULL
      AND (`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)
      AND `user_as_ally` IS NULL
    HAVING 
      `days_after_birthday` >= 0 AND `days_after_birthday` < {$config->user_birthday_range} FOR UPDATE;");

/*
  $query = doquery("
    SELECT 
      `id`, `username`, `user_birthday`, `user_birthday_celebrated`
      , CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`
      , DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
    FROM 
      `{{users}}` 
    WHERE
      `user_birthday` IS NOT NULL 
      AND `user_as_ally` IS NULL
    HAVING 
      `user_birthday_celebrated` IS NULL
      OR (`days_after_birthday` > 0 AND `days_after_birthday` < {$config->user_birthday_range})
      OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE
    FOR UPDATE;");
*/
  while($row = mysql_fetch_assoc($query))
  {
    $row['username'] = mysql_real_escape_string($row['username']);
    rpg_points_change($row['id'], RPG_BIRTHDAY, $config->user_birthday_gift, "Birthday gift for user {$row['username']} ID {$row['id']} on his birthday on {$row['user_birthday']}. Gift last gaved at {$row['user_birthday_celebrated']}");
/*
    if(($birthday_date = strtotime($row['current_birthday'])) > $time_now)
    {
      $birthday_date = strtotime('-1 Year', $birthday_date);
    }
*/
    doquery("UPDATE {{users}} SET `user_birthday_celebrated` = '{$row['current_birthday']}' WHERE `id` = {$row['id']} LIMIT 1;");
    msg_send_simple_message($row['id'], 0, $time_now, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_birthday'], sprintf($lang['sys_birthday_message'], $row['username'], $row['current_birthday'], $config->user_birthday_gift, $lang['sys_dark_matter_sh']), true, true);
  }

  $config->db_saveItem('user_birthday_celebrate', $time_now);
  doquery("COMMIT;");
}

?>