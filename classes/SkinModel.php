<?php

/**
 * Created by Gorlum 23.02.2017 12:20
 */
class SkinModel {
  /**
   * @var \Common\GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var skin[] $skins
   */
  // TODO - lazy loading
  protected $skins;

  /**
   * @var skin $activeSkin
   */
  protected $activeSkin;


  // TODO - remove
  public function init() {
  }


  public function __construct(\Common\GlobalContainer $gc) {
    $this->gc = $gc;
    $this->skins = array();

    global $user;

    // Берем текущий скин
    $skinName = $this->sanitizeSkinName(!empty($user['dpath']) ? $user['dpath'] : DEFAULT_SKINPATH);
    strpos($skinName, 'skins/') !== false ? $skinName = substr($skinName, 6) : false;
    strpos($skinName, '/') !== false ? $skinName = str_replace('/', '', $skinName) : false;

    // Загружены ли уже данные по текущему скину?
    if(empty($this->skins[$skinName])) {
      // Прогружаем текущий скин
      $this->activeSkin = $this->skins[$skinName] = new skin($skinName);
    }
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
