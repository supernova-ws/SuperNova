<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */
/*
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('options');
lng_include('messages');

$template_result = array();
sn_options_model();
sn_options_view();
*/

$sn_i18n['pages']['options'] = array(
  'options' => 'options',
  'messages' => 'messages',
);

$sn_mvc['model']['options'][] = 'sn_options_model';
$sn_mvc['view']['options'][] = 'sn_options_view';

function sn_options_model()
{
  global $user, $user_option_list, $lang, $template_result, $time_now;

  $FMT_DATE = preg_replace(array('/d/', '/m/', '/Y/'), array('DD', 'MM', 'YYYY'), FMT_DATE);

  if(sys_get_param_str('mode') == 'change')
  {
    if($user['authlevel'] > 0)
    {
      $planet_protection = sys_get_param_int('adm_pl_prot') ? $user['authlevel'] : 0;
      doquery ("UPDATE {{planets}} SET `id_level` = '{$planet_protection}' WHERE `id_owner` = '{$user['id']}';");
    }

    if(sys_get_param_int('vacation') && !$config->user_vacation_disable)
    {
      if($user['authlevel'] < 3)
      {
        $is_building = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_owner` = '{$user['id']}' LIMIT 1;", '', true);

        if($is_building)
        {
          message($lang['opt_vacation_err_your_fleet'], $lang['Error'], 'options.php', 5);
          die();
        }
        elseif($user['que'])
        {
          message($lang['opt_vacation_err_research'], $lang['Error'], 'options.php', 5);
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
              message(sprintf($lang['opt_vacation_err_building'], $planet['name']), $lang['Error'], 'options.php', 5);
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

    $username = sys_get_param_str_raw('username');
    if($username && $user['username'] != $username && $config->game_user_changename)
    {
      $user['username'] = $username;
      $username = mysql_real_escape_string($username);
      // TODO: Change cookie to not force user relogin
      setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
      $template_result['.']['result'][] = array(
        'STATUS'  => ERR_NONE,
        'MESSAGE' => $lang['opt_msg_name_changed']
      );
    }
    else
    {
      $username = mysql_real_escape_string($user['username']);
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

    doquery("UPDATE {{users}} SET
      `username` = '{$username}',
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
  $template->assign_recursive($template_result);

  $dir = dir(SN_ROOT_PHYSICAL . 'skins');
  $parse['opt_lst_skin_data'] = "<option value =\"\">{$lang['select_skin_path']}</option>";
  while(($entry = $dir->read()) !== false)
  {
    if(is_dir("skins/{$entry}") && $entry[0] !='.')
    {
      $parse['opt_lst_skin_data'] .= "<option value =\"{$entry}\">{$entry}</option>";
    }
  }
  $dir->close();

  //  $parse['opt_lst_skin_data']  = "<option value =\"skins/xnova/\">skins/xnova/</option>";
  $parse['opt_lst_ord_data']  = "<option value =\"0\"". (($user['planet_sort'] == 0) ? " selected": "") .">". $lang['opt_lst_ord0'] ."</option>";
  $parse['opt_lst_ord_data'] .= "<option value =\"1\"". (($user['planet_sort'] == 1) ? " selected": "") .">". $lang['opt_lst_ord1'] ."</option>";
  $parse['opt_lst_ord_data'] .= "<option value =\"2\"". (($user['planet_sort'] == 2) ? " selected": "") .">". $lang['opt_lst_ord2'] ."</option>";
  $parse['opt_lst_ord_data'] .= "<option value =\"3\"". (($user['planet_sort'] == 3) ? " selected": "") .">". $lang['opt_lst_ord3'] ."</option>";

  $parse['opt_lst_cla_data']  = "<option value =\"0\"". (($user['planet_sort_order'] == 0) ? " selected": "") .">". $lang['opt_lst_cla0'] ."</option>";
  $parse['opt_lst_cla_data'] .= "<option value =\"1\"". (($user['planet_sort_order'] == 1) ? " selected": "") .">". $lang['opt_lst_cla1'] ."</option>";

  $lang_list = lng_get_list();
  foreach($lang_list as $lang_id => $lang_data)
  {
    if($lang_id == $user['lang'])
    {
      $selected = 'selected';
    }
    else
    {
      $selected = '';
    }

    $parse['opt_lst_lang_data'] .= "<option value =\"{$lang_id}\" {$selected}>{$lang_data['LANG_NAME_NATIVE']}</option>";
  }

  if($user['authlevel'] >= 3)
  {
    $parse['adm_pl_prot_data'] = ($planetrow['id_level'] > 0) ? " checked='checked'" : '';
  }

  $template->assign_vars(array(
    'USER_ID'        => $user['id'],

    'IS_ADMIN'       => $user['authlevel'] >= 3,
    'opt_usern_data' => $user['username'],
    'opt_mail1_data' => $user['email'],
    'opt_mail2_data' => $user['email_2'],
    'opt_dpath_data' => $user['dpath'],
    'opt_probe_data' => $user['spio_anz'],
    'opt_toolt_data' => $user['settings_tooltiptime'],
    'opt_fleet_data' => $user['settings_fleetactions'],
    'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'":'',
    'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'":'',
    'opt_delac_data' => ($user['deltime'] == 1) ? " checked='checked'/":'',

    'opt_avatar'     => $user['avatar'],

    'user_settings_rep' => ($user['settings_rep'] == 1) ? " checked='checked'/":'',
    'user_settings_esp' => ($user['settings_esp'] == 1) ? " checked='checked'/":'',
    'user_settings_wri' => ($user['settings_wri'] == 1) ? " checked='checked'/":'',
    'user_settings_mis' => ($user['settings_mis'] == 1) ? " checked='checked'/":'',
    'user_settings_bud' => ($user['settings_bud'] == 1) ? " checked='checked'/":'',

    'user_birthday' => $user['user_birthday'],
    'FMT_DATE' => $FMT_DATE,
    'JS_FMT_DATE' => js_safe_string($FMT_DATE),

    'USER_VACATION_DISABLE' => $config->user_vacation_disable,
    'TIME_NOW' => $time_now,

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

  return parsetemplate($template, $parse);
}

?>
