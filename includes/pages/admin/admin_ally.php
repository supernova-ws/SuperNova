<?php
/**
 * Created by Gorlum 15.06.2017 10:08
 */

function sn_admin_ally_model($template = null) {
  define('IN_ADMIN', true);
  lng_include('admin');
  messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  global $template_result;

  if (
    ($allyId = sys_get_param_id('ally_id'))
    &&
    (sys_get_param_str('action') == 'pass')
    &&
    ($newOwnerId = sys_get_param_id('new_owner_id'))
  ) {
    try {
      \Alliance\AllianceStatic::passAlliance($allyId, $newOwnerId);
      $template_result['.']['result'][] = [
        'MESSAGE' => '{ Альянс успешно передан другому игроку }',
        'STATUS' => ERR_NONE,
      ];
    } catch (Exception $e) {
      $template_result['.']['result'][] = [
        'MESSAGE' => $e->getMessage(),
        'STATUS' => $e->getCode(),
      ];
    }
  }

  return $template;
}

function sn_admin_ally_view_one($template = null, $allyId) {
  messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  global $template_result;

  $template = gettemplate('admin/admin_ally_one', $template);

  $alliance = \Alliance\RecordAlliance::findFirst($allyId);

  if (empty($alliance)) {
    return $template;
  }

  $render = $alliance->ptlArray();
  $memberList = \Alliance\AllianceStatic::getMemberList($render['ID']);
  $titledMembers = \Alliance\AllianceStatic::titleMembers($memberList, $alliance);

  $template_result['.']['members'] = $titledMembers;

  $template->assign_vars($render);
  $template->assign_vars([
    'PAGE_HEADER'                    => '{ Альянс }' . ' [' . $render['ID'] . '] ' . ' [' . $render['TAG'] . '] ' . $render['NAME'],
    'ALLIANCE_HEAD_INACTIVE_TIMEOUT' => ALLIANCE_HEAD_INACTIVE_TIMEOUT,
    'SN_TIME_NOW'                    => SN_TIME_NOW,
  ]);

  return $template;
}

function sn_admin_ally_view_all($template = null) {
  messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  global $lang;

  $template = gettemplate('admin/admin_ally_all', $template);

  foreach (\Alliance\RecordAlliance::findAll([]) as $alliance) {
    $rendered = $alliance->ptlArray();
    $rendered['CREATED_SQL'] = date(FMT_DATE_TIME_SQL, $alliance->createdUnixTime);
    $rendered['STAT_POINTS_TEXT'] = pretty_number($alliance->statPoints);
    $template->assign_block_vars('ally', $rendered);
  };

  $template->assign_vars([
    'PAGE_HEADER' => $lang['admin_ally_list'],
  ]);

  return $template;
}

function sn_admin_ally_view($template = null) {
  define('IN_ADMIN', true);
  lng_include('admin');
  messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

  $allyId = sys_get_param_id('ally_id');

  return $allyId ? sn_admin_ally_view_one($template, $allyId) : sn_admin_ally_view_all($template);
}
