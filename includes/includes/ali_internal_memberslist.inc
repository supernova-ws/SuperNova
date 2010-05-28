<?php
// obtenemos el array de los rangos
// $user_can_watch_memberlist
// comprobamos el permiso
// El orden de aparicion
if ($sort2) {
  if ($sort1 == 1) {
    $sort = " ORDER BY `username`";
  } elseif ($sort1 == 2) {
    $sort = " ORDER BY `ally_rank_id`";
  } elseif ($sort1 == 3) {
    $sort = " ORDER BY `total_points`";
  } elseif ($sort1 == 4) {
    $sort = " ORDER BY `ally_register_time`";
  } elseif ($sort1 == 5) {
    $sort = " ORDER BY `onlinetime`";
  } else {
    $sort = " ORDER BY `id`";
  }

  if ($sort2 == 1) {
    $sort .= " DESC;";
  } elseif ($sort2 == 2) {
    $sort .= " ASC;";
  }
  $listuser = doquery("SELECT * FROM {{table}} inner join `game_statpoints` on `game_users`.`id`=`game_statpoints`.`id_owner` WHERE ally_id='{$user['ally_id']}' AND STAT_type=1 $sort", 'users');
} else {
  $listuser = doquery("SELECT * FROM {{table}} WHERE ally_id='{$user['ally_id']}'", 'users');
}
// contamos la cantidad de usuarios.
$i = 0;
// Como es costumbre. un row template
$template = gettemplate('alliance_memberslist_row');
$page_list = '';
while ($u = mysql_fetch_array($listuser)) {
  $UserPoints = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $u['id'] . "';", 'statpoints', true);

  $i++;
  $u['i'] = $i;

  if ($u["onlinetime"] + 60 * 10 >= time() && $user_onlinestatus) {
    $u["onlinetime"] = "lime>{$lang['On']}<";
  } elseif ($u["onlinetime"] + 60 * 20 >= time() && $user_onlinestatus) {
    $u["onlinetime"] = "yellow>{$lang['15_min']}<";
  } elseif ($user_onlinestatus) {
    $u["onlinetime"] = "red>{$lang['Off']}<";
  } else $u["onlinetime"] = "orange>-<";
  // Nombre de rango
  if ($ally['ally_owner'] == $u['id']) {
    $u["ally_range"] = ($ally['ally_owner_range'] == '')?$lang['Founder']:$ally['ally_owner_range'];
  } elseif ($u['ally_rank_id'] == 0 ) {
    $u["ally_range"] = $lang['Novate'];
  } else {
    $u["ally_range"] = $ranks[$u['ally_rank_id']]['name'];
  }

  $u["dpath"] = $dpath;
  $u['points'] = "" . pretty_number($UserPoints['total_points']) . "";

  if ($u['ally_register_time'] > 0)
    $u['ally_register_time'] = date("Y-m-d h:i:s", $u['ally_register_time']);
  else
    $u['ally_register_time'] = "-";

  $page_list .= parsetemplate($template, $u);
}
// para cambiar el link de ordenar.
if ($sort2 == 1) {
  $s = 2;
} elseif ($sort2 == 2) {
  $s = 1;
} else {
  $s = 1;
}

if ($i != $ally['ally_members']) {
  doquery("UPDATE {{table}} SET `ally_members`='{$i}' WHERE `id`='{$ally['id']}'", 'alliance');
}

$parse = $lang;
$parse['i'] = $i;
$parse['s'] = $s;
$parse['list'] = $page_list;

$page_title = $lang['Members_list'];

$page .= parsetemplate(gettemplate('alliance_memberslist_table'), $parse);
display($page, $page_title);
?>