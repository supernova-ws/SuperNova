<?php

define('INSIDE', true);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('active');
function sendpassemail($emailaddress, $password) {
  global $lang;

  $parse['gameurl']  = GAMEURL;
  $email             = parsetemplate($lang['mail_welcome'], $parse);
  $status            = mymail($emailaddress, $lang['mail_title'], $email);
  return $status;
}

function mymail($to, $title, $body, $from = '') {
  $from = trim($from);

if (!$from) {
    $from = ADMINEMAIL;
  }

  $rp     = ADMINEMAIL;
  $org    = GAMEURL;

  $head   = '';
  $head  .= "Content-Type: text/plain \r\n";
  $head  .= "Date: " . date('r') . " \r\n";
  $head  .= "Return-Path: $rp \r\n";
  $head  .= "From: $from \r\n";
  $head  .= "Sender: $from \r\n";
  $head  .= "Reply-To: $from \r\n";
  $head  .= "Organization: $org \r\n";
  $head  .= "X-Sender: $from \r\n";
  $head  .= "X-Priority: 3 \r\n";
  $body   = str_replace("\r\n", "\n", $body);
  $body   = str_replace("\n", "\r\n", $body);

  return mail($to, $title, $body, $head);
}

if($_POST){
  $errors = 0;
  $errorlist = "";
  if(!is_email($_POST['email'])){
    $errorlist .= "\"".$_POST['email']."\" ".$lang['error_mail'];
    $errors++;}

if (isset($_GET['user'])){$parse['aktyw'] = $_GET['user'];}
else{$parse['aktyw'] = "0";}
$aktyw = $_POST['aktyw'];

  if($errors != 0){message($errorlist,$lang['Register']);
  }else{
    /*
    doquery("UPDATE {{table}} SET aktywnosc='0' WHERE kod_aktywujacy='{$aktyw}'",'users');
    doquery("UPDATE {{table}} SET time_aktyw='0' WHERE kod_aktywujacy='{$aktyw}'",'users');
    doquery("UPDATE {{table}} SET kod_aktywujacy='0' WHERE kod_aktywujacy='{$aktyw}'",'users');
    */
    doquery("UPDATE {{table}} SET aktywnosc='0', time_aktyw='0', kod_aktywujacy='0' WHERE kod_aktywujacy='{$aktyw}'",'users');

    $Message  = $lang['thanksforregistry'];
    if (sendpassemail($_POST['email'], "")) {
      $Message .= " (" . htmlentities($_POST["email"]) . ")";
    } else {
      $Message .= " (" . htmlentities($_POST["email"]) . ")";
      $Message .= "<br><br>". $lang['error_mailsend'] ."";
    }
    message( $Message, $lang['reg_welldone']);
  }
} else {
  // Afficher le formulaire d'enregistrement
  $parse               = $lang;
  if (isset($_GET['user'])){$parse['aktyw'] = $_GET['user'];}
  else{$parse['aktyw'] = "0";}
  $parse['servername'] = $game_config['game_name'];

  display(parsetemplate(gettemplate('active'), $parse), $lang['active'], false);
}
?>