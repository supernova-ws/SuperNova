<?php

/**
 * Class DbRowDirectOperator
 *
 * Handle Entity\EntityModel storing/loading operations
 */
class DbRowDirectOperator {
  protected $db;

  /**
   * DbRowDirectOperator constructor.
   *
   * @param db_mysql $db
   */
  public function __construct($db) {
    $this->db = $db;
  }

  /**
   * @param \Entity\KeyedModel $cModel
   * @param int|string          $dbId
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
   * @param \Entity\KeyedModel $cModel
   * @param int|string          $dbId
   *
   * @return int
   */
  public function deleteById($cModel, $dbId) {
    $db = $this->db;

    $db->doDeleteRow(
      $cModel->getTableName(),
      array(
        $cModel->getIdFieldName() => $dbId,
      )
    );

    return $db->db_affected_rows();
  }

  /**
   * @param \Entity\EntityModel $cModel
   * @param array               $row
   *
   * @return int|string
   */
  public function insert($cModel, $row) {
    if (empty($row)) {
      // TODO Exception
      return 0;
    }
    $db = $this->db;
    $db->doInsertSet($cModel->getTableName(), $row);

    // TODO Exception if db_insert_id() is empty
    return $db->db_insert_id();
  }

  public function doSelectFetchValue($query) {
    return $this->db->doSelectFetchValue($query);
  }

  /**
   * Returns iterator to iterate through mysqli_result
   *
   * @param string $query
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public function doSelectIterator($query) {
    return $this->db->doSelectIterator($query);
  }

  public function doUpdateRowSetAffected($table, $fieldsAndValues, $where) {
    $this->db->doUpdateRowSet($table, $fieldsAndValues, $where);
    return $this->db->db_affected_rows();
  }

}
