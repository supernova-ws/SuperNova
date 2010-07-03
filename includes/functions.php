<?php

/**
 * functions.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------
//
// Routine pour la gestion du mode vacance
//

// include_once("/includes/functions/dump.php");

function check_urlaubmodus ($user) {
  if ($user['urlaubs_modus'] == 1) {
    message("Âû æå â îòïóñêå!", $title = $user['username'], $dest = "", $time = "3");
  }
}

function check_urlaubmodus_time () {
  global $user, $game_config;
  if ($game_config['urlaubs_modus_erz'] == 1) {
    $begrenzung = VOCATION_TIME; //24x60x60= 24h
    $iduser = $user["id"];
    $urlaub_modus_time = $user['urlaubs_modus_time'];
    $urlaub_modus_time_soll = $urlaub_modus_time + $begrenzung;
    $time_jetzt = time();
    if ($user['urlaubs_modus'] == 1 && $urlaub_modus_time_soll > $time_jetzt) {
      $soll_datum = date("d.m.Y", $urlaub_modus_time_soll);
      $soll_uhrzeit = date("H:i:s", $urlaub_modus_time_soll);
  //  message("");
    }
    elseif ($user['urlaubs_modus'] == 1 && $urlaub_modus_time_soll < $time_jetzt) {
      doquery("UPDATE {{table}} SET
        `urlaubs_modus` = '0',
        `urlaubs_modus_time` = '0'
        WHERE `id` = '$iduser' LIMIT 1", "users");
    }
  }
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Test de validité d'une adresse email
//
function is_email($email) {
  return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Affichage d'un message administrateur avec saut vers une autre page si souhaité
//
function AdminMessage ($mes, $title = 'Error', $dest = "", $time = "3") {
//  $parse['color'] = $color;
  $parse['title'] = $title;
  $parse['mes']   = $mes;

  $page = parsetemplate(gettemplate('admin/message_body'), $parse);

  display ($page, $title, false, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"$time;URL=javascript:self.location='$dest';\">" : ""), true);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Affichage d'un message avec saut vers une autre page si souhaité
//
function message ($mes, $title = 'Error', $dest = "", $time = "3", $show_header = true) {
  $parse['color'] = $color;
  $parse['title'] = $title;
  $parse['mes']   = $mes;

  $page .= parsetemplate(gettemplate('message_body'), $parse);

  display ($page, $title, $show_header, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"$time;URL=javascript:self.location='$dest';\">" : ""), false);
}

function ShowLeftMenu ( $Level = 0, $Template = 'left_menu') {
  global $lang, $dpath, $game_config, $user;

  includeLang('leftmenu');
  $Level = min ($Level, $user['authlevel']);

  $InfoTPL                  = gettemplate( 'serv_infos' );
  $parse                    = $lang;
  $parse['mf']              = '_self';
  $parse['dpath']           = $dpath;
  $parse['XNovaRelease']    = VERSION;
  $parse['servername']      = $game_config['game_name'];
  //$parse['servername']   = XNova;


  if ($Level <= "0") {
    $parse['lm_tx_serv']      = $game_config['resource_multiplier'];
    $parse['lm_tx_game']      = $game_config['game_speed'] / 2500;
    $parse['lm_tx_fleet']     = $game_config['fleet_speed'] / 2500;
    $parse['lm_tx_queue']     = MAX_FLEET_OR_DEFS_PER_ROW;
    $SubFrame                 = parsetemplate( $InfoTPL, $parse );
    $parse['server_info']     = $SubFrame;
    $parse['forum_url']       = $game_config['forum_url'];
    $parse['game_url']        = GAMEURL;
//    $gn                       = doquery("SELECT `config_value` FROM {{table}} WHERE config_name='game_name' LIMIT 1",'config',true);
//    $parse['game_name']       = $gn['config_value'];
    $parse['game_name']       = $config->game_name;
    $rank                     = doquery("SELECT `total_rank` FROM {{table}} WHERE `stat_code` = '1' AND `stat_type` = '1' AND `id_owner` = '". $user['id'] ."';",'statpoints',true);
    $parse['user_rank']       = $rank['total_rank'];

    if ($game_config['advGoogleLeftMenuIsOn'] == 1)
      $parse['GoogleCode'] = $game_config['advGoogleLeftMenuCode'];

    if ($user['authlevel'] > 0) {
      $parse['ADMIN_LINK']  = "
      <tr>
        <td colspan=\"2\"><div><a href=\"admin/overview.php\"><font color=\"lime\">".$lang['user_level'][$user['authlevel']]."</font></a></div></td>
      </tr>";
    };

    if (! HIDE_BUILDING_RECORDS ){
      $parse['BUILDING_RECORDS_LINK'] = "
      <tr>
          <td colspan='2'><div>
          <div align='left'><a href='records.php'>".$lang['Records']."</a></div>
          </div></td>
      </tr>";
    }
    else{
      $parse['BUILDING_RECORDS_LINK'] = "";
    }
  }
  elseif ($Level == "1") {
    $Template = 'admin/left_menu_modo';
  }
  elseif ($Level == "2") {
    $Template = 'admin/left_menu_op';
  }
  elseif ($Level >= "3") {
    $Template = 'admin/left_menu';
  };

  $Menu = parsetemplate( gettemplate($Template), $parse);

  return $Menu;
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine d'affichage d'une page dans un cadre donné
//
// $page      -> la page
// $title     -> le titre de la page
// $topnav    -> Affichage des ressources ? oui ou non ??
// $metatags  -> S'il y a quelques actions particulieres a faire ...
// $AdminPage -> Si on est dans la section admin ... faut le dire ...
function display ($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true) {
  global $link, $game_config, $debug, $user, $planetrow, $dpath, $IsUserChecked, $time_now;

  if (!$AdminPage) {
    $AdminPage = 0;
  } else {
    $AdminPage = $user['authlevel'];
  }
  $DisplayPage  = StdHeader ($title, $metatags, $AdminPage);


//  $DisplayPage .= '<table cellspacing=0 cellpadding=0 width=100% align=center style="none"><tr>';

  // $Menu = ShowLeftMenu ( $AdminPage );
  if ($isDisplayMenu && $IsUserChecked){ //
//    $DisplayPage .= "<div style=\"position: fixed; top: 0px; left: 0px; width: 190px; align: center;\">";
    $DisplayPage .= "<div style=\"float:left; width: 190px; text-align: center;\">";
//    $DisplayPage .= "<td width=190 valign=top>";
    $DisplayPage .= ShowLeftMenu ( $AdminPage );
//    $DisplayPage .= "</td>";
    $DisplayPage .= "</div>"; // float: left;
  }


//  $DisplayPage .= '<div id="page_body" style="position: absolute; top: 0px; left: 190px; width: auto;"><center>';
  $DisplayPage .= '<div id="page_body" style="margin-left: 190px; width: auto;"><center>';
//  $DisplayPage .= '<td align=center>';

  if ($topnav && $IsUserChecked) {
    if ($user['aktywnosc'] == 1) {
      $urlaub_act_time = $user['time_aktyw'];
      $act_datum = date("d.m.Y", $urlaub_act_time);
      $act_uhrzeit = date("H:i:s", $urlaub_act_time);
      //$DisplayPage .= "Le mode del dure jusque $act_datum $act_uhrzeit<br>  Ce n'est qu'après cette période que vous pouvez changer vos options.";
    }

    if ($user['db_deaktjava'] == 1) {
      $urlaub_del_time = $user['deltime'];
      $del_datum = date("d.m.Y", $urlaub_del_time);
      $del_uhrzeit = date("H:i:s", $urlaub_del_time);
      //$DisplayPage .= "Vous êtes en del user!<br>Le mode del dure jusque $del_datum $del_uhrzeit<br>  Ce n'est qu'après cette période que vous pouvez changer vos options.";
    }
    $DisplayPage .= ShowTopNavigationBar( $user, $planetrow );
  }
  echo $DisplayPage;

  if(is_object($page))
    displayP($page);
  else
    echo $page;

  $DisplayPage = '';

  // Affichage du Debug si necessaire
  if ($user['authlevel'] == 1 || $user['authlevel'] == 3) {
    if ($game_config['debug'] == 1) $debug->echo_log();
  }

  $DisplayPage .= '</center></div>';
  $DisplayPage .= StdFooter();

  echo $DisplayPage;

  sys_logHit();

  if (isset($link)) {
    mysql_close();
  }

  die();
}

// ----------------------------------------------------------------------------------------------------------------
//
// Entete de page
//
function StdHeader ($title = '', $metatags = '', $Level = 0) {
  global $user, $dpath, $langInfos;

  $dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];

  $parse           = $langInfos;
  $parse['dpath']  = $dpath;
  $parse['title']  = $title;
  $parse['-meta-'] = ($metatags) ? $metatags : "";
  $parse['-body-'] = "<body class=\"style\">"; //  class=\"style\" topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">";
  if ($Level>0){
    $parse['-path_prefix-'] = "../";
  };
  return parsetemplate(gettemplate('simple_header'), $parse);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Entete de page administration
//
function AdminUserHeader ($title = '', $metatags = '') {
  global $user, $dpath, $langInfos;

  $dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];

  $parse           = $langInfos;
  $parse['dpath']  = $dpath;
  $parse['title']  = $title;
  $parse['-meta-'] = ($metatags) ? $metatags : "";
  $parse['-body-'] = "<body><div style=\"height: 100%; overflow: auto;\">"; //  class=\"style\" topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">";
  return parsetemplate(gettemplate('admin/simple_header'), $parse);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Pied de page
//
function StdFooter() {
  global $game_config, $lang;
  $parse['copyright']     = $game_config['copyright'];
  $parse['TranslationBy'] = $lang['TranslationBy'];
  return parsetemplate(gettemplate('overall_footer'), $parse);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Calcul de la place disponible sur une planete
//
function CalculateMaxPlanetFields (&$planet) {
  global $resource;

  if($planet["planet_type"] != 3) {
  return $planet["field_max"] + ($planet[ $resource[33] ] * 5);
  }
  elseif($planet["planet_type"] == 3) {
  return $planet["field_max"] + ($planet[ $resource[41] ] * 3);
  }
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function GetSpyLevel(&$user) {
  global $resource;
  return $user[$resource[106]] + $user[$resource[610]];
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function GetMaxFleets(&$user) {
  global $resource;
  return 1 + $user[$resource[108]] + ($user[$resource[611]]*3);
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function GetMaxExpeditions(&$user) {
  global $resource;
  return floor(sqrt($user[$resource[124]]));
}

?>