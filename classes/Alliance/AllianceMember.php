<?php
/**
 * Created by Gorlum 28.11.2017 11:15
 */

namespace Alliance;


use Player\RecordPlayer;

/**
 * Class AllianceMember
 *
 * Class represents member of Alliance
 *
 * @package Alliance
 */
class AllianceMember {

  /**
   * @var AllianceTitle $title
   */
  protected $title;
  /**
   * @var array $player
   */
  public $player;
  /**
   * @var Alliance $alliance
   */
  protected $alliance;


  public function __construct(Alliance $alliance, RecordPlayer $player) {
    $this->alliance = $alliance;
    $this->player = $player;

    $this->title = $this->alliance->getTitleList()->getTitle($this->isOwner() ? Alliance::OWNER_INDEX : $this->player->ally_rank_id);
  }

  /**
   * Change title of member
   *
   * @param AllianceTitle $title
   *
   * @return bool
   */
  public function changeTitle(AllianceTitle $title) {
    if(!$title instanceof AllianceTitle) {
      return false;
    }

    $this->player->ally_rank_id = $title->index;

    return $this->player->update();
  }

  /**
   * Get player ID of member
   *
   * @return mixed
   */
  public function getPlayerId() {
    return $this->player->id;
  }

  /**
   * @return mixed
   */
  public function getMemberName() {
    return $this->player->username;
  }

  /**
   * @return bool
   */
  public function isOwner() {
    return $this->getPlayerId() == $this->alliance->ownerId;
  }

  /**
   * @return array
   */
  public function asPtl() {
    return [
      'PLAYER_ID'    => $this->getPlayerId(),
      'PLAYER_NAME'  => $this->player->username,
      'ONLINE'       => $this->player->onlinetime,
      'ONLINE_SQL'   => date(FMT_DATE_TIME_SQL, $this->player->onlinetime),
      'VACATION'     => $this->player->vacation,
      'VACATION_SQL' => date(FMT_DATE_TIME_SQL, $this->player->vacation),
      'BAN'          => $this->player->banaday,
      'BAN_SQL'      => date(FMT_DATE_TIME_SQL, $this->player->banaday),
      'TITLE'        => $this->title->name,
      'TITLE_ID'     => $this->title->index,
      'RIGHTS'       => $this->title->rightsAsString(),
      'OWNER'        => $this->isOwner(),
    ];
  }

}
