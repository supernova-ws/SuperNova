<?php

/**
 * Class UBERoundList
 *
 * @method UBERound offsetGet($offset)
 * @property UBERound[] $_container
 */
class UBERoundList extends ArrayAccessV2 {

  /**
   * @return UBERound
   */
  public function get_last_element() {
    return end($this->_container);
  }

}