<?php

/**
 * @param template $template
 * @param string   $query_where
 * @param int      $query_limit
 */
function nws_render(&$template, $query_where = '', $query_limit = 20) {
  global $config, $user;

  $announce_list = db_news_list_get_by_query($template, $query_where, $query_limit);

  $users = array();
  while($announce = db_fetch($announce_list)) {
    if($announce['user_id'] && !isset($users[$announce['user_id']])) {
      $users[$announce['user_id']] = db_user_by_id($announce['user_id']);
    }

    $survey_vote = array('survey_vote_id' => 1);
    $survey_complete = strtotime($announce['survey_until']) < SN_TIME_NOW;

    if($announce['survey_id'] && !empty($user['id'])) {
      $survey_vote = !$survey_complete ? db_survey_get_vote($announce, $user) : array();
    }

    $announce_exploded = explode("<br /><br />", cht_message_parse($announce['strAnnounce'], false, intval($announce['authlevel'])));

    $template->assign_block_vars('announces', array(
      'ID'              => $announce['idAnnounce'],
      'TIME'            => date(FMT_DATE_TIME, $announce['unix_time'] + SN_CLIENT_TIME_DIFF),
      'ANNOUNCE'        => cht_message_parse($announce['strAnnounce'], false, intval($announce['authlevel'])),
      'DETAIL_URL'      => $announce['detail_url'],
      'USER_NAME'       =>
        isset($users[$announce['user_id']]) && $users[$announce['user_id']] ? player_nick_render_to_html($users[$announce['user_id']], array('color' => true)) :
          js_safe_string($announce['user_name']),
      'NEW'             => $announce['unix_time'] + classSupernova::$config->game_news_actual >= SN_TIME_NOW,
      'FUTURE'          => $announce['unix_time'] > SN_TIME_NOW,
      'SURVEY_ID'       => $announce['survey_id'],
      'SURVEY_TEXT'     => $announce['survey_question'],
      'SURVEY_CAN_VOTE' => empty($survey_vote) && !$survey_complete,
      'SURVEY_COMPLETE' => $survey_complete,
      'SURVEY_UNTIL'    => $announce['survey_until'],
    ));

    foreach($announce_exploded as $announce_paragraph) {
      $template->assign_block_vars('announces.paragraph', array(
        'TEXT' => $announce_paragraph,
      ));
    }

    if($announce['survey_id']) {
      $survey_query = db_survey_get_answer_texts($announce);
      $survey_vote_result = array();
      $total_votes = 0;
      while($row = db_fetch($survey_query)) {
        $survey_vote_result[] = $row;
        $total_votes += $row['VOTES'];
      }

      if(empty($survey_vote) && !$survey_complete) {
        // Can vote
        $survey_query = db_survey_answers_get_list_by_parent($announce);
        while($row = db_fetch($survey_query)) {
          $template->assign_block_vars('announces.survey_answers', array(
            'ID'   => $row['survey_answer_id'],
            'TEXT' => $row['survey_answer_text'],
          ));
        }
      } else {
        // Show result
        foreach($survey_vote_result as &$vote_result) {
          $vote_percent = $total_votes ? $vote_result['VOTES'] / $total_votes * 100 : 0;
          $vote_result['PERCENT'] = $vote_percent;
          $vote_result['PERCENT_TEXT'] = round($vote_percent, 1);
          $vote_result['VOTES'] = pretty_number($vote_result['VOTES']);
          $template->assign_block_vars('announces.survey_votes', $vote_result);
        }
      }
      // Dirty hack
      $template->assign_block_vars('announces.total_votes', array(
        'TOTAL_VOTES' => $total_votes,
      ));
    }
  }
}

function nws_mark_read(&$user) {
  if(isset($user['id'])) {
    db_user_set_by_id($user['id'], '`news_lastread` = ' . SN_TIME_NOW);
    $user['news_lastread'] = SN_TIME_NOW;
  }

  return true;
}

function survey_vote(&$user) {
  if(empty($user['id'])) {
    return true;
  }

  sn_db_transaction_start();
  $survey_id = sys_get_param_id('survey_id');
  $is_voted = db_survey_vote_get($user, $survey_id);
  if(empty($is_voted)) {
    $survey_vote_id = sys_get_param_id('survey_vote');
    $is_answer_exists = db_survey_answer_get($survey_id, $survey_vote_id);
    if(!empty($is_answer_exists)) {
      $user_name_safe = db_escape($user['username']);
      db_survey_vote_insert($user, $survey_id, $survey_vote_id, $user_name_safe);
    }
  }
  sn_db_transaction_commit();

  return true;
}
