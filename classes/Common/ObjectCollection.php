<?php

/**
 * Created by Gorlum 21.08.2016 3:00
 */

namespace Common;


/**
 * Class ObjectCollection
 *
 * Remaps ArrayAccess interface to work with indexed objects only
 * Counts only indexed elements
 *
 * @package Common
 */

class ObjectCollection extends IndexedObjectStorage {

  /**
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   *
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   *
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset) {
    return $this->indexIsSet($offset);
  }

  /**
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   *
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   *
   * @return mixed Can return all value types.
   * @since 5.0.0
   */
  public function offsetGet($offset) {
    return $this->indexGetObject($offset);
  }

  /**
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   *
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetSet($offset, $value) {
    $this->attach($value, $offset);
  }

  /**
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   *
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset) {
    if($this->offsetExists($offset)) {
      parent::offsetUnset($this->offsetGet($offset));
    }
  }

  /**
   * Counts ONLY indexed elements
   *
   * @return int
   */
  public function count() {
    return count($this->index);
  }

}
