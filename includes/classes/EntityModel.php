<?php

/**
 * Class EntityModel
 *
 * @property int|float $dbId Buddy record DB ID
 */
class EntityModel implements \Common\IEntityModel {
  /**
   * Link to DB which used by this EntityModel
   *
   * @var \db_mysql $dbStatic
   * deprecated - replace with container ID like 'db' or 'dbAuth'
   */
  protected static $dbStatic;
  /**
   * Service to work with rows
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected static $rowOperator;

  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = '_table';

  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected $idField = 'id';
//  /**
//   * Property list and description
//   *
//   * propertyName => array(
//   *    P_DB_FIELD => 'dbFieldName', - directly converts property to field and vice versa
//   * )
//   *
//   * @var array[] $properties
//   */
//  protected $properties = array();

  /**
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected static $exceptionClass = 'EntityException';
  protected static $entityContainerClass = '\EntityContainer';


  /**
   * EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    static::$dbStatic = $gc->db;
    static::$rowOperator = $gc->dbRowOperator;
  }

  /**
   * @return \db_mysql
   */
  public function getDbStatic() {
    return static::$dbStatic;
  }

  /**
   * @return \DbRowDirectOperator
   */
  public function getRowOperator() {
    return static::$rowOperator;
  }

  public function setTableName($value) {
    $this->tableName = $value;
  }

  public function getTableName() {
    return $this->tableName;
  }

  public function setIdFieldName($value) {
    $this->idField = $value;
  }

  public function getIdFieldName() {
    return $this->idField;
  }





  public function fromArray($array) {
    /**
     * @var EntityContainer $cEntity
     */
    $cEntity = new static::$entityContainerClass(classSupernova::$gc);
    $cEntity->importRow($array);

    return $cEntity;
  }


  public function exportRow($cEntity) {
    return $cEntity->exportRow();
  }

  public function exportRowNoId($cEntity) {
    $row = $this->exportRow($cEntity);

    unset($row[$this->getIdFieldName()]);

    return $row;
  }













  public function loadTry($dbId) {
    $row = static::$rowOperator->getById($this, $dbId);
    if (empty($row)) {
      return false;
    } else {
      $cEntity = $this->fromArray($row);
    }

    return $cEntity;
  }

}
