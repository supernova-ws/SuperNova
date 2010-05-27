<?php

if ($ally['ally_owner'] != $user['id'] && !$user_can_edit_rights) {
  message($lang['Denied_access'], $lang['Members_list']);
} elseif (!empty($POST_newrangname)) {
  $name = mysql_escape_string(strip_tags($POST_newrangname));

  $allianz_raenge[] = array('name' => $name,
    'mails' => 0,
    'delete' => 0,
    'kick' => 0,
    'bewerbungen' => 0,
    'administrieren' => 0,
    'bewerbungenbearbeiten' => 0,
    'memberlist' => 0,
    'onlinestatus' => 0,
    'rechtehand' => 0
    );

  $ranks = serialize($allianz_raenge);

  doquery("UPDATE {{table}} SET `ally_ranks`='" . $ranks . "' WHERE `id`=" . $ally['id'], "alliance");

  $goto = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];

  header("Location: " . $goto);
  exit();
} elseif ($POST_id != '' && is_array($POST_id)) {
  $ally_ranks_new = array();

  foreach ($POST_id as $id) {
    $id = intval($id);
    $name = $allianz_raenge[$id]['name'];

    $ally_ranks_new[$id]['name'] = $name;

    if (isset($_POST['u' . $id . 'r0'])) {
      $ally_ranks_new[$id]['delete'] = 1;
    } else {
      $ally_ranks_new[$id]['delete'] = 0;
    }

    if (isset($_POST['u' . $id . 'r1']) && $ally['ally_owner'] == $user['id']) {
      $ally_ranks_new[$id]['kick'] = 1;
    } else {
      $ally_ranks_new[$id]['kick'] = 0;
    }

    if (isset($_POST['u' . $id . 'r2'])) {
      $ally_ranks_new[$id]['bewerbungen'] = 1;
    } else {
      $ally_ranks_new[$id]['bewerbungen'] = 0;
    }

    if (isset($_POST['u' . $id . 'r3'])) {
      $ally_ranks_new[$id]['memberlist'] = 1;
    } else {
      $ally_ranks_new[$id]['memberlist'] = 0;
    }

    if (isset($_POST['u' . $id . 'r4'])) {
      $ally_ranks_new[$id]['bewerbungenbearbeiten'] = 1;
    } else {
      $ally_ranks_new[$id]['bewerbungenbearbeiten'] = 0;
    }

    if (isset($_POST['u' . $id . 'r5'])) {
      $ally_ranks_new[$id]['administrieren'] = 1;
    } else {
      $ally_ranks_new[$id]['administrieren'] = 0;
    }

    if (isset($_POST['u' . $id . 'r6'])) {
      $ally_ranks_new[$id]['onlinestatus'] = 1;
    } else {
      $ally_ranks_new[$id]['onlinestatus'] = 0;
    }

    if (isset($_POST['u' . $id . 'r7'])) {
      $ally_ranks_new[$id]['mails'] = 1;
    } else {
      $ally_ranks_new[$id]['mails'] = 0;
    }

    if (isset($_POST['u' . $id . 'r8'])) {
      $ally_ranks_new[$id]['rechtehand'] = 1;
    } else {
      $ally_ranks_new[$id]['rechtehand'] = 0;
    }
  }

  $ranks = serialize($ally_ranks_new);

  doquery("UPDATE {{table}} SET `ally_ranks`='" . $ranks . "' WHERE `id`=" . $ally['id'], "alliance");

  $goto = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];

  header("Location: " . $goto);
  exit();
}
// borrar una entrada
elseif (isset($d) && isset($ally_ranks[$d])) {
  unset($ally_ranks[$d]);
  $ally['ally_rank'] = serialize($ally_ranks);

  doquery("UPDATE {{table}} SET `ally_ranks`='{$ally['ally_rank']}' WHERE `id`={$ally['id']}", "alliance");
}

if (count($ally_ranks) == 0 || $ally_ranks == '') { // si no hay rangos
  $list = "<th>{$lang['There_is_not_range']}</th>";
} else { // Si hay rangos
  // cargamos la template de tabla
  $list = parsetemplate(gettemplate('alliance_admin_laws_head'), $lang);
  $template = gettemplate('alliance_admin_laws_row');
  // Creamos la lista de rangos
  $i = 0;

  foreach($ally_ranks as $a => $b) {
    if ($ally['ally_owner'] == $user['id']) {
      // $i++;u2r5
      $lang['id'] = $a;
      $lang['delete'] = "<a href=\"alliance.php?mode=admin&edit=rights&d={$a}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"{$lang['Delete_range']}\" border=0></a>";
      $lang['r0'] = $b['name'];
      $lang['a'] = $a;
      $lang['r1'] = "<input type=checkbox name=\"u{$a}r0\"" . (($b['delete'] == 1)?' checked="checked"':'') . ">"; //{$b[1]}
      $lang['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1)?' checked="checked"':'') . ">";
      $lang['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1)?' checked="checked"':'') . ">";
      $lang['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1)?' checked="checked"':'') . ">";
      $lang['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1)?' checked="checked"':'') . ">";
      $lang['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1)?' checked="checked"':'') . ">";
      $lang['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1)?' checked="checked"':'') . ">";
      $lang['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1)?' checked="checked"':'') . ">";
      $lang['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1)?' checked="checked"':'') . ">";

      $list .= parsetemplate($template, $lang);
    } else {
      $lang['id'] = $a;
      $lang['r0'] = $b['name'];
      $lang['delete'] = "<a href=\"alliance.php?mode=admin&edit=rights&d={$a}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"{$lang['Delete_range']}\" border=0></a>";
      $lang['a'] = $a;
      $lang['r1'] = "<b>-</b>";
      $lang['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1)?' checked="checked"':'') . ">";
      $lang['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1)?' checked="checked"':'') . ">";
      $lang['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1)?' checked="checked"':'') . ">";
      $lang['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1)?' checked="checked"':'') . ">";
      $lang['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1)?' checked="checked"':'') . ">";
      $lang['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1)?' checked="checked"':'') . ">";
      $lang['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1)?' checked="checked"':'') . ">";
      $lang['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1)?' checked="checked"':'') . ">";

      $list .= parsetemplate($template, $lang);
    }
  }

  if (count($ally_ranks) != 0) {
    $list .= parsetemplate(gettemplate('alliance_admin_laws_feet'), $lang);
  }
}

$lang['list'] = $list;
$lang['dpath'] = $dpath;
$page .= parsetemplate(gettemplate('alliance_admin_laws'), $lang);

display($page, $lang['Law_settings']);
?>