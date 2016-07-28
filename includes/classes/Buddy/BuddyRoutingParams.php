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
 * @property int|float       $buddy_id
 * @property string          $mode
 * @property int|float       $newFriendIdSafe
 * @property string          $new_friend_name_unsafe
 * @property string          $new_request_text_unsafe
 * @property array           $playerArray - optional. Unfortunately - we need full record to get name and capital coordinates for buddy message
 * @property int|float       $playerId
 * @property string          $playerName
 * @property string          $playerNameAndCoordinates
 *
 * @package Pimple
 */
class BuddyRoutingParams extends ContainerPlus {

}
