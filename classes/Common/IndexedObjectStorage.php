<?php
/**
 * Created by Gorlum 17.08.2016 23:08
 */

namespace Common;

use \SplObjectStorage;

/**
 * Class IndexedObjectStorage
 *
 * Maintains per-method compatibility with SplObjectStorage
 * Introduces new methods:
 *    - check index existence
 *    - Get object by index
 *
 * @package Common
 */
class IndexedObjectStorage extends \SplObjectStorage {

  protected $index = array();

  /**
   * @param mixed $index
   *
   * @return bool
   */
  public function indexIsSet($index) {
    return array_key_exists($index, $this->index);
  }

  /**
   * @param mixed $index
   *
   * @return mixed|null
   */
  public function indexGetObject($index) {
    return $this->indexIsSet($index) ? $this->index[$index] : null;
  }

  public function offsetSet($object, $data = null) {
    $this->attach($object, $data);
  }

  public function offsetUnset($object) {
    $this->detach($object);
    parent::offsetUnset($object);
  }


  /**
   * Unassign index from object
   *
   * @param object $object
   */
  protected function indexUnset($object) {
    if (($oldData = parent::offsetGet($object)) !== null) {
      unset($this->index[$oldData]);
    }
  }

  /**
   * Function called when index already exists
   *
   * Can be used by child object
   *
   * @param $object
   * @param $data
   *
   * @throws \Exception
   */
  protected function indexDuplicated($object, $data) {
    throw new \Exception('Duplicate index [' . $data . '] in ' . __CLASS__);
  }

  /**
   * Function called when index is empty
   *
   * To use with child object
   *
   * @param $object
   *
   * @return bool
   */
  protected function indexEmpty($object) {
    // Do something if index is empty
    return true;
  }

  /**
   * Function to handle assigning keys
   *
   * Primary for dealing with duplicates - child can overwrite function to handle duplicates by itself
   *
   * @param object     $object
   * @param mixed|null $data - null means remove index
   *
   * @return bool
   */
  // Set indexes for existing objects
  protected function indexSet($object, $data = null) {
    // When calling indexSet - $object already SHOULD not have index associated in this concrete implementation

    // Checking if index free. NULL index is always free
    if (isset($this->index[$data])) {
      return $this->indexDuplicated($object, $data);
    } elseif ($data === null) {
      return $this->indexEmpty($object);
    } else {
      // Assigning index
      $this->index[$data] = $object;
      return true;
    }
  }

  public function attach($object, $data = null) {
    // If object already in storage - removing it's index from $indexes
    if ($this->contains($object)) {
      $this->indexUnset($object);
    }
    if($this->indexSet($object, $data)) {
      // Attaches object only if index sets successfully
      parent::attach($object, $data);
    }
  }

  public function detach($object) {
    $this->indexUnset($object);
    parent::detach($object);
  }

  public function setInfo($data) {
    if ($this->valid()) {
      // Removing index from current object - if any
      $this->indexUnset($this->current());
      // Setting new index for current object
      $this->indexSet($this->current(), $data);
      // Changing object data in storage - giving to
      parent::setInfo($data);
    }
  }


  /**
   * Rebuild index
   */
  protected function indexRebuild() {
    $this->index = array();
    $this->rewind();
    while ($this->valid()) {
      $this->indexSet($this->current(), $this->getInfo());
      $this->next();
    }
  }

  public function addAll($storage) {
    // Adding new elements from storage
    parent::addAll($storage);
    // Original addAll overwrites indexes with those in input $storage - so we must refresh current indexes
    $this->indexRebuild();
  }

  public function removeAll($storage) {
    parent::removeAll($storage);
    $this->indexRebuild();
  }

  public function removeAllExcept($storage) {
    parent::removeAllExcept($storage);
    $this->indexRebuild();
  }

  public function unserialize($serialized) {
    // Unserialize DOES NOT removes existing objects AND creates object copies making duplicates
    // So be careful
    parent::unserialize($serialized);
    $this->indexRebuild();
  }

}
