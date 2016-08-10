<?php

namespace Buddy;

use Common\ContainerPlus;
use Common\GlobalContainer;

/**
 * Class BuddyRoutingParams
 *
 * Hints to enable IDE autocomplete. Otherwise - useless
 *
 * @property GlobalContainer $gc
 * @property BuddyModel      $model
 * @property int|float       $buddy_id
 * @property string          $mode
 * @property int|float       $newFriendIdSafe
 * @property string          $new_friend_name_unsafe
 * @property string          $requestText
 * @property array           $playerArray - optional. Unfortunately - we need full record to get name and capital coordinates for buddy message
 * @property int|float       $playerId
 * @property string          $playerName
 * @property string          $playerNameAndCoordinates
 *
 * @package Pimple
 */
class BuddyRoutingParams extends \V2PropertyContainer {

  /**
   * BuddyRoutingParams constructor.
   *
   * @param GlobalContainer $gc
   * @param array           $user
   */
  public function __construct($gc, $user) {
    $this->gc = $gc;
    $this->model = $gc->buddy;
    $this->buddy_id = sys_get_param_id('buddy_id');
    $this->mode = sys_get_param_str('mode');
    $this->newFriendIdSafe = sys_get_param_id('request_user_id');
    $this->new_friend_name_unsafe = sys_get_param_str_unsafe('request_user_name');
    $this->requestText = sys_get_param_str_unsafe('request_text');
    $this->playerArray = $user;

    $this->playerId = function (BuddyRoutingParams $cBuddy) {
      return $cBuddy->playerArray['id'];
    };
    $this->playerName = function (BuddyRoutingParams $cBuddy) {
      return $cBuddy->playerArray['username'];
    };
    $this->playerNameAndCoordinates = function (BuddyRoutingParams $cBuddy) {
      return "{$cBuddy->playerArray['username']} " . uni_render_coordinates($cBuddy->playerArray);
    };
  }

}
