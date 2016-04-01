<?php

/**
 * Class UBEPlayer
 *
 */
class UBEPlayer extends Player {
  /**
   * @var bool
   */
  protected $is_attacker = false;

  /**
   * правильно используется через UBE_PLAYER_IS_ATTACKER
   *
   * @return bool
   */
  public function getSide() {
    return $this->is_attacker;
  }

  /**
   * @param bool $is_attacker
   */
  public function setSide($is_attacker) {
    $this->is_attacker = $is_attacker == UBE_PLAYER_IS_ATTACKER || $this->is_attacker == UBE_PLAYER_IS_ATTACKER ? UBE_PLAYER_IS_ATTACKER : $is_attacker;
  }


  /**
   * @param array $report_player_row
   */
  public function load_from_report_player_row($report_player_row) {
    $this->_dbRow['id'] = $report_player_row['ube_report_player_player_id'];
    $this->_dbRow['username'] = $report_player_row['ube_report_player_name'];
    $this->is_attacker = empty($report_player_row['ube_report_player_attacker']); // TODO - ПРАВИЛЬНО ВЫСТАВЛЯТЬ!

    $this->player_bonus->setBonusList(array(
      P_ATTACK => array(
        UNIT_REPORT_PLAYER => $report_player_row['ube_report_player_bonus_attack'],
      ),
      P_SHIELD => array(
        UNIT_REPORT_PLAYER => $report_player_row['ube_report_player_bonus_shield'],
      ),
      P_ARMOR  => array(
        UNIT_REPORT_PLAYER => $report_player_row['ube_report_player_bonus_armor'],
      ),
    ));
  }

}
