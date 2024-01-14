<?php
/** Created by Gorlum 08.01.2024 19:57 */

namespace Tools;

require_once __DIR__ . '/SpriteLine.php';
require_once __DIR__ . '/ImageFile.php';
require_once __DIR__ . '/ImageContainer.php';

use ImageFile;

class Sprite {
  const LAYOUT_LINE = 0;
  const LAYOUT_COLUMN = 1;
  const LAYOUT_SQUARE = 'square';
  const LAYOUT_BTREE = 'btree';

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
   * @param ImageFile[] $images
   *
   * @return static
   *
   * @noinspection PhpUnusedParameterInspection
   */
  public static function createGridSquare($images, $layout) {
    $gridSize = ceil(sqrt(count($images)));
    usort($images, function (ImageFile $a, ImageFile $b) { return $b->height - $a->height; });

    $sprite = new static($gridSize);
    foreach ($images as $image) {
      $sprite->addToGrid($image);
    }

    return $sprite;
  }


  /**
   * @param ImageFile $imageFile
   *
   * @return void
   */
  public function addToGrid($imageFile) {
    // This is first image in row
    if (empty($this->lines[$this->lineIndex])) {
      $this->lines[$this->lineIndex] = new SpriteLine();
    }
    $this->lines[$this->lineIndex]->addImage($imageFile);
    if ($this->lines[$this->lineIndex]->getImageCount() >= $this->gridSize) {
      $this->lineIndex++;
    }
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

  /**
   * @param $scaleToPx
   */
  public function renderLines($scaleToPx) {
    $this->width = $this->height = 0;
    // Generating lines and calculating line sizes
    foreach ($this->lines as $line) {
      $line->generate($this->height, $scaleToPx);

      $this->height += $line->height;
      $this->width  = max($this->width, $line->width);

      // $line->image2->savePng($dirOut . count($breakpoints) . '.png'); // TODO remove debug
    }
  }

  /**
   * @param int $scaleToPx
   *
   * @return ImageContainer|null
   */
  public function generate($scaleToPx) {
    $this->renderLines($scaleToPx);

    // Recreating main sprite image with new width and height
    $this->imageReset();

    // Generating final sprite
    $position = 0;
    foreach ($this->lines as $line) {
      $this->image->copyFrom($line->image, 0, $position);

      $position += $line->height;
    }

    return $this->image;
  }

  /**
   * @param $scaleToPx
   *
   * @return string %3$s - $outName, %4$s - $relativeUrl
   */
  public function generateCss($scaleToPx) {
    $css = '';
    foreach ($this->lines as $line) {
      $css .= $line->css;
    }

    return ".%3\$s{background-image: url('%4\$s%3\$s.png');display: inline-block;" .
      ($scaleToPx > 0 ? "transform-origin: top left;" : "") .
      "}\n" . $css;
  }

}
