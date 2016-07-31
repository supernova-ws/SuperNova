<?php

/**
 * Created by Gorlum 31.07.2016 12:08
 */

namespace DBAL;

use \classSupernova;
use Common\GlobalContainer;
use \SnCache;
use \db_mysql;

class DbTransaction {

  protected static $db_in_transaction = false;
  protected static $transaction_id = 0;

  /**
   * @var db_mysql $db
   */
  protected $db;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * DbTransaction constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->gc = $gc;
    $this->db = $gc->db;
  }

  /**
   * Эта функция проверяет статус транзакции
   *
   * Это - низкоуровневая функция. В нормальном состоянии движка её сообщения никогда не будут видны
   *
   * @param null|true|false $status Должна ли быть запущена транзакция в момент проверки
   *   <p>null - транзакция НЕ должна быть запущена</p>
   *   <p>true - транзакция должна быть запущена - для совместимости с $for_update</p>
   *   <p>false - всё равно - для совместимости с $for_update</p>
   *
   * @return bool Текущий статус транзакции
   */
  public static function db_transaction_check($status = null) {
    $error_msg = false;
    if ($status && !static::$db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif ($status === null && static::$db_in_transaction) {
      $error_msg = 'Transaction is already started';
    }

    if (!empty($error_msg)) {
      // TODO - Убрать позже
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_transaction_check() - ' . $error_msg . '</h1>');
      $backtrace = debug_backtrace();
      array_shift($backtrace);
      pdump($backtrace);
      die($error_msg);
    }

    return static::$db_in_transaction;
  }

  public static function db_transaction_start($level = '') {
    static::db_transaction_check(null);

    $level ? doquery('SET TRANSACTION ISOLATION LEVEL ' . $level) : false;

    static::$transaction_id++;
    doquery('START TRANSACTION');

    if (classSupernova::$gc->config->db_manual_lock_enabled) {
      classSupernova::$gc->config->db_loadItem('var_db_manually_locked');
      classSupernova::$gc->config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    static::$db_in_transaction = true;
    SnCache::locatorReset();
    SnCache::queriesReset();

    return static::$transaction_id;
  }

  // TODO - move changeset data and methods somewhere
  public static function db_transaction_commit() {
    static::db_transaction_check(true);

    if (!empty(classSupernova::$delayed_changset)) {
      classSupernova::db_changeset_apply(classSupernova::$delayed_changset, true);
    }
    doquery('COMMIT');

    return static::db_transaction_clear();
  }

  public static function db_transaction_rollback() {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция

    if (!empty(classSupernova::$delayed_changset)) {
//      static::db_changeset_revert();
      // TODO Для этапа 1 - достаточно чистить только те таблицы, что были затронуты
      // Для этапа 2 - чистить только записи
      // Для этапа 3 - возвращать всё
      SnCache::cache_clear_all(true);
    }
//    $this->db->doquery('ROLLBACK');
    doquery('ROLLBACK');

    return static::db_transaction_clear();
  }

  protected static function db_transaction_clear() {
    classSupernova::$delayed_changset = array();
    SnCache::cache_lock_unset_all();

    static::$db_in_transaction = false;
    static::$transaction_id++;

    return static::$transaction_id;
  }

  /**
   * Get Transaction ID for next query
   *
   * If transaction is in progress - next ID would be equal to current
   * Otherwise we should increase ID and return this value
   *
   * @return int
   */
  public static function getNextQueryTransactionId() {
    return static::db_transaction_check(false) ? static::$transaction_id : static::$transaction_id++;
  }

}
