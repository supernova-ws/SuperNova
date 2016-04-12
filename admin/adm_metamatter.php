<?php

/**
 * dark_matter.php
 *
 * Adjust Dark Matter quantity
 *
 * @version 1.0 (c) copyright 2013 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if (!sn_module_get_active_count('payment')) {
  sys_redirect(SN_ROOT_VIRTUAL . 'admin/overview.php');
}

if ($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate("admin/adm_metamatter", true);

$message = '';
$message_status = ERR_ERROR;

if ($points = sys_get_param_float('points')) {
  try {
    $username = sys_get_param_str_unsafe('id_user');
    if (empty($username)) {
      throw new Exception(classLocale::$lang['adm_mm_no_dest']);
    }

    $an_account = new Account(classSupernova::$auth->account->db);
    if (!$an_account->db_get_by_id($username) && !$an_account->db_get_by_name($username) && !$an_account->db_get_by_email($username)) {
      throw new Exception(sprintf(classLocale::$lang['adm_mm_user_none'], $username));
    }

    $mm_changed = $an_account->metamatter_change(RPG_ADMIN, $points, sprintf(
      classLocale::$lang['adm_matter_change_log_record'],
      $an_account->account_id, db_escape($an_account->account_name),
      $user['id'], db_escape($user['username']),
      db_escape(sys_get_param_str('reason'))
    ));
    if (empty($mm_changed)) {
      throw new Exception(classLocale::$lang['adm_mm_add_err']);
    }
    $message = sprintf(classLocale::$lang['adm_mm_user_added'], $an_account->account_name, $an_account->account_id, pretty_number($points));
    $isNoError = true;
    $message_status = ERR_NONE;
  } catch (Exception $e) {
    $message = $e->getMessage();
  }
}

if ($message_status == ERR_ERROR) {
  $template->assign_vars(array(
    'ID_USER' => $username,
    'POINTS'  => $points,
    'REASON'  => $reason,
  ));
};

if ($message) {
  $template->assign_block_vars('result', array('MESSAGE' => $message, 'STATUS' => $message_status ? $message_status : ERR_NONE));
}

display($template, classLocale::$lang['adm_mm_title'], false, '', true);
