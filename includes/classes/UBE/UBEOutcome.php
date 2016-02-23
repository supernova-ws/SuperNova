<?php

class UBEOutcome {
  /**
   * [$fleet_id]
   *
   * @var array
   */
  public $outcome_fleets = array(
//[UBE_DEFENCE_RESTORE]
//[UBE_UNITS_LOST]
//[UBE_RESOURCES_LOST]
//[UBE_CARGO_DROPPED]
//[UBE_RESOURCES_LOOTED]
//[UBE_RESOURCES_LOST_IN_METAL]
  );

  public $fleet_attackers = array();
  public $fleet_defenders = array();

  public $capacity_attackers = array();
  public $capacity_defenders = array();

  public function __construct() {
  }

  public function init_fleet($fleet_id, $is_attacker) {
    $this->outcome_fleets[$fleet_id] = array(UBE_UNITS_LOST => array());
    $this->link_fleet_to_side($fleet_id, $is_attacker);
  }

  protected function link_fleet_to_side($fleet_id, $is_attacker) {
    if($is_attacker) {
      $this->fleet_attackers[$fleet_id] = &$this->outcome_fleets[$fleet_id];
    } else {
      $this->fleet_defenders[$fleet_id] = &$this->outcome_fleets[$fleet_id];
    }
  }

  public function db_load_from_report_row($report_row, UBE $ube) {
    $query = doquery("SELECT * FROM {{ube_report_outcome_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];
      $this->load_fleet_from_row($row, $ube->fleet_list[$fleet_id]->UBE_FLEET_TYPE == UBE_ATTACKERS);
    }

    $query = doquery("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
    while($row = db_fetch($query)) {
      $this->load_unit_from_row($row);
    }
  }

  public function sql_generate_fleet_array($fleet_id, $ube_report_id) {
    return array(
      $ube_report_id,
      $fleet_id,

      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOST][RES_METAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOST][RES_CRYSTAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOST][RES_DEUTERIUM],

      (float)$this->outcome_fleets[$fleet_id][UBE_CARGO_DROPPED][RES_METAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_CARGO_DROPPED][RES_CRYSTAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_CARGO_DROPPED][RES_DEUTERIUM],

      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOOTED][RES_METAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOOTED][RES_CRYSTAL],
      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOOTED][RES_DEUTERIUM],

      (float)$this->outcome_fleets[$fleet_id][UBE_RESOURCES_LOST_IN_METAL][RES_METAL],
    );
  }

  /**
   * @param array $row
   * @param bool $is_attacker
   */
  protected function load_fleet_from_row($row, $is_attacker) {
    $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];

    $this->outcome_fleets[$fleet_id] = array(
      UBE_RESOURCES_LOST => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_lost_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_lost_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_lost_deuterium'],
      ),

      UBE_CARGO_DROPPED => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_dropped_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_dropped_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_dropped_deuterium'],
      ),

      UBE_RESOURCES_LOOTED => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_loot_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_loot_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_loot_deuterium'],
      ),

      UBE_RESOURCES_LOST_IN_METAL => array(
        RES_METAL => $row['ube_report_outcome_fleet_resource_lost_in_metal'],
      ),
    );

    $this->link_fleet_to_side($fleet_id, $is_attacker);
  }


  protected function load_unit_from_row($row) {
    $fleet_id = $row['ube_report_outcome_unit_fleet_id'];
    $unit_id = $row['ube_report_outcome_unit_unit_id'];
    // fleet_attackers[$fleet_id] и fleet_defenders[$fleet_id] содержат ССЫЛКИ на outcome_fleets[$fleet_id] - поэтому можно сразу писать в outcome_fleets
    $this->outcome_fleets[$fleet_id][UBE_UNITS_LOST][$unit_id] = $row['ube_report_outcome_unit_lost'];
    $this->outcome_fleets[$fleet_id][UBE_DEFENCE_RESTORE][$unit_id] = $row['ube_report_outcome_unit_restored'];
  }


  public function sql_generate_unit_array(UBEFleet $UBEFleet, &$sql_perform_report_unit, $ube_report_id) {
    $fleet_id = $UBEFleet->fleet_id;

    $unit_sort_order = 0;
    foreach($UBEFleet->UBE_COUNT as $unit_id => $unit_count) {
      if($this->outcome_fleets[$fleet_id][UBE_UNITS_LOST][$unit_id] || $this->outcome_fleets[$fleet_id][UBE_DEFENCE_RESTORE][$unit_id]) {
        $unit_sort_order++;
        $sql_perform_report_unit[] = array(
          $ube_report_id,
          $fleet_id,

          $unit_id,
          (float)$this->outcome_fleets[$fleet_id][UBE_DEFENCE_RESTORE][$unit_id],
          (float)$this->outcome_fleets[$fleet_id][UBE_UNITS_LOST][$unit_id],

          $unit_sort_order,
        );
      }
    }
  }

}
