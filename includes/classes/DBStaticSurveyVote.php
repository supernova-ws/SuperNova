<?php

class DBStaticSurveyVote {

  /**
   * @param $announce
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_get_vote($announce, $user) {
    return doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$announce['survey_id']} AND survey_vote_user_id = {$user['id']} LIMIT 1;", true);
  }


  /**
   * @param $user
   * @param $survey_id
   * @param $survey_vote_id
   * @param $user_name_safe
   */
  public static function db_survey_vote_insert(&$user, $survey_id, $survey_vote_id, $user_name_safe) {
    doquery("INSERT INTO {{survey_votes}} SET `survey_parent_id` = {$survey_id}, `survey_parent_answer_id` = {$survey_vote_id}, `survey_vote_user_id` = {$user['id']}, `survey_vote_user_name` = '{$user_name_safe}';");
  }


  /**
   * @param $user
   * @param $survey_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_vote_get(&$user, $survey_id) {
    $is_voted = doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$survey_id} AND survey_vote_user_id = {$user['id']} FOR UPDATE;", true);

    return $is_voted;
  }

}