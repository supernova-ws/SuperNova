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
   * @param string|string[] $dirInArray  Input directory
   * @param string          $dirOut      Output directory
   * @param string          $outName     Name to use CSS/PNG file names and global CSS class
   * @param string          $cssPrefix   Prefix to CSS qualifier. Default '#'
   * @param string          $cssSuffix   Suffix to CSS qualifier. Default ''
   * @param int             $scaleToPx   Pixel size to scale largest side of sprite to for scale(). Default 0 - no scaling
   * @param string          $relativeUrl Url relative to root where PNG sprite will reside. Default: '/design/images/'
   * @param string          $layout      How images should be arranged in sprite. Default: Sprite::LAYOUT_SQUARE
   *
   * @return void
   *
   * @throws Exception
   * @see Sprite::LAYOUT_SQUARE and others Sprite::LAYOUT_XXX constants
   *
   */
  public static function go($dirInArray, $dirOut, $outName, $cssPrefix = '#', $cssSuffix = '', $scaleToPx = 0, $relativeUrl = '/design/images/', $layout = Sprite::LAYOUT_SQUARE) {
    if (!is_array($dirInArray)) {
      $dirInArray = [$dirInArray];
    }

    $images = [];
    foreach ($dirInArray as $dirIn) {
      print "Loading files from folder/filter `$dirIn` \n";

//    $images = self::propagateImagesScanDir($dirIn);
      $images = array_merge($images, self::propagateImagesGlob($dirIn));
    }

    print "Processing images to `$outName`\n";
    if (empty($images)) {
      print "No images found to process\n\n";

      return;
    }

    $sprite = new Sprite($images, $layout, $scaleToPx);
    $sprite->generate();
    $sprite->saveOutput($dirOut, $outName, [$cssPrefix, $cssSuffix, $outName, $relativeUrl,]);

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
