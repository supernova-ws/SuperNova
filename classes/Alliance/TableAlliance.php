<?php
/**
 * Created by Gorlum 15.06.2017 10:37
 */

namespace Alliance;


use DBAL\ActiveRecordStatic;

/**
 * Class TableAlliance
 *
 * @package Alliance
 *
 * @property string     $tag
 * @property string     $name
 * @property int        $createdUnixTime
 * @property int|string $ownerId
 * @property string     $ownerRankName
 * @property string     $description
 * @property string     $webPageURL
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
class TableAlliance extends ActiveRecordStatic {

  protected function fillFields($array) {
    $this->id = $array['id'];
    $this->tag = $array['ally_tag'];
    $this->name = $array['ally_name'];
    $this->createdUnixTime = $array['ally_register_time'];
    $this->ownerId = $array['ally_owner'];
    $this->ownerRankName = $array['ally_owner_range'];
    $this->description = $array['ally_description'];
    $this->webPageURL = $array['ally_web'];
    $this->textInternal = $array['ally_text'];
    $this->isLogoPresent = $array['ally_image'];
    $this->requestTemplate = $array['ally_request'];
    $this->isRequestsDisabled = $array['ally_request_notallow'];
    $this->membersCount = $array['ally_members'];
    $this->statPosition = $array['total_rank'];
    $this->statPoints = $array['total_points'];
    $this->titleList = $array['ranklist'];
    $this->oldTitleList = $array['ally_ranks'];
    $this->parentPlayerRecordId = $array['ally_user_id'];
  }

  public function getValuesArray() {
    return [
      'id'                    => $this->id,
      'ally_tag'              => $this->tag,
      'ally_name'             => $this->name,
      'ally_register_time'    => $this->createdUnixTime,
      'ally_owner'            => $this->ownerId,
      'ally_owner_range'      => $this->ownerRankName,
      'ally_description'      => $this->description,
      'ally_web'              => $this->webPageURL,
      'ally_text'             => $this->textInternal,
      'ally_image'            => $this->isLogoPresent,
      'ally_request'          => $this->requestTemplate,
      'ally_request_notallow' => $this->isRequestsDisabled,
      'ally_members'          => $this->membersCount,
      'total_rank'            => $this->statPosition,
      'total_points'          => $this->statPoints,
      'ranklist'              => $this->titleList,
      'ally_ranks'            => $this->oldTitleList,
      'ally_user_id'          => $this->parentPlayerRecordId,
    ];
  }

  /**
   * @param array $array
   *
   * @return array
   */
  public function ptlFromObject() {
    return [
      'ID'                   => $this->id,
      'TAG'                  => $this->tag,
      'NAME'                 => $this->name,
      'CREATED'              => $this->createdUnixTime,
      'OWNER_ID'             => $this->ownerId,
      'OWNER_RANK_NAME'      => $this->ownerRankName,
      'DESCRIPTION'          => $this->description,
      'WEB_PAGE'             => $this->webPageURL,
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

  /**
   * @param array $array
   *
   * @return array
   */
  public static function ptlArray(array $array) {
    if (!is_array($array) || empty($array)) {
      return [];
    }


    return [
      'ID'                   => $array['id'],
      'TAG'                  => $array['ally_tag'],
      'NAME'                 => $array['ally_name'],
      'CREATED'              => $array['ally_register_time'],
      'OWNER_ID'             => $array['ally_owner'],
      'OWNER_RANK_NAME'      => $array['ally_owner_range'],
      'DESCRIPTION'          => $array['ally_description'],
      'WEB_PAGE'             => $array['ally_web'],
      'TEXT_INTERNAL'        => $array['ally_text'],
      'HAVE_LOGO'            => $array['ally_image'],
      'REQUEST_TEMPLATE'     => $array['ally_request'],
      'REQUESTS_NOT_ALLOWED' => $array['ally_request_notallow'],
      'MEMBER_COUNT'         => $array['ally_members'],
      'STAT_POSITION'        => $array['total_rank'],
      'STAT_POINTS'          => $array['total_points'],
      'TITLE_LIST_UNPARSED'  => $array['ranklist'],
      'OLD_TITLE_LIST'       => $array['ally_ranks'],
      'PARENT_PLAYER_ID'     => $array['ally_user_id'],
    ];
  }

}
