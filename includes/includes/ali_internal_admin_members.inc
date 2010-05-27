<?php

/*
En la administrar a los miembros se pueden establecer los rangos
para dar los diferentes derechos "Leyes"
*/
// comprobamos el permiso
if ($ally['ally_owner'] != $user['id'] && !$user_can_kick) {
  message($lang['Denied_access'], $lang['Members_list']);
}

/*
Kickear usuarios requiere el permiso numero 1
*/
if (isset($kick)) {
  if ($ally['ally_owner'] != $user['id'] && !$user_can_kick) {
    message($lang['Denied_access'], $lang['Members_list']);
  }

  $u = doquery("SELECT * FROM {{table}} WHERE `id` ='{$kick}' LIMIT 1", 'users', true);
  // kickeamos!
  if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner']) {
    doquery("UPDATE {{table}} SET `ally_id`='0', `ally_name` = '' WHERE `id`='{$u['id']}'", 'users');
  }
} elseif (isset($POST_newrang)) {
  $q = doquery("SELECT * FROM {{table}} WHERE `id` ='{$u}' LIMIT 1", 'users', true);

  if ((isset($ally_ranks[$POST_newrang-1]) || $POST_newrang == 0) && $q['id'] != $ally['ally_owner']) {
    doquery("UPDATE {{table}} SET `ally_rank_id`='" . mysql_escape_string(strip_tags($POST_newrang)) . "' WHERE `id`='" . intval($id) . "'", 'users');
  }
}
// obtenemos las template row
$template = gettemplate('alliance_admin_members_row');
$f_template = gettemplate('alliance_admin_members_function');
// El orden de aparicion
if ($sort2) {
  // agregar el =0 para las coordenadas...
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
  $listuser = doquery("SELECT * FROM {{table}} WHERE ally_id={$user['ally_id']}", 'users');
}
// contamos la cantidad de usuarios.
$i = 0;
// Como es costumbre. un row template
$page_list = '';
$lang['memberzahl'] = mysql_num_rows($listuser);

while ($u = mysql_fetch_array($listuser)) {
  $UserPoints = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $u['id'] . "';", 'statpoints', true);
  $i++;
  $u['i'] = $i;
  // Dias de inactivos
  $u['points'] = "" . pretty_number($u['total_points']) . "";
  $days = floor(round(time() - $u["onlinetime"]) / 3600 % 24);
  $u["onlinetime"] = str_replace("%s", $days, "%s d");
  // Nombre de rango
  if ($ally['ally_owner'] == $u['id']) {
    $ally_range = ($ally['ally_owner_range'] == '')?$lang['Founder']:$ally['ally_owner_range'];
  } elseif ($u['ally_rank_id'] == 0 || !isset($ally_ranks[$u['ally_rank_id']-1]['name'])) {
    $ally_range = $lang['Novate'];
  } else {
    $ally_range = $ally_ranks[$u['ally_rank_id']-1]['name'];
  }

  /*
  Aca viene la parte jodida...
  */
  if ($ally['ally_owner'] == $u['id'] || $user['id'] == $u['id']) {
    $u["functions"] = '';
  } elseif ($user_can_kick || $ally['ally_owner'] == $user['id']) {
    $f['dpath'] = $dpath;
    $f['Expel_user'] = $lang['Expel_user'];
    $f['Set_range'] = $lang['Set_range'];
    $f['Kick_sure'] = str_replace("%s", $u['username'], $lang['Kick_sure']);
    $f['id'] = $u['id'];
    $u["functions"] = parsetemplate($f_template, $f);
  } else {
    $u["functions"] = '';
  }
  $u["dpath"] = $dpath;
  // por el formulario...
  if ($rank != $u['id']) {
    $u['ally_range'] = $ally_range;
  } else {
    $u['ally_range'] = '';
  }
  $u['ally_register_time'] = date("Y-m-d h:i:s", $u['ally_register_time']);
  $page_list .= parsetemplate($template, $u);
  if ($rank == $u['id']) {
    $r['Rank_for'] = str_replace("%s", $u['username'], $lang['Rank_for']);
    $r['options'] .= "<option value=\"0\">{$lang['Novate']}</option>";

    foreach($ally_ranks as $a => $b) {
      $r['options'] .= "<option value=\"" . ($a + 1) . "\"";
      if ($u['ally_rank_id']-1 == $a) {
        $r['options'] .= ' selected=selected';
      }
      $r['options'] .= ">{$b['name']}</option>";
    }
    $r['id'] = $u['id'];
    $r['Save'] = $lang['Save'];
    $page_list .= parsetemplate(gettemplate('alliance_admin_members_row_edit'), $r);
  }
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

$lang['memberslist'] = $page_list;
$lang['s'] = $s;
$page .= parsetemplate(gettemplate('alliance_admin_members_table'), $lang);

display($page, $lang['members_admin']);
// a=9 es para cambiar la etiqueta de la etiqueta.
// a=10 es para cambiarle el nombre de la alianza

?>