<?php

class UBEOutcome {

  /**
   * [UBE_xxx]
   *
   * @var array
   */
  public $outcome = array();

  /**
   * [$fleet_id]
   *
   * @var array
   */
  public $outcome_fleets = array();

  public function __construct() {
  }

  public function load_from_report_row($report_row) {
    $this->outcome = array(
      UBE_ATTACKERS => array(),
      UBE_DEFENDERS => array(),
    );
  }

}
