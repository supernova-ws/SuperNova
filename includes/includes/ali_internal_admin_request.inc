<?php

if (!$user_admin_applications) {
  message($lang['Denied_access'], $lang['requests_admin']);
}

if($d){
  doquery("UPDATE {{table}} SET `request_denied` = 1, `request_text` = '{$lang['ali_req_deny_reason']}' WHERE `id_user`= {$d};",'alliance_requests');
}

if($id_user){
  doquery("UPDATE {{table}} SET `ally_id` = '{$ally['id']}', `ally_name` = '{$ally['ally_name']}', `ally_register_time` = {$time_now}, `ally_rank_id` = 0 WHERE `id`= {$id_user};",'users');
  doquery("DELETE FROM {{table}} WHERE `id_user`= '{$id_user}';",'alliance_requests');
}

$row = gettemplate('ali_admin_request_row');
$parse = $lang;
$query = doquery("SELECT {{table}}.*, {{users}}.username FROM {{table}} LEFT JOIN {{users}} ON {{users}}.id = {{table}}.id_user WHERE id_ally='{$ally['id']}'", 'alliance_requests');
while ($r = mysql_fetch_array($query)) {
  $r['request_time'] = date($config->game_date_withTime, $r['request_time']);
  if($r['request_denied']){
    $r['request_text'] = $lang['ali_req_deny_admin'];
  };
  $parse['list'] .= parsetemplate($row, $r);
}
if ($parse['list'] == '') {
  $parse['list'] = '<tr><th colspan=5>' . $lang['ali_req_emptyList'] . '</th></tr>';
}

$parse['ally_tag'] = $ally['ally_tag'];
$page = parsetemplate(gettemplate('ali_admin_request'), $parse);
display($page, $lang['requests_admin']);

?>