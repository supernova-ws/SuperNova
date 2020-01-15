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
   * @var \Core\GlobalContainer $gc
   */
  protected $gc;

  /**
   * TheUser constructor.
   *
   * @param \Core\GlobalContainer $gc
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

  /**
   * Get skin name
   *
   * @return string
   */
  public function getTemplateName() {
    global $user;

    return !empty($user['template']) ? $user['template'] : SnTemplate::getServerDefaultTemplateName();
  }

  /**
   * @return bool|null null - not set, true|false - WebP supported or not
   */
  public function isWebpSupported() {
    return !isset($_COOKIE[SN_COOKIE_WEBP]) ? null : !empty($_COOKIE[SN_COOKIE_WEBP]);
  }

  public function setWebpSupport($isSupported) {
    sn_setcookie(SN_COOKIE_WEBP, empty($isSupported) ? 0 : 1);
  }

}
