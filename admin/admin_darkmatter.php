<?php

/**
 * dark_matter.php
 *
 * Adjust Dark Matter quantity
 *
 * @version 1.0 (c) copyright 2010 by Gorlum for http://supernova.ws/
 *
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate("admin/admin_darkmatter", true);

$message = '';
$message_status = ERR_ERROR;

if($points = sys_get_param_float('points')) {
// If points not empty...
  if($username = sys_get_param_str_unsafe('id_user')) {
    $row = db_user_by_id($username, false, 'id, username', true, true);
    if(!isset($row['id'])) {
      $row = db_user_by_username($username, false, 'id, username', true, true);
    }
    if(is_array($row) && isset($row['id'])) {
      // Does anything post to DB?
      if(rpg_points_change($row['id'], RPG_ADMIN, $points, sprintf($lang['adm_matter_change_log_record'],
        $row['id'], db_escape($row['username']),
        $user['id'], db_escape($user['username']),
        db_escape(sys_get_param_str('reason'))
      ))) {
        $message = sprintf($lang['adm_dm_user_added'], $row['username'], $row['id'], $points);
        $isNoError = true;
        $message_status = ERR_NONE;
      } else {
        // No? We will say it to user...
        $message = $lang['adm_dm_add_err'];
      }
    }
  } else {
    // Points not empty but destination is not set - this means error
    $message = $lang['adm_dm_no_dest'];
  }
} elseif($id_user) {
  // Points is empty but destination is set - this again means error
  $message = $lang['adm_dm_no_quant'];
}

if(!$isNoError) {
  $template->assign_vars(array(
    'ID_USER' => $id_user,
    'POINTS' => $points,
    'REASON' => $reason,
  ));
};

if($message) {
  $template->assign_block_vars('result', array('MESSAGE' => $message, 'STATUS' => $message_status ? $message_status : ERR_NONE));
}

display($template, $lang['adm_dm_title'], false, '', true);
