<?php

/**
 * Class UBEPlayer
 *
 * $player_id   => $db_row['id']
 * $name        => $this->db_row['username']
 * $auth_level  => db_row['authlevel']
 */
class UBEPlayer {

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
  public function __construct() {
    $this->ube_bonuses = array(
      UBE_ATTACK => 0,
      UBE_SHIELD => 0,
      UBE_ARMOR  => 0,
    );
  }

  /**
   *
   */
  public function getCapitalPlanetId() {
    return $this->db_row['id_planet'];
  }

  public function getStatTotalPoints() {
    return $this->db_row['total_points'];
  }

  /**
   * Меняет активную планету игрока на столицу, если активная планета равна $captured_planet_id
   *
   * @param int $captured_planet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function db_user_change_active_planet_to_capital($captured_planet_id) {
    $user_id = $this->getDbId();
    return doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = {$user_id} AND `current_planet` = {$captured_planet_id};");
  }

  public function getColonyCount() {
    return $this->db_row[UNIT_PLAYER_COLONIES_CURRENT] = isset($this->db_row[UNIT_PLAYER_COLONIES_CURRENT]) ? $this->db_row[UNIT_PLAYER_COLONIES_CURRENT] : max(0, db_planet_count_by_type($this->db_row['id']) - 1);
  }

  /**
   * @param int $astrotech
   *
   * @return int|mixed
   */
  public function getColonyMaxCount($astrotech = -1) {
    global $config;

    if($astrotech == -1) {
      if(!isset($this->db_row[UNIT_PLAYER_COLONIES_MAX])) {

        $expeditions = get_player_max_expeditons($this->db_row);
        $astrotech = mrc_get_level($this->db_row, false, TECH_ASTROTECH);
        $colonies = $astrotech - $expeditions;

        $this->db_row[UNIT_PLAYER_COLONIES_MAX] = $config->player_max_colonies < 0 ? $colonies : min($config->player_max_colonies, $colonies);
      }

      return $this->db_row[UNIT_PLAYER_COLONIES_MAX];
    } else {
      $expeditions = get_player_max_expeditons($this->db_row, $astrotech);
      $colonies = $astrotech - $expeditions;

      return $config->player_max_colonies < 0 ? $colonies : min($config->player_max_colonies, $colonies);
    }
  }

  /**
   * @param array $report_player_row
   */
  public function load_from_report_player_row($report_player_row) {
    $this->db_row['id'] = $report_player_row['ube_report_player_player_id'];
    $this->db_row['username'] = $report_player_row['ube_report_player_name'];
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
  public function db_load_by_id($player_id) {
    global $ube_convert_techs;

    $this->db_row = db_user_by_id($player_id, true);

    $this->admiral_level = mrc_get_level($this->db_row, false, MRC_ADMIRAL);

    $this->admiral_bonus = $this->admiral_level * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
    foreach($ube_convert_techs as $unit_id => $ube_id) {
      // Вытаскиваем уровень техи, получаем нормированный бонус (НЕ В %!) и прибавляем бонус Адмирала
      $this->ube_bonuses[$ube_id] += mrc_get_level($this->db_row, false, $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $this->admiral_bonus;
    }

  }

  /**
   *
   * правильно используется через UBE_PLAYER_IS_ATTACKER
   *
   * @return bool
   */
  public function player_side_get() {
    return $this->is_attacker;
  }

  /**
   * @param int $player_id
   *
   * @return mixed
   */
  public function player_db_row_get() {
    return $this->db_row;
  }

  /**
   * @param bool $html_encoded
   *
   * @return string
   */
  public function player_name_get($html_encoded = false) {
    $player_name = $this->db_row['username'];

    return $html_encoded ? htmlentities($player_name, ENT_COMPAT, 'UTF-8') : $player_name;
  }

  /**
   * @return int
   */
  public function player_auth_level_get() {
    return $this->db_row['authlevel'];
  }

  /**
   * @return int
   */
  public function getDbId() {
    return $this->db_row['id'];
  }

  /**
   * @return bool
   */
  public function player_side() {
    return $this->is_attacker;
  }

  /**
   * @param bool $is_attacker
   */
  public function player_side_switch($is_attacker) {
    $this->is_attacker = $this->is_attacker || $is_attacker;
  }

  /**
   * @param string $name
   */
  public function player_name_set($name) {
    $this->db_row['username'] = $name;
  }

  /**
   * @param int $unit_id Real unit SNID
   * @param int $unit_count
   * @param int $ube_bonus_id UBE_ATTACK/...
   */
  public function player_bonus_add($unit_id, $unit_count, $ube_bonus_id) {
    $this->ube_bonuses[$ube_bonus_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
  }

  /**
   * @param int $ube_bonus_id UBE_ATTACK/...
   *
   * @return int
   */
  public function player_bonus_get($ube_bonus_id) {
    return
      isset($this->ube_bonuses[$ube_bonus_id])
        ? $this->ube_bonuses[$ube_bonus_id]
        : 0;
  }

  public function player_bonus_get_all() {
    return $this->ube_bonuses;
  }

}
