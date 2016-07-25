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

  /**
   * @param Entity $object
   */
  public function deleteById($object) {
    classSupernova::$gc->db->doquery("DELETE FROM `{{" . $object::$tableName . "}}` WHERE `{$object::$idField}` = '{$object->row[$object::$idField]}' LIMIT 1;");

    return classSupernova::$gc->db->db_affected_rows();
  }

}
