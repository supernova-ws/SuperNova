<?php
/**
 * Created by Gorlum 17.10.2017 10:48
 */

namespace DBAL;

use \Iterator;
use \mysqli_result;

class DbMysqliResultIterator implements Iterator {
  /**
   * @var mysqli_result|false $mysqli_result
   */
  private $mysqli_result;

  /**
   * @var int|float|null
   */
  private $counter = null;
  /**
   * @var array|null $currentRow
   */
  private $currentRow = null;


  /**
   * DbMysqliResultIterator constructor.
   *
   * @param mysqli_result|bool $mysqli_result
   */
  public function __construct($mysqli_result) {
    $this->mysqli_result = $mysqli_result;
    $this->rewind();
  }

  public function rewind() {
    if ($this->mysqli_result instanceof mysqli_result) {
      $this->mysqli_result->data_seek(0);
      $this->next();
    }
    $this->counter = $this->valid() ? 0 : null;
  }

  public function valid() {
    return $this->currentRow !== null;
  }

  public function next() {
    $this->currentRow = $this->mysqli_result instanceof mysqli_result ? $this->mysqli_result->fetch_assoc() : null;
    $this->counter++;
  }

  public function key() {
    return $this->counter;
  }

  public function current() {
    return $this->currentRow;
  }

  public function count() {
    return $this->mysqli_result instanceof mysqli_result ? $this->mysqli_result->num_rows : 0;
  }

}
