<?php

namespace Buddy;

use Pimple\ContainerPlus;
use Pimple\GlobalContainer;

/**
 * Class BuddyRoutingParams
 *
 * @property GlobalContainer $gc
 * @property int|float       $buddy_id
 * @property string          $mode
 * @property int|float       $new_friend_id_safe
 * @property string          $new_friend_name_unsafe
 * @property string          $new_request_text
 * @property array           $user
 *
 * @package Pimple
 */
class BuddyRoutingParams extends ContainerPlus {

}
