<?php

/**
 * Created by Gorlum 07.02.2017 10:20
 */

/**
 * Class Text
 *
 * Represents text in DB
 */
class Text {

  /**
   * @var int
   */
  public $id = 0;
  /**
   * @var int
   */
  public $parent = 0;
  /**
   * @var int
   */
  public $next = 0;
  /**
   * @var string
   */
  public $title = '';
  /**
   * @var string
   */
  public $content = '';

  /**
   * Text constructor.
   */
  public function __construct() {

  }

}
