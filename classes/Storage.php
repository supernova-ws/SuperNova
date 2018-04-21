<?php

/**
 * Created by Gorlum 10.02.2017 0:28
 */

use Core\GlobalContainer;

/**
 * Class Storage
 * @deprecated
 */
class Storage {

  /**
   * Storage constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
  }

  /**
   * @param TextRecordDescription $recordDescription
   * @param string|int            $id
   *
   * @return array|null
   */
  public function loadById($recordDescription, $id) {
    $dbq = new \DBAL\DbQuery($recordDescription->db);
    $dbq
      ->setTable($recordDescription->table)
      ->setOneRow()
      ->setWhereArray(array($recordDescription->indexFieldName => $id));
    return $recordDescription->db->dbqSelectAndFetch($dbq);
  }

}
