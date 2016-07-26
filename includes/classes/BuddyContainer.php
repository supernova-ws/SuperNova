<?php


/**
 * Class BuddyContainer
 * @property int|float $dbId
 * @property int|float $playerSenderId
 * @property int|float $playerOwnerId
 * @property int       $buddyStatusId
 * @property string    $requestText
 */
class BuddyContainer extends PropertyHiderInArray {
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
