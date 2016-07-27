<?php

namespace Buddy;

use classSupernova;
use db_mysql;
use DbEmptyIterator;
use DbMysqliResultIterator;
use DBStaticMessages;
use DBStaticUser;
use Entity;

/**
 * Class BuddyModel
 *
 * @property int|float $playerSenderId Who makes buddy request
 * @property int|float $playerOwnerId To whom this buddy request made
 * @property int       $buddyStatusId Current buddy request status
 * @property string    $requestText Request text
 *
 * @package Buddy
 */
class BuddyModel extends Entity {

  protected static $tableName = 'buddy';
  protected static $idField = 'BUDDY_ID';

  // TODO - make it work with Model's properties
  /**
   * Property list
   *
   * @var array
   */
  protected static $_properties = array(
    'dbId'           => array(
      P_DB_FIELD => 'BUDDY_ID',
    ),
    'playerSenderId' => array(
      P_DB_FIELD => 'BUDDY_SENDER_ID',
    ),
    'playerOwnerId'  => array(
      P_DB_FIELD => 'BUDDY_OWNER_ID',
    ),
    'buddyStatusId'  => array(
      P_DB_FIELD => 'BUDDY_STATUS',
    ),
    'requestText'    => array(
      P_DB_FIELD => 'BUDDY_REQUEST',
    ),
  );

  // TODO - remove public static function db_buddy_update_status($buddy_id, $status) {
  public function db_buddy_update_status($status) {
    $buddy_id = idval($this->dbId);

    doquery("UPDATE `{{buddy}}` SET `BUDDY_STATUS` = {$status} WHERE `BUDDY_ID` = '{$buddy_id}' LIMIT 1;");

    return classSupernova::$db->db_affected_rows();
  }

  /**
   * @param BuddyRoutingParams $cBuddy
   *
   * @return array|null
   */
  public function db_buddy_check_relation($cBuddy) {
    return static::$dbStatic->doQueryFetch(
      "SELECT `BUDDY_ID` FROM `{{buddy}}` WHERE
      (`BUDDY_SENDER_ID` = {$cBuddy->user['id']} AND `BUDDY_OWNER_ID` = {$cBuddy->newFriendId})
      OR
      (`BUDDY_SENDER_ID` = {$cBuddy->newFriendId} AND `BUDDY_OWNER_ID` = {$cBuddy->user['id']})
      LIMIT 1 FOR UPDATE;"
    );
  }

  /**
   * @param db_mysql $db
   * @param mixed    $user_id
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  // TODO - make it non-static
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
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function accept($cBuddy) {
    if ($cBuddy->mode != 'accept') {
      return;
    }

    $user = $cBuddy->user;

    if ($this->playerSenderId == $user['id']) {
      throw new BuddyException('buddy_err_accept_own', ERR_ERROR);
    }

    if ($this->playerOwnerId != $user['id']) {
      throw new BuddyException('buddy_err_accept_alien', ERR_ERROR);
    }

    if ($this->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      throw new BuddyException('buddy_err_accept_already', ERR_WARNING);
    }

    if ($this->buddyStatusId == BUDDY_REQUEST_DENIED) {
      throw new BuddyException('buddy_err_accept_denied', ERR_ERROR);
    }

    if ($this->buddyStatusId != BUDDY_REQUEST_WAITING) {
      throw new BuddyException('buddy_err_unknown_status', ERR_ERROR);
    }

    $result = $this->db_buddy_update_status(BUDDY_REQUEST_ACTIVE);
    if ($result) {
      DBStaticMessages::msgSendFromPlayerBuddy($this->playerSenderId, $user, 'buddy_msg_accept_title', 'buddy_msg_accept_text');
      throw new BuddyException('buddy_err_accept_none', ERR_NONE);
    } else {
      throw new BuddyException('buddy_err_accept_internal', ERR_ERROR);
    }
  }

  /**
   * Declining buddy request
   *
   * If it is own request - it will be deleted
   *
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function decline($cBuddy) {
    if ($cBuddy->mode != 'delete') {
      return;
    }

    $user = $cBuddy->user;

    if ($this->playerSenderId != $user['id'] && $this->playerOwnerId != $user['id']) {
      throw new BuddyException('buddy_err_delete_alien', ERR_ERROR);
    }

    if ($this->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      // Existing friendship
      $ex_friend_id = $this->playerSenderId == $user['id'] ? $this->playerOwnerId : $this->playerSenderId;

      DBStaticMessages::msgSendFromPlayerBuddy($ex_friend_id, $user, 'buddy_msg_unfriend_title', 'buddy_msg_unfriend_text');

      $this->delete();
      throw new BuddyException('buddy_err_unfriend_none', ERR_NONE);
    } elseif ($this->playerSenderId == $user['id']) {
      // Player's outcoming request - either denied or waiting
      $this->delete();
      throw new BuddyException('buddy_err_delete_own', ERR_NONE);
    } elseif ($this->buddyStatusId == BUDDY_REQUEST_WAITING) {
      // Deny incoming request
      DBStaticMessages::msgSendFromPlayerBuddy($this->playerSenderId, $user, 'buddy_msg_deny_title', 'buddy_msg_deny_text');

      $this->db_buddy_update_status(BUDDY_REQUEST_DENIED);
      throw new BuddyException('buddy_err_deny_none', ERR_NONE);
    }
  }

  /**
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function beFriend($cBuddy) {
    if (empty($cBuddy->new_friend_id_safe) && empty($cBuddy->new_friend_name_unsafe)) {
      return;
    }

    $user = $cBuddy->user;


    $new_friend_row = array();
    if ($cBuddy->new_friend_id_safe) {
      $new_friend_row = DBStaticUser::db_user_by_id($cBuddy->new_friend_id_safe, true, '`id`, `username`');
    } elseif ($cBuddy->new_friend_name_unsafe) {
      $new_friend_row = DBStaticUser::db_user_by_username($cBuddy->new_friend_name_unsafe, true, '`id`, `username`');
    }

    if (empty($new_friend_row) || empty($new_friend_row['id'])) {
      return;
    }

    if ($new_friend_row['id'] == $user['id']) {
      unset($new_friend_row);
      throw new BuddyException('buddy_err_adding_self', ERR_ERROR);
    }

    // Checking for user name & request text - in case if it was request to adding new request
    if ($cBuddy->new_request_text_unsafe) {
      $cBuddy->newFriendId = $new_friend_row['id'];
      $check_relation = $this->db_buddy_check_relation($cBuddy);
      if (isset($check_relation[$this->getIdFieldName()])) {
        throw new BuddyException('buddy_err_adding_exists', ERR_WARNING);
      }

      DBStaticMessages::msgSendFromPlayerBuddy($new_friend_row['id'], $user, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

      $this->playerSenderId = idval($user['id']);
      $this->playerOwnerId = idval($new_friend_row['id']);
      $this->buddyStatusId = BUDDY_REQUEST_WAITING;
      $this->requestText = $cBuddy->new_request_text_unsafe;

      $this->insert();
      throw new BuddyException('buddy_err_adding_none', ERR_NONE);
    }
  }

  public function isContainerEmpty() {
    return
      $this->buddyStatusId == null
      ||
      $this->buddyStatusId == BUDDY_REQUEST_NOT_SET
      ||
      empty($this->playerSenderId)
      ||
      empty($this->playerOwnerId);
  }

  /**
   * Trying to load object info by buddy ID - if it is supplied
   *
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  protected function loadTry($cBuddy) {
    if ($cBuddy->buddy_id) {
      $this->load($cBuddy->buddy_id);

      if ($this->isContainerEmpty()) {
        throw new BuddyException('buddy_err_not_exist', ERR_ERROR);
      }
    }
  }

  /**
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function route($cBuddy) {
    // Trying to load buddy record with supplied dbId
    $this->loadTry($cBuddy);
    // Trying to accept buddy request
    $this->accept($cBuddy);
    // Trying to decline buddy request. If it's own request - it will be deleted
    $this->decline($cBuddy);
    // New request?
    $this->beFriend($cBuddy);
  }

}
