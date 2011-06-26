<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('options');
lng_include('messages');

$template = gettemplate('options', true);

$mode = SYS_mysqlSmartEscape($_GET['mode']);
if($mode == 'change')
{
  // Gestion des options speciales pour les admins
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
      else
      {
        $query = doquery("SELECT * FROM `{{planets}}` WHERE `id_owner` = '{$user['id']}';");
        while($planet = mysql_fetch_assoc($query))
        {
          $global_data = sys_o_get_updated($user, $planet, $time_now, true);
          $planet = $global_data['planet'];
          if(($planet['que']) || ($planet['b_tech'] || $planet['b_tech_id']) || ($planet['b_hangar'] || $planet['b_hangar_id']))
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
          metal_perhour = '{$config->metal_basic_income}',
          crystal_perhour = '{$config->crystal_basic_income}',
          deuterium_perhour = '{$config->deuterium_basic_income}',
          energy_used = '0',
          energy_max = '0',
          metal_mine_porcent = '0',
          crystal_mine_porcent = '0',
          deuterium_sintetizer_porcent = '0',
          solar_plant_porcent = '0',
          fusion_plant_porcent = '0',
          solar_satelit_porcent = '0'
        WHERE id = '{$planet['id']}' LIMIT 1;");
      }
      $user['vacation'] = $time_now + VACATION_TIME;
    }
    else
    {
      $user['vacation'] = $time_now;
    }

//    doquery("UPDATE {{users}} SET `vacation` = '{$user['vacation']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
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
    setcookie(COOKIE_NAME, '', time()-100000, '/', '', 0); //le da el expire
    $template->assign_var('CHANGE_NAME', true);
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
        throw new Exception('', 1);
      }

      if($new_password != sys_get_param('newpass2'))
      {
        throw new Exception('', 2);
      }

      $user['password'] = md5($new_password);
      // TODO: Change cookie to not force user relogin
      setcookie(COOKIE_NAME, '', time()-100000, '/', '', 0); //le da el expire
      $template->assign_var('CHANGE_PASS', -1);
    }
    catch (Exception $e)
    {
      $template->assign_var('CHANGE_PASS', $e->getCode());
    }
  }

  $user['email'] = sys_get_param_str('db_email');
  $user['dpath']  = sys_get_param_str('dpath');
  $user['lang']   = sys_get_param_str('langer');
  $user['avatar'] = sys_get_param_str('avatar');

  $user['design'] = sys_get_param_int('design');
  $user['noipcheck'] = sys_get_param_int('noipcheck');
  $user['spio_anz'] = sys_get_param_int('spio_anz');
  $user['settings_tooltiptime'] = sys_get_param_int('settings_tooltiptime');
  $user['settings_fleetactions'] = sys_get_param_int('settings_fleetactions', 1);
  $user['settings_allylogo'] = sys_get_param_int('settings_allylogo');
  $user['settings_esp'] = sys_get_param_int('settings_esp');
  $user['settings_wri'] = sys_get_param_int('settings_wri');
  $user['settings_bud'] = sys_get_param_int('settings_bud');
  $user['settings_mis'] = sys_get_param_int('settings_mis');
  $user['settings_rep'] = sys_get_param_int('settings_rep');
  $user['planet_sort']  = sys_get_param_int('settings_sort');
  $user['planet_sort_order'] = sys_get_param_int('settings_order');
  $user['deltime'] = !sys_get_param_int('db_deaktjava') ? 0 : ($user['deltime'] ? $user['deltime'] : $time_now + 604800);

  doquery("UPDATE {{users}} SET
    `username` = '{$username}',
    `password` = '{$user['password']}',
    `email` = '{$user['email']}',
    `lang` = '{$user['lang']}',
    `avatar` = '{$user['avatar']}',
    `dpath` = '{$user['dpath']}',
    `design` = '{$design}',
    `noipcheck` = '{$user['noipcheck']}',
    `planet_sort` = '{$user['planet_sort']}',
    `planet_sort_order` = '{$user['planet_sort_order']}',
    `spio_anz` = '{$user['spio_anz']}',
    `settings_tooltiptime` = '{$user['settings_tooltiptime']}',
    `settings_fleetactions` = '{$user['settings_fleetactions']}',
    `settings_allylogo` = '{$user['settings_allylogo']}',
    `settings_esp` = '{$user['settings_esp']}',
    `settings_wri` = '{$user['settings_wri']}',
    `settings_bud` = '{$user['settings_bud']}',
    `settings_mis` = '{$user['settings_mis']}',
    `settings_rep` = '{$user['settings_rep']}',
    `deltime` = '{$user['deltime']}',
    `kolorminus` = '{$user['kolorminus']}',
    `kolorplus` = '{$user['kolorplus']}',
    `kolorpoziom` = '{$user['kolorpoziom']}',
    `vacation` = '{$user['vacation']}',
    `options` = '{$user['options']}'
  WHERE `id` = '{$user['id']}' LIMIT 1");

  $parse['SAVED'] = true;

  sys_user_vacation($user);
}

//-------------------------------
$dir = dir(SN_ROOT_PHYSICAL . 'skins');
$parse['opt_lst_skin_data']="<option value =\"\">{$lang['select_skin_path']}</option>";
while (false !== ($entry = $dir->read())) {
  if (is_dir("skins/{$entry}") && $entry[0] !='.') {
    $parse['opt_lst_skin_data'].="<option value =\"{$entry}\">{$entry}</option>";
  }
}
$dir->close();

//  $parse['opt_lst_skin_data']  = "<option value =\"skins/xnova/\">skins/xnova/</option>";
$parse['opt_lst_ord_data']   = "<option value =\"0\"". (($user['planet_sort'] == 0) ? " selected": "") .">". $lang['opt_lst_ord0'] ."</option>";
$parse['opt_lst_ord_data']  .= "<option value =\"1\"". (($user['planet_sort'] == 1) ? " selected": "") .">". $lang['opt_lst_ord1'] ."</option>";
$parse['opt_lst_ord_data']  .= "<option value =\"2\"". (($user['planet_sort'] == 2) ? " selected": "") .">". $lang['opt_lst_ord2'] ."</option>";
$parse['opt_lst_ord_data']  .= "<option value =\"3\"". (($user['planet_sort'] == 3) ? " selected": "") .">". $lang['opt_lst_ord3'] ."</option>";

$parse['opt_lst_cla_data']   = "<option value =\"0\"". (($user['planet_sort_order'] == 0) ? " selected": "") .">". $lang['opt_lst_cla0'] ."</option>";
$parse['opt_lst_cla_data']  .= "<option value =\"1\"". (($user['planet_sort_order'] == 1) ? " selected": "") .">". $lang['opt_lst_cla1'] ."</option>";

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

if ($user['authlevel'])
{
  $parse['adm_pl_prot_data'] = ($planetrow['id_level'] > 0) ? " checked='checked'" : '';
}

$template->assign_vars(array(
  'IS_ADMIN'       => $user['authlevel'],
  'opt_usern_data' => $user['username'],
  'opt_mail1_data' => $user['email'],
  'opt_mail2_data' => $user['email_2'],
  'opt_dpath_data' => $user['dpath'],
  'opt_avata_data' => $user['avatar'],
  'opt_probe_data' => $user['spio_anz'],
  'opt_toolt_data' => $user['settings_tooltiptime'],
  'opt_fleet_data' => $user['settings_fleetactions'],
  'opt_sskin_data' => ($user['design'] == 1) ? " checked='checked'":'',
  'opt_noipc_data' => ($user['noipcheck'] == 1) ? " checked='checked'":'',
  'opt_allyl_data' => ($user['settings_allylogo'] == 1) ? " checked='checked'/":'',
  'opt_delac_data' => ($user['db_deaktjava'] == 1) ? " checked='checked'/":'',

  'user_settings_rep' => ($user['settings_rep'] == 1) ? " checked='checked'/":'',
  'user_settings_esp' => ($user['settings_esp'] == 1) ? " checked='checked'/":'',
  'user_settings_wri' => ($user['settings_wri'] == 1) ? " checked='checked'/":'',
  'user_settings_mis' => ($user['settings_mis'] == 1) ? " checked='checked'/":'',
  'user_settings_bud' => ($user['settings_bud'] == 1) ? " checked='checked'/":'',
  'kolorminus'        => $user['kolorminus'],
  'kolorplus'         => $user['kolorplus'],
  'kolorpoziom'       => $user['kolorpoziom'],

  'USER_VACATION_DISABLE' => $config->user_vacation_disable,
  'VACATION_TIME' => VACATION_TIME,
  'DELETE_TIME' => 45*24*60*60,
  'TIME_NOW' => $time_now,
));

foreach($user_option_list as $option_group_id => $option_group)
{
  foreach($option_group as $option_name => $option_value)
  {
    $template->assign_block_vars("options_{$option_group_id}", array(
      'NAME'  => $option_name,
      'TEXT'  => $lang['opt_custom'][$option_name],
      'VALUE' => $user[$option_name],
    ));
  }
}

display(parsetemplate($template, $parse), $lang['opt_options'], false);

?>
