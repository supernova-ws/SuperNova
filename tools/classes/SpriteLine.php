<?php

/** Created by Gorlum 08.01.2024 20:04 */

/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace Tools;

use ImageFile;

class SpriteLine {
  /** @var ImageFile[] $files */
  public $files = [];

  public $height = 0;
  public $width = 0;

  /** @var ImageContainer|null $image */
  public $image = null;
  /** @var string */
  public $css = '';

  /**
   * @param ImageFile $image
   * @param int       $gridSize
   *
   * @return static|null
   */
  public function fillLine($image, $gridSize) {
    if($image->isAnimatedGif()) {
      $line = new SpriteLineGif();
    } else {
      $line = $this->isFull($gridSize) ? new static() : $this;
    }

    $line->addImage($image);

    return $line;
  }

  /**
   * @param ImageFile $imageFile
   *
   * @return void
   */
  protected function addImage($imageFile) {
    $this->files[] = $imageFile;

    $this->height = max($this->height, $imageFile->height);
    $this->width  += $imageFile->width;
  }

  public function getImageCount() {
    return count($this->files);
  }

  public function generate($posY, $scaleToPx) {
    unset($this->image);

    $this->image = ImageContainer::create($this->width, $this->height);

    $position = 0;
    foreach ($this->files as $file) {
      $this->image->copyFrom($file->getImageContainer(), $position, 0);

      $onlyName = explode('.', $file->fileName);
      if (count($onlyName) > 1) {
        array_pop($onlyName);
      }
      $onlyName = implode('.', $onlyName);

      $css = "%1\$s{$onlyName}%2\$s{background-position: -{$position}px -{$posY}px;";
      if ($scaleToPx > 0) {
        $maxSize = max($file->width, $file->height);
        if ($maxSize != $scaleToPx) {
          $css .= "zoom: calc({$scaleToPx}/{$maxSize});";
        }
      }
      $css .= "width: {$file->width}px;height: {$file->height}px;}\n";

      $this->css .= $css;

      $position += $file->width;
    }
  }

  protected function isFull($gridSize) {
    return $this->getImageCount() >= $gridSize;
  }

}