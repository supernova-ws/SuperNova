<?php
/**
 * Created by Gorlum 04.03.2017 16:36
 */

namespace Pages;


interface IPage {

  /**
   * IPage constructor.
   */
  public function __construct();

  /**
   * Loading page data from page params
   */
  public function loadParams();

  /**
   * @param string $action
   *
   * @return bool
   */
  public function checkAction($action);

}
