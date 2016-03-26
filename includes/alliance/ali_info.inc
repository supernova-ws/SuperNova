<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$template = gettemplate('ali_info', true);

if($mode == 'exit') {
  if($ally['ally_owner'] == $user['id']) {
    message(classLocale::$lang['Owner_cant_go_out'], classLocale::$lang['Alliance']);
  }

  if(sys_get_param_int('ali_info_leave_confirm')) {
    sn_db_transaction_start();
    db_user_set_by_id($user['id'], "`ally_id` = null, `ally_name` = null, `ally_tag` = null, `ally_register_time` = 0, `ally_rank_id` = 0");
    db_ally_list_recalc_counts();
    sn_db_transaction_commit();
    message(sprintf(classLocale::$lang['ali_info_leave_success'], $ally['ally_name']), classLocale::$lang['sys_alliance']);
  }
} elseif($mode == 'ainfo') {
  $tag = sys_get_param_str('tag');
  $id_ally = sys_get_param_id('a');
  if($tag) {
    $ally = db_ally_get_by_tag($tag);
  } elseif($id_ally) {
    $ally = db_ally_get_by_id($id_ally);
  }

  if(!$ally) {
    message(classLocale::$lang['ali_sys_notFound'], classLocale::$lang['Ally_info_1']);
  }

  if(!$ally['ally_description']) {
    $ally['ally_description'] = classLocale::$lang['Ally_nodescription'];
  }

  $template->assign_vars(array(
    'EXTERNAL'     => true,
    'USER_ALLY_ID' => $user['ally_id'],
  ));
  $page_header = classLocale::$lang['sys_alliance'];
} else {
  $page_header = classLocale::$lang['your_alliance'];

  if($ally['ally_owner'] == $user['id']) {
    $range = $ally['ally_owner_range'] ? $ally['ally_owner_range'] : classLocale::$lang['Founder'];
  } elseif($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name'])) {
    $range = $ranks[$user['ally_rank_id']]['name'];
  } else {
    $range = classLocale::$lang['member'];
  }

  $request = db_ally_request_count_by_id($ally);

  $template->assign_vars(array(
    'range'         => $range,
    'ALLY_REQUESTS' => $request['request_count'],

    'ALLY_ADMIN'      => $user_admin,
    'ALLY_CAN_KICK'   => $user_can_kick,
    'MASS_MAIL'       => $user_can_send_mails,
    'MANAGE_REQUESTS' => $user_admin_applications,
    'ALLY_NEGOTIATE'  => $user_can_negotiate,
  ));
}

$patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
$replacements[] = '<font color="\1">\2</font>';
$patterns[] = '#\[img\](.*?)\[/img\]#Smi';
$replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
$patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
$replacements[] = '<font color="\1">';
$patterns[] = "#\[/f\]#Ssi";
$replacements[] = '</font>';

$template->assign_vars(array(
  'ALLY_DESCRIPTION' => nl2br(preg_replace($patterns, $replacements, $ally['ally_description'])),
  'ALLY_TEXT'        => nl2br(preg_replace($patterns, $replacements, $ally['ally_text'])),
  'ally_id'          => $ally['id'],
  'ALLY_MEMBERS'     => $ally['ally_members'],
  'ally_web'         => $ally['ally_web'],
  'ALLY_TAG'         => $ally['ally_tag'],
  'ally_image'       => $ally['ally_image'],
  'ally_name'        => $ally['ally_name'],
));

$relations = ali_relations($ally['id']);
foreach($relations as $relation) {
  if($relation['alliance_diplomacy_contr_ally_id'] && $relation['alliance_diplomacy_ally_id']) {
    $template->assign_block_vars('relation', array(
      'NAME'     => $relation['alliance_diplomacy_contr_ally_name'],
      'RELATION' => classLocale::$lang['ali_dip_relations'][$relation['alliance_diplomacy_relation']],
      'TIME'     => date(FMT_DATE_TIME, $relation['alliance_diplomacy_time']),
    ));
  }
}

$template = parsetemplate($template);

function ali_render() {
  $func_args = func_get_args();

  return sn_function_call(__FUNCTION__, $func_args);
}

ali_render();

display($template, classLocale::$lang['your_alliance'] . ' [' . $ally['ally_name'] . ']');
