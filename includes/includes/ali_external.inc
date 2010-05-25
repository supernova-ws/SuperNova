<?php
  if ($mode == 'make' && $user['ally_request'] == 0) { // Make alliance
    /* Aca se crean las alianzas... */
    if ($yes) {
      // Por el momento solo estoy improvisando, luego se perfeccionara el sistema :)
      // Creo que aqui se realiza una query para comprovar el nombre, y luego le pregunta si es el tag correcto...
      if (!$POST_atag) {
        message($lang['have_not_tag'], $lang['make_alliance']);
      }
      if (!$POST_aname) {
        message($lang['have_not_name'], $lang['make_alliance']);
      }

      $tagquery = doquery("SELECT * FROM {{table}} WHERE `ally_tag` ='{$POST_atag}'", 'alliance', true);

      if ($tagquery) {
        message(str_replace('%s', $POST_atag, $lang['always_exist']), $lang['make_alliance']);
      }

      doquery("INSERT INTO {{table}} SET
      `ally_name`='{$POST_aname}',
      `ally_tag`='{$POST_atag}' ,
      `ally_owner`='{$user['id']}',
      `ally_owner_range`='Leader',
      `ally_members`='1',
      `ally_register_time`=" . time() , "alliance");

      $allyquery = doquery("SELECT * FROM {{table}} WHERE `ally_tag` ='{$POST_atag}'", 'alliance', true);

      doquery("UPDATE {{table}} SET
      `ally_id`='{$allyquery['id']}',
      `ally_name`='{$allyquery['ally_name']}',
      `ally_register_time`='" . time() . "'
      WHERE `id`='{$user['id']}'", "users");

      $page = MessageForm(str_replace('%s', $POST_atag, $lang['ally_maked']),

        str_replace('%s', $POST_atag, $lang['ally_been_maked']) . "<br><br>", "", $lang['Ok']);
    } else {
      $page .= parsetemplate(gettemplate('alliance_make'), $lang);
    }

    display($page, $lang['make_alliance']);
  }

  if ($mode == 'search' && $user['ally_request'] == 0) { // search one
    /* Buscador de alianzas */
    $parse = $lang;
    $lang['searchtext'] = $POST_searchtext;
    $page = parsetemplate(gettemplate('alliance_searchform'), $lang);

    if ($POST_searchtext) { // esta parte es igual que el buscador de search.php...
      // searchtext
      $search = doquery("SELECT * FROM {{table}} WHERE `ally_name` LIKE '%{$POST_searchtext}%' OR `ally_tag` LIKE '%{$POST_searchtext}%' LIMIT 30", "alliance");

      if (mysql_num_rows($search) != 0) {
        $template = gettemplate('alliance_searchresult_row');

        while ($s = mysql_fetch_array($search)) {
          $entry = array();
          $entry['ally_tag'] = "[<a href=\"alliance.php?mode=apply&allyid={$s['id']}\">{$s['ally_tag']}</a>]";
          $entry['ally_name'] = $s['ally_name'];
          $entry['ally_members'] = $s['ally_members'];

          $parse['result'] .= parsetemplate($template, $entry);
        }

        $page .= parsetemplate(gettemplate('alliance_searchresult_table'), $parse);
      }
    }

    display($page, $lang['search_alliance']);
  }

  if ($mode == 'apply' && $user['ally_request'] == 0) { // solicitudes
    if($allyid) {
      $alianza = doquery("SELECT * FROM {{table}} WHERE `id` ='{$allyid}'", "alliance", true); }
         if($alianza['ally_request_notallow'] == 1) { message($lang['not_possible_app']); } else {
    if (!is_numeric($allyid) || !$allyid || $user['ally_request'] != 0 || $user['ally_id'] != 0) {
      message($lang['not_possible_app'], $lang['not_possible_app']);
    }
    // pedimos la info de la alianza
    $allyrow = doquery("SELECT ally_tag,ally_request FROM {{table}} WHERE `id` ='" . $allyid . "'", "alliance", true);

    if (!$allyrow) {
      message($lang['not_possible_app'], $lang['not_possible_app']);
    }

    extract($allyrow);

    if ($POST_further == $lang['Send']) { // esta parte es igual que el buscador de search.php...
      doquery("UPDATE {{table}} SET `ally_request`='" . intval($allyid) . "', ally_request_text='" . $POST_text . "', ally_register_time='" . time() . "' WHERE `id`='" . $user['id'] . "'", "users");
      // mensaje de cuando se envia correctamente el mensaje
      message($lang['apply_registered'], $lang['your_apply']);
      // mensaje de cuando falla el envio
      // message($lang['apply_cantbeadded'], $lang['your_apply']);
    } else {
      $text_apply = ($ally_request) ? $ally_request : $lang['no_req_text'];
    }

    $parse = $lang;
    $parse['allyid'] = $allyid;
    $parse['chars_count'] = strlen($text_apply);
    $parse['text_apply'] = $text_apply;
    $parse['Write_to_alliance'] = str_replace('%s', $ally_tag, $lang['Write_to_alliance']);

    $page = parsetemplate(gettemplate('alliance_applyform'), $parse);

    display($page, $lang['Write_to_alliance']);
    }
  }

  if ($user['ally_request'] != 0) { // Esperando una respuesta
    // preguntamos por el ally_tag
    $allyquery = doquery("SELECT `ally_tag` FROM {{table}} WHERE `id` ='" . intval($user['ally_request']) . "' ORDER BY `id`", "alliance", true);

    extract($allyquery);
    if ($POST_bcancel) {
      doquery("UPDATE {{table}} SET `ally_request`=0 WHERE `id`=" . $user['id'], "users");

      $lang['request_text'] = str_replace('%s', $ally_tag, $lang['Canceld_req_text']);
      $lang['button_text'] = $lang['Ok'];
      $page = parsetemplate(gettemplate('alliance_apply_waitform'), $lang);
    } else {
      $lang['request_text'] = str_replace('%s', $ally_tag, $lang['Waiting_req_text']);
      $lang['button_text'] = $lang['Delete_apply'];
      $page = parsetemplate(gettemplate('alliance_apply_waitform'), $lang);
    }
    // mysql_escape_string(strip_tags());
    display($page, "Deine Anfrage");
  } else { // Vista sin allianza
    /* Vista normal de cuando no se tiene ni solicitud ni alianza */
    $page .= parsetemplate(gettemplate('alliance_defaultmenu'), $lang);
    display($page, $lang['alliance']);
  }
?>