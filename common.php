<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 */
require_once('includes/init.php');

if (!$InLogin) {
  $user          = CheckTheUser();

  if($config->game_disable)
  {
    if ($user['authlevel'] < 1)
    {
      message ( sys_bbcodeParse($config->game_disable_reason), $config->game_name );
      die();
    }
    else
    {
      print( "<div align=center style='font-size: 24; font-weight: bold; color:red;'>" . sys_bbcodeParse($config->game_disable_reason) . '</div><br>' );
    }
  }
  if(!$user['id'])
  {
    header('Location: login.php');
  }
}

if ($user['id'])
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

  SetSelectedPlanet ( $user );

  $planetrow = doquery("SELECT * FROM {{table}} WHERE `id` = '".$user['current_planet']."';", 'planets', true);

  CheckPlanetUsedFields($planetrow);
}
else
{
  // Bah si déja y a quelqu'un qui passe par là et qu'a rien a faire de pressé ...
  // On se sert de lui pour mettre a jour tout les retardataires !!
  $debug->warning("May be it's login page? InLogin = '{$InLogin}', IsUserChecked = '{$IsUserChecked}'", 'Unregistered user', 303);
}
?>
