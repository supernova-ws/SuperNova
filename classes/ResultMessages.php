<?php
/**
 * Created by Gorlum 25.01.2018 8:36
 */

class ResultMessages implements Countable {
  // TODO - move to separate class
  /**
   * @var array[] $resultMessages
   */
  protected $resultMessages = [];

  public function __construct() {
    $this->reset();
  }

  /**
   * @param array $result
   */
  public function add($message, $status = ERR_NONE) {
    $this->resultMessages[] = ['MESSAGE' => $message, 'STATUS' => $status];
  }

  /**
   * @return int
   */
  public function count() {
    return count($this->resultMessages);
  }

  /**
   * @param template $template
   */
  public function templateAdd(template $template) {
    foreach ($this->resultMessages as $error_message) {
      $template->assign_block_vars('result', $error_message);
    }
  }

  public function reset() {
    $this->resultMessages = [];
  }

}
