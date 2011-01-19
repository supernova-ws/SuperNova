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

  if ($Level < 1) {
    $parse['lm_tx_serv']      = $config->resource_multiplier;
    $parse['lm_tx_game']      = get_game_speed();
    $parse['lm_tx_fleet']     = get_fleet_speed();
    $parse['lm_tx_queue']     = MAX_FLEET_OR_DEFS_PER_ROW;
    $SubFrame                 = parsetemplate( $InfoTPL, $parse );
    $parse['server_info']     = $SubFrame;
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
  elseif ($Level == 1) {
    $Template = 'admin/left_menu_modo';
  }
  elseif ($Level == 2) {
    $Template = 'admin/left_menu_op';
  }
  elseif ($Level >= 3) {
    $Template = 'admin/left_menu';
  };

  $parse['new_announce_count'] = $user['news_lastread'];
//!!!!!!!!!!!!!!
$parse['QUE_STRUCTURES'] = QUE_STRUCTURES;

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
  global $user, $dpath, $ugamela_root_path;

  $template = gettemplate('simple_header');

  $parse['dpath']  = $user["dpath"] ? $user["dpath"] : DEFAULT_SKINPATH;
  $parse['title']  = $title;
  $parse['-meta-'] = ($metatags) ? $metatags : "";
  $parse['-path_prefix-'] = $ugamela_root_path;

  return parsetemplate($template, $parse);
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
    'SN_VERSION'  => SN_VERSION,
  ));

  return parsetemplate($template);
}

/**
 * ShowTopNavigationBar.php
 *
 * @version 2.0 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 *   [+] Complies with PCG
 *   [+] Utilize PTE
 *   [+] Heavy optimization
 * @version 1.1 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function ShowTopNavigationBar ( $CurrentUser, $CurrentPlanet )
{
  global $_GET, $time_now, $dpath, $lang, $config;

  if ($CurrentUser)
  {
    $GET_mode = SYS_mysqlSmartEscape($_GET['mode']);

    $template       = gettemplate('topnav', true);

    if (!$CurrentPlanet)
    {
      $CurrentPlanet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$CurrentUser['current_planet']}';", '', true);
    }

    PlanetResourceUpdate ( $CurrentUser, $CurrentPlanet, $time_now );

    $ThisUsersPlanets    = SortUserPlanets ( $CurrentUser );
    while ($CurPlanet = mysql_fetch_array($ThisUsersPlanets)) {
      if (!$CurPlanet['destruyed'])
      {
        $template->assign_block_vars('topnav_planets', array(
          'ID'     => $CurPlanet['id'],
          'NAME'   => $CurPlanet['name'],
          'COORDS' => INT_makeCoordinates($CurPlanet),
          'SELECTED' => $CurPlanet['id'] == $CurrentUser['current_planet'] ? ' selected' : '',
        ));
      }
    }

    $day_of_week = $lang['weekdays'][date('w')];
    $day         = date('d');
    $month       = $lang['months'][date('m')];
    $year        = date('Y');
    $hour        = date('H');
    $min         = date('i');
    $sec         = date('s');

    // Ïîäñ÷åò êîë-âà îíëàéí è êòî îíëàéí
    $time = $time_now - 15*60;
    $OnlineUsersNames2 = doquery("SELECT `username` FROM {{users}} WHERE `onlinetime`>'{$time}'");

    $template->assign_vars(array(
      'dpath'      => $dpath,
      'TIME_NOW' => $time_now,
      'TIME_TEXT'          => "$day_of_week, $day $month $year {$lang['top_of_year']},",

      'USERS_ONLINE'         => mysql_num_rows($OnlineUsersNames2),
      'USERS_TOTAL'          => $config->users_amount,

      'TOPNAV_CURRENT_PLANET' => $CurrentUser['current_planet'],
      'TOPNAV_MODE' => $GET_mode,

      'TOPNAV_METAL' => round($CurrentPlanet["metal"], 2),
      'TOPNAV_METAL_MAX' => round($CurrentPlanet["metal_max"]),
      'TOPNAV_METAL_PERHOUR' => round($CurrentPlanet["metal_perhour"], 5),
      'TOPNAV_METAL_MAX_TEXT' => pretty_number($CurrentPlanet["metal_max"], 2, -$CurrentPlanet["metal"]),

      'TOPNAV_CRYSTAL' => round($CurrentPlanet["crystal"], 2),
      'TOPNAV_CRYSTAL_PERHOUR' => round($CurrentPlanet["crystal_perhour"], 5),
      'TOPNAV_CRYSTAL_MAX' => round($CurrentPlanet["crystal_max"]),
      'TOPNAV_CRYSTAL_MAX_TEXT' => pretty_number($CurrentPlanet["crystal_max"], 2, -$CurrentPlanet["crystal"]),

      'TOPNAV_DEUTERIUM' => round($CurrentPlanet["deuterium"], 2),
      'TOPNAV_DEUTERIUM_PERHOUR' => round($CurrentPlanet["deuterium_perhour"], 5),
      'TOPNAV_DEUTERIUM_MAX' => round($CurrentPlanet["deuterium_max"]),
      'TOPNAV_DEUTERIUM_MAX_TEXT' => pretty_number($CurrentPlanet["deuterium_max"], 2, -$CurrentPlanet["deuterium"]),

      'TOPNAV_DARK_MATTER' => pretty_number($CurrentUser['rpg_points']),

      'ENERGY_BALANCE' => pretty_number($CurrentPlanet['energy_max'] - $CurrentPlanet['energy_used'], true, 0),
      'ENERGY_MAX' => pretty_number($CurrentPlanet['energy_max']),

      'TOPNAV_MESSAGES'    => $CurrentUser['new_message'],
    ));

    $TopBar = parsetemplate( $template, $parse);
  } else {
    $TopBar = "";
  }

  return $TopBar;
}

function displayP($template)
{
  if(is_object($template))
  {
    global $ugamela_root_path, $user;

    if($template->parse)
    {
      foreach($template->parse as $key => $data)
      {
        $template->assign_var($key, $data);
      }
    }

    $dpath = $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH;
    $template->assign_vars(array(
      'dpath'         => $dpath,
      'SN_ROOT_PATH'  => $ugamela_root_path,
      '-path_prefix-' => $ugamela_root_path,
    ));

    $template->display('body');
  }
  else
  {
    print($template);
  }
}

function parsetemplate ($template, $array = false)
{
  global $lang;

  if(is_object($template))
  {
    global $ugamela_root_path, $user;

    if($array)
    {
      foreach($array as $key => $data)
      {
        $template->assign_var($key, $data);
      }
    }

    $template->assign_vars(array(
      'dpath'         => $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH,
      'SN_ROOT_PATH'  => $ugamela_root_path,
      '-path_prefix-' => $ugamela_root_path,
    ));
//    $template->parse = $array;

    return $template;
  }
  else
  {
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