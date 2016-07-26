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

  public static $tableName = 'buddy';
  public static $idField = 'BUDDY_ID';

  // TODO - make it work with Model's properties
  /**
   * Property list
   *
   * @var array
   */
  protected static $_properties = array(
    'dbId'           => true,
    'playerSenderId' => true,
    'playerOwnerId'  => true,
    'buddyStatusId'  => true,
    'requestText'    => true,
  );


  // TODO - remove public static function db_buddy_update_status($buddy_id, $status) {
  public function db_buddy_update_status($status) {
    $buddy_id = idval($this->dbId);

    doquery("UPDATE `{{buddy}}` SET `BUDDY_STATUS` = {$status} WHERE `BUDDY_ID` = '{$buddy_id}' LIMIT 1;");

    return classSupernova::$db->db_affected_rows();
  }

  // TODO - make it non-static
  public function db_buddy_check_relation($user, $new_friend_row) {
    return static::$dbStatic->doQueryFetch(
      "SELECT `BUDDY_ID` FROM `{{buddy}}` WHERE
      (`BUDDY_SENDER_ID` = {$user['id']} AND `BUDDY_OWNER_ID` = {$new_friend_row['id']})
      OR
      (`BUDDY_SENDER_ID` = {$new_friend_row['id']} AND `BUDDY_OWNER_ID` = {$user['id']})
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
   * @param array $user
   *
   * @throws BuddyException
   */
  public function accept($user) {
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
   * @param array $user
   *
   * @throws BuddyException
   */
  public function decline($user) {
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
   * @param array              $user
   * @param mixed              $new_friend_id
   * @param string             $new_friend_name_unsafe
   * @param string             $new_request_text_safe
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function beFriend($user, $new_friend_id, $new_friend_name_unsafe, $new_request_text_safe, $cBuddy) {
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
    if ($new_request_text_safe) {
      $check_relation = $this->db_buddy_check_relation($user, $new_friend_row);
      if (isset($check_relation['BUDDY_ID'])) {
        throw new BuddyException('buddy_err_adding_exists', ERR_WARNING);
      }

      DBStaticMessages::msgSendFromPlayerBuddy($new_friend_row['id'], $user, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

      $this->playerSenderId = idval($user['id']);
      $this->playerOwnerId = idval($new_friend_row['id']);
      $this->buddyStatusId = BUDDY_REQUEST_WAITING;
      $this->requestText = $new_request_text_safe;

      $this->insert();
      throw new BuddyException('buddy_err_adding_none', ERR_NONE);
    }
  }

  public function isEmpty() {
    return
      $this->buddyStatusId == BUDDY_REQUEST_NOT_SET
      ||
      empty($this->playerSenderId)
      ||
      empty($this->playerOwnerId);
  }

  /**
   * @param array $row
   */
  // TODO -    PROOF OF CONCEPTION
  public function setRow($row) {
//    foreach($this->getProperties() as $propertyName => $cork) {
//    }
    $this->dbId = $row[static::$idField];
    $this->playerSenderId = $row['BUDDY_SENDER_ID'];
    $this->playerOwnerId = $row['BUDDY_OWNER_ID'];
    $this->buddyStatusId = $row['BUDDY_STATUS'];
    $this->requestText = $row['BUDDY_REQUEST'];
  }

  /**
   * Compiles object data into db row
   *
   * @return array
   */
  public function getRow($withDbId = true) {
    $row = array(
      static::$idField  => $this->dbId,
      'BUDDY_SENDER_ID' => $this->playerSenderId,
      'BUDDY_OWNER_ID'  => $this->playerOwnerId,
      'BUDDY_STATUS'    => $this->buddyStatusId,
      'BUDDY_REQUEST'   => $this->requestText,
    );

    if (!$withDbId) {
      unset($row[static::$idField]);
    }

    return $row;
  }

  /**
   * @param BuddyRoutingParams $cBuddy
   *
   * @throws BuddyException
   */
  public function route($cBuddy) {
    if ($cBuddy->buddy_id) {
      $this->load($cBuddy->buddy_id);

      if ($this->isEmpty()) {
        throw new BuddyException('buddy_err_not_exist', ERR_ERROR);
      }

      switch ($cBuddy->mode) {
        case 'accept':
          $this->accept($cBuddy->user);
        break;
        case 'delete':
          $this->decline($cBuddy->user);
        break;
      }
    } else {
      // New request?
      // Checking for user ID - in case if it was request from outside buddy system
      if (!empty($cBuddy->new_friend_id_safe) || !empty($cBuddy->new_friend_name_unsafe)) {
        $this->beFriend($cBuddy->user, $cBuddy->new_friend_id_safe, $cBuddy->new_friend_name_unsafe, $cBuddy->new_request_text, $cBuddy);
      }
    }
  }

}
