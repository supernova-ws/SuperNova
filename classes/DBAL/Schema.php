<?php
/**
 * Created by Gorlum 12.06.2017 15:29
 */

namespace DBAL;


class Schema {
  /**
   * @var db_mysql $db
   */
  protected $db;
  /**
   * List of all table names
   *
   * @var string[] $tablesAll
   */
  protected $tablesAll = null;
  /**
   * @var string[] $tablesSn
   */
  protected $tablesSn = null;
  /**
   * @var TableSchema[] $tableSchemas
   */
  protected $tableSchemas = [];

  public function __construct(db_mysql $db) {
    $this->db = $db;
  }

  public function getDb() {
    return $this->db;
  }

  public function clear() {
    $this->tablesAll    = null;
    $this->tablesSn     = null;
    $this->tableSchemas = [];
  }

  protected function loadTableNamesFromDb() {
    $this->clear();
    $this->tablesAll = array();
    $this->tablesSn  = array();

    $query = $this->db->mysql_get_table_list();

    $prefix_length = strlen($this->db->db_prefix);

    while ($row = $this->db->db_fetch($query)) {
      foreach ($row as $table_name) {
        $this->tablesAll[$table_name] = $table_name;

        if (strpos($table_name, $this->db->db_prefix) === 0) {
          $table_name_sn = substr($table_name, $prefix_length);

          $this->tablesSn[$table_name_sn] = $table_name_sn;
        }
      }
    }
  }

  /**
   * Get names of all tables in this DB
   *
   * @return \string[]
   */
  public function getAllTables() {
    if (!isset($this->tablesAll)) {
      $this->loadTableNamesFromDb();
    }

    return $this->tablesAll;
  }

  /**
   * Get un-prefixed table names potentially used by game
   *
   * @return string[]
   */
  public function getSnTables() {
    if (!isset($this->tablesSn)) {
      $this->loadTableNamesFromDb();
    }

    return $this->tablesSn;
  }

  /**
   * Checks if SN table exists
   *
   * @param $tableName
   *
   * @return bool
   */
  public function isSnTableExists($tableName) {
    return isset($this->getSnTables()[$tableName]);
  }

  /**
   * @param string $tableName
   *
   * @return TableSchema
   */
  public function getTableSchema($tableName) {
    if (empty($this->tableSchemas[$tableName])) {
      $this->tableSchemas[$tableName] = new TableSchema($tableName, $this);
    }

    return $this->tableSchemas[$tableName];
  }

  /**
   * @param $table
   * @param $index
   *
   * @return bool
   */
  public function isIndexExists($table, $index) {
    return $this->isSnTableExists($table) && $this->getTableSchema($table)->isIndexExists($index);
  }

  public function isFieldExists($table, $field) {
    return $this->isSnTableExists($table) && $this->getTableSchema($table)->isFieldExists($field);
  }

}
