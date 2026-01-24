<?php
/** Created by Gorlum 24.01.2026 19:07 */

namespace Refactor\PlayerNickRenderer;

class PlayerNickRenderOption {
  public bool $color = false;
  public bool $ally = false;
  public bool $icons = false;

  public bool $gender = false;
  public bool $player_rank = false;
  public bool $birthday = false;
  public bool $vacancy = false;
  public bool $race = false;
  public bool $award = false;

  /**
   * @psalm-type PlayerNickRenderOptionArray = array{
   *    color:bool,
   *    ally:bool,
   *    icons:bool,
   *    gender:bool,
   *    birthday:bool,
   *    vacancy:bool,
   *    player_rank:bool,
   *    race:bool,
   *    award:bool,
   * }
   *
   * @param PlayerNickRenderOptionArray|bool $options
   */
  public function __construct($options) { }

}
