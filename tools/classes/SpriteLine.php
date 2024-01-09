<?php
/** Created by Gorlum 08.01.2024 20:04 */

namespace Tools;


use ImageFile;

class SpriteLine {
  /** @var ImageFile[] $files */
  public $files = [];

  public $widthList = [];

  public $height = 0;
  public $width = 0;

  /** @var resource|null $image */
  public $image = null;
  /** @var ImageContainer|null $image2 */
  public $image2 = null;

  /**
   * @param ImageFile $imageFile
   *
   * @return void
   */
  public function addImage($imageFile) {
    $this->files[] = $imageFile;

    $this->height = max($this->height, $imageFile->height);
    $this->width  += $imageFile->width;
  }

  public function getImageCount() {
    return count($this->files);
  }

  public function generate() {
    unset($this->widthList);

    unset($this->image2);
    $this->image2 = ImageContainer::create($this->width, $this->height);

    $position = 0;
    foreach ($this->files as $file) {
      $this->image2->copyFrom($file->getImageContainer(), $position, 0);

      $this->widthList[] = $file->width;

      $position += $file->width;
    }
  }

//  /**
//   * @return void
//   */
//  public function imageReset() {
//    if (!empty($this->image)) {
//      imagedestroy($this->image);
//    }
//
//    $this->image = imagecreatetruecolor($this->width, $this->height);
//    imagealphablending($this->image, true);
//    imagesavealpha($this->image, true);
//    $color = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
//    imagefill($this->image, 0, 0, $color);
//  }

}
