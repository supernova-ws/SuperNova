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

  public function __construct($dir, $fileName, $fullFileName = '') {
    if ($fullFileName) {
      $this->dir      = realpath(dirname($fullFileName));
      $this->fileName = basename($fullFileName);
    } else {
      $this->dir      = realpath($dir);
      $this->fileName = $fileName;
    }
    $this->dir      = str_replace('\\', '/', $this->dir) . '/';

    $this->fullPath = $this->dir . $this->fileName;
  }

  public function __get($property) {
    if (in_array($property, ['height', 'width',])) {
      return $this->getImageContainer()->$property;
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

  public function load() {
    $this->image = ImageContainer::load($this->fullPath);
  }

}
