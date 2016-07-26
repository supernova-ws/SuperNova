<?php

/**
 * buddy.php
 *   Friend system
 *
 * v3.0 Fully rewrote by Gorlum for http://supernova.ws
 *   [!] Full rewrote from scratch
 *
 * Idea from buddy.php Created by Perberos. All rights reversed (C) 2006
 * */
use Buddy\Buddy;
use Buddy\BuddyException;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

class_exists('Buddy\Buddy');


/**
 * @var array $user
 */
global $user;

lng_include('buddy');

$result = array();
sn_db_transaction_start();
try {
  $cBuddy = new \Buddy\BuddyRoutingParams(array(
    'gc'                     => classSupernova::$gc,
    'buddy_id'               => sys_get_param_id('buddy_id'),
    'mode'                   => sys_get_param_str('mode'),
    'new_friend_id_safe'     => sys_get_param_id('request_user_id'),
    'new_friend_name_unsafe' => sys_get_param_str_unsafe('request_user_name'),
    'new_request_text'       => sys_get_param_str('request_text'),
    'user'                   => $user,
  ));

//  $className = classSupernova::$gc->buddyClass;
//  $className->route($cBuddy);
//  $className = classSupernova::$gc->buddyClass;
  classSupernova::$gc->buddy->route($cBuddy);

//  $buddy_id = sys_get_param_id('buddy_id');
//  if ($buddy_id) {
//    /**
//     * @var Buddy\Buddy\BuddyModel $buddy
//     */
//    $buddy = classSupernova::$gc->dbRowOperator->getById(classSupernova::$gc->buddy, $buddy_id);
//
//    if ($buddy->isEmpty()) {
//      throw new Exception('buddy_err_not_exist', ERR_ERROR);
//    }
//
//    $mode = sys_get_param_str('mode');
//    switch ($mode) {
//      case 'accept':
//        $buddy->accept($user);
//      break;
//      case 'delete':
//        $buddy->decline($user);
//      break;
//    }
//  } else {
//    // New request?
//    // Checking for user ID - in case if it was request from outside buddy system
//    $new_friend_id_safe = sys_get_param_id('request_user_id');
//    $new_friend_name = sys_get_param_str_unsafe('request_user_name');
//    if ($new_friend_id_safe || $new_friend_name) {
//      $buddy = classSupernova::$gc->buddy;
//      $new_request_text = sys_get_param_str('request_text');
//      $buddy->beFriend($user, $new_friend_id_safe, $new_friend_name, $new_request_text);
//    }
//  }
} catch (BuddyException $e) {
  $result[] = array(
    'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
    'MESSAGE' => classLocale::$lang[$e->getMessage()],
  );
  $e->getCode() == ERR_NONE ? sn_db_transaction_commit() : sn_db_transaction_rollback();
}
sn_db_transaction_rollback();
unset($buddy);

// TODO - Это просто заглушка. Дойдут руки - разобраться, в чём проблема
sn_db_transaction_rollback();

empty($template_result) ? $template_result = array() : false;

foreach (Buddy::db_buddy_list_by_user(classSupernova::$gc->db, $user['id']) as $row) {
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
