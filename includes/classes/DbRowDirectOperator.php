<?php

/**
 * Class DbRowDirectOperator
 *
 * Handle EntityModel storing/loading operations
 */

class DbRowDirectOperator {

  /**
   * @param \EntityModel $cModel
   * @param int|string   $dbId
   *
   * @return array
   */
  public function getById($cModel, $dbId) {
    $stmt = classSupernova::$gc->query
      ->setIdField($cModel->getIdFieldName())
      ->field('*')
      ->from($cModel->getTableName())
      ->where($cModel->getIdFieldName() . ' = "' . $dbId . '"');

    return $stmt->selectRow();
  }

  /**
   * @param \EntityModel $cModel
   * @param int|string   $dbId
   *
   * @return int
   */
  public function deleteById($cModel, $dbId) {
    $db = $cModel->getDbStatic();

    $db->doDeleteRow(
      $cModel->getTableName(),
      array(
        $cModel->getIdFieldName() => $dbId,
      )
    );

    return $db->db_affected_rows();
  }

  /**
   * @param \EntityModel $cModel
   * @param array        $row
   *
   * @return int|string
   */
  public function insert($cModel, $row) {
    if (empty($row)) {
      // TODO Exception
      return 0;
    }
    $db = $cModel->getDbStatic();
    $db->doInsertSet($cModel->getTableName(), $row);

    // TODO Exception if db_insert_id() is empty
    return $db->db_insert_id();
  }

}
