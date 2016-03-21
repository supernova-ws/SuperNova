<?php

/**
 * Class UBEPlayerList
 *
 * @method UBEPlayer offsetGet($offset)
 * @property UBEPlayer[] $_container
 */
class UBEPlayerList extends PlayerList {

  /**
   * Maximum auth_level of all players in list
   *
   * @var int $authLevelMax
   */
  public $authLevelMax = 0;


  /**
   * @return UBEPlayer
   *
   * @version 41a6.16
   */
  public function _createElement() {
    return new UBEPlayer();
  }


  // TODO - автоматически определять плеера в аттакеры или дефендеры (?) НЕ ЗАБЫВАТЬ О ВОЗМОЖНОСТИ СМЕНИТЬ СТОРОНУ ПРИ САБЕ!

  /**
   * @param array $report_player_row
   */
  public function init_player_from_report_info($report_player_row) {
    $UBEPlayer = new UBEPlayer();
    $UBEPlayer->load_from_report_player_row($report_player_row);
    $this[$UBEPlayer->dbId] = $UBEPlayer;

    $this->authLevelMax = max($this->authLevelMax, $UBEPlayer->authLevel);
  }


  public function db_load_player_by_id($player_id, $is_attacker) {
    parent::db_load_player_by_id($player_id);

    $this[$player_id]->setSide($is_attacker);
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
      $result[$UBEPlayer->getSide() ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER][$player_id] = $UBEPlayer->getDbRow();
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
      if($UBEPlayer->getSide() == $side) {
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
      $result[$player_id] = $UBEPlayer->getSide() ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;
    }

    return $result;
  }

  public function ubeLoadPlayersAndSetSideFromFleetIdList(array $added_fleets, UBEFleetList $fleetList, $side = UBE_PLAYER_IS_DEFENDER) {
    foreach($added_fleets as $fleet_id) {
      $this->db_load_player_by_id($fleetList[$fleet_id]->owner_id, $side);
    }
  }

}
