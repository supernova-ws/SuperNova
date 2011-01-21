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
  if ($user['authlevel'] < 1)
  {
    message ( sys_bbcodeParse($config->game_disable_reason), $config->game_name );
    die();
  }
  else
  {
    $disable_reason = sys_bbcodeParse($config->game_disable_reason);
    print("<div align=center style='font-size: 24; font-weight: bold; color:red;'>{$disable_reason}</div><br>");
  }
}

if(!$user && !$allow_anonymous)
{
  header('Location: login.php');
}

if ($user && is_array($user) && isset($user['id']) && !empty($user['id']))
{
  FlyingFleetHandler();

  if ( defined('IN_ADMIN') )
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
  }
  else
  {
    $dpath     = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  }

  SetSelectedPlanet($user);
  $planetrow = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$user['current_planet']}' LIMIT 1;", '', true);
  if(!$planetrow)
  {
    $planetrow = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$user['id_planet']}' LIMIT 1;", '', true);
    if(!$planetrow)
    {
      header('Location: login.php');
    }
  }
  CheckPlanetUsedFields($planetrow);
  $que = PlanetResourceUpdate($user, $planetrow, $time_now);

  if(!$skip_ban_check && !(IN_ADMIN === true))
  {
    sys_user_vacation($user);
  }
}

?>
