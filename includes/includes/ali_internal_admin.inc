<?php
if (!$user_admin) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

$lang['dpath'] = $dpath;
$parse = $lang;

$text_list = array(
  1 => array ( 'db_field' => 'ally_description', 'text_type' => 'Public_text_of_alliance'),
  2 => array ( 'db_field' => 'ally_text', 'text_type' => 'Internal_text'),
  3 => array ( 'db_field' => 'ally_request', 'text_type' => 'Show_of_request_text'),
);
if ($allyTextID<1 || $allyTextID>3) $allyTextID = 1;
extract($text_list[$allyTextID]);

if ($isSaveOptions) {
  $ally['ally_name'] = $POST_name;
  $ally['ally_tag']  = $POST_tag;
  $ally['ally_owner_range'] = $POST_owner_range;
  $ally['ally_web'] = $POST_web;
  $ally['ally_image'] = $POST_image;
  $ally['ally_request_notallow'] = $POST_request_notallow;

  if ($ally['ally_request_notallow'] != 0 && $ally['ally_request_notallow'] != 1) {
    message("You at \"Applications\" an option from the form!", "Mistake");
    exit;
  }

  doquery("UPDATE {{table}} SET
      `ally_name`='{$ally['ally_name']}',
      `ally_tag`='{$ally['ally_tag']}',
      `ally_owner_range`='{$ally['ally_owner_range']}',
      `ally_image`='{$ally['ally_image']}',
      `ally_web`='{$ally['ally_web']}',
      `ally_request_notallow`='{$ally['ally_request_notallow']}'
    WHERE `id`='{$ally['id']}'", "alliance");
} elseif ($isSaveText) {
  $ally[$db_field] = $POST_text;
  doquery("UPDATE {{table}} SET `{$db_field}`='{$POST_text}' WHERE `id`='{$ally['id']}'", "alliance");
} elseif ($isTransfer && $idNewLeader){
  $newLeader = doquery("SELECT `ally_id` FROM {{table}} WHERE `id` = '$idNewLeader';", "users", true);
  if($newLeader['ally_id'] == $user['ally_id']){
    doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`={$user['id']};", 'users');
    doquery("UPDATE {{table}} SET `ally_owner`='{$idNewLeader}' WHERE `id`={$user['ally_id']};", 'alliance');
    doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`='{$idNewLeader}';", 'users');
    header('Location: alliance.php');
    exit;
  }
} elseif ($isDisband && $isConfirmDisband){
  if (!$user_can_disband) {
    message($lang['Denied_access'], $lang['Members_list']);
  }
  doquery("DELETE FROM {{table}} WHERE id='{$ally['id']}'", "alliance");
  doquery("UPDATE {{table}} SET `ally_id` = 0, `ally_name` = '', `ally_request` = 0, `ally_rank_id` = 0, `ally_register_time` = 0 WHERE ally_id='{$ally['id']}'", "users");
  header('Location: alliance.php');
  exit;
};

$parse['text']         = $ally[$db_field];
$parse['request_type'] = $lang[$text_type];

$parse['t'] = $allyTextID;

$parse['ally_name'] = $ally['ally_name'];
$parse['ally_tag'] = $ally['ally_tag'];
$parse['ally_web'] = $ally['ally_web'];
$parse['ally_image'] = $ally['ally_image'];
$parse['ally_request_notallow_0'] = (( $ally['ally_request_notallow']) ? ' SELECTED' : '');
$parse['ally_request_notallow_1'] = ((!$ally['ally_request_notallow']) ? ' SELECTED' : '');
$parse['ally_owner_range'] = $ally['ally_owner_range'];
if($ally['ally_owner'] != $user['id']){
  $parse['hideNotOwner'] = ' style="hide"';
}

unset($tmp);
foreach($ranks as $rankID => $rank)
  if($rank['admin'])
    $tmp .= "`ally_rank_id` = {$rankID} OR ";
$tmp = substr($tmp, 0, -4);

if(!empty($tmp)){
  $userAllyAdmins = doquery("SELECT `id`, `username` FROM {{table}} WHERE ({$tmp}) AND `ally_id` = {$ally['id']};", 'users');
  unset($tmp);
  while ($userAllyAdmin = mysql_fetch_array($userAllyAdmins)) {
    $tmp .= "<option value={$userAllyAdmin['id']}>{$userAllyAdmin['username']}</option>";
  }

  $parse['adminMembers'] = $tmp;
}

$page .= parsetemplate(gettemplate('alliance_admin'), $parse);
display($page, $lang['ally_admin'] . '123');
?>