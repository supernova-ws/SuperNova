<?php

// Wrappers for functions
function gettemplatename($u_dpath)
{
  static $template_names = array();

  if(!isset($template_names[$u_dpath]))
  {
    $template_names[$u_dpath] = file_exists(SN_ROOT_PHYSICAL . $u_dpath . 'tmpl.ini') ? sys_file_read(SN_ROOT_PHYSICAL . $u_dpath . 'tmpl.ini') : TEMPLATE_NAME;
  }

  return $template_names[$u_dpath];
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Affichage d'un message administrateur avec saut vers une autre page si souhaité
//
function AdminMessage ($mes, $title = 'Error', $dest = '', $time = 3) {
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
function message ($mes, $title = 'Error', $dest = '', $time = 5, $show_header = true)
{
  $template = gettemplate('message_body', true);
  $template->assign_vars(array(
    'title' => $title,
    'mes'   => $mes,
    'DEST'  => $dest,
  ));

  display($template, $title, $show_header, (($dest != '') ? "<meta http-equiv=\"refresh\" content=\"{$time};url={$dest}\">" : ""), false);
/*
  global $lang;

  $parse['title'] = $title;
  $parse['mes']   = $mes;
  $parse['DEST']  = $dest;
  $parse['L_sys_continue']  = $lang['sys_continue'];

  $page .= parsetemplate(, $parse);

  display($page, $title, $show_header, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"{$time};url={$dest}\">" : ""), false);
*/
}

function tpl_menu_merge_extra(&$sn_menu, &$sn_menu_extra) {
  if(empty($sn_menu) || empty($sn_menu_extra)) {
    return;
  }

  foreach($sn_menu_extra as $menu_item_id => $menu_item) {
    $item_location = $menu_item['LOCATION'];
    unset($menu_item['LOCATION']);

    if(!$item_location) {
      $sn_menu[$menu_item_id] = $menu_item;
      continue;
    }

    $is_positioned = $item_location[0];
    if($is_positioned == '+' || $is_positioned == '-') {
      $item_location = substr($item_location, 1);
    } else {
      $is_positioned = '';
    }

    if($item_location) {
      $menu_keys = array_keys($sn_menu);
      $insert_position = array_search($item_location, $menu_keys);
      if($insert_position === false) {
        $insert_position = count($sn_menu)-1;
        $is_positioned = '+';
        $item_location = '';
      }
    } else {
      $insert_position = $is_positioned == '-' ? 0 : count($sn_menu);
    }

    $insert_position += $is_positioned == '+' ? 1 : 0;
    $spliced = array_splice($sn_menu, $insert_position, count($sn_menu) - $insert_position);
    $sn_menu[$menu_item_id] = $menu_item;
    if(!$is_positioned && $item_location) {
      unset($spliced[$item_location]);
    }
    $sn_menu = array_merge($sn_menu, $spliced);
  }

  $sn_menu_extra = array();
}

function tpl_menu_assign_to_template(&$sn_menu, &$template)
{
  global $lang, $user;

  if($sn_menu)
  {
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
        $lang_string = &$lang;
        if(preg_match('#(\w+)(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?#', $menu_item['ITEM'], $matches) && count($matches) > 1)
        {
          for($i = 1; $i < count($matches); $i++)
          {
            if(defined($matches[$i]))
            {
              $matches[$i] = constant($matches[$i]);
            }
            $lang_string = &$lang_string[$matches[$i]];
          }
        }
        $menu_item['ITEM'] = $lang_string && is_string($lang_string) ? $lang_string : "{L_{$menu_item['ITEM']}}";
      }

      $menu_item['ALT'] = htmlentities($menu_item['ALT']);
      $menu_item['TITLE'] = htmlentities($menu_item['TITLE']);

      if(!empty($menu_item['ICON']))
      {
        $menu_item['ICON'] = !is_string($menu_item['ICON']) ? $menu_item_id . '.png' : $menu_item['ICON'];
      }

      $template->assign_block_vars('menu', $menu_item);
    }
  }
}

function tpl_render_menu() {
  global $user, $lang, $template_result; // $config,

  //$template_name = IN_ADMIN === true ? 'admin/menu' : 'menu';
  //$template = gettemplate($template_name, true);
  $template = gettemplate('menu', true);
  $template->assign_recursive($template_result);

//  player_load_option($user, array(PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON, PLAYER_OPTION_MENU_SHOW_ON_BUTTON,
//    PLAYER_OPTION_MENU_HIDE_ON_BUTTON, PLAYER_OPTION_MENU_HIDE_ON_LEAVE, PLAYER_OPTION_MENU_UNPIN_ABSOLUTE,
//    PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS,
//  ));

//  $is_menu_customize = isset($template_result['MENU_CUSTOMIZE']);
  $template->assign_vars(array(
    'USER_AUTHLEVEL'      => $user['authlevel'],
    'USER_AUTHLEVEL_NAME' => $lang['user_level'][$user['authlevel']],
//    'USER_IMPERSONATOR'   => $template_result[F_IMPERSONATE_STATUS] != LOGIN_UNDEFINED,
    'PAYMENT'             => sn_module_get_active_count('payment'),
    'MENU_START_HIDE'     => !empty($_COOKIE[SN_COOKIE . '_menu_hidden']),
//    'MENU_START_HIDE'     => isset($_COOKIE[SN_COOKIE . '_menu_hidden']) && $_COOKIE[SN_COOKIE . '_menu_hidden'],

//    'PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON]: 0,
//    'PLAYER_OPTION_MENU_SHOW_ON_BUTTON' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON] : 0,
//    'PLAYER_OPTION_MENU_HIDE_ON_BUTTON' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON] : 0,
//    'PLAYER_OPTION_MENU_HIDE_ON_LEAVE' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE] : 0,
//    'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE] : 0,
//    'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS' => $is_menu_customize ? classSupernova::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS] : 0,
  ));

  if(isset($template_result['MENU_CUSTOMIZE'])) {
    $template->assign_vars(array(
      'PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON],
      'PLAYER_OPTION_MENU_SHOW_ON_BUTTON' => classSupernova::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
      'PLAYER_OPTION_MENU_HIDE_ON_BUTTON' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
      'PLAYER_OPTION_MENU_HIDE_ON_LEAVE' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
      'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE' => classSupernova::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
      'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS' => classSupernova::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
    ));
  }

  if(IN_ADMIN === true && $user['authlevel'] > 0) {
    //global $sn_version_check_class;
    //$template->assign_vars(array(
    //  'CHECK_DATE' => $config->server_updater_check_last ? date(FMT_DATE, $config->server_updater_check_last) : 0,
    //  'CHECK_RESULT' => $lang['adm_opt_ver_response_short'][$config->server_updater_check_result],
    //  'CHECK_CLASS' => $sn_version_check_class[$config->server_updater_check_result],
    //));
    //$template = gettemplate('menu', $template);

    global $sn_menu_admin_extra, $sn_menu_admin;

    lng_include('admin');

    tpl_menu_merge_extra($sn_menu_admin, $sn_menu_admin_extra);
    tpl_menu_assign_to_template($sn_menu_admin, $template);
  } else {
    global $sn_menu, $sn_menu_extra;

    tpl_menu_merge_extra($sn_menu, $sn_menu_extra);
    tpl_menu_assign_to_template($sn_menu, $template);
  }

  return $template;
}

function display($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true){$func_args = func_get_args();return sn_function_call('display', $func_args);}
function sn_display($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true, $die = true)
{
  global $link, $debug, $user, $planetrow, $config, $lang, $template_result, $sn_mvc;

  if(!$user || !isset($user['id']) || !is_numeric($user['id']))
  {
    $isDisplayMenu = false;
    $topnav = false;
  }

//  $template->assign_recursive($template_result);

  $isDisplayMenu = is_object($page) && isset($page->_rootref['MENU']) ? $page->_rootref['MENU'] : $isDisplayMenu;
  $topnav = is_object($page) && isset($page->_rootref['NAVBAR']) ? $page->_rootref['NAVBAR'] : $topnav;

  $title = $title ? $title : (is_object($page) && isset($page->_rootref['PAGE_HEADER']) ? $page->_rootref['PAGE_HEADER'] : '');
  if(is_object($page) && !isset($page->_rootref['PAGE_HEADER']) && $title)
  {
    $page->assign_var('PAGE_HEADER', $title);
  }

  isset($sn_mvc['view']['']) and execute_hooks($sn_mvc['view'][''], $page);

  // Global header
  $user_time_diff = user_time_diff_get();
  $user_time_measured_unix = intval(isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) ? strtotime($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) : 0);
  // $player_options = player_load_option($user);
  $font_size = !empty($_COOKIE[SN_COOKIE_F]) ? $_COOKIE[SN_COOKIE_F] : classSupernova::$user_options[PLAYER_OPTION_BASE_FONT_SIZE];
  if(!empty($font_size) && $font_size == intval($font_size)) {
    $font_size < 9 ? $font_size = 9 : false;
    $font_size > 19 ? $font_size = 19 : false;
    $font_size = ($font_size/16 * 100) . '%';
  }
  empty($font_size) ? $font_size = FONT_SIZE_PERCENT_DEFAULT . '%' :
    (floatval($font_size) < FONT_SIZE_PERCENT_MIN ? $font_size = FONT_SIZE_PERCENT_MIN :
      (floatval($font_size) > FONT_SIZE_PERCENT_MAX ? $font_size = FONT_SIZE_PERCENT_MAX : false));
  $template = gettemplate('_global_header', true);
  $template->assign_vars(array(
    'USER_AUTHLEVEL'           => intval($user['authlevel']),

    'FONT_SIZE'                => $font_size,

    'TIME_NOW'                 => SN_TIME_NOW,
    'LOGIN_LOGOUT'             => defined('LOGIN_LOGOUT') && LOGIN_LOGOUT === true,
    'GAME_MODE_CSS_PREFIX'     => $config->game_mode == GAME_BLITZ ? 'blitz_' : '',
    //'TIME_DIFF'                => SN_CLIENT_TIME_DIFF,
    'TIME_DIFF_MEASURE'        => intval(
      empty($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED])
      &&
      (SN_TIME_NOW - $user_time_measured_unix > PERIOD_HOUR || $user_time_diff[PLAYER_OPTION_TIME_DIFF] == '')
    ), // Проводить замер только если не выставлен флаг форсированного замера И (иссяк интервал замера ИЛИ замера еще не было)
    //'TIME_UTC_OFFSET'          => defined('SN_CLIENT_TIME_UTC_OFFSET') ? SN_CLIENT_TIME_UTC_OFFSET : '',

    'title'                    => ($title ? "{$title} - " : '') . "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}",
    '-meta-'                   => $metatags,
    'ADV_SEO_META_DESCRIPTION' => $config->adv_seo_meta_description,
    'ADV_SEO_META_KEYWORDS'    => $config->adv_seo_meta_keywords,
    'ADV_SEO_JAVASCRIPT'       => $config->adv_seo_javascript,

    'LANG_LANGUAGE'            => $lang['LANG_INFO']['LANG_NAME_ISO2'],
    'LANG_ENCODING'            => 'utf-8',
    'LANG_DIRECTION'           => $lang['LANG_INFO']['LANG_DIRECTION'],

    'SOUND_ENABLED'            => classSupernova::$user_options[PLAYER_OPTION_SOUND_ENABLED],

    'IMPERSONATING'            => $template_result[F_IMPERSONATE_STATUS] ? sprintf($lang['sys_impersonated_as'], $user['username'], $template_result[F_IMPERSONATE_OPERATOR]['username']) : '',
  ));
  $template->assign_recursive($template_result);
  displayP(parsetemplate($template));

  if($isDisplayMenu && !isset($_COOKIE['menu_disable'])) {
    $AdminPage = $AdminPage ? $user['authlevel'] : 0;
    displayP(parsetemplate(tpl_render_menu($AdminPage)));
  }

  if($topnav) {
    displayP(parsetemplate(tpl_render_topnav($user, $planetrow)));
  }

  displayP(parsetemplate(gettemplate('_content_header', true)));

  if(!is_array($page))
  {
    $page = array($page);
  }
  $result_added = false;
  foreach($page as $page_item)
  {
    if(!$result_added && is_object($page_item) && isset($page_item->_tpldata['result']))
    {
      $page_item = gettemplate('_result_message', $page_item);
      $temp = $page_item->files['_result_message'];
      unset($page_item->files['_result_message']);
      $page_item->files = array_reverse($page_item->files);
      $page_item->files['_result_message'] = $temp;
      $page_item->files = array_reverse($page_item->files);
      $result_added = true;
    }
    displayP($page_item);
  }
//  echo '</center>';
  if($isDisplayMenu)
  {
//    echo '</div>';
  }
  displayP(parsetemplate(gettemplate('_content_footer', true)));

  // Global footer
  $template = gettemplate('_global_footer', true);
  $template->assign_vars(array(
    'ADMIN_EMAIL' => $config->game_adminEmail,
    'TIME_NOW' => SN_TIME_NOW,
    'SN_VERSION'  => SN_VERSION,
  ));
  displayP(parsetemplate($template));

  $user['authlevel'] >= 3 && $config->debug ? $debug->echo_log() : false;;

  isset($link) ? sn_db_diconnect($link) : false;

  sn_benchmark();

  $die ? die($die === true ? 0 : $die) : false;
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
    'COORDINATES_TYPE' => $fleet_flying_row["{$prefix}type"],
    'TEXT' => "{$msg}",
    'DECREASE' => $is_decrease,
  );
  $fleet_event_count++;
}

function tpl_topnav_event_build(&$template, $fleet_flying_list, $type = 'fleet')
{
  if(empty($fleet_flying_list))
  {
    return;
  }

  global $lang;

  $fleet_event_count = 0;
  $fleet_flying_sorter = array();
  $fleet_flying_events = array();
  foreach($fleet_flying_list as &$fleet_flying_row)
  {
    $will_return = true;
    if($fleet_flying_row['fleet_mess'] == 0)
    {
      if($fleet_flying_row['fleet_start_time'] >= SN_TIME_NOW) // cut fleets on Hold and Expedition
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
      'TIME' => max(0, $fleet_time - SN_TIME_NOW),
      'TEXT' => $fleet_flying_count,
      'HINT' => date(FMT_DATE_TIME, $fleet_time + SN_CLIENT_TIME_DIFF) . " - {$lang['sys_fleet']} {$fleet_event['TEXT']} {$fleet_event['COORDINATES']} {$lang['sys_planet_type_sh'][$fleet_event['COORDINATES_TYPE']]} {$lang['type_mission'][$fleet_event['ROW']['fleet_mission']]}",
    ));
    if($fleet_event['DECREASE'])
    {
      $fleet_flying_count--;
    }
  }
}

function tpl_render_topnav(&$user, $planetrow){return sn_function_call('tpl_render_topnav', array(&$user, $planetrow));}
function sn_tpl_render_topnav(&$user, $planetrow) {
  if (!is_array($user)) {
    return '';
  }

  global $lang, $config;

  $GET_mode = sys_get_param_str('mode');

  $template       = gettemplate('topnav', true);

  /*
  $planetrow = $planetrow ? $planetrow : $user['current_planet'];

  sn_db_transaction_start();
  $planetrow = sys_o_get_updated($user, $planetrow, SN_TIME_NOW);
  sn_db_transaction_commit();
  $planetrow = $planetrow['planet'];
  */

  $ThisUsersPlanets = db_planet_list_sorted ( $user );
  // while ($CurPlanet = db_fetch($ThisUsersPlanets))
  foreach($ThisUsersPlanets as $CurPlanet)
  {
    if (!$CurPlanet['destruyed'])
    {
      $fleet_listx = flt_get_fleets_to_planet($CurPlanet);

      $template->assign_block_vars('topnav_planets', array(
        'ID'     => $CurPlanet['id'],
        'NAME'   => $CurPlanet['name'],
        'PLIMAGE'  => $CurPlanet['image'],
        'FLEET_ENEMY'   => $fleet_listx['enemy']['count'],
        'COORDS' => uni_render_coordinates($CurPlanet),
        'SELECTED' => $CurPlanet['id'] == $user['current_planet'] ? ' selected' : '',
      ));
    }
  }

  $fleet_flying_list = tpl_get_fleets_flying($user);
  tpl_topnav_event_build($template, $fleet_flying_list[0]);
  tpl_topnav_event_build($template, $fleet_flying_list[MT_EXPLORE], 'expedition');

  que_tpl_parse($template, QUE_RESEARCH, $user);
  que_tpl_parse($template, SUBQUE_FLEET, $user, $planetrow, null, true);

  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate(SN_TIME_NOW);
  $time_local_parsed = getdate(defined('SN_CLIENT_TIME_LOCAL') ? SN_CLIENT_TIME_LOCAL : SN_TIME_NOW);

  if($config->game_news_overview) {
    nws_render($template, "WHERE UNIX_TIMESTAMP(`tsTimeStamp`) >= {$user['news_lastread']}", $config->game_news_overview);
  }

  $notes_query = doquery("SELECT * FROM {{notes}} WHERE `owner` = {$user['id']} AND `sticky` = 1 ORDER BY priority DESC, time DESC");
  while($note_row = db_fetch($notes_query)) {
    note_assign($template, $note_row);
  }

  $premium_lvl = mrc_get_level($user, false, UNIT_PREMIUM, true, true);

  $template->assign_vars(array(
    'QUE_ID'             => QUE_RESEARCH,
    'QUE_HTML'           => 'topnav',

    'RESEARCH_ONGOING'   => (boolean)$user['que'],

    'TIME_TEXT'          => sprintf($str_date_format, $time_now_parsed['year'], $lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),
    'TIME_TEXT_LOCAL'          => sprintf($str_date_format, $time_local_parsed['year'], $lang['months'][$time_local_parsed['mon']], $time_local_parsed['mday'],
      $time_local_parsed['hours'], $time_local_parsed['minutes'], $time_local_parsed['seconds']
    ),

    'GAME_BLITZ_REGISTER'      => $config->game_blitz_register,
    'GAME_BLITZ_REGISTER_TEXT'      => $lang['sys_blitz_registration_mode_list'][$config->game_blitz_register],
    'BLITZ_REGISTER_OPEN'      => $config->game_blitz_register == BLITZ_REGISTER_OPEN,
    'BLITZ_REGISTER_CLOSED'      => $config->game_blitz_register == BLITZ_REGISTER_CLOSED,
    'BLITZ_REGISTER_SHOW_LOGIN'      => $config->game_blitz_register == BLITZ_REGISTER_SHOW_LOGIN,
    'BLITZ_REGISTER_DISCLOSURE_NAMES'      => $config->game_blitz_register == BLITZ_REGISTER_DISCLOSURE_NAMES,
    'GAME_BLITZ'               => $config->game_mode == GAME_BLITZ,

    'USERS_ONLINE'         => $config->var_online_user_count,
    'USERS_TOTAL'          => $config->users_amount,
    'USER_RANK'            => $user['total_rank'],
    'USER_NICK'            => $user['username'],
    'USER_AVATAR'          => $user['avatar'],
    'USER_AVATARID'        => $user['id'],
    'USER_PREMIUM'         => $premium_lvl,
    'USER_RACE'        	   => $user['player_race'],

    'TOPNAV_CURRENT_PLANET' => $user['current_planet'],
    'TOPNAV_CURRENT_PLANET_NAME' => uni_render_planet_full($planetrow), // htmlspecialchars($planetrow['name']),
    'TOPNAV_CURRENT_PLANET_IMAGE' => ($user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH) . 'planeten/small/s_' . $planetrow['image'] . '.jpg',
    'TOPNAV_COLONIES_CURRENT' => get_player_current_colonies($user),
    'TOPNAV_COLONIES_MAX' => get_player_max_colonies($user),
    'TOPNAV_MODE' => $GET_mode,

    'TOPNAV_DARK_MATTER' => mrc_get_level($user, '', RES_DARK_MATTER),
    'TOPNAV_DARK_MATTER_TEXT' => pretty_number(mrc_get_level($user, '', RES_DARK_MATTER)),
    'TOPNAV_DARK_MATTER_PLAIN' => mrc_get_level($user, '', RES_DARK_MATTER, false, true),
    'TOPNAV_DARK_MATTER_PLAIN_TEXT' => pretty_number(mrc_get_level($user, '', RES_DARK_MATTER, false, true)),
    'TOPNAV_METAMATTER'  => mrc_get_level($user, '', RES_METAMATTER),
    'TOPNAV_METAMATTER_TEXT'  => pretty_number(mrc_get_level($user, '', RES_METAMATTER)),

    // TODO ГРЯЗНЫЙ ХАК!!!
    'TOPNAV_PAYMENT' => sn_module_get_active_count('payment') && !defined('SN_GOOGLE'),

    'TOPNAV_MESSAGES_ADMIN'     => $user['msg_admin'],
    'TOPNAV_MESSAGES_PLAYER'    => $user['mnl_joueur'],
    'TOPNAV_MESSAGES_ALLIANCE'  => $user['mnl_alliance'],
    'TOPNAV_MESSAGES_ATTACK'    => $user['mnl_attaque'],
    'TOPNAV_MESSAGES_ALL'       => $user['new_message'],

    'TOPNAV_FLEETS_FLYING'      => count($fleet_flying_list[0]),
    'TOPNAV_FLEETS_TOTAL'       => GetMaxFleets($user),
    'TOPNAV_EXPEDITIONS_FLYING' => count($fleet_flying_list[MT_EXPLORE]),
    'TOPNAV_EXPEDITIONS_TOTAL'  => get_player_max_expeditons($user),

    'TOPNAV_QUEST_COMPLETE'     => get_quest_amount_complete($user['id']),

    'GAME_NEWS_OVERVIEW'        => $config->game_news_overview,
    'GAME_RESEARCH_DISABLED'    => defined('GAME_RESEARCH_DISABLED') && GAME_RESEARCH_DISABLED,
    'GAME_DEFENSE_DISABLED'     => defined('GAME_DEFENSE_DISABLED') && GAME_DEFENSE_DISABLED,
    'GAME_STRUCTURES_DISABLED'  => defined('GAME_STRUCTURES_DISABLED') && GAME_STRUCTURES_DISABLED,
    'GAME_HANGAR_DISABLED'      => defined('GAME_HANGAR_DISABLED') && GAME_HANGAR_DISABLED,

    'PLAYER_OPTION_NAVBAR_DISABLE_PLANET' => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_PLANET],
    'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR' => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_HANGAR],
    'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS' => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_QUESTS],

    'SUBQUE_FLEET'              => SUBQUE_FLEET,
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

function displayP($template) {
  if(is_object($template)) {
    if(!$template->parsed) {
      parsetemplate($template);
    }

    foreach($template->files as $section => $filename) {
      $template->display($section);
    }
  } else {
    print($template);
  }
}

function parsetemplate($template, $array = false) {
  if(is_object($template)) {
    global $user;

    if($array) {
      foreach($array as $key => $data) {
        $template->assign_var($key, $data);
      }
    }

    $tmpl_name = gettemplatename($user['dpath']);

    $template->assign_vars(array(
      'dpath'         => $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH,
      'TIME_NOW'      => SN_TIME_NOW,
      'USER_AUTHLEVEL'=> isset($user['authlevel']) ? $user['authlevel'] : -1,
      'SN_GOOGLE'     => defined('SN_GOOGLE'),
    ));

    $template->parsed = true;

    return $template;
  } else {
    $search[] = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
    $replace[] = '((isset($lang[\'\1\'][\'\2\'])) ? $lang[\'\1\'][\'\2\'] : \'{L_\1[\2]}\');';

    $search[] = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
//    $replace[] = '((isset($lang[\'\1\'])) ? $lang[\'\1\'] : \'\{L_\}\');';
    $replace[] = '((isset($lang[\'\1\'])) ? $lang[\'\1\'] : \'{L_\1}\');';

    $search[] = '#\{([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '((isset($array[\'\1\'])) ? $array[\'\1\'] : \'{\1}\');';

    return preg_replace($search, $replace, $template);
  }
}

/**
 * @param            $files
 * @param bool|false $template
 * @param bool|false $template_path
 *
 * @return template
 */
function gettemplate($files, $template = false, $template_path = false)
{
  global $user;

  $template_ex = '.tpl.html';

  if($template === false)
  {
    return sys_file_read(TEMPLATE_DIR . '/' . $files . $template_ex);
  }

  if(is_string($files)) {
//    $files = array('body' => $files);
    $files = array(basename($files) => $files);
  }

  if(!is_object($template)) {
    $template = new template();
  }
  //$template->set_custom_template($template_path ? $template_path : TEMPLATE_DIR, TEMPLATE_NAME, TEMPLATE_DIR);

  $tmpl_name = gettemplatename($user['dpath']);
  $template->set_custom_template(($template_path ? $template_path : SN_ROOT_PHYSICAL . 'design/templates/') . $tmpl_name . '/', $tmpl_name, TEMPLATE_DIR);

  // TODO ГРЯЗНЫЙ ХАК! Это нужно, что бы по возможности перезаписать инфу из языковых пакетов модулей там, где она была перезаписана раньше инфой из основного пакета. Почему?
  //  - сначала грузятся модули и их языковые пакеты
  //  - затем по ходу дела ОСНОВНОЙ языковой пакет может перезаписать данные из МОДУЛЬНОГО языкового пакета
  // Поэтому и нужен этот грязный хак
  // В норме же - страницы заявляют сами, какие им пакеты нужны. Так что сначала всегда должны грузится основные языковые пакеты, а уже ПОВЕРХ них - пакеты модулей
  global $sn_mvc, $sn_page_name;
  !empty($sn_mvc['i18n']['']) ? lng_load_i18n($sn_mvc['i18n']['']) : false;
  $sn_page_name ? lng_load_i18n($sn_mvc['i18n'][$sn_page_name]) : false;

  foreach($files as &$filename) {
    $filename = $filename . $template_ex;
  }

  $template->set_filenames($files);

  return $template;
}

function tpl_login_lang(&$template)
{
  global $language;

  $url_params = array();

  if($language) {
    $url_params[] = "lang={$language}";
  }
  if($id_ref = sys_get_param_id('id_ref')) {
    $url_params[] = "id_ref={$id_ref}";
  }

  $template->assign_vars($q = array(
//    'LANG' => $language ? '?lang=' . $language : '',
    'LANG' => $language ? $language : '',
    'referral' => $id_ref ? '&id_ref=' . $id_ref : '',

    'REQUEST_PARAMS' => !empty($url_params) ? '?' . implode('&', $url_params) : '',// "?lang={$language}" . ($id_ref ? "&id_ref={$id_ref}" : ''),
    'FILENAME' => basename($_SERVER['PHP_SELF']),
  ));

  foreach(lng_get_list() as $lng_id => $lng_data)
  {
    if(isset($lng_data['LANG_VARIANTS']) && is_array($lng_data['LANG_VARIANTS']))
    {
      foreach($lng_data['LANG_VARIANTS'] as $lang_variant)
      {
        $lng_data1 = $lng_data;
        $lng_data1 = array_merge($lng_data1, $lang_variant);
        $template->assign_block_vars('language', $lng_data1);
      }
    }
    else
    {
      $template->assign_block_vars('language', $lng_data);
    }
  }
}

function tpl_get_fleets_flying(&$user)
{
  $fleet_flying_list = array();
  $fleet_flying_query = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner = {$user['id']}");
  while($fleet_flying_row = db_fetch($fleet_flying_query))
  {
    $fleet_flying_list[0][] = $fleet_flying_row;
    $fleet_flying_list[$fleet_flying_row['fleet_mission']][] = &$fleet_flying_list[0][count($fleet_flying_list)-1];
  }
  return $fleet_flying_list;
}

function tpl_assign_hangar(&$template, $planet, $que_type)
{
  $que = que_get($planet['id_owner'], $planet['id'], $que_type);
  $que = $que['ques'][$que_type][$planet['id_owner']][$planet['id']];
  $que_length = 0;
  if(!empty($que))
  {
    foreach($que as $que_item)
    {
      $template->assign_block_vars('que', que_tpl_parse_element($que_item));
    }
    $que_length = count($que);
  }

  return $que_length;
}

function tpl_planet_density_info(&$template, &$density_price_chart, $user_dark_matter) {
  global $lang;

  $density_base_cost = get_unit_param(UNIT_PLANET_DENSITY, P_COST);
  $density_base_cost = $density_base_cost[RES_DARK_MATTER];

  foreach($density_price_chart as $density_price_index => &$density_price_data) {
    //$density_number_style = pretty_number($density_cost = $density_base_cost * $density_price_data, true, $user_dark_matter, false, false);
    // $density_cost = ceil($density_base_cost * $density_price_data);
    $density_cost = $density_price_data;
    $density_number_style = pretty_number($density_cost, true, $user_dark_matter, false, false);

    $density_price_data = array(
      'COST' => $density_cost,
      'COST_TEXT' => $density_number_style['text'],
      'COST_TEXT_CLASS' => $density_number_style['class'],
      'REST' => $user_dark_matter - $density_cost,
      'ID' => $density_price_index,
      'TEXT' => $lang['uni_planet_density_types'][$density_price_index],
    );
    $template->assign_block_vars('densities', $density_price_data);
  }
}

function tpl_assign_select(&$template, $name, $values)
{
  foreach($values as $key => $value)
  {
    $template->assign_block_vars($name, array(
      'KEY' => htmlentities($key, ENT_COMPAT, 'UTF-8'),
      'VALUE' => htmlentities($value, ENT_COMPAT, 'UTF-8'),
    ));
  }
}
