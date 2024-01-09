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
   * @return void
   */
  public static function go($dirIn, $dirOut) {
    $dirIn = realpath($dirIn) . '/';

    $files = scandir($dirIn);

    /** @var ImageFile[] $images */
    $images = [];
    foreach ($files as $fileName) {
      if (in_array($fileName, ['.', '..',])) {
        continue;
      }

      $images[] = new ImageFile($dirIn, $fileName);
    }

    $gridSize = ceil(sqrt(count($images)));
    usort($images, function (ImageFile $a, ImageFile $b) { return $b->height - $a->height; });

//    pdump($images);
//    die();

    $sprite = new Sprite($gridSize);
    foreach ($images as $image) {
      $sprite->addToGrid($image);
    }

    $dirOut = realpath($dirOut) . '/';
    if (!is_dir($dirOut) && !mkdir($dirOut, 0777, true)) {
      throw new Exception("Can't create directory {$dirOut}\n");
    }

    $sprite->generate($dirOut);

//
//    $avatar_size = getimagesize($_FILES['avatar']['tmp_name']);
//    $avatar_max_width = $config->avatar_max_width;
//    $avatar_max_height = $config->avatar_max_height;
//    if ($avatar_size[0] > $avatar_max_width || $avatar_size[1] > $avatar_max_height) {
//      $aspect_ratio = min($avatar_max_width / $avatar_size[0], $avatar_max_height / $avatar_size[1]);
//      $avatar_image_new = imagecreatetruecolor($avatar_size[0] * $aspect_ratio, $avatar_size[0] * $aspect_ratio);
//      $result = imagecopyresized($avatar_image_new, $avatar_image, 0, 0, 0, 0, $avatar_size[0] * $aspect_ratio, $avatar_size[0] * $aspect_ratio, $avatar_size[0], $avatar_size[1]);
//      imagedestroy($avatar_image);
//      $avatar_image = $avatar_image_new;
//    }


    /*
  .bg-menu_affiliates {
      width: 12px; height: 12px;
      background: url('css_sprites.png') -58px -42px;
  }
     */

//    pdump($files);
//    pdump($images);
//    pdump($images[0]->height);

//    pdump($sprite);
  }

}
