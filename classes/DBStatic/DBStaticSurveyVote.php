<?php

namespace DBStatic;
use classSupernova;
use mysqli_result;

class DBStaticSurveyVote {

  /**
   * @param $announce
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_get_vote($announce, $user) {
    return classSupernova::$db->doSelectFetchArray("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$announce['survey_id']} AND survey_vote_user_id = {$user['id']} LIMIT 1;");
  }


  /**
   * @param $survey_id
   * @param $survey_vote_id
   * @param $userId
   * @param $user_name_unsafe
   */
  public static function db_survey_vote_insert($survey_id, $survey_vote_id, $userId, $user_name_unsafe) {
    classSupernova::$db->doInsertSet(TABLE_SURVEY_VOTES, array(
      'survey_parent_id'        => $survey_id,
      'survey_parent_answer_id' => $survey_vote_id,
      'survey_vote_user_id'     => $userId,
      'survey_vote_user_name'   => $user_name_unsafe,
    ));
  }


  /**
   * @param $user
   * @param $survey_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_vote_get(&$user, $survey_id) {
    $is_voted = classSupernova::$db->doSelectFetchArray("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$survey_id} AND survey_vote_user_id = {$user['id']} FOR UPDATE;");

    return $is_voted;
  }

}