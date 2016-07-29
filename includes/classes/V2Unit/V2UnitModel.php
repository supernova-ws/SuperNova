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
 * @property int $playerOwnerId
 * @property int $locationType
 * @property int $locationId
 * @property int $type
 * @property int $snId
 * @property int $dbLevel - level of unit for DB: $count for stackable units, $level - fon unstackable units
 * @property int $level // TODO
 * property int $count // TODO
 * property \DateTime $timeStart // TODO
 * property \DateTime $timeFinish  // TODO
 *
 * @package V2Unit
 *
 */
class V2UnitModel extends \Entity {

  protected static $tableName = 'unit';
  protected static $idField = 'unit_id';

  protected static $_properties = array(
    'dbId'          => array(
      P_DB_FIELD => 'unit_id',
    ),
    'playerOwnerId' => array(
      P_DB_FIELD => 'unit_player_id',
    ),
    'locationType'  => array(
      P_DB_FIELD => 'unit_location_type',
    ),
    'locationId'    => array(
      P_DB_FIELD => 'unit_location_id',
    ),
    'type'          => array(
      P_DB_FIELD => 'unit_type',
    ),
    'snId'          => array(
      P_DB_FIELD => 'unit_snid',
    ),
    // Order is important!
    'dbLevel'       => array(
      // TODO - aggregate function from Level/Count
      P_DB_FIELD => 'unit_level',
    ),

    // TODO - split dbLevel to level and count
    'level'         => array(),
    'count'         => array(),

    // TODO - move to child class
    'timeStart'     => array(
      P_DB_FIELD => 'unit_time_start',
    ),
    'timeFinish'    => array(
      P_DB_FIELD => 'unit_time_finish',
    ),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);

    // TODO - remove and check how it's works
    $this->_container = new static::$_containerName();
    $this->_container->setProperties(static::$_properties);

    $that = $this;
    $this->_container->assignAccessor(
      'type',
      P_CONTAINER_SETTER,
      function ($value) use ($that) {
        $that->type = $value;
      }
    );

    $this->_container->assignAccessor(
      'level',
      P_CONTAINER_IMPORTER,
      function (&$row) use ($that) {
        $that->level = $row['unit_level'];
      }
    );

    $this->_container->assignAccessor(
      'dbLevel',
      P_CONTAINER_EXPORTER,
      function (&$row) use ($that) {
        $row['unit_level'] = $that->dbLevel;
      }
    );
  }

}
