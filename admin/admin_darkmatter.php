<?php

/**
 * admin_darkmatter.php
 *
 * Adjust Dark Matter quantity
 *
 * @version 2.0 (c) copyright 2010-2017 by Gorlum for http://supernova.ws/
 *
 */

use Common\Exceptions\ExceptionSnLocalized;

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

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
  $playerIdOrName_unsafe = sys_get_param_str_unsafe('playerId');

  // If no points and no username - nothing to do
  if (!$points && !$playerIdOrName_unsafe) {
    return;
  }

  if (!$points) {
    throw new ExceptionSnLocalized('adm_dm_no_quant', ERR_ERROR);
  }
  if (empty($playerIdOrName_unsafe)) {
    throw new ExceptionSnLocalized('adm_dm_no_dest', ERR_ERROR);
  }

  $row = dbPlayerByIdOrName($playerIdOrName_unsafe);
  if (empty($row['id'])) {
    throw new ExceptionSnLocalized('adm_dm_user_none', ERR_ERROR, null, array($playerIdOrName_unsafe));
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
    array($row['username'], $row['id'], HelperString::numberFloorAndFormat($points))
  );
}


/**
 * @param $template |null $template
 */
function admin_dark_matter_view($template = null) {
  global $user, $lang;

  $playerIdOrName_unsafe = sys_get_param_str_unsafe('playerId');
  $points = sys_get_param_float('points');
  $reason_unsafe = sys_get_param_str_unsafe('reason');

  $template = SnTemplate::gettemplate("admin/admin_darkmatter", true);

  try {
    admin_dark_matter_model($lang, $user);
  } catch (ExceptionSnLocalized $e) {
    $template->assign_block_vars('result', array(
      'MESSAGE' => $e->getMessageLocalized(),
      'STATUS'  => $e->getCode() ? $e->getCode() : ERR_NONE,
    ));

    if ($e->getCode() != ERR_NONE) {
      $template->assign_vars(array(
        'PLAYER_ID' => sys_safe_output($playerIdOrName_unsafe),
        'POINTS'    => $points,
        'REASON'    => sys_safe_output($reason_unsafe),
      ));
    };

  }

  SnTemplate::display($template, $lang['adm_dm_title']);

  return $template;
}

admin_dark_matter_view();
