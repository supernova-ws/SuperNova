<?php
/** Created by Gorlum 21.01.2024 01:07 */

namespace Tools;

use GIFEndec\Decoder;
use GIFEndec\Events\FrameDecodedEvent;
use GIFEndec\Frame;
use GIFEndec\IO\FileStream;

class SpriteLineGif extends SpriteLine {
  /** @var bool $expandFrame Should frame be expanded for CSS animation? */
  protected $expandFrame = true;

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

      $this->width  += $event->decodedFrame->getSize()->getWidth();
      $this->height = max($this->height, $event->decodedFrame->getSize()->getHeight());
    });
    // For EXPAND_FRAME delta width would be equal size of the largest frame
    $this->width = count($this->frames) * reset($this->frames)->getSize()->getWidth();
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
    // Extracting file name from full path
    $file     = reset($this->files);
    $onlyName = explode('.', $file->fileName);
    if (count($onlyName) > 1) {
      array_pop($onlyName);
    }
    $onlyName = implode('.', $onlyName);
    // You can't have this chars in CSS qualifier
    $onlyName = str_replace(['.', '#'], '_', $onlyName);

    $maxDimension = max($file->width, $file->height);

    // Recreating image - if any
    unset($this->image);
    $this->image = ImageContainer::create($this->width, $this->height);

    $position = 0;
    foreach ($this->frames as $i => $frame) {
      $frameGdImage = $this->expandFrame($i);
      $width = imagesx($frameGdImage);
      $height = imagesy($frameGdImage);


      $this->image->copyFromGd($frameGdImage, $position, 0);

      $frameName = $onlyName . '_' . $i;

      $frame = $this->frames[$i];

      $css = "%1\$s{$frameName}%2\$s{background-position: -{$position}px -{$posY}px;";

      // Extra info about frame
      $size   = $frame->getSize();
      $offset = $frame->getOffset();
      $css    = "/* Frame {$size->getWidth()}x{$size->getHeight()} @ ({$offset->getX()},{$offset->getY()}) duration {$frame->getDuration()} disposition {$frame->getDisposalMethod()} */" . $css;

      if ($scaleToPx > 0) {
        if ($maxDimension != $scaleToPx) {
          $css .= "zoom: calc({$scaleToPx}/{$maxDimension});";
        }
      }
      $css .= "width: {$width}px;height: {$height}px;}\n";

      if ($i === 0) {
        // If it's first frame - generating CSS for static image
        $css = "%1\$s{$onlyName}%2\$s," . $css;
      }

      $this->css .= $css;

      $position += $width;
    }
  }

  /**
   * @param int $i
   *
   * @return resource|\GdImage
   */
  protected function expandFrame($i) {
    /**
     * Disposal method
     * Values :
     *   0 - No disposal specified. The decoder is not required to take any action.
     *   1 - Do not dispose. The graphic is to be left in place.
     *   2 - Restore to background color. The area used by the graphic must be restored to the background color.
     *   3 - Restore to previous. The decoder is required to restore the area overwritten by the graphic with
     *       what was there prior to rendering the graphic.
     */
    $thisFrame = $this->frames[$i];
    if (!$this->expandFrame || $i === 0) {
      // This is first frame - just return it immediately
      return $thisFrame->gdImage = $thisFrame->createGDImage();
    }
//    return $thisFrame->createGDImage();

    $prevFrame = $this->frames[$i - 1];
    // For now no different
    if (!in_array($prevFrame->getDisposalMethod(), [0, 1])) {
      die("Disposal method {$prevFrame->getDisposalMethod()} does not supported yet");
    }
    // Disposal method 0 or 1 - just copy next frame above

    // Creating detached copy of previous frame image
    $newGdImage = imagecreatetruecolor(imagesx($prevFrame->gdImage), imagesy($prevFrame->gdImage));
    imagealphablending($newGdImage, false);
    imagesavealpha($newGdImage, true);
    imagefill($newGdImage, 0, 0, imagecolorallocatealpha($newGdImage, 0, 0, 0, 127));

    imagecopy($newGdImage, $prevFrame->gdImage,
      0, 0,
      0, 0, imagesx($prevFrame->gdImage), imagesy($prevFrame->gdImage)
    );
    //
    $sourceGdImage = $thisFrame->createGDImage();
    imagecopy($newGdImage, $sourceGdImage,
      // GIF offset starts from (1,1) instead of (0,0)
      $thisFrame->getOffset()->getX() - 1, $thisFrame->getOffset()->getY() - 1,
      0, 0, imagesx($sourceGdImage), imagesy($sourceGdImage)
    );

    return $thisFrame->gdImage = $newGdImage;
  }

}
