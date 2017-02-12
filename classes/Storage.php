<?php

/**
 * Created by Gorlum 10.02.2017 0:28
 */
class Storage {

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
