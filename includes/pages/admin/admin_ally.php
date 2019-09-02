<?php

use Alliance\Alliance;

/**
 * Created by Gorlum 15.06.2017 10:08
 */

function sn_admin_ally_model($template = null) {
  define('IN_ADMIN', true);
  lng_include('admin');
  SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  global $template_result;

  if (
    ($allyId = sys_get_param_id('ally_id'))
    &&
    (sys_get_param_str('action') == 'pass')
    &&
    ($newOwnerId = sys_get_param_id('new_owner_id'))
  ) {
    try {
      if (empty($alliance = Alliance::findById($allyId))) {
        throw new \Exception('{ Альянс с указанным ID не найден }', ERR_ERROR);
      }
      if (empty($newOwnerMember = $alliance->getMemberList()->getById($newOwnerId))) {
        throw new \Exception('{ Новый владелец Альянса не найден }', ERR_ERROR);
      }

      $alliance->pass($newOwnerMember);

      $template_result['.']['result'][] = [
        'MESSAGE' => '{ Альянс успешно передан другому игроку }',
        'STATUS'  => ERR_NONE,
      ];
    } catch (Exception $e) {
      $template_result['.']['result'][] = [
        'MESSAGE' => $e->getMessage(),
        'STATUS'  => $e->getCode(),
      ];
    }
  }

  return $template;
}

/**
 * @param template|null $template
 *
 * @param Alliance      $alliance
 *
 * @return null|template
 */
function sn_admin_ally_view_one($template, $alliance) {
  global $template_result;

  SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  $template = SnTemplate::gettemplate('admin/admin_ally_one', $template);

  $template_result['.']['members'] = $alliance->getMemberList()->asPtl();

  $template->assign_recursive($alliance->asPtl());
  $template->assign_vars([
    'PAGE_HEADER'                    => '{ Альянс }' . ' [' . $alliance->id . '] ' . ' [' . $alliance->tag . '] ' . $alliance->name,
    'ALLIANCE_HEAD_INACTIVE_TIMEOUT' => ALLIANCE_HEAD_INACTIVE_TIMEOUT,
    'SN_TIME_NOW'                    => SN_TIME_NOW,
  ]);

  return $template;
}

function sn_admin_ally_view_all($template = null) {
  SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  $template = SnTemplate::gettemplate('admin/admin_ally_all', $template);

  foreach (Alliance::findAll([]) as $alliance) {
    $template->assign_block_vars('ally', $alliance->asPtl());
  };

  $template->assign_vars([
    'PAGE_HEADER' => SN::$lang['admin_ally_list'],
  ]);

  return $template;
}

function sn_admin_ally_view($template = null) {
  define('IN_ADMIN', true);
  lng_include('admin');
  SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  $allyId = sys_get_param_id('ally_id');
  $alliance = Alliance::findById($allyId);

  return !empty($alliance) ? sn_admin_ally_view_one($template, $alliance) : sn_admin_ally_view_all($template);
}
