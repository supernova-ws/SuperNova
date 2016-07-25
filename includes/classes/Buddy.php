<?php

class Buddy extends Entity {

  public static $tableName = 'buddy';
  public static $idField = 'BUDDY_ID';


  // TODO - remove public static function db_buddy_update_status($buddy_id, $status) {
  public function db_buddy_update_status($status) {
    $buddy_id = idval($this->row['BUDDY_ID']);

    doquery("UPDATE `{{buddy}}` SET `BUDDY_STATUS` = {$status} WHERE `BUDDY_ID` = '{$buddy_id}' LIMIT 1;");

    return classSupernova::$db->db_affected_rows();
  }

  public function db_buddy_delete($buddy_id) {
    $buddy_id = idval($this->row['BUDDY_ID']);

    doquery("DELETE FROM `{{buddy}}` WHERE `BUDDY_ID` = '{$buddy_id}' LIMIT 1;");
  }

  public function db_buddy_check_relation($user, $new_friend_row) {
    return static::$dbStatic->doQueryFetch(
      "SELECT `BUDDY_ID` FROM `{{buddy}}` WHERE
      (`BUDDY_SENDER_ID` = {$user['id']} AND `BUDDY_OWNER_ID` = {$new_friend_row['id']})
      OR
      (`BUDDY_SENDER_ID` = {$new_friend_row['id']} AND `BUDDY_OWNER_ID` = {$user['id']})
      LIMIT 1 FOR UPDATE;"
    );
  }

  public function db_buddy_insert($userId_safe, $new_friend_row_id_safe, $new_request_text) {
    doquery("INSERT INTO `{{buddy}}` SET `BUDDY_SENDER_ID` = {$userId_safe}, `BUDDY_OWNER_ID` = '{$new_friend_row_id_safe}', `BUDDY_REQUEST` = '{$new_request_text}';");
  }

  /**
   * @param db_mysql $db
   * @param mixed    $user_id
   *
   * @return DbEmptyIterator
   */
  public static function db_buddy_list_by_user($db, $user_id) {
    return ($user_id = idval($user_id)) ? $db->doQueryIterator(
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
    ) : new DbEmptyIterator();
  }

  /**
   * @param array $user
   *
   * @throws Exception
   */
  public function accept($user) {
    $buddy_row = $this->row;

    if ($buddy_row['BUDDY_SENDER_ID'] == $user['id']) {
      throw new Exception('buddy_err_accept_own', ERR_ERROR);
    }

    if ($buddy_row['BUDDY_OWNER_ID'] != $user['id']) {
      throw new Exception('buddy_err_accept_alien', ERR_ERROR);
    }

    if ($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) {
      throw new Exception('buddy_err_accept_already', ERR_WARNING);
    }

    if ($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED) {
      throw new Exception('buddy_err_accept_denied', ERR_ERROR);
    }

    if ($buddy_row['BUDDY_STATUS'] != BUDDY_REQUEST_WAITING) {
      throw new Exception('buddy_err_unknown_status', ERR_ERROR);
    }

    $result = $this->db_buddy_update_status(BUDDY_REQUEST_ACTIVE);
    if ($result) {
      DBStaticMessages::msgSendFromPlayerBuddy($buddy_row['BUDDY_SENDER_ID'], $user, 'buddy_msg_accept_title', 'buddy_msg_accept_text');
      throw new Exception('buddy_err_accept_none', ERR_NONE);
    } else {
      throw new Exception('buddy_err_accept_internal', ERR_ERROR);
    }
  }

  /**
   * @param array $user
   *
   * @throws Exception
   */
  public function delete($user) {
    $buddy_row = $this->row;

    if ($buddy_row['BUDDY_SENDER_ID'] != $user['id'] && $buddy_row['BUDDY_OWNER_ID'] != $user['id']) {
      throw new Exception('buddy_err_delete_alien', ERR_ERROR);
    }

    if ($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) {
      // Existing friendship
      $ex_friend_id = $buddy_row['BUDDY_SENDER_ID'] == $user['id'] ? $buddy_row['BUDDY_OWNER_ID'] : $buddy_row['BUDDY_SENDER_ID'];

      DBStaticMessages::msgSendFromPlayerBuddy($ex_friend_id, $user, 'buddy_msg_unfriend_title', 'buddy_msg_unfriend_text');

      $this->db_buddy_delete($buddy_row['BUDDY_ID']);
      throw new Exception('buddy_err_unfriend_none', ERR_NONE);
    } elseif ($buddy_row['BUDDY_SENDER_ID'] == $user['id']) {
      // Player's outcoming request - either denied or waiting
      $this->db_buddy_delete($buddy_row['BUDDY_ID']);
      throw new Exception('buddy_err_delete_own', ERR_NONE);
    } elseif ($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_WAITING) {
      // Deny incoming request
      DBStaticMessages::msgSendFromPlayerBuddy($buddy_row['BUDDY_SENDER_ID'], $user, 'buddy_msg_deny_title', 'buddy_msg_deny_text');

      $this->db_buddy_update_status(BUDDY_REQUEST_DENIED);
      throw new Exception('buddy_err_deny_none', ERR_NONE);
    }
  }

  /**
   * @param array  $user
   * @param mixed  $new_friend_id
   * @param string $new_friend_name_unsafe
   * @param string $new_request_text_safe
   *
   * @throws Exception
   */
  public function beFriend($user, $new_friend_id, $new_friend_name_unsafe, $new_request_text_safe) {
    $new_friend_row = array();
    if ($new_friend_id) {
      $new_friend_row = DBStaticUser::db_user_by_id($new_friend_id, true, '`id`, `username`');
    } elseif ($new_friend_name_unsafe) {
      $new_friend_row = DBStaticUser::db_user_by_username($new_friend_name_unsafe, true, '`id`, `username`');
    }

    if (empty($new_friend_row) || empty($new_friend_row['id'])) {
      return;
    }

    if ($new_friend_row['id'] == $user['id']) {
      unset($new_friend_row);
      throw new Exception('buddy_err_adding_self', ERR_ERROR);
    }

    // Checking for user name & request text - in case if it was request to adding new request
    if ($new_request_text_safe) {
      $check_relation = $this->db_buddy_check_relation($user, $new_friend_row);
      if (isset($check_relation['BUDDY_ID'])) {
        throw new Exception('buddy_err_adding_exists', ERR_WARNING);
      }

      DBStaticMessages::msgSendFromPlayerBuddy($new_friend_row['id'], $user, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

      $this->db_buddy_insert(idval($user['id']), idval($new_friend_row['id']), $new_request_text_safe);
      throw new Exception('buddy_err_adding_none', ERR_NONE);
    }
  }

}
