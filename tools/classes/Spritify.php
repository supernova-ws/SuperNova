<?php
/** Created by Gorlum 08.01.2024 19:21 */

namespace Tools;

use Exception;
use ImageFile;

define('INSIDE', true);

require_once __DIR__ . '/../../classes/debug.php';
require_once __DIR__ . '/Sprite.php';

class Spritify {
  /**
   * @param string $dirIn        Input directory
   * @param string $dirOut       Output directory
   * @param string $outName      Name to use as CSS/PNG files
   * @param string $cssPrefix    Prefix to CSS qualifier. Default '#'
   * @param string $cssSuffix    Suffix to CSS qualifier. Default ''
   * @param int    $scaleToPx    Pixel size to scale largest side of sprite to for scale(). Default 0 - no scaling
   * @param string $httpLocation Url relative to root where PNG sprite will reside - '/design/images/' by default
   *
   * @return void
   * @throws Exception
   */
  public static function go($dirIn, $dirOut, $outName, $cssPrefix = '#', $cssSuffix = '', $scaleToPx = 0, $httpLocation = '/design/images/') {
//    $images = self::propagateImagesScanDir(scandir($dirIn), $dirIn);
    $images = self::propagateImagesGlob(glob($dirIn, GLOB_BRACE));

    $sprite = Sprite::createGridSquare($images, Sprite::LAYOUT_SQUARE);

    if (!is_dir($dirOut) && !mkdir($dirOut, 0777, true)) {
      throw new Exception("Can't create directory {$dirOut}\n");
    }

    $sprite->generate($dirOut, $outName, $cssPrefix, $cssSuffix, $scaleToPx, $httpLocation);
  }

  /**
   * Propagates array with image files from glob pattern (as in OS - with * and ?)
   *
   *
   * @return ImageFile[]
   */
  protected static function propagateImagesGlob($fullFiles) {
    /** @var ImageFile[] $images */
    $images = [];
    foreach ($fullFiles as $fileName) {
      $images[] = new ImageFile('', '', $fileName);
    }

    return $images;
  }

  /**
   * Propagates array with image files from folder
   *
   * @param array  $files
   * @param string $dirIn
   *
   * @return ImageFile[]
   */
  protected static function propagateImagesScanDir($files, $dirIn) {
    /** @var ImageFile[] $images */
    $images = [];
    foreach ($files as $fileName) {
      if (in_array($fileName, ['.', '..',])) {
        continue;
      }

      $images[] = new ImageFile($dirIn, $fileName, '');
    }

    return $images;
  }

}
