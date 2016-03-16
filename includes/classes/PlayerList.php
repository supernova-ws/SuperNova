<?php

/**
 * Class PlayerList
 *
 * @method Player offsetGet($offset)
 * @property Player[] $_container
 */
class PlayerList extends ArrayAccessV2 {

  /**
   * @return Player
   *
   * @version 41a6.0
   */
  public function _createElement() {
    return new Player();
  }

  /**
   * @param int $player_id
   *
   * @version 41a6.0
   */
  protected function db_load_player_by_id($player_id) {
    if(isset($this[$player_id])) {
      return;
    }

    $this[$player_id] = $this->_createElement();
    $this[$player_id]->db_load_by_id($player_id);
  }


}
