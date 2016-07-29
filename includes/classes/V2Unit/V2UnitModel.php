<?php
/**
 * Created by PhpStorm.
 * User: oomelche
 * Date: 29.07.2016
 * Time: 13:18
 */

namespace V2Unit;

use Common\ContainerPlus;

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
//pdump('level setter is launched');
//pdump($row, '$row');
//var_dump($that);
        $that->level = $row['unit_level'];
//var_dump($that);
//pdie();



//pdump($row['unit_level'], '$row[\'unit_level\']');
//pdump($that->level, '$that->level');
//pdump($that->_container, '$that->_container');
//        pdump($that);
//pdie('importer launched');
      }
    );

    $this->_container->assignAccessor(
      'dbLevel',
      P_CONTAINER_EXPORTER,
      function (&$row) use ($that) {
//pdump('level setter is launched');
//pdump($row, '$row');
//var_dump($that);
        $row['unit_level'] = $that->dbLevel;
//var_dump($that);
//pdie();



//pdump($row['unit_level'], '$row[\'unit_level\']');
//pdump($that->level, '$that->level');
//pdump($that->_container, '$that->_container');
//        pdump($that);
//pdie('importer launched');
      }
    );


//    $this->_container->importRow(array('unit_level' => 5, 'unit_type' => 10,));
//
//    pdump($this->_container->level);
//    pdump($this->level);
////    pdump($this->type);
//
//    pdie();
  }

  /**
   * @param static $that
   * @param array  $row
   */
  protected function importLevel($that, &$row) {
    $that->level = $row['unit_level'];
  }

  /**
   * @param static $that
   * @param array  $row
   */
  protected function exportLevel($that, &$row) {
    // $row['unit_level'] = $that->level;
  }

  /**
   * @param ContainerPlus $cUnit
   */
  public function test($unitId, $cUnit = null) {
    $this->dbId = $unitId;
    static::$rowOperator->getById($this);
  }

}
