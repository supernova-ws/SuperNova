<?php

namespace Buddy;

use DbEmptyIterator;
use DbMysqliResultIterator;
use DBStaticMessages;
use DBStaticUser;

/**
 * Class BuddyModel
 *
 * property int|float|string $playerSenderId Who makes buddy request
 * property int|float|string $playerOwnerId To whom this buddy request made
 * property int              $buddyStatusId Current buddy request status
 * property string           $requestText Request text
 *
 * @package Buddy
 */
class BuddyModel extends \EntityModel {

  protected static $tableName = 'buddy';
  protected static $idField = 'BUDDY_ID';
  protected static $exceptionClass = 'BuddyException';


  /**
   * @param BuddyContainer $cBuddy
   *
   * @return int
   */
  public function db_buddy_update_status($cBuddy) {
    $db = $cBuddy->getDbStatic();
    $db->doUpdateRowSet(
      TABLE_BUDDY,
      array(
        'BUDDY_STATUS' => $cBuddy->buddyStatusId,
      ),
      array(
        'BUDDY_ID' => $cBuddy->dbId,
      )
    );

    return $db->db_affected_rows();
  }

  /**
   * @param BuddyContainer $cBuddy
   *
   * @throws BuddyException
   */
  public function db_buddy_check_relation($cBuddy) {
    $playerIdSafe = idval($cBuddy->playerId);
    $newFriendIdSafe = idval($cBuddy->newFriendIdSafe);

    $result = $cBuddy->getDbStatic()->doSelectFetchValue(
      "SELECT `BUDDY_ID` 
      FROM `{{buddy}}` 
      WHERE
        (`BUDDY_SENDER_ID` = {$playerIdSafe} AND `BUDDY_OWNER_ID` = {$newFriendIdSafe})
        OR
        (`BUDDY_SENDER_ID` = {$newFriendIdSafe} AND `BUDDY_OWNER_ID` = {$playerIdSafe})
      LIMIT 1 
      FOR UPDATE;"
    );

    if (!empty($result)) {
      throw new BuddyException('buddy_err_adding_exists', ERR_WARNING);
    }
  }

  /**
   * @param BuddyContainer $cBuddy
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public function db_buddy_list_by_user($cBuddy) {
    return ($user_id = idval($cBuddy->playerId)) ? $cBuddy->getDbStatic()->doSelectIterator(
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
   * @param BuddyContainer $cBuddy
   *
   * @throws BuddyException
   */
  public function accept($cBuddy) {
    if ($cBuddy->mode != 'accept') {
      return;
    }

    if ($cBuddy->playerSenderId == $cBuddy->playerId) {
      throw new BuddyException('buddy_err_accept_own', ERR_ERROR);
    }

    if ($cBuddy->playerOwnerId != $cBuddy->playerId) {
      throw new BuddyException('buddy_err_accept_alien', ERR_ERROR);
    }

    if ($cBuddy->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      throw new BuddyException('buddy_err_accept_already', ERR_WARNING);
    }

    if ($cBuddy->buddyStatusId == BUDDY_REQUEST_DENIED) {
      throw new BuddyException('buddy_err_accept_denied', ERR_ERROR);
    }

    if ($cBuddy->buddyStatusId != BUDDY_REQUEST_WAITING) {
      throw new BuddyException('buddy_err_unknown_status', ERR_ERROR);
    }

    $cBuddy->buddyStatusId = BUDDY_REQUEST_ACTIVE;
    $result = $this->db_buddy_update_status($cBuddy);
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
   * @param BuddyContainer $cBuddy
   *
   * @throws BuddyException
   */
  public function decline($cBuddy) {
    if ($cBuddy->mode != 'delete') {
      return;
    }

    $playerId = $cBuddy->playerId;

    if ($cBuddy->playerSenderId != $cBuddy->playerId && $cBuddy->playerOwnerId != $cBuddy->playerId) {
      throw new BuddyException('buddy_err_delete_alien', ERR_ERROR);
    }

    if ($cBuddy->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      // Existing friendship
      $cBuddy->newFriendIdSafe = $cBuddy->playerSenderId == $playerId ? $cBuddy->playerOwnerId : $cBuddy->playerSenderId;
      DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_unfriend_title', 'buddy_msg_unfriend_text');

      $cBuddy->delete();
      throw new BuddyException('buddy_err_unfriend_none', ERR_NONE);
    } elseif ($cBuddy->playerSenderId == $playerId) {
      // Player's outcoming request - either denied or waiting
      $cBuddy->delete();
      throw new BuddyException('buddy_err_delete_own', ERR_NONE);
    } elseif ($cBuddy->buddyStatusId == BUDDY_REQUEST_WAITING) {
      // Deny incoming request
      DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_deny_title', 'buddy_msg_deny_text');

      $cBuddy->buddyStatusId = BUDDY_REQUEST_DENIED;
      $this->db_buddy_update_status($cBuddy);
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
   * @param BuddyContainer $cBuddy
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
    $this->db_buddy_check_relation($cBuddy);
    DBStaticMessages::msgSendFromPlayerBuddy($cBuddy, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

    $cBuddy->playerSenderId = $cBuddy->playerId;
    $cBuddy->playerOwnerId = $new_friend_row['id'];
    $cBuddy->buddyStatusId = BUDDY_REQUEST_WAITING;

    $cBuddy->insert();
    throw new BuddyException('buddy_err_adding_none', ERR_NONE);
  }

  /**
   * @param BuddyContainer $cBuddy
   *
   * @throws BuddyException
   */
  public function route($cBuddy) {
    // Trying to load buddy record with supplied dbId
    if ($cBuddy->buddy_id) {
      $cBuddy->dbId = $cBuddy->buddy_id;
      if (!$cBuddy->loadTry()) {
        throw new BuddyException('buddy_err_not_exist', ERR_ERROR);
      }
    }

    // Trying to accept buddy request
    $this->accept($cBuddy);
    // Trying to decline buddy request. If it's own request - it will be deleted
    $this->decline($cBuddy);
    // New request?
    $this->beFriend($cBuddy);
  }

}
