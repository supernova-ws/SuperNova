<?php
/**
 * Created by Gorlum 28.11.2017 6:01
 */

namespace Alliance;

use Common\GlobalContainer;
use \Exception;
use \HelperString;

/**
 * Class Alliance
 *
 * Implements Alliance entity
 *
 * @package Alliance
 */
class Alliance extends RecordAlliance {
  const OWNER_INDEX = -1;
  const DEFAULT_INDEX = 0;
  const RIGHTS_ALL = [
    0 => 'name',
    1 => 'mail',
    2 => 'online',
    3 => 'invite',
    4 => 'kick',
    5 => 'admin',
    6 => 'forum',
    7 => 'diplomacy'
  ];
  const RIGHT_WEIGHTS = [
    'mail'      => 3,
    'online'    => 4,
    'invite'    => 1,
    'kick'      => 10,
    'admin'     => 99,
    'forum'     => 0,
    'diplomacy' => 5,
  ];


  /**
   * @var AllianceTitleList $titles
   */
  protected $titles;
  /**
   * @var AllianceMemberList $members
   */
  protected $members;

  /**
   * Alliance constructor.
   *
   * @param GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);
  }

  /**
   * @param array $properties
   */
  protected function fromProperties(array $properties) {
    parent::fromProperties($properties);

    $this->titles = new AllianceTitleList($this);
  }

  /**
   * @return AllianceMemberList
   */
  public function getMemberList() {
    if (!isset($this->members)) {
      $this->members = new AllianceMemberList(static::$db, $this);
    }

    return $this->members;
  }

  /**
   * List of titles
   *
   * @return AllianceTitleList
   */
  public function getTitleList() {
    return $this->titles;
  }

  /**
   * Pass alliance to a member
   *
   * @param AllianceMember $newOwnerMember
   *
   * @return bool
   * @throws Exception
   */
  public function pass(AllianceMember $newOwnerMember) {
    try {
      sn_db_transaction_start();

      if ($newOwnerMember->isOwner()) {
        throw new Exception('{ Указанный пользователь уже является владельцем указанного Альянса }', ERR_NOTICE);
      }

      if (!empty($oldOwnerMember = $this->members->getOwner())) {
        if (!$oldOwnerMember->changeTitle($this->titles->getTitle(static::DEFAULT_INDEX))) {
          throw new Exception('{ Ошибка изменения ранга у старого владельца }', ERR_ERROR);
        }
      }

      if (!$newOwnerMember->changeTitle($this->titles->getTitle(static::OWNER_INDEX))) {
        throw new Exception('{ Ошибка изменения ранга у нового владельца }', ERR_ERROR);
      }

      $this->ownerId = $newOwnerMember->getPlayerId();
      if (!$this->update()) {
        throw new Exception('{ Ошибка изменения владельца Альянса }', ERR_ERROR);
      }

      sn_db_transaction_commit();
    } catch (Exception $e) {
      sn_db_transaction_rollback();

      throw $e;
    }

    return true;
  }

  /**
   * @return array
   */
  public function asPtl() {
    $ownerName = $this->getMemberList()->getOwner() instanceof AllianceMember ? $this->getMemberList()->getOwner()->getMemberName() : '';

    return
      $this->ptlArray()
      + [
        '.'                => [
          'title' => $this->titles->asPtl()
        ],
        'OWNER_NAME_SAFE'  => HelperString::htmlSafe($ownerName),
        'CREATED_TEXT'     => date(FMT_DATE_TIME_SQL, $this->createdUnixTime),
        'STAT_POINTS_TEXT' => HelperString::numberFloorAndFormat($this->statPoints),
      ];
  }

}
