<?php

// News & surveys ******************************************************************************************************
function db_news_update_set($announce_time, $text, $detail_url, $announce_id) {
  doquery("UPDATE {{announce}} SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$announce_id};");
}

function db_survey_delete_by_id($announce_id) {
  doquery("DELETE FROM {{survey}} WHERE `survey_announce_id` = {$announce_id};");
}

function db_news_insert_set($announce_time, $text, $detail_url, $user) {
  doquery("INSERT INTO {{announce}}
        SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}',
        `user_id` = {$user['id']}, `user_name` = '" . db_escape($user['username']) . "'");
}

function db_survey_insert($announce_id, $survey_question, $survey_until) {
  doquery("INSERT INTO {{survey}} SET `survey_announce_id` = {$announce_id}, `survey_question` = '{$survey_question}', `survey_until` = '{$survey_until}'");
}

function db_survey_answer_insert($survey_id, $survey_answer) {
  doquery("INSERT INTO {{survey_answers}} SET `survey_parent_id` = {$survey_id}, `survey_answer_text` = '{$survey_answer}'");
}

function db_news_delete_by_id($announce_id) {
  doquery("DELETE FROM {{announce}} WHERE `idAnnounce` = {$announce_id} LIMIT 1;");
}

function db_news_with_survey_select_by_id($announce_id) {
  return doquery(
    "SELECT a.*, s.survey_id, s.survey_question, s.survey_until
        FROM {{announce}} AS a
        LEFT JOIN {{survey}} AS s ON s.survey_announce_id = a.idAnnounce
        WHERE `idAnnounce` = {$announce_id} LIMIT 1;", true);
}

function db_survey_answer_text_select_by_news($announce) {
  return doquery("SELECT survey_answer_text FROM {{survey_answers}} WHERE survey_parent_id = {$announce['survey_id']};");
}
