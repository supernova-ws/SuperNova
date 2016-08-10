<?php

/**
 * Class DbRowDirectOperator
 *
 * Handle EntityModel storing/loading operations
 */

class DbRowDirectOperator implements \Common\IEntityOperator {

  /**
   * @param IEntityContainer $cEntity
   *
   * @return array
   */
  public function getById($cEntity) {
    $stmt = classSupernova::$gc->query
      ->setIdField($cEntity->getIdFieldName())
      ->field('*')
      ->from($cEntity->getTableName())
      ->where($cEntity->getIdFieldName() . ' = "' . $cEntity->dbId . '"');

    return $stmt->selectRow();
  }

  /**
   * @param IEntityContainer $cEntity
   *
   * @return int
   */
  public function deleteById($cEntity) {
    $db = $cEntity->getDbStatic();

    $db->doDeleteRow(
      $cEntity->getTableName(),
      array(
        $cEntity->getIdFieldName() => $cEntity->dbId,
      )
    );

    return $db->db_affected_rows();
  }

  /**
   * @param IEntityContainer $cEntity
   *
   * @return int|string
   */
  public function insert($cEntity) {
    $db = $cEntity->getDbStatic();

    $row = $cEntity->exportRowWithoutId();
    if (empty($row)) {
      // TODO Exception
      return 0;
    }
    $db->doInsertSet($cEntity->getTableName(), $row);

    // TODO Exception if db_insert_id() is empty
    return $cEntity->dbId = $db->db_insert_id();
  }

}
