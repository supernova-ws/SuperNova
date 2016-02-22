<?php

/**
 * Class UBEFleet
 */
class UBEFleet {

  public $fleet_id = 0;
  public $UBE_OWNER = 0;
  public $UBE_FLEET_GROUP = 0;

  public $UBE_FLEET_TYPE = UBE_DEFENDERS; // UBE_ATTACKERS : UBE_DEFENDERS
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


  public function load_from_report($fleet_row, UBE $ube) {
    $this->fleet_id = $fleet_row['ube_report_fleet_fleet_id'];
    $this->UBE_OWNER = $fleet_row['ube_report_fleet_player_id'];
    $this->UBE_FLEET_TYPE = $ube->players[$fleet_row['ube_report_fleet_player_id']]->player_side_get() == UBE_PLAYER_IS_ATTACKER ? UBE_ATTACKERS : UBE_DEFENDERS;


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
