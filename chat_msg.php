<?php
//print('Chat temporary disabled');
//die();

/**
 * chat_msg.php
 *
 * @version 1.2s Security checks by Gorlum for http://supernova.ws
 * @version 1.2 by Ihor
 * @version 1.0 copyright 2008 by e-Zobar for XNova
 */

$doNotUpdateFleet = true;

define('INSIDE'  , true);
define('INSTALL' , false);

$xnova_root_path = './';
include($xnova_root_path . 'extension.inc');
include($xnova_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

$GET_ally_id = intval($_GET['ally_id']);
$GET_chat_type = SYS_mysqlSmartEscape($_GET['chat_type']);

includeLang('chat');

if(!$user['id'] || ($config->users[$user['id']]['chat_lastUpdate'] + $config->chat_timeout < $time_now)){
  print(iconv('CP1251', 'UTF-8', $lang['chat_timeout']));
  die();
}

$page_limit = 25; // Chat rows Limit
if($_GET['page']>''){
  $page = $_GET['page'];
}else{
  $page = 0;
}
$start_row = $page * $page_limit;

if ($_GET) {
  if($GET_chat_type=='ally' && $GET_ally_id>'' && $GET_ally_id<>$user['ally_id']){
    $debug->error("Buguser: ".$user['username']." (".$user['id'].")<br />","Bug use");
    die();
  };

  if($GET_chat_type=='ally' && $GET_ally_id>''){
    if ($_GET['show']=='history') {
      showPageButtons($page,'ally');
      $query = doquery("SELECT * FROM {{table}} WHERE ally_id = '".$GET_ally_id."' ORDER BY messageid DESC LIMIT ".$start_row.",".$page_limit." ", "chat");
    }else{
      $query = doquery("SELECT * FROM {{table}} WHERE ally_id = '".$GET_ally_id."' ORDER BY messageid DESC LIMIT ".$page_limit." ", "chat");
    }
  }else{
    if ($_GET['show']=='history') {
      showPageButtons($page,'all');
      $query = doquery("SELECT * FROM {{table}} WHERE ally_id < 1 ORDER BY messageid DESC LIMIT ".$start_row.",".$page_limit." ", "chat");
    }else{
      $query = doquery("SELECT * FROM {{table}} WHERE ally_id < 1 ORDER BY messageid DESC LIMIT ".$page_limit." ", "chat");
    }
  }
}else{
  if($_POST['chat_type']=='ally' && $_POST['ally_id']>''){
    $query = doquery("SELECT * FROM {{table}} WHERE ally_id = '".$_POST['ally_id']."' ORDER BY messageid DESC LIMIT ".$page_limit." ", "chat");
  }else{
    $query = doquery("SELECT * FROM {{table}} WHERE ally_id < 1 ORDER BY messageid DESC LIMIT ".$page_limit." ", "chat");
  }
}

$buff = "";
while($v=mysql_fetch_object($query)){
  $msg = "";
  if($_GET['show']!='history'){
    // $nick="<a href='#' onmousedown='addNick(this)'>".htmlentities($v->user, ENT_QUOTES, cp1251)."</a>";
    $nick="<a href='#' onmousedown=\"addSmiley('[ ".htmlentities($v->user, ENT_QUOTES, cp1251)." ]')\">".htmlentities($v->user, ENT_QUOTES, cp1251)."</a>";
  }else{
    $nick=htmlentities($v->user, ENT_QUOTES, cp1251);
  }
  $msg=htmlentities($v->message, ENT_QUOTES, cp1251);
  $msgtimestamp=htmlentities($v->timestamp, ENT_QUOTES, cp1251);
  $msgtimestamp=date(DATE_TIME, $msgtimestamp);
  // Les différentes polices (gras, italique, couleurs, etc...)
  $msg = CHT_messageParse($msg);

  // Affichage du message
  $msg="<div align=\"left\" style='background-color:black;color:white;'><span style='font:menu;'>[".$msgtimestamp."]</span> <span style='width:50px;font:menu;'><b>".$nick."</b></span> : ".$msg."<br></div>";
  $buff = $msg . $buff;
}
print $buff;
sys_logHit();

function showPageButtons($curPage,$type){
  global $page_limit, $lang, $GET_chat_type, $GET_ally_id;

  echo "<div style='width:100%;border:1px solid red;padding:4px;' align=center>";
  echo "<b><font size=3>".$lang['AllyChat']." / ".$lang['chat_history']."</font></b> ";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<b><font size=2>".$lang['chat_page'].":</font></b> ";
  echo "<select name='page' onchange='document.location.assign(\"chat_msg.php?chat_type=".$GET_chat_type."&ally_id=".$GET_ally_id."&show=".$_GET['show']."&page=\"+this.value)'>";
  if($type=='ally'){
    $rows = doquery("SELECT count(1) AS CNT FROM {{table}} WHERE ally_id = '".$GET_ally_id."'", "chat",true);
    $cnt = $rows['CNT'] / $page_limit;
      for($i = 0; $i < $cnt; $i++) {
      if($curPage==$i){
        echo "<option value=".$i." selected>".$i."</option> ";
      }else{
        echo "<option value=".$i.">".$i."</option> ";
      }
      }
  }else{
    $rows = doquery("SELECT count(1) AS CNT FROM {{table}} WHERE ally_id < 1", "chat",true);
    $cnt = $rows['CNT'] / $page_limit;
      for($i = 0; $i < $cnt; $i++) {
      if($curPage==$i){
        echo "<option value=".$i." selected>".$i."</option> ";
      }else{
        echo "<option value=".$i.">".$i."</option> ";
      }
      }
  }
  echo "</select> ";
  echo "</div>";
}

// Shoutbox by e-Zobar - Copyright XNova Team 2008
?>
