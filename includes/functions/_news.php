<?php

use DBAL\DbSqlPaging;
use General\Helpers\PagingRenderer;

/**
 * @param template $template
 * @param string   $query_where
 * @param int      $query_limit
 */
function nws_render(&$user, &$template, $query_where = '', $query_limit = 20) {
  $mmModuleIsActive = !empty(SN::$gc->modules->getModulesInGroup('payment'));

  $sqlText = "SELECT a.*, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time, u.authlevel, s.*
    FROM
      `{{announce}}` AS a
      LEFT JOIN `{{survey}}` AS s ON s.survey_announce_id = a.idAnnounce
      LEFT JOIN `{{users}}` AS u ON u.id = a.user_id
    WHERE 1 {$query_where}
    ORDER BY `tsTimeStamp` DESC, idAnnounce";

  $announce_list = new DbSqlPaging($sqlText, $query_limit, sys_get_param_int(PagingRenderer::KEYWORD));
  $pager = new PagingRenderer($announce_list, 'announce.php');

  $users = array();
  foreach ($announce_list as $announce) {
    if ($announce['user_id'] && !isset($users[$announce['user_id']])) {
      $users[$announce['user_id']] = db_user_by_id($announce['user_id']);
    }

    $survey_vote = array('survey_vote_id' => 1);
    $survey_complete = strtotime($announce['survey_until']) < SN_TIME_NOW;

    if ($announce['survey_id'] && !empty($user['id'])) {
      $survey_vote = !$survey_complete ? $survey_vote = doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$announce['survey_id']} AND survey_vote_user_id = {$user['id']} LIMIT 1;", true) : array();
    }

    $announce_exploded = explode("<br /><br />", SN::$gc->bbCodeParser->expandBbCode($announce['strAnnounce'], intval($announce['authlevel'])));

    $template->assign_block_vars('announces', array(
      'ID'              => $announce['idAnnounce'],
      'TIME'            => date(FMT_DATE_TIME, $announce['unix_time'] + SN_CLIENT_TIME_DIFF),
      'ANNOUNCE'        => SN::$gc->bbCodeParser->expandBbCode($announce['strAnnounce'], intval($announce['authlevel'])),
      'DETAIL_URL'      => $announce['detail_url'],
      'USER_NAME'       => !empty($users[$announce['user_id']])
        ? player_nick_render_to_html($users[$announce['user_id']], array('color' => true))
        : js_safe_string($announce['user_name']),
      'NEW'             => $announce['unix_time'] + SN::$config->game_news_actual >= SN_TIME_NOW,
      'FUTURE'          => $announce['unix_time'] > SN_TIME_NOW,
      'SURVEY_ID'       => $announce['survey_id'],
      'SURVEY_TEXT'     => $announce['survey_question'],
      'SURVEY_CAN_VOTE' => empty($survey_vote) && !$survey_complete,
      'SURVEY_COMPLETE' => $survey_complete,
      'SURVEY_UNTIL'    => $announce['survey_until'],
    ));

    foreach ($announce_exploded as $announce_paragraph) {
      $template->assign_block_vars('announces.paragraph', array(
        'TEXT' => $announce_paragraph,
      ));
    }

    if ($announce['survey_id']) {
      $survey_query = doquery(
        "SELECT survey_answer_id AS `ID`, survey_answer_text AS `TEXT`, count(DISTINCT survey_vote_id) AS `VOTES`
          FROM `{{survey_answers}}` AS sa
            LEFT JOIN `{{survey_votes}}` AS sv ON sv.survey_parent_answer_id = sa.survey_answer_id
          WHERE sa.survey_parent_id = {$announce['survey_id']}
          GROUP BY survey_answer_id
          ORDER BY survey_answer_id;"
      );
      $survey_vote_result = array();
      $total_votes = 0;
      $total_mm = 0;
      $total_money = 0;
      while ($row = db_fetch($survey_query)) {
        $survey_vote_result[$row['ID']] = $row;
        $total_votes += $row['VOTES'];
      }

      if ($mmModuleIsActive && $user['authlevel'] >= AUTH_LEVEL_ADMINISTRATOR) {
        $mQuery = doquery(
          "SELECT
            sa.survey_answer_id,
            sum(acc.account_metamatter_total) AS `mm`,
            (
              SELECT sum(payment_amount)
              FROM `{{payment}}` AS pay
              WHERE payment_currency = 'USD' AND pay.payment_user_id = sv.survey_vote_user_id
              GROUP BY payment_user_id, payment_currency
            )                                 AS `money`
          FROM `{{survey_votes}}` AS sv
            LEFT JOIN `{{survey_answers}}` AS sa ON sa.survey_answer_id = sv.survey_parent_answer_id
            LEFT JOIN `{{account_translate}}` AS act ON act.user_id = sv.survey_vote_user_id
            LEFT JOIN `{{account}}` AS acc ON acc.account_id = act.provider_account_id
          WHERE sv.survey_parent_id = {$announce['survey_id']}
          GROUP BY sv.survey_parent_id, sv.survey_parent_answer_id;"
        );
        while ($row = db_fetch($mQuery)) {
          $survey_vote_result[$row['survey_answer_id']] += [
            'MM'    => $row['mm'],
            'MONEY' => $row['money'],
          ];
          $total_mm += $row['mm'];
          $total_money += $row['money'];
        }
      }

      // Show result
      foreach ($survey_vote_result as &$vote_result) {
        $vote_percent = $total_votes ? $vote_result['VOTES'] / $total_votes * 100 : 0;
        $vote_result['PERCENT'] = $vote_percent;
        $vote_result['PERCENT_TEXT'] = round($vote_percent, 1);
        $vote_result['VOTES'] = HelperString::numberFloorAndFormat($vote_result['VOTES']);

        if ($mmModuleIsActive && $user['authlevel'] >= AUTH_LEVEL_ADMINISTRATOR) {
          $vote_result['PERCENT_MM'] = $total_mm ? $vote_result['MM'] / $total_mm * 100 : 0;
          $vote_result['PERCENT_MONEY'] = $total_money ? $vote_result['MONEY'] / $total_money * 100 : 0;
        }

        $template->assign_block_vars('announces.survey_answers', $vote_result);
      }
      // Dirty hack
      $template->assign_block_vars('announces.total_votes', array(
        'TOTAL_VOTES' => $total_votes,
        'TOTAL_MM'    => $total_mm,
        'TOTAL_MONEY' => $total_money,
      ));
    }
  }

  $template->assign_vars([
    'PAGER_MESSAGES' => $pager ? $pager->render() : '',
    'NEWS_COUNT'     => HelperString::numberFloorAndFormat($announce_list->getTotalRecords()),

    'MM_MODULE_ACTIVE' => $mmModuleIsActive,
  ]);
}

function nws_mark_read(&$user) {
  if (!empty($user['id'])) {
    db_user_set_by_id($user['id'], '`news_lastread` = ' . SN_TIME_NOW);
    $user['news_lastread'] = SN_TIME_NOW;
  }

  return true;
}

function survey_vote(&$user) {
  if (empty($user['id'])) {
    return true;
  }

  sn_db_transaction_start();
  $survey_id = sys_get_param_id('survey_id');
  $is_voted = doquery("SELECT `survey_vote_id` FROM `{{survey_votes}}` WHERE survey_parent_id = {$survey_id} AND survey_vote_user_id = {$user['id']} FOR UPDATE;", true);
  if (empty($is_voted)) {
    $survey_vote_id = sys_get_param_id('survey_vote');
    $is_answer_exists = doquery("SELECT `survey_answer_id` FROM `{{survey_answers}}` WHERE survey_parent_id = {$survey_id} AND survey_answer_id = {$survey_vote_id};", true);
    if (!empty($is_answer_exists)) {
      $user_name_safe = db_escape($user['username']);
      doquery("INSERT INTO `{{survey_votes}}` SET `survey_parent_id` = {$survey_id}, `survey_parent_answer_id` = {$survey_vote_id}, `survey_vote_user_id` = {$user['id']}, `survey_vote_user_name` = '{$user_name_safe}';");
    }
  }
  sn_db_transaction_commit();

  return true;
}
