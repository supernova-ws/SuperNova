<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 */

require_once('includes/init.php');

$user = sn_autologin(!$allow_anonymous);
$sys_user_logged_in = is_array($user) && isset($user['id']) && $user['id'];

$time_diff_seconds = $user['user_time_diff'];
$time_utc_offset = $user['user_time_utc_offset'];
$time_diff = $time_diff_seconds + $time_utc_offset;
$time_local = SN_TIME_NOW + $time_diff;

if(!defined('SN_CLIENT_TIME_DIFF'))
{
  define('SN_CLIENT_TIME_DIFF', $time_diff);
}

if(!defined('SN_CLIENT_TIME_LOCAL'))
{
  define('SN_CLIENT_TIME_LOCAL', $time_local);
}

define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);

$dpath = $user["dpath"] ? $user["dpath"] : DEFAULT_SKINPATH;

lng_switch(sys_get_param_str('lang'));

if($config->game_disable)
{
  $disable_reason = sys_bbcodeParse($config->game_disable_reason);
  if ($user['authlevel'] < 1 || !(defined('IN_ADMIN') && IN_ADMIN))
  {
    message($disable_reason, $config->game_name);
    ob_end_flush();
    die();
  }
  else
  {
    print("<div align=center style='font-size: 24; font-weight: bold; color:red;'>{$disable_reason}</div><br>");
  }
}

if(!(($allow_anonymous || (isset($sn_page_data['allow_anonymous']) && $sn_page_data['allow_anonymous'])) || $sys_user_logged_in) || (defined('IN_ADMIN') && IN_ADMIN === true && $user['authlevel'] < 1))
{
  setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
  sys_redirect(SN_ROOT_VIRTUAL .'login.php');
}

if($user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0)
{
  echo "<a href=\"badqrys.txt\" target=\"_NEW\"><font color=\"red\">{$lang['ov_hack_alert']}</font</a>";
}

if(defined('IN_ADMIN') && IN_ADMIN === true)
{
  lng_include('admin');
}
elseif($sys_user_logged_in)
{
/*
  sn_db_transaction_start();

  que_add_unit(TECH_ASTROTECH, $user, $planetrow, array(RES_TIME => array(BUILD_CREATE => 5)), 5);
  sn_db_transaction_rollback();
  die();
*/
//  $unit_level = mrc_get_level($user, $planet, $tech_id, false, true) + $global_que['in_que'][QUE_RESEARCH][0][$tech_id];
//  $build_data = eco_get_build_data($user, $planet, $tech_id, $unit_level);
//
//  if(!$build_data['CAN'][BUILD_CREATE])
//  {
//    throw new exception($lang['eco_bld_resources_not_enough'], ERR_ERROR);
//  }
//
//  que_add_unit(QUE_RESEARCH, $tech_id, $user, $planet, $build_data, $unit_level + 1, 1);




/*
  require_once('includes/includes/sys_stat.php');
  sys_stat_calculate();
die();
*/






  if(!($skip_fleet_update || $supernova->options['fleet_update_skip']) && $time_now - $config->flt_lastUpdate >= 4)
  {
    require_once("includes/includes/flt_flying_fleet_handler2.php");
    flt_flying_fleet_handler($config, $skip_fleet_update);
  }

//  if(!$allow_anonymous)
//  {
  sys_user_vacation($user);
//  }

  $planet_id = SetSelectedPlanet($user);

  // sn_db_transaction_start();
  // que_process($user); // TODO UNCOMMENT
  // sn_db_transaction_commit();


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
    // que_process($user['ally']['player']);
    // doquery("UPDATE `{{users}}` SET `onlinetime` = {$time_now} WHERE `id` = '{$user['ally']['player']['id']}' LIMIT 1;");
    sn_db_transaction_commit();
  }



  // TODO - Выбирать $planet_row в SetSelectedPlanet(), а не в sys_o_get_updated()
  sn_db_transaction_start();
  $global_data = sys_o_get_updated($user, $planet_id, $time_now);
  /* // Это - не нужно. Проверки на существование планеты делается в SetSelectedPlanet()
  if(!$global_data['planet'])
  {
    doquery("UPDATE {{users}} SET `current_planet` = '{$user['id_planet']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    $global_data = sys_o_get_updated($user, $user['id_planet'], $time_now);
  }
  */
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
