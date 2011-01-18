<?php

/**
 * announce.php
 *
 * @v4 Security checks by Gorlum for http://supernova.ws
 * @v2 (c) copyright 2010 by Gorlum for http://supernova.ws
 * based on admin/activeplanet.php (c) 2008 for XNova
 */

define('INSIDE', true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once("{$ugamela_root_path}common.{$phpEx}");

$template     = gettemplate('announce', true);

$announce_id   = intval($_GET['id']);
$text          = SYS_mysqlSmartEscape($_POST['text']);
$announce_time = SYS_mysqlSmartEscape($_POST['dtDateTime']);
$detail_url    = SYS_mysqlSmartEscape($_POST['detail_url']);
$mode          = SYS_mysqlSmartEscape($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

doquery("UPDATE {{users}} SET `news_lastread` = 0 WHERE `id` = {$user['id']};");
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

    $mode = '';
  };

  switch($mode)
  {
    case 'del':
      doquery( "DELETE FROM {{announce}} WHERE `idAnnounce`={$announce_id}");
      $mode = '';
    break;

    case 'edit':
      $template->assign_var('ID', $announce_id);

    case 'copy':
      $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`={$announce_id};", 'announce', true);
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
  'dpath'           => $dpath,
  'tsTimeStamp'     => $announce['tsTimeStamp'],
  'strAnnounce'     => $announce['strAnnounce'],
  'DETAIL_URL'      => $announce['detail_url'],
  'time_now'        => $time_now,
));

while ($announce = mysql_fetch_array($allAnnounces)) {
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
