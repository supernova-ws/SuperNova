<?php
/** Created by Gorlum 08.01.2024 19:57 */

namespace Tools;

require_once __DIR__ . '/SpriteLine.php';
require_once __DIR__ . '/ImageFile.php';
require_once __DIR__ . '/ImageContainer.php';

use ImageFile;

class Sprite {
  /** @var SpriteLine[] $lines */
  public $lines = [];

  protected $gridSize = 0;

  protected $lineIndex = 0;
  protected $columnIndex = 0;

  protected $height = 0;
  protected $width = 0;

  /** @var ImageContainer|null $image */
  public $image = null;

  public function __construct($gridSize = 0) {
    if ($gridSize < 1) {
      $gridSize = 1;
    }

    $this->gridSize = $gridSize;
  }

  public static function createGridSquare(array $images) {
    $gridSize = ceil(sqrt(count($images)));
    usort($images, function (ImageFile $a, ImageFile $b) { return $b->height - $a->height; });

    $sprite = new static($gridSize);
    foreach ($images as $image) {
      $sprite->addToGrid($image);
    }

    return $sprite;
  }


  /**
   * @param ImageFile $imageFIle
   *
   * @return void
   */
  public function addToGrid($imageFIle) {
    // This is first image in row
    if (empty($this->lines[$this->lineIndex])) {
      $this->lines[$this->lineIndex] = new SpriteLine();
    }
    $this->lines[$this->lineIndex]->addImage($imageFIle);
    if ($this->lines[$this->lineIndex]->getImageCount() >= $this->gridSize) {
      $this->lineIndex++;
    }
  }

  /**
   * @param string $dirOut
   *
   * @return void
   */
  public function generate($dirOut) {
    $this->width = $this->height = 0;

    foreach ($this->lines as $line) {
      $line->generate();

      $this->height += $line->height;
      $this->width = max($this->width, $line->width);

      // TODO debug
      // $line->image2->savePng($dirOut . count($breakpoints) . '.png');
    }

    $this->imageReset();

    $position = 0;
    foreach ($this->lines as $line) {
      $this->image->copyFrom($line->image, 0, $position);

      $position += $line->height;
    }

    $this->image->savePng($dirOut . 'output.png');


    /*
  .bg-menu_affiliates {
      width: 12px; height: 12px;
      background: url('css_sprites.png') -58px -42px;
  }
     */

  }

  /**
   * @return void
   */
  public function imageReset() {
    if (!empty($this->image)) {
      unset($this->image);
    }

    $this->image = ImageContainer::create($this->width, $this->height);
  }

}
