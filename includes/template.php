<?php
// Wrappers for functions
function display($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true)
{
  $func_args = func_get_args();
  return sn_function_call('display', $func_args);
}

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

function tpl_render_menu()
{
  global $config, $user, $user_impersonator, $lang, $time_now;

  lng_include('menu');

  $sn_menu = array(
/*
    'triolan_menu' => array(                  // This should be used as ID for both internal submenu insert AND as "id" attribute of Tx HTML-tag (see below)
      'LEVEL' => 'submenu',                   // Which Tx HTML tag to use. 'header' - would be used TH; 'submenu' - TD
      'TYPE'  => 'image',                     // Menu item type: 'image' (wrapped by IMG tag), 'text' (puts "as-is") or 'lang' for late biding with $lang[ITEM] values. Default is 'text'
      'CLASS' => 'c_c',                       // Class for TD/TH element. Can be c_c, c_l, c_r or any other custom. 'c_c' default for 'header', 'c_l' default for 'text'
      'TITLE' => 'Triolan.COM',               // TITLE tag for Tx HTML-element

      'ITEM'  => 'images/triolan.gif',        // Item: text or relative image URL
      'LINK'  => 'http://www.triolan.com/',   // URL
      'BLANK' => true,                        // Should link open in new window/tab?
      'SPAN'  => 'lm_overview',               // Class for internal SPAN - to override <A> style
      'ALT'   => 'Triolan.COM',               // ALT-tag for image
    ),
*/

    'server_name' => array(
      'LEVEL' => 'header',
      'ITEM' => "{$config->game_name}<br />{$lang['sys_from']} {$config->server_start_date}",
    ),
    'server_logo' => array(
      'LEVEL' => 'submenu',
      'TYPE' => 'image',
      'CLASS' => 'c_c',
      'ITEM' => 'design/images/supernova.png',
      'LINK' => '.',
      'ALT' => 'supernova.ws',
    ),

    'admin' => $user['authlevel'] <= 0 ? array() : 
    array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['user_level'][$user['authlevel']],
      'LINK'  => 'admin/overview.php',
      'SPAN'  => 'ok',
    ),
    'impersonator' => !is_array($user_impersonator) ? array() : 
    array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['sys_impersonate_done'],
      'LINK'  => 'logout.php',
      'SPAN'  => 'important',
    ),

    'rules' => !$config->url_rules ? array() : 
    array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['sys_game_rules'],
      'LINK'  => $config->url_rules,
      'SPAN'  => 'warning',
    ),
    'faq' => !$config->url_faq ? array() : 
    array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['m_faq'],
      'LINK'  => $config->url_faq,
      'SPAN'  => 'notice',
    ),

    'news' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['news_title'] . ($user['news_lastread'] < $config->var_news_last ? "&nbsp;<span class=\"important\">{$lang['lm_announce_fresh']}</span>" : ''),
      'LINK'  => 'announce.php',
      'SPAN'  => 'ok',
    ),

    'affiliates' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['m_affilates'],
      'LINK'  => 'affilates.php',
      'SPAN'  => 'affiliates',
    ),

    'planet_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['sys_planet'],
    ),
    'planet_overview' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Overview'],
      'LINK'  => 'overview.php',
      'SPAN'  => 'lm_overview',
    ),
    'planet_resources' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Resources'],
      'LINK'  => 'resources.php',
    ),
    'planet_fleet' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['lm_fleet_orbiting'],
      'LINK'  => 'fleet.php',
      'SPAN'  => 'lm_fleet',
    ),
    'planet_structures' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Buildings'],
      'LINK'  => 'buildings.php?mode=' . QUE_STRUCTURES,
      'SPAN'  => 'lm_buildings',
    ),
    'planet_shipyard' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Shipyard'],
      'LINK'  => 'buildings.php?mode=' . SUBQUE_FLEET,
      'SPAN'  => 'lm_shipyard',
    ),
    'planet_defense' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Defense'],
      'LINK'  => 'buildings.php?mode=' . SUBQUE_DEFENSE,
      'SPAN'  => 'lm_shipyard',
    ),

    'empire_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['sys_empire'],
    ),
    'empire_overview' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['imp_overview'],
      'LINK'  => 'imperium.php',
      'SPAN'  => 'lm_overview',
    ),
    'empire_emperor' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['imp_imperator'],
      'LINK'  => 'imperator.php',
    ),
    'empire_fleets' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['imp_fleets'],
      'LINK'  => 'flying_fleets.php',
      'SPAN'  => 'lm_fleet',
    ),
    'empire_research' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Research'],
      'LINK'  => 'buildings.php?mode=research',
      'SPAN'  => 'lm_techtree',
    ),
    'empire_techtree' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Technology'],
      'LINK'  => 'techtree.php',
      'SPAN'  => 'lm_techtree',
    ),
    'empire_mercenaries' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['tech'][UNIT_MERCENARIES],
      'LINK'  => 'officer.php?mode=' . UNIT_MERCENARIES,
      'SPAN'  => 'lm_overview',
    ),
    'empire_schematics' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['tech'][UNIT_PLANS],
      'LINK'  => 'officer.php?mode=' . UNIT_PLANS,
    ),
    'empire_artifacts' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['tech'][UNIT_ARTIFACTS],
      'LINK'  => 'artifacts.php',
    ),
    'empire_market' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['rinok'],
      'LINK'  => 'market.php',
    ),
    'empire_universe' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['sys_universe'],
      'LINK'  => 'galaxy.php?mode=0',
      'SPAN'  => 'lm_universe',
    ),
    'empire_quests' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['qst_quests'],
      'LINK'  => 'quest.php',
    ),

    'ally_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['sys_alliance'],
    ),
    'ally_overview' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['sys_alliance'],
      'LINK'  => 'alliance.php',
    ),
    'ally_chat' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['AllyChat'],
      'LINK'  => 'chat.php?ally=ally',
      'SPAN'  => 'lm_chatally',
    ),

    'comm_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['m_communication'],
    ),

    'comm_messages' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Messages'],
      'LINK'  => 'messages.php',
    ),
    'comm_chat' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Chat'],
      'LINK'  => 'chat.php',
      'SPAN'  => 'lm_chat',
    ),
    'comm_forum' => !$config->url_forum ? array() :
    array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['m_forum'],
      'LINK'  => $config->url_forum,
      'SPAN'  => 'lm_forum',
    ),

    'utils_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['m_others'],
    ),
    'utils_simulator' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['m_simulator'],
      'LINK'  => 'simulator.php',
      'SPAN'  => 'lm_simulator',
    ),
    'utils_reports' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['lm_combat_reports'],
      'LINK'  => 'viewreport.php',
    ),
    'utils_buddies' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Buddylist'],
      'LINK'  => 'buddy.php',
    ),
    'utils_notes' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Notes'],
      'LINK'  => 'notes.php',
    ),
    'utils_shortcuts' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['lm_shortcuts'],
      'LINK'  => 'fleet_shortcuts.php',
    ),
    'utils_search' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Search'],
      'LINK'  => 'search.php',
      'SPAN'  => 'lm_search',
    ),

    'info_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['navig'],
    ),
    'info_stats' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Statistics'],
      'LINK'  => 'stat.php',
    ),
    'info_records' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['Records'],
      'LINK'  => 'records.php',
    ),
    'info_server' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['lm_server_info'],
      'LINK'  => 'server_info.php',
    ),
    'info_ban' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['lm_banned'],
      'LINK'  => 'banned.php',
    ),
    'info_admins' => array(
      'LEVEL' => 'submenu',
      'ITEM'  => $lang['commun'],
      'LINK'  => 'contact.php',
    ),

    'options_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['Options'],
      'LINK'  => 'options.php',
      'SPAN'  => 'lm_options',
    ),

    'logout_menu' => array(
      'LEVEL' => 'header',
      'ITEM'  => $lang['Logout'],
      'LINK'  => 'logout.php',
      'SPAN'  => 'lm_logout',
    ),

    'extra_menu' => !$config->advGoogleLeftMenuIsOn ? array() :
    array(
      'LEVEL' => 'submenu',
      'CLASS' => 'c_c',
      'ITEM'  => $config->advGoogleLeftMenuCode,
    ),
    'supernova_logo' => array(
      'LEVEL' => 'submenu',
      'TYPE' => 'image',
      'CLASS' => 'c_c',
      'ITEM' => 'design/images/supernova.png',
      'LINK' => 'http://supernova.ws',
      'ALT' => 'Powered by \'Project "SuperNova.WS"\' engine',
    ),
    'triolan_menu' => array(
      'LEVEL' => 'submenu',
      'TYPE'  => 'image',
      'CLASS' => 'c_c',
      'ITEM'  => 'images/triolan.gif',
      'LINK'  => 'http://www.triolan.com/',
      'BLANK' => true,
      'ALT'   => 'Triolan.COM',
    ),

  );

  $template_name = IN_ADMIN === true ? 'admin/menu' : 'menu';
  $template = gettemplate($template_name, true);

  $template->assign_vars(array(
    'SERVER_TIME'         => $time_now,
    'USER_AUTHLEVEL'      => $user['authlevel'],
    'USER_AUTHLEVEL_NAME' => $lang['user_level'][$user['authlevel']],
    'USER_IMPERSONATOR'   => is_array($user_impersonator),
  ));

  if(IN_ADMIN === true && $user['authlevel'] > 0)
  {
    global $sn_version_check_class;

    $template->assign_vars(array(
      'CHECK_DATE' => $config->server_updater_check_last ? date(FMT_DATE, $config->server_updater_check_last) : 0,
      'CHECK_RESULT' => $lang['adm_opt_ver_response_short'][$config->server_updater_check_result],
      'CHECK_CLASS' => $sn_version_check_class[$config->server_updater_check_result],
    ));
  }
  else
  {
    global $sn_menu_extra;

    foreach($sn_menu_extra as $menu_item_id => $menu_item)
    {
      $item_location = $menu_item['LOCATION'];
      unset($menu_item['LOCATION']);

      if(!$item_location)
      {
        $sn_menu[$menu_item_id] = $menu_item;
        continue;
      }

      $is_positioned = $item_location[0];
      if($is_positioned == '+' || $is_positioned == '-')
      {
        $item_location = substr($item_location, 1);
      }
      else
      {
        $is_positioned = '';
      }

      if($item_location)
      {
        $menu_keys = array_keys($sn_menu);
        $insert_position = array_search($item_location, $menu_keys);
        if($insert_position === false)
        {
          $insert_position = count($sn_menu)-1;
          $is_positioned = '+';
          $item_location = '';
        }
      }
      else
      {
        $insert_position = $is_positioned == '-' ? 0 : count($sn_menu);
      }

      $insert_position += $is_positioned == '+' ? 1 : 0;
      $spliced = array_splice($sn_menu, $insert_position, count($sn_menu) - $insert_position);
      $sn_menu[$menu_item_id] = $menu_item;
      if(!$is_positioned && $item_location)
      {
        unset($spliced[$item_location]);
      }
      $sn_menu = array_merge($sn_menu, $spliced);
    }

    foreach($sn_menu as $menu_item_id => $menu_item)
    {
      if(!$menu_item)
      {
        continue;
      }

      if(is_string($menu_item_id))
      {
        $menu_item['ID'] = $menu_item_id;
      }

      if($menu_item['TYPE'] == 'lang')
      {
        $menu_item['ITEM'] = $lang[$menu_item['ITEM']];
      }

      $menu_item['ALT'] = htmlentities($menu_item['ALT']);
      $menu_item['TITLE'] = htmlentities($menu_item['TITLE']);

      $template->assign_block_vars('menu', $menu_item);
    }
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
function sn_display($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true)
{
  global $link, $debug, $user, $user_impersonator, $planetrow, $IsUserChecked, $time_now, $config, $lang;

  if(!$user || !isset($user['id']) || !is_numeric($user['id']))
  {
    $isDisplayMenu = false;
    $topnav = false;
  }

  // Global header
  $template = gettemplate('simple_header', true);
  $template->assign_vars(array(
    'title'          => ($title ? "{$title} - " : '') . "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}",
    '-meta-'         => $metatags,
    'LANG_LANGUAGE'  => $lang['LANG_INFO']['LANG_NAME_ISO2'],
    'LANG_ENCODING'  => 'utf-8',
    'LANG_DIRECTION' => $lang['LANG_INFO']['LANG_DIRECTION'],
    'IMPERSONATING'  => $user_impersonator ? sprintf($lang['sys_impersonated_as'], $user['username'], $user_impersonator['username']) : '',
  ));
  displayP(parsetemplate($template));

  if($isDisplayMenu)
  {
    $AdminPage = $AdminPage ? $user['authlevel'] : 0;
    displayP(parsetemplate(tpl_render_menu($AdminPage)));
  }

  if($topnav)
  {
    displayP(parsetemplate(tpl_render_topnav($user, $planetrow)));
  }

  echo '<div id="page_body"><center>';
  if(!is_array($page))
  {
    $page = array($page);
  }
  foreach($page as $page_item)
  {
    displayP($page_item);
  }
  echo '</div></center>';

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
  if ($user['authlevel'] >= 3 && $config->debug)
  {
    $debug->echo_log();
  }

  if (isset($link))
  {
    mysql_close();
  }

  die();
}

function tpl_topnav_event_build_helper($time, $event, $msg, $prefix, $is_decrease, $fleet_flying_row, &$fleet_flying_sorter, &$fleet_flying_events, &$fleet_event_count)
{
  global $lang;

  $fleet_flying_sorter[$fleet_event_count] = $time;
  $fleet_flying_events[$fleet_event_count] = array(
    'ROW'  => $fleet_flying_row,
    'FLEET_ID' => $fleet_flying_row['fleet_id'],
    'EVENT' => $event,
    'COORDINATES' => uni_render_coordinates($fleet_flying_row, $prefix),
    'TEXT' => "{$msg}",
    'DECREASE' => $is_decrease
  );
  $fleet_event_count++;
}

function tpl_topnav_event_build(&$template, $fleet_flying_list, $type = 'fleet')
{
  if(empty($fleet_flying_list))
  {
    return;
  }

  global $lang, $user, $time_now;

  $fleet_event_count = 0;
  $fleet_flying_sorter = array();
  $fleet_flying_events = array();
  foreach($fleet_flying_list as &$fleet_flying_row)
  {
    $will_return = true;
    if($fleet_flying_row['fleet_mess'] == 0)
    {
      if($fleet_flying_row['fleet_start_time'] >= $time_now) // cut fleets on Hold and Expedition
      {
        if($fleet_flying_row['fleet_mission'] == MT_RELOCATE)
        {
          $will_return = false;
        }
        tpl_topnav_event_build_helper($fleet_flying_row['fleet_start_time'], EVENT_FLEET_ARRIVE, $lang['sys_event_arrive'], 'fleet_end_', !$will_return, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
      }
      if($fleet_flying_row['fleet_end_stay'])
      {
        tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_stay'], EVENT_FLEET_STAY, $lang['sys_event_stay'], 'fleet_end_', false, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
      }
    }
    if($will_return)
    {
      tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_time'], EVENT_FLEET_RETURN, $lang['sys_event_return'], 'fleet_start_', true, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
    }
  }
  asort($fleet_flying_sorter);
  $fleet_flying_count = count($fleet_flying_list);
  foreach($fleet_flying_sorter as $fleet_event_id => $fleet_time)
  {
    $fleet_event = &$fleet_flying_events[$fleet_event_id];
    $template->assign_block_vars("flying_{$type}s", array(
      'TIME' => max(0, $fleet_time - $time_now),
      'TEXT' => $fleet_flying_count,
      'HINT' => date(FMT_DATE_TIME, $fleet_time) . " - {$lang['sys_fleet']} {$fleet_event['TEXT']} {$fleet_event['COORDINATES']} {$lang['type_mission'][$fleet_event['ROW']['fleet_mission']]}",
    ));
    if($fleet_event['DECREASE'])
    {
      $fleet_flying_count--;
    }
  }
}

function tpl_render_topnav(&$user, $planetrow){return sn_function_call('tpl_render_topnav', array(&$user, $planetrow));}
function sn_tpl_render_topnav(&$user, $planetrow)
{
  if (!is_array($user))
  {
    return '';
  }

  global $time_now, $lang, $config, $sn_data;

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

  $fleet_flying_list = tpl_get_fleets_flying($user);
  tpl_topnav_event_build($template, $fleet_flying_list[0]);
  tpl_topnav_event_build($template, $fleet_flying_list[MT_EXPLORE], 'expedition');

  $time = $time_now - 15*60;
  $online_count = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE `onlinetime`>'{$time}' AND `user_as_ally` IS NULL;", '', true);

  $que_length = 0;
  if($user['que'])
  {
    $que_item = $user['que'] ? explode(',', $user['que']) : array();
    $unit_id = $que_item[QI_UNIT_ID];
    $unit_data = eco_get_build_data($user, $planet, $unit_id, $user[$sn_data[$unit_id]['name']]);

    $template->assign_block_vars('que', array(
      'ID' => $unit_id,
      'QUE' => QUE_RESEARCH,
      'NAME' => $lang['tech'][$unit_id],
      'TIME' => $que_item[QI_TIME],
      'TIME_FULL' => $unit_data[RES_TIME][BUILD_CREATE],
      'AMOUNT' => 1,
      'LEVEL' => $user[$sn_data[$unit_id]['name']] + 1,
    ));

    $que_length++;
  }

  $template->assign_vars(array(
    'QUE_ID'             => QUE_RESEARCH,
    'QUE_HTML'           => 'topnav',

    'RESEARCH_ONGOING'   => (boolean)$user['que'],

    'TIME_NOW'   => $time_now,
    'DATE_TEXT'          => "$day_of_week, $day $month $year {$lang['top_of_year']},",
    'TIME_TEXT'          => "{$hour}:{$min}:{$sec}",

    'USERS_ONLINE'         => $online_count['users_online'],
    'USERS_TOTAL'          => $config->users_amount,

    'TOPNAV_CURRENT_PLANET' => $user['current_planet'],
    'TOPNAV_MODE' => $GET_mode,

    'TOPNAV_DARK_MATTER' => pretty_number($user[$sn_data[RES_DARK_MATTER]['name']]),

    'TOPNAV_MESSAGES_ADMIN'    => $user['msg_admin'],
    'TOPNAV_MESSAGES_PLAYER'   => $user['mnl_joueur'],
    'TOPNAV_MESSAGES_ALLIANCE' => $user['mnl_alliance'],
    'TOPNAV_MESSAGES_ALL'      => $user['new_message'],

    'TOPNAV_FLEETS_FLYING'      => count($fleet_flying_list[0]),
    'TOPNAV_FLEETS_TOTAL'       => GetMaxFleets($user),
    'TOPNAV_EXPEDITIONS_FLYING' => count($fleet_flying_list[MT_EXPLORE]),
    'TOPNAV_EXPEDITIONS_TOTAL'  => GetMaxExpeditions($user),

    'TOPNAV_QUEST_COMPLETE'     => get_quest_amount_complete($user['id']),
  ));

  if((defined('SN_RENDER_NAVBAR_PLANET') && SN_RENDER_NAVBAR_PLANET === true) || ($user['option_list'][OPT_INTERFACE]['opt_int_navbar_resource_force'] && SN_RENDER_NAVBAR_PLANET !== false))
  {
    tpl_set_resource_info($template, $planetrow);
    $template->assign_vars(array(
      'SN_RENDER_NAVBAR_PLANET' => true,
      'SN_NAVBAR_HIDE_FLEETS' => true,
    ));
  }

  return $template;
}

function displayP($template)
{
  if(is_object($template))
  {
    if(!$template->parsed)
    {
      parsetemplate($template);
    }

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
    global $time_now, $user; // $ugamela_root_path,

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
      'TIME_NOW'      => $time_now,
    ));

    $template->parsed = true;

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

function gettemplate($templatename, $is_phpbb = false, $template_path = false)
{
  $templatename .= '.tpl.html';

  if($is_phpbb)
  {
    $template = new template();
    $template->set_custom_template($template_path ? $template_path : TEMPLATE_DIR, TEMPLATE_NAME, TEMPLATE_DIR);

    $template->set_filenames(array(
        'body' => $templatename
    ));

    return $template;
  }
  else
  {
    return sys_file_read(TEMPLATE_DIR . '/' . $templatename);
  }
}

function tpl_login_lang(&$template, $id_ref)
{
  global $user, $language;

  $template->assign_vars(array(
    'LANG'         => "?lang={$language}",
    'referral'     => $id_ref ? "&id_ref={$id_ref}" : '',
    'FILENAME'     => basename($_SERVER['PHP_SELF']),
  ));

  foreach(lng_get_list() as $lng_id => $lng_data)
  {
    $template->assign_block_vars('language', $lng_data);
  }
}

function tpl_get_fleets_flying(&$user)
{
  $fleet_flying_list = array();
  $fleet_flying_query = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner = {$user['id']}");
  while($fleet_flying_row = mysql_fetch_assoc($fleet_flying_query))
  {
    $fleet_flying_list[0][] = $fleet_flying_row;
    $fleet_flying_list[$fleet_flying_row['fleet_mission']][] = &$fleet_flying_list[0][count($fleet_flying_list)-1];
  }
  return $fleet_flying_list;
}

function tpl_assign_hangar($que_type, $planet, &$template)
{
  global $user, $lang;

  $que_length = 0;
  $hangar_que_strings = explode(';', $planet['b_hangar_id']);
  foreach($hangar_que_strings as $hangar_que_string_id => $hangar_que_string)
  {
    if(!$hangar_que_string)
    {
      continue;
    }

    list($unit_id, $unit_amount) = explode(',', $hangar_que_string);

    $unit_data = eco_get_build_data($user, $planet, $unit_id, 0);

    $template->assign_block_vars('que', array(
      'ID' => $unit_id,
      'QUE' => $que_type,
      'NAME' => $lang['tech'][$unit_id],
      'TIME' => $unit_data[RES_TIME][BUILD_CREATE] - ($hangar_que_string_id ? 0 : $planet['b_hangar']),
      'TIME_FULL' => $unit_data[RES_TIME][BUILD_CREATE],
      'AMOUNT' => $unit_amount,
      'LEVEL' => -1,
    ));

    $que_length++;
  }

  return($que_length);
}

?>
