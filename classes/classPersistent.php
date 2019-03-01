<?php
/**
 * Created by Gorlum 29.10.2016 10:16
 */

/**
 *
 * Persistent is extension of class cacher and can save itself to DB
 * It's most usefull to hold basic structures as configuration, variables etc
 * Persistent pretty smart to handle one-level tables structures a-la "variable_name"+"variable_value"
 * Look supernova.sql to learn more
 * Also this class can holds default values for variables
 *
 * @package supernova
 *
 */
class classPersistent extends classCache {
  protected $table_name;
  protected $sql_index_field;
  protected $sql_value_field;

  protected $defaults = array();

  /**
   * List of fields which should have not empty values
   *
   * @var string[] $notEmptyFields [(str)fieldName => (str)fieldName, ...]
   */
  protected $notEmptyFields = [];

  /**
   * @var bool $force
   */
  protected $force = false;

  public function __construct($gamePrefix = 'sn_', $table_name = 'table') {
    parent::__construct("{$gamePrefix}{$table_name}_");
    $this->table_name = $table_name;

    $this->sql_index_field = "{$table_name}_name";
    $this->sql_value_field = "{$table_name}_value";

    if(!$this->_DB_LOADED) {
      $this->db_loadAll();
    }
  }

  public static function getInstance($gamePrefix = 'sn_', $table_name = '') {
    if (!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, $table_name);
    }
    return self::$cacheObject;
  }

  /**
   * @param string $index
   *
   * @return string|null
   */
  public function db_loadItem($index) {
    $result = null;
    if($index) {
      $index_safe = db_escape($index);
      $queryResult = doquery("SELECT `{$this->sql_value_field}` FROM `{{{$this->table_name}}}` WHERE `{$this->sql_index_field}` = '{$index_safe}' FOR UPDATE", true);
      if(is_array($queryResult) && !empty($queryResult)) {
        $this->$index = $result = $queryResult[$this->sql_value_field];
      }
    }

    return $result;
  }

  public function db_loadAll() {
    $this->loadDefaults();

    $query = doquery("SELECT * FROM {{{$this->table_name}}} FOR UPDATE;");
    while($row = db_fetch($query)) {
      $this[$row[$this->sql_index_field]] = $row[$this->sql_value_field];
    }

    $this->_DB_LOADED = true;
  }

  public function loadDefaults() {
    foreach($this->defaults as $defName => $defValue) {
      $this->$defName = $defValue;
    }
  }

  public function db_saveAll() {
    $this->db_saveItem(array_combine(array_keys($this->defaults), array_fill(0, count($this->defaults), null)));
  }

  public function db_saveItem($item_list, $value = NULL) {
    if(empty($item_list)) {
      return;
    }

    !is_array($item_list) ? $item_list = array($item_list => $value) : false;

    // Сначала записываем данные в базу - что бы поймать все блокировки
    $qry = array();
    foreach($item_list as $item_name => $item_value) {
      if($item_name) {
        $item_value = db_escape($item_value === NULL ? $this->$item_name : $item_value);
        $item_name = db_escape($item_name);
        $qry[] = "('{$item_name}', '{$item_value}')";
      }
    }
    doquery("REPLACE INTO `{{" . $this->table_name . "}}` (`{$this->sql_index_field}`, `{$this->sql_value_field}`) VALUES " . implode(',', $qry) . ";");

    // И только после взятия блокировок - меняем значения в кэше
    foreach($item_list as $item_name => $item_value) {
      if($item_name && $item_value !== null) {
        $this->__set($item_name, $item_value);
      }
    }
  }

  /**
   * Instructs cache to pass next operation to DB - whether it read or write
   *
   * This allows more transparency when accessing variables. So
   *    $this->db_loadItem('variable_name')
   * converts to
   *    $this->pass()->variable_name
   * Latest makes IDE aware of operation with variables and makes navigation and code refactoring (i.e. variable renaming) much easier
   * Same work with saving items directly to DB:
   *    $this->db_saveItem('variable_name', $value)
   * becomes
   *    $this->pass()->variable_name = $value;
   *
   * @return $this
   */
  public function pass() {
    $this->force = true;

    return $this;
  }

  public function __get($name) {
    if($this->force) {
      $this->force = false;
      $value = $this->db_loadItem($name);
    } else {
      $value = parent::__get($name);
    }

    if(isset($this->notEmptyFields[$name]) && empty($value) && isset($this->defaults[$name])) {
      $value = $this->defaults[$name];
    }

    return $value;
  }

  public function __set($name, $value) {
    if($this->force) {
      $this->force = false;
      $this->db_saveItem($name, $value);
    }

    parent::__set($name, $value);
  }

  public function __unset($name) {
    doquery('DELETE FROM `{{config}}` WHERE `config_name` = "' . SN::$db->db_escape($name) . '"');

    parent::__unset($name);
  }

}
