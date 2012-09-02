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
  if(!$skip_fleet_update && $time_now - $config->flt_lastUpdate >= 4)
  {
    require_once("includes/includes/flt_flying_fleet_handler.php");
    flt_flying_fleet_handler($config, $skip_fleet_update);
  }

//  if(!$allow_anonymous)
//  {
  sys_user_vacation($user);
//  }

  $planet_id = SetSelectedPlanet($user);

  doquery('START TRANSACTION;');
  eco_bld_que_tech($user);

  if($user['ally_id'])
  {
    sn_ali_fill_user_ally($user);
    if(!$user['ally']['player']['id'])
    {
      sn_sys_logout(false, true);
      $debug->error("User ID {$user['id']} has ally ID {$user['ally_id']} but no ally info", 'User record error', 502);
    }
    eco_bld_que_tech($user['ally']['player']);
    doquery("UPDATE `{{users}}` SET `onlinetime` = {$time_now} WHERE `id` = '{$user['ally']['player']['id']}' LIMIT 1;");
  }
  doquery('COMMIT;');

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planet_id, $time_now);
  if(!$global_data['planet'])
  {
    doquery("UPDATE {{users}} SET `current_planet` = '{$user['id_planet']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    $global_data = sys_o_get_updated($user, $user['id_planet'], $time_now);
  }
  doquery('COMMIT;');

  $planetrow = $global_data['planet'];
  if(!($planetrow && isset($planetrow['id']) && $planetrow['id']))
  {
    sn_sys_logout(false, true);
    $debug->error("User ID {$user['id']} has no current planet and no homeworld", 'User record error', 502);
  }

  $que = $global_data['que'];
}

?>
