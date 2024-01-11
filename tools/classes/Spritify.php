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
   * @param string $dirIn
   * @param string $dirOut
   *
   * @return void
   * @throws Exception
   */
  public static function go($dirIn, $dirOut) {
    $dirIn = realpath($dirIn) . '/';

    $images = self::propagateImages(scandir($dirIn), $dirIn);

    $sprite = Sprite::createGridSquare($images);

    $dirOut = realpath($dirOut) . '/';
    if (!is_dir($dirOut) && !mkdir($dirOut, 0777, true)) {
      throw new Exception("Can't create directory {$dirOut}\n");
    }

    $sprite->generate($dirOut);
  }

  /**
   * Propagates array with image files from folder
   *
   * @param array  $files
   * @param string $dirIn
   *
   * @return ImageFile[]
   */
  protected static function propagateImages($files, $dirIn) {
    /** @var ImageFile[] $images */
    $images = [];
    foreach ($files as $fileName) {
      if (in_array($fileName, ['.', '..',])) {
        continue;
      }

      $images[] = new ImageFile($dirIn, $fileName);
    }

    return $images;
  }

}
