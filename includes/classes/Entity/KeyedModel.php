<?php
/**
 * Created by Gorlum 19.08.2016 21:26
 */

namespace Entity;


class KeyedModel extends EntityModel {

  /**
   * Name of key field field in this table
   *
   * @var string $idFieldName
   */
  protected $idFieldName = 'id';

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);
    $this->extendProperties(
      array(
        'dbId' => array(
          P_DB_FIELD => $this->getIdFieldName(),
        )
      )
    );
  }

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @param \Entity\KeyedContainer $cEntity
   */
  protected function exportRowNoId($cEntity) {
    $this->exportRow($cEntity);

    if ($this->getIdFieldName() != '') {
      unset($cEntity->row[$this->getIdFieldName()]);
    }
  }

  /**
   * @param int|string $dbId
   *
   * @return EntityContainer|false
   */
  public function loadById($dbId) {
    $row = $this->rowOperator->getById($this, $dbId);
    if (empty($row)) {
      return false;
    } else {
      $cEntity = $this->fromArray($row);
    }

    return $cEntity;
  }


  /**
   * @param string $value
   */
  public function setIdFieldName($value) {
    $this->idFieldName = $value;
  }

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName() {
    return $this->idFieldName;
  }

}
