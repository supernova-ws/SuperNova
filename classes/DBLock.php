<?php


/**
 * Class DBLock
 */
class DBLock {

  /**
   * @var DBRow
   */
  public $initiator;

  /**
   * Array of locks for player
   * [$table.'+'.$linked_player_id] => array($table, $linked_player_id)
   *
   * @var string[][]
   */
  public $lock_player = array();

  /**
   * DBLock constructor.
   *
   * @param DBRow $initiator - Object that initiates lock
   */
  public function __construct($initiator) {
    $this->initiator = $initiator;
  }


  public function addPlayerLock($table, $playerIdFieldName) {
    $this->lock_player[$table . '-' . $playerIdFieldName] = array($table, $playerIdFieldName);
  }
}
