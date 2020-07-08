<?php

use DBAL\DbQuery;
use Fleet\DbFleetStatic;
use Old\Avatar;
use Planet\DBStaticPlanet;
use Player\playerTimeDiff;

/**
 * options.php
 *
 * @copyright (c) 2010-2017 by Gorlum for http://supernova.ws
 */

function sn_options_model() {
  global $user, $template_result;

  $language_new = sys_get_param_str('langer', $user['lang']);
  if ($language_new != $user['lang']) {
    SN::$lang->lng_switch($language_new);
  }

  lng_include('options');
  lng_include('messages');

  sys_user_options_unpack($user);

  $savedOk = false;
//  if (sys_get_param_str('mode') == 'change') {
  if (sys_get_param_str('save_settings')) {
    if (!is_array($template_result['.']['result'])) {
      $template_result['.']['result'] = [];
    }

    $user = sn_options_admin_protection($user);
    $user = sn_options_vacation($user);
    $user = sn_options_gender($user);
    $user = sn_options_change_birthday($user);
    $user = sn_options_deprecated($user);
    sn_options_player_standard();

    $template_result['.']['result'][] = sn_options_change_password();
    list($user, $usernameResult) = sn_options_change_username($user);
    $template_result['.']['result'] = array_merge($template_result['.']['result'], $usernameResult);

    playerTimeDiff::sn_options_timediff(
      sys_get_param_int('PLAYER_OPTION_TIME_DIFF'),
      sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED'),
      sys_get_param_int('opt_time_diff_clear')
    );

    $avatar_upload_result = Avatar::sys_avatar_upload($user['id'], $user['avatar']);
    $template_result['.']['result'][] = $avatar_upload_result;

    $user['email'] = sys_get_param_str('db_email');
    SN::$gc->theUser->setSkinName(sys_get_param_str('skin_name'));
    $user['lang'] = sys_get_param_str('langer', $user['lang']);
    $user['design'] = sys_get_param_int('design');
    $user['noipcheck'] = sys_get_param_int('noipcheck');
    $user['deltime'] = !sys_get_param_int('deltime') ? 0 : ($user['deltime'] ? $user['deltime'] : SN_TIME_NOW + SN::$config->player_delete_time);

    DbQuery::build(SN::$db)
      ->setTable('users')
      ->setValues([
        'email'                    => $user['email'],
        'lang'                     => $user['lang'],
        'avatar'                   => $user['avatar'],
        'design'                   => $user['design'],
        'noipcheck'                => $user['noipcheck'],
        'deltime'                  => $user['deltime'],
        'vacation'                 => $user['vacation'],
        'gender'                   => $user['gender'],
        'skin'                     => SN::$gc->theUser->getSkinName(),
        'user_birthday'            => $user['user_birthday'],
        'user_birthday_celebrated' => $user['user_birthday_celebrated'],
        'options'                  => $user['options'],
      ])
      ->setWhereArray(['id' => $user['id']])
      ->doUpdate();

    $savedOk = true;
  } elseif (sys_get_param_str('result') == 'ok') {
    $savedOk = true;
  }

  if ($savedOk) {
    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => SN::$lang['opt_msg_saved']
    );
  }
}

//-------------------------------

function sn_options_view($template = null) {
  global $lang, $template_result, $user, $planetrow, $user_option_list, $user_option_types, $sn_message_class_list, $config;

  sys_user_vacation($user);

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  $template = SnTemplate::gettemplate('options', $template);

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  while (($entry = $dir->read()) !== false) {
    if (is_dir("skins/{$entry}") && $entry[0] != '.') {
      $template_result['.']['skin_list'][] = array(
        'VALUE'    => $entry,
        'NAME'     => $entry,
        'SELECTED' => SN::$gc->theUser->getSkinName() == $entry,
      );
    }
  }
  $dir->close();

  $ignores = SN::$gc->ignores->getIgnores($user['id'], true);
  $template_result['.']['ignores'] = $ignores;

  foreach ($lang['opt_planet_sort_options'] as $key => &$value) {
    $template_result['.']['planet_sort_options'][] = array(
      'VALUE'    => $key,
      'NAME'     => $value,
      'SELECTED' => SN::$user_options[PLAYER_OPTION_PLANET_SORT] == $key,
    );
  }

  foreach ($lang['sys_gender_list'] as $key => $value) {
    $template_result['.']['gender_list'][] = array(
      'VALUE'    => $key,
      'NAME'     => $value,
      'SELECTED' => $user['gender'] == $key,
    );
  }

  $lang_list = lng_get_list();
  foreach ($lang_list as $lang_id => $lang_data) {
    $template_result['.']['languages'][] = array(
      'VALUE'    => $lang_id,
      'NAME'     => $lang_data['LANG_NAME_NATIVE'],
      'SELECTED' => $lang_id == $user['lang'],
    );
  }


  if (isset($lang['menu_customize_show_hide_button_state'])) {
    foreach ($lang['menu_customize_show_hide_button_state'] as $key => $value) {
      $template->assign_block_vars('menu_customize_show_hide_button_state', array(
        'ID'   => $key,
        'NAME' => $value,
      ));
    }
  }

  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate($user['deltime']);

  sn_options_add_standard($template);

  $template->assign_vars([
    'USER_ID' => $user['id'],

    'ACCOUNT_NAME' => sys_safe_output(SN::$auth->account->account_name),

    'USER_AUTHLEVEL' => $user['authlevel'],

    'menu_customize_show_hide_button'     => SN::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON],
    'PLAYER_OPTION_MENU_SHOW_ON_BUTTON'   => SN::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_BUTTON'   => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_LEAVE'    => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
    'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE'   => SN::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
    'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS' => SN::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
    'PLAYER_OPTION_MENU_WHITE_TEXT'       => SN::$user_options[PLAYER_OPTION_MENU_WHITE_TEXT],
    'PLAYER_OPTION_MENU_OLD'              => SN::$user_options[PLAYER_OPTION_MENU_OLD],

    'PLAYER_OPTION_TUTORIAL_CURRENT_ID' => PLAYER_OPTION_TUTORIAL_CURRENT,

    'ADM_PROTECT_PLANETS' => $user['authlevel'] >= 3,
    'opt_usern_data'      => htmlspecialchars($user['username']),
    'opt_mail1_data'      => $user['email'],
    'opt_mail2_data'      => sys_safe_output(SN::$auth->account->account_email),

    'PLAYER_OPTION_PLANET_SORT_INVERSE'    => SN::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE],
    'PLAYER_OPTION_FLEET_SPY_DEFAULT'      => SN::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'PLAYER_OPTION_TOOLTIP_DELAY'          => SN::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE' => SN::$user_options[PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE],

    'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'" : '',
    'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'" : '',
    'deltime'        => $user['deltime'],
    'deltime_text'   => sprintf($str_date_format, $time_now_parsed['year'], $lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),

    'opt_avatar' => $user['avatar'],

    'config_game_email_pm' => $config->game_email_pm,

    'user_settings_esp'        => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING],
    'user_settings_mis'        => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_MISSILE],
    'user_settings_wri'        => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM],
    'user_settings_statistics' => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_STATS],
    'user_settings_info'       => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PROFILE],
    'user_settings_bud'        => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY],

    'user_time_diff_forced' => playerTimeDiff::getTimeDiffForced(),

    'adm_pl_prot' => $user['admin_protection'],

    'user_birthday' => $user['user_birthday'],
    'GENDER'        => $user['gender'],
    'GENDER_TEXT'   => $lang['sys_gender_list'][$user['gender']],
    'FMT_DATE'      => $FMT_DATE,
    'JS_FMT_DATE'   => js_safe_string($FMT_DATE),

    'USER_VACATION_DISABLE' => $config->user_vacation_disable,
    'VACATION_NEXT'         => $user['vacation_next'],
    'VACATION_NEXT_TEXT'    => date(FMT_DATE_TIME, $user['vacation_next']),
    'VACATION_TIMEOUT'      => $user['vacation_next'] - SN_TIME_NOW > 0 ? $user['vacation_next'] - SN_TIME_NOW : 0,
    'SN_TIME_NOW'           => SN_TIME_NOW,

    'SERVER_SEND_EMAIL' => $config->game_email_pm,

    'SERVER_NAME_CHANGE'         => $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE,
    'SERVER_NAME_CHANGE_PAY'     => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY,
    'SERVER_NAME_CHANGE_ENABLED' => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_FREE || ($config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY && mrc_get_level($user, $planetrow, RES_DARK_MATTER) >= $config->game_user_changename_cost),

    'DARK_MATTER' => prettyNumberStyledCompare($config->game_user_changename_cost, mrc_get_level($user, $planetrow, RES_DARK_MATTER)),

    'GROUP_DESIGN_BLOCK_TUTORIAL'      => GROUP_DESIGN_BLOCK_TUTORIAL,
    'GROUP_DESIGN_BLOCK_FLEET_COMPOSE' => GROUP_DESIGN_BLOCK_FLEET_COMPOSE,
    'GROUP_DESIGN_BLOCK_UNIVERSE'      => GROUP_DESIGN_BLOCK_UNIVERSE,
    'GROUP_DESIGN_BLOCK_NAVBAR'        => GROUP_DESIGN_BLOCK_NAVBAR,
    'GROUP_DESIGN_BLOCK_RESOURCEBAR'   => GROUP_DESIGN_BLOCK_RESOURCEBAR,
    'GROUP_DESIGN_BLOCK_PLANET_SORT'   => GROUP_DESIGN_BLOCK_PLANET_SORT,
    'GROUP_DESIGN_BLOCK_COMMON_ONE'    => GROUP_DESIGN_BLOCK_COMMON_ONE,
    'GROUP_DESIGN_BLOCK_COMMON_TWO'    => GROUP_DESIGN_BLOCK_COMMON_TWO,

    'PAGE_HEADER' => $lang['opt_header'],
  ]);

  foreach ($user_option_list as $option_group_id => $option_group) {
    if ($option_group_id == OPT_MESSAGE) {
      foreach ($sn_message_class_list as $message_class_id => $message_class_data) {
        if ($message_class_data['switchable'] || ($message_class_data['email'] && $config->game_email_pm)) {
          $option_name = $message_class_data['name'];

          $template->assign_block_vars("options_{$option_group_id}", array(
            'NAME'  => $message_class_data['name'],
            'TEXT'  => $lang['msg_class'][$message_class_id], // $lang['opt_custom'][$option_name],
            'PM'    => $message_class_data['switchable'] ? $user["opt_{$option_name}"] : -1,
            'EMAIL' => $message_class_data['email'] && $config->game_email_pm ? $user["opt_email_{$option_name}"] : -1,
          ));
        }
      }
    } else {
      foreach ($option_group as $option_name => $option_value) {
        if (array_key_exists($option_name, $user_option_types)) {
          $option_type = $user_option_types[$option_name];
        } else {
          $option_type = 'switch';
        }

        $template->assign_block_vars("options_{$option_group_id}", array(
          'NAME'  => $option_name,
          'TYPE'  => $option_type,
          'TEXT'  => $lang['opt_custom'][$option_name],
          'HINT'  => $lang['opt_custom']["{$option_name}_hint"],
          'VALUE' => $user[$option_name],
        ));
      }
    }
  }

//  var_dump($template_result['.']['result']);
//  var_dump($template->_tpldata);
//
  return $template;
}

//-------------------------------

/**
 * @param $user
 *
 * @return array
 */
function sn_options_gender($user) {
  $gender = sys_get_param_int('gender', $user['gender']);
  !isset(SN::$lang['sys_gender_list'][$gender]) ? $gender = $user['gender'] : false;
  $user['gender'] = $user['gender'] == GENDER_UNKNOWN ? $gender : $user['gender'];

  return $user;
}

/**
 * @param array $user
 *
 * @return array
 */
function sn_options_change_birthday($user) {
  $user_birthday = sys_get_param_str_unsafe('user_birthday');
  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if ($user['birthday'] || empty($user_birthday) || $user_birthday == $FMT_DATE) {
    return $user;
  }

  try {
    // Some black magic to parse any valid date format - those that contains all three "d", "m" and "Y" and any of the delimeters "\", "/", ".", "-"
    $pos['d'] = strpos(FMT_DATE, 'd');
    $pos['m'] = strpos(FMT_DATE, 'm');
    $pos['Y'] = strpos(FMT_DATE, 'Y');
    asort($pos);
    $i = 0;
    foreach ($pos as &$position) {
      $position = ++$i;
    }

    $regexp = "/" . preg_replace(array('/\\\\/', '/\//', '/\./', '/\-/', '/d/', '/m/', '/Y/'), array('\\\\\\', '\/', '\.', '\-', '(\d?\d)', '(\d?\d)', '(\d{4})'), FMT_DATE) . "/";
    if (!preg_match($regexp, $user_birthday, $match)) {
      throw new Exception();
    }

    if (!checkdate($match[$pos['m']], $match[$pos['d']], $match[$pos['Y']])) {
      throw new Exception();
    }

    $user_birthday_new_unescaped = "{$match[$pos['Y']]}-{$match[$pos['m']]}-{$match[$pos['d']]}";
    $user['user_birthday'] = $user_birthday_new_unescaped;
    // EOF black magic! Now we have valid SQL date in $user['user_birthday'] - independent of date format

    $year = date('Y', SN_TIME_NOW);
    if (mktime(0, 0, 0, $match[$pos['m']], $match[$pos['d']], $year) > SN_TIME_NOW) {
      $year--;
    }
    $user['user_birthday_celebrated'] = "{$year}-{$match[$pos['m']]}-{$match[$pos['d']]}";
  } catch (exception $e) {
    $user['user_birthday'] = null;
    $user['user_birthday_celebrated'] = null;
  }

  return $user;
}

/**
 * @return array
 */
function sn_options_change_password() {
  $result = [];
  if (!($new_password = sys_get_param('newpass1'))) {
    return $result;
  }

  try {
    if ($new_password != sys_get_param('newpass2')) {
      throw new Exception('opt_err_pass_unmatched', ERR_WARNING);
    }

    if (!SN::$auth->password_change(sys_get_param('db_password'), $new_password)) {
      throw new Exception('opt_err_pass_wrong', ERR_WARNING);
    }

    throw new Exception('opt_msg_pass_changed', ERR_NONE);
  } catch (Exception $e) {
    $result = [
      'STATUS'  => in_array($e->getCode(), [ERR_NONE, ERR_WARNING, ERR_ERROR]) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => SN::$lang[$e->getMessage()],
    ];
  }

  return $result;
}

function sn_options_player_standard() {
  $player_options = sys_get_param('options');
  if (empty($player_options)) {
    return;
  }

  if ($player_options[PLAYER_OPTION_TUTORIAL_CURRENT]) {
    $player_options[PLAYER_OPTION_TUTORIAL_CURRENT] = SN::$config->tutorial_first_item;
    $player_options[PLAYER_OPTION_TUTORIAL_FINISHED] = 0;
  } else {
    unset($player_options[PLAYER_OPTION_TUTORIAL_CURRENT]);
  }

  array_walk($player_options, function (&$value) {
    // TODO - Когда будет больше параметров - сделать больше проверок
    $value = intval($value);
  });
  SN::$user_options->offsetSet($player_options);
}

/**
 * @param array $user
 *
 * @return array
 */
function sn_options_change_username($user) {
  $config = SN::$config;
  $lang = SN::$lang;

  $result = [];

  $username = substr(sys_get_param_str_unsafe('username'), 0, 32);
  if (
    empty($username)
    || $user['username'] == $username
    || $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_NONE
    || !sys_get_param_int('username_confirm')
    || strpbrk($username, LOGIN_REGISTER_CHARACTERS_PROHIBITED)
  ) {
    return [$user, $result];
  }

  // проверка на корректность
  sn_db_transaction_start();
  $username_safe = db_escape($username);
  /** @noinspection SqlResolve */
  $name_check = doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` LIKE \"{$username_safe}\" LIMIT 1 FOR UPDATE;", true);
  if (empty($name_check['player_id']) || $name_check['player_id'] == $user['id']) {
    $user = db_user_by_id($user['id'], true);
    switch ($config->game_user_changename) {
      /** @noinspection PhpMissingBreakStatementInspection */
      case SERVER_PLAYER_NAME_CHANGE_PAY:
        if (mrc_get_level($user, [], RES_DARK_MATTER) < $config->game_user_changename_cost) {
          $result[] = [
            'STATUS'  => ERR_ERROR,
            'MESSAGE' => $lang['opt_msg_name_change_err_no_dm'],
          ];
          break;
        }
        rpg_points_change(
          $user['id'],
          RPG_NAME_CHANGE,
          -$config->game_user_changename_cost,
          vsprintf('Пользователь ID %1$d сменил имя с "%2$s" на "%3$s"', [$user['id'], $user['username'], $username,])
        );

      case SERVER_PLAYER_NAME_CHANGE_FREE:
        db_user_set_by_id($user['id'], "`username` = '{$username_safe}'");
        /** @noinspection SqlResolve */
        doquery("REPLACE INTO `{{player_name_history}}` SET `player_id` = {$user['id']}, `player_name` = '{$username_safe}'");
        // TODO: Change cookie to not force user relogin
        // sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
        $result[] = [
          'STATUS'  => ERR_NONE,
          'MESSAGE' => $lang['opt_msg_name_changed']
        ];
        $user['username'] = $username;
      break;
    }
  } else {
    $result[] = [
      'STATUS'  => ERR_ERROR,
      'MESSAGE' => $lang['opt_msg_name_change_err_used_name'],
    ];
  }
  sn_db_transaction_commit();

  return [$user, $result];
}

/**
 * Set old options
 *
 * @param array $user
 *
 * @return array
 * @deprecated
 */
function sn_options_deprecated($user) {
  global $user_option_list;

  foreach ($user_option_list as $option_group_id => $option_group) {
    foreach ($option_group as $option_name => $option_value) {
      if ($user[$option_name] !== null) {
        $user[$option_name] = sys_get_param_str($option_name);
      } else {
        $user[$option_name] = $option_value;
      }
    }
  }

  sys_user_options_pack($user);

  return $user;
}

/**
 * @param array $user
 *
 * @return array
 */
function sn_options_admin_protection($user) {
  if ($user['authlevel'] <= AUTH_LEVEL_REGISTERED) {
    return $user;
  }

  $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
  DBStaticPlanet::db_planet_set_by_owner($user['id'], "`id_level` = '{$planet_protection}'");
  db_user_set_by_id($user['id'], "`admin_protection` = '{$planet_protection}'");
  $user['admin_protection'] = $planet_protection;

  return $user;
}

/**
 * @param array $user
 *
 * @return array
 */
function sn_options_vacation($user) {
  $config = SN::$config;
  $lang = SN::$lang;

  if (!sys_get_param_int('vacation') || $config->user_vacation_disable) {
    return $user;
  }

  sn_db_transaction_start();
  if ($user['authlevel'] < AUTH_LEVEL_ADMINISTRATOR) {
    if ($user['vacation_next'] > SN_TIME_NOW) {
      SnTemplate::messageBox($lang['opt_vacation_err_timeout'], $lang['Error'], 'index.php?page=options', 5);
      die();
    }

    if (DbFleetStatic::fleet_count_flying($user['id'])) {
      SnTemplate::messageBox($lang['opt_vacation_err_your_fleet'], $lang['Error'], 'index.php?page=options', 5);
      die();
    }

    $que = que_get($user['id'], false);
    if (!empty($que)) {
      SnTemplate::messageBox($lang['opt_vacation_err_que'], $lang['Error'], 'index.php?page=options', 5);
      die();
    }

    $query = SN::db_get_record_list(LOC_PLANET, "`id_owner` = {$user['id']}");
    foreach ($query as $planet) {
      DBStaticPlanet::db_planet_set_by_id($planet['id'],
        "last_update = " . SN_TIME_NOW . ", energy_used = '0', energy_max = '0',
        metal_perhour = '{$config->metal_basic_income}', crystal_perhour = '{$config->crystal_basic_income}', deuterium_perhour = '{$config->deuterium_basic_income}',
        metal_mine_porcent = '0', crystal_mine_porcent = '0', deuterium_sintetizer_porcent = '0', solar_plant_porcent = '0',
        fusion_plant_porcent = '0', solar_satelit_porcent = '0', ship_sattelite_sloth_porcent = 0"
      );
    }
    $user['vacation'] = SN_TIME_NOW + $config->player_vacation_time;
  } else {
    $user['vacation'] = SN_TIME_NOW;
  }
  sn_db_transaction_commit();

  return $user;
}


/**
 * @param template $template
 * @param string   $blockName
 * @param int      $blockId
 * @param int[]    $optionsNavBar
 * @param array    $options
 */
function sn_options_render_block($template, $blockName, $blockId, $optionsNavBar, $options = []) {
  $template->assign_block_vars('player_options', [
    'ID'   => $blockId,
    'NAME' => $blockName,
  ]);

  foreach ($optionsNavBar as $optionId) {
    $template->assign_block_vars('player_options.option', [
      'ID'         => $optionId,
      'VALUE'      => SN::$user_options[$optionId],
      'NAME'       => SN::$lang['opt_player_options'][$optionId],
      'ALWAYS_OFF' => !empty($options[$optionId]['always_off']),
      'CLASS'      => !empty($options[$optionId]['class']) ? $options[$optionId]['class'] : 'cell',
    ]);
  }
}

/**
 * @param $template
 */
function sn_options_add_standard($template) {
  sn_options_render_block($template, '', 5, [
  ]);


  // 8
  sn_options_render_block($template, '', GROUP_DESIGN_BLOCK_COMMON_TWO,
    [
      PLAYER_OPTION_SOUND_ENABLED,
      PLAYER_OPTION_ANIMATION_DISABLED,
      PLAYER_OPTION_PROGRESS_BARS_DISABLED,
    ],
    [
      PLAYER_OPTION_SOUND_ENABLED          => ['class' => 'header'],
      PLAYER_OPTION_ANIMATION_DISABLED     => ['class' => 'header'],
      PLAYER_OPTION_PROGRESS_BARS_DISABLED => ['class' => 'header'],
    ]
  );
  // 7
  sn_options_render_block($template, '', GROUP_DESIGN_BLOCK_COMMON_ONE, [
    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE,
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS,
    PLAYER_OPTION_TECH_TREE_TABLE,
  ]);
  // 6
  sn_options_render_block($template, '', GROUP_DESIGN_BLOCK_PLANET_SORT, [
    PLAYER_OPTION_PLANET_SORT_INVERSE,
  ]);
  // 4
  sn_options_render_block($template, SN::$lang['opt_navbar_resourcebar_description'], GROUP_DESIGN_BLOCK_RESOURCEBAR, [
    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL,
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE,
    PLAYER_OPTION_NAVBAR_PLANET_OLD,
  ]);
  // 3
  sn_options_render_block($template, SN::$lang['opt_navbar_buttons_title'], GROUP_DESIGN_BLOCK_NAVBAR, [
    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE,
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH,
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET,
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR,
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE,
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS,
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS,
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS,
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER,
  ]);
  // 2
  sn_options_render_block($template, SN::$lang['galaxyvision_options'], GROUP_DESIGN_BLOCK_UNIVERSE, [
    PLAYER_OPTION_UNIVERSE_OLD,
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE,
  ]);
  // 1
  sn_options_render_block($template, SN::$lang['option_fleet_send'], GROUP_DESIGN_BLOCK_FLEET_COMPOSE, [
    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION,
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY,
  ]);
  // 0
  sn_options_render_block($template, SN::$lang['opt_tutorial'], GROUP_DESIGN_BLOCK_TUTORIAL, [
    PLAYER_OPTION_TUTORIAL_DISABLED,
    // PLAYER_OPTION_TUTORIAL_WINDOWED,
    PLAYER_OPTION_TUTORIAL_CURRENT,
  ], [PLAYER_OPTION_TUTORIAL_CURRENT => ['always_off' => true]]);
}
