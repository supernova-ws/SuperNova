<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */
/*
$sn_mvc['model']['options'][] = 'sn_options_model';
$sn_mvc['view']['options'][] = 'sn_options_view';

$sn_mvc['i18n']['options'] = array(
  'options' => 'options',
  'messages' => 'messages',
);
 */

function sn_options_model() {
  global $user, $user_option_list, $lang, $template_result, $config;

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if(sys_get_param_str('mode') == 'change') {
    if($user['authlevel'] > 0) {
      $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
      db_planet_set_by_owner($user['id'], "`id_level` = '{$planet_protection}'");
      db_user_set_by_id($user['id'], "`admin_protection` = '{$planet_protection}'");
      $user['admin_protection'] = $planet_protection;
    }

    if(sys_get_param_int('vacation') && !$config->user_vacation_disable) {
      sn_db_transaction_start();
      if($user['authlevel'] < 3) {
        if($user['vacation_next'] > SN_TIME_NOW) {
          message($lang['opt_vacation_err_timeout'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $is_building = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_owner` = '{$user['id']}' LIMIT 1;", true);

        if($is_building) {
          message($lang['opt_vacation_err_your_fleet'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $que = que_get($user['id'], false);
        if(!empty($que)) {
          message($lang['opt_vacation_err_que'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $query = classSupernova::db_get_record_list(LOC_PLANET, "`id_owner` = {$user['id']}");
        foreach($query as $planet)
        {
          // $planet = sys_o_get_updated($user, $planet, SN_TIME_NOW);
          // $planet = $planet['planet'];

          db_planet_set_by_id($planet['id'],
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
      array_walk($player_options, function(&$value){
        // TODO - Когда будет больше параметров - сделать больше проверок
        $value = intval($value);
      });
      classSupernova::$user_options->__set($player_options);
      // pdump($player_options);die();
      //      player_save_option_array($user, $player_options);
    }

    $username = substr(sys_get_param_str_unsafe('username'), 0, 32);
    $username_safe = db_escape($username);
    if($username && $user['username'] != $username && $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE && sys_get_param_int('username_confirm') && !strpbrk($username, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
    // проверка на корректность
      sn_db_transaction_start();
      $name_check = doquery("SELECT * FROM {{player_name_history}} WHERE `player_name` LIKE \"{$username_safe}\" LIMIT 1 FOR UPDATE;", true);
      if(!$name_check || $name_check['player_id'] == $user['id'])
      {
        $user = db_user_by_id($user['id'], true);
        switch($config->game_user_changename)
        {
          case SERVER_PLAYER_NAME_CHANGE_PAY:
            if(mrc_get_level($user, $planetrow, RES_DARK_MATTER) < $config->game_user_changename_cost)
            {
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
      }
      else
      {
        $template_result['.']['result'][] = array(
          'STATUS'  => ERR_ERROR,
          'MESSAGE' => $lang['opt_msg_name_change_err_used_name'],
        );
      }
      sn_db_transaction_commit();
    }

    $new_password = sys_get_param('newpass1');
    if($new_password) {
      try {
        if($new_password != sys_get_param('newpass2')) {
          throw new Exception($lang['opt_err_pass_unmatched'], ERR_WARNING);
        }

        if(!auth::password_change(sys_get_param('db_password'), $new_password)) {
          throw new Exception($lang['opt_err_pass_wrong'], ERR_WARNING);
        }

        throw new Exception($lang['opt_msg_pass_changed'], ERR_NONE);
      } catch (Exception $e) {
        $template_result['.']['result'][] = array(
          'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
          'MESSAGE' => $e->getMessage()
        );
      }
    }

    $user['email'] = sys_get_param_str('db_email');
//    if(!$template_result[F_ACCOUNT]['account_email'] && ($email_2 = sys_get_param_str('db_email2'))) {
//      auth::email_set($email_2);
//    }
    $user['dpath'] = sys_get_param_str('dpath');
    $user['lang']  = sys_get_param_str('langer', $user['lang']);

    if($lang->lng_switch($user['lang'])) {
      lng_include('options');
      lng_include('messages');
    }

    $user['design'] = sys_get_param_int('design');
    $user['noipcheck'] = sys_get_param_int('noipcheck');
    // $user['spio_anz'] = sys_get_param_int('spio_anz');
    // $user['settings_fleetactions'] = sys_get_param_int('settings_fleetactions', 1);
    // $user['settings_tooltiptime'] = sys_get_param_int('settings_tooltiptime');
    // $user['settings_esp'] = sys_get_param_int('settings_esp');
    // $user['settings_wri'] = sys_get_param_int('settings_wri');
    // $user['settings_bud'] = sys_get_param_int('settings_bud');
    // $user['settings_mis'] = sys_get_param_int('settings_mis');
    // $user['settings_statistics'] = sys_get_param_int('settings_statistics');
    // $user['settings_info'] = sys_get_param_int('settings_info');
    // $user['settings_rep'] = sys_get_param_int('settings_rep');
    // $user['planet_sort']  = sys_get_param_int('settings_sort');
    // $user['planet_sort_order'] = sys_get_param_int('settings_order');
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
    } catch (exception $e) {
      $user_birthday = '';
    }

    require_once('includes/includes/sys_avatar.php');

    $avatar_upload_result = sys_avatar_upload($user['id'], $user['avatar']);
    $template_result['.']['result'][] = $avatar_upload_result;

    $user_time_diff = user_time_diff_get();
    if(sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED')) {
      user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF => sys_get_param_int('PLAYER_OPTION_TIME_DIFF'),
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED => 1,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    } elseif(sys_get_param_int('opt_time_diff_clear') || $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
      user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF => '',
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED => 0,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    }

    $user_options_safe = db_escape($user['options']);
    // `spio_anz` = '{$user['spio_anz']}', `settings_fleetactions` = '{$user['settings_fleetactions']}',
    // `settings_esp` = '{$user['settings_esp']}', `settings_mis` = '{$user['settings_mis']}', `settings_wri` = '{$user['settings_wri']}',
    // `settings_statistics` = '{$user['settings_statistics']}', `settings_info` = '{$user['settings_info']}', `settings_bud` = '{$user['settings_bud']}',
    // `settings_rep` = '{$user['settings_rep']}', `settings_tooltiptime` = '{$user['settings_tooltiptime']}',
    // `planet_sort` = '{$user['planet_sort']}', `planet_sort_order` = '{$user['planet_sort_order']}',
    db_user_set_by_id($user['id'], "`email` = '{$user['email']}', `lang` = '{$user['lang']}', `avatar` = '{$user['avatar']}',
      `dpath` = '{$user['dpath']}', `design` = '{$user['design']}', `noipcheck` = '{$user['noipcheck']}',
      `deltime` = '{$user['deltime']}', `vacation` = '{$user['vacation']}', `options` = '{$user_options_safe }', `gender` = {$user['gender']}
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

//  $template_result['.']['skin_list'][] = array(
//    'NAME'  => $lang['select_skin_path'],
//    'VALUE' => '',
//  );

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  while(($entry = $dir->read()) !== false) {
    if(is_dir("skins/{$entry}") && $entry[0] !='.')
    {
      $template_result['.']['skin_list'][] = array(
        'VALUE' => $entry,
        'NAME'  => $entry,
        'SELECTED' => $user['dpath'] == "skins/{$entry}/",
      );
    }
  }
  $dir->close();

  foreach($lang['opt_planet_sort_options'] as $key => &$value) {
    $template_result['.']['planet_sort_options'][] = array(
      'VALUE' => $key,
      'NAME'  => $value,
      'SELECTED' => classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT] == $key,
    );
  }
/*
  foreach($lang['opt_planet_sort_ascending'] as $key => &$value) {
    $template_result['.']['planet_sort_ascending'][] = array(
      'VALUE' => $key,
      'NAME'  => $value,
      'SELECTED' => classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE] == $key,
    );
  }
*/
  foreach($lang['sys_gender_list'] as $key => $value) {
    $template_result['.']['gender_list'][] = array(
      'VALUE' => $key,
      'NAME'  => $value,
      'SELECTED' => $user['gender'] == $key,
    );
  }

  $lang_list = lng_get_list();
  foreach($lang_list as $lang_id => $lang_data) {
    $template_result['.']['languages'][] = array(
      'VALUE' => $lang_id,
      'NAME'  => $lang_data['LANG_NAME_NATIVE'],
      'SELECTED' => $lang_id == $user['lang'],
    );
  }




  if(isset($lang['menu_customize_show_hide_button_state'])) {
    foreach($lang['menu_customize_show_hide_button_state'] as $key => $value) {
      $template->assign_block_vars('menu_customize_show_hide_button_state', array(
        'ID' => $key,
        'NAME' => $value,
      ));
    }
  }




  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate($user['deltime']);


//  $account = db_account_by_user_id($user['id']);


//  pdump($template_result);
//  pdump($template_result[F_USER_ID], F_USER_ID);
//  pdump($template_result[F_PROVIDER_ID], F_PROVIDER_ID);
//  pdump(sys_safe_output($template_result[F_ACCOUNT]['account_name']));

  $user_time_diff = user_time_diff_get();
  // $player_options = player_load_option($user);
  $template->assign_vars(array(
    'USER_ID'        => $user['id'],

    'AUTH_PROVIDER' => $template_result[F_PROVIDER_ID],
    'ACCOUNT_NAME' => sys_safe_output($template_result[F_ACCOUNT]['account_name']),

//    'ACCOUNT_NAME' => sys_safe_output($account['account_name']),

    'USER_AUTHLEVEL'           => $user['authlevel'],

//    'menu_customize_show_hide_button' => isset($player_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON])
//      ? $player_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON] : 0,
//    'menu_customize_show_button_enter' => isset($player_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON])
//      ? $player_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON] : 0,
//    'menu_customize_hide_button_enter' => isset($player_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON])
//      ? $player_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON] : 0,
//    'menu_customize_hide_unpinned_on_exit' => isset($player_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE])
//      ? $player_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE] : 0,
//    'menu_customize_show_absolute' => isset($player_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE])
//      ? $player_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE] : 0,
//    'menu_customize_items_as_buttons' => isset($player_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS])
//      ? $player_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS] : 0,
//    'sound_enabled' => isset($player_options[PLAYER_OPTION_SOUND_ENABLED])
//      ? $player_options[PLAYER_OPTION_SOUND_ENABLED] : 0,
    'menu_customize_show_hide_button' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON],
    'menu_customize_show_button_enter' => classSupernova::$user_options[PLAYER_OPTION_MENU_SHOW_ON_BUTTON],
    'menu_customize_hide_button_enter' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_BUTTON],
    'menu_customize_hide_unpinned_on_exit' => classSupernova::$user_options[PLAYER_OPTION_MENU_HIDE_ON_LEAVE],
    'menu_customize_show_absolute' => classSupernova::$user_options[PLAYER_OPTION_MENU_UNPIN_ABSOLUTE],
    'menu_customize_items_as_buttons' => classSupernova::$user_options[PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS],
    'sound_enabled' => classSupernova::$user_options[PLAYER_OPTION_SOUND_ENABLED],

    'ADM_PROTECT_PLANETS' => $user['authlevel'] >= 3,
    'opt_usern_data' => htmlspecialchars($user['username']),
    'opt_mail1_data' => $user['email'],
    'opt_mail2_data' => sys_safe_output($template_result[F_ACCOUNT]['account_email']),
    'OPT_DPATH_DATA' => $user['dpath'],

    'PLAYER_OPTION_PLANET_SORT_INVERSE' => classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE],
    // 'opt_fleet_data' => $user['settings_fleetactions'],
    // 'opt_fleet_data' => classSupernova::$user_options[PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX],
    // 'opt_probe_data' => $user['spio_anz'],
    // 'opt_toolt_data' => $user['settings_tooltiptime'],
    'PLAYER_OPTION_FLEET_SPY_DEFAULT' => classSupernova::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'PLAYER_OPTION_TOOLTIP_DELAY' => classSupernova::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'":'',
    'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'":'',
    'deltime'        => $user['deltime'],
    'deltime_text'   => sprintf($str_date_format, $time_now_parsed['year'], $lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),

    'opt_avatar'     => $user['avatar'],

    'config_game_email_pm'     => $config->game_email_pm,

    // 'user_settings_rep' => ($user['settings_rep'] == 1) ? " checked='checked'/":'', // UNUSED
    // 'user_settings_esp' => ($user['settings_esp'] == 1) ? " checked='checked'/":'',
    // 'user_settings_mis' => ($user['settings_mis'] == 1) ? " checked='checked'/":'',
    // 'user_settings_wri' => ($user['settings_wri'] == 1) ? " checked='checked'/":'',
    // 'user_settings_statistics' => ($user['settings_statistics'] == 1) ? " checked='checked'/":'',
    // 'user_settings_info' => ($user['settings_info'] == 1) ? " checked='checked'/":'',
    // 'user_settings_bud' => ($user['settings_bud'] == 1) ? " checked='checked'/":'',
    'user_settings_esp' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING],
    'user_settings_mis' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_MISSILE],
    'user_settings_wri' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM],
    'user_settings_statistics' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_STATS],
    'user_settings_info' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PROFILE],
    'user_settings_bud' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY],

    'user_time_diff_forced' => $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED],
    // '_user_time_diff' => SN_CLIENT_TIME_DIFF,

    'adm_pl_prot' => $user['admin_protection'],

    'user_birthday' => $user['user_birthday'],
    'GENDER' => $user['gender'],
    'GENDER_TEXT' => $lang['sys_gender_list'][$user['gender']],
    'FMT_DATE' => $FMT_DATE,
    'JS_FMT_DATE' => js_safe_string($FMT_DATE),

    'USER_VACATION_DISABLE' => $config->user_vacation_disable,
    'VACATION_NEXT' => $user['vacation_next'],
    'VACATION_NEXT_TEXT' => date(FMT_DATE_TIME, $user['vacation_next']),
    'VACATION_TIMEOUT' => $user['vacation_next'] - SN_TIME_NOW > 0 ? $user['vacation_next'] - SN_TIME_NOW : 0,
    'TIME_NOW' => SN_TIME_NOW,

    'SERVER_SEND_EMAIL' => $config->game_email_pm,

    'SERVER_NAME_CHANGE' => $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE,
    'SERVER_NAME_CHANGE_PAY' => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY,
    'SERVER_NAME_CHANGE_ENABLED' => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_FREE || ($config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY && mrc_get_level($user, $planetrow, RES_DARK_MATTER) >= $config->game_user_changename_cost),

    'DARK_MATTER' => pretty_number($config->game_user_changename_cost, true, mrc_get_level($user, $planetrow, RES_DARK_MATTER)),

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
            'PM' => $message_class_data['switchable'] ? $user["opt_{$option_name}"] : -1,
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

  return parsetemplate($template);
}
