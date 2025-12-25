<?php

/** Created by Gorlum 08.01.2024 19:14 */

use GIFEndec\Decoder;
use GIFEndec\Events\FrameDecodedEvent;
use GIFEndec\IO\FileStream;
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
  /** @var string|false $content Image file content */
  protected $content;

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

  public function isAnimatedGif() {
    // Checking file extension
    if (!strtolower(pathinfo($this->fullPath, PATHINFO_EXTENSION)) === 'gif') {
      return false;
    }

    // Counting frame(s)
    if (substr($content = $this->loadContent(), 0, 4) !== 'GIF8') {
      return false;
    }
    //an animated gif contains multiple "frames", with each frame having a
    //header made up of:
    // * a static 4-byte sequence (\x00\x21\xF9\x04)
    // * 4 variable bytes
    // * a static 2-byte sequence (\x00\x2C)
    if (preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $content) <= 1) {
      return false;
    }

    return true;
  }

  /**
   * @return false|string
   */
  protected function loadContent() {
    if (empty($this->content)) {
      $this->content = file_get_contents($this->fullPath);
    }

    return $this->content;
  }


}
