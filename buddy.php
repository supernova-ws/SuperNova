<?php

/**
 * buddy.php
 *   Friend system
 *
 * v3.0 Fully rewrote by Gorlum for http://supernova.ws
 *   [!] Full rewrote from scratch
 *
 * Idea from buddy.php Created by Perberos. All rights reversed (C) 2006
**/

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

includeLang('buddy');

$userID  = intval(isset($_GET['u']) ? $_GET['u'] : $_POST['u']);
$buddyID = intval(isset($_GET['buddyID']) ? $_GET['buddyID'] : $_POST['buddyID']);
$text    = SYS_mysqlSmartEscape(sys_bbcodeParse(strip_tags($_POST['text'])));
$mode    = SYS_mysqlSmartEscape(isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode']);

if($userID){
  if($userID == $user['id'])
    message( $lang['bud_sys_cantFriendYourself'], $lang['bud_req_title'], 'buddy.php' );

  $qryRes = doquery("SELECT sender FROM {{buddy}} WHERE `sender` = '{$user['id']}' AND `owner` = '{$userID}';", 'buddy', true);
  if($qryRes)
    message( $lang['bud_sys_cantFriendAgain'], $lang['bud_req_title'], 'buddy.php' );

  if($text){
    doquery( "INSERT INTO `{{table}}` SET `sender` = '{$user['id']}', `owner` = '{$userID}', `active` = '0', `text` = '{$text}';", 'buddy' );
    message( $lang['Request_sent'], $lang['Buddy_request'], 'buddy.php');
  }else{
    $friend = doquery( "SELECT `id`, `username` FROM `{{table}}` WHERE `id` = '{$userID}'", "users", true );
    $friend = array_merge($friend, $lang);
    display( parsetemplate( gettemplate('bud_request'), $friend ), $lang['adm_an_title']);
  }
}

if($buddyID){
  $friend = doquery("SELECT * FROM {{buddy}} WHERE `id` = '{$buddyID}';", 'buddy', true);
  if($friend){
    switch($mode){
      case 'delete':
        doquery("DELETE FROM {{buddy}} WHERE `sender` = '{$friend['owner']}'  AND `owner` = {$friend['sender']};");
        doquery("DELETE FROM {{buddy}} WHERE `sender` = '{$friend['sender']}' AND `owner` = {$friend['owner']} ;");
        break;
      case 'accept':
        doquery("UPDATE {{buddy}} SET `active` = 1 WHERE `id` = '{$buddyID}'");
        doquery("DELETE FROM {{buddy}} WHERE `sender` = '{$friend['owner']}' AND `owner` = {$friend['sender']};");
        doquery("INSERT INTO {{buddy}} (`sender`, `owner`, `active`, `text`) VALUES ({$friend['owner']},{$friend['sender']},1,'Ответная дружба');");
        break;
    }
  }
}

$friendTables = array(
  0 => array('title' => $lang['bud_listTitle']    , 'empty' => $lang['bud_noFriends'] , 'column4title' => $lang['sys_status']  , 'isShowAccept' => 'class="hide"', 'req' => "sender WHERE `active` = 1 AND `owner`  = {$user['id']}"), // Friend list
  1 => array('title' => $lang['bud_req_toMeTitle'], 'empty' => $lang['bud_noReqsToMe'], 'column4title' => $lang['bud_req_text'], 'isShowAccept' => ''            , 'req' => "sender WHERE `active` = 0 AND `owner`  = {$user['id']}"), // Requests to me
  2 => array('title' => $lang['bud_req_myTitle']  , 'empty' => $lang['bud_noMyReqs']  , 'column4title' => $lang['bud_req_text'], 'isShowAccept' => 'class="hide"', 'req' => "owner  WHERE `active` = 0 AND `sender` = {$user['id']}"), // My requests
);

$parse = $lang;
$parse['dpath'] = $dpath;
foreach($friendTables as $tableID => $friendTable){
  $parse = array_merge($parse, $friendTable);
  $renderRow = '';

  $friendList = doquery("SELECT {{buddy}}.*, {{users}}.username, {{users}}.ally_name, {{users}}.onlinetime, {{users}}.galaxy, {{users}}.system, {{users}}.planet FROM {{buddy}} LEFT JOIN {{users}} ON {{users}}.id = {{buddy}}." . $friendTable['req']);
  if(mysql_num_rows($friendList))
    $parse['isShowPlaceholder'] = 'class="hide"';
  else
    $parse['isShowPlaceholder'] = '';

  while ($friend = mysql_fetch_assoc($friendList)){
    $parse = array_merge($parse, $friend);
    if(!$tableID)
      $parse['column4data'] = int_renderLastActiveHTML($time_now - $friend['onlinetime']);
    else
      $parse['column4data'] = $friend['text'];

    if($friend['sender'] == $user['id'])
      $parse['addressee'] = $friend['owner'];
    else
      $parse['addressee'] = $friend['sender'];

    $renderRow .= parsetemplate( gettemplate('bud_table_row'), $parse );
  }

  $parse['rows'] = $renderRow;
  $page .= parsetemplate( gettemplate('bud_table'), $parse );
}

$page .= MessageForm($lang['sys_hint'], $lang['bud_hint'], "", "", true);

display ($page, $lang['bud_listTitle']);
?>
