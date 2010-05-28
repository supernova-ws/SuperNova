<?php
if (!$user_can_kick) {
  message($lang['Denied_access'], $lang['Members_list']);
}

// Changing rank for single user
if($id_user && isset($id_rank)){
  doquery("UPDATE {{table}} SET `ally_rank_id` = {$id_rank} WHERE `id`= {$id_user};", 'users');
}

if ($kick) {
  $u = doquery("SELECT * FROM {{table}} WHERE `id` ='{$kick}' LIMIT 1", 'users', true);
  // kickeamos!
  if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner']) {
    doquery("UPDATE {{table}} SET `ally_id`='0', `ally_name` = '' WHERE `id`='{$kick}'", 'users');
  }
}

$sort1s = array(0 => "`id`", 1 => "`username`", 2 => "`ally_rank_id`", 3 => "`total_points`", 4 => "`ally_register_time`", 5 => "`onlinetime`");
if($sort1>5 || $sort1<0) $sort1 = 0;
$sort = " ORDER BY " . $sort1s[$sort1];
if ($sort2){
  $sort .= " DESC;";
  $sort2 = 0;
} else {
  $sort .= " ASC;";
  $sort2 = 1;
}

$userList = doquery("SELECT {{table}}.id, {{table}}.username, {{table}}.galaxy, {{table}}.system, {{table}}.planet, {{table}}.onlinetime, {{table}}.ally_rank_id, {{table}}.ally_register_time, {{statpoints}}.total_points FROM {{table}} LEFT JOIN {{statpoints}} on {{statpoints}}.`id_owner`={{table}}.`id` AND `stat_type` = 1 AND `stat_code` = 1 WHERE `ally_id`='{$user['ally_id']}' {$sort}", 'users');
$lang['memberzahl'] = mysql_num_rows($userList);

while ($userRow = mysql_fetch_array($userList)) {
  $parse = $lang;
  $parse["dpath"] = $dpath;
  $parse = array_merge($parse, $userRow);

  $i++;
  $parse['i'] = $i;
  $parse['points'] = "" . pretty_number($userRow['total_points']) . "";
  $days = floor(round(time() - $userRow["onlinetime"]) / 3600 % 24);
  $parse["onlinetime"] = str_replace("%s", $days, "%s d");

  if (!isset($ranks[$userRow['ally_rank_id']])) {
    $userRow['ally_rank_id'] = 0;
  }

  if ($ally['ally_owner'] == $userRow['id']) {
    $ally_range = ($ally['ally_owner_range'])?$ally['ally_owner_range']:$lang['Founder'];
  } else {
    $ally_range = '<select onchange="javascript:window.location=\'alliance.php?mode=admin&edit=members&id_user=' . $userRow['id'] . '&id_rank=\' + this.value">';

    foreach($ranks as $rankID => $rankArray){
      $ally_range .= '<option value="' . $rankID . '"';
      if($rankID == $userRow['ally_rank_id'])
        $ally_range .= " selected";
      $ally_range .= '>' . $rankArray['name'];
      $ally_range .= '</option>';
    }
    $ally_range .= '</select>';
  }
  $parse['ally_range'] = $ally_range;

  $parse["functions"] = 'class="hide"';
  if ($user_can_kick && $user['id'] != $userRow['id'] && $ally['ally_owner'] != $userRow['id']) {
    $parse["functions"] = '';
  }

  $parse['ally_register_time'] = date("Y-m-d h:i:s", $userRow['ally_register_time']);
  $page_list .= parsetemplate(gettemplate('alliance_admin_members_row'), $parse);
}

if ($i != $ally['ally_members']) {
  doquery("UPDATE {{table}} SET `ally_members`='{$i}' WHERE `id`='{$ally['id']}'", 'alliance');
}

$lang['memberslist'] = $page_list;
$lang['s'] = $sort2;
$lang['dpath'] = $dpath;
$page .= parsetemplate(gettemplate('alliance_admin_members'), $lang);

display($page, $lang['members_admin']);
?>