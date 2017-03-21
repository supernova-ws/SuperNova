<?php

use \Pages\PageTutorial;

// Wrappers for functions

/**
 * Get template name from path to skin
 *
 * @param $userSkinPath
 *
 * @return mixed
 */
function getSkinPathTemplate($userSkinPath) {
  static $template_names = array();

  if(!isset($template_names[$userSkinPath])) {
    $template_names[$userSkinPath] = file_exists(SN_ROOT_PHYSICAL . $userSkinPath . '_template.ini') ? sys_file_read(SN_ROOT_PHYSICAL . $userSkinPath . '_template.ini') : TEMPLATE_NAME;
  }

  return $template_names[$userSkinPath];
}

function AdminCheckLevel($level = 3) {
  global $user, $lang;

  if($user['authlevel'] < $level) {
    AdminMessage($lang['adm_err_denied'], 'Error', SN_ROOT_VIRTUAL . 'overview.php');
  }
}

/**
 * @param        $mes
 * @param string $title
 * @param string $dest
 * @param int    $time
 */
function AdminMessage($mes, $title = 'Error', $dest = '', $time = 5) {
  $template = gettemplate('admin/message_body', true);
  $template->assign_vars(array(
    'GLOBAL_META_TAGS'      => $dest ? "<meta http-equiv=\"refresh\" content=\"{$time};URL={$dest}\">" : '',
    'GLOBAL_DISPLAY_NAVBAR' => false,

    'title' => $title,
    'mes'   => $mes,
    'DEST'  => $dest,
  ));

  display($template, $title);
}

/**
 * @param           $mes
 * @param string    $title
 * @param string    $dest
 * @param int       $time
 * @param bool|true $show_header
 */
function message($mes, $title = 'Error', $dest = '', $time = 5, $show_header = true) {
  $template = gettemplate('message_body', true);
  $template->assign_vars(array(
    'GLOBAL_META_TAGS' => $dest ? "<meta http-equiv=\"refresh\" content=\"{$time};URL={$dest}\">" : '',
    'GLOBAL_DISPLAY_NAVBAR' => $show_header,

    'title' => $title,
    'mes'   => $mes,
    'DEST'  => $dest,
  ));

  display($template, $title);
}

/**
 * @param $sn_menu
 * @param $sn_menu_extra
 */
function tpl_menu_merge_extra(&$sn_menu, &$sn_menu_extra) {
  if(empty($sn_menu) || empty($sn_menu_extra)) {
    return;
  }

  foreach($sn_menu_extra as $menu_item_id => $menu_item) {
    if(empty($menu_item['LOCATION'])) {
      $sn_menu[$menu_item_id] = $menu_item;
      continue;
    }

    $item_location = $menu_item['LOCATION'];
    unset($menu_item['LOCATION']);

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
        $insert_position = count($sn_menu) - 1;
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

/**
 * @param array    $sn_menu
 * @param template $template
 */
function tpl_menu_assign_to_template(&$sn_menu, &$template) {
  global $lang;

  if(empty($sn_menu) || !is_array($sn_menu)) {
    return;
  }

  foreach($sn_menu as $menu_item_id => $menu_item) {
    if(!$menu_item) {
      continue;
    }

    if(is_string($menu_item_id)) {
      $menu_item['ID'] = $menu_item_id;
    }

    if($menu_item['TYPE'] == 'lang') {
      $lang_string = &$lang;
      if(preg_match('#(\w+)(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?#', $menu_item['ITEM'], $matches) && count($matches) > 1) {
        for($i = 1; $i < count($matches); $i++) {
          if(defined($matches[$i])) {
            $matches[$i] = constant($matches[$i]);
          }
          $lang_string = &$lang_string[$matches[$i]];
        }
      }
      $menu_item['ITEM'] = $lang_string && is_string($lang_string) ? $lang_string : "{L_{$menu_item['ITEM']}}";
    }

    $menu_item['ALT'] = htmlentities($menu_item['ALT']);
    $menu_item['TITLE'] = htmlentities($menu_item['TITLE']);

    if(!empty($menu_item['ICON'])) {
      if(is_string($menu_item['ICON'])) {
        $menu_item['ICON_PATH'] = $menu_item['ICON'];
      } else {
        $menu_item['ICON'] = $menu_item_id;
      }
    }

    $template->assign_block_vars('menu', $menu_item);
  }
}

/**
 * @param template $template
 *
 * @return template
 */
function tpl_render_menu($template) {
  global $user, $lang, $template_result, $sn_menu_admin_extra, $sn_menu_admin, $sn_menu, $sn_menu_extra;

  lng_include('admin');

//  $template = gettemplate('menu', true);
//  $template->assign_recursive($template_result);

  $template->assign_vars(array(
    'USER_AUTHLEVEL'      => $user['authlevel'],
    'USER_AUTHLEVEL_NAME' => $lang['user_level'][$user['authlevel']],
//    'USER_IMPERSONATOR'   => $template_result[F_IMPERSONATE_STATUS] != LOGIN_UNDEFINED,
    'PAYMENT'             => sn_module_get_active_count('payment'),
    'MENU_START_HIDE'     => !empty($_COOKIE[SN_COOKIE . '_menu_hidden']) || defined('SN_GOOGLE'),
  ));

  if(isset($template_result['MENU_CUSTOMIZE'])) {
    $template->assign_vars(array(
      'PLAYER_OPTION_MENU_SHOW_ON_BUTTON'   => classSupernova::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
      'PLAYER_OPTION_MENU_HIDE_ON_BUTTON'   => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
      'PLAYER_OPTION_MENU_HIDE_ON_LEAVE'    => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
      'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE'   => classSupernova::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
      'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS' => classSupernova::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
      'PLAYER_OPTION_MENU_WHITE_TEXT'       => classSupernova::$user_options[PLAYER_OPTION_MENU_WHITE_TEXT],
      'PLAYER_OPTION_MENU_OLD'              => classSupernova::$user_options[PLAYER_OPTION_MENU_OLD],
      'PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON' => empty($_COOKIE[SN_COOKIE . '_menu_hidden']) && !defined('SN_GOOGLE')
        ? classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON] : 1,
    ));
  }

  if(defined('IN_ADMIN') && IN_ADMIN === true && !empty($user['authlevel']) && $user['authlevel'] > 0) {
    tpl_menu_merge_extra($sn_menu_admin, $sn_menu_admin_extra);
    tpl_menu_assign_to_template($sn_menu_admin, $template);
  } else {
    tpl_menu_merge_extra($sn_menu, $sn_menu_extra);
    tpl_menu_assign_to_template($sn_menu, $template);
  }

  return $template;
}

/**
 * @param template|string $page
 * @param string          $title
 * @param bool|true       $isDisplayTopNav
 * @param string          $metatags
 * @param bool|false      $AdminPage
 * @param bool|true       $isDisplayMenu
 *
 * @return mixed
 */
function display($page, $title = '') {
  if(!defined('SN_TIME_RENDER_START')) {
    define('SN_TIME_RENDER_START', microtime(true));
  }

  return sn_function_call('display', array($page, $title));
}

/**
 * @param template|string $page
 * @param string          $title
 * @param bool|true       $isDisplayTopNav
 * @param string          $metatags
 * @param bool|false      $AdminPage
 * @param bool|true       $isDisplayMenu
 * @param bool|int|string $exitStatus - Код или сообщение выхода
 */
function sn_display($page, $title = '') {
  global $debug, $user, $planetrow, $config, $lang, $template_result, $sn_mvc, $sn_page_name;

  $isDisplayTopNav = true;
  $metatags = '';
  $isDisplayMenu = true;
  $exitStatus = true;

  $in_admin = defined('IN_ADMIN') && IN_ADMIN === true;
  $is_login = defined('LOGIN_LOGOUT') && LOGIN_LOGOUT === true;

  $isRenderGlobal = true;
  if(is_object($page)) {
    isset($page->_rootref['GLOBAL']) ? $isRenderGlobal = $page->_rootref['GLOBAL'] : false;

    isset($page->_rootref['GLOBAL_DISPLAY_MENU']) ? $isDisplayMenu = $page->_rootref['GLOBAL_DISPLAY_MENU'] : false;
    isset($page->_rootref['MENU']) ? $isDisplayMenu = $page->_rootref['MENU'] : false;

    isset($page->_rootref['GLOBAL_DISPLAY_NAVBAR']) ? $isDisplayTopNav = $page->_rootref['GLOBAL_DISPLAY_NAVBAR'] : false;
    isset($page->_rootref['NAVBAR']) ? $isDisplayTopNav = $page->_rootref['NAVBAR'] : false;

    isset($page->_rootref['PAGE_TITLE']) && empty($title) ? $title = $page->_rootref['PAGE_TITLE'] : false;
    !$title && !empty($page->_rootref['PAGE_HEADER']) ? $title = $page->_rootref['PAGE_HEADER'] : false;
    !isset($page->_rootref['PAGE_HEADER']) && $title ? $page->assign_var('PAGE_HEADER', $title) : false;
  }

  $isDisplayMenu = ($isDisplayMenu || $in_admin) && !isset($_COOKIE['menu_disable']);
  $isDisplayTopNav = $isDisplayTopNav && !$in_admin;

  if(empty($user['id']) || !is_numeric($user['id'])) {
    $isDisplayMenu = false;
    $isDisplayTopNav = false;
  }

  if(defined('LOGIN_LOGOUT') && LOGIN_LOGOUT === true) {
    $isDisplayMenu = false;
    $isDisplayTopNav = false;
  }

  !empty($sn_mvc['view']['']) and execute_hooks($sn_mvc['view'][''], $page, 'view', '');

  // Global header
  $template_result['LOGIN_LOGOUT'] = $is_login;

  if($isRenderGlobal) {
    $user_time_diff = playerTimeDiff::user_time_diff_get();
    $user_time_measured_unix = intval(isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) ? strtotime($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) : 0);
    $measureTimeDiff = intval(
      empty($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED])
      &&
      (SN_TIME_NOW - $user_time_measured_unix > PERIOD_HOUR || $user_time_diff[PLAYER_OPTION_TIME_DIFF] == '')
    );

    $template = gettemplate('_page_20_header', true);

    tpl_global_header($template_result, $is_login);

    $metatags = isset($page->_rootref['GLOBAL_META_TAGS']) ? $page->_rootref['GLOBAL_META_TAGS'] : $metatags;
    $template->assign_vars(array(
      'GLOBAL_DISPLAY_MENU' => $isDisplayMenu,
      'GLOBAL_DISPLAY_NAVBAR' => $isDisplayTopNav,

      'USER_AUTHLEVEL' => intval($user['authlevel']),

      'FONT_SIZE'                        => playerFontSize(),
      'FONT_SIZE_PERCENT_DEFAULT_STRING' => FONT_SIZE_PERCENT_DEFAULT_STRING,

      'SN_TIME_NOW'          => SN_TIME_NOW,
      'LOGIN_LOGOUT'         => $is_login,
      'GAME_MODE_CSS_PREFIX' => $config->game_mode == GAME_BLITZ ? 'blitz_' : '',
      'TIME_DIFF_MEASURE'    => $measureTimeDiff, // Проводить замер только если не выставлен флаг форсированного замера И (иссяк интервал замера ИЛИ замера еще не было)

      'title'                    => ($title ? "{$title} - " : '') . "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}",
      'GLOBAL_META_TAGS'         => $metatags,
      'ADV_SEO_META_DESCRIPTION' => $config->adv_seo_meta_description,
      'ADV_SEO_META_KEYWORDS'    => $config->adv_seo_meta_keywords,
      'ADV_SEO_JAVASCRIPT'       => $config->adv_seo_javascript,

      'LANG_LANGUAGE'  => $lang['LANG_INFO']['LANG_NAME_ISO2'],
      'LANG_ENCODING'  => 'utf-8',
      'LANG_DIRECTION' => $lang['LANG_INFO']['LANG_DIRECTION'],

      'SOUND_ENABLED'                        => classSupernova::$user_options[PLAYER_OPTION_SOUND_ENABLED],
      'PLAYER_OPTION_ANIMATION_DISABLED'     => classSupernova::$user_options[PLAYER_OPTION_ANIMATION_DISABLED],
      'PLAYER_OPTION_PROGRESS_BARS_DISABLED' => classSupernova::$user_options[PLAYER_OPTION_PROGRESS_BARS_DISABLED],

      // 'IMPERSONATING'            => $template_result[F_IMPERSONATE_STATUS] != LOGIN_UNDEFINED ? sprintf($lang['sys_impersonated_as'], $user['username'], classSupernova::$auth->account->account_name) : '',
      'IMPERSONATING'                        => !empty($template_result[F_IMPERSONATE_STATUS]) ? sprintf($lang['sys_impersonated_as'], $user['username'], $template_result[F_IMPERSONATE_OPERATOR]) : '',
      'PLAYER_OPTION_DESIGN_DISABLE_BORDERS' => classSupernova::$user_options[PLAYER_OPTION_DESIGN_DISABLE_BORDERS],
    ));
    $template->assign_recursive($template_result);

    if($isDisplayMenu) {
      tpl_render_menu($template);
    }

    if($isDisplayTopNav) {
      tpl_render_topnav($user, $planetrow, $template);
    }
    displayP($template);
  }

  // Page content
  !is_array($page) ? $page = array($page) : false;
  $result_added = false;
  foreach($page as $page_item) {
    if(!$result_added && is_object($page_item) && isset($page_item->_tpldata['result'])) {
      $resultTemplate = gettemplate('_result_message');
      $resultTemplate->_tpldata = $page_item->_tpldata;
      displayP($resultTemplate);
//      $page_item = gettemplate('_result_message', $page_item);
//      $temp = $page_item->files['_result_message'];
//      unset($page_item->files['_result_message']);
//      $page_item->files = array_reverse($page_item->files);
//      $page_item->files['_result_message'] = $temp;
//      $page_item->files = array_reverse($page_item->files);
      $result_added = true;
    }

    displayP($page_item);
  }

  // Global footer
  if($isRenderGlobal) {
    $templateFooter = gettemplate('_page_90_footer', true);

    $templateFooter->assign_vars(array(
      'SN_TIME_NOW'  => SN_TIME_NOW,
      'SN_VERSION'   => SN_VERSION,
      'ADMIN_EMAIL'  => $config->game_adminEmail,
      'CURRENT_YEAR' => date('Y', SN_TIME_NOW),
    ));

    displayP($templateFooter);
  }

  $user['authlevel'] >= 3 && $config->debug ? $debug->echo_log() : false;;

  sn_db_disconnect();

  $exitStatus and die($exitStatus === true ? 0 : $exitStatus);
}

/**
 * @return mixed|string
 */
function playerFontSize() {
  $font_size = !empty($_COOKIE[SN_COOKIE_F]) ? $_COOKIE[SN_COOKIE_F] : classSupernova::$user_options[PLAYER_OPTION_BASE_FONT_SIZE];
  if (strpos($font_size, '%') !== false) {
    // Размер шрифта в процентах
    $font_size = min(max(floatval($font_size), FONT_SIZE_PERCENT_MIN), FONT_SIZE_PERCENT_MAX) . '%';

    return $font_size;
  } elseif (strpos($font_size, 'px') !== false) {
    // Размер шрифта в пикселях
    $font_size = min(max(floatval($font_size), FONT_SIZE_PIXELS_MIN), FONT_SIZE_PIXELS_MAX) . 'px';

    return $font_size;
  } else {
    // Не мышонка, не лягушка...
    $font_size = FONT_SIZE_PERCENT_DEFAULT_STRING;

    return $font_size;
  }
}

/**
 * @param $template_result
 * @param $is_login
 */
function tpl_global_header(&$template_result, $is_login) {
  global $sn_mvc, $sn_page_name, $user;

  if(!empty($sn_mvc['javascript'])) {
    foreach($sn_mvc['javascript'] as $page_name => $script_list) {
      if(empty($page_name) || $page_name == $sn_page_name) {
        foreach($script_list as $filename => $content) {
          $template_result['.']['javascript'][] = array(
            'FILE'    => $filename,
            'CONTENT' => $content,
          );
        }
      }
    }
  }

  empty($sn_mvc['css']) ? $sn_mvc['css'] = array('' => array()) : false;
  $standard_css = array(
    'design/css/jquery-ui.css' => '',
    'design/css/global.min.css' => '',
  );
  $is_login ? $standard_css['design/css/login.min.css'] = '': false;
  $standard_css += array(
//    'design/css/design/css/global-ie.min.css' => '', // TODO
    TEMPLATE_PATH . '/_template.min.css' => '',
    classSupernova::$gc->theUser->getSkinPath() . 'skin.min.css' => '',
  );

  // Prepending standard CSS files
  $sn_mvc['css'][''] = array_merge($standard_css, $sn_mvc['css']['']);


  foreach($sn_mvc['css'] as $page_name => $script_list) {
    if(empty($page_name) || $page_name == $sn_page_name) {
      foreach($script_list as $filename => $content) {
        $template_result['.']['css'][] = array(
          'FILE'    => $filename,
          'CONTENT' => $content,
        );
      }
    }
  }

}

/**
 * @param $time
 * @param $event
 * @param $msg
 * @param $prefix
 * @param $is_decrease
 * @param $fleet_flying_row
 * @param $fleet_flying_sorter
 * @param $fleet_flying_events
 * @param $fleet_event_count
 */
function tpl_topnav_event_build_helper($time, $event, $msg, $prefix, $is_decrease, $fleet_flying_row, &$fleet_flying_sorter, &$fleet_flying_events, &$fleet_event_count) {
  $fleet_flying_sorter[$fleet_event_count] = $time;
  $fleet_flying_events[$fleet_event_count] = array(
    'ROW'              => $fleet_flying_row,
    'FLEET_ID'         => $fleet_flying_row['fleet_id'],
    'EVENT'            => $event,
    'COORDINATES'      => uni_render_coordinates($fleet_flying_row, $prefix),
    'COORDINATES_TYPE' => $fleet_flying_row["{$prefix}type"],
    'TEXT'             => "{$msg}",
    'DECREASE'         => $is_decrease,
  );
  $fleet_event_count++;
}

/**
 * @param template $template
 * @param array    $fleet_flying_list
 * @param string   $type
 */
function tpl_topnav_event_build(&$template, $fleet_flying_list, $type = 'fleet') {
  if(empty($fleet_flying_list)) {
    return;
  }

  global $lang;

  $fleet_event_count = 0;
  $fleet_flying_sorter = array();
  $fleet_flying_events = array();
  foreach($fleet_flying_list as &$fleet_flying_row) {
    $will_return = true;
    if($fleet_flying_row['fleet_mess'] == 0) {
      // cut fleets on Hold and Expedition
      if($fleet_flying_row['fleet_start_time'] >= SN_TIME_NOW) {
        $fleet_flying_row['fleet_mission'] == MT_RELOCATE ? $will_return = false : false;
        tpl_topnav_event_build_helper($fleet_flying_row['fleet_start_time'], EVENT_FLEET_ARRIVE, $lang['sys_event_arrive'], 'fleet_end_', !$will_return, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
      }
      if($fleet_flying_row['fleet_end_stay']) {
        tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_stay'], EVENT_FLEET_STAY, $lang['sys_event_stay'], 'fleet_end_', false, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
      }
    }
    if($will_return) {
      tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_time'], EVENT_FLEET_RETURN, $lang['sys_event_return'], 'fleet_start_', true, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
    }
  }

  asort($fleet_flying_sorter);

  $fleet_flying_count = count($fleet_flying_list);
  foreach($fleet_flying_sorter as $fleet_event_id => $fleet_time) {
    $fleet_event = &$fleet_flying_events[$fleet_event_id];
    $template->assign_block_vars("flying_{$type}s", array(
      'TIME' => max(0, $fleet_time - SN_TIME_NOW),
      'TEXT' => $fleet_flying_count,
      'HINT' => date(FMT_DATE_TIME, $fleet_time + SN_CLIENT_TIME_DIFF) . " - {$lang['sys_fleet']} {$fleet_event['TEXT']} {$fleet_event['COORDINATES']} {$lang['sys_planet_type_sh'][$fleet_event['COORDINATES_TYPE']]} {$lang['type_mission'][$fleet_event['ROW']['fleet_mission']]}",
    ));
    $fleet_event['DECREASE'] ? $fleet_flying_count-- : false;
  }
}

/**
 * @param array $user
 * @param array $planetrow
 * @param template $template
 *
 * @return string|template
 */
function tpl_render_topnav(&$user, $planetrow, $template) { return sn_function_call('tpl_render_topnav', array(&$user, $planetrow, $template)); }

/**
 * @param array $user
 * @param array $planetrow
 * @param template $template
 *
 * @return string|template
 */
function sn_tpl_render_topnav(&$user, $planetrow, $template) {
  global $lang, $config, $sn_module_list, $template_result, $sn_mvc;

  if(!is_array($user)) {
    return '';
  }

  $GET_mode = sys_get_param_str('mode');

  $ThisUsersPlanets = DBStaticPlanet::db_planet_list_sorted($user);
  foreach($ThisUsersPlanets as $CurPlanet) {
    if($CurPlanet['destruyed']) {
      continue;
    }

    $fleet_listx = flt_get_fleets_to_planet($CurPlanet);

    $template->assign_block_vars('topnav_planets', array(
      'ID'          => $CurPlanet['id'],
      'NAME'        => $CurPlanet['name'],
      'TYPE'        => $CurPlanet['planet_type'],
      'TYPE_TEXT'   => $lang['sys_planet_type_sh'][$CurPlanet['planet_type']],
      'PLIMAGE'     => $CurPlanet['image'],
      'FLEET_ENEMY' => $fleet_listx['enemy']['count'],
      'COORDS'      => uni_render_coordinates($CurPlanet),
      'SELECTED'    => $CurPlanet['id'] == $user['current_planet'] ? ' selected' : '',
    ));
  }

  $fleet_flying_list = tpl_get_fleets_flying($user);
  tpl_topnav_event_build($template, $fleet_flying_list[0]);
  tpl_topnav_event_build($template, $fleet_flying_list[MT_EXPLORE], 'expedition');

  que_tpl_parse($template, QUE_STRUCTURES, $user, $planetrow, null, true);
  que_tpl_parse($template, QUE_RESEARCH, $user, array(), null, !classSupernova::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE]);
  que_tpl_parse($template, SUBQUE_FLEET, $user, $planetrow, null, true);

  tpl_navbar_extra_buttons($sn_mvc, $template);
  tpl_navbar_render_news($template, $user, $config);
  tpl_navbar_render_notes($template, $user);
  $tutorial_enabled = PageTutorial::renderNavBar($template);


  $premium_lvl = mrc_get_level($user, false, UNIT_PREMIUM, true, true);

  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate(SN_TIME_NOW);
  $time_local_parsed = getdate(defined('SN_CLIENT_TIME_LOCAL') ? SN_CLIENT_TIME_LOCAL : SN_TIME_NOW);

  $template->assign_vars(array(
    'HALLOWEEN' => !empty($sn_module_list['event']['event_halloween_2015']) && $sn_module_list['event']['event_halloween_2015']->manifest['active'],

    'QUE_ID'   => QUE_RESEARCH,
    'QUE_HTML' => 'topnav',

    'RESEARCH_ONGOING' => (boolean)$user['que'],

    'TIME_TEXT'       => sprintf($str_date_format, $time_now_parsed['year'], $lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),
    'TIME_TEXT_LOCAL' => sprintf($str_date_format, $time_local_parsed['year'], $lang['months'][$time_local_parsed['mon']], $time_local_parsed['mday'],
      $time_local_parsed['hours'], $time_local_parsed['minutes'], $time_local_parsed['seconds']
    ),

    'GAME_BLITZ_REGISTER'             => $config->game_blitz_register,
    'GAME_BLITZ_REGISTER_TEXT'        => $lang['sys_blitz_registration_mode_list'][$config->game_blitz_register],
    'BLITZ_REGISTER_OPEN'             => $config->game_blitz_register == BLITZ_REGISTER_OPEN,
    'BLITZ_REGISTER_CLOSED'           => $config->game_blitz_register == BLITZ_REGISTER_CLOSED,
    'BLITZ_REGISTER_SHOW_LOGIN'       => $config->game_blitz_register == BLITZ_REGISTER_SHOW_LOGIN,
    'BLITZ_REGISTER_DISCLOSURE_NAMES' => $config->game_blitz_register == BLITZ_REGISTER_DISCLOSURE_NAMES,
    'GAME_BLITZ'                      => $config->game_mode == GAME_BLITZ,

    'USERS_ONLINE'  => $config->var_online_user_count,
    'USERS_TOTAL'   => $config->users_amount,
    'USER_RANK'     => $user['total_rank'],
    'USER_NICK'     => $user['username'],
    'USER_AVATAR'   => $user['avatar'],
    'USER_AVATARID' => $user['id'],
    'USER_PREMIUM'  => $premium_lvl,
    'USER_RACE'     => $user['player_race'],

    'TOPNAV_CURRENT_PLANET'       => $user['current_planet'],
    'TOPNAV_CURRENT_PLANET_NAME'  => uni_render_planet_full($planetrow), // htmlspecialchars($planetrow['name']),
    'TOPNAV_CURRENT_PLANET_IMAGE' => $planetrow['image'],
    'TOPNAV_COLONIES_CURRENT'     => get_player_current_colonies($user),
    'TOPNAV_COLONIES_MAX'         => get_player_max_colonies($user),
    'NAVBAR_MODE'                 => $GET_mode,

    'TOPNAV_DARK_MATTER'            => mrc_get_level($user, '', RES_DARK_MATTER),
    'TOPNAV_DARK_MATTER_TEXT'       => pretty_number(mrc_get_level($user, '', RES_DARK_MATTER)),
    'TOPNAV_DARK_MATTER_PLAIN'      => mrc_get_level($user, '', RES_DARK_MATTER, false, true),
    'TOPNAV_DARK_MATTER_PLAIN_TEXT' => pretty_number(mrc_get_level($user, '', RES_DARK_MATTER, false, true)),
    'TOPNAV_METAMATTER'             => mrc_get_level($user, '', RES_METAMATTER),
    'TOPNAV_METAMATTER_TEXT'        => pretty_number(mrc_get_level($user, '', RES_METAMATTER)),

    // TODO ГРЯЗНЫЙ ХАК!!!
    'TOPNAV_PAYMENT'                => sn_module_get_active_count('payment') && !defined('SN_GOOGLE'),

    'TOPNAV_MESSAGES_ADMIN'    => $user['msg_admin'],
    'TOPNAV_MESSAGES_PLAYER'   => $user['mnl_joueur'],
    'TOPNAV_MESSAGES_ALLIANCE' => $user['mnl_alliance'],
    'TOPNAV_MESSAGES_ATTACK'   => $user['mnl_attaque'],
    'TOPNAV_MESSAGES_ALL'      => $user['new_message'],

    'TOPNAV_FLEETS_FLYING'      => count($fleet_flying_list[0]),
    'TOPNAV_FLEETS_TOTAL'       => GetMaxFleets($user),
    'TOPNAV_EXPEDITIONS_FLYING' => count($fleet_flying_list[MT_EXPLORE]),
    'TOPNAV_EXPEDITIONS_TOTAL'  => get_player_max_expeditons($user),

    'TOPNAV_QUEST_COMPLETE' => get_quest_amount_complete($user['id']),
    'TOPNAV_QUEST_IN_PROGRESS' => get_quest_amount_in_progress($user['id']),

    'GAME_NEWS_OVERVIEW'       => $config->game_news_overview,
    'GAME_RESEARCH_DISABLED'   => defined('GAME_RESEARCH_DISABLED') && GAME_RESEARCH_DISABLED,
    'GAME_DEFENSE_DISABLED'    => defined('GAME_DEFENSE_DISABLED') && GAME_DEFENSE_DISABLED,
    'GAME_STRUCTURES_DISABLED' => defined('GAME_STRUCTURES_DISABLED') && GAME_STRUCTURES_DISABLED,
    'GAME_HANGAR_DISABLED'     => defined('GAME_HANGAR_DISABLED') && GAME_HANGAR_DISABLED,

    'PLAYER_OPTION_NAVBAR_PLANET_VERTICAL'        => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_PLANET_VERTICAL],
    'PLAYER_OPTION_NAVBAR_PLANET_OLD'             => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_PLANET_OLD],
    'PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE' => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE],
    'PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH'       => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH],
    'PLAYER_OPTION_NAVBAR_DISABLE_PLANET'         => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_PLANET],
    'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR'         => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_HANGAR],
    'PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS'  => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS],
    'PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS'    => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS],
    'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS'         => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_QUESTS],
    'PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER'    => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER],
    'PLAYER_OPTION_NAVBAR_RESEARCH_WIDE'          => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE],

    'TUTORIAL_ENABLED' => $tutorial_enabled,

    'SUBQUE_FLEET' => SUBQUE_FLEET,
    'QUE_RESEARCH' => QUE_RESEARCH,
    'QUE_STRUCTURES' => QUE_STRUCTURES,
  ));

  if((defined('SN_RENDER_NAVBAR_PLANET') && SN_RENDER_NAVBAR_PLANET === true) || ($user['option_list'][OPT_INTERFACE]['opt_int_navbar_resource_force'] && SN_RENDER_NAVBAR_PLANET !== false)) {
    tpl_set_resource_info($template, $planetrow);
    $template->assign_vars(array(
      'SN_RENDER_NAVBAR_PLANET' => true,
      'SN_NAVBAR_HIDE_FLEETS'   => true,
    ));
  }

  return $template;
}

/**
 * @param $template
 * @param $user
 */
function tpl_navbar_render_notes(&$template, &$user) {
  $notes_query = doquery("SELECT * FROM {{notes}} WHERE `owner` = {$user['id']} AND `sticky` = 1 ORDER BY priority DESC, time DESC");
  while ($note_row = db_fetch($notes_query)) {
    note_assign($template, $note_row);
  }
}

/**
 * @param $template
 * @param $user
 * @param $config
 */
function tpl_navbar_render_news(&$template, &$user, $config) {
  if ($config->game_news_overview) {
    $user_last_read_safe = intval($user['news_lastread']);
    $newsSql = "WHERE UNIX_TIMESTAMP(`tsTimeStamp`) >= {$user_last_read_safe}";
    $newsOverviewShowSeconds = intval($config->game_news_overview_show);
    if ($newsOverviewShowSeconds) {
      $newsSql .= " AND `tsTimeStamp` >= DATE_SUB(NOW(), INTERVAL {$newsOverviewShowSeconds} SECOND)";
    }
    nws_render($template, $newsSql, $config->game_news_overview);
  }
}

/**
 * @param array $sn_mvc
 * @param string $blockName
 *
 * @return array|false
 */
function render_button_block(&$sn_mvc, $blockName) {
  $result = false;

  if (!empty($sn_mvc[$blockName]) && is_array($sn_mvc[$blockName])) {
    foreach ($sn_mvc[$blockName] as $navbar_button_image => $navbar_button_url) {
      $result[] = array(
        'IMAGE'        => $navbar_button_image,
        'URL_RELATIVE' => $navbar_button_url,
      );
    }

    $result = array(
      '.' => array(
        $blockName =>
          $result
      ),
    );
  }

  return $result;
}

/**
 * @param array $sn_mvc
 * @param template $template
 */
function tpl_navbar_extra_buttons(&$sn_mvc, $template) {
  ($block = render_button_block($sn_mvc, 'navbar_prefix_button')) ? $template->assign_recursive($block) : false;
  ($block = render_button_block($sn_mvc, 'navbar_main_button')) ? $template->assign_recursive($block) : false;
}

/**
 * @param template|string $template
 */
function templateRenderToHtml($template) {
  $output = null;

  ob_start();
  displayP($template);
  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}


/**
 * @param template|string $template
 */
function displayP($template) {
  if(is_object($template)) {
    if(empty($template->parsed)) {
      parsetemplate($template);
    }

    foreach($template->files as $section => $filename) {
      $template->display($section);
    }
  } else {
    print($template);
  }
}

/**
 * @param template $template
 * @param array|bool      $array
 *
 * @return mixed
 */
function templateObjectParse($template, $array = false) {
  global $user;

  if(!empty($array) && is_array($array)) {
    foreach($array as $key => $data) {
      $template->assign_var($key, $data);
    }
  }

  $template->assign_vars(array(
    'SN_TIME_NOW'    => SN_TIME_NOW,
    'USER_AUTHLEVEL' => isset($user['authlevel']) ? $user['authlevel'] : -1,
    'SN_GOOGLE'      => defined('SN_GOOGLE'),
  ));

  $template->parsed = true;

  return $template;
}

/**
 * @param template|string $template
 * @param array|bool      $array
 *
 * @return mixed
 */
function parsetemplate($template, $array = false) {
  if(is_object($template)) {
    return templateObjectParse($template, $array);
  } else {
    $search[] = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
    $replace[] = '((isset($lang[\'\1\'][\'\2\'])) ? $lang[\'\1\'][\'\2\'] : \'{L_\1[\2]}\');';

    $search[] = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '((isset($lang[\'\1\'])) ? $lang[\'\1\'] : \'{L_\1}\');';

    $search[] = '#\{([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '((isset($array[\'\1\'])) ? $array[\'\1\'] : \'{\1}\');';

    return preg_replace($search, $replace, $template);
  }
}

/**
 * @param array|string  $files
 * @param template|null $template
 * @param string|null   $template_path - path to template
 *
 * @return template
 */
function gettemplate($files, $template = null, $template_path = null) {
  global $sn_mvc, $sn_page_name, $user;

  $template_ex = '.tpl.html';

  is_string($files) ? $files = array(basename($files) => $files) : false;

  !is_object($template) ? $template = new template(SN_ROOT_PHYSICAL) : false;
  //$template->set_custom_template($template_path ? $template_path : TEMPLATE_DIR, TEMPLATE_NAME, TEMPLATE_DIR);

  $templateName = getSkinPathTemplate(classSupernova::$gc->theUser->getSkinPath());
  !$template_path || !is_string($template_path) ? $template_path = SN_ROOT_PHYSICAL . 'design/templates/' : false;
  $template->set_custom_template($template_path . $templateName . '/', $templateName, TEMPLATE_DIR);

  // TODO ГРЯЗНЫЙ ХАК! Это нужно, что бы по возможности перезаписать инфу из языковых пакетов модулей там, где она была перезаписана раньше инфой из основного пакета. Почему?
  //  - сначала грузятся модули и их языковые пакеты
  //  - затем по ходу дела ОСНОВНОЙ языковой пакет может перезаписать данные из МОДУЛЬНОГО языкового пакета
  // Поэтому и нужен этот грязный хак
  // В норме же - страницы заявляют сами, какие им пакеты нужны. Так что сначала всегда должны грузится основные языковые пакеты, а уже ПОВЕРХ них - пакеты модулей
  !empty($sn_mvc['i18n']['']) ? lng_load_i18n($sn_mvc['i18n']['']) : false;
  $sn_page_name ? lng_load_i18n($sn_mvc['i18n'][$sn_page_name]) : false;

  foreach($files as &$filename) {
    $filename = $filename . $template_ex;
  }

  $template->set_filenames($files);

  return $template;
}

/**
 * @param template $template
 */
function tpl_login_lang(&$template) {
  global $language;

  $url_params = array();

  $language ? $url_params[] = "lang={$language}" : false;

  ($id_ref = sys_get_param_id('id_ref')) ? $url_params[] = "id_ref={$id_ref}" : false;

  $template->assign_vars($q = array(
    'LANG'     => $language ? $language : '',
    'referral' => $id_ref ? '&id_ref=' . $id_ref : '',

    'REQUEST_PARAMS' => !empty($url_params) ? '?' . implode('&', $url_params) : '',// "?lang={$language}" . ($id_ref ? "&id_ref={$id_ref}" : ''),
    'FILENAME'       => basename($_SERVER['PHP_SELF']),
  ));

  foreach(lng_get_list() as $lng_id => $lng_data) {
    if(isset($lng_data['LANG_VARIANTS']) && is_array($lng_data['LANG_VARIANTS'])) {
      foreach($lng_data['LANG_VARIANTS'] as $lang_variant) {
        $lng_data1 = $lng_data;
        $lng_data1 = array_merge($lng_data1, $lang_variant);
        $template->assign_block_vars('language', $lng_data1);
      }
    } else {
      $template->assign_block_vars('language', $lng_data);
    }
  }
}

/**
 * @param array $user
 *
 * @return array
 */
function tpl_get_fleets_flying(&$user) {
  $fleet_flying_list = array();

  $fleet_flying_list[0] = fleet_list_by_owner_id($user['id']);
  foreach($fleet_flying_list[0] as $fleet_id => $fleet_flying_row) {
    $fleet_flying_list[$fleet_flying_row['fleet_mission']][$fleet_id] = &$fleet_flying_list[0][$fleet_id];
  }

  return $fleet_flying_list;
}

/**
 * @param template $template
 * @param array    $planet
 * @param int      $que_type
 *
 * @return int
 */
function tpl_assign_hangar(&$template, $planet, $que_type) {
  $que = que_get($planet['id_owner'], $planet['id'], $que_type);
  $que = $que['ques'][$que_type][$planet['id_owner']][$planet['id']];

  $que_length = 0;
  if(!empty($que)) {
    foreach($que as $que_item) {
      $template->assign_block_vars('que', que_tpl_parse_element($que_item));
    }
    $que_length = count($que);
  }

  return $que_length;
}

/**
 * @param template $template
 * @param array    $density_price_chart
 * @param int      $user_dark_matter
 */
function tpl_planet_density_info(&$template, &$density_price_chart, $user_dark_matter) {
  global $lang;

  foreach($density_price_chart as $density_price_index => &$density_price_data) {
    $density_cost = $density_price_data;
    $density_number_style = pretty_number($density_cost, true, $user_dark_matter, false, false);

    $density_price_data = array(
      'COST'            => $density_cost,
      'COST_TEXT'       => $density_number_style['text'],
      'COST_TEXT_CLASS' => $density_number_style['class'],
      'REST'            => $user_dark_matter - $density_cost,
      'ID'              => $density_price_index,
      'TEXT'            => $lang['uni_planet_density_types'][$density_price_index],
    );
    $template->assign_block_vars('densities', $density_price_data);
  }
}

/**
 * @param template $template
 * @param string   $name
 * @param mixed    $values
 */
function tpl_assign_select(&$template, $name, $values) {
  !is_array($values) ? $values = array($values => $values) : false;

  foreach($values as $key => $value) {
    $template->assign_block_vars($name, array(
      'KEY'   => htmlentities($key, ENT_COMPAT, 'UTF-8'),
      'VALUE' => htmlentities($value, ENT_COMPAT, 'UTF-8'),
    ));
  }
}
