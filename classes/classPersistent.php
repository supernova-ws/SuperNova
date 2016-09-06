<?php

/**
 *
 * Persistent is extension of class cacher and can save itself to DB
 * It's most usefull to hold basic structures as configuration, variables etc
 * Persistent pretty smart to handle one-level tables structures a-la "variable_name"+"variable_value"
 * Look supernova.sql to learn more
 * Also this class can holds default values for variables
 *
 * @property bool _DB_LOADED
 *
 * @package supernova
 */
class classPersistent extends classCache {
  protected $table_name;
  protected $sql_index_field;
  protected $sql_value_field;

  protected $defaults = array();

  public function __construct($gamePrefix = 'sn_', $table_name = 'table') {
    parent::__construct("{$gamePrefix}{$table_name}_");
    $this->table_name = $table_name;

    $this->sql_index_field = "{$table_name}_name";
    $this->sql_value_field = "{$table_name}_value";

    if (!$this->_DB_LOADED) {
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

  public function db_loadItem($index) {
    $result = null;
    if ($index) {
      $index_safe = db_escape($index);
      $result = classSupernova::$db->doSelectFetchArray("SELECT `{$this->sql_value_field}` FROM `{{{$this->table_name}}}` WHERE `{$this->sql_index_field}` = '{$index_safe}' FOR UPDATE");
      // В две строки - что бы быть уверенным в порядке выполнения
      $result = $result[$this->sql_value_field];
      $this->$index = $result;
    }

    return $result;
  }

  public function db_loadAll() {
    $this->loadDefaults();

    $query = classSupernova::$db->doSelect("SELECT * FROM {{{$this->table_name}}} FOR UPDATE;");
    while ($row = db_fetch($query)) {
      $this->$row[$this->sql_index_field] = $row[$this->sql_value_field];
    }

    $this->_DB_LOADED = true;
  }

  public function loadDefaults() {
    foreach ($this->defaults as $defName => $defValue) {
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

  public function db_saveItem($item_list, $value = null) {
    if (empty($item_list)) {
      return;
    }

    !is_array($item_list) ? $item_list = array($item_list => $value) : false;

    // Сначала записываем данные в базу - что бы поймать все блокировки
    $qry = array();
    foreach ($item_list as $item_name => $item_value) {
      if ($item_name) {
        $item_value = $item_value === null ? $this->$item_name : $item_value;
//        $item_name = $item_name;
        $qry[] = array($item_name, $item_value);
      }
    }
    classSupernova::$gc->db->doInsertBatch(
      $this->table_name, $qry, array(
      $this->sql_index_field,
      $this->sql_value_field,
    ), DB_INSERT_REPLACE
    );


    // И только после взятия блокировок - меняем значения в кэше
    foreach ($item_list as $item_name => $item_value) {
      if ($item_name && $item_value !== null) {
        $this->$item_name = $item_value;
      }
    }
  }
}
