<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 13.04.2015
 * Time: 6:27
 */

function    db_account_by_user_id($user_id_safe, &$result = null) {
  die('db_account_by_user_id() должна быть реализована в класса auth!');
  // return sn_function_call(__FUNCTION__, array($user_id_safe, &$result));
}
/*
function sn_db_account_by_user_id($user_id_safe, &$result = null) {
  if(!$result && ($user_id_safe = round(floatval($user_id_safe)))) {
    $user = db_user_by_id($user_id_safe);
    $result = db_account_by_id($user['parent_account_id']);
  }

  return $result;
}
*/

function    db_account_set_by_user_id($user_id_safe, $set, &$result = null) {
  die('db_account_set_by_user_id() должна быть реализована в класса auth!');
  // return sn_function_call(__FUNCTION__, array($user_id_safe, $set, &$result));
}
/*
function sn_db_account_set_by_user_id($user_id_safe, $set, &$result = null) {
  if($result === null || $result === true) {
    if(($user = db_user_by_id($user_id_safe)) && !empty($user['parent_account_id'])) {
      $result = db_account_set_by_id($user['parent_account_id'], $set);
    }
  }

  return $result;
}

*/
/*
function    db_account_by_id($account_id_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($account_id_unsafe, &$result));}
function sn_db_account_by_id($account_id_unsafe, &$result = null) {
  if(empty($result) && ($account_id_safe = round(floatval($account_id_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe};", true);
  }

  return $result;
}
*/
/*
function    db_account_by_name($account_name_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($account_name_unsafe, &$result));}
function sn_db_account_by_name($account_name_unsafe, &$result = null) {
  if(empty($result) && ($account_name_safe = db_escape(trim($account_name_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}';", true);
  }

  return $result;
}
*/
/*
function    db_account_by_email($email_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($email_unsafe, &$result));}
function sn_db_account_by_email($email_unsafe, &$result = null) {
  if(empty($result) && ($email_safe = db_escape(trim($email_unsafe)))) {
    $result = doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
  }

  return $result;
}
*/
/*
function    db_account_by_user($user, &$result = null) {return sn_function_call(__FUNCTION__, array($user, &$result));}
function sn_db_account_by_user($user, &$result = null) {
  return $result = empty($user['parent_account_id']) ? false : db_account_by_id($user['parent_account_id']);
}
*/


/**
 * Получаем запись user по его аккаунту (account)
 *
 * @param      $account
 * @param null $result
 *
 * @return mixed
 */
/*
function    db_user_by_account($account, &$result = null) {return sn_function_call(__FUNCTION__, array($account, &$result));}
function sn_db_user_by_account($account, &$result = null) {
  if(empty($result) && !empty($account['account_id'])) {
    $result = classSupernova::db_get_user_by_where("`parent_account_id` = {$account['account_id']}");
  }
  return $result;
}
*/

/**
 * @param      $account_id_safe
 * @param      $set
 * @param null $result
 *
 * @return bool
 */
/*
function    db_account_set_by_id($account_id_safe, $set, &$result = null) {return sn_function_call(__FUNCTION__, array($account_id_safe, $set, &$result));}
function sn_db_account_set_by_id($account_id_safe, $set, &$result = null) {
  $result = doquery("UPDATE {{account}} SET {$set} WHERE `account_id` = {$account_id_safe};");
  return empty($result) ? false : true;
}
*/
