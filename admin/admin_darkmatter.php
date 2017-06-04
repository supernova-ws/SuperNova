<?php

use Exceptions\ExceptionSnLocalized;

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

messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

/**
 * @param $lang
 * @param $user
 *
 * @throws ExceptionSnLocalized
 *
 */
function admin_dark_matter_model($lang, $user) {
  $points = sys_get_param_float('points');
  $reason_unsafe = sys_get_param_str_unsafe('reason');
  $userIdOrName_unsafe = sys_get_param_str_unsafe('id_user');

  // If no points and no username - nothing to do
  if (!$points && !$userIdOrName_unsafe) {
    return;
  }

  if (!$points) {
    throw new ExceptionSnLocalized('adm_dm_no_quant', ERR_ERROR);
  }
  if (empty($userIdOrName_unsafe)) {
    throw new ExceptionSnLocalized('adm_dm_no_dest', ERR_ERROR);
  }

  $row = db_user_by_id($userIdOrName_unsafe, false, 'id, username');
  if (empty($row['id'])) {
    $row = db_user_by_username($userIdOrName_unsafe, false, 'id, username', true, true);
  }

  if (empty($row['id'])) {
    throw new ExceptionSnLocalized('adm_dm_user_none', ERR_ERROR, null, array($userIdOrName_unsafe));
  }

  // Does anything post to DB?
  if (!rpg_points_change(
    $row['id'],
    RPG_ADMIN,
    $points,
    sprintf($lang['adm_matter_change_log_record'], $row['id'], $row['username'], $user['id'], $user['username'], $reason_unsafe)
  )
  ) {
    // No? We will say it to user...
    throw new ExceptionSnLocalized('adm_dm_add_err', ERR_ERROR);
  }

  throw new ExceptionSnLocalized(
    'adm_dm_user_added',
    ERR_NONE,
    null,
    array($row['username'], $row['id'], pretty_number($points))
  );
}


/**
 * @param $template |null $template
 */
function admin_dark_matter_view($template = null) {
  global $user, $lang;

  $userIdOrName_unsafe = sys_get_param_str_unsafe('id_user');
  $points = sys_get_param_float('points');
  $reason_unsafe = sys_get_param_str_unsafe('reason');

  $template = gettemplate("admin/admin_darkmatter", true);

  try {
    admin_dark_matter_model($lang, $user);
  } catch (ExceptionSnLocalized $e) {
    $template->assign_block_vars('result', array(
      'MESSAGE' => $e->getMessageLocalized(),
      'STATUS'  => $e->getCode() ? $e->getCode() : ERR_NONE,
    ));

    if ($e->getCode() != ERR_NONE) {
      $template->assign_vars(array(
        'ID_USER' => $userIdOrName_unsafe,
        'POINTS'  => $points,
        'REASON'  => $reason_unsafe,
      ));
    };

  }

  display($template, $lang['adm_dm_title']);

  return $template;
}

admin_dark_matter_view();
