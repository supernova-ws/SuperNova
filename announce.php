<?php

/**
 * announce.php
 *
 * @copyright (c) 2010-2016 Gorlum for http://supernova.ws
 */

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

global $config;

nws_mark_read($user);
$template = SnTemplate::gettemplate('announce', true);

$announce_id = sys_get_param_id('id');
$text = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url = sys_get_param_str('detail_url');
$mode = sys_get_param_str('mode');

$announce = array();
if ($user['authlevel'] >= 3) {
  if (!empty($text)) {
    $announce_time = strtotime($announce_time, SN_TIME_NOW);
    $announce_time = $announce_time ? $announce_time : SN_TIME_NOW;

    if ($mode == 'edit') {
      /** @noinspection SqlResolve */
      doquery("UPDATE `{{announce}}` SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$announce_id};");
      /** @noinspection SqlResolve */
      doquery("DELETE FROM `{{survey}}` WHERE `survey_announce_id` = {$announce_id};");
    } else {
      /** @noinspection SqlResolve */
      doquery("INSERT INTO `{{announce}}`
        SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}',
        `user_id` = {$user['id']}, `user_name` = '" . db_escape($user['username']) . "'");
      $announce_id = db_insert_id();
    }
    if (($survey_question = sys_get_param_str('survey_question')) && ($survey_answers = sys_get_param('survey_answers'))) {
      $survey_until = strtotime($survey_until = sys_get_param_str('survey_until'), SN_TIME_NOW);
      $survey_until = date(FMT_DATE_TIME_SQL, $survey_until ? $survey_until : SN_TIME_NOW + PERIOD_DAY * 1);
      /** @noinspection SqlResolve */
      doquery("INSERT INTO `{{survey}}` SET `survey_announce_id` = {$announce_id}, `survey_question` = '{$survey_question}', `survey_until` = '{$survey_until}'");
      $survey_id = db_insert_id();

      // To remove difference between Linux/Windows/OsX/etc browsers
      $survey_answers = nl2br($survey_answers);
      $survey_answers = explode('<br />', $survey_answers);
      foreach ($survey_answers as $survey_answer) {
        $survey_answer = db_escape(trim($survey_answer));
        /** @noinspection SqlResolve */
        $survey_answer ? doquery("INSERT INTO `{{survey_answers}}` SET `survey_parent_id` = {$survey_id}, `survey_answer_text` = '{$survey_answer}'") : false;
      }
    }

    if ($announce_time <= SN_TIME_NOW) {
      if ($announce_time > SN::$config->var_news_last && $announce_time == SN_TIME_NOW) {
        SN::$config->db_saveItem('var_news_last', $announce_time);
      }

      if (sys_get_param_int('news_mass_mail')) {
        $text = sys_get_param('text') . ($detail_url ? " <a href=\"{$detail_url}\"><span class=\"positive\">{$lang['news_more']}</span></a>" : '');
        msg_send_simple_message('*', 0, 0, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['news_title'], $text);
      }
    }

    $mode = '';
    $announce_id = 0;
  }

  $survey_answers = '';
  switch ($mode) {
    case 'del':
      /** @noinspection SqlResolve */
      doquery("DELETE FROM `{{announce}}` WHERE `idAnnounce` = {$announce_id} LIMIT 1;");
      $mode = '';
    break;

    /** @noinspection PhpMissingBreakStatementInspection */
    case 'edit':
      $template->assign_var('ID', $announce_id);
    case 'copy':
      /** @noinspection SqlResolve */
      $announce = doquery(
        "SELECT a.*, s.survey_id, s.survey_question, s.survey_until
        FROM `{{announce}}` AS a
        LEFT JOIN `{{survey}}` AS s ON s.survey_announce_id = a.idAnnounce
        WHERE `idAnnounce` = {$announce_id} LIMIT 1;", true);
      if ($announce['survey_id']) {
        /** @noinspection SqlResolve */
        $query = doquery("SELECT survey_answer_text FROM `{{survey_answers}}` WHERE survey_parent_id = {$announce['survey_id']};");
        $survey_answers_array = [];
        while ($row = db_fetch($query)) {
          $survey_answers_array[] = $row['survey_answer_text'];
        }
        $survey_answers = implode("\n", $survey_answers_array);
      }
    break;

    default:
      if ($announce_id) {
        $annQuery = "AND `idAnnounce` = {$announce_id} ";
      }
    break;
  }
} else {
  $annQuery = 'AND UNIX_TIMESTAMP(`tsTimeStamp`) <= ' . SN_TIME_NOW . ' ';

  if ($announce_id) {
    $annQuery .= "AND `idAnnounce` = {$announce_id} ";
  }
}

nws_render($user, $template, $annQuery, 20);

$template->assign_vars(array(
  'PAGE_HEADER'     => $lang['news_title'],
  'AUTHLEVEL'       => $user['authlevel'],
  'MODE'            => $mode,
  'ANNOUNCE_ID'     => $announce_id,
  'tsTimeStamp'     => $announce['tsTimeStamp'],
  'strAnnounce'     => $announce['strAnnounce'],
  'DETAIL_URL'      => $announce['detail_url'],
  'SURVEY_QUESTION' => $announce['survey_question'],
  'SURVEY_UNTIL'    => $announce['survey_until'],
  'SURVEY_ANSWERS'  => $survey_answers,

));

SnTemplate::display($template, $lang['news_title']);
