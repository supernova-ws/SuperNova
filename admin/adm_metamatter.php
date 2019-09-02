<?php

/**
 * adm_meta_matter.php
 *
 * Adjust Meta Matter quantity
 *
 * @version 2.0 (c) copyright 2013-2017 by Gorlum for http://supernova.ws
 *
 */

use Common\Exceptions\ExceptionSnLocalized;

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if(!SN::$gc->modules->countModulesInGroup('payment')) {
  sys_redirect(SN_ROOT_VIRTUAL . 'admin/overview.php');
}

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

/**
 * @param classLocale $lang
 * @param array       $user
 * @param string      $accountIdOrName_unsafe
 * @param string      $playerIdOrName_unsafe
 * @param float       $points
 * @param string      $reason_unsafe
 * @param bool        $confirmed
 *
 * @throws ExceptionSnLocalized
 */
function admin_meta_matter_model($lang, $user, $accountIdOrName_unsafe, $playerIdOrName_unsafe, $points, $reason_unsafe, $confirmed) {
  // If no points and no username - nothing to do
  if (!$points && !$playerIdOrName_unsafe && !$accountIdOrName_unsafe) {
    return;
  }

  if (!$points) {
    throw new ExceptionSnLocalized('adm_mm_err_points_empty', ERR_ERROR);
  }

  $account = new Account(SN::$auth->account->db);

  if (!empty($accountIdOrName_unsafe)) {
    if (
      !$account->db_get_by_id($accountIdOrName_unsafe)
      &&
      !$account->db_get_by_name($accountIdOrName_unsafe)
      &&
      !$account->db_get_by_email($accountIdOrName_unsafe)
    ) {
      throw new ExceptionSnLocalized('adm_mm_err_account_not_found', ERR_ERROR);
    }
  } elseif (!empty($playerIdOrName_unsafe)) {
    $row = dbPlayerByIdOrName($playerIdOrName_unsafe);
    if (empty($row['id'])) {
      throw new ExceptionSnLocalized('adm_mm_err_player_not_found', ERR_ERROR, null, array($playerIdOrName_unsafe));
    }

    if (!$account->dbGetByPlayerId($row['id'])) {
      throw new ExceptionSnLocalized('adm_mm_err_player_no_account', ERR_ERROR, null, array($playerIdOrName_unsafe));
    }
  } else {
    throw new ExceptionSnLocalized('adm_mm_err_account_and_player_empty', ERR_ERROR);
  }

  $sprintfPayload = array(
    $account->account_name,
    $account->account_id,
    HelperString::numberFloorAndFormat($points),
    !empty($row['id']) ? $row['id'] : 0,
    !empty($row['username']) ? $row['username'] : ''
  );

  if ($confirmed) {
    if (empty($account->metamatter_change(
      RPG_ADMIN,
      $points,
      sprintf(
        $lang['adm_mm_msg_change_mm_log_record'],
        $account->account_id,
        $account->account_name,
        $user['id'],
        $user['username'],
        $reason_unsafe,
        core_auth::$main_provider->account->account_id,
        core_auth::$main_provider->account->account_name,
        !empty($row['id']) ? $row['id'] : 0,
        !empty($row['username']) ? $row['username'] : ''
      )
    ))) {
      throw new ExceptionSnLocalized($lang['adm_mm_err_mm_change_failed'], ERR_ERROR);
    }

    throw new ExceptionSnLocalized('adm_mm_msg_mm_changed', ERR_NONE, null, $sprintfPayload);
  } else {
    throw new ExceptionSnLocalized('adm_mm_msg_confirm_mm_change', ERR_WARNING, null, $sprintfPayload);
  }

}


/**
 * @param array       $user
 * @param classLocale $lang
 */
function admin_meta_matter_view($user, $lang) {
  $accountIdOrName_unsafe = sys_get_param_str_unsafe('accountId');
  $playerIdOrName_unsafe = sys_get_param_str_unsafe('playerId');
  $points = sys_get_param_float('points');
  $reason_unsafe = sys_get_param_str_unsafe('reason');
  $confirmed = sys_get_param('confirm_mm_change');
  $confirmed = !empty($confirmed); // can't use empty() or isset() with function result in PHP 5.3

  $template = SnTemplate::gettemplate("admin/adm_metamatter", true);

  try {
    admin_meta_matter_model($lang, $user, $accountIdOrName_unsafe, $playerIdOrName_unsafe, $points, $reason_unsafe, $confirmed);
  } catch (ExceptionSnLocalized $e) {
    $template->assign_block_vars('result', array(
      'MESSAGE' => $e->getMessageLocalized(),
      'STATUS'  => $e->getCode() ? $e->getCode() : ERR_NONE,
    ));

    if ($e->getCode() != ERR_NONE) {
      $template->assign_vars(array(
        'ACCOUNT_ID' => sys_safe_output($accountIdOrName_unsafe),
        'PLAYER_ID'  => sys_safe_output($playerIdOrName_unsafe),
        'POINTS'     => $points,
        'REASON'     => sys_safe_output($reason_unsafe),
      ));
    };

    if ($e->getCode() == ERR_WARNING) {
      $template->assign_vars(array(
        'NEED_CONFIRMATION' => 1,
      ));
    }
  }

  SnTemplate::display($template, $lang['adm_dm_title']);
}

global $user, $lang;

admin_meta_matter_view($user, $lang);
