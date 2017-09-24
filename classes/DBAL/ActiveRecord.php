<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;

/**
 * Class ActiveRecord
 *
 * Represent table in DB/one record in DB. Breaking SRP with joy!
 *
 * @property int|string $id - Record ID name would be normalized to 'id'
 *
 * @package DBAL
 */
abstract class ActiveRecord extends ActiveRecordAbstract {

  /**
   * @inheritdoc
   */
  protected function dbInsert() {
    return
      static::dbPrepareQuery()
        ->setValues(static::translateNames($this->values, self::PROPERTIES_TO_FIELDS))
        ->doInsert();
  }

  /**
   * @inheritdoc
   */
  protected function dbLastInsertId() {
    return static::db()->db_insert_id();
  }

  /**
   * @inheritdoc
   */
  protected function dbUpdate() {
    return
      static::dbPrepareQuery()
        ->setValues(empty($this->_changes) ? [] : static::translateNames($this->_changes, self::PROPERTIES_TO_FIELDS))
        ->setAdjust(empty($this->_deltas) ? [] : static::translateNames($this->_deltas, self::PROPERTIES_TO_FIELDS))
        ->setWhereArray([static::$_primaryIndexField => $this->id])
        ->doUpdate();
  }

}
