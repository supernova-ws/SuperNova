<?php
/**
 * Created by Gorlum 13.11.2018 14:08
 */

namespace DBAL;


use Core\GlobalContainer;
use SN;

class StorageSqlV2 {
  /**
   * @var null|db_mysql $db
   */
  protected static $db = null;

  protected $forUpdate = false;

  public function __construct(GlobalContainer $services = null) {
    static::$db = !empty($services) ? $services->db : SN::$gc->db;
  }

  /**
   * @return $this
   */
  public function forUpdate() {
    $this->forUpdate = true;

    return $this;
  }

//  /**
//   * @param $tableName
//   * @param $conditions
//   *
//   * @return array|null
//   */
//  public function findFirstDbq($tableName, $conditions) {
//    $dbq = new DbQuery(static::$db);
//    $dbq
//      ->setTable($tableName)
//      ->setOneRow()
//      ->setWhereArray($conditions);
//
//    $array = static::$db->dbqSelectAndFetch($dbq);
//
//    return $array;
//  }

  /**
   * @param $tableName
   * @param $conditions
   *
   * @return DbMysqliResultIterator
   */
  public function findIterator($tableName, $conditions) {
    $dbq = new DbQuery(static::$db);
    $dbq
      ->setTable($tableName)
      ->setWhereArray($conditions);

    if ($this->forUpdate) {
      $dbq->setForUpdate();
    }

    return static::$db->selectIterator($dbq->select());
  }

  /**
   * @param $tableName
   * @param $conditions
   *
   * @return array|null
   */
  public function findFirst($tableName, $conditions) {
    $iterator = $this->findIterator($tableName, $conditions);

    $iterator->rewind();

    return $iterator->valid() ? $iterator->current() : [];
  }

  /**
   * @param $tableName
   * @param $conditions
   *
   * @return array
   */
  public function findAll($tableName, $conditions) {
    $result = [];

    foreach ($this->findIterator($tableName, $conditions) as $record) {
      $result[] = $record;
    }

    return $result;
  }

//  /**
//   * @param IRecordIndexed $record
//   * @param                $index
//   *
//   * @return IRecordIndexed
//   */
//  public function findById(IRecordIndexed $record, $index, $tableName, $conditions) {
//    // TODO: Remake it for Storage not to know how record implemented
//
//    $dbq = new DbQuery(static::$db);
//    $dbq
//      ->setTable($tableName)
//      ->setOneRow()
//      ->setWhereArray([$record::indexField() => $index]);
//
//    $array = static::$db->dbqSelectAndFetch($dbq);
//    if (!empty($array)) {
//
//    }
//
//    return $record;
//  }

  /**
   * @param string $tableName
   *
   * @return DbFieldDescription[]
   */
  public function fields($tableName) {
    return static::$db->schema()->getTableSchema($tableName)->fields;
  }


  /**
   * @param string $tableName
   * @param array  $fieldList
   *
   * @return int|null|string
   */
  public function insert($tableName, array $fieldList) {
    $dbq = new DbQuery(static::$db);
    $dbq
      ->setTable($tableName)
      ->setValues($fieldList);

    $result = static::$db->doquery($dbq->insert(DbQuery::DB_INSERT_PLAIN, true));

    return $result ? static::$db->db_insert_id() : null;
  }

  /**
   * @param string $tableName
   * @param array  $conditions
   *
   * @return bool|null
   */
  public function delete($tableName, $conditions) {
    $dbq = new DbQuery(static::$db);
    $dbq
      ->setTable($tableName)
      ->setWhereArray($conditions);

    return $dbq->doDeleteDb();
  }

  public function update($tableName, $conditions, $changes, $deltas) {
    $dbq = new DbQuery(static::$db);
    $dbq
      ->setTable($tableName)
      ->setValues($changes)
      ->setAdjust($deltas)
      ->setWhereArray($conditions);

//    var_dump($dbq->update());

//    die();

    return $dbq->doUpdate();
  }

  public function transactionStart($level = '') {
    static::$db->transactionStart($level);
  }

  public function transactionCommit() {
    static::$db->transactionCommit();
  }

  public function transactionRollback() {
    static::$db->transactionRollback();
  }

  /**
   * @return bool
   */
  public function transactionCheck() {
    return static::$db->transactionCheck();
  }

}
