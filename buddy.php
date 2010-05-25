<?php

// @v2.0s Security checks by Gorlum for http://supernova.ws
//buddy.php - version 2.0
// Friend system

define('INSIDE'  , true );
define('INSTALL' , false);

$ugamela_root_path = './';
include( $ugamela_root_path . 'extension.inc' );
include( $ugamela_root_path . 'common.' . $phpEx );

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);
  includeLang('buddy');

$a = intval($_GET['a']);
$e = intval($_GET['e']);
$s = intval($_GET['s']);
$u = intval( $_GET['u'] );
$bid = intval( $_GET['bid'] );
$POST_s = intval($_POST["s"]);
$POST_u = intval($_POST["u"]);
$POST_a = intval($_POST["a"]);
$POST_e = intval($_POST["e"]);
$POST_text = SYS_mysqlSmartEscape($_POST['text']);

if ( $s == 1 && isset( $bid ) ) {
  // Effacer une entree de la liste d'amis

  $buddy = doquery( "SELECT * FROM `{{table}}` WHERE `id` = '".$bid."';", 'buddy', true );
  if ( $buddy['owner'] == $user['id'] ) {
    if ( $buddy['active'] == 0 && $a == 1 ) {
      doquery( "DELETE FROM `{{table}}` WHERE `id` = '".$bid."';", 'buddy' );
    } elseif ( $buddy['active'] == 1 ) {
      doquery( "DELETE FROM `{{table}}` WHERE `id` = '".$bid."';", 'buddy' );
    } elseif ( $buddy['active'] == 0 ) {
      doquery( "UPDATE `{{table}}` SET `active` = '1' WHERE `id` = '".$bid."';", 'buddy' );
    }
  } elseif ( $buddy['sender'] == $user['id'] ) {
    doquery( "DELETE FROM `{{table}}` WHERE `id` = '".$bid."';", 'buddy' );
  }
} elseif ( $POST_s == 3 && $POST_a == 1 && $POST_e == 1 && isset( $POST_u ) ) {
  // Traitement de l'enregistrement de la demande d'entree dans la liste d'amis
  $uid = $user["id"];
  $u = $POST_u;

  $buddy = doquery( "SELECT * FROM `{{table}}` WHERE `sender` = '{$uid}' AND `owner` = '{$u}' OR `sender` = '{$u}' AND `owner` = '{$uid}'", 'buddy', true );

  if ( !$buddy ) {
    if ( strlen( $POST_text ) > 5000 ) {
      message( "Текст не может содержать больше чем 5 000 символов.", "Ошибка" );
    }
    $text = strip_tags( $POST_text );
    doquery( "INSERT INTO `{{table}}` SET `sender` = '{$uid}', `owner` = '{$u}', `active` = '0', `text` = '{$text}'", 'buddy' );
    message( $lang['Request_sent'], $lang['Buddy_request'], 'buddy.php' );
  } else {
    message( $lang['A_request_exists_already_for_this_user'], $lang['Buddy_request'] );
  }
}

$page = "<br>";

if ( $a == 2 && isset( $u ) ) {
  // Saisie texte de demande d'entree dans la liste d'amis
  $u = doquery( "SELECT * FROM `{{table}}` WHERE `id` ='$u'", "users", true );
  if ( isset( $u ) && $u["id"] != $user["id"] ) {
    $page .= "
    <script src=\"scripts/cntchar.js\" type=\"text/javascript\"></script>
    <script src=\"scripts/win.js\" type=\"text/javascript\"></script>
    <center>
      <form action=buddy.php method=post>
      <input type=hidden name=a value=1>
      <input type=hidden name=s value=3>
      <input type=hidden name=e value=1>
      <input type=hidden name=u value=" . $u["id"] . ">
      <table width=600>
      <tr>
        <td class=c colspan=4>{$lang['Buddy_request']}</td>
      </tr>
      <tr>
        <th>{$lang['Player']}</th>
        <th>" . $u["username"] . "</th>
      </tr>
      <tr>
        <th>{$lang['Request_text']}<br> (<span id=\"cntChars\">0</span> / 2000 {$lang['characters']})</th>
        <th width=400><textarea name=text cols=40 rows=12 size=100 onKeyUp=\"javascript:cntchar(2000)\"></textarea></th>
      </tr>
      <tr>
        <td class=c><a href=\"buddy.php\">{$lang['Back']}</a></td>
        <td class=c><p align=center><input type=submit value='{$lang['Send']}'></p></td>
      </tr>
    </table></form>
    </center>
    </body>
    </html>";
    display( $page, 'buddy' );
  } elseif ( $u["id"] == $user["id"] ) {
    message( $lang['You_cannot_ask_yourself_for_a_request'], $lang['Buddy_request'] );
  }
}
// con a indicamos las solicitudes y con e las distiguimos
if ( $a == 1 )
  $TableTitle = ( $e == 1 ) ? $lang['My_requests']:$lang['Anothers_requests'];
else
  $TableTitle = $lang['Buddy_list'];

$page .= "
<table width=600>
<tr>
  <td class=c colspan=6>{$TableTitle}</td>
</tr>";

if ( !isset( $a ) ) {
  $page .= "
  <tr>
    <th colspan=6><a href=buddy.php?a=1>{$lang['Requests']}</a></th>
  </tr><tr>
    <th colspan=6><a href=buddy.php?a=1&e=1>{$lang['My_requests']}</a></th>
  </tr><tr>
    <td class=c></td>
    <td class=c>{$lang['Name']}</td>
    <td class=c>{$lang['Alliance']}</td>
    <td class=c>{$lang['Coordinates']}</td>
    <td class=c>{$lang['Position']}</td>
    <td class=c></td>
  </tr>";
}

if ( $a == 1 ) {
  $query = ( $e == 1 ) ? "WHERE `active` = '0' AND `sender` = " . $user["id"] : "WHERE `active` = '0' AND `owner` = " . $user["id"];
} else {
  $query = "WHERE `active` = '1' AND `sender` = " . $user["id"] . " OR `active` = '1' AND `owner` = " . $user["id"];
}
$buddyrow = doquery( "SELECT * FROM `{{table}}` " . $query, 'buddy' );

while ( $b = mysql_fetch_array( $buddyrow ) ) {
  // para solicitudes
  if ( !isset( $i ) && isset( $a ) ) {
    $page .= "
    <tr>
      <td class=c></td>
      <td class=c>{$lang['User']}</td>
      <td class=c>{$lang['Alliance']}</td>
      <td class=c>{$lang['Coordinates']}</td>
      <td class=c>{$lang['Text']}</td>
      <td class=c></td>
    </tr>";
  }

  $i++;
  $uid = ( $b["owner"] == $user["id"] ) ? $b["sender"] : $b["owner"];
  $u = doquery( "SELECT id,username,galaxy,system,planet,onlinetime,ally_id,ally_name FROM {{table}} WHERE `id` = " . $uid, "users", true );
  if ( $u["ally_id"] != 0 ) {
    //$allyrow = doquery("SELECT id,ally_tag FROM `{{table}}` WHERE id=".$u["ally_id"],"alliance",true);
    $UserAlly .= "<a target=\"Hauptframe\" href=alliance.php?mode=ainfo&a=" . $u["ally_id"] . ">" . $u["ally_name"] . "</a>";
  }

  if ( isset( $a ) ) {
    $LastOnline = $b["text"];
  } else {
    $LastOnline = "<font color=";
    if ( $u["onlinetime"] + 60 * 10 >= time() ) {
      $LastOnline .= "lime>{$lang['On']}";
    } elseif ( $u["onlinetime"] + 60 * 20 >= time() ) {
      $LastOnline .= "yellow>{$lang['15_min']}";
    } else {
      $LastOnline .= "red>{$lang['Off']}";
    }
    $LastOnline .= "</font>";
  }

  if ( isset( $a ) && isset( $e ) ) {
    $UserCommand = "<a href=?s=1&bid=" . $b["id"] . ">{$lang['Delete_request']}</a>";
  } elseif ( isset( $a ) ) {
    $UserCommand = "<a href=?s=1&bid=" . $b["id"] . ">{$lang['Ok']}</a><br/>";
    $UserCommand .= "<a href=buddy.php?a=1&s=1&bid=" . $b["id"] . ">{$lang['Reject']}</a></a>";
  } else {
    $UserCommand = "<a href=buddy.php?s=1&bid=" . $b["id"] . ">{$lang['Delete']}</a>";
  }

  $page .= "
  <tr>
    <th width=20>" . $i . "</th>
    <th><a target=\"Hauptframe\" href=messages.php?mode=write&id=" . $u["id"] . ">" . $u["username"] . "</a></th>
    <th>{$UserAlly}</th>
    <th><a target=\"Hauptframe\" href=\"galaxy.php?mode=3&galaxy=" . $u["galaxy"] . "&system=" . $u["system"] . "\">" . $u["galaxy"] . ":" . $u["system"] . ":" . $u["planet"] . "</a></th>
    <th>{$LastOnline}</th>
    <th>{$UserCommand}</th>
  </tr>";
}

if ( !isset( $i ) ) {
  $page .= "
  <tr>
    <th colspan=6>{$lang['There_is_no_request']}</th>
  </tr>";
}

if ( $a == 1 ) {
  $page .= "
  <tr>
    <td colspan=6 class=c><a href=buddy.php>{$lang['Back']}</a></td>
  </tr>";
}

$page .= "
  </table>
  </center>";

display ($page, $lang['Buddy_list']);

// Created by Perberos. All rights reversed (C) 2006
?>
