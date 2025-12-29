<?php
/**
 * Created by Gorlum 28.11.2017 11:16
 */

namespace Alliance;


use DBAL\db_mysql;
use Player\RecordPlayer;

class AllianceMemberList {
  /**
   * @var Alliance $alliance
   */
  protected $alliance;
  /**
   * @var \DBAL\db_mysql $db
   */
  protected $db;

  /**
   * @var AllianceMember[] $members
   */
  protected $members;


  /**
   * AllianceMemberList constructor.
   *
   * @param db_mysql $db
   * @param Alliance $alliance
   */
  public function __construct(db_mysql $db, Alliance $alliance) {
    $this->alliance = $alliance;
    $this->db = $db;

    foreach (RecordPlayer::findAll(['ally_id' => $this->alliance->id]) as $member) {
      $this->members[] = new AllianceMember($this->alliance, $member);
    }
  }

  /**
   * Get ally's owner
   *
   * @return AllianceMember|null
   */
  public function getOwner() {
    foreach ($this->members as $member) {
      if ($member->isOwner()) {
        return $member;
      }
    }

    return null;
  }

  /**
   * @return array
   */
  public function asPtl() {
    $result = [];
    if ($this->isEmpty()) {
      return $result;
    }

    foreach ($this->members as $member) {
      $result[] = $member->asPtl();
    }

    return $result;
  }

  /**
   * @return bool
   */
  public function isEmpty() {
    return empty($this->members);
  }

  /**
   * @param int|float|string $id
   *
   * @return AllianceMember|null
   */
  public function getById($id) {
    foreach ($this->members as $member) {
      if ($member->getPlayerId() == $id) {
        return $member;
      }
    }

    return null;
  }

}
