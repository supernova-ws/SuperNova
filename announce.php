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

$announce_id   = intval($_GET['id']);
$text          = SYS_mysqlSmartEscape($_POST['text']);
$announce_time = SYS_mysqlSmartEscape($_POST['dtDateTime']);
$detail_url    = SYS_mysqlSmartEscape($_POST['detail_url']);
$mode          = SYS_mysqlSmartEscape($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

if($sys_user_logged_in)
{
  doquery("UPDATE {{users}} SET `news_lastread` = 0 WHERE `id` = {$user['id']};");
}

if ($user['authlevel'] >= 3)
{
  if (!empty($text))
  {
    $idAnnounce = intval(mysql_real_escape_string($_POST['id']));
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
      if($detail_url)
      {
        // TODO: Move merging detail url to template
        $text = "{$text} <a href=\"{$detail_url}\">{$lang['news_more']}</a>";
      }

      doquery("INSERT INTO {{messages}} (message_owner, message_time, message_type, message_from, message_subject, message_text) SELECT `id`, unix_timestamp(now()), 1, '{$lang['sys_administration']}', '{$lang['news_title']}', '{$text}' FROM {{users}};");
      doquery("UPDATE {{users}} SET {$messfields[1]} = {$messfields[1]} + 1, {$messfields[100]} = {$messfields[100]} + 1;");
    }

    $mode = '';
  };

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

while ($announce = mysql_fetch_assoc($allAnnounces)) {
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
