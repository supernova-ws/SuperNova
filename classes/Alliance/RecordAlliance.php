<?php
/**
 * Created by Gorlum 15.06.2017 10:37
 */

namespace Alliance;


use DBAL\ActiveRecord;

/**
 * Class RecordAlliance
 *
 * @package Alliance
 *
 * @property string     $tag
 * @property string     $name
 * @property int        $createdUnixTime
 * @property int|string $ownerId
 * @property string     $ownerRankName
 * @property string     $description
 * @property string     $webPageUrl
 * @property string     $textInternal
 * @property int        $isLogoPresent
 * @property string     $requestTemplate
 * @property int        $isRequestsDisabled
 * @property int        $membersCount
 * @property float      $statPosition
 * @property float      $statPoints
 * @property string     $titleList
 * @property string     $oldTitleList
 * @property int|string $parentPlayerRecordId
 */
class RecordAlliance extends ActiveRecord {

  protected static $_fieldsToProperties = [
    'ally_tag'              => 'tag',
    'ally_name'             => 'name',
    'ally_register_time'    => 'createdUnixTime',
    'ally_owner'            => 'ownerId',
    'ally_owner_range'      => 'ownerRankName',
    'ally_description'      => 'description',
    'ally_web'              => 'webPageUrl',
    'ally_text'             => 'textInternal',
    'ally_image'            => 'isLogoPresent',
    'ally_request'          => 'requestTemplate',
    'ally_request_notallow' => 'isRequestsDisabled',
    'ally_members'          => 'membersCount',
    'total_rank'            => 'statPosition',
    'total_points'          => 'statPoints',
    'ranklist'              => 'titleList',
    'ally_ranks'            => 'oldTitleList',
    'ally_user_id'          => 'parentPlayerRecordId',
  ];


  /**
   * @return array
   */
  public function ptlArray() {
    return [
      'ID'                   => $this->id,
      'TAG'                  => $this->tag,
      'NAME'                 => $this->name,
      'CREATED'              => $this->createdUnixTime,
      'OWNER_ID'             => $this->ownerId,
      'OWNER_RANK_NAME'      => $this->ownerRankName,
      'DESCRIPTION'          => $this->description,
      'WEB_PAGE'             => $this->webPageUrl,
      'TEXT_INTERNAL'        => $this->textInternal,
      'HAVE_LOGO'            => $this->isLogoPresent,
      'REQUEST_TEMPLATE'     => $this->requestTemplate,
      'REQUESTS_NOT_ALLOWED' => $this->isRequestsDisabled,
      'MEMBER_COUNT'         => $this->membersCount,
      'STAT_POSITION'        => $this->statPosition,
      'STAT_POINTS'          => $this->statPoints,
      'TITLE_LIST_UNPARSED'  => $this->titleList,
      'OLD_TITLE_LIST'       => $this->oldTitleList,
      'PARENT_PLAYER_ID'     => $this->parentPlayerRecordId,
    ];
  }

}
