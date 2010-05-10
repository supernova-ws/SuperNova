<?php

//print('Chat temporary disabled');
//die();

/**
 * chat_add.php
 *
 * @version 1.0
 * @version 1.2 by Ihor
 * @copyright 2008 by e-Zobar for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$xnova_root_path = './';
include($xnova_root_path . 'extension.inc');
include($xnova_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}
  // On récupère les informations du message et de l'envoyeur
  if (isset($_POST["msg"]) && isset($user['username'])) {
     $msg  = SYS_mysqlSmartEscape ($_POST["msg"]);
     if ($user['authlevel'] == 3) {
       $msg = preg_replace("#\[c=(white|blue|yellow|green|pink|red|orange)\](.+)\[/c\]#isU", $config->chat_admin_msgFormat, $msg);
     }
     $nick = addslashes ($user['username']);
     $chat_type = addslashes ($_POST["chat_type"]);
     $ally_id = addslashes ($_POST["ally_id"]);
     $nick = addslashes ($user['username']);
     $msg = iconv('UTF-8', 'CP1251', $msg); // CHANGE IT !!!!!!!!!!!
  }
  else {
     $msg="";
     $nick="";
  }
  if ($msg!="" && $nick!="") {
    if($chat_type=="ally" && $ally_id>""){
      $query = doquery("INSERT INTO {{table}}(user, ally_id,message, timestamp) VALUES ('".$nick."','".$ally_id."','".$msg."', '".time()."')", "chat");
    }else{
      $query = doquery("INSERT INTO {{table}}(user, ally_id, message, timestamp) VALUES ('".$nick."','0', '".$msg."', '".time()."')", "chat");
    }
  }
?>