<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

$sn_mvc['i18n']['options'] = array(
  'options' => 'options',
  'messages' => 'messages',
);

$sn_mvc['model']['options'][] = 'sn_options_model';
$sn_mvc['view']['options'][] = 'sn_options_view';

function sn_options_model()
{
  global $user, $user_option_list, $lang, $template_result, $time_now, $config;

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if(sys_get_param_str('mode') == 'change')
  {
    if($user['authlevel'] > 0)
    {
      $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
      doquery ("UPDATE {{planets}} SET `id_level` = '{$planet_protection}' WHERE `id_owner` = '{$user['id']}';");
      doquery ("UPDATE {{users}} SET `admin_protection` = '{$planet_protection}' WHERE `id` = '{$user['id']}';");
      $user['admin_protection'] = $planet_protection;
    }

    if(sys_get_param_int('vacation') && !$config->user_vacation_disable)
    {
      if($user['authlevel'] < 3)
      {
        if($user['vacation_next'] > $time_now)
        {
          message($lang['opt_vacation_err_timeout'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }

        $is_building = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_owner` = '{$user['id']}' LIMIT 1;", true);

        if($is_building)
        {
          message($lang['opt_vacation_err_your_fleet'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }
        elseif($user['que'])
        {
          message($lang['opt_vacation_err_research'], $lang['Error'], 'index.php?page=options', 5);
          die();
        }
        else
        {
          $query = doquery("SELECT * FROM `{{planets}}` WHERE `id_owner` = '{$user['id']}';");
          while($planet = mysql_fetch_assoc($query))
          {
            $global_data = sys_o_get_updated($user, $planet, $time_now, true);
            $planet = $global_data['planet'];
            if(($planet['que']) || ($planet['b_hangar'] || $planet['b_hangar_id']))
            {
              message(sprintf($lang['opt_vacation_err_building'], $planet['name']), $lang['Error'], 'index.php?page=options', 5);
              die();
            }
          }
        }

        $query = doquery("SELECT * FROM {{planets}} WHERE id_owner = '{$user['id']}' FOR UPDATE;");
        while($planet = mysql_fetch_assoc($query))
        {
          $planet = sys_o_get_updated($user, $planet, $time_now);
          $planet = $planet['planet'];

          doquery("UPDATE {{planets}} SET
            last_update = '{$time_now}',
            metal_perhour = '{$config->metal_basic_income}', crystal_perhour = '{$config->crystal_basic_income}', deuterium_perhour = '{$config->deuterium_basic_income}',
            energy_used = '0', energy_max = '0',
            metal_mine_porcent = '0', crystal_mine_porcent = '0', deuterium_sintetizer_porcent = '0', solar_plant_porcent = '0',
            fusion_plant_porcent = '0', solar_satelit_porcent = '0'
          WHERE id = '{$planet['id']}' LIMIT 1;");
        }
        $user['vacation'] = $time_now + $config->player_vacation_time;
      }
      else
      {
        $user['vacation'] = $time_now;
      }
    }

    foreach($user_option_list as $option_group_id => $option_group)
    {
      foreach($option_group as $option_name => $option_value)
      {
        if($user[$option_name] !== null)
        {
          $user[$option_name] = sys_get_param_str($option_name);
        }
        else
        {
          $user[$option_name] = $option_value;
        }
      }
    }
    $options = sys_user_options_pack($user);

    $username = substr(sys_get_param_str_raw('username'), 0, 32);
    $username_safe = mysql_real_escape_string($username);
    if($username && $user['username'] != $username && $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE && sys_get_param_int('username_confirm'))
    {
    // проверка на корректность
      sn_db_transaction_start();
      $name_check = doquery("SELECT * FROM {{player_name_history}} WHERE `player_name` LIKE \"{$username_safe}\" LIMIT 1 FOR UPDATE;", true);
      if(!$name_check || $name_check['player_id'] == $user['id'])
      {
        $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE", true);
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
            doquery("UPDATE {{users}} SET `username` = \"{$username_safe}\" WHERE `id` = {$user['id']}");
            doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user['id']}, `player_name` = \"{$username_safe}\"");
            // TODO: Change cookie to not force user relogin
            setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
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
    if($new_password)
    {
      try
      {
        if(md5(sys_get_param('db_password')) != $user['password'])
        {
          throw new Exception($lang['opt_err_pass_wrong'], ERR_WARNING);
        }

        if($new_password != sys_get_param('newpass2'))
        {
          throw new Exception($lang['opt_err_pass_unmatched'], ERR_WARNING);
        }

        $user['password'] = md5($new_password);
        // TODO: Change cookie to not force user relogin
        setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
        throw new Exception($lang['opt_msg_pass_changed'], ERR_NONE);
      }
      catch (Exception $e)
      {
        $template_result['.']['result'][] = array(
          'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
          'MESSAGE' => $e->getMessage()
        );
      }
    }

    $user['email'] = sys_get_param_str('db_email');
    $user['dpath'] = sys_get_param_str('dpath');
    $user['lang']  = sys_get_param_str('langer', $language);
    if(lng_switch($user['lang']))
    {
      lng_include('options');
      lng_include('messages');
    }

    $user['design'] = sys_get_param_int('design');
    $user['noipcheck'] = sys_get_param_int('noipcheck');
    $user['spio_anz'] = sys_get_param_int('spio_anz');
    $user['settings_tooltiptime'] = sys_get_param_int('settings_tooltiptime');
    $user['settings_fleetactions'] = sys_get_param_int('settings_fleetactions', 1);
    $user['settings_esp'] = sys_get_param_int('settings_esp');
    $user['settings_wri'] = sys_get_param_int('settings_wri');
    $user['settings_bud'] = sys_get_param_int('settings_bud');
    $user['settings_mis'] = sys_get_param_int('settings_mis');
    $user['settings_rep'] = sys_get_param_int('settings_rep');
    $user['planet_sort']  = sys_get_param_int('settings_sort');
    $user['planet_sort_order'] = sys_get_param_int('settings_order');
    $user['deltime'] = !sys_get_param_int('deltime') ? 0 : ($user['deltime'] ? $user['deltime'] : $time_now + $config->player_delete_time);

    try
    {
      if($user['birthday'])
      {
        throw new exception();
      }

      $user_birthday = sys_get_param_str_raw('user_birthday');
      if(!$user_birthday || $user_birthday == $FMT_DATE)
      {
        throw new exception();
      }

      // Some black magic to parse any valid date format - those that contains all three "d", "m" and "Y" and any of the delimeters "\", "/", ".", "-"
      $pos['d'] = strpos(FMT_DATE, 'd');
      $pos['m'] = strpos(FMT_DATE, 'm');
      $pos['Y'] = strpos(FMT_DATE, 'Y');
      asort($pos);
      $i = 0;
      foreach($pos as &$position)
      {
        $position = ++$i;
      }

      $regexp = "/" . preg_replace(array('/\\\\/', '/\//', '/\./', '/\-/', '/d/', '/m/', '/Y/'), array('\\\\\\', '\/', '\.', '\-', '(\d?\d)', '(\d?\d)', '(\d{4})'), FMT_DATE) . "/";
      if(!preg_match($regexp, $user_birthday, $match))
      {
        throw new exception();
      }

      if(!checkdate($match[$pos['m']], $match[$pos['d']], $match[$pos['Y']]))
      {
        throw new exception();
      }

      $user['user_birthday'] = mysql_real_escape_string("{$match[$pos['Y']]}-{$match[$pos['m']]}-{$match[$pos['d']]}");
      // EOF black magic! Now we have valid MYSQL date in $user['user_birthday'] - independent of date format

      $year = date('Y', $time_now);
      if(mktime(0, 0, 0, $match[$pos['m']], $match[$pos['d']], $year) > $time_now)
      {
        $year--;
      }
      $user['user_birthday_celebrated'] = mysql_real_escape_string("{$year}-{$match[$pos['m']]}-{$match[$pos['d']]}");

      $user_birthday = ", `user_birthday` = '{$user['user_birthday']}', `user_birthday_celebrated` = '{$user['user_birthday_celebrated']}'";
    }
    catch (exception $e)
    {
      $user_birthday = '';
    }

    require_once('includes/includes/sys_avatar.php');

    $avatar_upload_result = sys_avatar_upload($user['id'], $user['avatar']);
    $template_result['.']['result'][] = $avatar_upload_result;

    $user_birthday .= sys_get_param_int('opt_time_diff_clear') ? ', `user_time_diff` = NULL' : '';

//      `username` = '{$username_safe}',
    doquery("UPDATE {{users}} SET
      `password` = '{$user['password']}',
      `email` = '{$user['email']}',
      `lang` = '{$user['lang']}',
      `avatar` = '{$user['avatar']}',
      `dpath` = '{$user['dpath']}',
      `design` = '{$user['design']}',
      `noipcheck` = '{$user['noipcheck']}',
      `planet_sort` = '{$user['planet_sort']}',
      `planet_sort_order` = '{$user['planet_sort_order']}',
      `spio_anz` = '{$user['spio_anz']}',
      `settings_tooltiptime` = '{$user['settings_tooltiptime']}',
      `settings_fleetactions` = '{$user['settings_fleetactions']}',
      `settings_esp` = '{$user['settings_esp']}',
      `settings_wri` = '{$user['settings_wri']}',
      `settings_bud` = '{$user['settings_bud']}',
      `settings_mis` = '{$user['settings_mis']}',
      `settings_rep` = '{$user['settings_rep']}',
      `deltime` = '{$user['deltime']}',
      `vacation` = '{$user['vacation']}',
      `options` = '{$user['options']}'
      {$user_birthday}
    WHERE `id` = '{$user['id']}' LIMIT 1");

    $template_result['.']['result'][] = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => $lang['opt_msg_saved']
    );
  }
}

//-------------------------------

function sn_options_view($template = null)
{
  global $lang, $template_result, $user, $planetrow, $user_option_list, $user_option_types, $sn_message_class_list, $config, $time_now;

  sys_user_vacation($user);

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  $template = gettemplate('options', $template);

//  $template_result['.']['skin_list'][] = array(
//    'NAME'  => $lang['select_skin_path'],
//    'VALUE' => '',
//  );

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  while(($entry = $dir->read()) !== false)
  {
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

  for($i = 0; $i < 2; $i++)
  {
    $template_result['.']['planet_order'][] = array(
      'VALUE' => $i,
      'NAME'  => $lang['opt_lst_cla' . $i],
      'SELECTED' => $user['planet_sort_order'] == $i,
    );
  }
  for($i = 0; $i < 4; $i++)
  {
    $template_result['.']['planet_order_type'][] = array(
      'VALUE' => $i,
      'NAME'  => $lang['opt_lst_ord' . $i],
      'SELECTED' => $user['planet_sort'] == $i,
    );
  }

  $lang_list = lng_get_list();
  foreach($lang_list as $lang_id => $lang_data)
  {
    $template_result['.']['languages'][] = array(
      'VALUE' => $lang_id,
      'NAME'  => $lang_data['LANG_NAME_NATIVE'],
      'SELECTED' => $lang_id == $user['lang'],
    );
  }








  $str_date_format = "%3$02d %2$0s %1$04d {$lang['top_of_year']} %4$02d:%5$02d:%6$02d";
  $time_now_parsed = getdate($user['deltime']);





  $template->assign_vars(array(
    'USER_ID'        => $user['id'],

    'USER_AUTHLEVEL'           => $user['authlevel'],

    'ADM_PROTECT_PLANETS' => $user['authlevel'] >= 3,
    'opt_usern_data' => htmlspecialchars($user['username']),
    'opt_mail1_data' => $user['email'],
    'opt_mail2_data' => $user['email_2'],
    'OPT_DPATH_DATA' => $user['dpath'],
    'opt_probe_data' => $user['spio_anz'],
    'opt_toolt_data' => $user['settings_tooltiptime'],
    'opt_fleet_data' => $user['settings_fleetactions'],
    'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'":'',
    'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'":'',
    'deltime'        => $user['deltime'],
    'deltime_text'   => sprintf($str_date_format, $time_now_parsed['year'], $lang['months'][$time_now_parsed['mon']], $time_now_parsed['mday'],
      $time_now_parsed['hours'], $time_now_parsed['minutes'], $time_now_parsed['seconds']
    ),

    'opt_avatar'     => $user['avatar'],

    'user_settings_rep' => ($user['settings_rep'] == 1) ? " checked='checked'/":'',
    'user_settings_esp' => ($user['settings_esp'] == 1) ? " checked='checked'/":'',
    'user_settings_wri' => ($user['settings_wri'] == 1) ? " checked='checked'/":'',
    'user_settings_mis' => ($user['settings_mis'] == 1) ? " checked='checked'/":'',
    'user_settings_bud' => ($user['settings_bud'] == 1) ? " checked='checked'/":'',

    'adm_pl_prot' => $user['admin_protection'],

    'user_birthday' => $user['user_birthday'],
    'FMT_DATE' => $FMT_DATE,
    'JS_FMT_DATE' => js_safe_string($FMT_DATE),

    'USER_VACATION_DISABLE' => $config->user_vacation_disable,
    'VACATION_NEXT' => $user['vacation_next'],
    'VACATION_NEXT_TEXT' => date(FMT_DATE_TIME, $user['vacation_next']),
    'VACATION_TIMEOUT' => $user['vacation_next'] - $time_now > 0 ? $user['vacation_next'] - $time_now : 0,
    'TIME_NOW' => $time_now,

    'SERVER_NAME_CHANGE' => $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_NONE,
    'SERVER_NAME_CHANGE_PAY' => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY,
    'SERVER_NAME_CHANGE_ENABLED' => $config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_FREE || ($config->game_user_changename == SERVER_PLAYER_NAME_CHANGE_PAY && mrc_get_level($user, $planetrow, RES_DARK_MATTER) >= $config->game_user_changename_cost),

    'DARK_MATTER' => pretty_number($config->game_user_changename_cost, true, mrc_get_level($user, $planetrow, RES_DARK_MATTER)),

    'PAGE_HEADER' => $lang['opt_header'],
  ));

  foreach($user_option_list as $option_group_id => $option_group)
  {
    if($option_group_id == OPT_MESSAGE)
    {
      foreach($sn_message_class_list as $message_class_id => $message_class_data)
      {
        if($message_class_data['switchable'] || ($message_class_data['email'] && $config->game_email_pm))
        {
          $option_name = $message_class_data['name'];

          $template->assign_block_vars("options_{$option_group_id}", array(
            'NAME'  => $message_class_data['name'],
            'TEXT'  => $lang['msg_class'][$message_class_id], // $lang['opt_custom'][$option_name],
            'PM' => $message_class_data['switchable'] ? $user["opt_{$option_name}"] : -1,
            'EMAIL' => $message_class_data['email'] && $config->game_email_pm ? $user["opt_email_{$option_name}"] : -1,
          ));
        }
      }
    }
    else
    {
      foreach($option_group as $option_name => $option_value)
      {
        if(array_key_exists($option_name, $user_option_types))
        {
          $option_type = $user_option_types[$option_name];
        }
        else
        {
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
