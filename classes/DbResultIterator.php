<?php

/**
 * Class DbResultIterator
 *
 * @property mixed $_result
 */
abstract class DbResultIterator implements Iterator, Countable {
  /**
   * @var mixed $_result
   */
  protected $_result;
  /**
   * @var int
   */
  protected $fetchMode;
  /**
   * @var int
   */
  protected $position;
  /**
   * @var array|null
   */
  protected $currentRow;

  abstract protected function fetchCurrentRow();

  /**
   * Constructor
   *
   * @param mixed $result
   * @param int   $fetchMode constant (MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH)
   */
  public function __construct($result, $fetchMode = MYSQLI_ASSOC) {
    $this->_result = $result;
    $this->fetchMode = $fetchMode;

    $this->position = 0;
    // prefetch the current row
    // note that this advances the Results internal pointer.
    $this->fetchCurrentRow();
  }

  /**
   * Gets first column value (if any) of current row
   *
   * @return mixed
   */
  public function getFirstColumn() {
    $row = $this->valid() ? $this->current() : null;

    return is_array($row) ? array_pop($row) : $row;
  }

  /**
   * Destructor
   * Frees the Result object
   */
  public function __destruct() {
    //TODO
//    $this->Result->free();
  }

  /**
   * Return the current element
   * Returns the row that matches the current position
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current() {
    return $this->currentRow;
  }

  /**
   * Move forward to next element
   * Moves the internal pointer one step forward
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next() {
    // prefetch the current row
    $this->fetchCurrentRow();

    // and increment internal pointer
    ++$this->position;
  }

  /**
   * Return the key of the current element - the current position
   * @link http://php.net/manual/en/iterator.key.php
   * return mixed scalar on success, or null on failure.
   * @return int
   * @since 5.0.0
   */
  public function key() {
    return $this->position;
  }

  /**
   * Checks if current position is valid
   * Returns true if the current position is valid, false otherwise.
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
   */
  public function valid() {
    return $this->position < $this->_result->num_rows;
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind() {
    // data_seek moves the Results internal pointer
    $this->_result->data_seek($this->position = 0);

    // prefetch the current row
    // note that this advances the Results internal pointer.
    $this->fetchCurrentRow();
  }


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
    throw new Exception('You should implement ' . get_called_class() . '::count()');
  }

}
