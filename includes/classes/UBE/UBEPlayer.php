<?php

class UBEPlayer {

  /**
   * @var int
   */
  protected $player_id = 0;
  /**
   * @var string
   */
  protected $name = '';
  /**
   * @var int
   */
  protected $auth_level = 0;
  /**
   * @var bool
   */
  protected $is_attacker = false;
  /**
   * [UBE_BONUS]
   *
   * @var array
   */
  protected $ube_bonuses = array();

  /**
   * @var int
   */
  protected $admiral_level = 0;
  /**
   * @var int
   */
  protected $admiral_bonus = 0;

  /**
   * @var array
   */
  protected $db_row = array();

  /**
   * UBEPlayer constructor.
   */
  // OK1
  public function __construct() {
    $this->ube_bonuses = array(
      UBE_ATTACK => 0,
      UBE_SHIELD => 0,
      UBE_ARMOR  => 0,
    );
  }

  /**
   * @param array $report_player_row
   */
  // OK1
  public function load_from_report_player_row($report_player_row) {
    $this->player_id = $report_player_row['ube_report_player_player_id'];
    $this->name = $report_player_row['ube_report_player_name'];
    $this->is_attacker = empty($report_player_row['ube_report_player_attacker']); // TODO - ПРАВИЛЬНО ВЫСТАВЛЯТЬ!

    $this->ube_bonuses = array(
      UBE_ATTACK => $report_player_row['ube_report_player_bonus_attack'],
      UBE_SHIELD => $report_player_row['ube_report_player_bonus_shield'],
      UBE_ARMOR  => $report_player_row['ube_report_player_bonus_armor'],
    );
  }

  /**
   * @param $player_id
   */
  // OK1
  public function db_load_by_id($player_id) {
    global $ube_convert_techs;

    $this->db_row = db_user_by_id($player_id, true);
    $this->name = $this->db_row['username'];
    $this->auth_level = $this->db_row['authlevel'];

    $this->admiral_level = mrc_get_level($this->db_row, false, MRC_ADMIRAL);

    $this->admiral_bonus = $this->admiral_level * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
    foreach($ube_convert_techs as $unit_id => $ube_id) {
      // Вытаскиваем уровень техи, получаем нормированный бонус (НЕ В %!) и прибавляем бонус Адмирала
      $this->ube_bonuses[$ube_id] += mrc_get_level($this->db_row, false, $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $this->admiral_bonus;
    }

  }

  /**
   * @return bool
   */
  // OK1 - правильно используется через UBE_PLAYER_IS_ATTACKER
  public function player_side_get() {
    return $this->is_attacker;
  }

  /**
   * @param int $player_id
   *
   * @return mixed
   */
  // OK1
  public function player_db_row_get() {
    return $this->db_row;
  }

  /**
   * @param bool $html_encoded
   *
   * @return string
   */
  // OK1
  public function player_name_get($html_encoded = false) {
    $player_name = $this->name;

    return $html_encoded ? htmlentities($player_name, ENT_COMPAT, 'UTF-8') : $player_name;
  }

  /**
   * @return int
   */
  // OK1
  public function player_auth_level_get() {
    return $this->auth_level;
  }

  /**
   * @return int
   */
  // OK1
  public function player_id_get() {
    return $this->player_id;
  }

  /**
   * @return bool
   */
  // OK1
  public function player_side() {
    return $this->is_attacker;
  }

  /**
   * @param bool $is_attacker
   */
  // OK1
  public function player_side_switch($is_attacker) {
    $this->is_attacker = $this->is_attacker || $is_attacker;
  }

  /**
   * @param string $name
   */
  // OK1
  public function player_name_set($name) {
    $this->name = $name;
  }

  /**
   * @param int $unit_id Real unit SNID
   * @param int $unit_count
   * @param int $ube_bonus_id UBE_ATTACK/...
   */
  // OK1
  public function player_bonus_add($unit_id, $unit_count, $ube_bonus_id) {
    $this->ube_bonuses[$ube_bonus_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
  }

  /**
   * @param int $ube_bonus_id UBE_ATTACK/...
   *
   * @return int
   */
  // OK1
  public function player_bonus_get($ube_bonus_id) {
    return
      isset($this->ube_bonuses[$ube_bonus_id])
        ? $this->ube_bonuses[$ube_bonus_id]
        : 0;
  }

}
