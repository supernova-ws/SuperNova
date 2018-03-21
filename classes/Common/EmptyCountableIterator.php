<?php
/**
 * Created by Gorlum 19.10.2017 15:07
 */

namespace Common;

use Common\Interfaces\ICountableIterator;

class EmptyCountableIterator extends \EmptyIterator implements ICountableIterator {

  /**
   * Count elements of an object
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   * @since 5.1.0
   */
  public function count() {
    return 0;
  }

}
