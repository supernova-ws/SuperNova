<?php
/**
 * Created by Gorlum 17.08.2016 22:07
 */

namespace V2Fleet;

use Common\V2Location;
use DBStatic\DBStaticFleetACS;
use V2Unit\V2UnitList;
use Vector\Vector;
use Entity\KeyedModel;

/**
 * Class V2FleetModel
 *
 * @method V2FleetContainer buildContainer()
 * @method V2FleetContainer loadById(mixed $dbId)
 *
 * @package V2Fleet
 */
class V2FleetModel extends KeyedModel {
  protected $location;

  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = 'fleets';

  protected $exceptionClass = 'Entity\EntityException';
  protected $entityContainerClass = 'V2Fleet\V2FleetContainer';

  protected $newProperties = array(
    'dbId'              => array(
      P_DB_FIELD => 'fleet_id',
    ),
    'ownerId'           => array(P_DB_FIELD => 'fleet_owner',),
    'arriveOwnerId'     => array(P_DB_FIELD => 'fleet_target_owner'),
    'departurePlanetId' => array(P_DB_FIELD => 'fleet_start_planet_id'),
    'arrivePlanetId'    => array(P_DB_FIELD => 'fleet_end_planet_id'),

    'missionType' => array(
      P_DB_FIELD      => 'fleet_mission',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),
    'status'      => array(
      P_DB_FIELD      => 'fleet_mess',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),
    'groupId'     => array(P_DB_FIELD => 'fleet_group',),


//    'fleet_start_galaxy'       => array(P_DB_FIELD => 'fleet_start_galaxy'),
//    'fleet_start_system'       => array(P_DB_FIELD => 'fleet_start_system'),
//    'fleet_start_planet'       => array(P_DB_FIELD => 'fleet_start_planet'),
//    'fleet_start_type'         => array(P_DB_FIELD => 'fleet_start_type'),
//    'fleet_end_galaxy'         => array(P_DB_FIELD => 'fleet_end_galaxy'),
//    'fleet_end_system'         => array(P_DB_FIELD => 'fleet_end_system'),
//    'fleet_end_planet'         => array(P_DB_FIELD => 'fleet_end_planet'),
//    'fleet_end_type'           => array(P_DB_FIELD => 'fleet_end_type'),
//    'fleet_resource_metal'     => array(P_DB_FIELD => 'fleet_resource_metal'),
//    'fleet_resource_crystal'   => array(P_DB_FIELD => 'fleet_resource_crystal'),
//    'fleet_resource_deuterium' => array(P_DB_FIELD => 'fleet_resource_deuterium'),


    'vectorDeparture' => array(P_DB_FIELD => 'fleet_start_galaxy'),
    'vectorArrive'    => array(P_DB_FIELD => 'fleet_end_galaxy'),

    'timeDeparture' => array(
      P_DB_FIELD      => 'start_time',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),
    'timeArrive'    => array(
      P_DB_FIELD      => 'fleet_start_time',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),
    'timeComplete'  => array(
      P_DB_FIELD      => 'fleet_end_stay',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),
    'timeReturn'    => array(
      P_DB_FIELD      => 'fleet_end_time',
      P_DB_FIELD_TYPE => TYPE_INTEGER,
    ),

    'shipsCount'  => array(P_DB_FIELD => 'fleet_amount'),
    'units'       => array(),
    'isReturning' => array(),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    $this->accessors->set(P_CONTAINER_SET, 'dbId', function (V2FleetContainer $that, $value) {
      $that->setDirect('dbId', $value);
      $that->location->setLocationId($value);
    });

    $this->accessors->set(P_CONTAINER_SET, 'ownerId', function (V2FleetContainer $that, $value) {
      $that->setDirect('ownerId', $value);
      $that->location->setLocationPlayerId($value);
    });

    $this->accessors->set(P_CONTAINER_IMPORT, 'vectorDeparture', array($this, 'importVector'));
    $this->accessors->set(P_CONTAINER_EXPORT, 'vectorDeparture', array($this, 'exportVector'));
    $this->accessors->set(P_CONTAINER_IMPORT, 'vectorArrive', array($this, 'importVector'));
    $this->accessors->set(P_CONTAINER_EXPORT, 'vectorArrive', array($this, 'exportVector'));


    $this->accessors->set(P_CONTAINER_GET, 'location', function (V2FleetContainer $that) {
//      if (is_null($location = $that->getDirect('location'))) {}
      $location = new V2Location(LOC_FLEET);
      $that->setDirect('location', $location);

      return $location;
    }, ACCESSOR_SHARED);

    $this->accessors->set(P_CONTAINER_GET, 'units', function (V2FleetContainer $that) {
//      if (is_null($units = $that->getDirect('units'))) {}
      $units = \classSupernova::$gc->unitList;
      $that->setDirect('units', $units);

      return $units;
    }, ACCESSOR_SHARED);

    $this->accessors->set(P_CONTAINER_GET, 'isReturning', function (V2FleetContainer $that) {
      return $that->status == FLEET_FLAG_RETURNING;
    });

  }

  public function importVector(V2FleetContainer $that, $propertyName) {
    if ($propertyName == 'vectorDeparture') {
      $that->vectorDeparture = Vector::convertToVector($that->row, FLEET_START_PREFIX);
    } else {
      $that->vectorArrive = Vector::convertToVector($that->row, FLEET_END_PREFIX);
    }
  }

  public function exportVector(V2FleetContainer $that, $propertyName) {
    if ($propertyName == 'vectorDeparture') {
      $that->row += $that->vectorDeparture->toArray(FLEET_START_PREFIX);
    } else {
      $that->row += $that->vectorArrive->toArray(FLEET_END_PREFIX);
    }
  }

  /**
   * @param array $array
   *
   * @return V2FleetContainer
   */
  public function fromArray($array) {
    /**
     * @var V2FleetContainer $cFleet
     */
    $cFleet = parent::fromArray($array);

    $cFleet->units->load($cFleet->location);

    foreach (array(
      RES_METAL     => 'fleet_resource_metal',
      RES_CRYSTAL   => 'fleet_resource_crystal',
      RES_DEUTERIUM => 'fleet_resource_deuterium',
    ) as $resourceId => $fieldName) {
      $cFleet->units->unitAdd($resourceId, $array[$fieldName]);
    }

    return $cFleet;
  }

  protected function save(V2FleetContainer $cFleet) {
    throw new \Exception('V2FleetModel::dbSave() is not yet implemented');
  }

  public function isEmpty(V2FleetContainer $cFleet) {
    return
      // Fleet is empty if not units in it
      $cFleet->units->isEmpty()// parent::isEmpty($cFleet)
      ;
  }

  /**
   * Forcibly returns fleet before time outs
   */
  protected function doReturn(V2FleetContainer $cFleet) {
    if ($cFleet->isReturning) {
      return;
    }

    // Marking fleet as returning
    $cFleet->status = FLEET_FLAG_RETURNING;

    // If fleet not yet arrived - return time is equal already fled time
    if ($cFleet->timeArrive <= SN_TIME_NOW) {
      $returnTime = SN_TIME_NOW - $cFleet->timeDeparture;
    } else {
      // Arrived fleet on mission will return in same time as it takes to get to the destination
      $returnTime = $cFleet->timeArrive - $cFleet->timeDeparture;
    }
    $cFleet->timeReturn = SN_TIME_NOW + $returnTime;

    // Считаем, что флот уже долетел
    $cFleet->timeArrive = SN_TIME_NOW;
    // Отменяем работу в точке назначения
    $cFleet->timeComplete = 0;
    // Убираем флот из группы
    $oldGroupId = $cFleet->groupId;
    $cFleet->groupId = 0;

    // Записываем изменения в БД
    $this->save($cFleet);

    if ($oldGroupId) {
      // TODO: Make here to delete only one AKS - by adding aks_fleet_count to AKS table
      DBStaticFleetACS::db_fleet_aks_purge();
    }
  }

  public function commandReturn($userId, $fleet_id) {
    $fleetV2 = $this->loadById($fleet_id);
    if (!$fleetV2->dbId) {
      return;
    }

    if ($fleetV2->ownerId != $userId) {
      throw new \Exception($fleetV2->ownerId, ERR_ERROR);
    }

    $this->doReturn($fleetV2);
  }

}
