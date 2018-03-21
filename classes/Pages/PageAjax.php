<?php
/**
 * Created by Gorlum 04.03.2017 16:38
 */

namespace Pages;

use Player\userOptions;

class PageAjax implements IPage {
  /**
   * List of allowed actions on page to prevent unauthorized user's access to methods
   *
   * @var array $allowedActions
   */
  protected $allowedActions = array();

  /**
   * @var \Player\userOptions $userOptions
   */
  protected $userOptions;


  /**
   * @inheritdoc
   */
  public function __construct() {

  }

  /**
   * @inheritdoc
   */
  public function loadParams() {
  }

  /**
   * @inheritdoc
   */
  public function checkAction($action) {
    return !empty($this->allowedActions[$action]);
  }

}
