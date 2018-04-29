<?php
/**
 * Created by Gorlum 18.04.2018 16:30
 */

namespace Fleet;


use Core\EntityDb;
use SN;

/**
 * Class Fleet
 * @package Fleet
 *
 * @property int|string $id                       - bigint     -
 * @property int|string $ownerId                  - bigint     - Fleet player owner ID
 * @property int        $fleet_mission            - int        -
 * @property int|string $fleet_amount             - bigint     -
 * @property string     $fleet_array              - mediumtext -
 * @property int        $timeLaunch               - int        - Fleet launched from source planet (unix)
 * @property int        $timeArrive               - int        - Time fleet arrive to destination (unix)
 * @property int        $timeEndStay              - int        - Time when fleet operation on destination complete (if any) (unix)
 * @property int        $timeReturn               - int        - Time fleet would return to source planet (unix)
 * @property int|string $fleet_start_planet_id    - bigint     -
 * @property int        $fleet_start_galaxy       - int        -
 * @property int        $fleet_start_system       - int        -
 * @property int        $fleet_start_planet       - int        -
 * @property int        $fleet_start_type         - int        -
 * @property int|string $fleet_end_planet_id      - bigint     -
 * @property int        $fleet_end_galaxy         - int        -
 * @property int        $fleet_end_system         - int        -
 * @property int        $fleet_end_planet         - int        -
 * @property int        $fleet_end_type           - int        -
 * @property int|string $fleet_resource_metal     - decimal    -
 * @property int|string $fleet_resource_crystal   - decimal    -
 * @property int|string $fleet_resource_deuterium - decimal    -
 * @property int|string $fleet_target_owner       - int        -
 * @property int|string $fleet_group              - varchar    -
 * @property int        $status                   - int        - Current fleet status: flying to destination; returning
 */
class Fleet extends EntityDb {

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = '\\Fleet\\RecordFleet';

  /**
   * @var RecordFleet $_container
   */
  protected $_container;

  protected $_containerTranslateNames = [
    'ownerId' => 'fleet_owner',

    'timeLaunch'  => 'start_time',
    'timeArrive'  => 'fleet_start_time',
    'timeEndStay' => 'fleet_end_stay',
    'timeReturn'  => 'fleet_end_time',

    'status' => 'fleet_mess',
  ];

  /**
   * Information about ships
   *
   * @var array[] $shipInfo
   */
  protected static $shipInfo = [];

  protected $speedPercentTenth = 10;

  /**
   * @var null|array $ownerRecord
   */
  protected $ownerRecord = null;
  /**
   * @var null|array $sourcePlanet
   */
  protected $sourcePlanet = null;


  /**
   * Fleet constructor.
   *
   * @throws \Exception
   */
  public function __construct() {
    parent::__construct();
  }

  // Real fleet actions ------------------------------------------------------------------------------------------------

  /**
   * Forced return fleet to source planet
   *
   * @param int|string $byPlayerId
   *
   * @return bool
   */
  public function returnForce($byPlayerId) {
    if ($this->ownerId != $byPlayerId) {
      return false;
    }

    if ($this->status == FLEET_STATUS_RETURNING) {
      return true;
    }

//    $ReturnFlyingTime = ($this->timeEndStay != 0 && $this->timeArrive < SN_TIME_NOW ? $this->timeArrive : SN_TIME_NOW) - $this->timeLaunch + SN_TIME_NOW + 1;
    $timeToReturn = SN_TIME_NOW - $this->timeLaunch + 1;
    $ReturnFlyingTime = (!empty($this->timeEndStay) && $this->timeArrive < SN_TIME_NOW ? $this->timeArrive : SN_TIME_NOW) + $timeToReturn;

    // TODO - Those two lines should be removed - fleet times should be filtered on interface side
    $this->timeArrive = SN_TIME_NOW;
    !empty($this->timeEndStay) ? $this->timeEndStay = SN_TIME_NOW : false;

    $this->timeReturn = $ReturnFlyingTime;
    $this->status = FLEET_STATUS_RETURNING;

    return $this->dbUpdate();
  }

  // Service functions  -----------------------------------------------------------------------------------------------

  /**
   * @return RecordFleet
   */
  public function _getContainer() {
    return $this->_container;
  }

  /**
   * @param int $shipId
   *
   * @return array
   */
  protected static function getUnitInfo($shipId) {
    if (!isset(static::$shipInfo[$shipId])) {
      static::$shipInfo[$shipId] = get_unit_param($shipId);
    }

    return static::$shipInfo[$shipId];
  }

  /**
   * @param int $resourceId
   *
   * @return float[] - [(int)$shipId => (float)costInMetal]
   */
  public function getShipsBasicCosts($resourceId = RES_METAL) {
    $result = [];
    foreach ($this->getShipListArray() as $shipId => $shipAmount) {
      $result[$shipId] = getStackableUnitsCost([$shipId => 1], $resourceId);
    }

    return $result;
  }

  /**
   * Get cost of single ship in metal
   *
   * @param int $shipId
   *
   * @return int|float
   */
  public function getShipCostInMetal($shipId) {
    return getStackableUnitsCost([$shipId => 1], RES_METAL);
//
//    if(!isset(static::getUnitInfo($shipId)[P_COST_METAL])) {
//      static::$shipInfo[$shipId][P_COST_METAL] = get_unit_cost_in(static::getUnitInfo($shipId)[P_COST], RES_METAL);
//    }
//
//    return static::getUnitInfo($shipId)[P_COST_METAL];
  }

  /**
   * Get fleet cost in metal
   *
   * @return float|int
   */
  public function getCostInMetal() {
    return getStackableUnitsCost($this->getShipListArray(), RES_METAL);
//
//    $result = 0;
//    foreach($this->getShipList() as $shipId => $amount) {
//      $result += $amount * $this->getShipCostInMetal($shipId);
//    }
//
//    return $result;
  }

  /**
   * Get single ship basic capacity
   *
   * @param int $shipId
   *
   * @return int|mixed
   */
  public function getShipCapacity($shipId) {
    if (!isset(static::getUnitInfo($shipId)[P_CAPACITY])) {
      static::$shipInfo[$shipId][P_CAPACITY] = 0;
    }

    return static::getUnitInfo($shipId)[P_CAPACITY];
  }

  /**
   * Get current fleet capacity counting loaded resources and fuel
   *
   * @return float|int
   */
  public function getCapacityActual() {
    $result = 0;
    foreach ($this->getShipListArray() as $shipId => $amount) {
      $result += $amount * $this->getShipCapacity($shipId);
    }

    $travelData = $this->getTravelData();

    $result = max(0, $result - array_sum($this->getResourceList()) - $travelData['consumption']);

    return $result;
  }

  public function isEmpty() {
    return $this->getShipCount() < 1;
  }

  // Using RecordFleet functions ---------------------------------------------------------------------------------------

  /**
   * @param int   $shipSnId
   * @param float $shipCount
   *
   * @throws \Exception
   */
  public function changeShipCount($shipSnId, $shipCount) {
    $this->_getContainer()->changeShipCount($shipSnId, $shipCount);
  }

  /**
   * @param int   $resourceId
   * @param float $resourceCount
   *
   * @throws \Exception
   */
  public function changeResource($resourceId, $resourceCount) {
    $this->_getContainer()->changeResource($resourceId, $resourceCount);
  }

  /**
   * @return float[] - [shipSnId => $shipAmount]
   */
  public function getShipListArray() {
    return $this->_getContainer()->getShipList();
  }

  /**
   * @return float[] - [$resourceSnId => $resourceAmount]
   */
  public function getResourceList() {
    return $this->_getContainer()->getResourceList();
  }

  /**
   * @return float|int
   */
  public function getShipCount() {
    return array_sum($this->getShipListArray());
  }

  /**
   * @param float $multiplier
   *
   * @return int[]|float[]
   */
  public function calcShipLossByMultiplier($multiplier) {
    $result = [];

    foreach ($this->getShipListArray() as $unit_id => $unit_amount) {
      $shipsLost = ceil($unit_amount * $multiplier);
      $result[$unit_id] += $shipsLost;
    }

    return $result;
  }

  /**
   * @param int $missionId
   *
   * @return Fleet
   */
  public function setMission($missionId) {
    $this->fleet_mission = $missionId;
    $this->status = FLEET_STATUS_FLYING;

    return $this;
  }

  /**
   * @param array $playerRecord
   *
   * @return Fleet
   */
  public function setFleetOwnerRecord($playerRecord) {
    $this->ownerRecord = $playerRecord;
    !empty($this->ownerRecord['id']) ? $this->ownerId = $this->ownerRecord['id'] : false;

    return $this;
  }

  /**
   * @return array|false|null
   */
  public function getFleetOwnerRecord() {
    if (!isset($this->ownerRecord['id']) && !empty($this->ownerId)) {
      // Trying to get owner record by id
      empty($this->ownerRecord = db_user_by_id($this->ownerId)) ? $this->ownerRecord = null : false;
    }

    return $this->ownerRecord;
  }

  /**
   * @param array $from
   *
   * @return Fleet
   */
  public function setSourceFromPlanetRecord($from) {
    empty($this->ownerId) && !empty($from['id_owner']) ? $this->ownerId = $from['id_owner'] : false;

    $this->fleet_start_planet_id = !empty($from['id']) && intval($from['id']) ? $from['id'] : null;

    $this->fleet_start_galaxy = $from['galaxy'];
    $this->fleet_start_system = $from['system'];
    $this->fleet_start_planet = $from['planet'];
    $this->fleet_start_type = $from['planet_type'];

    $this->sourcePlanet = $from;

    return $this;
  }


  /**
   * @param $to
   *
   * @return Fleet
   */
  public function setDestinationFromPlanetRecord($to) {
    empty($this->fleet_target_owner) ? $this->fleet_target_owner = !empty($to['id_owner']) && intval($to['id_owner']) ? $to['id_owner'] : 0 : false;

    $this->fleet_end_planet_id = !empty($to['id']) && intval($to['id']) ? $to['id'] : null;

    $this->fleet_end_galaxy = $to['galaxy'];
    $this->fleet_end_system = $to['system'];
    $this->fleet_end_planet = $to['planet'];
    $this->fleet_end_type = $to['planet_type'];

    return $this;
  }

  /**
   * @param array $fleet - list of units [(int)unitId => (float)unitAmount]
   *
   * @return Fleet
   * @throws \Exception
   */
  public function setUnits($fleet) {
    foreach ($fleet as $unit_id => $amount) {
      if (!$amount || !$unit_id) {
        continue;
      }

      if (in_array($unit_id, sn_get_groups('fleet'))) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->changeShipCount($unit_id, $amount);
      } elseif (in_array($unit_id, sn_get_groups('resources_loot'))) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->changeResource($unit_id, $amount);
      }
    }

    return $this;
  }

  /**
   * @param int $speedPercentTenth - fleet speed percent in 10% from 1..10 - i.e. 1 = 10%, 10 = 100%
   *
   * @return Fleet
   */
  public function setSpeedPercentInTenth($speedPercentTenth) {
    $this->speedPercentTenth = max(0, min(10, intval($speedPercentTenth)));

    return $this;
  }

  /**
   * @param int $launchTime   - unix timestamp when fleet leave source planet
   * @param int $stayDuration - seconds how long fleet should stay executing mission task (i.e. HOLD or EXPLORE)
   *
   * @return array
   */
  public function calcTravelTimes($launchTime = SN_TIME_NOW, $stayDuration = 0) {
    $this->timeLaunch = $launchTime;

    $travel_data = $this->getTravelData();

    $this->timeArrive = $this->timeLaunch + $travel_data['duration'];
    $this->timeEndStay = $this->fleet_mission == MT_EXPLORE || $this->fleet_mission == MT_HOLD ? $this->timeArrive + $stayDuration : 0;
    $this->timeReturn = $this->timeArrive + $stayDuration + $travel_data['duration'];

    return $travel_data;
  }


  public function save() {
    return parent::save(); // TODO: Change the autogenerated stub
  }

  /**
   * @return array
   */
  protected function getTravelData() {
    $travel_data = flt_travel_data(
      $this->getFleetOwnerRecord(),
      ['galaxy' => $this->fleet_start_galaxy, 'system' => $this->fleet_start_system, 'planet' => $this->fleet_start_planet,],
      ['galaxy' => $this->fleet_end_galaxy, 'system' => $this->fleet_end_system, 'planet' => $this->fleet_end_planet,],
      $this->getShipListArray(),
      $this->speedPercentTenth
    );

    return $travel_data;
  }

}
