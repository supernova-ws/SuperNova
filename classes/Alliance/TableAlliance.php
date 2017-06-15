<?php
/**
 * Created by Gorlum 15.06.2017 10:37
 */

namespace Alliance;


use DBAL\ActiveRecordStatic;

class TableAlliance extends ActiveRecordStatic {

  /**
   * @param array $array
   *
   * @return array
   */
  public static function ptlArray(array $array) {
    if(!is_array($array) || empty($array)) {
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
