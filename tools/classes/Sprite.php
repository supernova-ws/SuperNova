<?php
/** Created by Gorlum 08.01.2024 19:57 */

namespace Tools;

require_once __DIR__ . '/SpriteLine.php';
require_once __DIR__ . '/SpriteLineGif.php';
require_once __DIR__ . '/ImageFile.php';
require_once __DIR__ . '/ImageContainer.php';

use Exception;
use ImageFile;

class Sprite {
  const LAYOUT_LINE = 0;
  const LAYOUT_COLUMN = 1;
  const LAYOUT_SQUARE = 'square';
  // const LAYOUT_MINIMAL = 'minimal'; // Minimize sum of linear dimensions
  // const LAYOUT_EQUAL_HEIGHT = 'equal_height'; // Put images with equal height in one line
  const LAYOUT_BTREE = 'btree';

  /** @var ImageFile[] $imageList */
  public $imageList = [];
  /** @var SpriteLine[] $lines */
  public $lines = [];
  /** @var ImageContainer|null $image */
  public $image = null;

  /** @var int $gridSize Sprite grid size in images */
  protected $gridSize = 0;
  /** @var int $scaleToPx Scale input images to specified PX. Default: 0 - no scaling */
  protected $scaleToPx = 0;

  protected $lineIndex = 0;
  protected $columnIndex = 0;

  protected $height = 0;
  protected $width = 0;

  /**
   * @param ImageFile[] $images
   * @param             $layout
   */
  public function __construct($images, $layout, $scaleToPx) {
    $this->scaleToPx = $scaleToPx;

    if ($layout === self::LAYOUT_SQUARE) {
      $gridSize = ceil(sqrt(count($images)));
    } elseif ($layout === self::LAYOUT_COLUMN) {
      $gridSize = 1;
    } elseif ($layout === self::LAYOUT_LINE) {
      $gridSize = 0;
    } else {
      $gridSize = 1;
    }

    $this->gridSize = $gridSize;

    $this->imageList = $images;
  }

  /**
   * @return void
   */
  protected function imageReset() {
    if (!empty($this->image)) {
      unset($this->image);
    }

    $this->image = ImageContainer::create($this->width, $this->height);
  }

  /**
   * @param $scaleToPx
   */
  protected function renderLines($scaleToPx) {
    $this->width = $this->height = 0;
    // Generating lines and calculating line sizes
    foreach ($this->lines as $line) {
      $line->generate($this->height, $scaleToPx);

      $this->height += $line->height;
      $this->width  = max($this->width, $line->width);

//      $line->image->savePng(__DIR__ . '/../gif_line_' . $this->height . '.png'); // TODO remove debug
    }
  }

  /**
   *
   * @return ImageContainer|null
   */
  public function generate() {
    $this->createGrid();

    $this->renderLines($this->scaleToPx);

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
   *
   * @return string %3$s - $outName, %4$s - $relativeUrl
   */
  protected function generateCss() {
    $css = '';
    foreach ($this->lines as $line) {
      $css .= $line->css;
    }

    return ".%3\$s{background-image: url('%4\$s%3\$s.png');display: inline-block;" .
      ($this->scaleToPx > 0 ? "transform-origin: top left;" : "") .
      "}\n" . $css;
  }

  public function savePng($fileName) {
    // Saving PNG
    $this->image->savePng($fileName);
  }

  /**
   * @param          $fileName
   * @param string[] $vsprintf [$cssPrefix, $cssSuffix, $outName, $relativeUrl,]
   *
   * @return false|int
   */
  public function saveCss($fileName, $vsprintf = []) {
    // Saving CSS
    return file_put_contents($fileName, vsprintf($this->generateCss(), $vsprintf));
  }

  /**
   * @param string   $dirOut
   * @param string   $outName
   * @param string[] $vsprintf [$cssPrefix, $cssSuffix, $outName, $relativeUrl,]
   *
   * @return void
   * @throws Exception
   */
  public function saveOutput($dirOut, $outName, $vsprintf = []) {
    // Checking if output directory exists and creating one - if not
    if (!is_dir($dirOut) && !mkdir($dirOut, 0777, true)) {
      throw new Exception("Can't create output directory {$dirOut}\n");
    }
    // Saving PNG
    $this->savePng($dirOut . $outName . '.png');
    $this->saveCss($dirOut . $outName . '.css', $vsprintf);
  }

  /**
   * @return void
   */
  protected function createGrid() {
    $this->lineIndex = 0;

    usort($this->imageList, function (ImageFile $a, ImageFile $b) { return $b->height - $a->height; });

    $prevLine = new SpriteLine();
    foreach ($this->imageList as $image) {
      $line = $prevLine->fillLine($image, $this->gridSize);
      if ($line != $prevLine) {
        if ($prevLine && $prevLine->width > 0) {
          $this->lines[$this->lineIndex] = $prevLine;
          $this->lineIndex++;
        }
        $prevLine = $line;
      }
    }

    if ($prevLine) {
      // There is something in line to keep
      $this->lines[$this->lineIndex] = $prevLine;
    }
  }

}
