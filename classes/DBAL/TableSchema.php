<?php
/**
 * Created by Gorlum 19.06.2017 15:21
 */

namespace DBAL;


class TableSchema {

  /**
   * @var \db_mysql $db
   */
  protected $db;

  /**
   * @var Schema $dbSchema
   */
  protected $dbSchema;

  public $tableName = '';

  /**
   * @var \array[] $fields
   */
  public $fields;
  /**
   * @var \array[] $indexes
   */
  public $indexes;
  /**
   * @var \array[] $foreign
   */
  public $foreign;

  /**
   * TableSchema constructor.
   *
   * @param string         $tableName
   * @param \db_mysql|null $db
   */
  public function __construct($tableName, Schema $dbSchema) {
    $this->dbSchema = $dbSchema;
    $this->tableName = $tableName;

    $this->db = $this->dbSchema->getDb();

    $this->fields = $this->db->mysql_get_fields($this->tableName);
    $this->indexes = $this->db->mysql_get_indexes($this->tableName);
    $this->foreign = $this->db->mysql_get_foreign($this->tableName);
  }

}
