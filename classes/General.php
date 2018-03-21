<?php
/**
 * Created by Gorlum 12.10.2017 15:19
 */

use Core\GlobalContainer;

/**
 * Class General
 *
 * Wrapper for /includes/general.php
 * Will make unit testing easier
 *
 */
class General {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var \Bonus\ValueStorage $valueStorage
   */
  protected $valueStorage;

  /**
   * General constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
    $this->valueStorage = $this->gc->valueStorage;
  }

  /**
   * @param string|string[] $groupNameList
   *
   * @return array|array[]
   */
  public function getGroupsByName($groupNameList) {
    return sn_get_groups($groupNameList);
  }

  /**
   * @param int|string|array $groupIdList
   *
   * @return array|array[]
   */
  public function getGroupsById($groupIdList) {
    if (!is_array($groupIdList)) {
      $groupIdList = [$groupIdList];
    }

    $idToName = $this->getGroupsByName(GROUP_GROUP_ID_TO_NAMES);
    foreach ($groupIdList as &$groupId) {
      if (!empty($idToName[$groupId])) {
        $groupId = $idToName[$groupId];
      }

    }

    return $this->getGroupsByName($groupIdList);
  }

  /**
   * @return float|int
   */
  public function fleetNoobPoints() {
    return $this->valueStorage->getValue(UNIT_SERVER_FLEET_NOOB_POINTS) * game_resource_multiplier(true);
  }

  /**
   * Is player qualified as "noob" aka "New Inexpirienced player"
   *
   * @param float $playerTotalPoints
   *
   * @return bool
   */
  public function playerIsNoobByPoints($playerTotalPoints) {
    return $playerTotalPoints <= $this->fleetNoobPoints();
  }

  /**
   * Is player 1 stronger then player 2 counting game noob factor?
   *
   * @param float $player1TotalPoints
   * @param float $player2TotalPoints
   *
   * @return bool
   */
  public function playerIs1stStrongerThen2nd($player1TotalPoints, $player2TotalPoints) {
    $gameNoobFactor = $this->valueStorage->getValue(UNIT_SERVER_FLEET_NOOB_FACTOR);

    return $gameNoobFactor && $player1TotalPoints > $player2TotalPoints * $gameNoobFactor;
  }

}
