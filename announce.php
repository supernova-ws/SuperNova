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

ini_set('display_errors', 1);

includeLang('admin');

$GET_cmd = SYS_mysqlSmartEscape($_GET['cmd']);
$GET_id = intval($_GET['id']);
$POST_text = SYS_mysqlSmartEscape($_POST['text']);
$POST_dtDateTime = SYS_mysqlSmartEscape($_POST['dtDateTime']);
$POST_mode = SYS_mysqlSmartEscape($_POST['mode']);

$parse          = $lang;
$parse['dpath'] = $dpath;
$parse['submitTitle'] = $lang['adm_an_add'];
$parse['modePrintable'] = $lang['adm_an_mode_new'];

if ($user['authlevel'] >= 3) {
  if (!empty($POST_text)){
    $idAnnounce = intval(mysql_real_escape_string($_POST['id']));
    $dtDateTime = empty($POST_dtDateTime) ? ("FROM_UNIXTIME(".time().")") : "'" . $POST_dtDateTime . "'";
    $strText = $POST_text;

    if ($POST_mode == 'edit'){
      doquery( "UPDATE {{table}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}' WHERE `idAnnounce`={$idAnnounce}", 'announce');
    }else{
      doquery( "INSERT INTO {{table}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}'", 'announce');
    }
  };

  if ($GET_cmd=='del'){
    $idAnnounce = $GET_id;
    doquery( "DELETE FROM {{table}} WHERE `idAnnounce`={$idAnnounce}", 'announce');
  };

  if ($GET_cmd=='edit'){
    $parse['id'] = $GET_id;
    $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`=".$GET_id, 'announce', true);
    $parse['tsTimeStamp']   = $announce['tsTimeStamp'];
    $parse['strAnnounce']   = $announce['strAnnounce'];
    $parse['modePrintable'] = $lang['adm_an_mode_edit'];
    $parse['submitTitle']   = $lang['adm_an_edit'];
  };

  if ($GET_cmd=='dup'){
    $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`=".$GET_id, 'announce', true);
    $parse['tsTimeStamp']   = $announce['tsTimeStamp'];
    $parse['strAnnounce']   = $announce['strAnnounce'];
    $parse['modePrintable'] = $lang['adm_an_mode_dupe'];
  };
  $parse['mode'] = $GET_cmd;

  $annQuery = '';
}else{
  $parse['DisplayAdmin'] = "display: none";

  $annQuery = 'WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<=' . intval($time_now) . ' ';
}

$PageTPL        = gettemplate('announce');
$allAnnounces   = doquery("SELECT * FROM {{table}} " . $annQuery . "ORDER BY `tsTimeStamp` DESC", 'announce');
$Count          = 0;

while ($announce = mysql_fetch_array($allAnnounces)) {
  $parse['announces'] .= "<tr>";
  $parse['announces'] .= "<td class=b>". str_replace(" ", "&nbsp;", $announce['tsTimeStamp']) ."</td>";
  $parse['announces'] .= "<td class=b align=justify>". sys_parseBBCode($announce['strAnnounce']) ."</td>";
  if ($user['authlevel'] >= 1) {
    $parse['announces'] .= "<td class=b><center><a href=\"announce.php?cmd=edit&id=".$announce['idAnnounce']."\">Ed</a></th>";
    $parse['announces'] .= "<td class=b><center><a href=\"announce.php?cmd=dup&id=".$announce['idAnnounce']."\">Dup</a></th>";
    $parse['announces'] .= "<td class=b><center><a href=\"announce.php?cmd=del&id=".$announce['idAnnounce']."\"><img src=\"../images/r1.png\"></a></th>";
  }
  $parse['announces'] .= "</tr>";
  $Count++;
}

$parse['announces_total'] = $Count;
$parse['colspan'] = ($user['authlevel'] >= 1) ? 5 : 2;

$page = parsetemplate( $PageTPL , $parse );

display( $page, $lang['adm_an_title']);
?>