<?php
/**
 * Created by Gorlum 17.08.2016 22:07
 */

namespace V2Fleet;

use V2Unit\V2UnitContainer;
use V2Unit\V2UnitList;
use V2Unit\V2UnitModel;
use Vector\Vector;

/**
 * Class V2FleetModel
 *
 * @method V2FleetContainer getContainer()
 * method V2FleetContainer fromArray(array $array)
 * @method V2FleetContainer loadById(mixed $dbId)
 *
 * @package V2Fleet
 */
class V2FleetModel extends \EntityModel {
  protected $locationType = LOC_FLEET;

  /**
   * Return location type
   *
   * @param \EntityContainer $cEntity
   *
   * @return int
   */
  public function getLocationType($cEntity) {
    return $this->locationType;
  }

  /**
   * Return location ID
   *
   * @param \EntityContainer $cEntity
   *
   * @return mixed
   */
  public function getLocationId($cEntity) {
    return $cEntity->dbId;
  }


  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = 'fleets';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected $idField = 'fleet_id';

//  protected $exceptionClass = 'EntityException';
  protected $entityContainerClass = 'V2Fleet\V2FleetContainer';

  protected $properties = array(
    'dbId'        => array(P_DB_FIELD => 'fleet_id',),
    'ownerId'     => array(P_DB_FIELD => 'fleet_owner',),
    'missionType' => array(P_DB_FIELD => 'fleet_mission'),

    'fleet_amount'          => array(P_DB_FIELD => 'fleet_amount'),
    'fleet_start_planet_id' => array(P_DB_FIELD => 'fleet_start_planet_id'),

//    'fleet_start_galaxy'       => array(P_DB_FIELD => 'fleet_start_galaxy'),
//    'fleet_start_system'       => array(P_DB_FIELD => 'fleet_start_system'),
//    'fleet_start_planet'       => array(P_DB_FIELD => 'fleet_start_planet'),
//    'fleet_start_type'         => array(P_DB_FIELD => 'fleet_start_type'),

    'fleet_end_planet_id' => array(P_DB_FIELD => 'fleet_end_planet_id'),
    'fleet_target_owner'  => array(P_DB_FIELD => 'fleet_target_owner'),

//    'fleet_end_galaxy'         => array(P_DB_FIELD => 'fleet_end_galaxy'),
//    'fleet_end_system'         => array(P_DB_FIELD => 'fleet_end_system'),
//    'fleet_end_planet'         => array(P_DB_FIELD => 'fleet_end_planet'),
//    'fleet_end_type'           => array(P_DB_FIELD => 'fleet_end_type'),

//    'fleet_resource_metal'     => array(P_DB_FIELD => 'fleet_resource_metal'),
//    'fleet_resource_crystal'   => array(P_DB_FIELD => 'fleet_resource_crystal'),
//    'fleet_resource_deuterium' => array(P_DB_FIELD => 'fleet_resource_deuterium'),

    'groupId' => array(P_DB_FIELD => 'fleet_group'),
    'status'  => array(P_DB_FIELD => 'fleet_mess'),

    'vectorDeparture' => array(P_DB_FIELD => 'fleet_start_galaxy'),
    'vectorArrive'    => array(P_DB_FIELD => 'fleet_end_galaxy'),

    'timeDeparture' => array(P_DB_FIELD => 'start_time'),
    'timeArrive'    => array(P_DB_FIELD => 'fleet_start_time'),
    'timeComplete'  => array(P_DB_FIELD => 'fleet_end_stay'),
    'timeReturn'    => array(P_DB_FIELD => 'fleet_end_time'),

    'units' => array(),
    'isReturning' => array(),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    $this->setAccessor('units', P_CONTAINER_GET, function (V2FleetContainer $that) {
      if(is_null($units = $that->getDirect('units'))) {
        $units = new V2UnitList();
        $that->setDirect('units', $units);
      }

      return $units;
    });

    $this->setAccessor('isReturning', P_CONTAINER_GET, function (V2FleetContainer $that) {
      return $that->status == 1;
    });

    $this->setAccessor('vectorDeparture', P_CONTAINER_IMPORT, array($this, 'importVector'));
    $this->setAccessor('vectorDeparture', P_CONTAINER_EXPORT, array($this, 'exportVector'));
    $this->setAccessor('vectorArrive', P_CONTAINER_IMPORT, array($this, 'importVector'));
    $this->setAccessor('vectorArrive', P_CONTAINER_EXPORT, array($this, 'exportVector'));
  }

  public function importVector(V2FleetContainer $that, $propertyName, $fieldName) {
    $prefix = $propertyName == 'vectorDeparture' ? 'fleet_start_' : 'fleet_end_';
    $that->$propertyName = new Vector(
      $that->row[$prefix . 'galaxy'],
      $that->row[$prefix . 'system'],
      $that->row[$prefix . 'planet'],
      $that->row[$prefix . 'type']
    );
  }

  public function exportVector(V2FleetContainer $that, $propertyName, $fieldName) {
    $prefix = $propertyName == 'vectorDeparture' ? 'fleet_start_' : 'fleet_end_';
    $that->row[$prefix . 'galaxy'] = $that->$propertyName->galaxy;
    $that->row[$prefix . 'system'] = $that->$propertyName->system;
    $that->row[$prefix . 'planet'] = $that->$propertyName->planet;
    $that->row[$prefix . 'type'] = $that->$propertyName->type;
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

    $cFleet->units->load($this->getLocationType($cFleet), $this->getLocationId($cFleet));

    foreach (array(
      RES_METAL     => 'fleet_resource_metal',
      RES_CRYSTAL   => 'fleet_resource_crystal',
      RES_DEUTERIUM => 'fleet_resource_deuterium',
    ) as $resourceId => $fieldName) {
      $unit = \classSupernova::$gc->unitModel->getContainer();
      $unit->snId = $resourceId;
      $unit->level = $array[$fieldName];
      $cFleet->units->attach($unit, $resourceId);
    }

    return $cFleet;
  }

}
