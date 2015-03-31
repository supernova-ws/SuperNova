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

global $config;

nws_mark_read($user);
$template     = gettemplate('announce', true);

$announce_id   = sys_get_param_id('id');
$text          = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url    = sys_get_param_str('detail_url');
$mode          = sys_get_param_str('mode');

$announce = array();
if ($user['authlevel'] >= 3) {
  if (!empty($text)) {
    // $idAnnounce = sys_get_param_id('id');
    $announce_time = strtotime($announce_time, SN_TIME_NOW);
    $announce_time = $announce_time ? $announce_time : SN_TIME_NOW;

    if ($mode == 'edit') {
      doquery("UPDATE {{announce}} SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$announce_id};");
      doquery("DELETE FROM {{survey}} WHERE `survey_announce_id` = {$announce_id};");
    } else {
      doquery("INSERT INTO {{announce}}
        SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}',
        `user_id` = {$user['id']}, `user_name` = '" . mysql_real_escape_string($user['username']) . "'");
      $announce_id = mysql_insert_id();
    }
    if(($survey_question = sys_get_param_str('survey_question')) && ($survey_answers = sys_get_param_str('survey_answers'))) {
      $survey_answers = explode('\r\n', $survey_answers);
      if(count($survey_answers) > 1) {
        $survey_until = strtotime($survey_until = sys_get_param_str('survey_until'), SN_TIME_NOW);
        $survey_until = date(FMT_DATE_TIME_SQL, $survey_until ? $survey_until : SN_TIME_NOW + PERIOD_DAY * 1);
        doquery("INSERT INTO {{survey}} SET `survey_announce_id` = {$announce_id}, `survey_question` = '{$survey_question}', `survey_until` = '{$survey_until}'");
        $survey_id = mysql_insert_id();
        foreach($survey_answers as $survey_answer) {
          $survey_answer = mysql_real_escape_string(trim($survey_answer));
          $survey_answer ? doquery("INSERT INTO {{survey_answers}} SET `survey_parent_id` = {$survey_id}, `survey_answer_text` = '{$survey_answer}'") : false;
        }
      }
    }

    if($announce_time <= SN_TIME_NOW) {
      if($announce_time > $config->var_news_last && $announce_time == SN_TIME_NOW) {
        $config->db_saveItem('var_news_last', $announce_time);
      }

      if(sys_get_param_int('news_mass_mail')) {
        $text = sys_get_param('text') . ($detail_url ? " <a href=\"{$detail_url}\"><span class=\"positive\">{$lang['news_more']}</span></a>" : '');
        msg_send_simple_message('*', 0, 0, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['news_title'], $text);
      }
    }

    $mode = '';
    $announce_id = 0;
  }

  $survey_answers = '';
  switch($mode) {
    case 'del':
      doquery( "DELETE FROM {{announce}} WHERE `idAnnounce` = {$announce_id} LIMIT 1;");
      $mode = '';
    break;

    case 'edit':
      $template->assign_var('ID', $announce_id);
    case 'copy':
      $announce = doquery(
        "SELECT a.*, s.survey_id, s.survey_question, s.survey_until
        FROM {{announce}} AS a
        LEFT JOIN {{survey}} AS s ON s.survey_announce_id = a.idAnnounce
        WHERE `idAnnounce` = {$announce_id} LIMIT 1;", true);
      if($announce['survey_id']) {
        $query = doquery("SELECT survey_answer_text FROM {{survey_answers}} WHERE survey_parent_id = {$announce['survey_id']};");
        while($row = mysql_fetch_assoc($query)) {
          $survey_answers[] = $row['survey_answer_text'];
        }
        $survey_answers = implode("\r\n", $survey_answers);
      }
    break;
  }
} else {
  $annQuery = 'WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<=' . intval($time_now);
}

nws_render($template, $annQuery, 20);

$template->assign_vars(array(
  'AUTHLEVEL'       => $user['authlevel'],
//  'total'           => mysql_num_rows($allAnnounces),
  'MODE'            => $mode,
  'tsTimeStamp'     => $announce['tsTimeStamp'],
  'strAnnounce'     => $announce['strAnnounce'],
  'DETAIL_URL'      => $announce['detail_url'],
  'SURVEY_QUESTION' => $announce['survey_question'],
  'SURVEY_UNTIL' => $announce['survey_until'],
  'SURVEY_ANSWERS'  => $survey_answers,
  'time_now'        => SN_TIME_NOW,
));

display($template, $lang['news_title']);
