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

    $skinName = !empty($user['dpath']) ? $user['dpath'] : DEFAULT_SKINPATH;
    strpos($skinName, 'skins/') !== false ? $skinName = substr($skinName, 6) : false;
    $skinName = str_replace('/', '', $skinName);

    return $skinName;
  }

  /**
   * @return string
   */
  public function getSkinPath() {
    return 'skins/' . $this->getSkinName() . '/';
  }

  /**
   * @param string $skinPath
   */
  public function setSkinPath($skinPath) {
    global $user;

    $user['dpath'] = $skinPath;
  }

}
