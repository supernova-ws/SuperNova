<?php

/**
 * Class UBEPlayerList
 *
 * @method UBEPlayer offsetGet($offset)
 */
class UBEPlayerList extends ArrayAccessV2 {

  /**
   * @var UBEPlayer[]
   */
  protected $_container = array();

  // TODO - автоматически определять плеера в аттакеры или дефендеры (?) НЕ ЗАБЫВАТЬ О ВОЗМОЖНОСТИ СМЕНИТЬ СТОРОНУ ПРИ САБЕ!

  /**
   * @param array $report_player_row
   */
  // OK1
  public function init_player_from_report_info($report_player_row) {
    $UBEPlayer = new UBEPlayer();
    $UBEPlayer->load_from_report_player_row($report_player_row);
    $this->_container[$UBEPlayer->player_id_get()] = $UBEPlayer;
  }

  /**
   * @param int $player_id
   */
  // OK1
  public function db_load_player_by_id($player_id) {
    if(isset($this[$player_id])) {
      return;
    }

    $UBEPlayer = new UBEPlayer();
    $UBEPlayer->db_load_by_id($player_id);
    $this->_container[$player_id] = $UBEPlayer;
  }

  /**
   * @return array[][] [UBE_PLAYER_IS_ATTACKER][$player_id] => (array)$planet_db_records
   */
  // OK1
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
   * @return array [$player_id] => (bool)UBE_PLAYER_IS_ATTACKER
   */
  // OK1 - правильно используется через UBE_PLAYER_IS_ATTACKER
  public function get_player_sides() {
    $result = array();
    foreach($this->_container as $player_id => $UBEPlayer) {
      $result[$player_id] = $UBEPlayer->player_side() ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;
    }

    return $result;
  }

}
