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

$template     = gettemplate('announce', true);

$announce_id   = sys_get_param_int('id');
$text          = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url    = sys_get_param_str('detail_url');
$mode          = sys_get_param_str('mode');

if($sys_user_logged_in)
{
  doquery("UPDATE {{users}} SET `news_lastread` = 0 WHERE `id` = {$user['id']} LIMIT 1;");
}

if ($user['authlevel'] >= 3)
{
  if (!empty($text))
  {
    $idAnnounce = sys_get_param_int('id');
    $dtDateTime = empty($announce_time) ? ("FROM_UNIXTIME(".time().")") : "'{$announce_time}'";

    if ($mode == 'edit')
    {
      doquery( "UPDATE {{announce}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$idAnnounce}");
    }
    else
    {
      doquery( "INSERT INTO {{announce}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$text}', detail_url = '{$detail_url}'");
    }
    doquery("UPDATE {{users}} SET `news_lastread` = `news_lastread` + 1;");

    if(sys_get_param_int('news_mass_mail'))
    {
      $text = $_POST['text'];
      if($detail_url)
      {
        // TODO: Move merging detail url to template
        $text = "{$text} <a href=\"{$detail_url}\">{$lang['news_more']}</a>";
      }

      msg_send_simple_message('*', 0, 0, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['news_title'], $text);
    }

    $mode = '';
  }

  switch($mode)
  {
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
}
else
{
  $annQuery = 'WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<=' . intval($time_now);
}

$allAnnounces = doquery("SELECT *, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time FROM {{announce}} {$annQuery} ORDER BY `tsTimeStamp` DESC");

$template->assign_vars(array(
  'AUTHLEVEL'       => $user['authlevel'],
  'total'           => mysql_num_rows($allAnnounces),
  'MODE'            => $mode,
  'tsTimeStamp'     => $announce['tsTimeStamp'],
  'strAnnounce'     => $announce['strAnnounce'],
  'DETAIL_URL'      => $announce['detail_url'],
  'time_now'        => $time_now,
));

while ($announce = mysql_fetch_assoc($allAnnounces))
{
  $template->assign_block_vars('announces', array(
    'ID'         => $announce['idAnnounce'],
    'TIME'       => $announce['tsTimeStamp'],
    'ANNOUNCE'   => sys_bbcodeParse($announce['strAnnounce']),
    'DETAIL_URL' => $announce['detail_url'],
    'NEW'        => $announce['unix_time'] + $config->game_news_actual > $time_now,
    'FUTURE'     => $announce['unix_time'] > $time_now,
  ));
}

display($template, $lang['news_title']);

?>
