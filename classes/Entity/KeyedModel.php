<?php
/**
 * Created by Gorlum 19.08.2016 21:26
 */

namespace Entity;

/**
 * Class KeyedModel
 *
 * @method KeyedContainer fromArray($array)
 *
 * @package Entity
 */

class KeyedModel extends EntityModel {

  protected $newProperties = array(
    'dbId' => array(
      P_DB_FIELD => 'id',
    ),
  );

  public function __construct(\Common\GlobalContainer $gc) {
    parent::__construct($gc);
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

    if (($idFieldName = $this->getIdFieldName()) != '') {
      unset($cEntity->row[$idFieldName]);
    }
  }

  /**
   * @param int|string $dbId
   *
   * @return KeyedContainer|false
   */
  public function loadById($dbId) {
    $row = $this->rowOperator->getById($this, $dbId);
    if (empty($row)) {
      return false;
    }

    $cEntity = $this->fromArray($row);

    return $cEntity;
  }

//  protected function load(KeyedContainer $cEntity) {
//    throw new \Exception('EntityModel::dbSave() is not yet implemented');
//  }
//
  protected function save(KeyedContainer $cEntity) {
    throw new \Exception('EntityModel::dbSave() is not yet implemented');
  }

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName() {
    return $this->properties['dbId'][P_DB_FIELD];
  }

}
