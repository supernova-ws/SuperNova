<?php

/**
 * Class EntityModel
 *
 * @property int|float $dbId Buddy record DB ID
 */
class EntityModel implements \Common\IEntity {
//  /**
//   * Link to DB which used by this EntityModel
//   *
//   * @var db_mysql $dbStatic
//   * deprecated - replace with container ID like 'db' or 'dbAuth'
//   */
//  protected static $dbStatic;

  /**
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected static $exceptionClass = 'EntityException';

  /**
   * Container for property values
   *
   * @var \Common\IPropertyContainer $_container
   */
  protected $_container;
//  protected static $_containerName = 'V2PropertyContainer';

  /**
   * Service to work with rows
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected static $rowOperator;

  /**
   * EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
//    empty(static::$dbStatic) && !empty($gc->db) ? static::$dbStatic = $gc->db : false;
    static::$rowOperator = $gc->dbRowOperator;
  }



}
