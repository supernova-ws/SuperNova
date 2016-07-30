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
 * @property int       $playerOwnerId
 * @property int       $locationType
 * @property int       $locationId
 * @property int       $type
 * @property int       $snId
 * @property int       $level - level of unit for DB: $count for stackable units, $level - fon unstackable units
 * property int $count // TODO
 * @property \DateTime $timeStart // TODO
 * @property \DateTime $timeFinish  // TODO
 * @property bool      $isStackable
 * @property string    $locationDefaultType
 * @property int       $bonusType // TODO - Optional?
 * @property array     $unitInfo - full info about unit
 *
 * @package V2Unit
 *
 */
class V2UnitModel extends \Entity {

  protected static $tableName = 'unit';
  protected static $idField = 'unit_id';
  protected static $exceptionClass = 'EntityException';

  protected static $_properties = array(
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

    $that = $this;

    $this->_container->assignAccessor('type', P_CONTAINER_GETTER,
      function () use ($that) {
        return $that->type;
      }
    );
    $this->_container->assignAccessor('type', P_CONTAINER_SETTER,
      function ($value) use ($that) {
        $that->type = $value;
        $array = get_unit_param($value);
        $that->unitInfo = $array;
        // Mandatory
        $that->isStackable = empty($array[P_STACKABLE]) ? false : true;
        $that->locationDefaultType = empty($array[P_LOCATION_DEFAULT]) ? LOC_NONE : $array[P_LOCATION_DEFAULT];
        // Optional
        $that->bonusType = empty($array[P_BONUS_TYPE]) ? BONUS_NONE : $array[P_BONUS_TYPE];
      }
    );

    // This crap code is until php 5.4+. There we can use $this binding for lambdas
    $propertyName = 'timeStart';
    $fieldName = self::$_properties[$propertyName][P_DB_FIELD];
    $this->_container->assignAccessor($propertyName, P_CONTAINER_IMPORTER,
      function (&$row) use ($that, $fieldName) {
        if (isset($row[$fieldName])) {
          $dateTime = new \DateTime($row[$fieldName]);
        } else {
          $dateTime = null;
        }
        $that->timeStart = $dateTime;
      }
    );
    $this->_container->assignAccessor($propertyName, P_CONTAINER_EXPORTER,
      function (&$row) use ($that, $fieldName) {
        $dateTime = $that->timeStart;
        if ($dateTime instanceof \DateTime) {
          $row[$fieldName] = $dateTime->format(FMT_DATE_TIME_SQL);
        } else {
          $row[$fieldName] = null;
        }
      }
    );

    $propertyName = 'timeFinish';
    $fieldName = self::$_properties[$propertyName][P_DB_FIELD];
    $this->_container->assignAccessor($propertyName, P_CONTAINER_IMPORTER,
      function (&$row) use ($that, $fieldName) {
        if (isset($row[$fieldName])) {
          $dateTime = new \DateTime($row[$fieldName]);
        } else {
          $dateTime = null;
        }
        $that->timeFinish = $dateTime;
      }
    );
    $this->_container->assignAccessor($propertyName, P_CONTAINER_EXPORTER,
      function (&$row) use ($that, $fieldName) {
        $dateTime = $that->timeFinish;
        if ($dateTime instanceof \DateTime) {
          $row[$fieldName] = $dateTime->format(FMT_DATE_TIME_SQL);
        } else {
          $row[$fieldName] = null;
        }
      }
    );

  }

}
