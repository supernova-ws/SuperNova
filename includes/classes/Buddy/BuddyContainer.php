<?php

namespace Buddy;

/**
 * Class BuddyContainer
 *
 * @method BuddyModel getModel()
 *
 * @property int|string $dbId Entity DB ID
 * @property int|string $playerSenderId Who makes buddy request
 * @property int|string $playerOwnerId To whom this buddy request made
 * @property int        $buddyStatusId Current buddy request status
 * @property string     $requestText Request text
 *
 *
 * @package Buddy
 */
class BuddyContainer extends \Entity\KeyedContainer {

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
