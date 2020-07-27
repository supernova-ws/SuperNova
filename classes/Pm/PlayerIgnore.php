<?php
/** @noinspection PhpDeprecationInspection */
/** @noinspection SqlResolve */

/**
 * Created by Gorlum 14.08.2019 1:37
 */

namespace Pm;

use Core\GlobalContainer;
use HelperString;

class PlayerIgnore {
  const IGNORE_PM = 1;

  /**
   * @var bool[][][] $ignores [$playerId][$ignoredId][$subsystem] = {false|true}
   */
  protected $ignores = [];

  public function __construct(GlobalContainer $gc) {
  }

  public function ignore($playerId, $ignoredId, $subsystem = self::IGNORE_PM) {
    if (!$this->isIgnored($playerId, $ignoredId, $subsystem)) {
      doquery("REPLACE INTO `{{player_ignore}}` SET `player_id` = {$playerId}, `ignored_id` = {$ignoredId}, `subsystem` = {$subsystem}");

      $this->ignores[$playerId][$ignoredId][$subsystem] = true;
    }
  }

  public function unIgnore($playerId, $ignoredId, $subsystem = self::IGNORE_PM) {
    if ($this->isIgnored($playerId, $ignoredId, $subsystem)) {
      doquery("DELETE FROM `{{player_ignore}}` WHERE `player_id` = {$playerId} AND `ignored_id` = {$ignoredId} AND `subsystem` = {$subsystem}");

      $this->ignores[$playerId][$ignoredId][$subsystem] = false;
    }
  }

  public function isIgnored($playerId, $ignoredId, $subsystem = self::IGNORE_PM) {
    if (!isset($this->ignores[$playerId][$ignoredId][$subsystem])) {
      $ignored = doquery("SELECT * FROM `{{player_ignore}}` WHERE `player_id` = {$playerId} AND `ignored_id` = {$ignoredId} AND `subsystem` = {$subsystem}", true);

      $this->ignores[$playerId][$ignoredId][$subsystem] = !empty($ignored);
    }

    return $this->ignores[$playerId][$ignoredId][$subsystem];
  }

  public function getIgnores($playerId, $htmlEncode = true) {
    $result = [];

    $ignores = doquery(
      "SELECT pi.*, u.username
        FROM `{{player_ignore}}` AS pi
            LEFT JOIN `{{users}}` AS u ON u.id = pi.ignored_id
        WHERE `player_id` = {$playerId} 
        ORDER BY `player_id`, `ignored_id`,`subsystem`"
    );
    while ($row = db_fetch($ignores)) {
      $name = $htmlEncode ? HelperString::htmlEncode($row['username']) : $row['username'];

      $result[] = [
        'ID'   => $row['ignored_id'],
        'NAME' => $name,
      ];
    }

    return $result;
  }

}
