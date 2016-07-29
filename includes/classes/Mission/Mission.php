<?php

namespace Mission;

use Fleet;

/**
 * Created by Gorlum 06.02.2016 22:30
 */
class Mission {
  /**
   * @var int
   */
  public $type = MT_NONE;

  /**
   * @var Fleet $fleet
   */
  public $fleet;

  /**
   * @var array
   */
  public $src_user;
  /**
   * @var array
   */
  public $src_planet;

  /**
   * @var array
   */
  public $dst_user;
  /**
   * @var array
   */
  public $dst_planet;

  /**
   * @var array
   */
  public $fleet_event;

//  protected $validator;

  protected static $conditionsLocal = array();

  /**
   * Mission constructor.
   *
   * @param Fleet $fleet
   */
  public function __construct($fleet) {
//    $this->type = $type;
    $this->fleet = $fleet;

//    $this->src_user = $fleet->dbOwnerRow;
//    $this->src_planet = $fleet->dbSourcePlanetRow;
//
//    $this->dst_user = $fleet->dbTargetOwnerRow;
//    $this->dst_planet = $fleet->dbTargetRow;
//
//    $this->fleet_event = array();

//    $this->validator = new \FleetValidator($fleet);
    /**
     * MT_EXPLORE  - Conditions: OK, Checks: need exp slot tests
     * MT_COLONIZE - Conditions: OK, Checks: OK
     * MT_MISSILE  - Conditions: NOT OK, Checks: NOT OK
     */
  }

  public function validate() {
    try {
      $result = $this->fleet->validator->checkMissionRestrictions(static::$conditionsLocal);
    } catch (\ExceptionFleetInvalid $e) {
      $result = $e->getCode();
    }

    return $result;
  }

}
