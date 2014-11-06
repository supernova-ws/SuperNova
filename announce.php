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
$template     = gettemplate('announce', true);

$announce_id   = sys_get_param_id('id');
$text          = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url    = sys_get_param_str('detail_url');
$mode          = sys_get_param_str('mode');


if ($user['authlevel'] >= 3)  {
  if (!empty($text)) {
    $idAnnounce = sys_get_param_id('id');
    $announce_time = strtotime($announce_time);
    $announce_time = $announce_time ? $announce_time : $time_now; // ("FROM_UNIXTIME(".time().")")

    if ($mode == 'edit') {
      doquery("UPDATE {{announce}} SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$idAnnounce}");
    } else {
      doquery("INSERT INTO {{announce}}
        SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}',
        `user_id` = {$user['id']}, `user_name` = '" . mysql_real_escape_string($user['username']) . "'");
    }

    if($announce_time <= $time_now) {
      if($announce_time > $config->var_news_last && $announce_time == $time_now) {
        $config->db_saveItem('var_news_last', $announce_time);
      }

      if(sys_get_param_int('news_mass_mail')) {
        $text = sys_get_param('text') . ($detail_url ? " <a href=\"{$detail_url}\"><span class=\"positive\">{$lang['news_more']}</span></a>" : '');
        msg_send_simple_message('*', 0, 0, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['news_title'], $text);
      }
    }

    $mode = '';
  }

  switch($mode) {
    case 'del':
      doquery( "DELETE FROM {{announce}} WHERE `idAnnounce`={$announce_id} LIMIT 1;");
      $mode = '';
    break;

    case 'edit':
      $template->assign_var('ID', $announce_id);

    case 'copy':
      $announce = doquery("SELECT * FROM {{announce}} WHERE `idAnnounce`={$announce_id} LIMIT 1;", '', true);
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
  'time_now'        => $time_now,
));

display($template, $lang['news_title']);
