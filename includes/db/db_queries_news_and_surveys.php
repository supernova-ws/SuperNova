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

/**
 * @param $announce
 *
 * @return array|bool|mysqli_result|null
 */
function db_survey_answers_get_list_by_parent($announce) {
  $survey_query = doquery("SELECT * FROM {{survey_answers}} WHERE survey_parent_id  = {$announce['survey_id']} ORDER BY survey_answer_id;");

  return $survey_query;
}

/**
 * @param $announce
 *
 * @return array|bool|mysqli_result|null
 */
function db_survey_get_answer_texts($announce) {
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
 * @param $announce
 * @param $user
 *
 * @return array|bool|mysqli_result|null
 */
function db_survey_get_vote($announce, $user) {
  return $survey_vote = doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$announce['survey_id']} AND survey_vote_user_id = {$user['id']} LIMIT 1;", true);
}

/**
 * @param $template
 * @param $query_where
 * @param $query_limit
 *
 * @return array|bool|mysqli_result|null
 */
function db_news_list_get_by_query(&$template, $query_where, $query_limit) {
  $announce_list = doquery(
    "SELECT a.*, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time, u.authlevel, s.*
    FROM
      {{announce}} AS a
      LEFT JOIN {{survey}} AS s ON s.survey_announce_id = a.idAnnounce
      LEFT JOIN {{users}} AS u ON u.id = a.user_id
    {$query_where}
    ORDER BY `tsTimeStamp` DESC, idAnnounce" .
    ($query_limit ? " LIMIT {$query_limit}" : ''));

  $template->assign_var('NEWS_COUNT', db_num_rows($announce_list));

  return $announce_list;
}

/**
 * @param $user
 * @param $survey_id
 * @param $survey_vote_id
 * @param $user_name_safe
 */
function db_survey_vote_insert(&$user, $survey_id, $survey_vote_id, $user_name_safe) {
  doquery("INSERT INTO {{survey_votes}} SET `survey_parent_id` = {$survey_id}, `survey_parent_answer_id` = {$survey_vote_id}, `survey_vote_user_id` = {$user['id']}, `survey_vote_user_name` = '{$user_name_safe}';");
}

/**
 * @param $survey_id
 * @param $survey_vote_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_survey_answer_get($survey_id, $survey_vote_id) {
  $is_answer_exists = doquery("SELECT `survey_answer_id` FROM `{{survey_answers}}` WHERE survey_parent_id = {$survey_id} AND survey_answer_id = {$survey_vote_id};", true);

  return $is_answer_exists;
}

/**
 * @param $user
 * @param $survey_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_survey_vote_get(&$user, $survey_id) {
  $is_voted = doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$survey_id} AND survey_vote_user_id = {$user['id']} FOR UPDATE;", true);

  return $is_voted;
}
