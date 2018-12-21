<?php
/**
 * Created by Gorlum 04.10.2017 8:23
 */

namespace DBAL;

/**
 * Class ActiveRecordAbstractIndexed
 * @package DBAL
 *
 * @property int|string $id - Record ID name would be normalized to 'id'
 *
 */
class ActiveRecordAbstractIndexed extends ActiveRecordAbstract {
  const ID_PROPERTY_NAME = 'id';

  /**
   * Autoincrement index field name in DB
   * Would be normalized to 'id' ($id class property)
   *
   * @var string $_primaryIndexField
   */
  protected static $_primaryIndexField = 'id';


  /**
   * @param int|string $recordId
   *
   * @return string[]
   */
  public static function findRecordById($recordId) {
    return static::findRecordFirst([self::ID_PROPERTY_NAME => $recordId]);
  }

  /**
   * @param int|string $recordId
   *
   * @return bool|static
   */
  public static function findById($recordId) {
    return static::findFirst([self::ID_PROPERTY_NAME => $recordId]);
  }

  /**
   * @return bool
   */
  public function update() {
    if (empty($this->_changes) && empty($this->_deltas)) {
      return true;
    }

    $this->defaultValues();

    if (!$this->dbUpdate()) {
      return false;
    }


    $this->accept();

//    $this->_isNew = false;

    return true;
  }

  public function delete() {
    $result = static::dbPrepareQuery()
      ->setWhereArray([static::$_primaryIndexField => $this->id])
      ->doDelete();

    if ($result) {
      $this->id = 0;
      $this->_isDeleted = true;
    }

    return $result;
  }


//  /**
//   * Reload current record from ID
//   *
//   * @return bool
//   */
//  public function reload() {
//    //$recordId = $this->id;
//    $recordId = $this->{self::ID_PROPERTY_NAME};
//    if (empty($recordId)) {
//      return false;
//    }
//
//    $this->accept();
//
//    $fields = static::findRecordFirst($recordId);
//    if (empty($fields)) {
//      return false;
//    }
//
//    $this->fromFields($fields);
//    $this->_isNew = false;
//
//    return true;
//  }
//
//  protected static function dbFetch(\mysqli_result $mysqliResult) {
//    return $mysqliResult->fetch_assoc();
//  }


  /**
   * @return bool
   */
  protected function dbUpdate() {
    return
      static::dbPrepareQuery()
        ->setValues(empty($this->_changes) ? [] : static::translateNames($this->_changes, self::PROPERTIES_TO_FIELDS))
        ->setAdjust(empty($this->_deltas) ? [] : static::translateNames($this->_deltas, self::PROPERTIES_TO_FIELDS))
        ->setWhereArray([static::$_primaryIndexField => $this->id])
        ->doUpdate();
  }








  // Some overrides

  /**
   * @inheritdoc
   */
  protected static function getFieldName($propertyName) {
    return
      $propertyName == static::ID_PROPERTY_NAME
        ? static::$_primaryIndexField
        : parent::getFieldName($propertyName);
  }

  /**
   * @inheritdoc
   */
  protected static function getPropertyName($fieldName) {
    return
      $fieldName == static::$_primaryIndexField
        ? static::ID_PROPERTY_NAME
        : parent::getPropertyName($fieldName);
  }

//  // TODO - Возможно - не нужно наследовать
//  public function accept() {
//    parent::accept();
////    $this->_isNew = empty($this->id);
//  }

  /**
   * @inheritdoc
   */
  // TODO - do a check that all fields present in stored data. I.e. no empty fields with no defaults
  public function insert() {
    if (!parent::insert()) {
      return false;
    }

    $this->{self::ID_PROPERTY_NAME} = $this->dbLastInsertId();
    $this->accept();

    return true;
  }

  /**
   * @return int|string
   */
  protected function dbLastInsertId() {
    return static::db()->db_insert_id();
  }

}
