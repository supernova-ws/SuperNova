<?php
/**
 * Created by Gorlum 17.10.2017 8:36
 */

namespace General;

use Common\GlobalContainer;

/**
 * Class LogCounterShrinker
 *
 * This class shrinking user visit log (table `counter`) by merging conclusive records with same signature
 *
 * @package General
 */
class LogCounterShrinker extends VisitMerger {
  const PLAYER_ACTIVITY_MAX_INTERVAL = PERIOD_MINUTE_15;
  const BATCH_DELETE_PER_LOOP = 1000;
  const BATCH_UPDATE_PER_LOOP = 1000;
  const BATCH_DELETE_PER_QUERY = 25;

  public $maxInterval = 0;
  protected $batchSize = 10000;

  protected $_debug = false;

  /**
   * @var array
   */
  protected $batchDelete = [];
  /**
   * @var VisitAccumulator[] $batchUpdate
   */
  protected $batchUpdate = [];

  public function __construct(GlobalContainer $gc) {
    parent::__construct($gc);

    $this->maxInterval = static::PLAYER_ACTIVITY_MAX_INTERVAL;

    $this->_extendTime = 30;
  }

  /**
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   *
   * @return bool
   */
  protected function isSameVisit($sign, $logRecord) {
    return $this->data[$sign]->time + $this->data[$sign]->length + $this->maxInterval >= $logRecord->time;
  }

  public function process($cutTails = true) {
    ini_set('memory_limit', '1000M');

    if ($this->_debug) {
      print("<table>");
    }

    while (
      ($iter = $this->gc->db->selectIterator($q =
        "SELECT * 
        FROM `{{counter}}`
        WHERE `visit_time` < DATE_SUB(NOW(), INTERVAL 3 WEEK)
        AND `counter_id` > {$this->batchEnd} 
        ORDER BY `visit_time`, `counter_id` 
        LIMIT {$this->batchSize}
        ;")
      )
      &&
      $iter->count()
    ) {
      $this->setIterator($iter);

      parent::process(false);

      if ($this->prevBatchStart == $this->batchStart && $this->prevBatchEnd == $this->batchEnd) {
        die("{Зациклились с размером блока {$this->batchSize} - [{$this->batchStart},{$this->batchEnd}]}");
      }

      $this->batchSave();
    }

    $this->cutTails();
    $this->batchSave(true);

    if ($this->_debug) {
      print("</table>");
    }
  }

  protected function batchSave($forceSave = false) {
    $this->gc->db->doQueryFast('START TRANSACTION');
    if (count($this->batchDelete) >= static::BATCH_DELETE_PER_LOOP || $forceSave) {
      $this->deleteMergedRecords($this->batchDelete);
      $this->batchDelete = [];
    }
    if (count($this->batchUpdate) >= static::BATCH_UPDATE_PER_LOOP || $forceSave) {
      foreach ($this->batchUpdate as $record) {
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
    $this->batchUpdate[] = $this->data[$sign];
    is_array($this->mergedIds[$sign]) ? $this->batchDelete = array_merge($this->batchDelete, $this->mergedIds[$sign]) : false;

//    $this->gc->db->doQueryFast('START TRANSACTION');
//    $this->gc->db->doQueryFast(
//      'UPDATE `{{counter}}`
//      SET `visit_length` = ' . $this->data[$sign]->length . '
//      WHERE `counter_id` = ' . $this->data[$sign]->counterId . ';'
//    );
//
//    if (!empty($this->mergedIds[$sign]) && is_array($this->mergedIds[$sign])) {
//      $this->deleteMergedRecords($this->mergedIds[$sign]);
//    }
//    $this->gc->db->doQueryFast('COMMIT');
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


  // DEBUG FUNCTIONS ===================================================================================================

//  protected function newVisit($sign, $logRecord) {
//    // TODO - remove debug
//    $this->dump($logRecord, $sign, "new visit");
//    parent::newVisit($sign, $logRecord);
//  }
//
//  protected function resetVisit($sign, $logRecord) {
//    // TODO - remove debug
//    $this->dump($logRecord, $sign, "prevVisitLength = {$this->data[$sign]->length}. restarting visit");
//    parent::resetVisit($sign, $logRecord);
//  }

  /**
   * @param VisitAccumulator $logRecord
   * @param string           $sign
   * @param string           $message
   */
  protected function dump($logRecord, $sign, $message) {
    if (!$this->_debug) {
      return;
    }
    $visitTimeStr = date(FMT_DATE_TIME_SQL, $logRecord->time);
    print("<tr><td>{$visitTimeStr}</td><td>[{$sign}]</td><td>{$message}</td></tr>");
  }

}
