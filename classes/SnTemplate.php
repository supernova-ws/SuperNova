<?php

use Common\Traits\TSingleton;
use Core\GlobalContainer;
use Fleet\DbFleetStatic;
use Note\Note;
use \Pages\PageTutorial;
use Planet\DBStaticPlanet;
use Player\playerTimeDiff;
use Template\TemplateMeta;

/**
 * Template manager
 */
class SnTemplate {

  use TSingleton;

  const TPL_HTML = '.tpl.html';
  /**
   * Partial path to templates root
   */
  const SN_TEMPLATES_PARTIAL_PATH = 'design/templates/';
  const SN_TEMPLATE_NAME_DEFAULT = 'OpenGame';
  const P_CONTENT = '__RENDERED_CONTENT';

  /**
   * @param template|string $template
   */
  public static function displayP($template) {
    if (is_object($template)) {
      if (empty($template->parsed)) {
        self::parsetemplate($template);
      }

      foreach ($template->files as $section => $filename) {
        $template->display($section);
      }
    } else {
      print($template);
    }
  }

  /**
   * @param template|string $template
   * @param array|bool      $array
   *
   * @return mixed
   */
  public static function parsetemplate($template, $array = false) {
    if (is_object($template)) {
      return self::templateObjectParse($template, $array);
    } else {
      $search[]  = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
      $replace[] = '((isset($lang[\'\1\'][\'\2\'])) ? $lang[\'\1\'][\'\2\'] : \'{L_\1[\2]}\');';

      $search[]  = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
      $replace[] = '((isset($lang[\'\1\'])) ? $lang[\'\1\'] : \'{L_\1}\');';

      $search[]  = '#\{([a-z0-9\-_]*?)\}#Ssie';
      $replace[] = '((isset($array[\'\1\'])) ? $array[\'\1\'] : \'{\1}\');';

      return preg_replace($search, $replace, $template);
    }
  }

  /**
   * @param template   $template
   * @param array|bool $array
   *
   * @return mixed
   */
  public static function templateObjectParse($template, $array = false) {
    global $user;

    if (!empty($array) && is_array($array)) {
      foreach ($array as $key => $data) {
        $template->assign_var($key, $data);
      }
    }

    $template->assign_vars(array(
      'SN_TIME_NOW'      => SN_TIME_NOW,
      'SN_TEMPLATE_NAME' => SnTemplate::getPlayerTemplateName(),
      'USER_AUTHLEVEL'   => isset($user['authlevel']) ? $user['authlevel'] : -1,
      'SN_GOOGLE'        => defined('SN_GOOGLE'),
    ));

    $template->parsed = true;

    return $template;
  }

  /**
   * @param array $menu
   * @param array $extra
   */
  public static function tpl_menu_merge_extra(&$menu, &$extra) {
    if (!is_array($extra) || !is_array($menu) || empty($menu)) {
      return;
    }

    foreach ($extra as $menu_item_id => $menu_item) {
      if (empty($menu_item['LOCATION'])) {
        $menu[$menu_item_id] = $menu_item;
        continue;
      }

      $item_location = $menu_item['LOCATION'];
      unset($menu_item['LOCATION']);

      $is_positioned = $item_location[0];
      if ($is_positioned == '+' || $is_positioned == '-') {
        $item_location = substr($item_location, 1);
      } else {
        $is_positioned = '';
      }

      if ($item_location) {
        $menu_keys       = array_keys($menu);
        $insert_position = array_search($item_location, $menu_keys);
        if ($insert_position === false) {
          $insert_position = count($menu) - 1;
          $is_positioned   = '+';
          $item_location   = '';
        }
      } else {
        $insert_position = $is_positioned == '-' ? 0 : count($menu);
      }

      $insert_position     += $is_positioned == '+' ? 1 : 0;
      $spliced             = array_splice($menu, $insert_position, count($menu) - $insert_position);
      $menu[$menu_item_id] = $menu_item;

      if (!$is_positioned && $item_location) {
        unset($spliced[$item_location]);
      }
      $menu = array_merge($menu, $spliced);
    }

    $extra = array();
  }

  /**
   * @param array $menu
   *
   * @return array
   */
  public static function tpl_menu_adminize($menu) {
    !is_array($menu) ? $menu = [] : false;

    foreach ($menu as &$menuItem) {
      if (!isset($menuItem[MENU_FIELD_AUTH_LEVEL])) {
        $menuItem[MENU_FIELD_AUTH_LEVEL] = AUTH_LEVEL_ADMINISTRATOR;
      }
    }

    return $menu;
  }

  /**
   * @param array    $sn_menu
   * @param template $template
   */
  public static function tpl_menu_assign_to_template(&$sn_menu, &$template) {
    global $lang;

    if (empty($sn_menu) || !is_array($sn_menu)) {
      return;
    }

    foreach ($sn_menu as $menu_item_id => $menu_item) {
      if (!$menu_item) {
        continue;
      }

      if (is_string($menu_item_id)) {
        $menu_item['ID'] = $menu_item_id;
      }

      if ($menu_item['TYPE'] == 'lang') {
        $lang_string = &$lang;
        if (preg_match('#(\w+)(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?(?:\[(\w+)\])?#', $menu_item['ITEM'], $matches) && count($matches) > 1) {
          for ($i = 1; $i < count($matches); $i++) {
            if (defined($matches[$i])) {
              $matches[$i] = constant($matches[$i]);
            }
            $lang_string = &$lang_string[$matches[$i]];
          }
        }
        $menu_item['ITEM'] = $lang_string && is_string($lang_string) ? $lang_string : "{L_{$menu_item['ITEM']}}";
      }

      $menu_item['ALT']   = htmlentities($menu_item['ALT']);
      $menu_item['TITLE'] = htmlentities($menu_item['TITLE']);

      if (!empty($menu_item['ICON'])) {
        if (is_string($menu_item['ICON'])) {
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
  public static function tpl_render_menu($template) {
    global $user, $lang, $template_result, $sn_menu_admin_extra, $sn_menu_admin, $sn_menu, $sn_menu_extra;

    lng_include('admin');

    $template->assign_vars(array(
      'USER_AUTHLEVEL'      => $user['authlevel'],
      'USER_AUTHLEVEL_NAME' => $lang['user_level'][$user['authlevel']],
      'PAYMENT'             => SN::$gc->modules->countModulesInGroup('payment'),
      'MENU_START_HIDE'     => !empty($_COOKIE[SN_COOKIE . '_menu_hidden']) || defined('SN_GOOGLE'),
    ));

    if (isset($template_result['MENU_CUSTOMIZE'])) {
      $template->assign_vars(array(
        'PLAYER_OPTION_MENU_SHOW_ON_BUTTON'   => SN::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
        'PLAYER_OPTION_MENU_HIDE_ON_BUTTON'   => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
        'PLAYER_OPTION_MENU_HIDE_ON_LEAVE'    => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
        'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE'   => SN::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
        'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS' => SN::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
        'PLAYER_OPTION_MENU_WHITE_TEXT'       => SN::$user_options[PLAYER_OPTION_MENU_WHITE_TEXT],
        'PLAYER_OPTION_MENU_OLD'              => SN::$user_options[PLAYER_OPTION_MENU_OLD],
        'PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON' => empty($_COOKIE[SN_COOKIE . '_menu_hidden']) && !defined('SN_GOOGLE')
          ? SN::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON] : 1,
      ));
    }

    if (defined('IN_ADMIN') && IN_ADMIN === true && !empty($user['authlevel']) && $user['authlevel'] > 0) {
      SnTemplate::tpl_menu_merge_extra($sn_menu_admin, $sn_menu_admin_extra);
      $sn_menu_admin = SnTemplate::tpl_menu_adminize($sn_menu_admin);
      SnTemplate::tpl_menu_assign_to_template($sn_menu_admin, $template);
    } else {
      SnTemplate::tpl_menu_merge_extra($sn_menu, $sn_menu_extra);
      SnTemplate::tpl_menu_assign_to_template($sn_menu, $template);
    }

    return $template;
  }

  /**
   * @param template[] $page
   * @param array      $template_result
   */
  public static function renderFooter($page, $template_result) {
    $templateFooter = self::gettemplate('_page/_99_footer', true);

    $templateFooter->assign_vars([
      'SN_TIME_NOW'      => SN_TIME_NOW,
      'SN_VERSION'       => SN_VERSION,
      'ADMIN_EMAIL'      => SN::$config->game_adminEmail,
      'CURRENT_YEAR'     => date('Y', SN_TIME_NOW),
      'DB_PATCH_VERSION' => dbPatchGetCurrent(),
    ]);

    SnTemplate::displayP($templateFooter);
  }

  /**
   * @param $page
   * @param $title
   * @param $template_result
   * @param $inLoginLogout
   * @param $user
   * @param $config
   * @param $lang
   * @param $planetrow
   */
  public static function renderHeader($page, $title, &$template_result, $inLoginLogout, &$user, $config, $lang, $planetrow, $renderedContent) {
    if (SN::$headerRendered) {
      return;
    }

    ob_end_flush();

    ob_start();
//  pdump(microtime(true) - SN_TIME_MICRO, 'Header render started');
    $isDisplayTopNav = true;
    $isDisplayMenu   = true;

    isset($template_result['GLOBAL_DISPLAY_MENU']) ? $isDisplayMenu = $template_result['GLOBAL_DISPLAY_MENU'] : false;
    isset($template_result['GLOBAL_DISPLAY_NAVBAR']) ? $isDisplayTopNav = $template_result['GLOBAL_DISPLAY_NAVBAR'] : false;

    // TODO: DEPRECATED! Use $template_result to turn menu and navbar or ond off!
    if (is_object($page)) {
      isset($page->_rootref['MENU']) ? $isDisplayMenu = $page->_rootref['MENU'] : false;
      isset($page->_rootref['NAVBAR']) ? $isDisplayTopNav = $page->_rootref['NAVBAR'] : false;
    }

    $inAdmin         = defined('IN_ADMIN') && IN_ADMIN === true;
    $isDisplayMenu   = ($isDisplayMenu || $inAdmin) && !isset($_COOKIE['menu_disable']);
    $isDisplayTopNav = $isDisplayTopNav && !$inAdmin;

    if ($inLoginLogout || empty($user['id']) || !is_numeric($user['id'])) {
      $isDisplayMenu   = false;
      $isDisplayTopNav = false;
    }

    $template = self::gettemplate('_page/_00_header', true);
    $template->assign_vars([
      'SN_TIME_NOW'      => SN_TIME_NOW,
      'SN_VERSION'       => SN_VERSION,
      'ADMIN_EMAIL'      => SN::$config->game_adminEmail,
      'CURRENT_YEAR'     => date('Y', SN_TIME_NOW),
      'DB_PATCH_VERSION' => dbPatchGetCurrent(),
    ]);

    self::renderJavaScript();

    self::renderCss($inLoginLogout);

    $template->assign_vars(array(
      self::P_CONTENT => $renderedContent,

      'LANG_LANGUAGE'  => $lang['LANG_INFO']['LANG_NAME_ISO2'],
      'LANG_ENCODING'  => 'utf-8',
      'LANG_DIRECTION' => $lang['LANG_INFO']['LANG_DIRECTION'],

      'SN_ROOT_VIRTUAL' => SN_ROOT_VIRTUAL,

      'ADV_SEO_META_DESCRIPTION' => $config->adv_seo_meta_description,
      'ADV_SEO_META_KEYWORDS'    => $config->adv_seo_meta_keywords,

      // WARNING! This can be set by page!
      // CHANGE CODE TO MAKE IT IMPOSSIBLE!
      'GLOBAL_META_TAGS'         => isset($page->_rootref['GLOBAL_META_TAGS']) ? $page->_rootref['GLOBAL_META_TAGS'] : '',
    ));

    $template->assign_vars(array(
      'GLOBAL_DISPLAY_MENU'   => $isDisplayMenu,
      'GLOBAL_DISPLAY_NAVBAR' => $isDisplayTopNav,

      'USER_AUTHLEVEL' => intval($user['authlevel']),

      'FONT_SIZE'                        => self::playerFontSize(),
      'FONT_SIZE_PERCENT_DEFAULT_STRING' => FONT_SIZE_PERCENT_DEFAULT_STRING,

      'SN_TIME_NOW'          => SN_TIME_NOW,
      'LOGIN_LOGOUT'         => $template_result['LOGIN_LOGOUT'],
      'GAME_MODE_CSS_PREFIX' => $config->game_mode == GAME_BLITZ ? 'blitz_' : '',
      'TIME_DIFF_MEASURE'    => playerTimeDiff::timeDiffTemplate(), // Проводить замер только если не выставлен флаг форсированного замера И (иссяк интервал замера ИЛИ замера еще не было)

      'title'              => ($title ? "{$title} - " : '') . "{$lang['sys_server']} {$config->game_name} - {$lang['sys_supernova']}",
      'ADV_SEO_JAVASCRIPT' => $config->adv_seo_javascript,

      'SOUND_ENABLED'                        => SN::$user_options[PLAYER_OPTION_SOUND_ENABLED],
      'PLAYER_OPTION_ANIMATION_DISABLED'     => SN::$user_options[PLAYER_OPTION_ANIMATION_DISABLED],
      'PLAYER_OPTION_PROGRESS_BARS_DISABLED' => SN::$user_options[PLAYER_OPTION_PROGRESS_BARS_DISABLED],

      'IMPERSONATING'                        => !empty($template_result[F_IMPERSONATE_STATUS]) ? sprintf($lang['sys_impersonated_as'], $user['username'], $template_result[F_IMPERSONATE_OPERATOR]) : '',
      'PLAYER_OPTION_DESIGN_DISABLE_BORDERS' => SN::$user_options[PLAYER_OPTION_DESIGN_DISABLE_BORDERS],

      'WEBP_SUPPORT_NEED_CHECK' => ($webpSupported = SN::$gc->theUser->isWebpSupported()) === null ? 1 : 0,
      'WEBP_SUPPORTED'          => $webpSupported ? 1 : 0,
    ));
    $template->assign_recursive($template_result);

    if ($isDisplayMenu) {
      SnTemplate::tpl_render_menu($template);
    }

    if ($isDisplayTopNav) {
      SN::$gc->pimp->tpl_render_topnav($user, $planetrow, $template);
    }

    SnTemplate::displayP($template);
    ob_end_flush();

    SN::$headerRendered = true;

    ob_start();
  }

  /**
   * @param $is_login
   */
  public static function renderCss($is_login) {
    global $sn_mvc, $sn_page_name, $template_result;

    empty($sn_mvc['css']) ? $sn_mvc['css'] = ['' => []] : false;

    $standard_css = [];
    $standard_css = self::cssAddFileName('design/css/jquery-ui', $standard_css);
    $standard_css = self::cssAddFileName('design/css/global', $standard_css);
    $is_login ? $standard_css = self::cssAddFileName('design/css/login', $standard_css) : false;

    $standard_css = self::getCurrentTemplate()->cssAddFileName('_template', $standard_css);

    $standard_css = self::cssAddFileName(SN::$gc->theUser->getSkinPath() . 'skin', $standard_css);
    $standard_css = self::cssAddFileName('design/css/global_override', $standard_css);

    // Prepending standard CSS files
    $sn_mvc['css'][''] = array_merge($standard_css, $sn_mvc['css']['']);

    self::renderFileListInclude($template_result, $sn_mvc, $sn_page_name, 'css');
  }

  /**
   */
  public static function renderJavaScript() {
    global $sn_mvc, $sn_page_name, $template_result;

    self::renderFileListInclude($template_result, $sn_mvc, $sn_page_name, 'javascript');
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
  public static function tpl_topnav_event_build_helper($time, $event, $msg, $prefix, $is_decrease, $fleet_flying_row, &$fleet_flying_sorter, &$fleet_flying_events, &$fleet_event_count) {
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
  public static function tpl_topnav_event_build(&$template, $fleet_flying_list, $type = 'fleet') {
    if (empty($fleet_flying_list)) {
      return;
    }

    global $lang;

    $fleet_event_count   = 0;
    $fleet_flying_sorter = array();
    $fleet_flying_events = array();
    foreach ($fleet_flying_list as &$fleet_flying_row) {
      $will_return = true;
      if ($fleet_flying_row['fleet_mess'] == 0) {
        // cut fleets on Hold and Expedition
        if ($fleet_flying_row['fleet_start_time'] >= SN_TIME_NOW) {
          $fleet_flying_row['fleet_mission'] == MT_RELOCATE ? $will_return = false : false;
          SnTemplate::tpl_topnav_event_build_helper($fleet_flying_row['fleet_start_time'], EVENT_FLEET_ARRIVE, $lang['sys_event_arrive'], 'fleet_end_', !$will_return, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
        }
        if ($fleet_flying_row['fleet_end_stay']) {
          SnTemplate::tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_stay'], EVENT_FLEET_STAY, $lang['sys_event_stay'], 'fleet_end_', false, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
        }
      }
      if ($will_return) {
        SnTemplate::tpl_topnav_event_build_helper($fleet_flying_row['fleet_end_time'], EVENT_FLEET_RETURN, $lang['sys_event_return'], 'fleet_start_', true, $fleet_flying_row, $fleet_flying_sorter, $fleet_flying_events, $fleet_event_count);
      }
    }

    asort($fleet_flying_sorter);

    $fleet_flying_count = count($fleet_flying_list);
    foreach ($fleet_flying_sorter as $fleet_event_id => $fleet_time) {
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
   * @return mixed|string
   */
  public static function playerFontSize() {
    $font_size = !empty($_COOKIE[SN_COOKIE_F]) ? $_COOKIE[SN_COOKIE_F] : SN::$user_options[PLAYER_OPTION_BASE_FONT_SIZE];
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
   * Checks if minified/full-size CSS file exists - and adds it if any
   *
   * @param string $cssFileName
   * @param array  $cssArray
   *
   * @return array
   */
  public static function cssAddFileName($cssFileName, $cssArray) {
    if (file_exists(SN_ROOT_PHYSICAL . $cssFileName . '.min.css')) {
      $cssArray[$cssFileName . '.min.css'] = '';
    } elseif (file_exists(SN_ROOT_PHYSICAL . $cssFileName . '.css')) {
      $cssArray[$cssFileName . '.css'] = '';
    }

    return $cssArray;
  }

  /**
   * @param array   $template_result
   * @param array[] $sn_mvc
   * @param string  $sn_page_name
   * @param string  $fileType - 'css' or 'javascript'
   */
  public static function renderFileListInclude(&$template_result, &$sn_mvc, $sn_page_name, $fileType) {
    if (empty($sn_mvc[$fileType])) {
      return;
    }

    foreach ($sn_mvc[$fileType] as $page_name => $script_list) {
      if (empty($page_name) || $page_name == $sn_page_name) {
        foreach ($script_list as $filename => $content) {
          $template_result['.'][$fileType][] = array(
            'FILE'    => $filename,
            'CONTENT' => $content,
          );
        }
      }
    }
  }

  /**
   * @param $template
   * @param $user
   */
  public static function tpl_navbar_render_notes(&$template, &$user) {
    $notes_query = doquery("SELECT * FROM {{notes}} WHERE `owner` = {$user['id']} AND `sticky` = 1 ORDER BY priority DESC, time DESC");
    while ($note_row = db_fetch($notes_query)) {
      Note::note_assign($template, $note_row);
    }
  }

  /**
   * @param $template
   * @param $user
   * @param $config
   */
  public static function tpl_navbar_render_news(&$template, &$user, $config) {
    if ($config->game_news_overview) {
      $user_last_read_safe     = intval($user['news_lastread']);
      $newsSql                 = "AND UNIX_TIMESTAMP(`tsTimeStamp`) >= {$user_last_read_safe} ";
      $newsOverviewShowSeconds = intval($config->game_news_overview_show);
      if ($newsOverviewShowSeconds) {
        $newsSql .= "AND `tsTimeStamp` >= DATE_SUB(NOW(), INTERVAL {$newsOverviewShowSeconds} SECOND) ";
      }
      nws_render($user, $template, $newsSql, $config->game_news_overview);
    }
  }

  /**
   * @param array  $sn_mvc
   * @param string $blockName
   *
   * @return array|false
   */
  public static function render_button_block(&$sn_mvc, $blockName) {
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
   * @param array    $sn_mvc
   * @param template $template
   */
  public static function tpl_navbar_extra_buttons(&$sn_mvc, $template) {
    ($block = SnTemplate::render_button_block($sn_mvc, 'navbar_prefix_button')) ? $template->assign_recursive($block) : false;
    ($block = SnTemplate::render_button_block($sn_mvc, 'navbar_main_button')) ? $template->assign_recursive($block) : false;
  }

  /**
   * @param template|string $template
   *
   * @return false|string|null
   */
  public static function templateRenderToHtml($template) {
    $output = null;

    ob_start();
    SnTemplate::displayP($template);
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

  /**
   * @param template $template
   */
  public static function tpl_login_lang(&$template) {
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

    foreach (lng_get_list() as $lng_id => $lng_data) {
      if (isset($lng_data['LANG_VARIANTS']) && is_array($lng_data['LANG_VARIANTS'])) {
        foreach ($lng_data['LANG_VARIANTS'] as $lang_variant) {
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
   * @param template $template
   * @param string   $blockName
   * @param mixed    $values
   * @param string   $keyName   - Name for key name
   * @param string   $valueName - Name for value name
   */
  public static function tpl_assign_select(&$template, $blockName, $values, $keyName = 'KEY', $valueName = 'VALUE') {
    !is_array($values) ? $values = array($values => $values) : false;

    foreach ($values as $key => $value) {
      $template->assign_block_vars($blockName, array(
        $keyName   => HelperString::htmlSafe($key),
        $valueName => HelperString::htmlSafe($value),
      ));
    }
  }

  /**
   * Renders unit bonus from unit data
   *
   * @param array $unitInfo
   *
   * @return string
   */
  public static function tpl_render_unit_bonus_data($unitInfo) {
    $strBonus = self::tplAddPlus($unitInfo[P_BONUS_VALUE]);
    switch ($unitInfo[P_BONUS_TYPE]) {
      case BONUS_PERCENT:
        $strBonus = "{$strBonus}% ";
      break;

      case BONUS_ABILITY:
        $strBonus = '';
      break;

      case BONUS_ADD:
      default:
      break;
    }

    return $strBonus;
  }

  /**
   * Converts number to string then adds "+" sign for positive AND ZERO numbers
   *
   * @param float $value
   *
   * @return string
   */
  public static function tplAddPlus($value) {
    return ($value >= 0 ? '+' : '') . $value;
  }

  /**
   * Convert number to prettified string then adds "+" sign for positive AND ZERO numbers
   *
   * @param float $value
   *
   * @return string
   */
  public static function tplPrettyPlus($value) {
    return ($value >= 0 ? '+' : '') . HelperString::numberFloorAndFormat($value);
  }

  /**
   * Add message to result box
   *
   * If $template specified - message would be added to template supplied. Otherwise - to $template_result
   *
   * @param string $message
   * @param int    $status
   * @param null   $template
   */
  public static function tplAddResult($message, $status = ERR_NONE, $template = null) {
    global $template_result;

    $block = [
      'STATUS'  => $status,
      'MESSAGE' => $message,
    ];

    if ($template instanceof template) {
      $template->assign_block_vars('result', $block);
    } else {
      $template_result['.']['result'][] = $block;
    }
  }

  /**
   * @param string    $message
   * @param string    $title
   * @param string    $redirectTo
   * @param int       $timeout
   * @param bool|true $showHeader
   */
  public static function messageBox($message, $title = '', $redirectTo = '', $timeout = 5, $showHeader = true) {
    global $lang, $template_result;

    if (empty($title)) {
      $title = $lang['sys_error'];
    }

    $template = self::gettemplate('message_body', true);

    $template_result['GLOBAL_DISPLAY_NAVBAR'] = $showHeader;

    $template->assign_vars(array(
//    'GLOBAL_DISPLAY_NAVBAR' => $showHeader,

      'TITLE'       => $title,
      'MESSAGE'     => $message,
      'REDIRECT_TO' => $redirectTo,
      'TIMEOUT'     => $timeout,
    ));

    self::display($template, $title);
  }

  /**
   * Admin message box
   *
   * @param        $message
   * @param string $title
   * @param string $redirectTo
   * @param int    $timeout
   *
   * @see SnTemplate::messageBox()
   */
  public static function messageBoxAdmin($message, $title = '', $redirectTo = '', $timeout = 5) {
    SnTemplate::messageBox($message, $title, $redirectTo, $timeout, false);
  }

  public static function messageBoxAdminAccessDenied($level = AUTH_LEVEL_ADMINISTRATOR) {
    global $user, $lang;

    if ($user['authlevel'] < $level) {
      SnTemplate::messageBoxAdmin($lang['adm_err_denied'], $lang['admin_title_access_denied'], SN_ROOT_VIRTUAL . 'overview.php');
    }
  }

  /**
   * @param          $prevUser
   * @param array    $user
   * @param array    $planetrow
   * @param template $template
   *
   * @return array
   */
  public static function sn_tpl_render_topnav(&$prevUser, $user, $planetrow, $template) {
    global $lang, $config, $sn_mvc;

    // This call was not first one... Using results from previous call
    if (!empty($prevUser['username'])) {
      $user = $prevUser;
    }

    if (!is_array($user)) {
      return $user;
    }

    $GET_mode = sys_get_param_str('mode');

    $ThisUsersPlanets = DBStaticPlanet::db_planet_list_sorted($user);
    foreach ($ThisUsersPlanets as $CurPlanet) {
      if ($CurPlanet['destruyed']) {
        continue;
      }

      $fleet_listx = flt_get_fleets_to_planet($CurPlanet);
      if($CurPlanet['planet_type'] == PT_MOON) {
        $parentPlanet = DBStaticPlanet::db_planet_by_id($CurPlanet['parent_planet']);
      } else {
        $parentPlanet = $CurPlanet;
      }

      $template->assign_block_vars('topnav_planets', [
        'ID'          => $CurPlanet['id'],
        'NAME'        => $CurPlanet['name'],
        'TYPE'        => $CurPlanet['planet_type'],
        'TYPE_TEXT'   => $lang['sys_planet_type_sh'][$CurPlanet['planet_type']],
        'IS_CAPITAL'  => $parentPlanet['id'] == $user['id_planet'],
        'IS_MOON'     => $CurPlanet['planet_type'] == PT_MOON,
        'PLIMAGE'     => $CurPlanet['image'],
        'FLEET_ENEMY' => $fleet_listx['enemy']['count'],
        'COORDS'      => uni_render_coordinates($CurPlanet),
        'SELECTED'    => $CurPlanet['id'] == $user['current_planet'] ? ' selected' : '',
      ]);
    }

    $fleet_flying_list = DbFleetStatic::tpl_get_fleets_flying($user);
    SnTemplate::tpl_topnav_event_build($template, $fleet_flying_list[0]);
    SnTemplate::tpl_topnav_event_build($template, $fleet_flying_list[MT_EXPLORE], 'expedition');

    que_tpl_parse($template, QUE_STRUCTURES, $user, $planetrow, null, true);
    que_tpl_parse($template, QUE_RESEARCH, $user, array(), null, !SN::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE]);
    que_tpl_parse($template, SUBQUE_FLEET, $user, $planetrow, null, true);
    que_tpl_parse($template, SUBQUE_DEFENSE, $user, $planetrow, null, true);

    SnTemplate::tpl_navbar_extra_buttons($sn_mvc, $template);
    SnTemplate::tpl_navbar_render_news($template, $user, $config);
    SnTemplate::tpl_navbar_render_notes($template, $user);
    $tutorial_enabled = PageTutorial::renderNavBar($template);


    $premium_lvl = mrc_get_level($user, false, UNIT_PREMIUM, true, true);

    $str_date_format   = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
    $time_now_parsed   = getdate(SN_TIME_NOW);
    $time_local_parsed = getdate(defined('SN_CLIENT_TIME_LOCAL') ? SN_CLIENT_TIME_LOCAL : SN_TIME_NOW);

    $template->assign_vars(array(
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
      'TOPNAV_DARK_MATTER_TEXT'       => HelperString::numberFloorAndFormat(mrc_get_level($user, '', RES_DARK_MATTER)),
      'TOPNAV_DARK_MATTER_PLAIN'      => mrc_get_level($user, '', RES_DARK_MATTER, false, true),
      'TOPNAV_DARK_MATTER_PLAIN_TEXT' => HelperString::numberFloorAndFormat(mrc_get_level($user, '', RES_DARK_MATTER, false, true)),
      'TOPNAV_METAMATTER'             => mrc_get_level($user, '', RES_METAMATTER),
      'TOPNAV_METAMATTER_TEXT'        => HelperString::numberFloorAndFormat(mrc_get_level($user, '', RES_METAMATTER)),

      // TODO ГРЯЗНЫЙ ХАК!!!
      'TOPNAV_PAYMENT'                => SN::$gc->modules->countModulesInGroup('payment') && !defined('SN_GOOGLE'),

      'TOPNAV_MESSAGES_ADMIN'    => $user['msg_admin'],
      'TOPNAV_MESSAGES_PLAYER'   => $user['mnl_joueur'],
      'TOPNAV_MESSAGES_ALLIANCE' => $user['mnl_alliance'],
      'TOPNAV_MESSAGES_ATTACK'   => $user['mnl_attaque'],
      'TOPNAV_MESSAGES_ALL'      => $user['new_message'],

      'TOPNAV_FLEETS_FLYING'      => count($fleet_flying_list[0]),
      'TOPNAV_FLEETS_TOTAL'       => GetMaxFleets($user),
      'TOPNAV_EXPEDITIONS_FLYING' => count($fleet_flying_list[MT_EXPLORE]),
      'TOPNAV_EXPEDITIONS_TOTAL'  => get_player_max_expeditons($user),

      'TOPNAV_QUEST_COMPLETE'    => get_quest_amount_complete($user['id']),
      'TOPNAV_QUEST_IN_PROGRESS' => get_quest_amount_in_progress($user['id']),

      'GAME_NEWS_OVERVIEW'       => $config->game_news_overview,
      'GAME_RESEARCH_DISABLED'   => defined('GAME_RESEARCH_DISABLED') && GAME_RESEARCH_DISABLED,
      'GAME_DEFENSE_DISABLED'    => defined('GAME_DEFENSE_DISABLED') && GAME_DEFENSE_DISABLED,
      'GAME_STRUCTURES_DISABLED' => defined('GAME_STRUCTURES_DISABLED') && GAME_STRUCTURES_DISABLED,
      'GAME_HANGAR_DISABLED'     => defined('GAME_HANGAR_DISABLED') && GAME_HANGAR_DISABLED,

      'PLAYER_OPTION_NAVBAR_PLANET_VERTICAL'        => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_VERTICAL],
      'PLAYER_OPTION_NAVBAR_PLANET_OLD'             => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_OLD],
      'PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE' => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE],
      'PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH'       => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH],
      'PLAYER_OPTION_NAVBAR_DISABLE_PLANET'         => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_PLANET],
      'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR'         => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_HANGAR],
      'PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE'        => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE],
      'PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS'  => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS],
      'PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS'    => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS],
      'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS'         => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_QUESTS],
      'PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER'    => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER],
      'PLAYER_OPTION_NAVBAR_RESEARCH_WIDE'          => SN::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE],

      'TUTORIAL_ENABLED' => $tutorial_enabled,

      'PT_MOON'        => PT_MOON,
      'SUBQUE_FLEET'   => SUBQUE_FLEET,
      'SUBQUE_DEFENSE' => SUBQUE_DEFENSE,
      'QUE_RESEARCH'   => QUE_RESEARCH,
      'QUE_STRUCTURES' => QUE_STRUCTURES,
    ));

    if ((defined('SN_RENDER_NAVBAR_PLANET') && SN_RENDER_NAVBAR_PLANET === true) || ($user['option_list'][OPT_INTERFACE]['opt_int_navbar_resource_force'] && SN_RENDER_NAVBAR_PLANET !== false)) {
      tpl_set_resource_info($template, $planetrow);
      $template->assign_vars(array(
        'SN_RENDER_NAVBAR_PLANET' => true,
        'SN_NAVBAR_HIDE_FLEETS'   => true,
      ));
    }

    return $user;
  }

  /**
   * @param array|string  $files
   * @param template|null $template
   * @param string|null   $template_path - path to templates root
   *
   * @return template
   */
  public static function gettemplate($files, $template = null, $template_path = null) {
    global $sn_mvc, $sn_page_name;

    $template = self::getCurrentTemplate()->getTemplate($template, $template_path);

    // TODO ГРЯЗНЫЙ ХАК! Это нужно, что бы по возможности перезаписать инфу из языковых пакетов модулей там, где она была перезаписана раньше инфой из основного пакета. Почему?
    //  - сначала грузятся модули и их языковые пакеты
    //  - затем по ходу дела ОСНОВНОЙ языковой пакет может перезаписать данные из МОДУЛЬНОГО языкового пакета
    // Поэтому и нужен этот грязный хак
    // В норме же - страницы заявляют сами, какие им пакеты нужны. Так что сначала всегда должны грузится основные языковые пакеты, а уже ПОВЕРХ них - пакеты модулей
    !empty($sn_mvc['i18n']['']) ? lng_load_i18n($sn_mvc['i18n']['']) : false;
    $sn_page_name ? lng_load_i18n($sn_mvc['i18n'][$sn_page_name]) : false;

    if (empty($files)) {
      // Make sure that all empty files will translate to empty array
      $files = [];
    } elseif (is_string($files)) {
      // If we have single filename - making array from it
      $files = [basename($files) => $files];
    } elseif (!is_array($files)) {
      // And final touch - all other non-string and non-array inputs converted to empty array
      $files = [];
    }

    foreach ($files as &$filename) {
      $filename = $filename . self::TPL_HTML;
    }

    $template->set_filenames($files);

    return $template;
  }

  /**
   * @param template|string $page
   * @param string          $title
   *
   * @return mixed
   */
  public static function display($page, $title = '') {
    SN::$gSomethingWasRendered = true;

    if (!defined('SN_TIME_RENDER_START')) {
      define('SN_TIME_RENDER_START', microtime(true));
    }

//  return sn_function_call('display', array($page, $title));
//}
//
///**
// * @param template|string $page
// * @param string          $title
// */
//function sn_display($page, $title = '') {
    global $debug, $user, $planetrow, $config, $lang, $template_result, $sn_mvc;

    !empty($sn_mvc['view']['']) and execute_hooks($sn_mvc['view'][''], $page, 'view', '');

    $exitStatus                      = true;
    $template_result['LOGIN_LOGOUT'] = $inLoginLogout = defined('LOGIN_LOGOUT') && LOGIN_LOGOUT === true;

    if (is_object($page)) {
      isset($page->_rootref['PAGE_TITLE']) && empty($title) ? $title = $page->_rootref['PAGE_TITLE'] : false;
      !$title && !empty($page->_rootref['PAGE_HEADER']) ? $title = $page->_rootref['PAGE_HEADER'] : false;
      !isset($page->_rootref['PAGE_HEADER']) && $title ? $page->assign_var('PAGE_HEADER', $title) : false;
    }

    $isRenderGlobal = is_object($page) && isset($template_result['GLOBAL_DISPLAY_HEADER']) ? $template_result['GLOBAL_DISPLAY_HEADER'] : true;

    if (self::getCurrentTemplate()->isRenderWhole()) {
      ob_start();
    } else {
      // Global header
      if ($isRenderGlobal) {
        SnTemplate::renderHeader($page, $title, $template_result, $inLoginLogout, $user, $config, $lang, $planetrow, null);
      }
    }

    // Page content
    !is_array($page) ? $page = array($page) : false;
    $result_added = false;
    foreach ($page as $page_item) {
      /**
       * @var template $page_item
       */
      if (
        !$result_added
        && is_object($page_item)
        && (
          isset($page_item->_tpldata['result'])
          ||
          !empty($template_result['.']['result'])
        )
      ) {
        $resultTemplate = SnTemplate::gettemplate('_result_message');

        $resultTemplate->_tpldata = $page_item->_tpldata;

        // Checking that no duplicates would be merged from template_result to template itself
        $filtered = [];
        if (!empty($template_result['.']['result']) && is_array($template_result['.']['result'])) {
          foreach ($template_result['.']['result'] as $message) {
            if (empty($message['MESSAGE'])) {
              continue;
            }

            foreach ($resultTemplate->_tpldata['result'] as $tplData) {
              if (empty($tplData['MESSAGE'])) {
                continue;
              }

              if ($tplData['MESSAGE'] == $message['MESSAGE']) {
                continue 2;
              }
            }

            $filtered['.']['result'][] = $message;
          }
        }
        $resultTemplate->assign_recursive($filtered);

        SnTemplate::displayP($resultTemplate);
        $result_added = true;
      }

      SnTemplate::displayP($page_item);
    }

    if (is_array($template_result[TEMPLATE_EXTRA_ARRAY]) && !empty($template_result[TEMPLATE_EXTRA_ARRAY])) {
      foreach ($template_result[TEMPLATE_EXTRA_ARRAY] as $extraName => $extraTemplate) {
        /**
         * @var template $extraTemplate
         */
        SnTemplate::displayP($extraTemplate);
      }
    }

    if (
      is_object($page[0])
      &&
      (
        // Checking if hiding page hint flag is present
        empty($template_result['PAGE_HINT_HIDE'])
        &&
        empty($page[0]->_rootref['PAGE_HINT_HIDE'])
      )
      &&
      (
        isset($page[0]->_tpldata['page_hint'])
        ||
        isset($page[0]->_rootref['PAGE_HINT'])
        ||
        !empty($template_result['.']['page_hint'])
        ||
        !empty($template_result['PAGE_HINT'])
      )
    ) {
      $resultTemplate = self::gettemplate('page_hint');

      $resultTemplate->_tpldata = &$page[0]->_tpldata;
      $resultTemplate->_rootref = &$page[0]->_rootref;
      $resultTemplate->assign_recursive($template_result);

      SnTemplate::displayP($resultTemplate);
    }

    if (self::getCurrentTemplate()->isRenderWhole()) {
      $renderedContent = ob_get_clean();
      // Global header
      if ($isRenderGlobal) {
        SnTemplate::renderHeader($page, $title, $template_result, $inLoginLogout, $user, $config, $lang, $planetrow, $renderedContent);
      } else {
        echo $renderedContent;
      }
    }

    // Flushing all opened buffers
    while (@ob_end_flush()) {
      ;
    }


    // Global footer
    if ($isRenderGlobal) {
      SnTemplate::renderFooter($page, $template_result);
    }

    $user['authlevel'] >= 3 && $config->debug ? $debug->echo_log() : false;;

    sn_db_disconnect();

    $exitStatus and die($exitStatus === true ? 0 : $exitStatus);

    return $exitStatus;
  }

  /**
   * @var GlobalContainer|null $gc
   */
  protected $gc = null;

  /**
   * @var TemplateMeta[] $templates
   */
  protected $templates = [];

  public function __construct($gc = null) {
    $this->gc = empty($gc) ? SN::$gc : $gc;
  }

  public function registerTemplate($templateName) {
    if (empty($this->templates[$templateName])) {
      $this->templates[$templateName] = new TemplateMeta($this, $templateName);
    }

    return $this->templates[$templateName];
  }


  /**
   * Дефолтное имя темплейта на сервере
   *
   * @return string
   */
  public static function getServerDefaultTemplateName() {
    return SN::$config->game_default_template ? SN::$config->game_default_template : self::SN_TEMPLATE_NAME_DEFAULT;
  }

  /**
   * Имя темплейта у игрока
   *
   * @return string
   */
  public static function getPlayerTemplateName() {
    return SN::$gc->theUser->getTemplateName();
  }

  /**
   * Относительный путь к сконфигурированному темплейту
   * 'design/templates/(имя_темплейта)
   *
   * @return string
   * @deprecated
   */
  public static function pathRelativeToCurrentTemplate() {
    return self::SN_TEMPLATES_PARTIAL_PATH . self::getPlayerTemplateName();
  }

  /**
   * @return TemplateMeta
   */
  protected static function getCurrentTemplate() {
    $templateName = SnTemplate::getPlayerTemplateName();
    $tMeta        = static::me()->registerTemplate($templateName);

    return $tMeta;
  }

}
