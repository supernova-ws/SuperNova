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
 * @method V2UnitContainer fromArray(array $array)
 *
 * @package V2Unit
 *
 */

class V2UnitModel extends \Entity\KeyedModel {
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = 'unit';
  /**
   * Name of key field field in this table
   *
   * @var string $idFieldName
   */
  protected $idFieldName = 'unit_id';

  protected $exceptionClass = 'Entity\EntityException';
  protected $entityContainerClass = 'V2Unit\V2UnitContainer';

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    $this->extendProperties(
      array(
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
      )
    );

    $this->accessors->setAccessor('snId', P_CONTAINER_SET, array($this, 'setSnId'));
    $this->accessors->setAccessor('snId', P_CONTAINER_UNSET, array($this, 'unsetSnId'));

    // This crap code is until php 5.4+. There we can use $this binding for lambdas
    $propertyName = 'timeStart';
    $this->accessors->setAccessor($propertyName, P_CONTAINER_IMPORT, array($gc->types, 'dateTimeImport'));
    $this->accessors->setAccessor($propertyName, P_CONTAINER_EXPORT, array($gc->types, 'dateTimeExport'));

    $propertyName = 'timeFinish';
    $this->accessors->setAccessor($propertyName, P_CONTAINER_IMPORT, array($gc->types, 'dateTimeImport'));
    $this->accessors->setAccessor($propertyName, P_CONTAINER_EXPORT, array($gc->types, 'dateTimeExport'));
  }

  /**
   * @return V2UnitContainer
   */
  public function buildContainer($snId = 0, $level = 0) {
    /**
     * @var V2UnitContainer $unit
     */
    $unit = parent::buildContainer();
    $unit->snId = $snId;
    $unit->level = $level;
    return $unit;
  }

  public function setSnId(V2UnitContainer $that, $value) {
    $that->setDirect('snId', $value);

    $array = get_unit_param($value);
    $that->unitInfo = $array;
    $that->type = $array[P_UNIT_TYPE];
    // Mandatory
    $that->isStackable = empty($array[P_STACKABLE]) ? false : true;
    $that->locationDefaultType = empty($array[P_LOCATION_DEFAULT]) ? LOC_NONE : $array[P_LOCATION_DEFAULT];
    // Optional
    $that->bonusType = empty($array[P_BONUS_TYPE]) ? BONUS_NONE : $array[P_BONUS_TYPE];
    // TODO - Записывать перечень фич для модуля, определяемых по его типу
    // А фичи сначала должны быть где-то зарегестрированы - в каком-то сервис-локаторе
    // Что-то типа classSupernova::registerUnitFeature
    // Кэш фич для разных типов юнитов
    $that->features = array(); //new FeatureList($that->unitInfo['features']);
  }

  public function unsetSnId(V2UnitContainer $that) {
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
   * @throws \Entity\EntityException
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

  /**
   * @param V2UnitContainer $cUnit
   * @param string          $featureName
   *
   * return UnitFeature
   *
   * @return mixed|null
   */
  public function feature($cUnit, $featureName) {
    return isset($cUnit->features[$featureName]) ? $cUnit->features[$featureName] : null;
  }

}
