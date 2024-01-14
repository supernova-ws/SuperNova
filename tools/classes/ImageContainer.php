<?php
/** Created by Gorlum 09.01.2024 15:59 */

namespace Tools;

/**
 * @property int $height
 * @property int $width
 */
class ImageContainer {
  private $height = -1;
  private $width = -1;

  /** @var resource|null $image */
  public $image = null;

  /**
   * @param string $file
   *
   * @return static|null
   */
  public static function load($file) {
    $image = @imagecreatefromstring(file_get_contents($file));
    if (!$image) {
      return null;
    }

    $that = new static();

    $that->image = $image;
    imagesavealpha($that->image, true);

    $that->width  = imagesx($that->image);
    $that->height = imagesy($that->image);

    return $that;
  }

  /**
   * @param int $width
   * @param int $height
   *
   * @return static
   */
  public static function create($width, $height) {
    $that = new static();

    $that->width  = $width;
    $that->height = $height;

    $that->imageReset();

    return $that;
  }

  public function __get($property) {
    if (in_array($property, ['height', 'width',]) && ($this->$property === -1)) {
      if (isset($this->image)) {
        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
      } else {
        $this->width = $this->height = 0;
      }
    }

    return property_exists($this, $property) ? $this->$property : null;
  }

  public function __destruct() {
    if (!empty($this->image)) {
      imagedestroy($this->image);
    }
  }

  /**
   * @param ImageContainer $anImage
   * @param int            $positionX
   * @param int            $positionY
   * @param int            $sourceX
   * @param int            $sourceY
   *
   * @return bool
   */
  public function copyFrom(ImageContainer $anImage, $positionX, $positionY, $sourceX = 0, $sourceY = 0) {
    return imagecopy($this->image, $anImage->image, $positionX, $positionY, $sourceX, $sourceY, $anImage->width, $anImage->height);
  }

  /**
   * @param string $string
   *
   * @return bool
   */
  public function savePng($string) {
    return imagepng($this->image, $string, 9);
  }

  /**
   * @return void
   */
  protected function imageReset() {
    if (!empty($this->image)) {
      imagedestroy($this->image);
    }

    $this->image = imagecreatetruecolor($this->width, $this->height);
    imagealphablending($this->image, true);
    imagesavealpha($this->image, true);
    $color = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
    imagefill($this->image, 0, 0, $color);
  }

}
