<?php

namespace Buddy;

/**
 * Class BuddyContainer
 *
 * @method BuddyModel getModel()
 *
 * @property int|float|string $playerSenderId Who makes buddy request
 * @property int|float|string $playerOwnerId To whom this buddy request made
 * @property int              $buddyStatusId Current buddy request status
 * @property string           $requestText Request text
 *
 * @property int|float        $buddy_id
 * @property string           $mode
 * @property int|float        $newFriendIdSafe
 * @property string           $new_friend_name_unsafe
 * @property array            $playerArray - optional. Unfortunately - we need full record to get name and capital coordinates for buddy message
 * @property int|float        $playerId
 * @property string           $playerName
 * @property string           $playerNameAndCoordinates
 *
 * @package Buddy
 */
class BuddyContainer extends \EntityContainer {
  /**
   * @var BuddyModel $model
   */
  protected $model;

  protected static $exceptionClass = 'BuddyException';
  protected static $modelClass = 'Buddy\BuddyModel';

  /**
   * Property list
   *
   * @var array $properties
   */
  protected $properties = array(
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
   * BuddyContainer constructor.
   *
   * @param array $user
   */
  public function __construct($user = array()) {
    parent::__construct();

    $this->buddy_id = sys_get_param_id('buddy_id');
    $this->mode = sys_get_param_str('mode');
    $this->newFriendIdSafe = sys_get_param_id('request_user_id');
    $this->new_friend_name_unsafe = sys_get_param_str_unsafe('request_user_name');
    $this->requestText = sys_get_param_str_unsafe('request_text');

    $this->playerArray = $user;

    $this->playerId = function (BuddyContainer $cBuddy) {
      return $cBuddy->playerArray['id'];
    };
    $this->playerName = function (BuddyContainer $cBuddy) {
      return $cBuddy->playerArray['username'];
    };
    $this->playerNameAndCoordinates = function (BuddyContainer $cBuddy) {
      return "{$cBuddy->playerArray['username']} " . uni_render_coordinates($cBuddy->playerArray);
    };
  }

  public function isEmpty() {
    return
      $this->buddyStatusId === null
      ||
      $this->buddyStatusId === BUDDY_REQUEST_NOT_SET
      ||
      empty($this->playerSenderId)
      ||
      empty($this->playerOwnerId);
  }

}
