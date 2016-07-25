<?php

class DbRowSimple {

  /**
   * @param Entity $object
   * @param mixed  $rowId
   *
   * @return Entity
   */
  public function getById($object, $rowId) {
    $stmt = classSupernova::$gc->query
      ->setIdField($object::$idField)
      ->field('*')
      ->from($object::$tableName)
      ->where($object::$idField . ' = "' . $rowId . '"');

    $object->row = $stmt->selectRow();

    return $object;
  }

}
