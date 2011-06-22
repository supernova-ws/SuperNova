<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$template = gettemplate('ali_info', true);

if($mode == 'exit')
{
  if ($ally['ally_owner'] == $user['id'])
  {
    message($lang['Owner_cant_go_out'], $lang['Alliance']);
  }

  if (sys_get_param_int('ali_info_leave_confirm'))
  {
    doquery('START TRANSACTION;');
    doquery("UPDATE {{alliance}} SET `ally_members` = `ally_members` - 1 WHERE `id` = {$user['ally_id']} LIMIT 1;");
    doquery("UPDATE {{users}} SET `ally_id` = null, `ally_name` = null, `ally_register_time` = 0, `ally_rank_id` = 0 WHERE `id` = {$user['id']} LIMIT 1;");
    doquery('COMMIT;');
    $lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['Go_out_welldone']);
    $page = MessageForm($lang['sys_alliance'], sprintf($lang['ali_info_leave_success'], $ally['ally_name']), $PHP_SELF, $lang['Ok']);
  }

  display($page);
}
elseif($mode == 'ainfo')
{
  $tag = sys_get_param_str('tag');
  $id_ally = sys_get_param_int('a');
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
    message($lang['ali_sys_notFound'], $lang['Ally_info_1']);
  }

  if(!$ally['ally_description'])
  {
    $ally['ally_description'] = $lang['Ally_nodescription'];
  }

  $lang['EXTERNAL']     = true;
  $lang['USER_ALLY_ID'] = $user['ally_id'];
  $page_header          = $lang['sys_alliance'];
}
else
{
  $page_header = $lang['your_alliance'];

  $lang['ALLY_ADMIN'] = $user_admin;
  $lang['ALLY_CAN_KICK'] = $user_can_kick;
  $lang['MASS_MAIL'] = $user_can_send_mails;
  $lang['MANAGE_REQUESTS'] = $user_admin_applications;
  $lang['ALLY_NEGOTIATE'] = $user_can_negotiate;

  if($ally['ally_owner'] == $user['id'])
  {
    $range = ($ally['ally_owner_range'] != '') ? $lang['Founder'] : $ally['ally_owner_range'];
  }
  elseif($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name']))
  {
    $range = $ranks[$user['ally_rank_id']]['name'];
  }
  else
  {
    $range = $lang['member'];
  }
  $lang['range'] = $range;

  $request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);
  $lang['ALLY_REQUESTS'] = $request['request_count'];
}

if(!$ally['ally_description'])
{
  $ally['ally_description'] = $lang['Ally_nodescription'];
}

$patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
$replacements[] = '<font color="\1">\2</font>';
$patterns[] = '#\[img\](.*?)\[/img\]#Smi';
$replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
$patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
$replacements[] = '<font color="\1">';
$patterns[] = "#\[/f\]#Ssi";
$replacements[] = '</font>';
$lang['ALLY_DESCRIPTION'] = nl2br(preg_replace($patterns, $replacements, $ally['ally_description']));
$lang['ALLY_TEXT']    = nl2br(preg_replace($patterns, $replacements, $ally['ally_text']));

$lang['ally_id']      = $ally['id'];
$lang['ally_members'] = $ally['ally_members'];
$lang['ally_web']     = $ally['ally_web'];
$lang['ally_tag']     = $ally['ally_tag'];
$lang['ally_image']   = $ally['ally_image'];
$lang['ally_name']    = $ally['ally_name'];

$relations = ali_relations($ally['id']);
foreach($relations as $relation)
{
  /*
  if($relation['alliance_diplomacy_contr_ally_id'] == $user['ally_id'])
  {
    $contr_ally = doquery("SELECT ally_name FROM {{alliance}} WHERE `id` = {$relation['alliance_diplomacy_ally_id']} LIMIT 1;", '', true);
    $relation['alliance_diplomacy_contr_ally_name'] = $contr_ally['ally_name'];
  }
  */
  if($relation['alliance_diplomacy_contr_ally_id'] && $relation['alliance_diplomacy_ally_id'])
  {
    $template->assign_block_vars('relation', array(
      'NAME'     => $relation['alliance_diplomacy_contr_ally_name'],
      'RELATION' => $lang['ali_dip_relations'][$relation['alliance_diplomacy_relation']],
      'TIME'     => date(FMT_DATE_TIME, $relation['alliance_diplomacy_time']),
    ));
  }
}

display(parsetemplate($template, $lang), "{$lang['your_alliance']} [{$ally['ally_name']}]");

?>
