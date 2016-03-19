<?php

/**
 * Interface IDbRow
 */
interface IDbRow {
  /**
   * Loading object from DB by primary ID
   *
   * @param int $dbId
   */
  public function dbLoad($dbId);

  public function dBSave();

}
