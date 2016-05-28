<?php

/**
 * announce.php
 *
 * @v4 Security checks by Gorlum for http://supernova.ws
 * @v2 (c) copyright 2010 by Gorlum for http://supernova.ws
 * based on admin/activeplanet.php (c) 2008 for XNova
 */

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

nws_mark_read($user);
$template = gettemplate('announce', true);

$announce_id = sys_get_param_id('id');
$text = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url = sys_get_param_str('detail_url');
$mode = sys_get_param_str('mode');

$announce = array();
if($user['authlevel'] >= 3) {
  if(!empty($text)) {
    $announce_time = strtotime($announce_time, SN_TIME_NOW);
    $announce_time = $announce_time ? $announce_time : SN_TIME_NOW;

    if($mode == 'edit') {
      DBStaticNews::db_news_update_set($announce_time, $text, $detail_url, $announce_id);
      DBStaticSurvey::db_survey_delete_by_id($announce_id);
    } else {
      DBStaticNews::db_news_insert_set($announce_time, $text, $detail_url, $user);
      $announce_id = db_insert_id();
    }
    if(($survey_question = sys_get_param_str('survey_question')) && ($survey_answers = sys_get_param('survey_answers'))) {
      $survey_answers = explode("\r\n", $survey_answers);
      $survey_until = strtotime($survey_until = sys_get_param_str('survey_until'), SN_TIME_NOW);
      $survey_until = date(FMT_DATE_TIME_SQL, $survey_until ? $survey_until : SN_TIME_NOW + PERIOD_DAY * 1);
      DBStaticSurvey::db_survey_insert($announce_id, $survey_question, $survey_until);
      $survey_id = db_insert_id();
      foreach($survey_answers as $survey_answer) {
        $survey_answer = db_escape(trim($survey_answer));
        if(empty($survey_answer)) {
          continue;
        }
        DBStaticSurveyAnswer::db_survey_answer_insert($survey_id, $survey_answer);
      }
    }

    if($announce_time <= SN_TIME_NOW) {
      if($announce_time > classSupernova::$config->var_news_last && $announce_time == SN_TIME_NOW) {
        classSupernova::$config->db_saveItem('var_news_last', $announce_time);
      }

      if(sys_get_param_int('news_mass_mail')) {
        $lang_news_more = classLocale::$lang['news_more'];
        $text = sys_get_param('text') . ($detail_url ? " <a href=\"{$detail_url}\"><span class=\"positive\">{$lang_news_more}</span></a>" : '');
        DBStaticMessages::msgSendFromAdmin('*', classLocale::$lang['news_title'], $text);
      }
    }

    $mode = '';
    $announce_id = 0;
  }

  $survey_answers = '';
  switch($mode) {
    case 'del':
      DBStaticNews::db_news_delete_by_id($announce_id);
      $mode = '';
    break;

    /** @noinspection PhpMissingBreakStatementInspection */
    case 'edit':
      $template->assign_var('ID', $announce_id);
    case 'copy':
      $announce = DBStaticNews::db_news_with_survey_select_by_id($announce_id);
      if($announce['survey_id']) {
        $query = DBStaticSurveyAnswer::db_survey_answer_text_select_by_news($announce);
        while($row = db_fetch($query)) {
          $survey_answers[] = $row['survey_answer_text'];
        }
        $survey_answers = implode("\r\n", $survey_answers);
      }
    break;
  }
} else {
  $annQuery = 'WHERE UNIX_TIMESTAMP(`tsTimeStamp`) <= ' . SN_TIME_NOW;
}

nws_render($template, $annQuery, 20);

$template->assign_vars(array(
  'AUTHLEVEL'       => $user['authlevel'],
//  'total'           => db_num_rows($allAnnounces),
  'MODE'            => $mode,
  'tsTimeStamp'     => $announce['tsTimeStamp'],
  'strAnnounce'     => $announce['strAnnounce'],
  'DETAIL_URL'      => $announce['detail_url'],
  'SURVEY_QUESTION' => $announce['survey_question'],
  'SURVEY_UNTIL'    => $announce['survey_until'],
  'SURVEY_ANSWERS'  => $survey_answers,
));

display($template, classLocale::$lang['news_title']);
