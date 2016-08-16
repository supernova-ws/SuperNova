<?php

namespace V2Unit;


/**
 * Class V2UnitIterator
 *
 * @method V2UnitIterator getInnerIterator()
 *
 * @package V2Unit
 */
class V2UnitIterator extends \IteratorIterator {

  protected $type = 0;

  public function setFilterType($type) {
    $this->type = $type;
  }

  protected function filterCurrent() {
    return
      $this->valid()
      &&
      $this->filterType();
  }

  protected function filterType() {
    $inner = $this->getInnerIterator();
    return
      $this->type
      &&
      (
        !$inner->current()->type
        ||
        $inner->current()->type != $this->type
      );
  }

  public function next() {
    do {
      $this->getInnerIterator()->next();
    } while ($this->filterCurrent());
  }

  public function rewind() {
//    parent::rewind(); // TODO: Not working
    $this->getInnerIterator()->rewind();

    // If first element not of supplied type - finding first available element
    if ($this->filterCurrent()) {
      $this->next();
    }
  }

  // TODO - why it's not forwarded ??????????s
  public function valid() {
    return $this->getInnerIterator()->valid();
  }

  /**
   * @return V2UnitContainer
   */
  public function current() {
    return $this->getInnerIterator()->current();
  }

  public function key() {
    return $this->getInnerIterator()->key();
  }

}
