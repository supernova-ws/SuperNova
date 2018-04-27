<?php
/**
 * Created by Gorlum 07.12.2017 14:38
 */

namespace Fleet;


use Core\GlobalContainer;
use DBAL\ActiveRecord;

/**
 * Class RecordFleet
 * @package Fleet
 *
 * property int|string $fleet_id                 - bigint     -
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
 *
 */
class RecordFleet extends ActiveRecord {
  protected static $_primaryIndexField = 'fleet_id';

  protected static $_tableName = 'fleets';

  /**
   * List of fleet ships
   *
   * @var float[] $shipList
   */
  protected $shipList = [];

  /**
   * @var float[] $resources
   */
  protected $resources = [
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  ];

  /**
   * RecordFleet constructor.
   *
   * @param GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);
  }

  /**
   * @inheritdoc
   */
  protected function fromProperties(array $properties) {
    parent::fromProperties($properties);

    $this->shipList = !empty($this->fleet_array) ? sys_unit_str2arr($this->fleet_array) : [];

    $this->resources = [
      RES_METAL     => !empty($this->fleet_resource_metal) ? floatval($this->fleet_resource_metal) : 0,
      RES_CRYSTAL   => !empty($this->fleet_resource_crystal) ? floatval($this->fleet_resource_crystal) : 0,
      RES_DEUTERIUM => !empty($this->fleet_resource_deuterium) ? floatval($this->fleet_resource_deuterium) : 0,
    ];
  }

  /**
   * @inheritdoc
   */
  public function update() {
    if($this->getShipCount() < 1) {
      return $this->delete();
    } else {
      return parent::update();
    }
  }

//  /**
//   * @param int $shipId
//   *
//   * @return int|float
//   */
//  public function getShipCostInMetal($shipId) {
//    return !empty(static::$shipInfo[$shipId][P_COST_METAL]) ? static::$shipInfo[$shipId][P_COST_METAL] : 0;
//  }
//
//  /**
//   * Get fleet cost in metal
//   *
//   * @return float|int
//   */
//  public function getCostInMetal() {
//    $result = 0;
//    foreach($this->shipList as $shipId => $amount) {
//      $result += $amount * $this->getShipCostInMetal($shipId);
//    }
//
//    return $result;
//  }
//
//  /**
//   * @param int $shipId
//   *
//   * @return int|mixed
//   */
//  public function getShipCapacity($shipId) {
//    return !empty(static::$shipInfo[$shipId][P_CAPACITY]) ? static::$shipInfo[$shipId][P_CAPACITY] : 0;
//  }
//
//  /**
//   * @return float|int
//   */
//  public function getCapacity() {
//    $result = 0;
//    foreach($this->shipList as $shipId => $amount) {
//      $result += $amount * $this->getShipCapacity($shipId);
//    }
//
//    $result = max(0, $result - array_sum($this->resources));
//
//    return $result;
//  }

  /**
   * @param int   $shipSnId
   * @param float $shipCount
   *
   * @throws \Exception
   */
  public function changeShipCount($shipSnId, $shipCount) {
    !isset($this->shipList[$shipSnId]) ? $this->shipList[$shipSnId] = 0 : false;

    $shipCount = floor($shipCount);

    if($this->shipList[$shipSnId] + $shipCount < 0) {
      throw new \Exception("Trying to deduct more ships [{$shipSnId}] '{$shipCount}' when fleet [{$this->id}] has only {$this->shipList[$shipSnId]}");
    }

    $this->shipList[$shipSnId] += $shipCount;
    if($this->shipList[$shipSnId] < 1) {
      unset($this->shipList[$shipSnId]);
    }

    $this->fleet_array = sys_unit_arr2str($this->shipList);
    $this->fleet_amount = $this->getShipCount();
  }

  /**
   * @param int   $resourceId
   * @param float $resourceCount
   *
   * @throws \Exception
   */
  public function changeResource($resourceId, $resourceCount) {
    if (empty($resourceCount)) {
      return;
    }

    if (!array_key_exists($resourceId, $this->resources)) {
      throw new \Exception("FLEET ERROR! Trying to change unknown resource type [{$resourceId}] '{$resourceCount}' on fleet [{$this->id}]");
    }

    $resourceCount = ceil($resourceCount);

    if($this->resources[$resourceId] + $resourceCount < 0) {
      throw new \Exception("FLEET ERROR! Trying to deduct more resources [{$resourceId}] '{$resourceCount}' when fleet [{$this->id}] has only {$this->resources[$resourceId]}");
    }

    $this->resources[$resourceId] += $resourceCount;

    $fieldName = 'fleet_resource_' . pname_resource_name($resourceId);
    $this->inc()->$fieldName = $resourceCount;

//    $this->fleet_resource_metal = $this->resources[RES_METAL];
//    $this->fleet_resource_crystal = $this->resources[RES_CRYSTAL];
//    $this->fleet_resource_deuterium = $this->resources[RES_DEUTERIUM];
  }



  public function isEmpty() {
    return $this->getShipCount() < 1 && $this->getResourceCount() < 1;
  }

  public function getShipCount() {
    return array_sum($this->getShipList());
  }

  // Getters/Setters ---------------------------------------------------------------------------------------------------
  /**
   * @return float[] - [shipSnId => $shipAmount]
   */
  public function getShipList() {
    return $this->shipList;
  }

  /**
   * @return float|int
   */
  public function getResourceCount() {
    return array_sum($this->getResourceList());
  }

  /**
   * @return float[] - [$resourceSnId => $resourceAmount]
   */
  public function getResourceList() {
    return $this->resources;
  }

}
