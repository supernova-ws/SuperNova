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

$sys_user_logged_in = $user && is_array($user) && isset($user['id']) && $user['id'];

if(
  !($allow_anonymous || $sys_user_logged_in) ||
  (defined('IN_ADMIN') && IN_ADMIN && $user['authlevel'] < 1)
)
{
  setcookie($config->COOKIE_NAME, '', time() - 3600*25);
  header('Location: ' . (IN_ADMIN == true ? '../' : '') .'login.php');
  ob_end_flush();
  die();
}

includeLang('system');
includeLang('tech');

if($user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0)
{
  echo "<a href=\"badqrys.txt\" target=\"_NEW\"><font color=\"red\">{$lang['ov_hack_alert']}</font</a>";
}

if (defined('IN_ADMIN') && IN_ADMIN)
{
  $UserSkin  = $user['dpath'];
  $local     = stristr ( $UserSkin, "http:");
  if ($local === false)
  {
    if (!$user['dpath'])
    {
      $dpath     = "../". DEFAULT_SKINPATH  ;
    }
    else
    {
      $dpath     = "../". $user["dpath"];
    }
  }
  else
  {
    $dpath     = $UserSkin;
  }

  includeLang('admin');
}
elseif($sys_user_logged_in)
{
  $dpath     = $user["dpath"] ? $user["dpath"] : DEFAULT_SKINPATH;

  flt_flying_fleet_handler($config, $skip_fleet_update);

  $planet_id = SetSelectedPlanet($user);
  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planet_id, $time_now);
  if(!$global_data['planet'])
  {
    doquery("UPDATE {{users}} SET `current_planet` = '{$user['id_planet']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    $global_data = sys_o_get_updated($user, $user['id_planet'], $time_now);
  }
  doquery('COMMIT;');

  if(!$global_data)
  {
    $debug->error("User ID {$user['id']} has no current planet and no homeworld", 'User record error', 502);
  }

  $planetrow = $global_data['planet'];
  if(!($planetrow && isset($planetrow['id']) && $planetrow['id']))
  {
    header('Location: login.php');
    ob_end_flush();
    die();
  }

  $que = $global_data['que'];

  CheckPlanetUsedFields($planetrow);

  if(!$allow_anonymous)
  {
    sys_user_vacation($user);
  }
}

?>
