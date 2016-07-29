<?php

/**
 * Created by Gorlum 27.01.2016 20:44
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
    $this->cache = classSupernova::$cache;
  }

  /**
   * @param int $player_id
   */
  public function player_id_set($player_id) {

  }

}
