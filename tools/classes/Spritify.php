<?php

/** Created by Gorlum 08.01.2024 19:21 */

/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace Tools;

use Exception;
use ImageFile;

define('INSIDE', true);

require_once __DIR__ . '/../../classes/debug.php';
require_once __DIR__ . '/Sprite.php';

class Spritify {
  /**
   * @param string $dirIn       Input directory
   * @param string $dirOut      Output directory
   * @param string $outName     Name to use as CSS/PNG files
   * @param string $cssPrefix   Prefix to CSS qualifier. Default '#'
   * @param string $cssSuffix   Suffix to CSS qualifier. Default ''
   * @param int    $scaleToPx   Pixel size to scale largest side of sprite to for scale(). Default 0 - no scaling
   * @param string $relativeUrl Url relative to root where PNG sprite will reside - '/design/images/' by default
   *
   * @return void
   * @throws Exception
   */
  public static function go($dirIn, $dirOut, $outName, $cssPrefix = '#', $cssSuffix = '', $scaleToPx = 0, $relativeUrl = '/design/images/') {
    print "Processing folder/filter `$dirIn` to `$outName`\n";

//    $images = self::propagateImagesScanDir($dirIn);
    $images = self::propagateImagesGlob($dirIn);
    if (empty($images)) {
      print "No images found to process\n\n";

      return;
    }

    $sprite = Sprite::createGridSquare($images, Sprite::LAYOUT_SQUARE);

    $finalImage = $sprite->generate($scaleToPx);

    // Checking if output directory exists and creating one - if not
    if (!is_dir($dirOut) && !mkdir($dirOut, 0777, true)) {
      throw new Exception("Can't create output directory {$dirOut}\n");
    }
    // Saving PNG
    $finalImage->savePng($dirOut . $outName . '.png');
    file_put_contents(
      $dirOut . $outName . '.css',
      sprintf($sprite->generateCss($scaleToPx), $cssPrefix, $cssSuffix, $outName, $relativeUrl)
    );

    print "Folder/filter processed\n\n";
  }

  /**
   * Propagates array with image files from glob pattern (as in OS - with * and ?)
   *
   * @param string $dirIn
   *
   * @return ImageFile[]|false
   */
  protected static function propagateImagesGlob($dirIn) {
    if (($fullPathList = glob($dirIn, GLOB_BRACE)) === false) {
      print "Can't find directory `$dirIn`\n";

      return false;
    }

    /** @var ImageFile[] $images */
    $images = [];
    foreach ($fullPathList as $fullPath) {
      $fullPath = str_replace('\\', '/', realpath($fullPath));
      $images   = self::tryReadFile($fullPath, $images);
    }

    return $images;
  }

  /**
   * Propagates array with image files from folder
   *
   * @param string $dirIn
   *
   * @return ImageFile[]|false
   */
  protected static function propagateImagesScanDir($dirIn) {
    $dirRealPath = str_replace('\\', '/', realpath($dirIn ?: './'));
    if (!file_exists($dirRealPath) || !is_dir($dirRealPath)) {
      print "Can't find directory `$dirIn`\n";

      return false;
    }

    /** @var ImageFile[] $images */
    $images = [];

    foreach (scandir($dirRealPath) as $fileNameOnly) {
      if (in_array($fileNameOnly, ['.', '..',]) || is_dir($fullPath = "$dirRealPath/$fileNameOnly")) {
        continue;
      }
      $images = self::tryReadFile($fullPath, $images);
    }

    return $images;
  }

  /**
   * @param             $fullPath
   * @param ImageFile[] $images
   *
   * @return ImageFile[]
   */
  protected static function tryReadFile($fullPath, $images) {
    if ($image = ImageFile::read($fullPath)) {
      $images[] = $image;
      print "`$fullPath` read OK\n";
    } else {
      print "ERROR: Can't read file `$fullPath`\n";
    }

    return $images;
  }

}
