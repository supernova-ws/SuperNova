<?php

/**
 * messages.php
 * Handles internal message system
 *
 * @package messages
 * @version 2.0
 *
 * Revision History
 * ================
 *
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [!] Fully rewrote MessPageMode = 'show' part
 *   [~] All HTML code from 'show' part moved to messages.tpl
 *   [~] Tweaks and optimizations
 *
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Replaced table 'galaxy' with table 'planets'
 *
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Security checked & verified for SQL-injection by Gorlum for http://supernova.ws
 *
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] "Outbox" added
 *
 * 1.2 - copyright 2008 by Chlorel for XNova
 *   [+] Regroupage des 2 fichiers vers 1 seul plus simple a mettre en oeuvre et a gerer !
 *
 * 1.1 - Mise a plat, linearisation, suppression des doublons / triplons / 'n'gnions dans le code (Chlorel)
 *
 * 1.0 - Version originelle (Tom1991)
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

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

  $MessageType   = array ( -1 => '', 0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 15 => '', 99 => '', 100 => '' );
  $TitleColor    = array ( -1 => '#FFFFFF', 0 => '#FFFF00', 1 => '#FF6699', 2 => '#FF3300', 3 => '#FF9900', 4 => '#773399', 5 => '#009933', 15 => '#0270FF', 99 => '#007070', 100 => '#ABABAB'  );
  $BackGndColor  = array ( -1 => '#000000', 0 => '#663366', 1 => '#336666', 2 => '#000099', 3 => '#666666', 4 => '#999999', 5 => '#999999', 15 => '#999999', 99 => '#999999', 100 => '#999999'  );

  $UnRead        = doquery("SELECT * FROM {{users}} WHERE `id` = '". $user['id'] ."';", '', true);
  foreach($MessageType as $MessType => $msg_class)
  {
    $WaitingMess[$MessType] = $UnRead[$messfields[$MessType]];
    $TotalMess[$MessType]   = 0;
  }

  $UsrMess       = doquery("SELECT message_owner, message_type, COUNT(message_owner) AS message_count FROM {{messages}} WHERE `message_owner` = '".$user['id']."' GROUP BY message_owner, message_type ORDER BY message_owner ASC, message_type;");
  while ($CurMess = mysql_fetch_assoc($UsrMess)) {
    $TotalMess[$CurMess['message_type']]  = $CurMess['message_count'];
    $TotalMess[100]                      += $CurMess['message_count'];
  }

  $UsrMess       = doquery("SELECT COUNT(message_sender) AS message_count FROM {{messages}} WHERE `message_sender` = '".$user['id']."' AND message_type = 1 GROUP BY message_sender;", '', true);
  $TotalMess[-1] = intval($UsrMess['message_count']);

  switch ($MessPageMode) {
    case 'write':
      // -------------------------------------------------------------------------------------------------------
      // Envoi d'un messages
      if ( !is_numeric( $OwnerID ) ) {
        message ($lang['mess_no_ownerid'], $lang['mess_error']);
      }

      $OwnerRecord = doquery("SELECT * FROM {{users}} WHERE `id` = '".$OwnerID."';", '', true);

      if (!$OwnerRecord) {
        message ($lang['mess_no_owner']  , $lang['mess_error']);
      }

      $OwnerHome   = doquery("SELECT * FROM {{planets}} WHERE `id` = '". $OwnerRecord["id_planet"] ."';", '', true);
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
        msg_send_simple_message ( $Owner, $Sender, '', 1, $From, $Subject, $Message);
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
        doquery("DELETE FROM {{messages}} WHERE `message_owner` = '". $user['id'] ."';");
      } elseif ($DeleteWhat == 'deletemarked') {
        foreach($_POST as $Message => $Answer) {
          if (preg_match("/delmes/i", $Message) && $Answer == 'on') {
            $MessId   = str_replace("delmes", "", $Message);
            $MessHere = doquery("SELECT * FROM {{messages}} WHERE `message_id` = '". $MessId ."' AND `message_owner` = '". $user['id'] ."';");
            if ($MessHere) {
              doquery("DELETE FROM {{messages}} WHERE `message_id` = '".$MessId."';");
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
            doquery("DELETE FROM {{messages}} WHERE `message_id` = '{$MessId}' AND `message_owner` = '{$user['id']}';");
          }
        }
      }
      $MessCategory = intval($_POST['category']);

    case 'show':
      $template = gettemplate('messages', true);

      if($MessCategory == -1)
      {
        $UsrMess = doquery(
          "SELECT {{messages}}.message_id, {{messages}}.message_owner, {{users}}.id AS message_sender, {{messages}}.message_time,
            {{messages}}.message_type, {{users}}.username AS message_from, {{messages}}.message_subject, {{messages}}.message_text
         FROM
           {{messages}} LEFT JOIN {{users}} ON {{users}}.id = {{messages}}.message_owner WHERE `message_sender` = '{$user['id']}' AND `message_type` = 1 ORDER BY `message_time` DESC;");
      }
      else
      {
        $SubUpdateQry  = '';
        if ($MessCategory == 100) {
          foreach($messfields as $msg_type => $msg_field)
          {
            if($msg_type >= 0)
            {
              $SubUpdateQry .= "`{$msg_field}` = '0',";
            }
          }
          $SubUpdateQry = substr($SubUpdateQry, 0, -1);
        }
        else
        {
          $SubUpdateQry = "`{$messfields[$MessCategory]}` = '0', `{$messfields[100]}` = `{$messfields[100]}` - '{$WaitingMess[$MessCategory]}'";
          $SubSelectQry = "AND `message_type` = '{$MessCategory}'";
        }
        doquery("UPDATE {{users}} SET {$SubUpdateQry}  WHERE `id` = '{$user['id']}';");
        $UsrMess = doquery("SELECT * FROM {{messages}} WHERE `message_owner` = '{$user['id']}' {$SubSelectQry} ORDER BY `message_time` DESC;");
      };

      while ($CurMess = mysql_fetch_assoc($UsrMess)) {
        $template->assign_block_vars('messages', array(
          'ID'             => $CurMess['message_id'],
          'DATE'           => date(FMT_DATE_TIME, $CurMess['message_time']),
          'FROM'           => stripslashes($CurMess['message_from']),
          'SUBJ'           => stripslashes($CurMess['message_subject']),
          'TEXT'           => stripslashes(nl2br($CurMess['message_text'])),

          'FROM_ID'        => $CurMess['message_sender'],
          'SUBJ_SANITIZED' => htmlspecialchars($CurMess['message_subject']),
          'BG_COLOR'       => $MessCategory == 100 ? $BackGndColor[$CurMess['message_type']] : '',
          'STYLE'          => $MessCategory == -1 ? $messfields[-1] : $messfields[$CurMess['message_type']],
        ));
      }

      $template->assign_vars(array(
        "MSG_CATEGORY" => $MessCategory,
      ));

      display(parsetemplate($template), $lang['mess_pagetitle']);
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
      foreach($messfields as $MessType => $msg_class){
        $page .= "<tr>";
        $page .= "  <th colspan=\"3\"><a href=\"messages.php?mode=show&amp;messcat=". $MessType ." \"><span class=\"". $msg_class ."\">". $lang['type'][$MessType] ."</span></a></th>";
        $page .= "  <th><span class=\"". $msg_class ."\">". $WaitingMess[$MessType] ."</span></th>";
        $page .= "  <th><span class=\"". $msg_class ."\">". $TotalMess[$MessType] ."</span></th>";
        $page .= "</tr>";
      }
      $page .= "</table>";
      $page .= "</center>";
      break;
  }

  display($page, $lang['mess_pagetitle']);

?>
