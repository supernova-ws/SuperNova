<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$template = gettemplate('ali_internal', true);

$lang['ALLY_ADMIN'] = $user_admin;
$lang['ALLY_CAN_KICK'] = $user_can_kick;
$lang['MASS_MAIL'] = $user_can_send_mails;
$lang['MANAGE_REQUESTS'] = $user_admin_applications;
$lang['ALLY_NEGOTIATE'] = $user_can_negotiate;

$request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);
$lang['ALLY_REQUESTS'] = $request['request_count'] = 5;

// Imagen de la alianza
if ($ally['ally_owner'] == $user['id']) {
  $range = ($ally['ally_owner_range'] != '')?$lang['Founder']:$ally['ally_owner_range'];
} elseif ($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name'])) {
  $range = $ranks[$user['ally_rank_id']]['name'];
} else {
  $range = $lang['member'];
}
$lang['range'] = $range;
if ($ally['ally_owner'] != $user['id']) {
  $lang['ally_owner'] .= MessageForm($lang['Exit_of_this_alliance'], "", "alliance.php?mode=exit", $lang['Continue']);
}

$lang['ally_image'] = $ally['ally_image'];// ? $ally['ally_image'] :'';

// codigo raro
$patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
$replacements[] = '<font color="\1">\2</font>';
$patterns[] = '#\[img\](.*?)\[/img\]#Smi';
$replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
$patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
$replacements[] = '<font color="\1">';
$patterns[] = "#\[/f\]#Ssi";
$replacements[] = '</font>';
$ally['ally_description'] = preg_replace($patterns, $replacements, $ally['ally_description']);
$lang['ally_description'] = nl2br($ally['ally_description']);

$ally['ally_text'] = preg_replace($patterns, $replacements, $ally['ally_text']);
$lang['ally_text'] = nl2br($ally['ally_text']);

$lang['ally_web'] = $ally['ally_web'];
$lang['ally_tag'] = $ally['ally_tag'];
$lang['ally_members'] = $ally['ally_members'];
$lang['ally_name'] = $ally['ally_name'];

$relations = ali_relations($user['ally_id']);
foreach($relations as $relation)
{
  if($relation['alliance_diplomacy_contr_ally_id'] == $user['ally_id'])
  {
    $contr_ally = doquery("SELECT ally_name FROM {{alliance}} WHERE `id` = {$relation['alliance_diplomacy_ally_id']} LIMIT 1;", '', true);
    $relation['alliance_diplomacy_contr_ally_name'] = $contr_ally['ally_name'];
  }

  if($relation['alliance_diplomacy_contr_ally_id'] && $relation['alliance_diplomacy_ally_id'])
  {
    $template->assign_block_vars('relation', array(
      'NAME'     => $relation['alliance_diplomacy_contr_ally_name'],
      'RELATION' => $lang['ali_dip_relations'][$relation['alliance_diplomacy_relation']],
      'TIME'     => date(FMT_DATE_TIME, $relation['alliance_diplomacy_time']),
    ));
  }
}

display(parsetemplate($template, $lang), $lang['your_alliance']);

?>
