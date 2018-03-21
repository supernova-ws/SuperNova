<?php

/**
 * Created by Gorlum 15.02.2017 10:08
 */

use Core\GlobalContainer;

/**
 * Class Design
 *
 * Describes design elements. For now it's a smileset and bbCodes
 *
 */
class Design {
  /**
   * @var classConfig $gameConfig
   */
  protected $gameConfig;

  protected $smileList = array();
  protected $smiles = array();
  protected $bbCodes = array();

  /**
   * Design constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gameConfig = SN::$gc->config;

    // Prefix faq:// resolves to FAQ's URL - if configured
    if (is_object($this->gameConfig) && !empty($this->gameConfig->url_faq)) {
      $this->addBbCodes(array(
        '#faq://#isU' => $this->gameConfig->url_faq,
      ), AUTH_LEVEL_REGISTERED);
    }

    global $sn_data_bbCodes, $sn_data_smiles;
    $this->addBbCodes($sn_data_bbCodes);
    $this->addSmiles($sn_data_smiles);
  }

  protected function detectElementsFormat($elements, $accessLevel = AUTH_LEVEL_REGISTERED) {
    $firstElement = reset($elements);
    if (!is_array($firstElement)) {
      // Plain element list - making access list
      $elements = array($accessLevel => $elements);
    }

    return $elements;
  }

  /**
   * @param array               $array
   * @param string[]|string[][] $elements <p>list of smiles. Can be:</p>
   *    <p>- plain list of smiles in format ['text' => 'imageName',...], i.e. [':p:' => 'tongue']</p>
   *    <p>- access list of plaint list of smiles in format [AUTH_LEVEL_xxx => ['text' => 'imageName',...], ...], i.e. [AUTH_LEVEL_REGISTERED => [':p:' => 'tongue']]</p>
   *    <p>'text' can be a regex - otherwise it would be forcibly formatted to regex '#text#isU'</p>
   * @param int                 $accessLevel - access level for plain element list. Default - AUTH_LEVEL_REGISTERED
   */
  protected function addElements(&$array, $elements, $accessLevel = AUTH_LEVEL_REGISTERED) {
    if (!is_array($elements) || empty($elements)) {
      return; // TODO - Exception
    }

    $elements = $this->detectElementsFormat($elements, $accessLevel);

    HelperArray::merge($array, $elements, HelperArray::MERGE_RECURSIVE_NUMERIC);
  }

  /**
   * Add bbCodes to global collection
   *
   * @param string[]|string[][] $bbCodes <p>list of bbCodes formatted like in Design::addElements</p>
   * @param int                 $accessLevel - access level for plain element list. Default - AUTH_LEVEL_REGISTERED
   *
   * @see Design::addElements
   */
  public function addBbCodes($bbCodes, $accessLevel = AUTH_LEVEL_REGISTERED) {
    $this->addElements($this->bbCodes, $bbCodes, $accessLevel);
  }

  /**
   * Add smiles to global collection
   *
   * @param string[]|string[][] $smiles <p>list of smiles formatted like in Design::addElements</p>
   * @param int                 $accessLevel - access level for plain element list. Default - AUTH_LEVEL_REGISTERED
   *
   * @see Design::addElements
   */
  public function addSmiles($smiles, $accessLevel = AUTH_LEVEL_REGISTERED) {
    $smiles = $this->detectElementsFormat($smiles, $accessLevel);

    // Detecting regexps or plain text - converting plain text to regexps
    // This is for back compatibility with Festival data
    foreach ($smiles as $elementAuth => &$elementList) {
      $surelyRegexps = array();
      foreach ($elementList as $find => $replace) {
        // Caching smiles as-is for smile list generation
        $this->smileList[$elementAuth][$find] = $replace;

        // Now converting smile original data to preg_replace() params
        $replace = "<img src=\"design/images/smileys/" . $replace . ".gif\" align=\"absmiddle\" title=\"" . $find . "\" alt=\"" . $find . "\">";

        // Check for regexp
        if (($firstChar = substr($find, 0, 1)) != '#' && $firstChar != '/') {
          $find = '#' . addcslashes($find, '()[]{}') . '#isU';
        }

        // To maintain element order even when index change
        $surelyRegexps[$find] = $replace;
      }
      $elementList = $surelyRegexps;
    }

    $this->addElements($this->smiles, $smiles, $accessLevel);
  }

  // Getters/Setters +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  public function getSmiles() {
    return $this->smiles;
  }

  public function getBbCodes() {
    return $this->bbCodes;
  }

  /**
   * Return smiles for making smile's list
   *
   * @return array
   */
  public function getSmilesList() {
    return $this->smileList;
  }

}
