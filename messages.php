<?php

/**
 * messages.php
 *
 * @version 1.5 - Replaced table 'galaxy' with table 'planer' by Gorlum for http://supernova.ws
 * @version 1.4 - Security checked & verified for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.3 - Adding "Inbound" messages by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

require($ugamela_root_path.'config.php');
$prefix = $dbsettings["prefix"];
unset($dbsettings);

check_urlaubmodus ($user);
  includeLang('messages');


  $OwnerID       = intval($_GET['id']);
  $MessCategory  = (isset($_GET['messcat'])) ? intval($_GET['messcat']) : 100;
  $MessPageMode  = SYS_mysqlSmartEscape($_GET["mode"]);
  $DeleteWhat    = $_POST['deletemessages'];
  if (isset ($DeleteWhat)) {
    $MessPageMode = "delete";
  }

  $POST_text = str_replace("'", '&#39;', $_POST['text']);
  $POST_text = trim ( nl2br ( strip_tags ( $POST_text, '<br>' ) ) );
  $POST_text = str_replace("\r\n", '', $POST_text);
  $POST_text = SYS_mysqlSmartEscape($POST_text);

  $POST_subject = SYS_mysqlSmartEscape($_POST['subject']);

  $DeleteWhat = SYS_mysqlSmartEscape($_POST['deletemessages']);

  $MessageType   = array ( -1, 0, 1, 2, 3, 4, 5, 15, 99, 100 );
  $TitleColor    = array ( -1 => '#FFFFFF', 0 => '#FFFF00', 1 => '#FF6699', 2 => '#FF3300', 3 => '#FF9900', 4 => '#773399', 5 => '#009933', 15 => '#0270FF', 99 => '#007070', 100 => '#ABABAB'  );
  $BackGndColor  = array ( -1 => '#000000', 0 => '#663366', 1 => '#336666', 2 => '#000099', 3 => '#666666', 4 => '#999999', 5 => '#999999', 15 => '#999999', 99 => '#999999', 100 => '#999999'  );

  $UnRead        = doquery("SELECT * FROM {{table}} WHERE `id` = '". $user['id'] ."';", 'users', true);
  foreach($MessageType as $MessType){
    $WaitingMess[$MessType] = $UnRead[$messfields[$MessType]];
    $TotalMess[$MessType]   = 0;
  }

  $UsrMess       = doquery("SELECT message_owner, message_type, COUNT(message_owner) AS message_count FROM {{table}} WHERE `message_owner` = '".$user['id']."' GROUP BY message_owner, message_type ORDER BY message_owner ASC, message_type;", 'messages');
  while ($CurMess = mysql_fetch_array($UsrMess)) {
    $TotalMess[$CurMess['message_type']]  = $CurMess['message_count'];
    $TotalMess[100]                      += $CurMess['message_count'];
  }

  $UsrMess       = doquery("SELECT COUNT(message_sender) AS message_count FROM {{table}} WHERE `message_sender` = '".$user['id']."' AND message_type = 1 GROUP BY message_sender;", 'messages', true);
  $TotalMess[-1] = intval($UsrMess['message_count']);

  switch ($MessPageMode) {
    case 'write':
      // -------------------------------------------------------------------------------------------------------
      // Envoi d'un messages
      if ( !is_numeric( $OwnerID ) ) {
        message ($lang['mess_no_ownerid'], $lang['mess_error']);
      }

      $OwnerRecord = doquery("SELECT * FROM {{table}} WHERE `id` = '".$OwnerID."';", 'users', true);

      if (!$OwnerRecord) {
        message ($lang['mess_no_owner']  , $lang['mess_error']);
      }

      $OwnerHome   = doquery("SELECT * FROM {{table}} WHERE `id` = '". $OwnerRecord["id_planet"] ."';", 'planets', true);
      if (!$OwnerHome) {
        message ($lang['mess_no_ownerpl'], $lang['mess_error']);
      }

      $error = 0;
      if (!$POST_subject) {
        $error++;
        $page .= "<center><br><font color=#FF0000>".$lang['mess_no_subject']."<br></font></center>";
      }
      if (!$POST_text) {
        $error++;
        $page .= "<center><br><font color=#FF0000>".$lang['mess_no_text']."<br></font></center>";
      }
      if ($error == 0) {
        $page .= "<center><font color=#00FF00>".$lang['mess_sended']."<br></font></center>";

        $Message = $POST_text;

        $Owner   = $OwnerID;
        $Sender  = $user['id'];
        $From    = $user['username'] ." [".$user['galaxy'].":".$user['system'].":".$user['planet']."]";
        $Subject = $POST_subject;
        SendSimpleMessage ( $Owner, $Sender, '', 1, $From, $Subject, $Message);
        $subject = "";
        $text    = "";
      }

      $parse['Send_message'] = $lang['mess_pagetitle'];
      $parse['Recipient']    = $lang['mess_recipient'];
      $parse['Subject']      = $lang['mess_subject'];
      $parse['Message']      = $lang['mess_message'];
      $parse['characters']   = $lang['mess_characters'];
      $parse['Envoyer']      = $lang['mess_envoyer'];

      $parse['id']           = $OwnerID;
      $parse['to']           = $OwnerRecord['username'] ." [".$OwnerHome['galaxy'].":".$OwnerHome['system'].":".$OwnerHome['planet']."]";
      $parse['subject']      = (!isset($subject)) ? $lang['mess_no_subject'] : $subject ;
      $parse['text']         = $text;

      $page                 .= parsetemplate(gettemplate('messages_pm_form'), $parse);
      break;

    case 'delete':
      // -------------------------------------------------------------------------------------------------------
      // Suppression des messages selectionnés
      if       ($DeleteWhat == 'deleteall') {
        doquery("DELETE FROM {{table}} WHERE `message_owner` = '". $user['id'] ."';", 'messages');
      } elseif ($DeleteWhat == 'deletemarked') {
        foreach($_POST as $Message => $Answer) {
          if (preg_match("/delmes/i", $Message) && $Answer == 'on') {
            $MessId   = str_replace("delmes", "", $Message);
            $MessHere = doquery("SELECT * FROM {{table}} WHERE `message_id` = '". $MessId ."' AND `message_owner` = '". $user['id'] ."';", 'messages');
            if ($MessHere) {
              doquery("DELETE FROM {{table}} WHERE `message_id` = '".$MessId."';", 'messages');
            }
          }
        }
      } elseif ($DeleteWhat == 'deleteunmarked') {
        foreach($_POST as $Message => $Answer) {
          $CurMess    = preg_match("/showmes/i", $Message);
          $MessId     = str_replace("showmes", "", $Message);
          $Selected   = "delmes".$MessId;
          $IsSelected = $_POST[ $Selected ];
          if (preg_match("/showmes/i", $Message) && !isset($IsSelected)) {
            $MessHere = doquery("SELECT * FROM {{table}} WHERE `message_id` = '". $MessId ."' AND `message_owner` = '". $user['id'] ."';", 'messages');
            if ($MessHere) {
              doquery("DELETE FROM {{table}} WHERE `message_id` = '".$MessId."';", 'messages');
            }
          }
        }
      }
      $MessCategory = intval($_POST['category']);

    case 'show':
      // -------------------------------------------------------------------------------------------------------
      // Affichage de la page des messages
      $page  = "<script language=\"JavaScript\">\n";
      $page .= "function f(target_url, win_name) {\n";
      $page .= "var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');\n";
      $page .= "new_win.focus();\n";
      $page .= "}\n";
      $page .= "</script>\n";
      $page .= "<center>";
      $page .= "<table>";
      $page .= "<tr>";
      $page .= "<td></td>";
      $page .= "<td>\n";
      $page .= "<table width=\"519\">";
      $page .= "<form action=\"messages.php\" method=\"post\"><table>";
      $page .= "<tr>";
      $page .= "<td></td>";
      $page .= "<td>\n<input name=\"messages\" value=\"1\" type=\"hidden\">";
      $page .= "<table width=\"519\">";
      $page .= "<tr>";
      if($MessCategory != -1){
        $page .= "<th colspan=\"4\">";
        $page .= "<select onchange=\"document.getElementById('deletemessages').options[this.selectedIndex].selected='true'\" id=\"deletemessages2\" name=\"deletemessages2\">";
        $page .= "<option value=\"deletemarked\">".$lang['mess_deletemarked']."</option>";
        $page .= "<option value=\"deleteunmarked\">".$lang['mess_deleteunmarked']."</option>";
        $page .= "<option value=\"deleteall\">".$lang['mess_deleteall']."</option>";
        $page .= "</select>";
        $page .= "<input value=\"".$lang['mess_its_ok']."\" type=\"submit\">";
        $page .= "</th></tr>";

        $page .= "<tr>";
        $page .= "<th style=\"color: rgb(242, 204, 74);\" colspan=\"4\">";
        $page .= "<input name=\"category\" value=\"".$MessCategory."\" type=\"hidden\">";
        $page .= "<input onchange=\"document.getElementById('fullreports').checked=this.checked\" id=\"fullreports2\" name=\"fullreports2\" type=\"checkbox\">".$lang['mess_partialreport']."</th>";
        $page .= "</tr><tr>";
        $page .= "<th>".$lang['mess_action']."</th>";
      }
      $page .= "<th>".$lang['mess_date']."</th>";

      if($MessCategory == -1){
        $page .= "<th>".$lang['mess_recipient']."</th>";
      }else{
        $page .= "<th>".$lang['mess_from']."</th>";
      }

      $page .= "<th>".$lang['mess_subject']."</th>";
      $page .= "</tr>";

      if ($MessCategory == 100) {
        $UsrMess       = doquery("SELECT * FROM {{table}} WHERE `message_owner` = '".$user['id']."' ORDER BY `message_time` DESC;", 'messages');
        $SubUpdateQry  = "";
        for ($MessType = 0; $MessType < 101; $MessType++) {
          if ( in_array($MessType, $MessageType) ) {
            $SubUpdateQry .= "`".$messfields[$MessType]."` = '0', ";
          }
        }
        $QryUpdateUser  = "UPDATE {{table}} SET ";
        $QryUpdateUser .= $SubUpdateQry;
        $QryUpdateUser .= "`id` = '".$user['id']."' "; // Vraiment pas envie de me casser le fion a virer la derniere virgule du sub query
        $QryUpdateUser .= "WHERE ";
        $QryUpdateUser .= "`id` = '".$user['id']."';";
        doquery ( $QryUpdateUser, 'users' );

        while ($CurMess = mysql_fetch_array($UsrMess)) {
          $page .= "\n<tr>";
          $page .= "<input name=\"showmes". $CurMess['message_id'] . "\" type=\"hidden\" value=\"1\">";
          $page .= "<th><input name=\"delmes". $CurMess['message_id'] . "\" type=\"checkbox\"></th>";
          $page .= "<th>". date(FMT_DATE_TIME, $CurMess['message_time']) ."</th>";
          $page .= "<th>". stripslashes( $CurMess['message_from'] ) ."</th>";
          $page .= "<th>". stripslashes( $CurMess['message_subject'] ) ." ";
          if ($CurMess['message_type'] == 1) {
            $page .= "<a href=\"messages.php?mode=write&amp;id=". $CurMess['message_sender'] ."&amp;subject=".$lang['mess_answer_prefix'] . htmlspecialchars( $CurMess['message_subject']) ."\">";
            $page .= "<img src=\"". $dpath ."img/m.gif\" alt=\"".$lang['mess_answer']."\" border=\"0\"></a></th>";
          } else {
            $page .= "</th>";
          }
          $page .= "</tr><tr>";
          $page .= "<td style=\"background-color: ".$BackGndColor[$CurMess['message_type']]."; background-image: none;\"; class=\"b\"> </td>";
          $page .= "<td style=\"background-color: ".$BackGndColor[$CurMess['message_type']]."; background-image: none;\"; colspan=\"3\" class=\"b\">". stripslashes( nl2br( $CurMess['message_text'] ) ) ."</td>";
          $page .= "</tr>";
        }
      } else {
        if($MessCategory == -1){
          $tableUsers  = $prefix.'users';
          $UsrMess     = doquery("SELECT {{table}}.*, {$tableUsers}.username, {$tableUsers}.id FROM {{table}} LEFT JOIN {$tableUsers} ON id = message_owner WHERE `message_sender` = '".$user['id']."' AND `message_type` = 1 ORDER BY `message_time` DESC;", 'messages');
          $fieldFrom   = 'username';
          $fieldFromID = 'id';
        }else{
          $UsrMess       = doquery("SELECT * FROM {{table}} WHERE `message_owner` = '".$user['id']."' AND `message_type` = '".$MessCategory."' ORDER BY `message_time` DESC;", 'messages');
          $QryUpdateUser  = "UPDATE {{table}} SET ";
          $QryUpdateUser .= "`".$messfields[$MessCategory]."` = '0', ";
          $QryUpdateUser .= "`".$messfields[100]."` = `".$messfields[100]."` - '".$WaitingMess[$MessCategory]."' ";
          $QryUpdateUser .= "WHERE ";
          $QryUpdateUser .= "`id` = '".$user['id']."';";
          doquery ( $QryUpdateUser, 'users' );
          $fieldFrom   = 'message_from';
          $fieldFromID = 'message_sender';
        };
        while ($CurMess = mysql_fetch_array($UsrMess)) {
            $page .= "\n<tr>";
            $page .= "<input name=\"showmes". $CurMess['message_id'] . "\" type=\"hidden\" value=\"1\">";
            if($MessCategory != -1){
              $page .= "<th><input name=\"delmes". $CurMess['message_id'] ."\" type=\"checkbox\"></th>";
            }
            $page .= "<th>". date(FMT_DATE_TIME, $CurMess['message_time']) ."</th>";
            $page .= "<th>". stripslashes( $CurMess[$fieldFrom] ) ."</th>";
            $page .= "<th>". stripslashes( $CurMess['message_subject'] ) ." ";
            if ($CurMess['message_type'] == 1) {
              $page .= "<a href=\"messages.php?mode=write&amp;id=". $CurMess[$fieldFromID] ."&amp;subject=".$lang['mess_answer_prefix'] . htmlspecialchars( $CurMess['message_subject']) ."\">";
              $page .= "<img src=\"". $dpath ."img/m.gif\" alt=\"".$lang['mess_answer']."\" border=\"0\"></a></th>";
            } else {
              $page .= "</th>";
            }
            $page .= "</tr><tr>";
            if($MessCategory != -1){
              $page .= "<td class=\"b\"> </td>";
            }
            $page .= "<td colspan=\"3\" class=\"b\">". nl2br( stripslashes( $CurMess['message_text'] ) ) ."</td>";
            $page .= "</tr>";
        }
      }


      if($MessCategory != -1){
        $page .= "<tr>";
        $page .= "<th style=\"color: rgb(242, 204, 74);\" colspan=\"4\">";
        $page .= "<input onchange=\"document.getElementById('fullreports2').checked=this.checked\" id=\"fullreports\" name=\"fullreports\" type=\"checkbox\">".$lang['mess_partialreport']."</th>";
        $page .= "</tr>";

        $page .= "<tr><th colspan=\"4\">";
        $page .= "<select onchange=\"document.getElementById('deletemessages2').options[this.selectedIndex].selected='true'\" id=\"deletemessages\" name=\"deletemessages\">";
        $page .= "<option value=\"deletemarked\">".$lang['mess_deletemarked']."</option>";
        $page .= "<option value=\"deleteunmarked\">".$lang['mess_deleteunmarked']."</option>";
        $page .= "<option value=\"deleteall\">".$lang['mess_deleteall']."</option>";
        $page .= "</select>";
        $page .= "<input value=\"".$lang['mess_its_ok']."\" type=\"submit\">";
        $page .= "</th>";
        $page .= "</tr>";
      }
      $page .= "<tr><td colspan=\"4\"></td>";
      $page .= "</tr>";
      $page .= "</table>\n";
      $page .= "</td>";
      $page .= "</tr>";
      $page .= "</table>\n";
      $page .= "</form>";
      $page .= "</td>";
      $page .= "</table>\n";
      $page .= "</center>";
      break;

    default:
      $page  = "<script language=\"JavaScript\">\n";
      $page .= "function f(target_url, win_name) {\n";
      $page .= "var new_win = window.open(target_url, win_name, 'resizable=yes, scrollbars=yes, menubar=no, toolbar=no, width=550, height=280, top=0, left=0');\n";
      $page .= "new_win.focus();\n";
      $page .= "}\n";
      $page .= "</script>\n";
      $page .= "<center>";
      $page .= "<br>";
      $page .= "<table width=\"569\">";
      $page .= "<tr>";
      $page .= "  <td class=\"c\" colspan=\"5\">". $lang['title'] ."</td>";
      $page .= "</tr><tr>";
      $page .= "  <th colspan=\"3\">". $lang['head_type'] ."</th>";
      $page .= "  <th>". $lang['head_count'] ."</th>";
      $page .= "  <th>". $lang['head_total'] ."</th>";
      $page .= "</tr>";
      foreach($MessageType as $MessType){
        $page .= "<tr>";
        $page .= "  <th colspan=\"3\"><a href=\"messages.php?mode=show&amp;messcat=". $MessType ." \"><font color=\"". $TitleColor[$MessType] ."\">". $lang['type'][$MessType] ."</a></th>";
        $page .= "  <th><font color=\"". $TitleColor[$MessType] ."\">". $WaitingMess[$MessType] ."</font></th>";
        $page .= "  <th><font color=\"". $TitleColor[$MessType] ."\">". $TotalMess[$MessType] ."</font></th>";
        $page .= "</tr>";
      }
      $page .= "</table>";
      $page .= "</center>";
      break;
  }

  display($page, $lang['mess_pagetitle']);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version originelle (Tom1991)
// 1.1 - Mise a plat, linearisation, suppression des doublons / triplons / 'n'gnions dans le code (Chlorel)
// 1.2 - Regroupage des 2 fichiers vers 1 seul plus simple a mettre en oeuvre et a gerer !
?>