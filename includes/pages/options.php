<?php

/**
 * options.php
 *
 * @copyright (c) 2010-2017 by Gorlum for http://supernova.ws
 */

function sn_options_model() {
  global $user, $user_option_list, $lang, $template_result, $config;

  $language_new = sys_get_param_str('langer', $user['lang']);

  if($language_new != $user['lang']) {
    $lang->lng_switch($language_new);
  }

  lng_include('options');
  lng_include('messages');

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if(sys_get_param_str('mode') == 'change') {
    if($user['authlevel'] > 0) {
      $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
      DBStaticPlanet::db_planet_set_by_owner($user['id'], "`id_level` = '{$planet_protection}'");
      db_user_set_by_id($user['id'], "`admin_protection` = '{$planet_protection}'");
      $user['admin_protection'] = $planet_protection;
    }

    if(sys_get_param_int('vacation') && !$config->user_vacation_disable) {
      sn_db_transaction_start();
      if($user['authlevel'] < 3) {
        if($user['vacation_next'] > SN_TIME_NOW) {
          messageBox($lang['opt_vacation_err_timeout'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        if(fleet_count_flying($user['id'])) {
          messageBox($lang['opt_vacation_err_your_fleet'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $que = que_get($user['id'], false);
        if(!empty($que)) {
          messageBox($lang['opt_vacation_err_que'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $query = SN::db_get_record_list(LOC_PLANET, "`id_owner` = {$user['id']}");
        foreach($query as $planet) {
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
    }

    foreach($user_option_list as $option_group_id => $option_group) {
      foreach($option_group as $option_name => $option_value) {
        if($user[$option_name] !== null) {
          $user[$option_name] = sys_get_param_str($option_name);
        } else {
          $user[$option_name] = $option_value;
        }
      }
    }
    $options = sys_user_options_pack($user);


    $player_options = sys_get_param('options');
    if(!empty($player_options)) {
      if($player_options[PLAYER_OPTION_TUTORIAL_CURRENT] == SN::$config->tutorial_first_item) {
        $player_options[PLAYER_OPTION_TUTORIAL_FINISHED] = 0;
      }

      array_walk($player_options, function (&$value) {
        // TODO - Когда будет больше параметров - сделать больше проверок
        $value = intval($value);
      });
      SN::$user_options->offsetSet($player_options);
    }

    $username = substr(sys_get_param_str_unsafe('username'), 0, 32);
    $username_safe = db_escape($username);
    if($username && $user['username'] != $username && $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE && sys_get_param_int('username_confirm') && !strpbrk($username, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      // проверка на корректность
      sn_db_transaction_start();
      $name_check = doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` LIKE \"{$username_safe}\" LIMIT 1 FOR UPDATE;", true);
      if(!$name_check || $name_check['player_id'] == $user['id']) {
        $user = db_user_by_id($user['id'], true);
        switch($config->game_user_changename) {
          case SERVER_PLAYER_NAME_CHANGE_PAY:
            if(mrc_get_level($user, [], RES_DARK_MATTER) < $config->game_user_changename_cost) {
              $template_result['.']['result'][] = array(
                'STATUS'  => ERR_ERROR,
                'MESSAGE' => $lang['opt_msg_name_change_err_no_dm'],
              );
              break;
            }
            rpg_points_change($user['id'], RPG_NAME_CHANGE, -$config->game_user_changename_cost, sprintf('Пользователь ID %d сменил имя с "%s" на "%s"', $user['id'], $user['username'], $username));

          case SERVER_PLAYER_NAME_CHANGE_FREE:
            db_user_set_by_id($user['id'], "`username` = '{$username_safe}'");
            doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user['id']}, `player_name` = '{$username_safe}'");
            // TODO: Change cookie to not force user relogin
            // sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
            $template_result['.']['result'][] = array(
              'STATUS'  => ERR_NONE,
              'MESSAGE' => $lang['opt_msg_name_changed']
            );
            $user['username'] = $username;
          break;
        }
      } else {
        $template_result['.']['result'][] = array(
          'STATUS'  => ERR_ERROR,
          'MESSAGE' => $lang['opt_msg_name_change_err_used_name'],
        );
      }
      sn_db_transaction_commit();
    }

    if($new_password = sys_get_param('newpass1')) {
      try {
        if($new_password != sys_get_param('newpass2')) {
          throw new Exception($lang['opt_err_pass_unmatched'], ERR_WARNING);
        }

        if(!SN::$auth->password_change(sys_get_param('db_password'), $new_password)) {
          throw new Exception($lang['opt_err_pass_wrong'], ERR_WARNING);
        }

        throw new Exception($lang['opt_msg_pass_changed'], ERR_NONE);
      } catch(Exception $e) {
        $template_result['.']['result'][] = array(
          'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
          'MESSAGE' => $e->getMessage()
        );
      }
    }

    $user['email'] = sys_get_param_str('db_email');
    SN::$gc->theUser->setSkinName(sys_get_param_str('skin_name'));
    $user['lang'] = sys_get_param_str('langer', $user['lang']);

    $user['design'] = sys_get_param_int('design');
    $user['noipcheck'] = sys_get_param_int('noipcheck');
    $user['deltime'] = !sys_get_param_int('deltime') ? 0 : ($user['deltime'] ? $user['deltime'] : SN_TIME_NOW + $config->player_delete_time);

    $gender = sys_get_param_int('gender', $user['gender']);
    !isset($lang['sys_gender_list'][$gender]) ? $gender = $user['gender'] : false;
    $user['gender'] = $user['gender'] == GENDER_UNKNOWN ? $gender : $user['gender'];

    try {
      if($user['birthday']) {
        throw new exception();
      }

      $user_birthday = sys_get_param_str_unsafe('user_birthday');
      if(!$user_birthday || $user_birthday == $FMT_DATE) {
        throw new exception();
      }

      // Some black magic to parse any valid date format - those that contains all three "d", "m" and "Y" and any of the delimeters "\", "/", ".", "-"
      $pos['d'] = strpos(FMT_DATE, 'd');
      $pos['m'] = strpos(FMT_DATE, 'm');
      $pos['Y'] = strpos(FMT_DATE, 'Y');
      asort($pos);
      $i = 0;
      foreach($pos as &$position) {
        $position = ++$i;
      }

      $regexp = "/" . preg_replace(array('/\\\\/', '/\//', '/\./', '/\-/', '/d/', '/m/', '/Y/'), array('\\\\\\', '\/', '\.', '\-', '(\d?\d)', '(\d?\d)', '(\d{4})'), FMT_DATE) . "/";
      if(!preg_match($regexp, $user_birthday, $match)) {
        throw new exception();
      }

      if(!checkdate($match[$pos['m']], $match[$pos['d']], $match[$pos['Y']])) {
        throw new exception();
      }

      $user['user_birthday'] = db_escape("{$match[$pos['Y']]}-{$match[$pos['m']]}-{$match[$pos['d']]}");
      // EOF black magic! Now we have valid SQL date in $user['user_birthday'] - independent of date format

      $year = date('Y', SN_TIME_NOW);
      if(mktime(0, 0, 0, $match[$pos['m']], $match[$pos['d']], $year) > SN_TIME_NOW) {
        $year--;
      }
      $user['user_birthday_celebrated'] = db_escape("{$year}-{$match[$pos['m']]}-{$match[$pos['d']]}");

      $user_birthday = ", `user_birthday` = '{$user['user_birthday']}', `user_birthday_celebrated` = '{$user['user_birthday_celebrated']}'";
    } catch(exception $e) {
      $user_birthday = '';
    }

    require_once('includes/includes/sys_avatar.php');

    $avatar_upload_result = sys_avatar_upload($user['id'], $user['avatar']);
    $template_result['.']['result'][] = $avatar_upload_result;

    $user_time_diff = playerTimeDiff::user_time_diff_get();
    if(sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED')) {
      playerTimeDiff::user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF              => sys_get_param_int('PLAYER_OPTION_TIME_DIFF'),
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED       => 1,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    } elseif(sys_get_param_int('opt_time_diff_clear') || $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
      playerTimeDiff::user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF              => '',
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED       => 0,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    }

    $user_options_safe = db_escape($user['options']);
    db_user_set_by_id($user['id'], "`email` = '{$user['email']}', `lang` = '{$user['lang']}', `avatar` = '{$user['avatar']}',
      `skin` = '" . SN::$gc->theUser->getSkinName() . "', `design` = '{$user['design']}', `noipcheck` = '{$user['noipcheck']}',
      `deltime` = '{$user['deltime']}', `vacation` = '{$user['vacation']}', `options` = '{$user_options_safe}', `gender` = {$user['gender']}
      {$user_birthday}"
    );

    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => $lang['opt_msg_saved']
    );
  } elseif(sys_get_param_str('result') == 'ok') {
    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => $lang['opt_msg_saved']
    );
  }

  $user = db_user_by_id($user['id']);
  $options = sys_user_options_unpack($user);
}

//-------------------------------

function sn_options_view($template = null) {
  global $lang, $template_result, $user, $planetrow, $user_option_list, $user_option_types, $sn_message_class_list, $config;

  sys_user_vacation($user);

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  $template = gettemplate('options', $template);

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  while(($entry = $dir->read()) !== false) {
    if(is_dir("skins/{$entry}") && $entry[0] != '.') {
      $template_result['.']['skin_list'][] = array(
        'VALUE'    => $entry,
        'NAME'     => $entry,
        'SELECTED' => SN::$gc->theUser->getSkinName() == $entry,
      );
    }
  }
  $dir->close();

  foreach($lang['opt_planet_sort_options'] as $key => &$value) {
    $template_result['.']['planet_sort_options'][] = array(
      'VALUE'    => $key,
      'NAME'     => $value,
      'SELECTED' => SN::$user_options[PLAYER_OPTION_PLANET_SORT] == $key,
    );
  }

  foreach($lang['sys_gender_list'] as $key => $value) {
    $template_result['.']['gender_list'][] = array(
      'VALUE'    => $key,
      'NAME'     => $value,
      'SELECTED' => $user['gender'] == $key,
    );
  }

  $lang_list = lng_get_list();
  foreach($lang_list as $lang_id => $lang_data) {
    $template_result['.']['languages'][] = array(
      'VALUE'    => $lang_id,
      'NAME'     => $lang_data['LANG_NAME_NATIVE'],
      'SELECTED' => $lang_id == $user['lang'],
    );
  }


  if(isset($lang['menu_customize_show_hide_button_state'])) {
    foreach($lang['menu_customize_show_hide_button_state'] as $key => $value) {
      $template->assign_block_vars('menu_customize_show_hide_button_state', array(
        'ID'   => $key,
        'NAME' => $value,
      ));
    }
  }

  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate($user['deltime']);

  $user_time_diff = playerTimeDiff::user_time_diff_get();
  $template->assign_vars(array(
    'USER_ID'      => $user['id'],

    'ACCOUNT_NAME' => sys_safe_output(SN::$auth->account->account_name),

    'USER_AUTHLEVEL' => $user['authlevel'],

    'menu_customize_show_hide_button'         => SN::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON],
    'PLAYER_OPTION_MENU_SHOW_ON_BUTTON'       => SN::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_BUTTON'       => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_LEAVE'        => SN::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
    'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE'       => SN::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
    'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS'     => SN::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
    'PLAYER_OPTION_MENU_WHITE_TEXT'           => SN::$user_options[PLAYER_OPTION_MENU_WHITE_TEXT],
    'PLAYER_OPTION_MENU_OLD'                  => SN::$user_options[PLAYER_OPTION_MENU_OLD],
    'PLAYER_OPTION_UNIVERSE_OLD'              => SN::$user_options[PLAYER_OPTION_UNIVERSE_OLD],
    'PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE' => SN::$user_options[PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE],
    'PLAYER_OPTION_DESIGN_DISABLE_BORDERS'    => SN::$user_options[PLAYER_OPTION_DESIGN_DISABLE_BORDERS],
    'PLAYER_OPTION_TECH_TREE_TABLE'           => SN::$user_options[PLAYER_OPTION_TECH_TREE_TABLE],
    'sound_enabled'                           => SN::$user_options[PLAYER_OPTION_SOUND_ENABLED],
    'PLAYER_OPTION_ANIMATION_DISABLED'        => SN::$user_options[PLAYER_OPTION_ANIMATION_DISABLED],
    'PLAYER_OPTION_PROGRESS_BARS_DISABLED'    => SN::$user_options[PLAYER_OPTION_PROGRESS_BARS_DISABLED],
    'PLAYER_OPTION_FLEET_SHIP_SELECT_OLD'     => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SELECT_OLD],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED'     => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY'     => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION'     => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION],

    'PLAYER_OPTION_TUTORIAL_DISABLED'     => SN::$user_options[PLAYER_OPTION_TUTORIAL_DISABLED],
    'PLAYER_OPTION_TUTORIAL_WINDOWED'     => SN::$user_options[PLAYER_OPTION_TUTORIAL_WINDOWED],
    'PLAYER_OPTION_TUTORIAL_CURRENT'     => SN::$user_options[PLAYER_OPTION_TUTORIAL_CURRENT],

    'PLAYER_OPTION_NAVBAR_PLANET_OLD'     => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_OLD],
    'PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE' => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE],

    'ADM_PROTECT_PLANETS' => $user['authlevel'] >= 3,
    'opt_usern_data'      => htmlspecialchars($user['username']),
    'opt_mail1_data'      => $user['email'],
    'opt_mail2_data'      => sys_safe_output(SN::$auth->account->account_email),

    'PLAYER_OPTION_PLANET_SORT_INVERSE'    => SN::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE],
    'PLAYER_OPTION_FLEET_SPY_DEFAULT'      => SN::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'PLAYER_OPTION_TOOLTIP_DELAY'          => SN::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE' => SN::$user_options[PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE],

    'PLAYER_OPTION_NAVBAR_PLANET_VERTICAL'       => SN::$user_options[PLAYER_OPTION_NAVBAR_PLANET_VERTICAL],
    'PLAYER_OPTION_NAVBAR_RESEARCH_WIDE'         => SN::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE],
    'PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS'   => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS],
    'PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS' => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS],
    'PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH'      => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH],
    'PLAYER_OPTION_NAVBAR_DISABLE_PLANET'        => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_PLANET],
    'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR'        => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_HANGAR],
    'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS'        => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_QUESTS],
    'PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER'   => SN::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER],

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

    'user_time_diff_forced' => $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED],

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

    'PAGE_HEADER' => $lang['opt_header'],
  ));

  foreach($user_option_list as $option_group_id => $option_group) {
    if($option_group_id == OPT_MESSAGE) {
      foreach($sn_message_class_list as $message_class_id => $message_class_data) {
        if($message_class_data['switchable'] || ($message_class_data['email'] && $config->game_email_pm)) {
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
      foreach($option_group as $option_name => $option_value) {
        if(array_key_exists($option_name, $user_option_types)) {
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

  return $template;
}
