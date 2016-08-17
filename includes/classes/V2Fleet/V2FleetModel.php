<?php
/**
 * Created by Gorlum 17.08.2016 22:07
 */

namespace V2Fleet;

use V2Unit\V2UnitList;
use Vector\Vector;

/**
 * Class V2FleetModel
 *
 * @method V2FleetContainer getContainer()
 * @method V2FleetContainer fromArray(array $array)
 *
 * @package V2Fleet
 */
class V2FleetModel extends \EntityModel {
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

//    'fleet_end_galaxy'         => array(P_DB_FIELD => 'fleet_end_galaxy'),
//    'fleet_end_system'         => array(P_DB_FIELD => 'fleet_end_system'),
//    'fleet_end_planet'         => array(P_DB_FIELD => 'fleet_end_planet'),
//    'fleet_end_type'           => array(P_DB_FIELD => 'fleet_end_type'),

    'fleet_resource_metal'     => array(P_DB_FIELD => 'fleet_resource_metal'),
    'fleet_resource_crystal'   => array(P_DB_FIELD => 'fleet_resource_crystal'),
    'fleet_resource_deuterium' => array(P_DB_FIELD => 'fleet_resource_deuterium'),
    'fleet_target_owner'       => array(P_DB_FIELD => 'fleet_target_owner'),
    'fleet_group'              => array(P_DB_FIELD => 'fleet_group'),
    'fleet_mess'               => array(P_DB_FIELD => 'fleet_mess'),

    'vectorDeparture' => array(P_DB_FIELD => 'fleet_start_galaxy'),
    'vectorArrive'    => array(P_DB_FIELD => 'fleet_end_galaxy'),

    'timeDeparture' => array(P_DB_FIELD => 'start_time'),
    'timeArrive'    => array(P_DB_FIELD => 'fleet_start_time'),
    'timeComplete'  => array(P_DB_FIELD => 'fleet_end_stay'),
    'timeReturn'    => array(P_DB_FIELD => 'fleet_end_time'),

    'units' => array(),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    $this->assignAccessor('vectorDeparture', P_CONTAINER_IMPORT, array($this, 'importVector'));
    $this->assignAccessor('vectorDeparture', P_CONTAINER_EXPORT, array($this, 'exportVector'));
    $this->assignAccessor('vectorArrive', P_CONTAINER_IMPORT, array($this, 'importVector'));
    $this->assignAccessor('vectorArrive', P_CONTAINER_EXPORT, array($this, 'exportVector'));
  }

  public function importVector(V2FleetContainer $that, array &$row, $propertyName, $fieldName) {
    $prefix = $propertyName == 'vectorDeparture' ? 'fleet_start_' : 'fleet_end_';
    $that->$propertyName = new Vector(
      $row[$prefix . 'galaxy'],
      $row[$prefix . 'system'],
      $row[$prefix . 'planet'],
      $row[$prefix . 'type']
    );
  }

  public function exportVector(V2FleetContainer $that, array &$row, $propertyName, $fieldName) {
    $prefix = $propertyName == 'vectorDeparture' ? 'fleet_start_' : 'fleet_end_';
    $row[$prefix . 'galaxy'] = $that->$propertyName->galaxy;
    $row[$prefix . 'system'] = $that->$propertyName->system;
    $row[$prefix . 'planet'] = $that->$propertyName->planet;
    $row[$prefix . 'type'] = $that->$propertyName->type;
  }

  /**
   * @param int|string $dbId
   *
   * @return V2FleetContainer|false
   */
  public function loadById($dbId) {
    if ($fleet = parent::loadById($dbId)) {
      /**
       * @var V2FleetContainer $fleet
       */
      // Loading units
      $units = new V2UnitList();
      $units->load(LOC_FLEET, $dbId);
      $fleet->units = $units;
    }

    return $fleet;
  }

}
