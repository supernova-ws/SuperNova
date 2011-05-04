<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_admin_applications)
{
  message($lang['Denied_access'], $lang['requests_admin']);
}

$d = sys_get_param_int('d');
if($d)
{
  doquery("UPDATE {{alliance_requests}} SET `request_denied` = 1, `request_text` = '{$lang['ali_req_deny_reason']}' WHERE `id_user`= {$d} LIMIT 1;");
}

$id_user = sys_get_param_int('id_user');
if($id_user)
{
  doquery("UPDATE {{users}} SET `ally_id` = '{$ally['id']}', `ally_name` = '{$ally['ally_name']}', `ally_register_time` = {$time_now}, `ally_rank_id` = 0 WHERE `id`= {$id_user} LIMIT 1;");
  doquery("DELETE FROM {{alliance_requests}} WHERE `id_user`= '{$id_user}' LIMIT 1;");
}

$template = gettemplate('ali_admin_request', true);

$query = doquery("SELECT {{alliance_requests}}.*, {{users}}.username FROM {{alliance_requests}} LEFT JOIN {{users}} ON {{users}}.id = {{alliance_requests}}.id_user WHERE id_ally='{$ally['id']}'");
while ($ally_request_row = mysql_fetch_assoc($query))
{
  $template->assign_block_vars('alliance_request', array(
    'USER_ID'   => $ally_request_row['id_user'],
    'USER_NAME' => $ally_request_row['username'],
    'TIME'      => date(FMT_DATE_TIME, $ally_request_row['request_time']),
    'TEXT'      => sys_bbcodeParse($ally_request_row['request_text']),
    'DENIED'    => $ally_request_row['request_denied'],
  ));
}

$template->assign_vars(array(
  'ally_tag' => $ally['ally_tag'],
));

display(parsetemplate($template), $lang['requests_admin']);

?>
