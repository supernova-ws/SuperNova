<?php

/**
 * Created by Gorlum 23.02.2017 12:20
 */
class SkinModel {
  const NO_IMAGE_ID = '_no_image';
  const NO_IMAGE_PATH = '/design/images/_no_image.png';

  /**
   * @var \Common\GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var SkinInterface[] $skins
   */
  // TODO - lazy loading
  protected $skins;

  /**
   * @var SkinInterface $activeSkin
   */
  protected $activeSkin;


  // TODO - remove
  public function init() {
  }


  /**
   * SkinModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct(\Common\GlobalContainer $gc) {
    $this->gc = $gc;
    $this->skins = array();

    global $user;

    // Берем текущий скин
    $this->activeSkin = $this->getSkin(!empty($user['dpath']) ? $user['dpath'] : DEFAULT_SKINPATH);
  }

  /**
   * Returns skin with skin name. Loads it - if it is required
   *
   * @param string $skinName
   *
   * @return SkinInterface
   */
  public function getSkin($skinName) {
    $skinName = $this->sanitizeSkinName($skinName);

    if (empty($this->skins[$skinName])) {
      // Прогружаем текущий скин
      $this->skins[$skinName] = $this->loadSkin($skinName);
    }

    return $this->skins[$skinName];
  }

  public function getImageCurrent($image_tag, $template) {
    return $this->activeSkin->imageFromStringTag($image_tag, $template);
  }

  public function getImageFrom($skinName, $image_tag, $template) {
    return $this->getSkin($skinName)->imageFromStringTag($image_tag, $template);
  }

  /**
   * Switches active skin
   *
   * @param $skinName
   */
  public function switchActive($skinName) {
    $this->activeSkin = $this->getSkin($skinName);
  }

  /**
   * Loads skin
   *
   * @param string $skinName
   *
   * @return SkinInterface
   */
  protected function loadSkin($skinName) {
    $skinClass = $this->gc->skinEntityClass;

    $skin = new $skinClass($skinName, $this);
//    $skin->load();

    return $skin;
  }


  /**
   * @param string $skinName
   *
   * @return string
   */
  protected function sanitizeSkinName($skinName) {
    strpos($skinName, 'skins/') !== false ? $skinName = substr($skinName, 6) : false;
    strpos($skinName, '/') !== false ? $skinName = str_replace('/', '', $skinName) : false;

    return is_string($skinName) ? $skinName : '';
  }

}
