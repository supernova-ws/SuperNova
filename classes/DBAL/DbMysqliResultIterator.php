<?php
/**
 * Created by Gorlum 17.10.2017 10:48
 */

namespace DBAL;

use \mysqli_result;

/**
 * Class DbMysqliResultIterator
 *
 * Simplest implementation of DbAbstractResultIterator - getting result from constructor
 *
 * @package DBAL
 */
class DbMysqliResultIterator extends DbAbstractResultIterator {
  /**
   * DbMysqliResultIterator constructor.
   *
   * @param mysqli_result|bool $mysqli_result
   */
  public function __construct($mysqli_result) {
    $this->mysqli_result = $mysqli_result;

    $this->rewind();
  }

}
