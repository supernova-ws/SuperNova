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

    $object->setRow($stmt->selectRow());

    return $object;
  }

  /**
   * @param Entity $object
   */
  public function deleteById($object) {
    $db = classSupernova::$gc->db;

    $db->doquery("DELETE FROM `{{" . $object::$tableName . "}}` WHERE `{$object::$idField}` = '{$object->getDbId()}' LIMIT 1;");

    return $db->db_affected_rows();
  }

  /**
   * @param Entity $object
   */
  public function insert($object) {
    $db = classSupernova::$gc->db;

    $query = array();
    foreach ($object->getRow() as $fieldName => $fieldValue) {
      if($fieldName == $object::$idField) {
        continue;
      }
      $fieldValue = $db->db_escape($fieldValue);
      $query[] = "`{$fieldName}` = '{$fieldValue}'";
    }

    $query = implode(',', $query);
    if (empty($query)) {
      // TODO Exceptiion
      return 0;
    }

    $db->doquery("INSERT INTO `{{" . $object::$tableName . "}}` SET " . $query);

    // TODO Exceptiion if db_insert_id() is empty
    $dbId = $db->db_insert_id();
    $object->setDbId($dbId);

    return $dbId;
  }

}
