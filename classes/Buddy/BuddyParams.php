<?php
/**
 * Created by Gorlum 12.08.2016 21:12
 */

namespace Buddy;
use Common\ContainerPlus;

/**
 * Class BuddyParams
 * @package Buddy
 *
 * @property int|float        $buddy_id
 * @property string           $mode
 * @property int|float        $newFriendIdSafe
 * @property string           $new_friend_name_unsafe
 * @property string           $request_text_unsafe
 *
 * @property array            $playerArray - optional. Unfortunately - we need full record to get name and capital coordinates for buddy message
 * @property int|float        $playerId
 * @property string           $playerName
 * @property string           $playerNameAndCoordinates
 */
class BuddyParams extends ContainerPlus  {

  /**
   * BuddyParams constructor.
   *
   * @param array $values
   */
  public function __construct(array $values = array()) {
    parent::__construct($values);

    $this->buddy_id = sys_get_param_id('buddy_id');
    $this->mode = sys_get_param_str('mode');
    $this->newFriendIdSafe = sys_get_param_id('request_user_id');
    $this->new_friend_name_unsafe = sys_get_param_str_unsafe('request_user_name');
    $this->request_text_unsafe = sys_get_param_str_unsafe('request_text');

    $this->playerId = function (BuddyParams $cBuddy) {
      return $cBuddy->playerArray['id'];
    };
    $this->playerName = function (BuddyParams $cBuddy) {
      return $cBuddy->playerArray['username'];
    };
    $this->playerNameAndCoordinates = function (BuddyParams $cBuddy) {
      return "{$cBuddy->playerArray['username']} " . uni_render_coordinates($cBuddy->playerArray);
    };
  }

}
