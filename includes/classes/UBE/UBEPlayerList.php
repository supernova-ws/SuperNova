<?php

class UBEPlayerList {

  /**
   * @var array
   */
  protected $players = array();

  /**
   * @return array[][] [UBE_PLAYER_IS_ATTACKER][$player_id] => (array)$planet_db_records
   */
  public function get_db_rows_by_side() {
    $result = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->players as $player_id => $player_data) {
      $result[$player_data[UBE_ATTACKER] ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER][$player_id] = $player_data[UBE_PLAYER_DB_ROW];
    }

    return $result;
  }

  /**
   * @return array [$player_id] => (bool)UBE_PLAYER_IS_ATTACKER
   */
  public function get_player_sides() {
    $result = array();
    foreach($this->players as $player_id => $player_data) {
      $result[$player_id] = $player_data[UBE_ATTACKER] ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;
    }

    return $result;
  }

  /**
   * @return int
   */
  public function get_players_count() {
    return count($this->players);
  }

  /**
   * @param array $player_row
   */
  public function init_player_from_report_info($player_row) {
    $this->players[$player_row['ube_report_player_player_id']] = array(
      UBE_NAME     => $player_row['ube_report_player_name'],
      UBE_ATTACKER => $player_row['ube_report_player_attacker'],

      UBE_BONUSES => array(
        UBE_ATTACK => $player_row['ube_report_player_bonus_attack'],
        UBE_SHIELD => $player_row['ube_report_player_bonus_shield'],
        UBE_ARMOR  => $player_row['ube_report_player_bonus_armor'],
      ),
    );
  }

  /**
   * @param int $player_id
   *
   * @return mixed
   */
  public function get_player_side($player_id) {
    return $this->players[$player_id][UBE_ATTACKER];
  }

  /**
   * @param int $player_id
   *
   * @return mixed
   */
  public function get_player_db_row($player_id) {
    return $this->players[$player_id][UBE_PLAYER_DB_ROW];
  }

  /**
   * @param int  $player_id
   * @param bool $html_encoded
   *
   * @return string
   */
  public function get_player_name($player_id, $html_encoded = false) {
    $player_name = $this->players[$player_id][UBE_NAME];

    return $html_encoded ? htmlentities($player_name, ENT_COMPAT, 'UTF-8') : $player_name;
  }

  /**
   * @param int $player_id
   *
   * @return mixed
   */
  public function get_player_auth_level($player_id) {
    return $this->players[$player_id][UBE_PLAYER_DB_ROW]['authlevel'];
  }

  /**
   * @param int $player_id
   *
   * @return bool
   */
  public function is_set($player_id) {
    return isset($this->players[$player_id]);
  }

  /**
   * @param int $player_id
   */
  public function db_load_player_by_id($player_id) {
    global $ube_convert_techs;

    if($this->is_set($player_id)) {
      return;
    }

    $this->players[$player_id] = array(); // UBE_ATTACKER => $is_attacker

    $player_data = db_user_by_id($player_id, true);
    $this->players[$player_id][UBE_NAME] = $player_data['username'];
    $this->players[$player_id][UBE_AUTH_LEVEL] = $player_data['authlevel'];
    $this->players[$player_id][UBE_PLAYER_DB_ROW] = $player_data;

    $admiral_bonus = mrc_get_level($player_data, false, MRC_ADMIRAL) * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
    foreach($ube_convert_techs as $unit_id => $ube_id) {
      $this->players[$player_id][UBE_BONUSES][$ube_id] += mrc_get_level($player_data, false, $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $admiral_bonus;
    }

  }

  /**
   * @param int  $player_id
   * @param bool $is_attacker
   */
  public function player_switch_side($player_id, $is_attacker) {
    $this->players[$player_id][UBE_ATTACKER] = $this->players[$player_id][UBE_ATTACKER] || $is_attacker;
  }

  /**
   * @param int    $player_id
   * @param string $name
   */
  public function set_player_name($player_id, $name) {
    $this->players[$player_id][UBE_NAME] = $name;
  }

  /**
   * @param int $player_id
   * @param int $unit_id Real unit SNID
   * @param int $unit_count
   * @param int $ube_bonus_id UBE_ATTACK/...
   */
  public function player_bonus_single_add($player_id, $unit_id, $unit_count, $ube_bonus_id) {
    $this->players[$player_id][UBE_BONUSES][$ube_bonus_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
  }

  /**
   * @param int $player_id
   * @param int $ube_bonus_id UBE_ATTACK/...
   *
   * @return int
   */
  public function get_player_bonus_single($player_id, $ube_bonus_id) {
    return
      isset($this->players[$player_id][UBE_BONUSES][$ube_bonus_id])
        ? $this->players[$player_id][UBE_BONUSES][$ube_bonus_id]
        : 0;
  }

}
