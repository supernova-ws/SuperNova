<?php

/**
 * Interface IDbRow
 */
interface IDbRow {

  /**
   * Loading object from DB by primary ID
   *
   * @param int  $dbId
   * @param bool $lockSkip
   *
   * @return
   */
  public function dbLoad($dbId, $lockSkip = false);

  /**
   * Saving object to DB
   * This is meta-method:
   * - if object is new - then it inserted to DB. Usually governed by isNew() method;
   * - if object is empty - it deleted from DB. Usually governed by isEmpty() method;
   * - otherwise object is updated in DB;
   */
  public function dbSave();

}
