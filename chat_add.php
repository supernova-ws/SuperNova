<?php

//print('Chat temporary disabled');
//die();

/**
 * chat_add.php
 *
 * @version 1.2s Security checks by Gorlum for http://supernova.ws
 * @version 1.2 by Ihor
 * @version 1.0
 * @copyright 2008 by e-Zobar for XNova
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

  $msg  = SYS_mysqlSmartEscape ($_POST["msg"]);
  $chat_type = SYS_mysqlSmartEscape ($_POST["chat_type"]);
  $ally_id = SYS_mysqlSmartEscape ($_POST["ally_id"]);

  // On récupère les informations du message et de l'envoyeur
  if (isset($_POST["msg"]) && isset($user['username'])) {
     if ($user['authlevel'] == 3) {
       $msg = preg_replace("#\[c=(white|blue|yellow|green|pink|red|orange)\](.+)\[/c\]#isU", $config->chat_admin_msgFormat, $msg);
     }
     $nick = addslashes ($user['username']);
     if($user['ally_id'] && !$ally_id){
       $tag = doquery("SELECT ally_tag FROM {{alliance}} WHERE id = {$user['ally_id']}", "alliance", true);
       $nick .= addslashes ("(" . $tag['ally_tag'] . ")");
     };
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
    $temp = $config->users;
    $temp[$user['id']]['chat_lastUpdate'] = $time_now;
    $config->users = $temp;
  }
?>