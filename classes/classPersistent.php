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
      $this->$row[$this->sql_index_field] = $row[$this->sql_value_field];
    }

    $this->_DB_LOADED = true;
  }

  public function loadDefaults() {
    foreach($this->defaults as $defName => $defValue) {
      $this->$defName = $defValue;
    }
  }

  public function db_saveAll() {
//    $toSave = array();
//    foreach($this->defaults as $field => $value) {
//      $toSave[$field] = NULL;
//    }
//    $this->db_saveItem($toSave);
    // Для того, что бы не лезть в кэш за каждым айтемом, а сразу все известные переменные сохранить
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
      if($item_name) {
        $this->__set($item_name, $item_value);
      }
    }
  }

  /**
   * Makes cache to pass next operation to DB - whether it read or write
   */
  public function pass() {
    $this->force = true;
    return $this;
  }

  public function __get($name) {
    if($this->force) {
      $this->force = false;
      $this->db_loadItem($name);
    }

    return parent::__get($name);
  }

  public function __set($name, $value) {
    if($this->force) {
      $this->force = false;
      $this->db_saveItem($name, $value);
    }

    parent::__set($name, $value);
  }

}
