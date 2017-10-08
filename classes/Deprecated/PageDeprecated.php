<?php
/**
 * Created by Gorlum 08.10.2017 17:36
 */

namespace Deprecated;

use \template;

/**
 * Class PageDeprecated
 *
 * Simple class to streamline somehow refactored page classes
 *
 * @package Deprecated
 */
class PageDeprecated {

  public function __construct() {
    $this->resetResultMessages();
  }

  protected function loadParams() {

  }

  /**
   * Router/Controller for page
   */
  public function route() {

  }


  // TODO - move to separate class
  /**
   * @var array[] $resultMessages
   */
  protected $resultMessages = [];

  /**
   * @param array $result
   */
  protected function addResultMessage($result) {
    $this->resultMessages[] = $result;
  }

  /**
   * @return int
   */
  protected function countResultMessages() {
    return count($this->resultMessages);
  }

  /**
   * @param template $template
   */
  protected function templatizeResultMessages(template $template) {
    foreach ($this->resultMessages as $error_message) {
      $template->assign_block_vars('result', $error_message);
    }
  }

  protected function resetResultMessages() {
    $this->resultMessages = [];
  }
  // TODO - move to separate class

}
