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
function message ($mes, $title = 'Error', $dest = "", $time = "3", $show_header = true)
{
  $parse['title'] = $title;
  $parse['mes']   = $mes;

  $page .= parsetemplate(gettemplate('message_body'), $parse);

  display ($page, $title, $show_header, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"{$time};url={$dest}\">" : ""), false);
}

function ShowLeftMenu($Level = 0)
{
  lng_include('leftmenu');

  $template_name = $Level > 0 ? 'admin/left_menu' : 'left_menu';
  $template = gettemplate($template_name, true);

  $template->assign_vars(array(
    'USER_AUTHLEVEL'      => $GLOBALS['user']['authlevel'],
    'USER_AUTHLEVEL_NAME' => $GLOBALS['lang']['user_level'][$GLOBALS['user']['authlevel']],
  ));

  if ($Level < 1)
  {
    global $config;

    $template->assign_vars(array(
      'new_announce_count'  => $GLOBALS['user']['news_lastread'],
      'game_url'            => SN_ROOT_RELATIVE,
      'game_name'           => $config->game_name,
      'URL_RULES'           => $config->url_rules,
      'URL_FORUM'           => $config->url_forum,
      'URL_FAQ'             => $config->url_faq,
      'ADV_LEFT_BOTTOM'     => $config->advGoogleLeftMenuIsOn ? $config->advGoogleLeftMenuCode : '',
    ));
  }

  return $template;
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
  global $link, $debug, $user, $planetrow, $IsUserChecked, $time_now, $config, $lang;

  if(!$user || !isset($user['id']) || !is_numeric($user['id']))
  {
    $isDisplayMenu = false;
    $topnav = false;
  }

  // Global header
  $template = gettemplate('simple_header', true);
  $template->assign_vars(array(
    'title'         => ($title ? "{$title} - " : '') . "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}",
    '-meta-'        => $metatags,
  ));
  displayP(parsetemplate($template));

  // Left menu
  if ($isDisplayMenu)
  {
    $AdminPage = $AdminPage ? $user['authlevel'] : 0;
    displayP(parsetemplate(ShowLeftMenu ( $AdminPage )));
    echo '<div id="page_body">';
  }
  else
  {
    echo '<div>';
  }

  echo '<center>';
  // topnav
  if ($topnav)
  {
    displayP(parsetemplate(ShowTopNavigationBar($user, $planetrow)));
  }
  displayP($page);
  echo '</center></div>';

  // Global footer
  $template = gettemplate('simple_footer', true);
  $template->assign_vars(array(
    'ADMIN_EMAIL' => $config->game_adminEmail,
    'SERVER_TIME' => $time_now,
    'SN_VERSION'  => SN_VERSION,
  ));
  displayP(parsetemplate($template));

  sys_log_hit();

  // Affichage du Debug si necessaire
  if ($user['authlevel'] == 3 && $config->debug)
  {
    $debug->echo_log();
  }

  if (isset($link))
  {
    mysql_close();
  }

  die();
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

function ShowTopNavigationBar ( $user, $planetrow )
{
  if (!is_array($user))
  {
    return '';
  }

  global $time_now, $lang, $config;

  $GET_mode = sys_get_param_str('mode');

  $template       = gettemplate('topnav', true);

  $planetrow = $planetrow ? $planetrow : $user['current_planet'];

  $planetrow = sys_o_get_updated($user, $planetrow, $time_now, true);
  $planetrow = $planetrow['planet'];

  $ThisUsersPlanets = SortUserPlanets ( $user );
  while ($CurPlanet = mysql_fetch_assoc($ThisUsersPlanets))
  {
    if (!$CurPlanet['destruyed'])
    {
      $template->assign_block_vars('topnav_planets', array(
        'ID'     => $CurPlanet['id'],
        'NAME'   => $CurPlanet['name'],
        'COORDS' => uni_render_coordinates($CurPlanet),
        'SELECTED' => $CurPlanet['id'] == $user['current_planet'] ? ' selected' : '',
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
  $online_count = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE `onlinetime`>'{$time}';", '', true);

  $template->assign_vars(array(
    'TIME_NOW'   => $time_now,
    'DATE_TEXT'          => "$day_of_week, $day $month $year {$lang['top_of_year']},",
    'TIME_TEXT'          => "{$hour}:{$min}:{$sec}",

    'USERS_ONLINE'         => $online_count['users_online'],
    'USERS_TOTAL'          => $config->users_amount,

    'TOPNAV_CURRENT_PLANET' => $user['current_planet'],
    'TOPNAV_MODE' => $GET_mode,

    'TOPNAV_METAL' => round($planetrow["metal"], 2),
    'TOPNAV_METAL_MAX' => round($planetrow["metal_max"]),
    'TOPNAV_METAL_PERHOUR' => round($planetrow["metal_perhour"], 5),
    'TOPNAV_METAL_TEXT' => pretty_number($planetrow["metal"], 2),
    'TOPNAV_METAL_MAX_TEXT' => pretty_number($planetrow["metal_max"], 2, -$planetrow["metal"]),

    'TOPNAV_CRYSTAL' => round($planetrow["crystal"], 2),
    'TOPNAV_CRYSTAL_MAX' => round($planetrow["crystal_max"]),
    'TOPNAV_CRYSTAL_PERHOUR' => round($planetrow["crystal_perhour"], 5),
    'TOPNAV_CRYSTAL_TEXT' => pretty_number($planetrow["crystal"], 2),
    'TOPNAV_CRYSTAL_MAX_TEXT' => pretty_number($planetrow["crystal_max"], 2, -$planetrow["crystal"]),

    'TOPNAV_DEUTERIUM' => round($planetrow["deuterium"], 2),
    'TOPNAV_DEUTERIUM_MAX' => round($planetrow["deuterium_max"]),
    'TOPNAV_DEUTERIUM_PERHOUR' => round($planetrow["deuterium_perhour"], 5),
    'TOPNAV_DEUTERIUM_TEXT' => pretty_number($planetrow["deuterium"], 2),
    'TOPNAV_DEUTERIUM_MAX_TEXT' => pretty_number($planetrow["deuterium_max"], 2, -$planetrow["deuterium"]),

    'TOPNAV_DARK_MATTER' => pretty_number($user['rpg_points']),

    'ENERGY_BALANCE' => pretty_number($planetrow['energy_max'] - $planetrow['energy_used'], true, 0),
    'ENERGY_MAX' => pretty_number($planetrow['energy_max']),

    'TOPNAV_MESSAGES_PLAYER'   => $user['mnl_joueur'],
    'TOPNAV_MESSAGES_ALLIANCE' => $user['mnl_alliance'],
    'TOPNAV_MESSAGES_ALL'      => $user['new_message'],

    'TOPNAV_FLEETS_FLYING' => flt_get_fleets_flying($user),
    'TOPNAV_FLEETS_TOTAL' => GetMaxFleets($user),
    'TOPNAV_EXPEDITIONS_FLYING' => flt_get_expeditions_flying($user),
    'TOPNAV_EXPEDITIONS_TOTAL' => GetMaxExpeditions($user),
  ));
    
  return $template;
}

function displayP($template)
{
  if(is_object($template))
  {
/*
    global $user; // $ugamela_root_path,

    if($template->parse)
    {
      foreach($template->parse as $key => $data)
      {
        $template->assign_var($key, $data);
      }
    }

    $template->assign_vars(array(
      'dpath'         => $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH,
      'SN_ROOT_PATH'  => SN_ROOT_VIRTUAL, //$ugamela_root_path,
      '-path_prefix-' => SN_ROOT_VIRTUAL, //$ugamela_root_path,
    ));
*/
    $template->display('body');
  }
  else
  {
    print($template);
  }
}

function parsetemplate($template, $array = false)
{
  if(is_object($template))
  {
    global $user; // $ugamela_root_path,

    if($array)
    {
      foreach($array as $key => $data)
      {
        $template->assign_var($key, $data);
      }
    }

    $template->assign_vars(array(
      'dpath'         => $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH,
      'SN_ROOT_PATH'  => SN_ROOT_VIRTUAL, //$ugamela_root_path,
      '-path_prefix-' => SN_ROOT_VIRTUAL, //$ugamela_root_path,
    ));

    return $template;
  }
  else
  {
  /*
    global $lang;

    if(!$array)
    {
      $array = array();
    }
 */
    $search[] = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\'][\'\2\']) ) ? $lang[\'\1\'][\'\2\'] : \'\' );';

    $search[] = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\']) ) ? $lang[\'\1\'] : \'\' );';

    $search[] = '#\{([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );';

    return preg_replace($search, $replace, $template);
  }
}

function gettemplate($templatename, $is_phpbb = false)
{
  $templatename .= '.tpl';

  if($is_phpbb)
  {
    $template = new template();
    $template->set_custom_template(TEMPLATE_DIR, TEMPLATE_NAME);

    $template->set_filenames(array(
        'body' => $templatename
    ));

    return $template;
  }
  else
  {
    return ReadFromFile(TEMPLATE_DIR . '/' . $templatename);
  }
}

?>
