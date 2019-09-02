<?php

use Alliance\DBStaticAlly;
use Old\Avatar;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if(!$user_admin) {
  SnTemplate::messageBox($lang['Denied_access'], $lang['ally_admin']);
}

$template = SnTemplate::gettemplate('ali_admin', $template);

$text_list = array(
  1 => array ('db_field' => 'ally_description', 'text_type' => 'Public_text_of_alliance'),
  2 => array ('db_field' => 'ally_text', 'text_type' => 'Internal_text'),
  3 => array ('db_field' => 'ally_request', 'text_type' => 'Show_of_request_text'),
);

$allyTextID = sys_get_param_int('t', 1);
$allyTextID = ($allyTextID<1 || $allyTextID>3) ? 1 : $allyTextID;

if(sys_get_param_str('isSaveOptions')) {
  $new_image = $ally['ally_image'];
  $avatar_upload_result = Avatar::sys_avatar_upload($ally['id'], $new_image, 'ally');
  $template->assign_vars([
    'AVATAR_UPLOAD_STATUS' => $avatar_upload_result['STATUS'],
    'AVATAR_UPLOAD_MESSAGE' => $avatar_upload_result['MESSAGE'],
  ]);

  $ally_changeset = array();
  if(($new_tag = sys_get_param_str_unsafe('tag', $ally['ally_tag'])) && $new_tag != $ally['ally_tag']) {
    $new_tag_unsafe = $new_tag;
    $new_tag = db_escape($new_tag);
    $isTaggedAllyExists = DBStaticAlly::db_ally_get_by_tag($new_tag);
    if(!empty($isTaggedAllyExists)) {
      SnTemplate::messageBox(sprintf($lang['ally_message_tag_exists'], $new_tag_unsafe), '', 'alliance.php?mode=admin&edit=ally');
    }
    $ally_changeset[] = "`ally_tag`='{$new_tag}'";
    db_user_set_by_id($ally['ally_user_id'], "`username`='[{$new_tag}]'");
  }

  if(($new_name = sys_get_param_str_unsafe('name', $ally['ally_name'])) && $new_name != $ally['ally_name']) {
    $new_name_unsafe = $new_name;
    $new_name = db_escape($new_name);
    $isTaggedAllyExists = DBStaticAlly::db_ally_get_by_name($new_name);
    if(!empty($isTaggedAllyExists)) {
      SnTemplate::messageBox(sprintf($lang['ally_message_name_exists'], $new_name_unsafe), '', 'alliance.php?mode=admin&edit=ally');
    }
    $ally_changeset[] = "`ally_name`='{$new_name}'";
  }

  if(($new_owner_rank = sys_get_param_str_unsafe('owner_range', $ally['ally_owner_range'])) && $new_owner_rank != $ally['ally_owner_range']) {
    $new_owner_rank = db_escape($new_owner_rank);
    $ally_changeset[] = "`ally_owner_range` = '{$new_owner_rank}'";
  }
  if(($new_web = sys_get_param_str_unsafe('web', $ally['ally_web'])) && $new_web != $ally['ally_web']) {
    $new_web = db_escape($new_web);
    $ally_changeset[] = "`ally_web` = '{$new_web}'";
  }
  $new_request = sys_get_param_int('request_notallow', $ally['ally_request_notallow']);
  $ally_changeset[] = "`ally_request_notallow` = '{$new_request}'";
//  if(($new_request = sys_get_param_int('request_notallow', $ally['ally_request_notallow'])) && $new_request != $ally['ally_request_notallow']) {
//    $ally_changeset[] = "`ally_request_notallow` = '{$new_request}'";
//  }
  if($new_image != $ally['ally_image']) {
    $new_image = intval($new_image);
    $ally_changeset[] = "`ally_image` = '{$new_image}'";
  }

  if(!empty($ally_changeset)) {
    doquery("UPDATE {{alliance}} SET " . implode(',', $ally_changeset) . " WHERE `id`='{$ally['id']}' LIMIT 1;");
    sys_redirect('alliance.php?mode=admin&edit=ally');
  }
}
elseif(sys_get_param_str('isSaveText'))
{
  $text = sys_get_param_str_both('text');
  doquery("UPDATE {{alliance}} SET `{$text_list[$allyTextID]['db_field']}`='{$text['safe']}' WHERE `id`='{$ally['id']}';");
  $ally[$text_list[$allyTextID]['db_field']] = $text['unsafe'];
}
elseif(sys_get_param_str('isTransfer') && $idNewLeader = sys_get_param_id('idNewLeader'))
{
  if(!$isAllyOwner)
  {
    SnTemplate::messageBox($lang['Denied_access'], $lang['ally_admin']);
  }

  $newLeader = db_user_by_id($idNewLeader, false, `ally_id`);
  if($newLeader['ally_id'] == $user['ally_id'])
  {
    sn_db_transaction_start();
    db_user_set_by_id($user['id'], "`ally_rank_id`='0'");
    doquery("UPDATE {{alliance}} SET `ally_owner`='{$idNewLeader}' WHERE `id`={$user['ally_id']};");
    db_user_set_by_id($idNewLeader, "`ally_rank_id`='0'");
    sn_db_transaction_commit();
    sys_redirect('alliance.php');
  }
}
elseif(sys_get_param_str('isDisband') && sys_get_param_str('isConfirmDisband'))
{
  if(!$isAllyOwner)
  {
    SnTemplate::messageBox($lang['Denied_access'], $lang['ally_admin']);
  }
  sn_db_transaction_start();
  doquery("DELETE FROM {{alliance}} WHERE id='{$ally['id']}';");
  db_user_list_set_by_ally_and_rank($ally['id'], -20, "`ally_id` = null, `ally_name` = null, `ally_rank_id` = 0, `ally_register_time` = 0");
  sn_db_transaction_commit();
  sys_redirect('alliance.php');
};

$request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);

$template->assign_vars(array(
  'request_count' => $request['request_count'] ? $lang['ali_req_requestCount'] . ': ' . intval($request['request_count']) . '. ' . $lang['ali_req_check'] : $lang['ali_req_emptyList'],
  'text'         => $ally[$text_list[$allyTextID]['db_field']],
  'request_type' => $lang[$text_list[$allyTextID]['text_type']],
  't' => $allyTextID,
  'ALLY_ID' => $ally['id'],
  'ALLY_IMAGE' => $ally['ally_image'],
  'ally_name' => htmlspecialchars($ally['ally_name']),
  'ally_tag' => htmlspecialchars($ally['ally_tag']),
  'ally_web' => htmlspecialchars($ally['ally_web']),
  'ally_request_notallow_0' => (( $ally['ally_request_notallow']) ? ' SELECTED' : ''),
  'ally_request_notallow_1' => ((!$ally['ally_request_notallow']) ? ' SELECTED' : ''),
  'ally_owner_range' => htmlspecialchars($ally['ally_owner_range']),
  'hideNotOwner' => $ally['ally_owner'] != $user['id'] ? 'display: hide;' : '',
));

{
  $userAllyAdmins = db_user_list("`ally_id`= {$ally['id']}", false, '`id`, `username`');
  unset($tmp);
  foreach($userAllyAdmins as $userAllyAdmin)
  {
    $tmp .= "<option value={$userAllyAdmin['id']}>{$userAllyAdmin['username']}</option>";
  }

  $template->assign_var('adminMembers', $tmp);
}

foreach($sn_ali_admin_internal as $sn_ali_admin_action => $sn_ali_admin_action_locale)
{
  if(!$sn_ali_admin_action_locale['title'])
  {
    continue;
  }
  $template->assign_block_vars('admin_actions', array(
    'ACTION' => $sn_ali_admin_action,
    'LOCALE' => $lang[$sn_ali_admin_action_locale['title']],
  ));
}

SnTemplate::display($template, $lang['ally_admin']);
