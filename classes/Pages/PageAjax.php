<?php
/**
 * Created by Gorlum 04.03.2017 16:38
 */

namespace Pages;

use Player\userOptions;

/**
 * Class PageAjax
 *
 *
 *
 * @package Pages
 */
class PageAjax implements IPage {
  /**
   * List of allowed actions on page to prevent unauthorized user's access to methods
   * Also index of array is the name of the class method called
   *
   * @var array[] $allowedActions [(string)action] => {true|any data}
   */
  protected $allowedActions = [];

  /**
   * @var userOptions $userOptions
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
