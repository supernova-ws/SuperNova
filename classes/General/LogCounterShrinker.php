<?php
/**
 * Created by Gorlum 17.10.2017 8:36
 */

namespace General;

use Core\GlobalContainer;
use \DBAL\DbMysqliResultIterator;

/**
 * Class LogCounterShrinker
 *
 * This class shrinking user visit log (table `counter`) by merging conclusive records with same signature
 *
 * @package General
 */
class LogCounterShrinker extends VisitMerger {
  const RESERVE_WEEKS = 0; // How much weeks of logs left unshrinked
  const BATCH_DELETE_PER_LOOP = 10000;
  const BATCH_UPDATE_PER_LOOP = 10000;
  const BATCH_DELETE_PER_QUERY = 25;

  protected $batchSize = 10000;

  /**
   * @var array
   */
  protected $batchDelete = [];
  /**
   * @var VisitAccumulator[] $batchUpdate
   */
  protected $batchUpdate = [];

  /**
   * Records skipped in current batch - not changed during process
   *
   * @var int $batchSkipped
   */
  protected $batchSkipped = 0;
  protected $totalProcessed = 0;


  public function __construct(GlobalContainer $gc) {
    parent::__construct($gc);
  }

  /**
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   *
   * @return bool
   */

  /**
   * @return DbMysqliResultIterator
   */
  protected function buildIterator() {
    return $this->gc->db->selectIterator(
      "SELECT * 
        FROM `{{counter}}`
        WHERE `visit_time` < DATE_SUB(NOW(), INTERVAL " . static::RESERVE_WEEKS . " WEEK)
        AND `counter_id` > {$this->batchEnd} 
        ORDER BY `visit_time`, `counter_id` 
        LIMIT {$this->batchSize};"
    );
  }


  /**
   * @inheritdoc
   */
  public function process($cutTails = true) {
    ini_set('memory_limit', '1000M');

    $result = [];

    while (($iter = $this->buildIterator()) && $iter->count()) {
      $this->setIterator($iter);

      parent::process(false);

      $this->batchSave();

      $this->totalProcessed += $this->batchProcessed;

      if ($this->prevBatchStart == $this->batchStart && $this->prevBatchEnd == $this->batchEnd) {
        $result = [
          'STATUS'  => ERR_WARNING,
          'MESSAGE' => "{Зациклились с размером блока {$this->batchSize} - [{$this->batchStart},{$this->batchEnd}].",
        ];
        break;
      }

      if ($this->batchProcessed < $this->batchSize) {
        $result = [
          'STATUS'  => ERR_WARNING,
          'MESSAGE' => "{Размер текущего блока {$this->batchProcessed} меньше максимального {$this->batchSize} между ID [{$this->batchStart},{$this->batchEnd}].",
        ];
        break;
      }
    }

    $this->cutTails();
    $this->batchSave(true);

    if (is_array($result)) {
      $result['MESSAGE'] .= " {Обработано} {$this->totalProcessed}, {пропущено} {$this->batchSkipped}";
    }

    return $result;
  }

  protected function batchSave($forceSave = false) {
    $this->gc->db->doQueryFast('START TRANSACTION');

    if (count($this->batchDelete) >= static::BATCH_DELETE_PER_LOOP || $forceSave) {
      $this->deleteMergedRecords($this->batchDelete);
      $this->batchDelete = [];
    }

    if (count($this->batchUpdate) >= static::BATCH_UPDATE_PER_LOOP || $forceSave) {
      foreach ($this->batchUpdate as $record) {
        if (!$record->isChanged()) {
          $this->batchSkipped++;
          continue;
        }

        $this->addMoreTime();
        $this->gc->db->doQueryFast(
          'UPDATE `{{counter}}`
          SET ' .
          '`visit_length` = ' . $record->length . ',' .
          '`hits` = ' . $record->hits .
          ' WHERE `counter_id` = ' . $record->counterId . ';'
        );
      }
      $this->batchUpdate = [];
    }

    $this->gc->db->doQueryFast('COMMIT');
  }

  protected function flushVisit($sign) {
    if (!empty($this->data[$sign])) {
      $this->batchUpdate[] = $this->data[$sign];
    }

    if (!empty($this->mergedIds[$sign]) && is_array($this->mergedIds[$sign])) {
      $this->batchDelete = array_merge($this->batchDelete, $this->mergedIds[$sign]);
    }
  }


  /**
   * @param array $array
   */
  protected function deleteMergedRecords($array) {
    if (!is_array($array) || empty($array)) {
      return;
    }

    // Batch delete
    $i = 0;
    $tempArray = [];
    foreach ($array as $recordDeleteId) {
      $tempArray[] = $recordDeleteId;
      if ($i++ > static::BATCH_DELETE_PER_QUERY) {
        $this->addMoreTime();
        $this->dbDeleteExecute($tempArray);
        $i = 0;
      }
    }

    // Emptying possible tails
    if (!empty($tempArray)) {
      $this->dbDeleteExecute($tempArray);
    }
  }

  /**
   * @param array $toDeleteArray
   */
  protected function dbDeleteExecute(&$toDeleteArray) {
    $this->gc->db->doQueryFast('DELETE FROM `{{counter}}` WHERE `counter_id` IN (' . implode(',', $toDeleteArray) . ')');
    $toDeleteArray = [];
  }

}
