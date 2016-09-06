<?php


use \DBAL\DbQuery;
use \Entity\KeyedModel;
use \Entity\EntityModel;

/**
 * Class DbRowDirectOperator
 *
 * Handle Entity\EntityModel storing/loading operations
 */
class DbRowDirectOperator {
  /**
   * @var db_mysql $db
   */
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
   * Builds SELECT DbQuery
   *
   * @param EntityModel $mEntity
   * @param array       $where
   * @param array       $whereDanger
   *
   * @return DbQuery
   */
  protected function buildSelectDbQuery($mEntity, $where, $whereDanger = array()) {
    $dbQuery = new DbQuery($this->db);
    $dbQuery
      ->setFields(array('*'))// TODO - unused
      ->setTable($mEntity->getTableName())
      ->setWhereArray($where)
      ->setWhereArrayDanger($whereDanger);

    return $dbQuery;
  }

  /**
   * Gets Iterator for fetching query results
   *
   * @param EntityModel $model
   * @param array       $where
   * @param array       $whereDanger
   *
   * @return DbResultIterator
   */
  public function loadIterator($model, $where, $whereDanger = array()) {
    $dbQuery = $this->buildSelectDbQuery($model, $where, $whereDanger);
    $strSql = $dbQuery->select();

    return $this->doSelectIterator($strSql);
  }

  /**
   * Returns iterator to iterate through mysqli_result
   *
   * @param string $strSql
   *
   * return DbResultIterator
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public function doSelectIterator($strSql) {
    $queryResult = $this->db->doSelect($strSql);

    if ($queryResult instanceof mysqli_result) {
      $result = new DbMysqliResultIterator($queryResult);
    } else {
      $result = new DbEmptyIterator();
    }

    return $result;
  }

  /**
   * @param string $strSql
   *
   * @return array|null
   */
  public function doSelectFetchArray($strSql) {
    return $this->db->db_fetch($this->db->doSelect($strSql));
  }


  /**
   * @param string $query
   *
   * @return mixed|null
   */
  public function doSelectFetchValue($query) {
    $row = $this->doSelectFetchArray($query);

    return is_array($row) ? reset($row) : null;
  }















  /**
   * Loads one record according to filter
   *
   * @param EntityModel $model
   * @param array       $where
   * @param array       $whereDanger
   *
   * @return array|null
   */
  public function loadRecord($model, $where, $whereDanger = array()) {
    $dbQuery = $this->buildSelectDbQuery($model, $where, $whereDanger);
    $dbQuery->setOneRow(DB_RECORD_ONE);
    $strSql = $dbQuery->select();

    return $this->db->db_fetch($this->db->doSelect($strSql));
  }

  /**
   * Gets DB record array by dbId
   *
   * @param KeyedModel $model
   * @param int|string $dbId
   *
   * @return array|null
   */
  public function getById($model, $dbId) {
    return $this->loadRecord($model, array($model->getIdFieldName() => $dbId));
  }


  /**
   * @param KeyedModel $mKeyed
   * @param int|string $dbId
   *
   * @return int
   */
  public function deleteById($mKeyed, $dbId) {
    $db = $this->db;

    $db->doDeleteRow(
      $mKeyed->getTableName(),
      array(
        $mKeyed->getIdFieldName() => $dbId,
      )
    );

    return $db->db_affected_rows();
  }

  /**
   * @param EntityModel $mEntity
   * @param array       $row
   *
   * @return int|string
   */
  public function insert($mEntity, $row) {
    if (empty($row)) {
      // TODO Exception
      return 0;
    }
    $db = $this->db;
    $db->doInsertSet($mEntity->getTableName(), $row);

    // TODO Exception if db_insert_id() is empty
    return $db->db_insert_id();
  }


  public function doUpdateRowSetAffected($table, $fieldsAndValues, $where) {
    $this->db->doUpdateRowSet($table, $fieldsAndValues, $where);

    return $this->db->db_affected_rows();
  }

}
