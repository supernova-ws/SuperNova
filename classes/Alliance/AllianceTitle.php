<?php
/**
 * Created by Gorlum 28.11.2017 7:14
 */

namespace Alliance;

/**
 * Class AllianceTitle
 *
 * Alliance title and access rights
 *
 * @package Alliance
 */
class AllianceTitle {
  public $name = '';

  public $index = -2;

  public $mail = false;
  public $online = false;
  public $invite = false;
  public $kick = false;
  public $admin = false;
  public $forum = false;
  public $diplomacy = false;

  /**
   * @var Alliance $alliance
   */
  protected $alliance;


  /**
   * AllianceTitle constructor.
   *
   * @param Alliance $alliance
   */
  public function __construct(Alliance $alliance) {
    $this->alliance = $alliance;
  }

  /**
   * Fills title info from string
   *
   * @param int    $titleIndex - Internal Ally ID of title or -1 for owner
   * @param string $titleString - Title name and access rights or '' for owner
   */
  public function fromString($titleIndex, $titleString) {
    $this->index = $titleIndex;

    if ($titleIndex == Alliance::OWNER_INDEX) {
      $accessList = array_fill(1, count(Alliance::RIGHTS_ALL) - 1, 1);
      $this->name = $this->alliance->ownerRankName;
    } else {
      $accessList = explode(',', $titleString);
      $this->name = $accessList[0];
      unset($accessList[0]);
    }

    foreach ($accessList as $key => $access) {
      $this->{Alliance::RIGHTS_ALL[$key]} = $access == 1;
    }

//    var_dump($this->rightsAsString());
//    var_dump($this->getWeight());
  }

  /**
   * Compact title name and access rights into string
   *
   * @return string
   */
  public function __toString() {
    $result = [];
    foreach (Alliance::RIGHTS_ALL as $index => $rightName) {
      $result[] = $index == 0 ? $this->$rightName : ($this->$rightName ? 1 : 0);
    }

    return implode(',', $result);
  }

  /**
   * Compiles string with right lists for title
   *
   * @return string
   */
  public function rightsAsString() {
    $result = [];
    foreach (Alliance::RIGHT_WEIGHTS as $rightName => $weight) {
      $this->$rightName ? $result[] = $rightName : false;
    }

    return implode(',', $result);
  }

  /**
   * Get title right weight
   *
   * @return int
   */
  public function getWeight() {
    $totalWeight = 0;
    foreach (Alliance::RIGHT_WEIGHTS as $rightName => $weight) {
      $this->$rightName ? $totalWeight += $weight : false;
    }

    return $totalWeight;
  }

}
