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
if ($t<1 || $t>3) $t = 1;
extract($text_list[$t]);

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

  include('ali_internal_admin_transfer.inc');

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

$parse['t'] = $t;

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
//$parse['Ally_transfer'] = MessageForm($lang['Ally_transfer'], "", "?mode=admin&edit=transfer", $lang['Continue']);
//$parse['ally_dissolve'] = MessageForm($lang['ally_dissolve'], "", "?mode=admin&edit=exit", $lang['Continue']);

//$parse['Ally_transfer'] = $lang['Ally_transfer'];
//$parse['ally_dissolve'] = MessageForm($lang['ally_dissolve'], "", "?mode=admin&edit=exit", $lang['Continue']);

$page .= parsetemplate(gettemplate('alliance_admin'), $parse);
display($page, $lang['ally_admin']);
?>