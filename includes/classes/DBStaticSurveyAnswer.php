<?php

class DBStaticSurveyAnswer {

  public static function db_survey_answer_insert($survey_id, $survey_answer) {
    doquery("INSERT INTO {{survey_answers}} SET `survey_parent_id` = {$survey_id}, `survey_answer_text` = '{$survey_answer}'");
  }

  public static function db_survey_answer_text_select_by_news($announce) {
    return doquery("SELECT survey_answer_text FROM {{survey_answers}} WHERE survey_parent_id = {$announce['survey_id']};");
  }

  /**
   * @param $announce
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_answers_get_list_by_parent($announce) {
    $survey_query = doquery("SELECT * FROM {{survey_answers}} WHERE survey_parent_id  = {$announce['survey_id']} ORDER BY survey_answer_id;");

    return $survey_query;
  }

  /**
   * @param $announce
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_get_answer_texts($announce) {
    $survey_query = doquery(
      "SELECT survey_answer_text AS `TEXT`, count(DISTINCT survey_vote_id) AS `VOTES`
          FROM `{{survey_answers}}` AS sa
            LEFT JOIN `{{survey_votes}}` AS sv ON sv.survey_parent_answer_id = sa.survey_answer_id
          WHERE sa.survey_parent_id = {$announce['survey_id']}
          GROUP BY survey_answer_id
          ORDER BY survey_answer_id;"
    );

    return $survey_query;
  }

  /**
   * @param $survey_id
   * @param $survey_vote_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_survey_answer_get($survey_id, $survey_vote_id) {
    $is_answer_exists = doquery("SELECT `survey_answer_id` FROM `{{survey_answers}}` WHERE survey_parent_id = {$survey_id} AND survey_answer_id = {$survey_vote_id};", true);

    return $is_answer_exists;
  }

}