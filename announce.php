<?php

/**
 * announce.php
 *
 * @v2
 * @copyright 2010 by Gorlum for http://ogame.triolan.com.ua
 * based on admin/activeplanet.php (c) 2008 for XNova
 */

define('INSIDE', true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin');

$parse          = $lang;
$parse['dpath'] = $dpath;
$parse['submitTitle'] = $lang['adm_an_add'];
$parse['modePrintable'] = $lang['adm_an_mode_new'];

if ($user['authlevel'] >= 3) {
  if (!empty($_POST['text'])){
    $idAnnounce = intval(mysql_real_escape_string($_POST['id']));
    $dtDateTime = empty($_POST['dtDateTime']) ? ("FROM_UNIXTIME(".time().")") : "'" . mysql_real_escape_string($_POST['dtDateTime']) . "'";
    $strText = mysql_real_escape_string($_POST['text']);

    if ($_POST['mode']=='edit'){
      doquery( "UPDATE {{table}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}' WHERE `idAnnounce`={$idAnnounce}", 'announce');
    }else{
      doquery( "INSERT INTO {{table}} SET `tsTimeStamp`={$dtDateTime}, `strAnnounce`='{$strText}'", 'announce');
    }
  };

  if ($_GET['cmd']=='del'){
    $idAnnounce = intval(mysql_real_escape_string($_GET['id']));
    doquery( "DELETE FROM {{table}} WHERE `idAnnounce`={$idAnnounce}", 'announce');
  };

  if ($_GET['cmd']=='edit'){
    $parse['id'] = intval(mysql_real_escape_string($_GET['id']));
    $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`=".intval(mysql_real_escape_string($_GET['id'])), 'announce', true);
    $parse['tsTimeStamp']   = $announce['tsTimeStamp'];
    $parse['strAnnounce']   = $announce['strAnnounce'];
    $parse['modePrintable'] = $lang['adm_an_mode_edit'];
    $parse['submitTitle']   = $lang['adm_an_edit'];
  };

  if ($_GET['cmd']=='dup'){
    $announce = doquery("SELECT * FROM {{table}} WHERE `idAnnounce`=".intval(mysql_real_escape_string($_GET['id'])), 'announce', true);
    $parse['tsTimeStamp']   = $announce['tsTimeStamp'];
    $parse['strAnnounce']   = $announce['strAnnounce'];
    $parse['modePrintable'] = $lang['adm_an_mode_dupe'];
  };
  $parse['mode'] = $_GET['cmd'];

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
  $parse['announces'] .= "<td class=b align=justify>". $announce['strAnnounce'] ."</td>";
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