<?php

class DBStaticSurvey {

  public static function db_survey_delete_by_id($announce_id) {
    classSupernova::$gc->db->doDeleteWhereSimple(TABLE_SURVEY, array('survey_announce_id' => $announce_id));
  }

  public static function db_survey_insert($announce_id, $survey_question, $survey_until) {
    classSupernova::$db->doInsert("INSERT INTO {{survey}} SET `survey_announce_id` = {$announce_id}, `survey_question` = '{$survey_question}', `survey_until` = '{$survey_until}'");
  }

}