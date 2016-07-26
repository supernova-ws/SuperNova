<?php

/**
 * Class DbMysqliResultIterator
 *
 * @property mysqli_result $_result
 * @method array count()
 */
class DbMysqliResultIterator extends DbResultIterator {
  protected function fetchCurrentRow() {
    $this->currentRow = $this->_result->fetch_assoc();
    // TODO: Implement fetchCurrentRow() method.
  }

  /**
   * Constructor
   *
   * @param mysqli_result $result
   * @param int           $fetchMode constant (MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH)
   */
  public function __construct($result, $fetchMode = MYSQLI_ASSOC) {
    parent::__construct($result, $fetchMode);
  }

}
