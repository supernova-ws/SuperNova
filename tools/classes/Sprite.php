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
    // TODO - reset image sprite

    $this->width = $this->height = 0;

    $breakpoints = [];
    foreach ($this->lines as $line) {
      $line->generate();

      $breakpoints[] = $line->height;

      $this->height += $line->height;
      $this->width = max($this->width, $line->width);

      // TODO debug
      // $line->image2->savePng($dirOut . count($breakpoints) . '.png');
    }

    $this->imageReset();

    $position = 0;
    foreach ($this->lines as $line) {
      $this->image->copyFrom($line->image2, 0, $position);

      $position += $line->height;
    }

    $this->image->savePng($dirOut . 'output.png');
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
