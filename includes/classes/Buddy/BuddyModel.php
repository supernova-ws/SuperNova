<?php

namespace Buddy;

use classSupernova;
use db_mysql;
use DbEmptyIterator;
use DbMysqliResultIterator;
use DBStaticMessages;
use DBStaticUser;

/**
 * Class BuddyModel
 *
 * @property int|float|string $playerSenderId Who makes buddy request
 * @property int|float|string $playerOwnerId To whom this buddy request made
 * @property int              $buddyStatusId Current buddy request status
 * @property string           $requestText Request text
 *
 * @package Buddy
 */
class BuddyModel extends \Entity {

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
   * @param int $playerIdSafe
   * @param int $newFriendIdSafe
   *
   * @throws BuddyException
   */
  public function db_buddy_check_relation($playerIdSafe, $newFriendIdSafe) {
    $result = static::$dbStatic->doQueryFetchValue(
      "SELECT `BUDDY_ID` FROM `{{buddy}}` WHERE
      (`BUDDY_SENDER_ID` = {$playerIdSafe} AND `BUDDY_OWNER_ID` = {$newFriendIdSafe})
      OR
      (`BUDDY_SENDER_ID` = {$newFriendIdSafe} AND `BUDDY_OWNER_ID` = {$playerIdSafe})
      LIMIT 1 FOR UPDATE;"
    );

    if (!empty($result)) {
      throw new BuddyException('buddy_err_adding_exists', ERR_WARNING);
    }
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

    $playerId = $cBuddy->playerId;

    if ($this->playerSenderId == $playerId) {
      throw new BuddyException('buddy_err_accept_own', ERR_ERROR);
    }

    if ($this->playerOwnerId != $playerId) {
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
      DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_accept_title', 'buddy_msg_accept_text');
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

    $playerId = $cBuddy->playerId;

    if ($this->playerSenderId != $playerId && $this->playerOwnerId != $playerId) {
      throw new BuddyException('buddy_err_delete_alien', ERR_ERROR);
    }

    if ($this->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      // Existing friendship
      $cBuddy->newFriendIdSafe = $this->playerSenderId == $playerId ? $this->playerOwnerId : $this->playerSenderId;
      DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_unfriend_title', 'buddy_msg_unfriend_text');

      static::$rowOperator->deleteById($this);
      throw new BuddyException('buddy_err_unfriend_none', ERR_NONE);
    } elseif ($this->playerSenderId == $playerId) {
      // Player's outcoming request - either denied or waiting
      static::$rowOperator->deleteById($this);
      throw new BuddyException('buddy_err_delete_own', ERR_NONE);
    } elseif ($this->buddyStatusId == BUDDY_REQUEST_WAITING) {
      // Deny incoming request
      DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_deny_title', 'buddy_msg_deny_text');

      $this->db_buddy_update_status(BUDDY_REQUEST_DENIED);
      throw new BuddyException('buddy_err_deny_none', ERR_NONE);
    }
  }

  /**
   * @param int    $newFriendIdSafe
   * @param string $newFriendNameUnsafe
   *
   * @return array|bool|false|\mysqli_result|null
   */
  protected function getNewFriend($newFriendIdSafe, $newFriendNameUnsafe) {
    $new_friend_row = array();
    if ($newFriendIdSafe) {
      $new_friend_row = DBStaticUser::db_user_by_id($newFriendIdSafe, true, '`id`, `username`');
    } elseif ($newFriendNameUnsafe) {
      $new_friend_row = DBStaticUser::db_user_by_username($newFriendNameUnsafe, true, '`id`, `username`');
    }

    return $new_friend_row;
  }

  /**
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function beFriend($cBuddy) {
    if (empty($cBuddy->newFriendIdSafe) && empty($cBuddy->new_friend_name_unsafe)) {
      return;
    }

    $new_friend_row = $this->getNewFriend($cBuddy->newFriendIdSafe, $cBuddy->new_friend_name_unsafe);

    if (empty($new_friend_row) || empty($new_friend_row['id'])) {
      throw new BuddyException('buddy_err_unknown_player', ERR_ERROR);
    }

    if ($new_friend_row['id'] == $cBuddy->playerId) {
      throw new BuddyException('buddy_err_adding_self', ERR_ERROR);
    }

    $cBuddy->newFriendIdSafe = $new_friend_row['id'];
    $this->db_buddy_check_relation($cBuddy->playerId, $new_friend_row['id']);
    DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

    $this->playerSenderId = idval($cBuddy->playerId);
    $this->playerOwnerId = idval($new_friend_row['id']);
    $this->buddyStatusId = BUDDY_REQUEST_WAITING;
    $this->requestText = $cBuddy->new_request_text_unsafe;

    static::$rowOperator->insert($this);
    throw new BuddyException('buddy_err_adding_none', ERR_NONE);
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
      $this->dbId = $cBuddy->buddy_id;
      static::$rowOperator->getById($this);

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
