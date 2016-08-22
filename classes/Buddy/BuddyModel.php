<?php

namespace Buddy;

use DbEmptyIterator;
use DbMysqliResultIterator;
use DBStatic\DBStaticMessages;
use DBStatic\DBStaticUser;

/**
 * Class BuddyModel
 *
 * @method BuddyContainer buildContainer()
 * @method BuddyContainer loadById(int|string $dbId)
 *
 * property int|float|string $playerSenderId Who makes buddy request
 * property int|float|string $playerOwnerId To whom this buddy request made
 * property int              $buddyStatusId Current buddy request status
 * property string           $requestText Request text
 *
 * @package Buddy
 */
class BuddyModel extends \Entity\KeyedModel{
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = 'buddy';
  protected $exceptionClass = 'BuddyException';
  protected $entityContainerClass = 'Buddy\BuddyContainer';

  protected $newProperties = array(
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

  /**
   * @param BuddyContainer $cBuddy
   *
   * @return int
   */
  public function db_buddy_update_status($cBuddy) {
    return $this->rowOperator->doUpdateRowSetAffected(
      TABLE_BUDDY,
      array(
        'BUDDY_STATUS' => $cBuddy->buddyStatusId,
      ),
      array(
        'BUDDY_ID' => $cBuddy->dbId,
      )
    );
  }

  /**
   * @param $playerIdUnsafe
   * @param $newFriendIdUnsafe
   *
   * @throws BuddyException
   */
  public function db_buddy_check_relation($playerIdUnsafe, $newFriendIdUnsafe) {
    $playerIdSafe = idval($playerIdUnsafe);
    $newFriendIdSafe = idval($newFriendIdUnsafe);

    $result = $this->rowOperator->doSelectFetchValue(
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
   * @param mixed $playerId
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public function db_buddy_list_by_user($playerId) {
    return ($user_id = idval($playerId)) ? $this->rowOperator->doSelectIterator(
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
   * @param BuddyParams    $params
   *
   * @throws BuddyException
   */
  public function accept($cBuddy, $params) {
    if ($params->mode != 'accept') {
      return;
    }

    if ($cBuddy->playerSenderId == $params->playerId) {
      throw new BuddyException('buddy_err_accept_own', ERR_ERROR);
    }

    if ($cBuddy->playerOwnerId != $params->playerId) {
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
      DBStaticMessages::msgSendFromPlayerBuddy($params, 'buddy_msg_accept_title', 'buddy_msg_accept_text');
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
   * @param BuddyParams    $params
   *
   * @throws BuddyException
   */
  public function deleteRequest($cBuddy, $params) {
    if ($params->mode != 'delete') {
      return;
    }

    $playerId = $params->playerId;

    if ($cBuddy->playerSenderId != $params->playerId && $cBuddy->playerOwnerId != $params->playerId) {
      throw new BuddyException('buddy_err_delete_alien', ERR_ERROR);
    }

    if ($cBuddy->buddyStatusId == BUDDY_REQUEST_ACTIVE) {
      // Existing friendship
      $params->newFriendIdSafe = $cBuddy->playerSenderId == $playerId ? $cBuddy->playerOwnerId : $cBuddy->playerSenderId;
      DBStaticMessages::msgSendFromPlayerBuddy($params, 'buddy_msg_unfriend_title', 'buddy_msg_unfriend_text');

      $this->rowOperator->deleteById($this, $cBuddy->dbId);
      throw new BuddyException('buddy_err_unfriend_none', ERR_NONE);
    } elseif ($cBuddy->playerSenderId == $playerId) {
      // Player's outcoming request - either denied or waiting
      $this->rowOperator->deleteById($this, $cBuddy->dbId);
      throw new BuddyException('buddy_err_delete_own', ERR_NONE);
    } elseif ($cBuddy->buddyStatusId == BUDDY_REQUEST_WAITING) {
      // Deny incoming request
      DBStaticMessages::msgSendFromPlayerBuddy($params, 'buddy_msg_deny_title', 'buddy_msg_deny_text');

      $cBuddy->buddyStatusId = BUDDY_REQUEST_DENIED;
      $this->db_buddy_update_status($cBuddy);
      throw new BuddyException('buddy_err_deny_none', ERR_NONE);
    }
  }

  /**
   * @param int    $newFriendIdSafe
   * @param string $newFriendNameUnsafe
   *
   * @return array|false|null
   * @throws BuddyException
   */
  protected function getNewFriend($newFriendIdSafe, $newFriendNameUnsafe) {
    $new_friend_row = array();
    if ($newFriendIdSafe) {
      $new_friend_row = DBStaticUser::db_user_by_id($newFriendIdSafe, true, '`id`, `username`');
    } elseif ($newFriendNameUnsafe) {
      $new_friend_row = DBStaticUser::db_user_by_username($newFriendNameUnsafe, true, '`id`, `username`');
    }
    if (empty($new_friend_row['id'])) {
      throw new BuddyException('buddy_err_unknown_player', ERR_ERROR);
    }

    return $new_friend_row;
  }

  /**
   * @param BuddyParams $params
   *
   * @throws BuddyException
   */
  public function beFriend($params) {
    if (empty($params->newFriendIdSafe) && empty($params->new_friend_name_unsafe)) {
      return;
    }

    $new_friend_row = $this->getNewFriend($params->newFriendIdSafe, $params->new_friend_name_unsafe);
    if ($new_friend_row['id'] == $params->playerId) {
      throw new BuddyException('buddy_err_adding_self', ERR_ERROR);
    }

    $this->db_buddy_check_relation($params->playerId, $new_friend_row['id']);
    $params->newFriendIdSafe = $new_friend_row['id'];
    DBStaticMessages::msgSendFromPlayerBuddy($params, 'buddy_msg_adding_title', 'buddy_msg_adding_text');

    $cBuddy = $this->buildContainer();
    $cBuddy->playerSenderId = $params->playerId;
    $cBuddy->playerOwnerId = $new_friend_row['id'];
    $cBuddy->buddyStatusId = BUDDY_REQUEST_WAITING;
    $cBuddy->requestText = $params->request_text_unsafe;

    $this->exportRowNoId($cBuddy);
    $cBuddy->dbId = $this->rowOperator->insert($this, $cBuddy->row);
    throw new BuddyException('buddy_err_adding_none', ERR_NONE);
  }

  /**
   * @param BuddyParams $params
   *
   * @throws BuddyException
   */
  public function route($params) {
    // Trying to load buddy record with supplied dbId
    if ($params->buddy_id) {
      $cBuddy = $this->loadById($params->buddy_id);
      if (!$cBuddy) {
        throw new BuddyException('buddy_err_not_exist', ERR_ERROR);
      }

      // Trying to accept buddy request
      $this->accept($cBuddy, $params);
      // Trying to decline buddy request. If it's own request - it will be deleted
      $this->deleteRequest($cBuddy, $params);
    } else {
      $this->beFriend($params);
    }
  }

}
