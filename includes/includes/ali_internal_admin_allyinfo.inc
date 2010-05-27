<?php
if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

if ($t != 1 && $t != 2 && $t != 3) {
  $t = 1;
}
// post!

if ($POST_options) {
  $ally['ally_owner_range'] = $POST_owner_range;
  $ally['ally_web'] = $POST_web;
  $ally['ally_image'] = $POST_image;
  $ally['ally_request_notallow'] = $POST_request_notallow;

  if ($ally['ally_request_notallow'] != 0 && $ally['ally_request_notallow'] != 1) {
    message("You at \"Applications\" an option from the form!", "Mistake");
    exit;
  }

  doquery("UPDATE {{table}} SET
  `ally_owner_range`='{$ally['ally_owner_range']}',
  `ally_image`='{$ally['ally_image']}',
  `ally_web`='{$ally['ally_web']}',
  `ally_request_notallow`='{$ally['ally_request_notallow']}'
  WHERE `id`='{$ally['id']}'", "alliance");
} elseif ($t) {
  if ($t == 3) {
    $ally['ally_request'] = strip_tags($POST_text);

    doquery("UPDATE {{table}} SET
    `ally_request`='{$ally['ally_request']}'
    WHERE `id`='{$ally['id']}'", "alliance");
  } elseif ($t == 2) {
    $ally['ally_text'] = strip_tags($POST_text);
    doquery("UPDATE {{table}} SET
    `ally_text`='{$ally['ally_text']}'
    WHERE `id`='{$ally['id']}'", "alliance");
  } else {
    $ally['ally_description'] = strip_tags($POST_text);

    doquery("UPDATE {{table}} SET
    `ally_description`='" . $ally['ally_description'] . "'
    WHERE `id`='{$ally['id']}'", "alliance");
  }
}
$lang['dpath'] = $dpath;
/*
  Depende del $t, muestra el formulario para cada tipo de texto.
*/
if ($t == 3) {
  $lang['request_type'] = $lang['Show_of_request_text'];
} elseif ($t == 2) {
  $lang['request_type'] = $lang['Internal_text'];
} else {
  $lang['request_type'] = $lang['Public_text_of_alliance'];
}

if ($t == 2) {
  $lang['text'] = $ally['ally_text'];
  $lang['Texts'] = $lang['Texts'];
  $lang['Show_of_request_text'] = $lang['Internal_text'];
} elseif ($t == 1) {
  $lang['text'] = $ally['ally_description'];
  $lang['Show_of_request_text'] = $lang['Public_text_of_alliance'];
} else {
    $lang['text'] = $ally['ally_request'];
  $lang['Show_of_request_text'] = $lang['Show_of_request_text'];
      }

$lang['t'] = $t;

$lang['ally_web'] = $ally['ally_web'];
$lang['ally_image'] = $ally['ally_image'];
$lang['ally_request_notallow_0'] = (($ally['ally_request_notallow'] == 1) ? ' SELECTED' : '');
$lang['ally_request_notallow_1'] = (($ally['ally_request_notallow'] == 0) ? ' SELECTED' : '');
$lang['ally_owner_range'] = $ally['ally_owner_range'];
$lang['Ally_transfer'] = MessageForm($lang['Ally_transfer'], "", "?mode=admin&edit=transfer", $lang['Continue']);
$lang['ally_dissolve'] = MessageForm($lang['ally_dissolve'], "", "?mode=admin&edit=exit", $lang['Continue']);

$page .= parsetemplate(gettemplate('alliance_admin'), $lang);
display($page, $lang['ally_admin']);
?>