<?php
/**
 * Created by Gorlum 17.10.2017 10:20
 */

namespace General;

use \Common\GlobalContainer;
use \Iterator;
use \EmptyIterator;

/**
 * Class VisitMerger
 *
 * Class merges supplied visits with provided criterias
 *
 * @package General
 */
abstract class VisitMerger {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var VisitAccumulator[] $data
   */
  protected $data = [];
  /**
   * List of IDs of log record that was merged with upper ones
   *
   * @var array $mergedIds
   */
  protected $mergedIds = [];

  /**
   * @var Iterator|null
   */
  protected $iterator = null;

  /**
   * Time to extend PHP execution time each loop
   *
   * @var int $_extendTime
   */
  protected $_extendTime = 0;

  protected $prevBatchStart = 0;
  protected $prevBatchEnd = 0;
  protected $batchStart = 0;
  protected $batchEnd = 0;

  /**
   * General constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
    $this->iterator = new EmptyIterator();
  }

  public function setIterator(Iterator $iterator) {
    $this->iterator = $iterator;
  }

  /**
   * Is supplied log record could be merged to data array, indicated by $sign
   *
   * Can (and should!) be overrode by successors to provide different groupping mechanic
   *
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   *
   * @return bool
   */
  abstract protected function isSameVisit($sign, $logRecord);

  /**
   * Class entry point
   */
  public function process($cutTails = true) {
    $this->prevBatchStart = $this->batchStart;
    $this->prevBatchEnd = $this->batchEnd;
    if ($this->iterator->valid()) {
      $logRecord = VisitAccumulator::build($this->iterator->current());
      $this->batchStart = $logRecord->counterId;
    }

    foreach ($this->iterator as $row) {
      $this->addMoreTime();
      $logRecord = VisitAccumulator::build($row);
      $this->processRecord($logRecord);
    }
    $this->batchEnd = isset($logRecord) ? $logRecord->counterId : $this->batchStart;

    $this->cutTails();
  }

  /**
   * @param VisitAccumulator $logRecord
   */
  protected function processRecord($logRecord) {
    $sign = $this->calcSignature($logRecord);

    if (!isset($this->data[$sign])) {
      $this->newVisit($sign, $logRecord);
    } else {
      if ($this->isSameVisit($sign, $logRecord)) {
        $this->extendVisit($sign, $logRecord);
      } else {
        $this->resetVisit($sign, $logRecord);
      }
    }
  }

  /**
   * Cutting tales - working on opened visits after all rows processed
   */
  protected function cutTails() {
    foreach ($this->data as $sign => $tails) {
      if ($tails->length > 0) {
        $this->addMoreTime();
        $this->resetVisit($sign, $tails);
      }
    }
  }

  /**
   * Getting signature (name of group to which this visit can belong) from logged visit
   *
   * Signature made from user ID, device ID, browser ID and IPs
   *
   * @param VisitAccumulator $logRecord
   *
   * @return string
   */
  protected function calcSignature($logRecord) {
    return
      $logRecord->userId . '_' .
      $logRecord->deviceId . '_' .
      $logRecord->browserId . '_' .
      $logRecord->ip . '_' .
      $logRecord->proxies;
  }

  /**
   * Starting new visit from supplied log record
   *
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   */
  protected function newVisit($sign, $logRecord) {
    $this->data[$sign] = $logRecord;
  }


  /**
   * Extending current visit with supplied log record
   *
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   */
  protected function extendVisit($sign, $logRecord) {
    // Adjusting current visit length
    $this->data[$sign]->length = max(
    // Newly calculated visit length
      $logRecord->time - $this->data[$sign]->time + $logRecord->length,
      // Fallback to current visit length logged record start later but is shorter then already calculated visit
      // Should never happen, honestly
      $this->data[$sign]->length
    );

    // Adding hit count
    $this->data[$sign]->hits += $logRecord->hits;

    // Marking current log record for deletion
    $this->mergedIds[$sign][] = $logRecord->counterId;
  }

  /**
   * Called in resetVisit to perform necessary storage operations for current visit and deletion data
   *
   * Doing nothing here - just a callback to easy override in successors
   *
   * @param string $sign
   */
  protected function flushVisit($sign) { }

  /**
   * Closing previous visit and starting new one
   *
   * @param string           $sign
   * @param VisitAccumulator $logRecord
   */
  protected function resetVisit($sign, $logRecord) {
    $this->flushVisit($sign);

    $this->mergedIds[$sign] = [];
    // Current row now is a visit start
    $this->data[$sign] = $logRecord;
  }


  protected function addMoreTime() {
    $this->_extendTime ? set_time_limit($this->_extendTime) : false;
  }

  protected function resetArrays() {
    $this->data = [];
    $this->mergedIds = [];
  }

}
