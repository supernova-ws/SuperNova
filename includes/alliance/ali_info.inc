<?php

use Alliance\AllianceHelper;
use Alliance\DBStaticAlly;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$template = SnTemplate::gettemplate('ali_info', true);

if($mode == 'exit')
{
  if ($ally['ally_owner'] == $user['id'])
  {
    SnTemplate::messageBox($lang['Owner_cant_go_out'], $lang['Alliance']);
  }

  if (sys_get_param_int('ali_info_leave_confirm'))
  {
    sn_db_transaction_start();
    db_user_set_by_id($user['id'], "`ally_id` = null, `ally_name` = null, `ally_tag` = null, `ally_register_time` = 0, `ally_rank_id` = 0");
    DBStaticAlly::db_ally_list_recalc_counts();
    sn_db_transaction_commit();
    $lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['Go_out_welldone']);
    SnTemplate::messageBox(sprintf($lang['ali_info_leave_success'], $ally['ally_name']), $lang['sys_alliance']);
  }
}
elseif($mode == 'ainfo')
{
  $tag = sys_get_param_str('tag');
  $id_ally = sys_get_param_id('a');
  if($tag)
  {
    $ally = doquery("SELECT * FROM {{alliance}} WHERE ally_tag='{$tag}' LIMIT 1;", '', true);
  }
  elseif($id_ally)
  {
    $ally = doquery("SELECT * FROM {{alliance}} WHERE id='{$id_ally}' LIMIT 1;", '', true);
  }

  if(!$ally)
  {
    SnTemplate::messageBox($lang['ali_sys_notFound'], $lang['Ally_info_1']);
  }

  if(!$ally['ally_description'])
  {
    $ally['ally_description'] = $lang['Ally_nodescription'];
  }

  $template->assign_vars(array(
    'EXTERNAL'     => true,
    'USER_ALLY_ID' => $user['ally_id'],
  ));
  $page_header          = $lang['sys_alliance'];
}
else
{
  $page_header = $lang['your_alliance'];

  if($ally['ally_owner'] == $user['id'])
  {
    $range = $ally['ally_owner_range'] ? $ally['ally_owner_range'] : $lang['Founder'];
  }
  elseif($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name']))
  {
    $range = $ranks[$user['ally_rank_id']]['name'];
  }
  else
  {
    $range = $lang['member'];
  }

  $request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);

  $template->assign_vars(array(
    'range' => $range,
    'ALLY_REQUESTS' => $request['request_count'],

    'ALLY_ADMIN' => $user_admin,
    'ALLY_CAN_KICK' => $user_can_kick,
    'MASS_MAIL' => $user_can_send_mails,
    'MANAGE_REQUESTS' => $user_admin_applications,
    'ALLY_NEGOTIATE' => $user_can_negotiate,
  ));
}

$template->assign_vars([
  'ALLY_DESCRIPTION' => AllianceHelper::formatText($ally['ally_description']),
  'ALLY_TEXT'        => AllianceHelper::formatText($ally['ally_text']),
  'ally_id'          => $ally['id'],
  'ALLY_MEMBERS'     => $ally['ally_members'],
  'ally_web'         => $ally['ally_web'],
  'ally_tag'         => $ally['ally_tag'],
  'ally_image'       => $ally['ally_image'],
  'ally_name'        => $ally['ally_name'],
]);

$relations = ali_relations($ally['id']);
foreach($relations as $relation)
{
  if($relation['alliance_diplomacy_contr_ally_id'] && $relation['alliance_diplomacy_ally_id'])
  {
    $template->assign_block_vars('relation', array(
      'NAME'     => $relation['alliance_diplomacy_contr_ally_name'],
      'RELATION' => $lang['ali_dip_relations'][$relation['alliance_diplomacy_relation']],
      'TIME'     => date(FMT_DATE_TIME, $relation['alliance_diplomacy_time']),
    ));
  }
}

SN::$gc->pimp->allyInfoView();

SnTemplate::display($template, "{$lang['alliance']} [{$ally['ally_name']}]");
