<?php
/**
 * Created by Gorlum 19.06.2017 15:21
 */

namespace DBAL;


class TableSchema {

  /**
   * @var \DBAL\db_mysql $db
   */
  protected $db;

  public $tableName = '';

  /**
   * @var DbFieldDescription[] $fields
   */
  public $fields = [];
  /**
   * @var DbIndexDescription[] $indexesObject
   */
  public $indexes = [];
  /**
   * @var \array[] $constraints
   */
  public $constraints = [];

  /**
   * TableSchema constructor.
   *
   * @param string $tableName
   * @param Schema $dbSchema
   */
  public function __construct($tableName, Schema $dbSchema) {
    $this->db = $dbSchema->getDb();

    $this->tableName = $tableName;

    $this->fields      = $this->db->mysql_get_fields($this->tableName);
    $this->indexes     = $this->db->mysql_get_indexes($this->tableName);
    $this->constraints = $this->db->mysql_get_constraints($this->tableName);
  }

  public function isFieldExists($field) {
    return isset($this->fields[$field]);
  }

  public function isIndexExists($index) {
    return isset($this->indexes[$index]);
  }

}
