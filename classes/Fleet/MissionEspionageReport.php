<?php
/**
 * Created by Gorlum 12.10.2017 13:10
 */

namespace Fleet;

use Common\Traits\TJsonSerializable;


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
  public $attackerPlayerName = '';
  public $attackerPlayerAllyTag = '';
  public $attackerPlanetId = 0;
  public $attackerPlanetName = '';
  public $attackerPlanetGalaxy = 0;
  public $attackerPlanetSystem = 0;
  public $attackerPlanetPlanet = 0;
  public $attackerPlanetPlanetType = PT_NONE;

  public $targetPlayerId = 0;
  public $targetPlayerName = '';
  public $targetPlayerAllyTag = '';
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

  private $simulatorLink = '';

  /**
   * Chance for target to detect spying fleet
   *
   * @var null|float $detectionChance
   */
//  public $detectionChance = null;
  public $rolledChance = null;

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
    $this->attackerPlayerName = $missionData->src_user['username'];
    $this->attackerPlayerAllyTag = $missionData->src_user['ally_tag'];
    $this->attackerPlanetId = $missionData->src_planet['id'];
    $this->attackerPlanetName = $missionData->src_planet['name'];
    $this->attackerPlanetGalaxy = intval($missionData->src_planet['galaxy']);
    $this->attackerPlanetSystem = intval($missionData->src_planet['system']);
    $this->attackerPlanetPlanet = intval($missionData->src_planet['planet']);
    $this->attackerPlanetPlanetType = intval($missionData->src_planet['planet_type']);

    $this->targetPlayerId = $missionData->dst_user['id'];
    $this->targetPlayerName = $missionData->dst_user['username'];
    $this->targetPlayerAllyTag = $missionData->dst_user['ally_tag'];
    $this->targetPlanetId = $missionData->dst_planet['id'];
    $this->targetPlanetName = $missionData->dst_planet['name'];
    $this->targetPlanetGalaxy = intval($missionData->dst_planet['galaxy']);
    $this->targetPlanetSystem = intval($missionData->dst_planet['system']);
    $this->targetPlanetPlanet = intval($missionData->dst_planet['planet']);
    $this->targetPlanetPlanetType = intval($missionData->dst_planet['planet_type']);

    $this->targetSpyLevel = intval(GetSpyLevel($missionData->dst_user));
    $this->attackerSpyLevel = intval(GetSpyLevel($missionData->src_user));

    $this->fleetUnits = sys_unit_str2arr($missionData->fleet['fleet_array']);

    $this->spiedUnits[RES_METAL] = floor($missionData->dst_planet['metal']);
    $this->spiedUnits[RES_CRYSTAL] = floor($missionData->dst_planet['crystal']);
    $this->spiedUnits[RES_DEUTERIUM] = floor($missionData->dst_planet['deuterium']);
    $this->spiedUnits[RES_ENERGY] = floor($missionData->dst_planet['energy_max']);

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

  public function getAntiSpyDiff() {
    $u = ['id' => $this->targetPlayerId];
    $p = [
      'id'       => $this->targetPlanetId,
      'id_owner' => $this->targetPlayerId,
    ];
    $onPlanet = mrc_get_level($u, $p, SHIP_SATELLITE_SPUTNIK, false, true);

    return !empty($onPlanet) && $onPlanet >= 1
      ? floor(pow($onPlanet, 0.52))
      : 0;
  }

  public function getPlanetSpyDiff() {
    return $this->getEmpireSpyDiff() + sqrt($this->getProbesNumber()) - 1 - $this->getAntiSpyDiff();
  }

  /**
   * @param int       $unitId
   * @param int|float $unitAmount
   */
  public function addUnit($unitId, $unitAmount) {
    if (($unitAmount = floor($unitAmount)) >= 1) {
      $this->spiedUnits[intval($unitId)] = floor($unitAmount);
      $this->simulatorLink = '';
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
  public function getDetectionTrashold() {
    return $this->getProbesNumber() * $this->enemyShips / 4 * pow(2, -$this->getEmpireSpyDiff());
  }

  public function rollChance() {
    if ($this->rolledChance === null) {
      $this->rolledChance = mt_rand(0, 99);
    }

    return $this->rolledChance;
  }

  public function isSpyDetected() {
    return $this->rollChance() < $this->getDetectionTrashold();
//    return $this->getDetectionChance() > 99 || $this->getDetectionTrashold() > $this->getDetectionChance();
  }

}
