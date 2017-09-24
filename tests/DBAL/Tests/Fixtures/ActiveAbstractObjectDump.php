<?php
/**
 * Created by Gorlum 13.07.2017 15:33
 */

namespace DBAL\Tests\Fixtures;


use DBAL\ActiveRecordAbstract;

class ActiveAbstractObjectDump extends ActiveRecordAbstract {
  protected static $_tableName = '';

  /**
   * @return bool
   */
  protected function dbInsert() {
    // TODO: Implement dbInsert() method.
  }

  /**
   * Asks DB for last insert ID
   *
   * @return int|string
   */
  protected function dbLastInsertId() {
    // TODO: Implement dbLastInsertId() method.
  }

  /**
   * @return bool
   */
  protected function dbUpdate() {
    // TODO: Implement dbUpdate() method.
  }

}
