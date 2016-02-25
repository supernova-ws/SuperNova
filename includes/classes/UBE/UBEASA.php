<?php

/**
 * Class UBEASA
 */
class UBEASA {
  public $attack = 0;
  public $shield = 0;
  public $armor = 0;

  public function _reset() {
    $this->attack = 0;
    $this->shield = 0;
    $this->armor = 0;
  }

  /**
   * @param array $unit_info
   */
  // OK0
  public function load_from_unit_info_param($unit_info) {
    $this->attack = $unit_info[P_ATTACK];
    $this->shield = $unit_info[P_SHIELD];
    $this->armor = $unit_info[P_ARMOR];
  }

  /**
   * @param float[] $ube_bonus_list - array[UBE_ATTACK] => $stat_multiplier
   */
  // OK0
  public function apply_ube_bonuses($ube_bonus_list) {
    $this->attack *= $ube_bonus_list[UBE_ATTACK];
    $this->shield *= $ube_bonus_list[UBE_SHIELD];
    $this->armor *= $ube_bonus_list[UBE_ARMOR];
  }

  /**
   * @param UBEASA $ASA
   */
  public function add_ASA(UBEASA $ASA) {
    $this->attack += $ASA->attack;
    $this->shield += $ASA->shield;
    $this->armor += $ASA->armor;
  }

  /**
   * @param array
   */
  // OK4
  public function add_unit_stats_array(array $unit_stats_array) {
    $this->attack += $unit_stats_array[UBE_ATTACK];
    $this->shield += $unit_stats_array[UBE_SHIELD];
    $this->armor += $unit_stats_array[UBE_ARMOR];
  }

  public function randomize_base_attack($is_simulator = false, $from_percent = UBE_RANDOMIZE_FROM, $to_percent = UBE_RANDOMIZE_TO) {
    $this->unit_attack_randomized = floor($this->attack * (!$is_simulator ? mt_rand($from_percent, $to_percent) / 100 : 1));
  }

  public function randomize_base_shield($is_simulator = false, $from_percent = UBE_RANDOMIZE_FROM, $to_percent = UBE_RANDOMIZE_TO) {
    $this->unit_shield_randomized = floor($this->attack * (!$is_simulator ? mt_rand($from_percent, $to_percent) / 100 : 1));
  }

  public function randomize_base_armor($is_simulator = false, $from_percent = UBE_RANDOMIZE_FROM, $to_percent = UBE_RANDOMIZE_TO) {
    $this->unit_armor_randomized = floor($this->attack * (!$is_simulator ? mt_rand($from_percent, $to_percent) / 100 : 1));
  }
  // TODO - _clone()

}
