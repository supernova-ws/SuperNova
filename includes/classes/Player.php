<?php

/**
 * Class Player
 *
 * $player_id   => $db_row['id']
 * $name        => $this->db_row['username']
 * $auth_level  => db_row['authlevel']
 */
class Player extends UnitContainer {
  /**
   * Type of this location
   *
   * @var int $locationType
   */
  public static $locationType = LOC_USER;
  /**
   * @var int $db_id
   */
  protected $db_id = 0;
  /**
   * @var UnitList $unitList
   */
  public $unitList = null;


  /**
   * @var Bonus $player_bonus
   */
  public $player_bonus = null;
  /**
   * @var array
   */
  protected $db_row = array();

  /**
   * Player constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->player_bonus = new Bonus();
  }

  /**
   * @param $player_id
   */
  public function db_load_by_id($player_id) {
    $this->db_row = db_user_by_id($player_id, true);
    $this->db_id = $player_id;

    // Загружаем юниты
    $this->unitList->loadByLocation($this);

    // Высчитываем бонусы
    $this->player_bonus->add_unit(MRC_ADMIRAL, mrc_get_level($this->db_row, false, MRC_ADMIRAL));
    $this->player_bonus->add_unit(TECH_WEAPON, mrc_get_level($this->db_row, false, TECH_WEAPON));
    $this->player_bonus->add_unit(TECH_SHIELD, mrc_get_level($this->db_row, false, TECH_SHIELD));
    $this->player_bonus->add_unit(TECH_ARMOR, mrc_get_level($this->db_row, false, TECH_ARMOR));
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
   * @param int $player_id
   *
   * @return mixed
   */
  public function player_db_row_get() {
    return $this->db_row;
  }

  /**
   * @param string $name
   */
  public function player_name_set($name) {
    $this->db_row['username'] = $name;
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

  public function getCapitalPlanetId() {
    return $this->db_row['id_planet'];
  }

  public function getStatTotalPoints() {
    return $this->db_row['total_points'];
  }

}
