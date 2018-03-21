<?php
/**
 * Created by Gorlum 08.10.2017 17:36
 */

namespace Pages\Deprecated;

use \template;
use \ResultMessages;

/**
 * Class PageDeprecated
 *
 * Simple class to streamline somehow refactored page classes
 *
 * @package Deprecated
 */
class PageDeprecated {
  /**
   * @var ResultMessages $resultMessageList
   */
  protected $resultMessageList;


  public function __construct() {
    $this->resultMessageList = new ResultMessages();
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
   * @param string $message
   * @param int    $status
   */
  protected function resultAdd($message, $status = ERR_NONE) {
    $this->resultMessageList->add($message, $status);
  }

  /**
   * @return int
   */
  protected function resultCount() {
    return count($this->resultMessageList);
  }

  /**
   * @param template $template
   */
  protected function resultTemplatize(template $template) {
    $this->resultMessageList->templateAdd($template);
  }

  protected function resultReset() {
    $this->resultMessageList->reset();
  }
  // TODO - move to separate class

}
