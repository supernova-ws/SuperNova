<?php

/**
 * Class DbRowDirectOperator
 *
 * Handle Entity storing/loading operations
 */

class DbRowDirectOperator implements \Common\IEntityOperator {

  /**
   * @param \Common\IEntity $entity
   */
  public function getById($entity) {
    $stmt = classSupernova::$gc->query
      ->setIdField($entity->getIdFieldName())
      ->field('*')
      ->from($entity->getTableName())
      ->where($entity->getIdFieldName() . ' = "' . $entity->dbId . '"');

    $entity->importRow($stmt->selectRow());
  }

  /**
   * @param \Common\IEntity $entity
   */
  public function deleteById($entity) {
    $db = $entity->getDbStatic();

    $db->doquery("DELETE FROM `{{" . $entity->getTableName() . "}}` WHERE `{$entity->getIdFieldName()}` = '{$entity->dbId}' LIMIT 1;");

    return $db->db_affected_rows();
  }

  /**
   * @param \Common\IEntity $entity
   */
  public function insert($entity) {
    $db = $entity->getDbStatic();

    $query = array();
    foreach ($entity->exportRowWithoutId() as $fieldName => $fieldValue) {
      // TODO: MORE type detection
      if(!is_numeric($fieldValue)) {
        $fieldValue = "'" . $db->db_escape($fieldValue) . "'";
      }
      $query[] = "`{$fieldName}` = {$fieldValue}";
    }

    $query = implode(',', $query);
    if (empty($query)) {
      // TODO Exceptiion
      return 0;
    }

    $db->doquery("INSERT INTO `{{" . $entity->getTableName() . "}}` SET " . $query);

    // TODO Exceptiion if db_insert_id() is empty
    return $entity->dbId = $db->db_insert_id();
  }

}
