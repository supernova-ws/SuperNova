<?php

/** Created by Gorlum 08.01.2024 19:14 */

use Tools\ImageContainer;

/**
 * @property int $height
 * @property int $width
 */
class ImageFile {
  public $dir = '';
  public $fileName = '';
  public $fullPath = '';

  private $image = null;

  /**
   * @param $fileName
   * @param $dir
   *
   * @return static|null
   */
  public static function read($fileName, $dir = '') {
    $that = new static($fileName, $dir);

    if ($that->getImageContainer() === null) {
      unset($that);
      $that = null;
    }

    return $that;
  }

  /**
   * @param string $fileName Name of image file. Can be full path to file or just filename. In latter case $dir will be used
   * @param string $dir      If present and filename is just name of file will we added to make full path. If empty - tools root folder will be assumed
   */
  public function __construct($fileName, $dir = '') {
    if (dirname($fileName) !== '.') {
      $this->dir      = realpath(dirname($fileName));
      $this->fileName = basename($fileName);
    } else {
      $this->dir      = realpath($dir ?: __DIR__ . '/../');
      $this->fileName = $fileName;
    }
    $this->dir = str_replace('\\', '/', $this->dir) . '/';

    $this->fullPath = $this->dir . $this->fileName;
  }

  public function __get($property) {
    if (in_array($property, ['height', 'width',])) {
      return $this->getImageContainer() ? $this->getImageContainer()->$property : 0;
    }

    return property_exists($this, $property) ? $this->$property : null;
  }

//  public function __set($property, $value) {
//    if (property_exists($this, $property)) {
//      $this->$property = $value;
//    }
//
//    return $this;
//  }

  /**
   * @return ImageContainer
   */
  public function getImageContainer() {
    if (empty($this->image)) {
      $this->image = ImageContainer::load($this->fullPath);
    }

    return $this->image;
  }

//  public function load() {
//    $this->image = ImageContainer::load($this->fullPath);
//  }

}
