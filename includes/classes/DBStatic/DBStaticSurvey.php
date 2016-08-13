<?php

namespace DBStatic;
use classSupernova;

class DBStaticSurvey {

  public static function db_survey_delete_by_id($announce_id) {
    classSupernova::$gc->db->doDeleteWhere(TABLE_SURVEY, array('survey_announce_id' => $announce_id));
  }

  public static function db_survey_insert($announce_id, $survey_question_unsafe, $survey_until) {
    classSupernova::$db->doInsertSet(TABLE_SURVEY, array(
      'survey_announce_id' => $announce_id,
      'survey_question'    => $survey_question_unsafe,
      'survey_until'       => $survey_until,
    ));
  }

}