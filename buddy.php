<?php

use Buddy\BuddyModel;
use Buddy\BuddyException;
use Buddy\BuddyRoutingParams;

/**
 * buddy.php
 *   Friend system
 *
 * v3.0 Fully rewrote by Gorlum for http://supernova.ws
 *   [!] Full rewrote from scratch
 *
 * Idea from buddy.php Created by Perberos. All rights reversed (C) 2006
 * */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

/**
 * @var array $user
 */
global $user;

lng_include('buddy');

$result = array();
sn_db_transaction_start();
try {
  $cBuddy = new BuddyRoutingParams();

  $cBuddy->gc = classSupernova::$gc;
  $cBuddy->buddy_id = sys_get_param_id('buddy_id');
  $cBuddy->mode = sys_get_param_str('mode');
  $cBuddy->newFriendIdSafe = sys_get_param_id('request_user_id');
  $cBuddy->new_friend_name_unsafe = sys_get_param_str_unsafe('request_user_name');
  $cBuddy->new_request_text_unsafe = sys_get_param_str_unsafe('request_text');
  $cBuddy->playerArray = $user;
  $cBuddy->playerId = function (BuddyRoutingParams $cBuddy) {
    return $cBuddy->playerArray['id'];
  };
  $cBuddy->playerName = function (BuddyRoutingParams $cBuddy) {
    return $cBuddy->playerArray['username'];
  };
  $cBuddy->playerNameAndCoordinates = function (BuddyRoutingParams $cBuddy) {
    return "{$cBuddy->playerArray['username']} " . uni_render_coordinates($cBuddy->playerArray) ;
  };

  classSupernova::$gc->buddy->route($cBuddy);
} catch (BuddyException $e) {
  $result[] = array(
    'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
    'MESSAGE' => classLocale::$lang[$e->getMessage()],
  );
  $e->getCode() == ERR_NONE ? sn_db_transaction_commit() : sn_db_transaction_rollback();
}
sn_db_transaction_rollback();
unset($buddy);

empty($template_result) ? $template_result = array() : false;

foreach (BuddyModel::db_buddy_list_by_user(classSupernova::$gc->db, $user['id']) as $row) {
  $row['BUDDY_REQUEST'] = sys_bbcodeParse($row['BUDDY_REQUEST']);

  $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
  $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
  $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $user['id'];
  $row['BUDDY_ONLINE'] = floor((SN_TIME_NOW - $row['onlinetime']) / 60);

  $template_result['.']['buddy'][] = $row;
}

$template_result += array(
  'PAGE_HEADER'       => classLocale::$lang['buddy_buddies'],
  'PAGE_HINT'         => classLocale::$lang['buddy_hint'],
  'USER_ID'           => $user['id'],
  'REQUEST_USER_ID'   => isset($new_friend_row['id']) ? $new_friend_row['id'] : 0,
  'REQUEST_USER_NAME' => isset($new_friend_row['username']) ? $new_friend_row['username'] : '',
);

$template_result['.']['result'] = is_array($template_result['.']['result']) ? $template_result['.']['result'] : array();
$template_result['.']['result'] += $result;

$template = gettemplate('buddy', true);
$template->assign_recursive($template_result);

display($template);
