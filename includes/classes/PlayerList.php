<?php

/**
 * Class PlayerList
 *
 * @method Player offsetGet($offset)
 * @property Player[] $_container
 */
class PlayerList extends ContainerArrayOfObject {

  /**
   * @return Player
   *
   * @version 41a50.9
   */
  public function _createElement() {
    return new Player();
  }

  /**
   * @param int $player_id
   *
   * @version 41a50.9
   */
  protected function db_load_player_by_id($player_id) {
    if(isset($this[$player_id])) {
      return;
    }

    $this[$player_id] = $this->_createElement();
    $this[$player_id]->dbLoad($player_id);
  }


}
