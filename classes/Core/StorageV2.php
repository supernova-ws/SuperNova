<?php
/**
 * Created by Gorlum 21.04.2018 16:13
 */

namespace Core;


use DBAL\db_mysql;

class StorageV2 {

  /**
   * @var GlobalContainer $gc
   * @deprecated
   */
  protected $gc;

  /**
   * @var db_mysql
   */
  protected $db;

  /**
   * Core\Repository constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
    $this->db = $gc->db;
  }

  /**
   * @param db_mysql $db
   */
  public function setDb(db_mysql $db) {
    $this->db = $db;
  }

}
