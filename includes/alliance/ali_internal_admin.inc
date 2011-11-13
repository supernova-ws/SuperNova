<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_admin)
{
  message($lang['Denied_access'], $lang['ally_admin']);
}

$parse = $lang;

$idNewLeader = sys_get_param_id('idNewLeader');

$text_list = array(
  1 => array ('db_field' => 'ally_description', 'text_type' => 'Public_text_of_alliance'),
  2 => array ('db_field' => 'ally_text', 'text_type' => 'Internal_text'),
  3 => array ('db_field' => 'ally_request', 'text_type' => 'Show_of_request_text'),
);

$allyTextID  = sys_get_param_int('t');
if ($allyTextID<1 || $allyTextID>3) $allyTextID = 1;
extract($text_list[$allyTextID]);

if ($isSaveOptions)
{
  $ally['ally_name'] = sys_get_param_str('name');
  $ally['ally_tag']  = sys_get_param_str('tag');
  $ally['ally_owner_range'] = sys_get_param_str('owner_range');
  $ally['ally_web'] = sys_get_param_str('web');
  $ally['ally_image'] = sys_get_param_str('image');
  $ally['ally_request_notallow'] = sys_get_param_str('request_notallow');

  doquery("UPDATE {{alliance}} SET
      `ally_name`='{$ally['ally_name']}',
      `ally_tag`='{$ally['ally_tag']}',
      `ally_owner_range`='{$ally['ally_owner_range']}',
      `ally_image`='{$ally['ally_image']}',
      `ally_web`='{$ally['ally_web']}',
      `ally_request_notallow`='{$ally['ally_request_notallow']}'
    WHERE `id`='{$ally['id']}' LIMIT 1;");
  $ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$user['ally_id']}';", '', true);
} elseif ($isSaveText) {
  $POST_text = sys_get_param_str('text');
  doquery("UPDATE {{alliance}} SET `{$db_field}`='{$POST_text}' WHERE `id`='{$ally['id']}';");
  $ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$user['ally_id']}';", '', true);
} elseif ($isTransfer && $idNewLeader){
  if (!$isAllyOwner)
    message($lang['Denied_access'], $lang['ally_admin']);

  $newLeader = doquery("SELECT `ally_id` FROM {{users}} WHERE `id` = '$idNewLeader';", '', true);
  if($newLeader['ally_id'] == $user['ally_id']){
    doquery("UPDATE {{users}} SET `ally_rank_id`='0' WHERE `id`={$user['id']} LIMIT 1;");
    doquery("UPDATE {{alliance}} SET `ally_owner`='{$idNewLeader}' WHERE `id`={$user['ally_id']};");
    doquery("UPDATE {{users}} SET `ally_rank_id`='0' WHERE `id`='{$idNewLeader}';");
    header('Location: alliance.php');
    exit;
  }
} elseif ($isDisband && $isConfirmDisband){
  if (!$isAllyOwner)
  {
    message($lang['Denied_access'], $lang['ally_admin']);
  }
  doquery("DELETE FROM {{alliance}} WHERE id='{$ally['id']}';");
  doquery("UPDATE {{users}} SET `ally_id` = null, `ally_name` = null, `ally_rank_id` = 0, `ally_register_time` = 0 WHERE ally_id='{$ally['id']}';");
  header('Location: alliance.php');
  exit;
};

$parse['text']         = $ally[$db_field];
$parse['request_type'] = $lang[$text_type];

$parse['t'] = $allyTextID;

$request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);
if($request['request_count']){
  $parse['request_count'] = $lang['ali_req_requestCount'] . ': ' . intval($request['request_count']) . '. ' . $lang['ali_req_check'];
}else{
  $parse['request_count'] = $lang['ali_req_emptyList'];
}

$parse['ally_name'] = htmlspecialchars($ally['ally_name']);
$parse['ally_tag'] = htmlspecialchars($ally['ally_tag']);
$parse['ally_web'] = htmlspecialchars($ally['ally_web']);
$parse['ally_image'] = htmlspecialchars($ally['ally_image']);
$parse['ally_request_notallow_0'] = (( $ally['ally_request_notallow']) ? ' SELECTED' : '');
$parse['ally_request_notallow_1'] = ((!$ally['ally_request_notallow']) ? ' SELECTED' : '');
$parse['ally_owner_range'] = htmlspecialchars($ally['ally_owner_range']);
if($ally['ally_owner'] != $user['id'])
  $parse['hideNotOwner'] = ' style="hide"';

unset($tmp);
foreach($ranks as $rankID => $rank)
  if($rank['admin'])
    $tmp .= "`ally_rank_id` = {$rankID} OR ";
$tmp = substr($tmp, 0, -4);

if(!empty($tmp)){
  $userAllyAdmins = doquery("SELECT `id`, `username` FROM {{users}} WHERE ({$tmp}) AND `ally_id` = {$ally['id']};");
  unset($tmp);
  while ($userAllyAdmin = mysql_fetch_assoc($userAllyAdmins))
    $tmp .= "<option value={$userAllyAdmin['id']}>{$userAllyAdmin['username']}</option>";

  $parse['adminMembers'] = $tmp;
}

display(parsetemplate(gettemplate('ali_admin', true), $parse), $lang['ally_admin']);

?>
