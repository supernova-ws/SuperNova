<?php
/**
 * Created by Gorlum 29.07.2016 13:18
 */

namespace V2Unit;

/**
 * Class V2UnitModel
 *
 * Second iteration of revised Unit
 *
 * @method V2UnitContainer getContainer()
 *
 * @package V2Unit
 *
 */

class V2UnitModel extends \EntityModel {
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = 'unit';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected $idField = 'unit_id';

  protected $exceptionClass = 'EntityException';
  protected $entityContainerClass = 'V2Unit\V2UnitContainer';

  protected $properties = array(
    'dbId'                => array(
      P_DB_FIELD => 'unit_id',
    ),
    'playerOwnerId'       => array(
      P_DB_FIELD => 'unit_player_id',
    ),
    'locationType'        => array(
      P_DB_FIELD => 'unit_location_type',
    ),
    'locationId'          => array(
      P_DB_FIELD => 'unit_location_id',
    ),
    'type'                => array(
      P_DB_FIELD => 'unit_type',
    ),
    'snId'                => array(
      P_DB_FIELD => 'unit_snid',
    ),
    // Order is important!
    // TODO - split dbLevel to level and count
    'level'               => array(
      P_DB_FIELD => 'unit_level',
    ),
    'count'               => array(),
    // TODO - move to child class
    'timeStart'           => array(
      P_DB_FIELD => 'unit_time_start',
    ),
    'timeFinish'          => array(
      P_DB_FIELD => 'unit_time_finish',
    ),
    // Do we need it? Or internal no info/getters/setters should be ignored?
    'unitInfo'            => array(),
    'isStackable'         => array(),
    'locationDefaultType' => array(),
    'bonusType'           => array(),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    $this->assignAccessor('type', P_CONTAINER_SET, array($this, 'setType'));
    $this->assignAccessor('type', P_CONTAINER_UNSET, array($this, 'unsetType'));

    // This crap code is until php 5.4+. There we can use $this binding for lambdas
    $propertyName = 'timeStart';
    $this->assignAccessor($propertyName, P_CONTAINER_IMPORT, array($gc->types, 'dateTimeImport'));
    $this->assignAccessor($propertyName, P_CONTAINER_EXPORT, array($gc->types, 'dateTimeExport'));

    $propertyName = 'timeFinish';
    $this->assignAccessor($propertyName, P_CONTAINER_IMPORT, array($gc->types, 'dateTimeImport'));
    $this->assignAccessor($propertyName, P_CONTAINER_EXPORT, array($gc->types, 'dateTimeExport'));
  }

  public function setType(V2UnitContainer $that, $value) {
    $that->setDirect('type', $value);
    $array = get_unit_param($value);
    $that->unitInfo = $array;
    // Mandatory
    $that->isStackable = empty($array[P_STACKABLE]) ? false : true;
    $that->locationDefaultType = empty($array[P_LOCATION_DEFAULT]) ? LOC_NONE : $array[P_LOCATION_DEFAULT];
    // Optional
    $that->bonusType = empty($array[P_BONUS_TYPE]) ? BONUS_NONE : $array[P_BONUS_TYPE];
    $that->features = array(); //new FeatureList();
  }

  public function unsetType(V2UnitContainer $that, $value) {
    unset($that->type);
    unset($that->unitInfo);
    // Mandatory
    unset($that->isStackable);
    unset($that->locationDefaultType);
    // Optional
    unset($that->bonusType);
    unset($that->features);
  }

  /**
   * @param V2UnitContainer $unitCaptain
   * @param int|string      $userId
   *
   * @throws \EntityException
   */
  // TODO - move to unitCaptain
  public function validateCaptainVsUser($unitCaptain, $userId) {
    if (!is_object($unitCaptain) || $this->isNew($unitCaptain) || $this->isEmpty($unitCaptain)) {
      throw new $this->$exceptionClass('module_unit_captain_error_not_found', ERR_ERROR);
    }
    if ($unitCaptain->snId != UNIT_CAPTAIN) {
      throw new $this->$exceptionClass('module_unit_captain_error_wrong_unit', ERR_ERROR);
    }
    if ($unitCaptain->playerOwnerId != $userId) {
      throw new $this->$exceptionClass('module_unit_captain_error_wrong_captain', ERR_ERROR);
    }
    if ($unitCaptain->locationType != LOC_PLANET) {
      throw new $this->$exceptionClass('module_unit_captain_error_wrong_location', ERR_ERROR);
    }
  }

  /**
   * @param V2UnitContainer $cUnit
   *
   * @return bool
   */
  public function isEmpty($cUnit) {
    return
      empty($cUnit->playerOwnerId)
      ||
      is_null($cUnit->locationType)
      ||
      $cUnit->locationType === LOC_NONE
      ||
      empty($cUnit->locationId)
      ||
      empty($cUnit->type)
      ||
      empty($cUnit->snId)
      ||
      empty($cUnit->level);
  }

}
