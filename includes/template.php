<?php
/**
 * functions.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
*/

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Affichage d'un message administrateur avec saut vers une autre page si souhaité
//
function AdminMessage ($mes, $title = 'Error', $dest = "", $time = "3") {
//  $parse['color'] = $color;
  $parse['title'] = $title;
  $parse['mes']   = $mes;

  $page = parsetemplate(gettemplate('admin/message_body'), $parse);

  display ($page, $title, false, ($dest ? "<meta http-equiv=\"refresh\" content=\"{$time};URL=javascript:self.location='{$dest}';\">" : ''), true);
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

  display ($page, $title, $show_header, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"{$time};url={$dest}\">" : ""), false);
}

function ShowLeftMenu ( $Level = 0, $Template = 'left_menu') {
  global $lang, $dpath, $user, $config, $time_now;

  includeLang('leftmenu');
  $Level = min ($Level, $user['authlevel']);

  $InfoTPL                  = gettemplate( 'serv_infos' );
  $parse                    = $lang;
  $parse['mf']              = '_self';
  $parse['dpath']           = $dpath;
  $parse['XNovaRelease']    = VERSION;
  $parse['servername']      = $config->game_name;

  if ($Level <= "0") {
    $parse['lm_tx_serv']      = $config->resource_multiplier;
    $parse['lm_tx_game']      = get_game_speed();
    $parse['lm_tx_fleet']     = get_fleet_speed();
    $parse['lm_tx_queue']     = MAX_FLEET_OR_DEFS_PER_ROW;
    $SubFrame                 = parsetemplate( $InfoTPL, $parse );
    $parse['server_info']     = $SubFrame;
    $parse['forum_url']       = $config->forum_url;
    $parse['C_rules_url']     = $config->rules_url;
    $parse['game_url']        = GAMEURL;
    $parse['game_name']       = $config->game_name;
    $rank                     = doquery("SELECT `total_rank` FROM {{table}} WHERE `stat_code` = '1' AND `stat_type` = '1' AND `id_owner` = '". $user['id'] ."';",'statpoints',true);
    $parse['user_rank']       = $rank['total_rank'];

    if ($config->advGoogleLeftMenuIsOn)
    {
      $parse['ADV_LEFT_BOTTOM'] = $config->advGoogleLeftMenuCode;
    }

    if ($user['authlevel'] > 0) {
      $parse['ADMIN_LINK']  = "
      <tr>
        <th><div><a href=\"admin/overview.php\"><font color=\"lime\">".$lang['user_level'][$user['authlevel']]."</font></a></div></th>
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

//  $time_new = $time_now - $config->game_news_actual;
//  $lastAnnounces = doquery("SELECT COUNT(*) AS `new_announce_count` FROM {{announce}} WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<='{$time_now}' AND UNIX_TIMESTAMP(`tsTimeStamp`)>='{$time_new}' ORDER BY `tsTimeStamp` DESC LIMIT 1;", '', true);
  $parse['new_announce_count'] = $user['news_lastread'];

  $Menu = parsetemplate( gettemplate($Template, true), $parse);

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
function display ($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true)
{
  global $link, $debug, $user, $planetrow, $dpath, $IsUserChecked, $time_now, $config, $lang;

  if(!$user)
  {
    $isDisplayMenu = false;
    $topnav = false;
  }

  $AdminPage = $AdminPage ? $user['authlevel'] : 0;

  $title = $title ? "{$title} - " : $title;
  $title .= "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}";

  displayP(StdHeader ($title, $metatags, $AdminPage));
  if ($isDisplayMenu && $IsUserChecked){ //
    displayP(ShowLeftMenu ( $AdminPage ));
    echo '<div id="page_body">';
  }
  else
  {
    echo '<div>';
  }

  echo '<center>';

  if ($topnav && $IsUserChecked) {
    if ($user['db_deaktjava'] == 1) {
      $urlaub_del_time = $user['deltime'];
      $del_datum = date(FMT_DATE, $urlaub_del_time);
      $del_uhrzeit = date(FMT_TIME, $urlaub_del_time);
    }
    $TopNav = ShowTopNavigationBar( $user, $planetrow );
  }

  displayP($TopNav);
  displayP($page);

  // Affichage du Debug si necessaire
  if ($user['authlevel'] == 3 && $config->debug)
  {
    $debug->echo_log();
  }
  echo '</center></div>';

  $std_footer = StdFooter();
  displayP($std_footer);

  sys_log_hit();

  if (isset($link))
  {
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
//  $parse['-body-'] = ''; //  class=\"style\" topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">";
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
  global $time_now, $config;

  $template = gettemplate('overall_footer', true);

  $template->assign_vars(array(
    'ADMIN_EMAIL' => $config->game_adminEmail,
    'SERVER_TIME' => $time_now,
  ));

  return parsetemplate($template);
}

function displayP($template)
{
  if(is_object($template))
  {
    global $lang, $user;

    if($template->parse)
    {
      foreach($template->parse as $key => $data)
      {
        $template->assign_var($key, $data);
      }
    }

    $template->assign_var('dpath', (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"]);

    $template->display('body');
  }
  else
  {
    print($template);
  }
}

function parsetemplate ($template, $array = false)
{
  if(is_object($template))
  {
    $template->parse = $array;

    return $template;
  }
  else
  {
    global $lang;

    if(!$array)
    {
      $array = array();
    }

    $search[] = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\'][\'\2\']) ) ? $lang[\'\1\'][\'\2\'] : \'\' );';
    $search[] = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\']) ) ? $lang[\'\1\'] : \'\' );';
    $search[] = '#\{([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );';

    return preg_replace($search, $replace, $template);
  }
}

function gettemplate ($templatename, $is_phpbb = false)
{
  global $ugamela_root_path;

  $filename = $ugamela_root_path . TEMPLATE_DIR . TEMPLATE_NAME . '/' . $templatename . ".tpl";

  if($is_phpbb)
  {
    $template = new template();
    $template->set_custom_template($ugamela_root_path . TEMPLATE_DIR . '/' . TEMPLATE_NAME, TEMPLATE_NAME);

    $template->set_filenames(array(
        'body' => $templatename . ".tpl"
    ));

    return $template;
  }
  else
  {
    return ReadFromFile($filename);
  }
}

?>