<?php

function nws_render(&$template, $query_where = '', $query_limit = 20)
{
  global $config;

  $announce_list = doquery(
    "SELECT a.*, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time, u.authlevel
    FROM
      {{announce}} AS a
      LEFT JOIN {{users}} AS u ON u.id = a.user_id
    {$query_where}
    ORDER BY `tsTimeStamp` DESC, idAnnounce" .
    ($query_limit ? " LIMIT {$query_limit}" : ''));

  $template->assign_var('NEWS_COUNT', mysql_num_rows($announce_list));

  $users = array();
  while($announce = mysql_fetch_assoc($announce_list))
  {
    if($announce['user_id'] && !isset($users[$announce['user_id']]))
    {
      $users[$announce['user_id']] = db_user_by_id($announce['user_id']);
    }
    $template->assign_block_vars('announces', array(
      'ID'         => $announce['idAnnounce'],
      'TIME'       => date(FMT_DATE_TIME, $announce['unix_time'] + SN_CLIENT_TIME_DIFF),
      'ANNOUNCE'   => cht_message_parse($announce['strAnnounce'], false, intval($announce['authlevel'])),
      'DETAIL_URL' => $announce['detail_url'],
      'USER_NAME'  =>
        isset($users[$announce['user_id']]) && $users[$announce['user_id']] ? render_player_nick($users[$announce['user_id']], array('color' => true)):
        js_safe_string($announce['user_name']),
      'NEW'        => $announce['unix_time'] + $config->game_news_actual >= SN_TIME_NOW,
      'FUTURE'     => $announce['unix_time'] > SN_TIME_NOW,
    ));
  }
}

function nws_mark_read(&$user)
{
  if(isset($user['id']))
  {
    db_user_set_by_id($user['id'], '`news_lastread` = ' . SN_TIME_NOW);
    $user['news_lastread'] = SN_TIME_NOW;
  }
}
