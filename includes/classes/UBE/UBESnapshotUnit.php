<?php

/**
 * Class UBESnapshotUnit
 */
class UBESnapshotUnit {
  public $unit_id = 0;
  public $count = 0;
  public $unit_count_boom = 0;
  public $pool_attack = 0;
  public $pool_shield = 0;
  public $pool_armor = 0;
  public $unit_randomized_attack = 0;
  public $unit_randomized_shield = 0;
  public $unit_randomized_armor = 0;

  /**
   * UBESnapshotUnit constructor.
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function __construct() {
  }

  /**
   * @param UBEUnit $UBEUnit
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function init_from_UBEUnit(UBEUnit $UBEUnit) {
    $this->unit_id = $UBEUnit->unit_id;
    $this->count = $UBEUnit->count;
    $this->unit_count_boom = $UBEUnit->unit_count_boom;
    $this->pool_attack = $UBEUnit->pool_attack;
    $this->pool_shield = $UBEUnit->pool_shield;
    $this->pool_armor = $UBEUnit->pool_armor;
    $this->unit_randomized_attack = $UBEUnit->unit_randomized_attack;
    $this->unit_randomized_shield = $UBEUnit->unit_randomized_shield;
    $this->unit_randomized_armor = $UBEUnit->unit_randomized_armor;
  }

  /**
   * @param array $report_unit_row
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function init_from_report_unit_row(array $report_unit_row) {
    $this->unit_id = $report_unit_row['ube_report_unit_unit_id'];
    $this->count = $report_unit_row['ube_report_unit_count'];
    $this->unit_count_boom = $report_unit_row['ube_report_unit_boom'];
    $this->pool_attack = $report_unit_row['ube_report_unit_attack'];
    $this->pool_shield = $report_unit_row['ube_report_unit_shield'];
    $this->pool_armor = $report_unit_row['ube_report_unit_armor'];
    $this->unit_randomized_attack = $report_unit_row['ube_report_unit_attack_base'];
    $this->unit_randomized_shield = $report_unit_row['ube_report_unit_shield_base'];
    $this->unit_randomized_armor = $report_unit_row['ube_report_unit_armor_base'];
  }

  //    REPORT RENDER ==================================================================================================
  /**
   * @param UBESnapshotUnit $prev_unit_snapshot
   *
   * @return array
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function report_render_unit(UBESnapshotUnit $prev_unit_snapshot) {
    global $lang;

    $shields_original = $this->unit_randomized_shield * $prev_unit_snapshot->count; //

    return array(
      'ID'          => $this->unit_id, //
      'NAME'        => $lang['tech'][$this->unit_id], //
      'ATTACK'      => pretty_number($this->pool_attack), //
      'SHIELD'      => pretty_number($shields_original),
      'SHIELD_LOST' => pretty_number($shields_original - $this->pool_shield), //
      'ARMOR'       => pretty_number($prev_unit_snapshot->pool_armor), //
      'ARMOR_LOST'  => pretty_number($prev_unit_snapshot->pool_armor - $this->pool_armor), //
      'UNITS'       => pretty_number($prev_unit_snapshot->count), //
      'UNITS_LOST'  => pretty_number($prev_unit_snapshot->count - $this->count), //
      'UNITS_BOOM'  => pretty_number($this->unit_count_boom), //
    );
  }

}
