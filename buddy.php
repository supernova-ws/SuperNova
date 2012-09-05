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
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('buddy');

$result = array();
try
{
  doquery('START TRANSACTION');
  if($buddy_id = sys_get_param_id('buddy_id'))
  {
    $buddy_row = doquery("SELECT BUDDY_SENDER_ID, BUDDY_OWNER_ID, BUDDY_STATUS FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1 FOR UPDATE;", true);
    if(!is_array($buddy_row))
    {
      throw new exception('buddy_err_not_exist', ERR_ERROR);
    }

    switch($mode = sys_get_param_str('mode'))
    {
      case 'accept':
        if($buddy_row['BUDDY_SENDER_ID'] == $user['id'])
        {
          throw new exception('buddy_err_accept_own', ERR_ERROR);
        }

        if($buddy_row['BUDDY_OWNER_ID'] != $user['id'])
        {
          throw new exception('buddy_err_accept_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE)
        {
          throw new exception('buddy_err_accept_already', ERR_WARNING);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED)
        {
          throw new exception('buddy_err_accept_denied', ERR_ERROR);
        }

        doquery("UPDATE {{buddy}} SET `BUDDY_STATUS` = " . BUDDY_REQUEST_ACTIVE . " WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
        if(mysql_affected_rows($link))
        {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], $time_now, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_accept_title'],
            sprintf($lang['buddy_msg_accept_text'], $user['username']));
          doquery('COMMIT');
          throw new exception('buddy_err_accept_none', ERR_NONE);
        }
        else
        {
          throw new exception('buddy_err_accept_internal', ERR_ERROR);
        }
      break;

      case 'delete':
        if($buddy_row['BUDDY_SENDER_ID'] != $user['id'] && $buddy_row['BUDDY_OWNER_ID'] != $user['id'])
        {
          throw new exception('buddy_err_delete_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) // Existing friendship
        {
          $ex_friend_id = $buddy_row['BUDDY_SENDER_ID'] == $user['id'] ? $buddy_row['BUDDY_OWNER_ID'] : $buddy_row['BUDDY_SENDER_ID'];

          msg_send_simple_message($ex_friend_id, $user['id'], $time_now, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_unfriend_title'],
            sprintf($lang['buddy_msg_unfriend_text'], $user['username']));

          doquery("DELETE FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          doquery('COMMIT');
          throw new exception('buddy_err_unfriend_none', ERR_NONE);
        }
        elseif($buddy_row['BUDDY_SENDER_ID'] == $user['id']) // Player's outcoming request - either denied or waiting
        {
          doquery("DELETE FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          doquery('COMMIT');
          throw new exception('buddy_err_delete_own', ERR_NONE);
        }
        elseif($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_WAITING) // Deny incoming request
        {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], $time_now, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_deny_title'],
            sprintf($lang['buddy_msg_deny_text'], $user['username']));

          doquery("UPDATE {{buddy}} SET `BUDDY_STATUS` = " . BUDDY_REQUEST_DENIED . " WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          doquery('COMMIT');
          throw new exception('buddy_err_deny_none', ERR_NONE);
        }
      break;
    }
  }

  // New request?
  // Checking for user ID - in case if it was request from outside buddy system
  if($new_friend_id = sys_get_param_id('request_user_id'))
  {
    $new_friend_row = doquery("SELECT `id`, `username` FROM {{users}} WHERE `id` = {$new_friend_id} LIMIT 1 FOR UPDATE;", true);
  }
  elseif($new_friend_name = sys_get_param_str('request_user_name'))
  {
    $new_friend_row = doquery("SELECT `id`, `username` FROM {{users}} WHERE `username` = '{$new_friend_name}' LIMIT 1 FOR UPDATE;", true);
  }

  if($new_friend_row['id'] == $user['id'])
  {
    unset($new_friend_row);
    throw new exception('buddy_err_adding_self', ERR_ERROR);
  }

  // Checking for user name & request text - in case if it was request to adding new request
  if(isset($new_friend_row['id']) && ($new_request_text = sys_get_param_str('request_text')))
  {
    $check_relation = doquery("SELECT `BUDDY_ID` FROM {{buddy}} WHERE
      (`BUDDY_SENDER_ID` = {$user['id']} AND `BUDDY_OWNER_ID` = {$new_friend_row['id']})
      OR
      (`BUDDY_SENDER_ID` = {$new_friend_row['id']} AND `BUDDY_OWNER_ID` = {$user['id']})
      LIMIT 1 FOR UPDATE;"
    , true);
    if(isset($check_relation['BUDDY_ID']))
    {
      throw new exception('buddy_err_adding_exists', ERR_WARNING);
    }

    msg_send_simple_message($new_friend_row['id'], $user['id'], $time_now, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_adding_title'],
      sprintf($lang['buddy_msg_adding_text'], $user['username']));

    doquery($q = "INSERT INTO {{buddy}} SET `BUDDY_SENDER_ID` = {$user['id']}, `BUDDY_OWNER_ID` = {$new_friend_row['id']}, `BUDDY_REQUEST` = '{$new_request_text}';");
    doquery('COMMIT');
    throw new exception('buddy_err_adding_none', ERR_NONE);
  }
}
catch(exception $e)
{
  doquery('ROLLBACK');
  $result[] = array(
    'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
    'MESSAGE' => $lang[$e->getMessage()],
  );
}




$query = doquery("
SELECT
  b.*,
--  b.BUDDY_ACTIVE + IF(b.BUDDY_OWNER_ID = {$user['id']}, " . BUDDY_REQUEST_INCOMING . ", " . BUDDY_REQUEST_OUTCOMING . ") AS BUDDY_REQUEST_STATUS,
  IF(b.BUDDY_OWNER_ID = {$user['id']}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID) AS BUDDY_USER_ID,
  u.username AS BUDDY_USER_NAME,
  p.name AS BUDDY_PLANET_NAME,
  p.galaxy AS BUDDY_PLANET_GALAXY,
  p.system AS BUDDY_PLANET_SYSTEM,
  p.planet AS BUDDY_PLANET_PLANET,
  a.id AS BUDDY_ALLY_ID,
  a.ally_name AS BUDDY_ALLY_NAME,
  u.onlinetime
FROM {{buddy}} AS b
  LEFT JOIN {{users}} AS u ON u.id = IF(b.BUDDY_OWNER_ID = {$user['id']}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID)
  LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id = id_planet
  LEFT JOIN {{alliance}} AS a ON a.id = u.ally_id
WHERE (`BUDDY_OWNER_ID` = {$user['id']}) OR `BUDDY_SENDER_ID` = {$user['id']}
ORDER BY BUDDY_STATUS, BUDDY_ID");
// WHERE (`BUDDY_OWNER_ID` = {$user['id']} AND b.`BUDDY_STATUS` != " . BUDDY_REQUEST_DENIED . ") OR `BUDDY_SENDER_ID` = {$user['id']}
while($row = mysql_fetch_assoc($query))
{
  $row['BUDDY_REQUEST'] = sys_bbcodeParse($row['BUDDY_REQUEST']);

  $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
  $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
  $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $user['id'];
  $row['BUDDY_ONLINE'] = floor(($time_now - $row['onlinetime']) / 60);

  $template_result['.']['buddy'][] = $row;
}

$template_result += array(
  'PAGE_HEADER' => $lang['buddy_buddies'],
  'PAGE_HINT' => $lang['buddy_hint'],
  'USER_ID' => $user['id'],
  'REQUEST_USER_ID' => isset($new_friend_row['id']) ? $new_friend_row['id'] : 0,
  'REQUEST_USER_NAME' => isset($new_friend_row['username']) ? $new_friend_row['username'] : '',
);

$template_result['.']['result'] = is_array($template_result['.']['result']) ? $template_result['.']['result'] : array();
$template_result['.']['result'] += $result;

$template = gettemplate('buddy', true);
$template->assign_recursive($template_result);

display($template);

?>
