<?php
/**
 * Created by Gorlum 18.04.2018 16:30
 */

namespace Fleet;


use Core\EntityDb;

/**
 * Class Fleet
 * @package Fleet
 *
 * @property int|string $id                       - bigint     -
 * @property int|string $fleet_owner              - bigint     -
 * @property int        $fleet_mission            - int        -
 * @property int|string $fleet_amount             - bigint     -
 * @property string     $fleet_array              - mediumtext -
 * @property int        $fleet_start_time         - int        -
 * @property int|string $fleet_start_planet_id    - bigint     -
 * @property int        $fleet_start_galaxy       - int        -
 * @property int        $fleet_start_system       - int        -
 * @property int        $fleet_start_planet       - int        -
 * @property int        $fleet_start_type         - int        -
 * @property int        $fleet_end_time           - int        -
 * @property int        $fleet_end_stay           - int        -
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
 * @property int        $fleet_mess               - int        -
 * @property int        $start_time               - int        -
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

  /**
   * Information about ships
   *
   * @var array[] $shipInfo
   */
  protected static $shipInfo = [];


  /**
   * Fleet constructor.
   */
  public function __construct() {
    parent::__construct();
  }

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
    foreach ($this->getShipList() as $shipId => $shipAmount) {
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
    return getStackableUnitsCost($this->getShipList(), RES_METAL);
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
    foreach ($this->getShipList() as $shipId => $amount) {
      $result += $amount * $this->getShipCapacity($shipId);
    }

    $result = max(0, $result - array_sum($this->getResourceList()));

    return $result;
  }

  public function isEmpty() {
    return parent::isEmpty();
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
  public function getShipList() {
    return $this->_getContainer()->getShipList();
  }

  /**
   * @return float[] - [$resourceSnId => $resourceAmount]
   */
  public function getResourceList() {
    return $this->_getContainer()->getResourceList();
  }

  public function getShipCount() {
    return $this->_getContainer()->getShipCount();
//    return array_sum($this->getShipList());
  }

}
