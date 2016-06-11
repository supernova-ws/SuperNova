<?php

/**
 * Class DBMysqliStatementIterator
 *
 * @property mysqli_stmt $_result
 */
class DBMysqliStatementIterator extends DbResultIterator {

  protected function fetchArray() {
    $data = mysqli_stmt_result_metadata($this->_result);
    $fields = array();
    $out = array();

    $fields[0] = &$this->_result;

    while($field = mysqli_fetch_field($data)) {
      $fields[] = &$out[$field->name];
    }

    call_user_func_array('mysqli_stmt_bind_result', $fields);
    mysqli_stmt_fetch($this->_result);

    return (count($out) == 0) ? false : $out;

  }

  protected function fetchCurrentRow() {
    $this->currentRow = $this->fetchArray();
  }

  /**
   * Constructor
   *
   * @param mysqli_stmt $result
   * @param int         $fetchMode constant (MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH)
   */
  public function __construct($result, $fetchMode = MYSQLI_ASSOC) {
    parent::__construct($result, $fetchMode);
  }

}
