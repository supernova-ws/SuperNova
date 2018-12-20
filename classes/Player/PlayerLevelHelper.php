<?php
/**
 * Created by Gorlum 13.12.2017 15:45
 */

namespace Player;

use Core\GlobalContainer;
use Bonus\ValueStorage;
use HelperString;
use SN;
use classConfig;

class PlayerLevelHelper {
  /**
   * @var float[] $playerLevels - [(int)level => (float)maxPointsForLevel]
   */
  protected $playerLevels = [];

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var classConfig $config
   */
  protected $config;

  /**
   * @var ValueStorage $valueStorage
   */
  protected $valueStorage;

  /**
   * PlayerLevelHelper constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
    $this->config = $this->gc->config;
    $this->valueStorage = $this->gc->valueStorage;
  }

  /**
   * Get player level table
   *
   * @return float[]
   */
  public function getPlayerLevels() {
    if ($this->isLevelExpired() || empty($this->playerLevels)) {
      $this->loadLevels();
    }

    return $this->playerLevels;
  }

  /**
   * Calculate level by supplied points
   *
   * Presumed that it's a total points but can be any others
   *
   * @param float $totalPoints
   *
   * @return int
   */
  public function getPointLevel($totalPoints, $authLevel = false) {
    if ($authLevel && SN::$config->stats_hide_admins) {
      return PLAYER_RANK_MAX;
    }

    $playerLevels = $this->getPlayerLevels();

    $theLevel = null;
    foreach ($playerLevels as $level => $points) {
      if ($totalPoints <= $points) {
        $theLevel = $level;
        break;
      }
    }

    // If no levels found - it means that points is above max calculated level
    // We will address it tomorrow - when levels recalculates. For now just give use +1 level - would be ok for a day
    if ($theLevel === null) {
      end($playerLevels);
      $theLevel = key($playerLevels) + 1;
    }
    if ($theLevel > PLAYER_RANK_MAX) {
      $theLevel = PLAYER_RANK_MAX;
    }

    return $theLevel;
  }

  /**
   * @param int  $playerRank
   * @param bool $noTitle - do not add title to image
   *
   * @return string
   */
  public function renderRank($playerRank, $noTitle = false) {
    $title = (empty($noTitle)
      ? " title='" . HelperString::htmlEncode("[{$playerRank}] " . SN::$lang['ranks'][$playerRank]) . "'"
      : ''
    );

    return "<div class='rank nick rank-{$playerRank}'{$title}></div>";
  }

  /**
   * @param float $playerPoints
   * @param int   $authLevel
   * @param bool  $noTitle - do not add title to image
   *
   * @return string
   */
  public function renderRankFromPoints($playerPoints, $authLevel = AUTH_LEVEL_REGISTERED, $noTitle = false) {
    return $this->renderRank($this->getPointLevel($playerPoints, $authLevel), $noTitle);
  }

  /**
   * Should level be recalculated?
   *
   * @return bool
   */
  protected function isLevelExpired() {
    return datePart(strtotime($this->config->player_levels_calculated)) < datePart(SN_TIME_NOW);
  }

  /**
   * Loading level data from DB
   */
  protected function loadLevels() {
    if ($this->isLevelExpired()) {
      $levelArray = [];
    } else {
      $levelArray = json_decode($this->config->player_levels, true);
    }

    if (empty($levelArray) || !is_array($levelArray)) {
      $this->calcLevels();
      $this->storeLevels();
    } else {
      $this->playerLevels = $levelArray;
    }
  }

  /**
   * Storing level data in DB
   */
  protected function storeLevels() {
    $this->config->pass()->player_levels_calculated = SN_TIME_SQL;
    $this->config->pass()->player_levels = json_encode($this->playerLevels);
  }

  /**
   * Calculate level table
   */
  protected function calcLevels() {
    $this->playerLevels = [];

    $multiplier = 5; // $this->valueStorage->getValue(UNIT_SERVER_FLEET_NOOB_FACTOR);
    !$multiplier ? $multiplier = 5 : false;

    $levelPoints = $this->valueStorage->getValue(UNIT_SERVER_FLEET_NOOB_POINTS);
    !$levelPoints ? $levelPoints = 5000 * $this->valueStorage->getValue(UNIT_SERVER_SPEED_MINING) : false;

    for ($level = 0; $level <= PLAYER_RANK_MAX; $level++) {
      $this->playerLevels[$level] = $levelPoints;
      $levelPoints *= $multiplier;
    }
  }

}
