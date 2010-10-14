<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 */
require_once('includes/init.inc');

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
}

if ($user['id'])
{
  $update_file = "{$_SERVER['DOCUMENT_ROOT']}/includes/update.{$phpEx}";
  $flag_file   = "{$_SERVER['DOCUMENT_ROOT']}/includes/update.last";

  if(file_exists($update_file))
  {
    if(filemtime($update_file) != filemtime($flag_file))
    {
      if(!$config->db_loadItem('var_updating_db'))
      {
        $config->db_saveItem('var_updating_db', true);

        require_once($update_file);
        sys_refresh_tablelist();

        if(!file_exists($flag_file))
        {
          fclose(fopen($flag_file, 'w'));
        }

        touch($flag_file, filemtime($update_file));

        $config->db_saveItem('var_updating_db', false);
      }
    }
  }

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
