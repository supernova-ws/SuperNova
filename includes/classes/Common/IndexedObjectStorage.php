<?php
/**
 * Created by Gorlum 17.08.2016 23:08
 */

namespace Common;

use \SplObjectStorage;

/**
 * Class IndexedObjectStorage
 * @package Common
 */
class IndexedObjectStorage extends \SplObjectStorage {

  protected $index = array();

  /**
   * Unassign index from object
   *
   * @param object $object
   */
  protected function indexUnset($object) {
    if (($oldData = SplObjectStorage::offsetGet($object)) !== null) {
      unset($this->index[$oldData]);
    }
  }

  /**
   * Function to handle assigning keys
   *
   * Primary for dealing with duplicates - child can overwrite function to handle duplicates by itself
   *
   * @param object     $object
   * @param mixed|null $data - null means remove index
   *
   * @throws \Exception
   */
  protected function indexSet($object, $data = null) {
    // When calling indexSet - $object already SHOULD not have index associated in this concrete implementation

    // Checking if index free. NULL index is always free
    if (isset($this->index[$data])) {
      throw new \Exception('Duplicate index [' . $data . '] in ' . __CLASS__);
    }

    if ($data !== null) {
      // Assigning index
      $this->index[$data] = $object;
    }
  }

  /**
   * @param mixed $index
   *
   * @return mixed|null
   */
  public function indexGetObject($index) {
    return array_key_exists($index, $this->index) ? $this->index[$index] : null;
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

  public function attach($object, $data = null) {
    // If $object in storage - removing it's index from $indexes
    if ($this->contains($object)) {
      $this->indexUnset($object);
    }
    $this->indexSet($object, $data);
    parent::attach($object, $data);
  }

  public function offsetSet($object, $data = null) {
    $this->attach($object, $data);
  }


  public function detach($object) {
    $this->indexUnset($object);
    parent::detach($object);
  }

  public function offsetUnset($object) {
    $this->detach($object);
    parent::offsetUnset($object);
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

  public function setInfo($data) {
    if ($this->valid()) {
      $this->indexUnset($this->current());
      $this->indexSet($this->current(), $data);
    }
    // Changing data in storage
    parent::setInfo($data);
  }


}
