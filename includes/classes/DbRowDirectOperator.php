<?php

/**
 * Class DbRowDirectOperator
 *
 * Handle Entity storing/loading operations
 */

class DbRowDirectOperator implements \Common\IEntityOperator {

  /**
   * @param \Common\IEntity $entity
   *
   * @return array
   */
  public function getById($entity) {
    $stmt = classSupernova::$gc->query
      ->setIdField($entity->getIdFieldName())
      ->field('*')
      ->from($entity->getTableName())
      ->where($entity->getIdFieldName() . ' = "' . $entity->dbId . '"');

    return $stmt->selectRow();
  }

  /**
   * @param \Common\IEntity $entity
   */
  public function deleteById($entity) {
    $db = $entity->getDbStatic();

    $db->doDeleteRowWhere($entity->getTableName(), array($entity->getIdFieldName() => $entity->dbId));

    return $db->db_affected_rows();
  }

  /**
   * @param \Common\IEntity $entity
   */
  public function insert($entity) {
    $db = $entity->getDbStatic();

    $row = $entity->exportRowWithoutId();
    if (empty($row)) {
      // TODO Exceptiion
      return 0;
    }
    $db->doInsertSet($entity->getTableName(), $row);

    // TODO Exceptiion if db_insert_id() is empty
    return $entity->dbId = $db->db_insert_id();
  }

}
