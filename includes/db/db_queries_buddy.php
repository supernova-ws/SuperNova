<?php

// BUDDY *************************************************************************************************************

function db_buddy_insert($user, $new_friend_row, $new_request_text) {
  doquery("INSERT INTO {{buddy}} SET `BUDDY_SENDER_ID` = {$user['id']}, `BUDDY_OWNER_ID` = {$new_friend_row['id']}, `BUDDY_REQUEST` = '{$new_request_text}';");
}

function db_buddy_get_row($buddy_id) {
  return doquery("SELECT BUDDY_SENDER_ID, BUDDY_OWNER_ID, BUDDY_STATUS FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1 FOR UPDATE;", true);
}

function db_buddy_update_status($buddy_id, $status) {
  doquery("UPDATE {{buddy}} SET `BUDDY_STATUS` = {$status} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
}

function db_buddy_delete($buddy_id) {
  doquery("DELETE FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
}

function db_buddy_check_relation($user, $new_friend_row) {
  return doquery("SELECT `BUDDY_ID` FROM {{buddy}} WHERE
      (`BUDDY_SENDER_ID` = {$user['id']} AND `BUDDY_OWNER_ID` = {$new_friend_row['id']})
      OR
      (`BUDDY_SENDER_ID` = {$new_friend_row['id']} AND `BUDDY_OWNER_ID` = {$user['id']})
      LIMIT 1 FOR UPDATE;"
    , true);
}

function db_buddy_list_by_user($user_id) {
//  return ($user_id = intval($user_id)) ? doquery(
  return ($user_id = idval($user_id)) ? doquery(
    "SELECT
      b.*,
      IF(b.BUDDY_OWNER_ID = {$user_id}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID) AS BUDDY_USER_ID,
      u.username AS BUDDY_USER_NAME,
      p.name AS BUDDY_PLANET_NAME,
      p.galaxy AS BUDDY_PLANET_GALAXY,
      p.system AS BUDDY_PLANET_SYSTEM,
      p.planet AS BUDDY_PLANET_PLANET,
      a.id AS BUDDY_ALLY_ID,
      a.ally_name AS BUDDY_ALLY_NAME,
      u.onlinetime
    FROM {{buddy}} AS b
      LEFT JOIN {{users}} AS u ON u.id = IF(b.BUDDY_OWNER_ID = {$user_id}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID)
      LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id = id_planet
      LEFT JOIN {{alliance}} AS a ON a.id = u.ally_id
    WHERE (`BUDDY_OWNER_ID` = {$user_id}) OR `BUDDY_SENDER_ID` = {$user_id}
    ORDER BY BUDDY_STATUS, BUDDY_ID"
  ) : false;
}
