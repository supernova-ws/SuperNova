<?php
// print(iconv('CP1251', 'UTF-8', 'Чат временно отключен'));
// die();

/*
 chat_msg.php
   AJAX-called code to show last X messages/history screen

 Changelog:
   2.0 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 copyright 2008 by e-Zobar for XNova
*/

$skip_fleet_update = true;

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false)
{
  includeLang('login');
  header("Location: login.php");
}

$chat_type = SYS_mysqlSmartEscape($_GET['chat_type'] ? $_GET['chat_type'] : $_POST['chat_type']);
$show_history = SYS_mysqlSmartEscape($_GET['show'] ? $_GET['show'] : $_POST['show']);
$page = intval($_GET['page'] ? $_GET['page'] : $_POST['page']);

$ally_id = $user['ally_id'];

includeLang('chat');
if (($config->_MODE != CACHER_NO_CACHE) && ($config->chat_timeout) && ($config->array_get('users', $user['id'], 'chat_lastUpdate') + $config->chat_timeout < $time_now))
{
  print(iconv('CP1251', 'UTF-8', $lang['chat_timeout']));
  die();
}

$page_limit = 25; // Chat rows Limit
$start_row = $page * $page_limit;

if($chat_type!='ally' || !$ally_id){
  $ally_id = 0;
}

if ($show_history=='history') {
  echo "<div style='width:100%;border:1px solid red;padding:4px;' align=center>";
  echo "<b><font size=3>".$lang['AllyChat']." / ".$lang['chat_history']."</font></b> ";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<b><font size=2>".$lang['chat_page'].":</font></b> ";
  echo "<select name='page' onchange='document.location.assign(\"chat_msg.php?chat_type=".$chat_type."&ally_id=".$ally_id."&show=".$show_history."&page=\"+this.value)'>";
  $rows = doquery("SELECT count(1) AS CNT FROM {{chat}} WHERE ally_id = '{$ally_id}';", "",true);
  $cnt = $rows['CNT'] / $page_limit;
  for($i = 0; $i < $cnt; $i++) {
    if($page == $i){
      echo "<option value=\"{$i}\" selected>{$i}</option>";
    }else{
      echo "<option value=\"{$i}\">{$i}</option>";
    }
  }
  echo "</select>";
  echo "</div>";

  $qry = intval($start_row) . ',';
}else{
  $qry = '';
}

$query = doquery("SELECT * FROM {{chat}} WHERE ally_id = '{$ally_id}' ORDER BY messageid DESC LIMIT {$qry}{$page_limit};");

$buff = '';
while($v = mysql_fetch_object($query)){
  if($show_history != 'history'){
    $nick = "<a href='#' onmousedown=\"addSmiley('[ ".htmlentities($v->user, ENT_QUOTES, cp1251)." ]')\">".htmlentities($v->user, ENT_QUOTES, cp1251)."</a>";
  }else{
    $nick = htmlentities($v->user, ENT_QUOTES, cp1251);
  }
  $msg = htmlentities($v->message, ENT_QUOTES, cp1251);
  $msgtimestamp = date(FMT_DATE_TIME, htmlentities($v->timestamp, ENT_QUOTES, cp1251));

  $msg = CHT_messageParse($msg);

  // Affichage du message
  $msg = "<div align=\"left\" style='background-color:black;color:white;'><span style='font:menu;'>[".$msgtimestamp."]</span> <span style='font:menu;'><b>".$nick."</b></span> : ".$msg."<br></div>";
  $buff = $msg . $buff;
}
print $buff;
sys_log_hit();

?>
