<?php
/**
 * alliance.php
 *
 * Alliance control page
 *
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSTALL' , false);
define('INSIDE', true);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);

$mode       = SYS_mysqlSmartEscape($_GET['mode']);
$a          = intval($_GET['a']);
$edit       = SYS_mysqlSmartEscape($_GET['edit']);
$allyid     = intval($_GET['allyid']);
$d          = intval($_GET['d']);
$yes        = intval($_GET['yes']);
$sort1      = intval($_GET['sort1']);
$sort2      = intval($_GET['sort2']);
$t          = intval($_GET['t']);
$rank       = intval($_GET['rank']);
$kick       = intval($_GET['kick']);
$id         = intval($_GET['id']);
$show       = intval($_GET['show']);
$sendmail   = intval($_GET['sendmail']);
$tag        = SYS_mysqlSmartEscape($_GET['tag']);
$POST_atag  = SYS_mysqlSmartEscape($_POST['atag']);
$POST_aname = SYS_mysqlSmartEscape($_POST['aname']);
$POST_searchtext = SYS_mysqlSmartEscape($_POST['searchtext']);
$POST_text = SYS_mysqlSmartEscape($_POST['text']);
$POST_action = SYS_mysqlSmartEscape($_POST['action']);
$POST_r = intval($_POST['r']);
$POST_id = $_POST['id']; // pretty safe 'cause it's array. We will handle it's later
$POST_further = SYS_mysqlSmartEscape($_POST['further']);
$POST_bcancel = SYS_mysqlSmartEscape($_POST['bcancel']);
$POST_newrangname = SYS_mysqlSmartEscape($_POST['newrangname']);
$POST_owner_range = SYS_mysqlSmartEscape($_POST['owner_range']);
$POST_web = SYS_mysqlSmartEscape($_POST['web']);
$POST_image = SYS_mysqlSmartEscape($_POST['image']);
$POST_request_notallow = intval($_POST['request_notallow']);
$POST_newleader = SYS_mysqlSmartEscape($_POST['newleader']);
$POST_options = SYS_mysqlSmartEscape($_POST['options']);
$POST_newrang = SYS_mysqlSmartEscape($_POST['newrang']);
$POST_newname = SYS_mysqlSmartEscape($_POST['newname']);
$POST_newtag = SYS_mysqlSmartEscape($_POST['newtag']);

includeLang('alliance');


/*
  Alianza consiste en tres partes.
  La primera es la comun. Es decir, no se necesita comprobar si se esta en una alianza o no.
  La segunda, es sin alianza. Eso implica las solicitudes.
  La ultima, seria cuando ya se esta dentro de una.
*/
// Parte inicial.

if ($mode == 'ainfo') {
  // Evitamos errores casuales xD
  // query
  if (isset($tag)) {
    $allyrow = doquery("SELECT * FROM {{table}} WHERE ally_tag='{$tag}'", "alliance", true);
  } elseif (is_numeric($a) && $a != 0) {
    $allyrow = doquery("SELECT * FROM {{table}} WHERE id='{$a}'", "alliance", true);
  } else {
    message($lang['Ally_not_exist'], $lang['Ally_info_1']);
  }
  // Si no existe
  if (!$allyrow) {
    message($lang['Ally_not_exist'], $lang['Ally_info_1']);
  }
  extract($allyrow);

  if ($ally_image != "") {
    $ally_image = "<tr><th colspan=2><img src=\"{$ally_image}\"></td></tr>";
  }

  if ($ally_description != "") {
    $ally_description = "<tr><th colspan=2 height=100>{$ally_description}</th></tr>";
  } else
    $ally_description = "<tr><th colspan=2 height=100>{$lang['Ally_nodescription']}</th></tr>";

  if ($ally_web != "") {
    $ally_web = "<tr>
    <th>{$lang['Initial_page']}</th>
    <th><a href=\"{$ally_web}\">{$ally_web}</a></th>
    </tr>";
  }

  $lang['ally_member_scount'] = $ally_members;
  $lang['ally_name'] = $ally_name;
  $lang['ally_tag'] = $ally_tag;
  // codigo raro
  $patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
  $replacements[] = '<font color="\1">\2</font>';
  $patterns[] = '#\[img\](.*?)\[/img\]#Smi';
  $replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
  $patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
  $replacements[] = '<font color="\1">';
  $patterns[] = "#\[/f\]#Ssi";
  $replacements[] = '</font>';
  $ally_description = preg_replace($patterns, $replacements, $ally_description);

  $lang['ally_description'] = nl2br($ally_description);
  $lang['ally_image'] = $ally_image;
  $lang['ally_web'] = $ally_web;

  if ($user['ally_id'] == 0) {
    $lang['bewerbung'] = "<tr>
    <th>Bewerben</th>
    <th><a href=\"alliance.php?mode=apply&amp;allyid=" . $id . "\">{$lang['Click_writerequest']}</a></th>

  </tr>";
  } else
    $lang['bewerbung'] = "";

  $page .= parsetemplate(gettemplate('alliance_ainfo'), $lang);
  display($page, str_replace('%s', $ally_name, $lang['Info_of_Alliance']));
}
// --[Comprobaciones de alianza]-------------------------
if ($user['ally_id'] == 0) { // Sin alianza
  if ($mode == 'make' && $user['ally_request'] == 0) { // Make alliance
    /*
    Aca se crean las alianzas...
  */
    if ($yes) {
      /*
      Por el momento solo estoy improvisando, luego se perfeccionara el sistema :)
      Creo que aqui se realiza una query para comprovar el nombre, y luego le pregunta si es el tag correcto...
    */
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
    /*
    Buscador de alianzas
  */
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
    /*
    Vista normal de cuando no se tiene ni solicitud ni alianza
  */
    $page .= parsetemplate(gettemplate('alliance_defaultmenu'), $lang);
    display($page, $lang['alliance']);
  }
}

//---------------------------------------------------------------------------------------------------------------------------------------------------
// Parte de adentro de la alianza
elseif ($user['ally_id'] != 0 && $user['ally_request'] == 0) { // Con alianza
  // query para la allyrow
  $ally = doquery("SELECT * FROM {{table}} WHERE `id` ='{$user['ally_id']}'", "alliance", true);

  $ally_ranks = unserialize($ally['ally_ranks']);

  $allianz_raenge = unserialize($ally['ally_ranks']);

    if ($allianz_raenge[$user['ally_rank_id']-1]['memberlist'] == 1 || $ally['ally_owner'] == $user['id']) {
    $user_can_watch_memberlist = true;
  } else
    $user_can_watch_memberlist = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['mails'] == 1 || $ally['ally_owner'] == $user['id']) {
    $user_can_send_mails = true;
  } else
    $user_can_send_mails = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['kick'] == 1 || $ally['ally_owner'] == $user['id']) {
    $user_can_kick = true;
  } else
    $user_can_kick = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['rechtehand'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_edit_rights = true;
  else
    $user_can_edit_rights = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['delete'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_exit_alliance = true;
  else
    $user_can_exit_alliance = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['bewerbungen'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_see_applications = true;
  else
    $user_can_see_applications = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['bewerbungenbearbeiten'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_admin_applications = true;
  else
    $user_admin_applications = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['administrieren'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_admin = true;
  else
    $user_admin = false;

  if ($allianz_raenge[$user['ally_rank_id']-1]['onlinestatus'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_onlinestatus = true;
  else
    $user_onlinestatus = false;

  if (!$ally) {
    doquery("UPDATE {{table}} SET `ally_id`=0 WHERE `id`='{$user['id']}'", "users");
    message($lang['ally_notexist'], $lang['your_alliance'], 'alliance.php');
  }

  if ($mode == 'exit') {
    if ($ally['ally_owner'] == $user['id']) {
      message($lang['Owner_cant_go_out'], $lang['Alliance']);
    }
    // se sale de la alianza
    if ($yes == 1) {
      doquery("UPDATE {{table}} SET `ally_id`=0, `ally_name` = '' WHERE `id`='{$user['id']}'", "users");
      $lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['Go_out_welldone']);
      $page = MessageForm($lang['Go_out_welldone'], "<br>", $PHP_SELF, $lang['Ok']);
      // Se quitan los puntos del user en la alianza
    } else {
      // se pregunta si se quiere salir
      $lang['Want_go_out'] = str_replace("%s", $ally_name, $lang['Want_go_out']);
      $page = MessageForm($lang['Want_go_out'], "<br>", "?mode=exit&yes=1", $lang['Ok']);
    }
    display($page);
  }

  if ($mode == 'memberslist') { // Lista de miembros.
    // obtenemos el array de los rangos
    // $ally_ranks = unserialize($ally['ally_ranks']);
    $allianz_raenge = unserialize($ally['ally_ranks']);
    // $user_can_watch_memberlist
    // comprobamos el permiso
    if ($ally['ally_owner'] != $user['id'] && !$user_can_watch_memberlist) {
      message($lang['Denied_access'], $lang['Members_list']);
    }
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
        $u["ally_range"] = $allianz_raenge[$u['ally_rank_id']-1]['name'];
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

    $page .= parsetemplate(gettemplate('alliance_memberslist_table'), $parse);

    display($page, $lang['Members_list']);
  }

  if ($mode == 'circular') { // Correo circular
    /*
    Mandar un correo circular.
    creo que aqui tendria que ver yo como crear el sistema de mensajes...
  */
    // un loop para mostrar losrangos
    $allianz_raenge = unserialize($ally['ally_ranks']);
    // comprobamos el permiso
    if ($ally['ally_owner'] != $user['id'] && !$user_can_send_mails) {
      message($lang['Denied_access'], $lang['Send_circular_mail']);
    }

    if ($sendmail == 1) {
      $POST_text = mysql_escape_string(strip_tags($POST_text));

      if ($POST_r == 0) {
        $sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}'", "users");
      } else {
        $sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
      }
      // looooooop
      $list = '';
      while ($u = mysql_fetch_array($sq)) {
        doquery("INSERT INTO {{table}} SET
        `message_owner`='{$u['id']}',
        `message_sender`='{$user['id']}' ,
        `message_time`='" . time() . "',
        `message_type`='2',
        `message_from`='{$ally['ally_tag']}',
        `message_subject`='{$user['username']}',
        `message_text`='{$POST_text}'
        ", "messages");
        $list .= "<br>{$u['username']} ";
      }
      // doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}' ORDER BY `id`","users");
      doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
      doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
      /*
      Aca un mensajito diciendo que a quien se mando.
    */
      $page = MessageForm($lang['Circular_sended'], $lang['members_who_recived_message'] . $list, "alliance.php", $lang['Ok'], true);
      display($page, $lang['Send_circular_mail']);
    }

    $lang['r_list'] = "<option value=\"0\">{$lang['All_players']}</option>";
    if ($allianz_raenge) {
      foreach($allianz_raenge as $id => $array) {
        $lang['r_list'] .= "<option value=\"" . ($id + 1) . "\">" . $array['name'] . "</option>";
      }
    }

    $page .= parsetemplate(gettemplate('alliance_circular'), $lang);

    display($page, $lang['Send_circular_mail']);
  }

  if ($mode == 'admin' && $edit == 'rights') { // Administrar leyes
    $allianz_raenge = unserialize($ally['ally_ranks']);

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
  }

  if ($mode == 'admin' && $edit == 'ally') {

    if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
      message($lang['Denied_access'], $lang['Send_circular_mail']);
    }

    if ($t != 1 && $t != 2 && $t != 3) {
      $t = 1;
    }
    // post!

    if ($POST_options) {
      $ally['ally_owner_range'] = $POST_owner_range;
      $ally['ally_web'] = $POST_web;
      $ally['ally_image'] = $POST_image;
      $ally['ally_request_notallow'] = $POST_request_notallow;

      if ($ally['ally_request_notallow'] != 0 && $ally['ally_request_notallow'] != 1) {
        message("You at \"Applications\" an option from the form!", "Mistake");
        exit;
      }

      doquery("UPDATE {{table}} SET
      `ally_owner_range`='{$ally['ally_owner_range']}',
      `ally_image`='{$ally['ally_image']}',
      `ally_web`='{$ally['ally_web']}',
      `ally_request_notallow`='{$ally['ally_request_notallow']}'
      WHERE `id`='{$ally['id']}'", "alliance");
    } elseif ($t) {
      if ($t == 3) {
        $ally['ally_request'] = strip_tags($POST_text);

        doquery("UPDATE {{table}} SET
        `ally_request`='{$ally['ally_request']}'
        WHERE `id`='{$ally['id']}'", "alliance");
      } elseif ($t == 2) {
        $ally['ally_text'] = strip_tags($POST_text);
        doquery("UPDATE {{table}} SET
        `ally_text`='{$ally['ally_text']}'
        WHERE `id`='{$ally['id']}'", "alliance");
      } else {
        $ally['ally_description'] = strip_tags($POST_text);

        doquery("UPDATE {{table}} SET
        `ally_description`='" . $ally['ally_description'] . "'
        WHERE `id`='{$ally['id']}'", "alliance");
      }
    }
    $lang['dpath'] = $dpath;
    /*
    Depende del $t, muestra el formulario para cada tipo de texto.
  */
    if ($t == 3) {
      $lang['request_type'] = $lang['Show_of_request_text'];
    } elseif ($t == 2) {
      $lang['request_type'] = $lang['Internal_text'];
    } else {
      $lang['request_type'] = $lang['Public_text_of_alliance'];
    }

    if ($t == 2) {
      $lang['text'] = $ally['ally_text'];
      $lang['Texts'] = $lang['Texts'];
      $lang['Show_of_request_text'] = $lang['Internal_text'];
    } elseif ($t == 1) {
      $lang['text'] = $ally['ally_description'];
      $lang['Show_of_request_text'] = $lang['Public_text_of_alliance'];
    } else {
        $lang['text'] = $ally['ally_request'];
      $lang['Show_of_request_text'] = $lang['Show_of_request_text'];
          }

    $lang['t'] = $t;

    $lang['ally_web'] = $ally['ally_web'];
    $lang['ally_image'] = $ally['ally_image'];
    $lang['ally_request_notallow_0'] = (($ally['ally_request_notallow'] == 1) ? ' SELECTED' : '');
    $lang['ally_request_notallow_1'] = (($ally['ally_request_notallow'] == 0) ? ' SELECTED' : '');
    $lang['ally_owner_range'] = $ally['ally_owner_range'];
    $lang['Ally_transfer'] = MessageForm($lang['Ally_transfer'], "", "?mode=admin&edit=transfer", $lang['Continue']);
    $lang['ally_dissolve'] = MessageForm($lang['ally_dissolve'], "", "?mode=admin&edit=exit", $lang['Continue']);

    $page .= parsetemplate(gettemplate('alliance_admin'), $lang);
    display($page, $lang['ally_admin']);
  }

  if ($mode == 'admin' && $edit == 'members') { // Administrar a los miembros
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
  }


  if ($mode == 'admin' && $edit == 'requests') { // Administrar solicitudes
    if ($ally['ally_owner'] != $user['id'] && !$user_admin_applications) {
      message($lang['Denied_access'], $lang['requests_admin']);
    }

    if ($POST_action == $lang['Accept_cand']) {
      $u = doquery("SELECT * FROM {{table}} WHERE `id` =$show", 'users', true);
      // agrega los puntos al unirse el user a la alianza
      doquery("UPDATE {{table}} SET
      `ally_members` = ally_members+1
      WHERE `id` ='{$ally['id']}'", 'alliance');

      doquery("UPDATE {{table}} SET
      `ally_name` ='{$ally['ally_name']}',
      `ally_request_text` ='',
      `ally_request` ='0',
      `ally_id` ='{$ally['id']}',
      `new_message` =new_message+1,
      `mnl_alliance` =mnl_alliance+1
      WHERE `id` ='{$show}'", 'users');
      // Se envia un mensaje avizando...

      doquery("INSERT INTO {{table}} SET
      `message_owner`='{$show}',
      `message_sender`='{$user['id']}' ,
      `message_time`='" . time() . "',
      `message_type`='2',
      `message_from`='{$ally['ally_tag']}',
      `message_subject`='[" . $ally['ally_name'] . "] {$lang['Aplication_acepted_subjet']}',
      `message_text`='{$lang['Aplication_hello']}<b>" . $ally['ally_name'] . "</b> {$lang['Aplication_acepted']}" . $POST_text . "'", "messages");

      header('Location:alliance.php?mode=admin&edit=requests');
      die();

    } elseif ($POST_action == $lang['Reject_cand'] && $POST_action != '') {
      $POST_text = mysql_escape_string(strip_tags($POST_text));

      doquery("UPDATE {{table}} SET ally_request_text='',ally_request='0',ally_id='0',new_message=new_message+1, mnl_alliance=mnl_alliance+1 WHERE id='{$show}'", 'users');
      // Se envia un mensaje avizando...
      doquery("INSERT INTO {{table}} SET
      `message_owner`='{$show}',
      `message_sender`='{$user['id']}' ,
      `message_time`='" . time() . "',
      `message_type`='2',
      `message_from`='{$ally['ally_tag']}',
      `message_subject`='[" . $ally['ally_name'] . "] {$lang['Aplication_rejected_subjet']}',
      `message_text`='{$lang['Aplication_hello']}<b>" . $ally['ally_name'] . "</b> {$lang['Aplication_rejected']}" . $POST_text . "'", "messages");

      header('Location:alliance.php?mode=admin&edit=requests');
      die();
    }

    $row = gettemplate('alliance_admin_request_row');
    $i = 0;
    $parse = $lang;
    $query = doquery("SELECT id,username,ally_request_text,ally_register_time FROM {{table}} WHERE ally_request='{$ally['id']}'", 'users');
    while ($r = mysql_fetch_array($query)) {
      // recolectamos los datos del que se eligio.
      if (isset($show) && $r['id'] == $show) {
        $s['username'] = $r['username'];
        $s['ally_request_text'] = nl2br($r['ally_request_text']);
        $s['id'] = $r['id'];
      }
      // la fecha de cuando se envio la solicitud
      $r['time'] = date("Y-m-d h:i:s", $r['ally_register_time']);
      $parse['list'] .= parsetemplate($row, $r);
      $i++;
    }
    if ($parse['list'] == '') {
      $parse['list'] = '<tr><th colspan=2> Больше нет заявок </th></tr>';
    }
    // Con $show
    if (isset($show) && $show != 0 && $parse['list'] != '') {
      // Los datos de la solicitud
      $s['Request_from'] = str_replace('%s', $s['username'], $lang['Request_from']);
      $s['Motive_optional'] = $lang['Motive_optional'];
      $s['Request_answer'] = $lang['Request_answer'];
      $s['Accept_cand'] = $lang['Accept_cand'];
      $s['Reject_cand'] = $lang['Reject_cand'];
      // el formulario
      $parse['request'] = parsetemplate(gettemplate('alliance_admin_request_form'), $s);
      $parse['request'] = parsetemplate($parse['request'], $lang);
    } else {
      $parse['request'] = '';
    }

    $parse['ally_tag'] = $ally['ally_tag'];
    $parse['Back'] = $lang['Back'];

    $parse['There_is_hanging_request'] = str_replace('%n', $i, $lang['There_is_hanging_request']);
    // $parse['list'] = $lang['Return_to_overview'];
    $page = parsetemplate(gettemplate('alliance_admin_request_table'), $parse);
    display($page, $lang['requests_admin']);
  }

  if ($mode == 'admin' && $edit == 'name') {
     // Changer le nom de l'alliance

    $ally_ranks = unserialize($ally['ally_ranks']);
    // comprobamos el permiso
    if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
      message($lang['Denied_access'], $lang['Members_list']);
    }

    if ($POST_newname) {
      // Y a le nouveau Nom
      $ally['ally_name'] = mysql_escape_string(strip_tags($POST_newname));
      doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `id` = '". $user['ally_id'] ."';", 'alliance');
      doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `ally_id` = '". $ally['id'] ."';", 'users');
    }

    $parse['question']           = str_replace('%s', $ally['ally_name'], $lang['Future_allyname']);
    $parse['New_name']           = $lang['New_name'];
    $parse['Change']             = $lang['Change'];
    $parse['name']               = 'newname';
    $parse['Return_to_overview'] = $lang['Return_to_overview'];
    $page .= parsetemplate(gettemplate('alliance_admin_rename'), $parse);
    display($page, $lang['ally_admin']);

  }

  if ($mode == 'admin' && $edit == 'tag') {
    // Changer le TAG l'alliance
    $ally_ranks = unserialize($ally['ally_ranks']);

    // Bon si on verifiait les autorisation ?
    if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
      message($lang['Denied_access'], $lang['Members_list']);
    }

    if ($POST_newtag) {
      // Y a le nouveau TAG
      $ally['ally_tag'] = mysql_escape_string(strip_tags($POST_newtag));
      doquery("UPDATE {{table}} SET `ally_tag` = '". $ally['ally_tag'] ."' WHERE `id` = '". $user['ally_id'] ."';", 'alliance');
    }

    $parse['question']           = str_replace('%s', $ally['ally_tag'], $lang['Future_allytag']);
    $parse['New_name']           = $lang['New_tag'];
    $parse['Change']             = $lang['Change'];
    $parse['name']               = 'newtag';
    $parse['Return_to_overview'] = $lang['Return_to_overview'];
    $page .= parsetemplate(gettemplate('alliance_admin_rename'), $parse);
    display($page, $lang['ally_admin']);
  }

  if ($mode == 'admin' && $edit == 'exit') { // disolver una alianza
    // obtenemos el array de los rangos
    $ally_ranks = unserialize($ally['ally_ranks']);
    // comprobamos el permiso
    if ($ally['ally_owner'] != $user['id'] && !$user_can_exit_alliance) {
      message($lang['Denied_access'], $lang['Members_list']);
    }
    /*
    Si bien, se tendria que confirmar, no tengo animos para hacerlo mas detallado...
    sorry :(
  */
    doquery("DELETE FROM {{table}} WHERE id='{$ally['id']}'", "alliance");
    header('Location: alliance.php');
    exit;
  }


// Передача альянса
    if ($mode == 'admin' && $edit == 'transfer') {

        if (isset($POST_newleader)) {
            doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`={$user['id']} ", 'users');
            doquery("UPDATE {{table}} SET `ally_owner`='" . mysql_escape_string(strip_tags($POST_newleader)) . "' WHERE `id`={$user['ally_id']} ", 'alliance');
            doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`='" . mysql_escape_string(strip_tags($POST_newleader)) . "' ", 'users');
            header('Location: alliance.php');
            exit;
        }
        // проверить лицензию
        if ($ally['ally_owner'] != $user['id']) {
            message($lang['Denied_access'], $lang['Members_list']);
        } else {
        // У нас есть массив из рядов
        $rangos_alianza = unserialize($ally['ally_ranks']);
        // Armamar опять список пользователей альянс
        $listuser = doquery("SELECT * FROM {{table}} WHERE `ally_id` ='{$user['ally_id']}'", 'users');
        // подсчет количества пользователей.
        $i = 0;
        // Как и в обычай. строка шаблона
        $template1 = gettemplate('alliance_admin_transfer_row');
        $page_list1 = '';
        // Мы делаем пользователю пользователь при проверке их ранга
        while ($u = mysql_fetch_array($listuser)) {
            $i=$i+1;
            $u['i'] = $i;
            if ($ally['ally_owner'] == $u['id']) {
            // Если он является ведущим союзником смысла, который autotransfiera Линия
            } elseif ($u['ally_rank_id'] == 0 ) {
            // Если вы новичок никогда не позволят правую руку
            } else {
                if ($rangos_alianza[$u['ally_rank_id']-1]['rechtehand'] == 1){
                // Вот, вооруженных вариант для пользователей, с разрешения его правой рукой
                $righthand['righthand'] .= "\n<option value=\"" . $u['id'] . "\"";
                $righthand['righthand'] .= ">";
                $righthand['righthand'] .= "".$u['username'];
                $righthand['righthand'] .= " [".$rangos_alianza[$u['ally_rank_id']-1]['name'];
                $righthand['righthand'] .= "]  </option>";
                }

            }
            // ПМА отправки данных, необходимых язык
            $righthand['transfer_to'] = $lang['transfer_to'];
            $righthand['transfer'] = $lang['transfer'];
            $righthand["dpath"] = $dpath;

        }
        // Наконец объединяет все в правильных шаблонов
        $page_list1 .= parsetemplate($template1, $righthand);
        $parse1 = $lang;
        $parse1['s'] = $s;
        $parse1['list'] = $page_list1;

        $page .= parsetemplate(gettemplate('alliance_admin_transfer'), $parse1);

        display($page, $lang['Members_list']);
    }
    }
    { // Конец передачи в режиме альянса

   // Default *falta revisar...*
    if ($ally['ally_owner'] != $user['id']) {
      $ally_ranks = unserialize($ally['ally_ranks']);
    }
    // Imagen de la alianza
    if ($ally['ally_ranks'] != '') {
      $ally['ally_ranks'] = "<tr><td colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>";
    }
    // temporalmente...
    if ($ally['ally_owner'] == $user['id']) {
      $range = ($ally['ally_owner_range'] != '')?$lang['Founder']:$ally['ally_owner_range'];
    } elseif ($user['ally_rank_id'] != 0 && isset($ally_ranks[$user['ally_rank_id']-1]['name'])) {
      $range = $ally_ranks[$user['ally_rank_id']-1]['name'];
    } else {
      $range = $lang['member'];
    }
    // Link de la lista de miembros
    if ($ally['ally_owner'] == $user['id'] || $user_can_watch_memberlist) {
      $lang['members_list'] = " (<a href=\"?mode=memberslist\">{$lang['Members_list']}</a>)";
    } else {
      $lang['members_list'] = '';
    }
    // El link de adminstrar la allianza
    if ($ally['ally_owner'] == $user['id'] || $user_admin) {
      $lang['alliance_admin'] = " (<a href=\"?mode=admin&edit=ally\">{$lang['ally_admin']}</a>)";
    } elseif ($user_can_kick && $ally['ally_owner'] != $user['id'] && !$user_admin ){
      $lang['alliance_admin'] = " (<a href=\"?mode=admin&edit=members\">{$lang['members_admin']}</a>)";
    }else{
      $lang['alliance_admin'] = '';
    }
    // El link de enviar correo circular
    if ($ally['ally_owner'] == $user['id'] || $user_can_send_mails) {
      $lang['send_circular_mail'] = "<tr><th>{$lang['Circular_message']}</th><th><a href=\"?mode=circular\">{$lang['Send_circular_mail']}</a></th></tr>";
    } else {
      $lang['send_circular_mail'] = '';
    }
    // El link para ver las solicitudes
    $lang['requests'] = '';
    $request = doquery("SELECT id FROM {{table}} WHERE `ally_request` ='{$ally['id']}'", 'users');
    $request_count = mysql_num_rows($request);
    if ($request_count != 0) {
      if ($ally['ally_owner'] == $user['id'] || $user_can_see_applications || $user_admin_applications)
        $lang['requests'] = "<tr><th>{$lang['Requests']}</th><th><a href=\"alliance.php?mode=admin&edit=requests\">{$request_count} {$lang['XRequests']}</a></th></tr>";
    }
    if ($ally['ally_owner'] != $user['id']) {
      $lang['ally_owner'] .= MessageForm($lang['Exit_of_this_alliance'], "", "?mode=exit", $lang['Continue']);
    } else {
      $lang['ally_owner'] .= '';
    }
    // La imagen de logotipo
    $lang['ally_image'] = ($ally['ally_image'] != '')?
    "<tr><th colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>":'';
    // $ally_image =
    $lang['range'] = $range;
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

    if ($game_config['OverviewClickBanner'] != '') {
      $parse['ClickBanner'] = stripslashes( $game_config['OverviewClickBanner'] );
    }
    display(parsetemplate(gettemplate('alliance_frontpage'), $lang), $lang['your_alliance']);
  }

}

?>
