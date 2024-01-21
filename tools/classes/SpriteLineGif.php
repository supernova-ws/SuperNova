<?php
/** Created by Gorlum 21.01.2024 01:07 */

namespace Tools;

use GIFEndec\Decoder;
use GIFEndec\Events\FrameDecodedEvent;
use GIFEndec\Frame;
use GIFEndec\IO\FileStream;

class SpriteLineGif extends SpriteLine {
  /** @var Frame[] $frames */
  protected $frames = [];

  protected function addImage($imageFile) {
    $this->files[] = $imageFile;

    $this->frames = [];

    $this->height = $this->width = 0;

    /** Open GIF as FileStream */
    // TODO - own class from loaded
    $gifStream = new FileStream($imageFile->fullPath);
    /** Create Decoder instance from MemoryStream */
    $gifDecoder = new Decoder($gifStream);
    /** Run decoder. Pass callback function to process decoded Frames when they're ready. */
    $gifDecoder->decode(function (FrameDecodedEvent $event) {
      $this->frames[] = $event->decodedFrame;

      $this->width += $event->decodedFrame->getSize()->getWidth();
      $this->height = max($this->height, $event->decodedFrame->getSize()->getHeight());
      // For EXPAND_FRAME delta width would be equal size of the largest frame
    });
  }

  /**
   * GIF image line considered always full
   *
   * @param $gridSize
   *
   * @return bool
   */
  protected function isFull($gridSize) {
    return true;
  }

  public function generate($posY, $scaleToPx) {
    unset($this->image);

    $this->image = ImageContainer::create($this->width, $this->height);

    $file     = reset($this->files);
    $onlyName = explode('.', $file->fileName);
    if (count($onlyName) > 1) {
      array_pop($onlyName);
    }
    $onlyName = implode('.', $onlyName);

    $position = 0;
    foreach ($this->frames as $i => $frame) {
      /**
       * Disposal method
       * Values :
       *   0 - No disposal specified. The decoder is not required to take any action.
       *   1 - Do not dispose. The graphic is to be left in place.
       *   2 - Restore to background color. The area used by the graphic must be restored to the background color.
       *   3 - Restore to previous. The decoder is required to restore the area overwritten by the graphic with
       *       what was there prior to rendering the graphic.
       */
      $frameGdImage = $frame->createGDImage();
      $this->image->copyFromGd($frameGdImage, $position, 0);

      $frameName = $onlyName . '_' . $i;

      $frame = $this->frames[$i];

      $css = "%1\$s{$frameName}%2\$s{background-position: -{$position}px -{$posY}px;";

      // Extra info about frame
      $size   = $frame->getSize();
      $offset = $frame->getOffset();
      $css    = "/* Frame {$size->getWidth()}x{$size->getWidth()} @ ({$offset->getX()},{$offset->getY()}) disposition {$frame->getDisposalMethod()} */" . $css;

      if ($scaleToPx > 0) {
        $maxSize = max($file->width, $file->height);
        if ($maxSize != $scaleToPx) {
          $css .= "zoom: calc({$scaleToPx}/{$maxSize});";
        }
      }
      $css .= "width: {$frame->getSize()->getWidth()}px;height: {$frame->getSize()->getHeight()}px;}\n";

      if ($i === 0) {
        // If it's first frame - generating CSS for static image
        $css = "%1\$s{$onlyName}%2\$s," . $css;
      }

      $this->css .= $css;

      $position += $frame->getSize()->getWidth();
    }
  }

}
