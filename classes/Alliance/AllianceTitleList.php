<?php
/**
 * Created by Gorlum 28.11.2017 7:15
 */

namespace Alliance;


class AllianceTitleList {

  /**
   * @var Alliance $alliance
   */
  protected $alliance;

  /**
   * @var AllianceTitle[] $titles
   */
  protected $titles = [];

  public function __construct(Alliance $alliance) {
    $this->alliance = $alliance;

    if (!empty($this->alliance->titleList)) {
      $this->extractTitles();
    }
  }

  /**
   * @return AllianceTitle[]
   */
  public function getTitles() {
    return $this->titles;
  }

  /**
   * Compact all titles into one string
   *
   * @return string
   */
  public function __toString() {
    $result = [];
    foreach ($this->titles as $index => $title) {
      if ($index == Alliance::OWNER_INDEX) {
        continue;
      }

      $result[] = (string)$title;
    }

    return implode(';', $result);
  }

  /**
   * @return int[]
   */
  public function getWeights() {
    $result = [];
    foreach ($this->titles as $index => $title) {
      $result[$index] = $title->getWeight();
    }

    return $result;
  }

  /**
   * Updates title names and access rights with current ones
   */
  public function updateTitles() {
    $this->alliance->titleList = (string)$this;
  }

  protected function extractTitles() {
    $this->titles = [];

    $this->titles[Alliance::OWNER_INDEX] = new AllianceTitle($this->alliance);
    $this->titles[Alliance::OWNER_INDEX]->fromString(Alliance::OWNER_INDEX, '');

    $titleList = explode(';', $this->alliance->titleList);
    foreach ($titleList as $titleIndex => $titleString) {
      if (empty($titleString)) {
        continue;
      }
      $this->titles[$titleIndex] = new AllianceTitle($this->alliance);
      $this->titles[$titleIndex]->fromString($titleIndex, $titleString);
    }

    return $this->titles;
  }

  /**
   * @param $titleIndex
   *
   * @return AllianceTitle|null
   */
  public function getTitle($titleIndex) {
    return !empty($this->titles[$titleIndex]) ? $this->titles[$titleIndex] : null;
  }


  /**
   * @return array
   */
  public function asPtl() {
    $result = [];
    foreach ($this->titles as $title) {
      $result[] = [
        'INDEX'       => $title->index,
        'NAME'        => $title->name,
        'NAME_SAFE'   => \HelperString::htmlSafe($title->name),
        'RIGHTS'      => $title->rightsAsString(),
        'RIGHTS_TEXT' => \HelperString::htmlSafe($title->rightsAsString()),
      ];
    }

    return $result;
  }

}
