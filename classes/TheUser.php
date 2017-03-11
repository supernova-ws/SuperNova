<?php

/**
 * Created by Gorlum 27.02.2017 16:22
 */

/**
 * Class User
 *
 * Dummy object for player class
 *
 */
class TheUser {

  /**
   * @var \Common\GlobalContainer $gc
   */
  protected $gc;

  /**
   * TheUser constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->gc = $gc;
  }

  /**
   * @return string
   */
  public function getSkinName() {
    global $user;

    $skinName = !empty($user['skin']) ? $user['skin'] : DEFAULT_SKIN_NAME;

    return $skinName;
  }

  /**
   * @return string
   */
  public function getSkinPath() {
    return 'skins/' . $this->getSkinName() . '/';
  }

  /**
   * Set skin name
   *
   * @param string $skinName
   */
  public function setSkinName($skinName) {
    global $user;

    $user['skin'] = $skinName;
  }
}
