<?php

/**
 * Class DBMysqliStatementIterator
 */
class DBMysqliStatementIterator extends DbRowIterator {
  /**
   * @var mysqli_stmt $stmt
   */
  protected $stmt;
  /**
   * @var int
   */
  protected $fetchMode;
  /**
   * @var int
   */
  protected $position;
  /**
   * @var array
   */
  protected $currentRow;

  /**
   * Constructor
   *
   * @param mysqli_stmt $Result
   * @param int         $fetchMode constant (MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH)
   */
  public function __construct($Result, $fetchMode = MYSQLI_ASSOC) {
    $this->stmt = $Result;
    $this->fetchMode = $fetchMode;

    $this->position = 0;
    // prefetch the current row
    // note that this advances the Results internal pointer.
    $this->fetchCurrentRow();
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

  protected function fetchCurrentRow() {
    $this->currentRow = $this->fetchArray();
  }

  protected function fetchArray () {
    $data = mysqli_stmt_result_metadata($this->stmt);
    $fields = array();
    $out = array();

    $fields[0] = &$this->stmt;

    while($field = mysqli_fetch_field($data)) {
      $fields[] = &$out[$field->name];
    }

    call_user_func_array('mysqli_stmt_bind_result', $fields);
    mysqli_stmt_fetch($this->stmt);
    return (count($out) == 0) ? false : $out;

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
    return $this->position < $this->stmt->num_rows;
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind() {
    // data_seek moves the Results internal pointer
    $this->stmt->data_seek($this->position = 0);

    // prefetch the current row
    // note that this advances the Results internal pointer.
    $this->fetchCurrentRow();
  }

}
