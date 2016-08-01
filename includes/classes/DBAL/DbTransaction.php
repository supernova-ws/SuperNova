<?php

/**
 * Created by Gorlum 31.07.2016 12:08
 */

namespace DBAL;

use \classSupernova;
use Common\GlobalContainer;
use \db_mysql;

class DbTransaction {

  protected $db_in_transaction = false;
  protected $transaction_id = 0;

  /**
   * @var db_mysql $db
   */
  protected $db;

  /**
   * @var \SnCache $snCache
   */
  protected $snCache;

  /**
   * DbTransaction constructor.
   *
   * @param GlobalContainer $gc
   * @param db_mysql $db
   */
  public function __construct($gc, $db) {
    $this->db = $db;
    $this->snCache = $db->snCache;
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
  public function check($status = null) {
    $error_msg = false;
    if ($status && !$this->db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif ($status === null && $this->db_in_transaction) {
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

    return $this->db_in_transaction;
  }

  public function start($level = '') {
    $this->check(null);

    $level ? $this->db->doExecute('SET TRANSACTION ISOLATION LEVEL ' . $level) : false;

    $this->transaction_id++;
    $this->db->doExecute('START TRANSACTION');
//pdump(debug_backtrace());pdie();

    if (classSupernova::$gc->config->db_manual_lock_enabled) {
      classSupernova::$gc->config->db_loadItem('var_db_manually_locked');
      classSupernova::$gc->config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    $this->db_in_transaction = true;
    $this->db->snCache->locatorReset();
    $this->db->snCache->queriesReset();

    return $this->transaction_id;
  }

  // TODO - move changeset data and methods somewhere
  public function commit() {
    $this->check(true);

    $this->db->doExecute('COMMIT');

    return $this->db_transaction_clear();
  }

  public function rollback() {
    // TODO - вообще-то тут тоже надо проверять есть ли транзакция

    $this->db->doExecute('ROLLBACK');

    return $this->db_transaction_clear();
  }

  protected function db_transaction_clear() {
    $this->db->snCache->cache_lock_unset_all();

    $this->db_in_transaction = false;
    $this->transaction_id++;

    return $this->transaction_id;
  }

  /**
   * Get Transaction ID for next query
   *
   * If transaction is in progress - next ID would be equal to current
   * Otherwise we should increase ID and return this value
   *
   * @return int
   */
  public function getNextQueryTransactionId() {
    return $this->check(false) ? $this->transaction_id : $this->transaction_id++;
  }

}
