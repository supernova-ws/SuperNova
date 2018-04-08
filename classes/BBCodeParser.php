<?php

/**
 * Created by Gorlum 14.02.2017 11:18
 */
class BBCodeParser {

  /**
   * @var Design $design
   */
  protected $design;

  public function __construct(Core\GlobalContainer $gc) {
    $this->design = $gc->design;
  }

  protected function applyElements($elements, $text, $authorAccessLevel = AUTH_LEVEL_REGISTERED) {
    foreach ($elements as $auth_level => $element) {
      if ($auth_level > $authorAccessLevel) {
        continue;
      }

      foreach ($element as $find => $replace) {
        $text = preg_replace($find, $replace, $text);
      }
    }

    return $text;
  }

  /**
   * Expands bbCodes and smiles in text
   *
   * @param      $text
   * @param int  $authorAccessLevel
   * @param int  $encodeOptions - HTML_ENCODE_xxx constants. HTML_ENCODE_MULTILINE by default
   *
   * @return mixed
   */
  public function expandBbCode($text, $authorAccessLevel = AUTH_LEVEL_REGISTERED, $encodeOptions = HTML_ENCODE_MULTILINE) {
    $text = HelperString::htmlEncode($text, $encodeOptions);

    $text = $this->applyElements($this->design->getBbCodes(), $text, $authorAccessLevel);
    $text = $this->applyElements($this->design->getSmiles(), $text, $authorAccessLevel);

    return $text;
  }

  public function compactBbCode($text, $authorAccessLevel = AUTH_LEVEL_REGISTERED, $encodeOptions = HTML_ENCODE_MULTILINE) {
    // Replacing news://xxx link with BBCode
    $text = preg_replace("#news\:\/\/(\d+)#", "[news=$1]", $text);
    // Replacing news URL with BBCode
    $text = preg_replace("#(?:https?\:\/\/(?:.+)?\/announce\.php\?id\=(\d+))#", "[news=$1]", $text);
    $text = preg_replace("#(?:https?\:\/\/(?:.+)?\/index\.php\?page\=battle_report\&cypher\=([0-9a-zA-Z]{32}))#", "[ube=$1]", $text);

    return $text;
  }
}
