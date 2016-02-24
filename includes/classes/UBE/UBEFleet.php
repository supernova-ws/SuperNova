<?php

/**
 * Class UBEFleet
 */
class UBEFleet {

  public $fleet_id = 0;
  public $UBE_OWNER = 0; // REPLACE WITH LINK TO OWNER!
  public $UBE_FLEET_GROUP = 0;

  public $is_attacker = UBE_PLAYER_IS_DEFENDER;
  public $UBE_PLANET = array();
  public $UBE_BONUSES = array(); // [UBE_ATTACK]
  public $UBE_RESOURCES = array();

  public $UBE_COUNT = array();
  public $UBE_TYPE = array();
  public $UBE_CAPACITY = array();
  public $UBE_PRICE = array(); // [$resource_id][$unit_id] => $resource_amount
  public $UBE_AMPLIFY = array();

  public $UBE_ATTACK = array();
  public $UBE_ARMOR = array();
  public $UBE_SHIELD = array();

  public $UBE_CAPTAIN = array();


  /**
   * @param UBEPlayer $player
   */
  // OK3
  public function copy_stats_from_player(UBEPlayer $player) {
    $this->is_attacker = $player->player_side_get();
  }

  /**
   * @param UBEPlayer $player
   */
  // OK3
  public function add_player_bonuses(UBEPlayer $player) {
    // Вычисляем бонус игрока и добавляем его к бонусам флота
    $this->UBE_BONUSES[UBE_ATTACK] += $player->player_bonus_get(UBE_ATTACK);
    $this->UBE_BONUSES[UBE_SHIELD] += $player->player_bonus_get(UBE_SHIELD);
    $this->UBE_BONUSES[UBE_ARMOR] += $player->player_bonus_get(UBE_ARMOR);

  }

  /**
   *
   */
  // OK3
  public function calculate_battle_stats() {
    global $ube_convert_to_techs;

    $this->UBE_PRICE = array();
    foreach($this->UBE_COUNT as $unit_id => $unit_count) {
      if($unit_count <= 0) {
        continue;
      }

      $unit_info = get_unit_param($unit_id);
      // Заполняем информацию о кораблях в информации флота
      $this->UBE_ATTACK[$unit_id] = floor($unit_info[$ube_convert_to_techs[UBE_ATTACK]] * (1 + $this->UBE_BONUSES[UBE_ATTACK]));
      $this->UBE_SHIELD[$unit_id] = floor($unit_info[$ube_convert_to_techs[UBE_SHIELD]] * (1 + $this->UBE_BONUSES[UBE_SHIELD]));
      $this->UBE_ARMOR[$unit_id] = floor($unit_info[$ube_convert_to_techs[UBE_ARMOR]] * (1 + $this->UBE_BONUSES[UBE_ARMOR]));

      $this->UBE_AMPLIFY[$unit_id] = $unit_info[P_AMPLIFY];
      // TODO: Переделать через get_ship_data()
      $this->UBE_CAPACITY[$unit_id] = $unit_info[P_CAPACITY];
      $this->UBE_TYPE[$unit_id] = $unit_info[P_UNIT_TYPE];
      // TODO: Переделать через список ресурсов
      $this->UBE_PRICE[RES_METAL]    [$unit_id] = $unit_info[P_COST][RES_METAL];
      $this->UBE_PRICE[RES_CRYSTAL]  [$unit_id] = $unit_info[P_COST][RES_CRYSTAL];
      $this->UBE_PRICE[RES_DEUTERIUM][$unit_id] = $unit_info[P_COST][RES_DEUTERIUM];
      $this->UBE_PRICE[RES_DARK_MATTER][$unit_id] = $unit_info[P_COST][RES_DARK_MATTER];

    }
  }



  public function load_from_report($fleet_row, UBE $ube) {
    $this->fleet_id = $fleet_row['ube_report_fleet_fleet_id'];
    $this->UBE_OWNER = $fleet_row['ube_report_fleet_player_id'];
    $this->is_attacker = $ube->players[$fleet_row['ube_report_fleet_player_id']]->player_side_get() == UBE_PLAYER_IS_ATTACKER ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;


    $this->UBE_PLANET = array(
      PLANET_ID     => $fleet_row['ube_report_fleet_planet_id'],
      PLANET_NAME   => $fleet_row['ube_report_fleet_planet_name'],
      PLANET_GALAXY => $fleet_row['ube_report_fleet_planet_galaxy'],
      PLANET_SYSTEM => $fleet_row['ube_report_fleet_planet_system'],
      PLANET_PLANET => $fleet_row['ube_report_fleet_planet_planet'],
      PLANET_TYPE   => $fleet_row['ube_report_fleet_planet_planet_type'],
    );

    $this->UBE_BONUSES = array(
      UBE_ATTACK => $fleet_row['ube_report_fleet_bonus_attack'],
      UBE_SHIELD => $fleet_row['ube_report_fleet_bonus_shield'],
      UBE_ARMOR  => $fleet_row['ube_report_fleet_bonus_armor'],
    );

    $this->UBE_RESOURCES = array(
      RES_METAL     => $fleet_row['ube_report_fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['ube_report_fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['ube_report_fleet_resource_deuterium'],
    );
  }

  public function sql_generate_array($ube_report_id) {
    return array(
      $ube_report_id,
      $this->UBE_OWNER,
      $this->fleet_id,

      (float)$this->UBE_PLANET[PLANET_ID],
      "'" . db_escape($this->UBE_PLANET[PLANET_NAME]) . "'",
      (int)$this->UBE_PLANET[PLANET_GALAXY],
      (int)$this->UBE_PLANET[PLANET_SYSTEM],
      (int)$this->UBE_PLANET[PLANET_PLANET],
      (int)$this->UBE_PLANET[PLANET_TYPE],

      (float)$this->UBE_RESOURCES[RES_METAL],
      (float)$this->UBE_RESOURCES[RES_CRYSTAL],
      (float)$this->UBE_RESOURCES[RES_DEUTERIUM],

      (float)$this->UBE_BONUSES[UBE_ATTACK],
      (float)$this->UBE_BONUSES[UBE_SHIELD],
      (float)$this->UBE_BONUSES[UBE_ARMOR],
    );
  }

  public function read_from_row($fleet_row) {
    $this->fleet_id = $fleet_row['fleet_id'];
    $this->UBE_OWNER = $fleet_row['fleet_owner'];
    $this->UBE_FLEET_GROUP = $fleet_row['fleet_group'];

    $fleet_unit_list = Fleet::static_proxy_string_to_array($fleet_row);
    foreach($fleet_unit_list as $unit_id => $unit_count) {
      if(!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $this->UBE_COUNT[$unit_id] = $unit_count;
      }
    }

    $this->UBE_RESOURCES = array(
      RES_METAL     => $fleet_row['fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['fleet_resource_deuterium'],
    );

    $this->UBE_PLANET = array(
//    PLANET_ID => $fleet['fleet_start_id'],
//    PLANET_NAME => $fleet['fleet_start_name'],
      PLANET_GALAXY => $fleet_row['fleet_start_galaxy'],
      PLANET_SYSTEM => $fleet_row['fleet_start_system'],
      PLANET_PLANET => $fleet_row['fleet_start_planet'],
      PLANET_TYPE   => $fleet_row['fleet_start_type'],
    );
  }

}
