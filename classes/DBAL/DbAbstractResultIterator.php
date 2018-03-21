<?php
/**
 * Created by Gorlum 25.11.2017 20:02
 */

namespace DBAL;

use \mysqli_result;
use Common\Interfaces\ICountableIterator;

/**
 * Class DbAbstractResultIterator
 *
 * Base class which makes Iterator from internal $mysqli_result
 * Other classes which inherits from this will implement their own methods to obtain mysqli_result (or fill it internally)
 *
 * @package DBAL
 */
abstract class DbAbstractResultIterator implements ICountableIterator {
  /**
   * @var mysqli_result|false $mysqli_result
   */
  protected $mysqli_result;

  /**
   * @var int|float|null
   */
  protected $counter = null;
  /**
   * @var array|null $currentRow
   */
  protected $currentRow = null;

  /**
   * Seeks mysqli_result to first record
   */
  protected function seekToFirst() {
    if ($this->mysqli_result instanceof mysqli_result) {
      $this->mysqli_result->data_seek(0);
    }
  }

  /**
   * @inheritdoc
   */
  public function rewind() {
    if ($this->mysqli_result instanceof mysqli_result) {
      $this->seekToFirst();
      $this->next();
    }
    $this->counter = $this->valid() ? 0 : null;
  }

  /**
   * @inheritdoc
   */
  public function valid() {
    return $this->mysqli_result instanceof mysqli_result && $this->currentRow !== null;
  }

  /**
   * @inheritdoc
   */
  public function next() {
    if ($this->mysqli_result instanceof mysqli_result) {
      $this->currentRow = $this->mysqli_result->fetch_assoc();
      $this->counter++;
    }
  }

  /**
   * @inheritdoc
   */
  public function key() {
    return $this->mysqli_result instanceof mysqli_result ? $this->counter : null;
  }

  /**
   * @inheritdoc
   */
  public function current() {
    return $this->currentRow;
  }

  /**
   * @inheritdoc
   */
  public function count() {
    return $this->mysqli_result instanceof mysqli_result ? $this->mysqli_result->num_rows : 0;
  }

}
