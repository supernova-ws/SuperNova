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

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin');

$GET_id          = intval($_GET['id']);
$POST_text       = SYS_mysqlSmartEscape($_POST['text']);
$POST_dtDateTime = SYS_mysqlSmartEscape($_POST['dtDateTime']);
$mode            = SYS_mysqlSmartEscape($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);

$template     = gettemplate('announce', true);

if ($user['authlevel'] >= 3)
{
  if (!empty($POST_text))
  {
    $idAnnounce = intval(mysql_real_escape_string($_POST['id']));
    $dtDateTime = empty($POST_dtDateTime) ? ("FROM_UNIXTIME(".time().")") : "'{$POST_dtDateTime}'";
    $strText = $POST_text;

    if ($mode == 'edit')
    {
      doquery( "UPDATE {{announce}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}' WHERE `idAnnounce`={$idAnnounce}");
    }
    else
    {
      doquery( "INSERT INTO {{announce}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}'");
    }
    $mode = '';
  };

  switch($mode)
  {
    case 'del':
      doquery( "DELETE FROM {{announce}} WHERE `idAnnounce`={$GET_id}");
      $mode = '';
    break;

    case 'edit':
      $template->assign_var('ID', $GET_id);
    case 'copy':
      $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`={$GET_id};", 'announce', true);
    break;
  }
}else{
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
  'time_now'        => $time_now,
));

while ($announce = mysql_fetch_array($allAnnounces)) {
  $template->assign_block_vars('announces', array(
    'ID'       => $announce['idAnnounce'],
    'TIME'     => $announce['tsTimeStamp'], //str_replace(' ', '&nbsp;', $announce['tsTimeStamp']),
    'ANNOUNCE' => sys_bbcodeParse($announce['strAnnounce']),
    'NEW'      => $announce['unix_time'] + $config->game_news_actual > $time_now,
    'FUTURE'   => $announce['unix_time'] > $time_now,
  ));
}

display( $template, $lang['adm_an_title']);
?>