<?php

/**
 * User: Gorlum
 * Date: 27.01.2016
 * Time: 20:44
 */
class PlayerDataCache {
  /**
   * Кэш игры
   *
   * @var classCache $cache
   */
  protected static $cache;

  /**
   * @var int $player_id
   */
  protected $player_id;

  public function __construct() {
    $this->cache = SN::$cache;
  }

  /**
   * @param int $player_id
   */
  public function player_id_set($player_id) {

  }

}
