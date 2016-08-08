<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */
function sn_options_model() {
  global $user, $user_option_list, $template_result;

  $language_new = sys_get_param_str('langer', $user['lang']);

  if($language_new != $user['lang']) {
    classLocale::$lang->lng_switch($language_new);
  }

  lng_include('options');
  lng_include('messages');

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if(sys_get_param_str('mode') == 'change') {
    if($user['authlevel'] > 0) {
      $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
      DBStaticPlanet::db_planet_set_by_owner(
        $user['id'],
        array(
          'id_level' => $planet_protection
        )
      );
      $user['admin_protection'] = $planet_protection;
      DBStaticUser::db_user_set_by_id(
        $user['id'],
        array(
          'admin_protection' => $user['admin_protection'],
        )
      );
    }

    if(sys_get_param_int('vacation') && !classSupernova::$config->user_vacation_disable) {
      sn_db_transaction_start();
      if($user['authlevel'] < 3) {
        if($user['vacation_next'] > SN_TIME_NOW) {
          message(classLocale::$lang['opt_vacation_err_timeout'], classLocale::$lang['Error'], 'index.php?page=options', 5);
          die();
        }

        if(FleetList::fleet_count_flying($user['id'])) {
          message(classLocale::$lang['opt_vacation_err_your_fleet'], classLocale::$lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $que = que_get($user['id'], false);
        if(!empty($que)) {
          message(classLocale::$lang['opt_vacation_err_que'], classLocale::$lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $query = classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET, "`id_owner` = {$user['id']}");
        foreach($query as $planet) {
          $classConfig = classSupernova::$config;
          DBStaticPlanet::db_planet_update_set_by_id(
            $planet['id'],
            array(
              'last_update'                  => SN_TIME_NOW,
              'metal_perhour'                => $classConfig->metal_basic_income,
              'crystal_perhour'              => $classConfig->crystal_basic_income,
              'deuterium_perhour'            => $classConfig->deuterium_basic_income,
              'energy_used'                  => 0,
              'energy_max'                   => 0,
              'metal_mine_porcent'           => 0,
              'crystal_mine_porcent'         => 0,
              'deuterium_sintetizer_porcent' => 0,
              'solar_plant_porcent'          => 0,
              'fusion_plant_porcent'         => 0,
              'solar_satelit_porcent'        => 0,
              'ship_sattelite_sloth_porcent' => 0,
            )
          );
        }
        $user['vacation'] = SN_TIME_NOW + classSupernova::$config->player_vacation_time;
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
      array_walk($player_options, function (&$value) {
        // TODO - Когда будет больше параметров - сделать больше проверок
        $value = intval($value);
      });
      classSupernova::$user_options->offsetSet($player_options);
      // pdump($player_options);die();
      //      player_save_option_array($user, $player_options);
    }

    $username = substr(sys_get_param_str_unsafe('username'), 0, 32);
    $username_safe = db_escape($username);
    if($username && $user['username'] != $username && classSupernova::$config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE && sys_get_param_int('username_confirm') && !strpbrk($username, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      // проверка на корректность
      sn_db_transaction_start();
      $name_check = db_player_name_history_get_name_by_name($username_safe);
      if(!$name_check || $name_check['player_id'] == $user['id']) {
        $user = DBStaticUser::db_user_by_id($user['id'], true);
        switch(classSupernova::$config->game_user_changename) {
          case SERVER_PLAYER_NAME_CHANGE_PAY:
            if(mrc_get_level($user, $planetrow, RES_DARK_MATTER) < classSupernova::$config->game_user_changename_cost) {
              $template_result['.']['result'][] = array(
                'STATUS'  => ERR_ERROR,
                'MESSAGE' => classLocale::$lang['opt_msg_name_change_err_no_dm'],
              );
              break;
            }
            rpg_points_change($user['id'], RPG_NAME_CHANGE, -classSupernova::$config->game_user_changename_cost, sprintf('Пользователь ID %d сменил имя с "%s" на "%s"', $user['id'], $user['username'], $username));

          case SERVER_PLAYER_NAME_CHANGE_FREE:
            $user['username'] = $username;
            DBStaticUser::db_user_set_by_id(
              $user['id'],
              array(
                '$username' => $user['$username'],
              )
            );

            db_player_name_history_replace($user['id'], $username);
            // TODO: Change cookie to not force user relogin
            // sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
            $template_result['.']['result'][] = array(
              'STATUS'  => ERR_NONE,
              'MESSAGE' => classLocale::$lang['opt_msg_name_changed']
            );
          break;
        }
      } else {
        $template_result['.']['result'][] = array(
          'STATUS'  => ERR_ERROR,
          'MESSAGE' => classLocale::$lang['opt_msg_name_change_err_used_name'],
        );
      }
      sn_db_transaction_commit();
    }

    if($new_password = sys_get_param('newpass1')) {
      try {
        if($new_password != sys_get_param('newpass2')) {
          throw new Exception(classLocale::$lang['opt_err_pass_unmatched'], ERR_WARNING);
        }

        if(!classSupernova::$auth->password_change(sys_get_param('db_password'), $new_password)) {
          throw new Exception(classLocale::$lang['opt_err_pass_wrong'], ERR_WARNING);
        }

        throw new Exception(classLocale::$lang['opt_msg_pass_changed'], ERR_NONE);
      } catch(Exception $e) {
        $template_result['.']['result'][] = array(
          'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
          'MESSAGE' => $e->getMessage()
        );
      }
    }

    $user['email'] = sys_get_param_str_unsafe('db_email');
    $user['dpath'] = sys_get_param_str_unsafe('dpath');
    $user['lang'] = sys_get_param_str_unsafe('langer', $user['lang']);


    $user['design'] = sys_get_param_int('design');
    $user['noipcheck'] = sys_get_param_int('noipcheck');
    $user['deltime'] = !sys_get_param_int('deltime') ? 0 : ($user['deltime'] ? $user['deltime'] : SN_TIME_NOW + classSupernova::$config->player_delete_time);

    $gender = sys_get_param_int('gender', $user['gender']);
    !isset(classLocale::$lang['sys_gender_list'][$gender]) ? $gender = $user['gender'] : false;
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

      $user['user_birthday'] = "{$match[$pos['Y']]}-{$match[$pos['m']]}-{$match[$pos['d']]}";
      // EOF black magic! Now we have valid SQL date in $user['user_birthday'] - independent of date format

      $year = date('Y', SN_TIME_NOW);
      if(mktime(0, 0, 0, $match[$pos['m']], $match[$pos['d']], $year) > SN_TIME_NOW) {
        $year--;
      }
      $user['user_birthday_celebrated'] = "{$year}-{$match[$pos['m']]}-{$match[$pos['d']]}";

    } catch(Exception $e) {
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

    DBStaticUser::db_user_set_by_id(
      $user['id'],
      array(
        'email'                    => $user['email'],
        'lang'                     => $user['lang'],
        'avatar'                   => $user['avatar'],
        'dpath'                    => $user['dpath'],
        'design'                   => $user['design'],
        'noipcheck'                => $user['noipcheck'],
        'deltime'                  => $user['deltime'],
        'vacation'                 => $user['vacation'],
        'options'                  => $user['options'],
        'gender'                   => $user['gender'],
        'user_birthday'            => $user['user_birthday'],
        'user_birthday_celebrated' => $user['user_birthday_celebrated'],
      )
    );

    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => classLocale::$lang['opt_msg_saved']
    );
  } elseif(sys_get_param_str('result') == 'ok') {
    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => classLocale::$lang['opt_msg_saved']
    );
  }

  $user = DBStaticUser::db_user_by_id($user['id']);
  $options = sys_user_options_unpack($user);
}

//-------------------------------

function sn_options_view($template = null) {
  global $template_result, $user, $planetrow, $user_option_list, $user_option_types;
  $classLocale = classLocale::$lang;

  sys_user_vacation($user);

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  $template = gettemplate('options', $template);

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  while(($entry = $dir->read()) !== false) {
    if(is_dir("skins/{$entry}") && $entry[0] != '.') {
      $template_result['.']['skin_list'][] = array(
        'VALUE'    => $entry,
        'NAME'     => $entry,
        'SELECTED' => $user['dpath'] == "skins/{$entry}/",
      );
    }
  }
  $dir->close();

  foreach(classLocale::$lang['opt_planet_sort_options'] as $key => &$value) {
    $template_result['.']['planet_sort_options'][] = array(
      'VALUE'    => $key,
      'NAME'     => $value,
      'SELECTED' => classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT] == $key,
    );
  }

  foreach(classLocale::$lang['sys_gender_list'] as $key => $value) {
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


  if(isset(classLocale::$lang['menu_customize_show_hide_button_state'])) {
    foreach(classLocale::$lang['menu_customize_show_hide_button_state'] as $key => $value) {
      $template->assign_block_vars('menu_customize_show_hide_button_state', array(
        'ID'   => $key,
        'NAME' => $value,
      ));
    }
  }

  $str_date_format = "%3$02d %2$0s %1$04d {$classLocale['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate($user['deltime']);

  $user_time_diff = playerTimeDiff::user_time_diff_get();
  // $player_options = player_load_option($user);
  $template->assign_vars(array(
    'USER_ID' => $user['id'],

    'ACCOUNT_NAME' => sys_safe_output(classSupernova::$auth->account->account_name),

    'USER_AUTHLEVEL' => $user['authlevel'],

    'menu_customize_show_hide_button'           => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON],
    'PLAYER_OPTION_MENU_SHOW_ON_BUTTON'         => classSupernova::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_BUTTON'         => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
    'PLAYER_OPTION_MENU_HIDE_ON_LEAVE'          => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
    'PLAYER_OPTION_MENU_UNPIN_ABSOLUTE'         => classSupernova::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
    'PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS'       => classSupernova::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
    'PLAYER_OPTION_MENU_WHITE_TEXT'             => classSupernova::$user_options[PLAYER_OPTION_MENU_WHITE_TEXT],
    'PLAYER_OPTION_MENU_OLD'                    => classSupernova::$user_options[PLAYER_OPTION_MENU_OLD],
    'PLAYER_OPTION_UNIVERSE_OLD'                => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_OLD],
    'PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE'   => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE],
    'PLAYER_OPTION_DESIGN_DISABLE_BORDERS'      => classSupernova::$user_options[PLAYER_OPTION_DESIGN_DISABLE_BORDERS],
    'PLAYER_OPTION_TECH_TREE_TABLE'             => classSupernova::$user_options[PLAYER_OPTION_TECH_TREE_TABLE],
    'sound_enabled'                             => classSupernova::$user_options[PLAYER_OPTION_SOUND_ENABLED],
    'PLAYER_OPTION_ANIMATION_DISABLED'          => classSupernova::$user_options[PLAYER_OPTION_ANIMATION_DISABLED],
    'PLAYER_OPTION_PROGRESS_BARS_DISABLED'      => classSupernova::$user_options[PLAYER_OPTION_PROGRESS_BARS_DISABLED],
    'PLAYER_OPTION_FLEET_SHIP_SELECT_OLD'       => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SELECT_OLD],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED'       => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY'    => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY],
    'PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION' => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION],

    'ADM_PROTECT_PLANETS' => $user['authlevel'] >= 3,
    'opt_usern_data'      => htmlspecialchars($user['username']),
    'opt_mail1_data'      => $user['email'],
    'opt_mail2_data'      => sys_safe_output(classSupernova::$auth->account->account_email),
    'OPT_DPATH_DATA'      => $user['dpath'],

    'PLAYER_OPTION_PLANET_SORT_INVERSE'    => classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE],
    'PLAYER_OPTION_FLEET_SPY_DEFAULT'      => classSupernova::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'PLAYER_OPTION_TOOLTIP_DELAY'          => classSupernova::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE' => classSupernova::$user_options[PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE],

    'PLAYER_OPTION_NAVBAR_PLANET_VERTICAL'       => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_PLANET_VERTICAL],
    'PLAYER_OPTION_NAVBAR_RESEARCH_WIDE'         => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_RESEARCH_WIDE],
    'PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS'   => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS],
    'PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS' => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS],
    'PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH'      => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH],
    'PLAYER_OPTION_NAVBAR_DISABLE_PLANET'        => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_PLANET],
    'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR'        => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_HANGAR],
    'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS'        => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_QUESTS],
    'PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER'   => classSupernova::$user_options[PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER],

    'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'" : '',
    'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'" : '',
    'deltime'        => $user['deltime'],
    'deltime_text'   => sprintf($str_date_format, $time_now_parsed['year'], classLocale::$lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),

    'opt_avatar' => $user['avatar'],

    'config_game_email_pm' => classSupernova::$config->game_email_pm,

    'user_settings_esp'        => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING],
    'user_settings_mis'        => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_MISSILE],
    'user_settings_wri'        => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM],
    'user_settings_statistics' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_STATS],
    'user_settings_info'       => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PROFILE],
    'user_settings_bud'        => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY],

    'user_time_diff_forced' => $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED],
    // '_user_time_diff' => SN_CLIENT_TIME_DIFF,

    'adm_pl_prot' => $user['admin_protection'],

    'user_birthday' => $user['user_birthday'],
    'GENDER'        => $user['gender'],
    'GENDER_TEXT'   => classLocale::$lang['sys_gender_list'][$user['gender']],
    'FMT_DATE'      => $FMT_DATE,
    'JS_FMT_DATE'   => js_safe_string($FMT_DATE),

    'USER_VACATION_DISABLE' => classSupernova::$config->user_vacation_disable,
    'VACATION_NEXT'         => $user['vacation_next'],
    'VACATION_NEXT_TEXT'    => date(FMT_DATE_TIME, $user['vacation_next']),
    'VACATION_TIMEOUT'      => $user['vacation_next'] - SN_TIME_NOW > 0 ? $user['vacation_next'] - SN_TIME_NOW : 0,
    'SN_TIME_NOW'           => SN_TIME_NOW,

    'SERVER_SEND_EMAIL' => classSupernova::$config->game_email_pm,

    'SERVER_NAME_CHANGE'         => classSupernova::$config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE,
    'SERVER_NAME_CHANGE_PAY'     => classSupernova::$config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY,
    'SERVER_NAME_CHANGE_ENABLED' => classSupernova::$config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_FREE || (classSupernova::$config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY && mrc_get_level($user, $planetrow, RES_DARK_MATTER) >= classSupernova::$config->game_user_changename_cost),

    'DARK_MATTER' => pretty_number(classSupernova::$config->game_user_changename_cost, true, mrc_get_level($user, $planetrow, RES_DARK_MATTER)),

    'PAGE_HEADER' => classLocale::$lang['opt_header'],
  ));

  foreach($user_option_list as $option_group_id => $option_group) {
    if($option_group_id == OPT_MESSAGE) {
      foreach(DBStaticMessages::$snMessageClassList as $message_class_id => $message_class_data) {
        if($message_class_data['switchable'] || ($message_class_data['email'] && classSupernova::$config->game_email_pm)) {
          $option_name = $message_class_data['name'];

          $template->assign_block_vars("options_{$option_group_id}", array(
            'NAME'  => $message_class_data['name'],
            'TEXT'  => classLocale::$lang['msg_class'][$message_class_id],
            'PM'    => $message_class_data['switchable'] ? $user["opt_{$option_name}"] : -1,
            'EMAIL' => $message_class_data['email'] && classSupernova::$config->game_email_pm ? $user["opt_email_{$option_name}"] : -1,
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
          'TEXT'  => classLocale::$lang['opt_custom'][$option_name],
          'HINT'  => classLocale::$lang['opt_custom']["{$option_name}_hint"],
          'VALUE' => $user[$option_name],
        ));
      }
    }
  }

  return parsetemplate($template);
}
