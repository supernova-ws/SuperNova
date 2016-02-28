<?php

/**
 * Class UBEPlayerList
 *
 * @method UBEPlayer offsetGet($offset)
 * @property UBEPlayer[] $_container
 */
class UBEPlayerList extends ArrayAccessV2 {

  // TODO - автоматически определять плеера в аттакеры или дефендеры (?) НЕ ЗАБЫВАТЬ О ВОЗМОЖНОСТИ СМЕНИТЬ СТОРОНУ ПРИ САБЕ!

  /**
   * @param array $report_player_row
   */
  public function init_player_from_report_info($report_player_row) {
    $UBEPlayer = new UBEPlayer();
    $UBEPlayer->load_from_report_player_row($report_player_row);
    $this[$UBEPlayer->getDbId()] = $UBEPlayer;
  }

  /**
   * @param int $player_id
   */
  public function db_load_player_by_id($player_id) {
    if(isset($this[$player_id])) {
      return;
    }

    $UBEPlayer = new UBEPlayer();
    $UBEPlayer->db_load_by_id($player_id);
    $this[$player_id] = $UBEPlayer;
  }

  /**
   * @return array[][] [UBE_PLAYER_IS_ATTACKER][$player_id] => (array)$planet_db_records
   */
  public function get_player_rows_by_side() {
    $result = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->_container as $player_id => $UBEPlayer) {
      $result[$UBEPlayer->player_side() ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER][$player_id] = $UBEPlayer->player_db_row_get();
    }

    return $result;
  }


  /**
   * @param bool $side UBE_PLAYER_IS_ATTACKER|UBE_PLAYER_IS_DEFENDER
   *
   * @return UBEPlayer
   */
  public function get_first_player_on_side($side) {
    $result = null;
    foreach($this->_container as $player_id => $UBEPlayer) {
      if($UBEPlayer->player_side() == $side) {
        $result = $UBEPlayer;
        break;
      }
    }

    return $result;
  }

  /**
   *
   * правильно используется через UBE_PLAYER_IS_ATTACKER
   *
   * @return array [$player_id] => (bool)UBE_PLAYER_IS_ATTACKER
   */
  public function get_player_sides() {
    $result = array();
    foreach($this->_container as $player_id => $UBEPlayer) {
      $result[$player_id] = $UBEPlayer->player_side() ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;
    }

    return $result;
  }

}
