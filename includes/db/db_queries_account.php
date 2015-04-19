<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 13.04.2015
 * Time: 6:27
 */

function    db_account_by_id($account_id_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($account_id_unsafe, &$result));}
function sn_db_account_by_id($account_id_unsafe, &$result = null) {
  if(empty($result) && ($account_id_safe = round(floatval($account_id_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe};", true);
  }

  return $result;
}
function    db_account_by_name($account_name_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($account_name_unsafe, &$result));}
function sn_db_account_by_name($account_name_unsafe, &$result = null) {
  if(empty($result) && ($account_name_safe = db_escape(trim($account_name_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}';", true);
  }

  return $result;
}
function    db_account_by_email($email_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($email_unsafe, &$result));}
function sn_db_account_by_email($email_unsafe, &$result = null) {
  if(empty($result) && ($email_safe = db_escape(trim($email_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
  }

  return $result;
}
function    db_account_by_user($user, &$result = null) {return sn_function_call(__FUNCTION__, array($user, &$result));}
function sn_db_account_by_user($user, &$result = null) {
  return $result = empty($user['parent_account_id']) ? false : db_account_by_id($user['parent_account_id']);
}
function    db_account_by_user_id($user_id_safe, &$result = null) {return sn_function_call(__FUNCTION__, array($user_id_safe, &$result));}
function sn_db_account_by_user_id($user_id_safe, &$result = null) {
  if(!$result && ($user_id_safe = round(floatval($user_id_safe)))) {
    $user = db_user_by_id($user_id_safe);
    $result = db_account_by_id($user['parent_account_id']);
  }

  return $result;
}





function db_account_create($field_set, $user_id) {
  if($account = db_field_set_create('account', $field_set)) {
    db_user_set_by_id($user_id, "`parent_account_id` = {$account['account_id']}");
  }
  return $account;
}



function    db_account_set_by_id($account_id_safe, $set, &$result = null) {return sn_function_call(__FUNCTION__, array($account_id_safe, $set, &$result));}
function sn_db_account_set_by_id($account_id_safe, $set) {
  return doquery("UPDATE {{account}} SET {$set} WHERE `account_id` = {$account_id_safe};");
}

function    db_account_set_by_user_id($user_id_safe, $set, &$result = null) {return sn_function_call(__FUNCTION__, array($user_id_safe, $set, &$result));}
function sn_db_account_set_by_user_id($user_id_safe, $set, &$result = null) {
  $local_result = false;
  if(($user = db_user_by_id($user_id_safe)) && !empty($user['parent_account_id'])) {
    $local_result = db_account_set_by_id($user['parent_account_id'], $set);
  }

  return ($result || $result === null) || $local_result;
}
