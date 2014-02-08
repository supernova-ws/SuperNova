<?php

function nws_render(&$template, $query_where = '', $query_limit = 20)
{
  global $config;

  $announce_list = doquery("SELECT *, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time FROM {{announce}} {$query_where} ORDER BY `tsTimeStamp` DESC" . ($query_limit ? " LIMIT {$query_limit}" : ''));

  $template->assign_var('NEWS_COUNT', mysql_num_rows($announce_list));

  while($announce = mysql_fetch_assoc($announce_list))
  {
    $template->assign_block_vars('announces', array(
      'ID'         => $announce['idAnnounce'],
      'TIME'       => date(FMT_DATE_TIME, $announce['unix_time'] + SN_CLIENT_TIME_DIFF),
      'ANNOUNCE'   => sys_bbcodeParse($announce['strAnnounce']),
      'DETAIL_URL' => $announce['detail_url'],
      'NEW'        => $announce['unix_time'] + $config->game_news_actual >= SN_TIME_NOW,
      'FUTURE'     => $announce['unix_time'] > SN_TIME_NOW,
    ));
  }
}

function nws_mark_read(&$user)
{
  if(isset($user['id']))
  {
    doquery("UPDATE {{users}} SET `news_lastread` = " . SN_TIME_NOW . " WHERE `id` = {$user['id']} LIMIT 1;");
    $user['news_lastread'] = SN_TIME_NOW;
  }
}
