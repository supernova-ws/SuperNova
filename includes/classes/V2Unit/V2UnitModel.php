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

  protected static $exceptionClass = 'EntityException';
  protected static $entityContainerClass = 'V2Unit\V2UnitContainer';

}
