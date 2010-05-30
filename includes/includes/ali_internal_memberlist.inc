<?php
if (!$user_can_kick && $allianceAdminMode) {
  message($lang['Denied_access'], $lang['Members_list']);
}

// Changing rank for single user
if($id_user && isset($id_rank) && $user_admin){
  doquery("UPDATE {{table}} SET `ally_rank_id` = {$id_rank} WHERE `id`= {$id_user};", 'users');
}

if ($id_kick && $user_can_kick) {
  $u = doquery("SELECT * FROM {{table}} WHERE `id` ='{$id_kick}' LIMIT 1", 'users', true);
  if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner']) {
    doquery("UPDATE {{table}} SET `ally_id`='0', `ally_name` = '' WHERE `id`='{$id_kick}'", 'users');
  }
}

$sort1s = array(0 => "`ally_rank_id`", 1 => "`username`", 2 => "`total_points`", 3 => '`galaxy` %1$s, `system` %1$s, `planet`', 4 => "`ally_register_time`", 5 => "`onlinetime`");
if ($sort2){
  $sort2s = "DESC";
  $sort2 = 0;
} else {
  $sort2s = "ASC";
  $sort2 = 1;
}

if($sort1>5 || $sort1<0) $sort1 = 0;
$sort = sprintf(' ORDER BY ' . $sort1s[$sort1] . ' %1$s;', $sort2s);

$userList = doquery("SELECT {{table}}.id, {{table}}.username, {{table}}.galaxy, {{table}}.system, {{table}}.planet, {{table}}.onlinetime, {{table}}.ally_rank_id, {{table}}.ally_register_time, {{statpoints}}.total_points FROM {{table}} LEFT JOIN {{statpoints}} on {{statpoints}}.`id_owner`={{table}}.`id` AND `stat_type` = 1 AND `stat_code` = 1 WHERE `ally_id`='{$user['ally_id']}' {$sort}", 'users');
$parse = $lang;
$parse["dpath"] = $dpath;
$parse["mode"] = $mode . ($edit ? "&edit=" . $edit : "");
if ($user_can_kick && $user['id'] != $userRow['id'] && $ally['ally_owner'] != $userRow['id'] && $allianceAdminMode) {
  $parse["isHideAdmin"] = '';
  $parse["adminBackLinkClass"] = 'fr';
  $parse["colspan"] = 9;
}else{
  $parse["isHideAdmin"] = 'class="hide"';
  $parse["adminBackLinkClass"] = 'hide';
  $parse["colspan"] = 8;
}

while ($userRow = mysql_fetch_array($userList)) {
  $parse = array_merge($parse, $userRow);

  $i++;
  $parse['i'] = $i;
  $parse['points'] = "" . pretty_number($userRow['total_points']) . "";

  if (!isset($ranks[$userRow['ally_rank_id']])) {
    $userRow['ally_rank_id'] = 0;
  }

  if ($ally['ally_owner'] == $userRow['id']) {
    $ally_range = ($ally['ally_owner_range'])?$ally['ally_owner_range']:$lang['Founder'];
  } else {
    if($allianceAdminMode && $user_admin){
      $ally_range = '<select onchange="javascript:window.location=\'alliance.php?mode=admin&edit=members&id_user=' . $userRow['id'] . '&id_rank=\' + this.value">';

      foreach($ranks as $rankID => $rankArray){
        $ally_range .= '<option value="' . $rankID . '"';
        if($rankID == $userRow['ally_rank_id'])
          $ally_range .= " selected";
        $ally_range .= '>' . $rankArray['name'];
        $ally_range .= '</option>';
      }
      $ally_range .= '</select>';
    }else{
      $ally_range = $ranks[$userRow['ally_rank_id']]['name'];
    }
  }
  $parse['ally_range'] = $ally_range;

  $parse['ally_register_time'] = date("Y-m-d h:i:s", $userRow['ally_register_time']);

  $last_active = time() - $userRow["onlinetime"];
  if($allianceAdminMode && ($user_admin)){
    $parse["onlineMessage"] = $lang['ali_lastActive'];
    if ( $last_active < 60 ) {
      $tmp = "lime>{$lang['On']}";
    } elseif ($last_active < 60 * 60) {
      $last_active = round($last_active / 60);
      $tmp = "lime>{$last_active} {$lang['sys_min_short']}";
    } elseif ($last_active < 60 * 60 * 24) {
      $last_active = round( $last_active / (60 * 60));
      $tmp = "green>{$last_active} {$lang['sys_hrs_short']}";
    } else {
      $last_active = round( $last_active / (60 * 60 * 24));
      if ($last_active < 7) {
        $tmp = "yellow";
      }elseif ($last_active < 30) {
        $tmp = "orange";
      }else{
        $tmp = "red";
      }
      $tmp .= ">{$last_active} {$lang['sys_day_short']}";
    }
  }else{
    $parse["onlineMessage"] = $lang['Online'];
    if($user_onlinestatus){
      if ( $last_active < 60 * 5 ) {
        $tmp = "lime>{$lang['On']}";
      } elseif ($last_active < 60 * 15) {
        $tmp = "yellow>{$lang['ali_lessThen15min']}";
      } else {
        $tmp = "red>{$lang['Off']}";
      }
    } else {
      $sort1 = max($sort1, 4);
      $tmp = "orange>-";
    }
  }
  $parse["onlinetime"] = '<font color=' . $tmp . '</font>';

  if ($user['id'] != $userRow['id'] && $ally['ally_owner'] != $userRow['id']) {
    $parse["isHideOwnerKick"] = '';
  }else{
    $parse["isHideOwnerKick"] = 'class="hide"';
  }

  $page_list .= parsetemplate(gettemplate('alliance_memberlist_row'), $parse);
}

if ($i != $ally['ally_members']) {
  doquery("UPDATE {{table}} SET `ally_members`='{$i}' WHERE `id`='{$ally['id']}'", 'alliance');
}

$parse['memberCount'] = mysql_num_rows($userList);
$parse['memberslist'] = $page_list;
$parse['s'] = $sort2;
$page .= parsetemplate(gettemplate('alliance_memberlist'), $parse);

display($page, $lang['members_admin']);
?>