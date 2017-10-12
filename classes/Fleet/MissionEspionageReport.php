<?php
/**
 * Created by Gorlum 12.10.2017 13:10
 */

namespace Fleet;

use \Traits\TJsonSerializable;


/**
 * Class MissionEspionageReport
 *
 * Result of MISSION_SPY
 *
 * @package Fleet
 */
class MissionEspionageReport {
  use TJsonSerializable;

  const SIMULATOR_GROUPS = [UNIT_SHIPS, UNIT_DEFENCE];
  const SIMULATOR_UNITS = [TECH_WEAPON, TECH_SHIELD, TECH_ARMOR, RES_METAL, RES_CRYSTAL, RES_DEUTERIUM];

  /**
   * Actual report time
   *
   * @var float $reportTime
   */
  public $reportTime = 0.0;
  /**
   * Fleet arrival time - i.e. when report supported to be made
   * Can differ from actual spy time due to delays in fleet dispatcher routines
   *
   * @var int $fleetTime
   */
  public $fleetTime = 0;

  public $attackerPlayerId = 0;

  public $targetPlayerId = 0;

  public $targetPlanetId = 0;
  public $targetPlanetName = '';
  public $targetPlanetGalaxy = 0;
  public $targetPlanetSystem = 0;
  public $targetPlanetPlanet = 0;
  public $targetPlanetPlanetType = PT_NONE;

  public $targetSpyLevel = 0;
  public $attackerSpyLevel = 0;

  public $fleetUnits = [];

  public $spiedUnits = [];

  public $simulatorLink = '';

  /**
   * Chance for target to detect spying fleet
   *
   * @var null|float $detectionChance
   */
  public $detectionChance = null;

  public $enemyShips = 0;

  /**
   * MissionEspionageReport constructor.
   *
   * @param MissionData $missionData
   */
  public function __construct(MissionData $missionData) {
    $this->reportTime = microtime(true);
    $this->fleetTime = $missionData->fleet['fleet_end_time'];

    $this->attackerPlayerId = $missionData->src_user['id'];

    $this->targetPlayerId = $missionData->dst_user['id'];

    $this->targetPlanetId = $missionData->dst_planet['id'];
    $this->targetPlanetName = $missionData->dst_planet['name'];
    $this->targetPlanetGalaxy = $missionData->dst_planet['galaxy'];
    $this->targetPlanetSystem = $missionData->dst_planet['system'];
    $this->targetPlanetPlanet = $missionData->dst_planet['planet'];
    $this->targetPlanetPlanetType = $missionData->dst_planet['planet_type'];

    $this->targetSpyLevel = GetSpyLevel($missionData->dst_user);
    $this->attackerSpyLevel = GetSpyLevel($missionData->src_user);

    $this->fleetUnits = sys_unit_str2arr($missionData->fleet['fleet_array']);

    $this->spiedUnits[RES_METAL] = $missionData->dst_planet['metal'];
    $this->spiedUnits[RES_CRYSTAL] = $missionData->dst_planet['crystal'];
    $this->spiedUnits[RES_DEUTERIUM] = $missionData->dst_planet['deuterium'];
    $this->spiedUnits[RES_ENERGY] = $missionData->dst_planet['energy_max'];

    $this->enemyShips = 0;
    foreach (sn_get_groups('fleet') as $unit_id) {
      $this->enemyShips += max(0, mrc_get_level($missionData->dst_user, $missionData->dst_planet, $unit_id, false, true));
    }

  }


  public function getEmpireSpyDiff() {
    return $this->attackerSpyLevel - $this->targetSpyLevel;
  }

  /**
   * @return float|int
   */
  public function getProbesNumber() {
    return !empty($this->fleetUnits[SHIP_SPY]) && $this->fleetUnits[SHIP_SPY] >= 1 ? floor($this->fleetUnits[SHIP_SPY]) : 0;
  }

  public function getPlanetSpyDiff() {
    return $this->getEmpireSpyDiff() + sqrt($this->getProbesNumber()) - 1;
  }

  /**
   * @param int       $unitId
   * @param int|float $unitAmount
   */
  public function addUnit($unitId, $unitAmount) {
    if (($unitAmount = floor($unitAmount)) >= 1) {
      $this->spiedUnits[$unitId] = $unitAmount;
      $this->simulatorLink;
    }
  }

  public function getSimulatorLink() {
    if (empty($this->simulatorLink)) {
      $combat_pack[0] = [];
      foreach ($this->spiedUnits as $unitId => $unitAmount) {
        $unitGroup = get_unit_param($unitId, P_UNIT_TYPE);
        if (in_array($unitGroup, static::SIMULATOR_GROUPS) || in_array($unitId, static::SIMULATOR_UNITS)) {
          $combat_pack[0][$unitId] = $unitAmount;
        }
      }
      $this->simulatorLink = sn_ube_simulator_encode_replay($combat_pack, 'D');
    }

    return $this->simulatorLink;
  }

  /**
   * Chance for target to detect spying fleet
   *
   * @return float|null
   */
  public function getDetectionChance() {
    return $this->getProbesNumber() * $this->enemyShips / 4 * pow(2, -$this->getEmpireSpyDiff());
  }

}
