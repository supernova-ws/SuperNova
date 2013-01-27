<?php

function nws_render(&$template, $query_where = '', $query_limit = 20)
{
  global $config, $time_now, $time_diff;

  $announce_list = doquery("SELECT *, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time FROM {{announce}} {$query_where} ORDER BY `tsTimeStamp` DESC" . ($query_limit ? " LIMIT {$query_limit}" : ''));

  $template->assign_var('NEWS_COUNT', mysql_num_rows($announce_list));

  while($announce = mysql_fetch_assoc($announce_list))
  {
    $template->assign_block_vars('announces', array(
      'ID'         => $announce['idAnnounce'],
      'TIME'       => $announce['tsTimeStamp'] + $time_diff,
      'ANNOUNCE'   => sys_bbcodeParse($announce['strAnnounce']),
      'DETAIL_URL' => $announce['detail_url'],
      'NEW'        => $announce['unix_time'] + $config->game_news_actual >= $time_now,
      'FUTURE'     => $announce['unix_time'] > $time_now,
    ));
  }
}

?>
