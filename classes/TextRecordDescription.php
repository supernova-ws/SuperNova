<?php

/**
 * Created by Gorlum 10.02.2017 0:39
 */

use DBAL\db_mysql;

/**
 * Class TextRecordDescription
 *
 * Describe storage's record attributes
 */
class TextRecordDescription {
  public $table = 'text';
  public $indexFieldName = 'id';
  /**
   * @var db_mysql $db
   */
  public $db;

  /**
   * TextRecordDescription constructor.
   *
   * @param db_mysql|null $db
   */
  public function __construct($db = null) {
    $this->db = empty($db) ? SN::$gc->db : $db;
  }

}
