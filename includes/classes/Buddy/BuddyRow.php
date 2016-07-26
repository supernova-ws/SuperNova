<?php


namespace Buddy;

use PropertyHiderInArray;

/**
 * Class Buddy\BuddyRow
 * @property int|float $dbId Buddy record DB ID
 * @property int|float $playerSenderId Who makes buddy request
 * @property int|float $playerOwnerId To whom this buddy request made
 * @property int       $buddyStatusId Current buddy request status
 * @property string    $requestText Request text
 */
class BuddyRow extends PropertyHiderInArray {
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

}
