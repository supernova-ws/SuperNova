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

  public function load_from_report_row($report_row) {
  }

}
