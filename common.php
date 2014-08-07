<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 */

require_once('includes/init.php');

global $debug, $sn_mvc, $template_result, $user;
if($user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0)
{
  echo '<a href="badqrys.txt" target="_blank" style="color:red">', $lang[ov_hack_alert], '</a>';
}

// Напоминание для Администрации, что игра отключена
if($template_result[F_GAME_DISABLE]) {
  // $disable_reason = sys_bbcodeParse($config->game_disable_reason); // Должна быть инициализирована выше по коду - в init.php
  echo '<div class="global_admin_warning">', $template_result[F_GAME_DISABLE_REASON], '</div>';
}
unset($disable_reason);


if(defined('IN_ADMIN') && IN_ADMIN === true) {
  lng_include('admin');
}
elseif($sys_user_logged_in)
{
  sys_user_vacation($user);

  $planet_id = SetSelectedPlanet($user);

  // TODO НЕ НУЖНО АЛЬЯНС КАЖДЫЙ РАЗ ОБНОВЛЯТЬ!!!
  if($user['ally_id'])
  {
    sn_db_transaction_start();
    sn_ali_fill_user_ally($user);
    if(!$user['ally']['player']['id'])
    {
      sn_sys_logout(false, true);
      $debug->error("User ID {$user['id']} has ally ID {$user['ally_id']} but no ally info", 'User record error', 502);
    }
    // TODO UNCOMMENT
    que_process($user['ally']['player']);
    db_user_set_by_id($user['ally']['player']['id'], '`onlinetime` = ' . SN_TIME_NOW);
    sn_db_transaction_commit();
  }


  // TODO - в режиме эмуляции, на самом деле!
  sn_db_transaction_start();
  $global_data = sys_o_get_updated($user['id'], $planet_id, SN_TIME_NOW);
  sn_db_transaction_commit();

  $planetrow = $global_data['planet'];
  if(!($planetrow && isset($planetrow['id']) && $planetrow['id']))
  {
    sn_sys_logout(false, true);
    $debug->error("User ID {$user['id']} has no current planet and no homeworld", 'User record error', 502);
  }

  $que = $global_data['que'];
}

require_once('includes/vars_menu.php');

if($sn_mvc['model'][''])
{
  foreach($sn_mvc['model'][''] as $hook)
  {
    if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable))))
    {
      call_user_func($hook_call);
    }
  }
}

sys_user_options_unpack($user);
