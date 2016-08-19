<?php
/**
 * Created by Gorlum 19.08.2016 21:26
 */

namespace Entity;


class KeyedModel extends EntityModel {

  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected $idField = 'id';

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
   * @param string $value
   */
  public function setIdFieldName($value) {
    $this->idField = $value;
  }

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName() {
    return $this->idField;
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

}
