<?php

/**
 * rw.php
 *
 * 1.0st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$open = true;
$reportid = $_GET["raport"];
$raportrow = doquery("SELECT * FROM {{rw}} WHERE `rid` = '".(mysql_real_escape_string($_GET["raport"]))."';", '', true);

if ($allow == 1 || $open) {
  $Page  = "<html>";
  $Page .= "<head>";
  $Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$dpath."/formate.css\">";
  $Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />";
  $Page .= "</head>";
  $Page .= "<body>";
  $Page .= "<center>";

  if (
      ($raportrow["owners"] == $user["id"])
      //and ($raportrow["a_zestrzelona"] == 1)
   ) {
    $Page .= "<td>Контакт с флотом был потерян.<br>";
    $Page .= "(Скорее всего флот был уничтожен за 1 раунд.)</td>";
  } else {
    $report = stripslashes($raportrow["raport"]);
    foreach ($lang['tech_rc'] as $id => $s_name) {
      $str_replace1  = array("[ship[".$id."]]");
      $str_replace2  = array($s_name);
      $report = str_replace($str_replace1, $str_replace2, $report);
    }
    $no_fleet = "<table border=1 align=\"center\"><tr><th>Тип</th></tr><tr><th>Всего</th></tr><tr><th>Оружие</th></tr><tr><th>Щиты</th></tr><tr><th>Броня</th></tr></table>";
    $destroyed = "<table border=1 align=\"center\"><tr><th><font color=\"red\"><strong>Уничтожены!</strong></font></th></tr></table>";
    $str_replace1  = array($no_fleet);
    $str_replace2  = array($destroyed);
    $report = str_replace($str_replace1, $str_replace2, $report);
    $Page .= $report;
  }
  $Page .= "<br /><br />";
  $Page .= "Share this report - ";
  $Page .= $reportid;
  $Page .= "<br /><br />";
  $Page .= "</center>";
  $Page .= "</body>";
  $Page .= "</html>";

  echo $Page;
}

// -----------------------------------------------------------------------------------------------------------
// History version

?>
