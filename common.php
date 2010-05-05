<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.0st Tested s-version
 * @version 1.0s Security checks by Gorlum for http://supernova.ws
 */
//print('ÂÍÈÌÀÍÈÅ! ÈÃÐÀ ÎÑÒÀÍÎÂËÅÍÀ ÍÀ ÏÐÎÔÈËÀÊÒÈÊÓ!');
//die();
//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL ^ E_NOTICE);
define('VERSION','v. 0.26');                // Passera en version 1.0 quand toutes les fonctions ET l'install seront correct

                                                                // Et c'est pas encore demain la veille !!!
//$ugamela_root_path = '/srv/www/ogame.triolan.com.ua/';
$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
set_magic_quotes_runtime(0);
$phpEx = "php";

$user          = array();
$lang          = array();
$IsUserChecked = false;

define('DEFAULT_SKINPATH' , 'skins/xnova/');
define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , 'OpenGame');
define('DEFAULT_LANG'     , 'ru');

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

include($ugamela_root_path . 'includes/debug.class.'.$phpEx);
$debug = new debug();

include($ugamela_root_path . 'includes/constants.'.$phpEx);
include($ugamela_root_path . 'includes/functions.'.$phpEx);
include_once($ugamela_root_path . 'includes/unlocalised.'.$phpEx);
include($ugamela_root_path . 'includes/todofleetcontrol.'.$phpEx);
include($ugamela_root_path . 'language/'. DEFAULT_LANG .'/lang_info.cfg');

$game_config   = $game_config_default;

$time_now = time();

if (INSTALL != true) {
  include($ugamela_root_path . 'includes/vars.'.$phpEx);
  include($ugamela_root_path . 'includes/db.'.$phpEx);
  include($ugamela_root_path . 'includes/strings.'.$phpEx);

  // Initializing global "config" object
  $config = objConfig::getInstance();

  // Lecture de la table de configuration
  $query = doquery("SELECT * FROM {{table}}",'config');
  while ( $row = mysql_fetch_assoc($query) ) {
    $game_config[$row['config_name']] = $row['config_value'];
  }

  if ($InLogin != true) {
    $Result        = CheckTheUser ( $IsUserChecked );
    $IsUserChecked = $Result['state'];
    $user          = $Result['record'];
  } elseif ($InLogin == false) {
    // Jeux en mode 'clos' ???
    if( $game_config['game_disable']) {
      if ($user['authlevel'] < 1) {
        message ( stripslashes ( $game_config['close_reason'] ), $game_config['game_name'] );
      }
    }
  }

  includeLang ("system");
  includeLang ('tech');

if ( isset ($user) ) {
  $_lastupdate = doquery("SELECT `lastupdate` FROM {{table}} LIMIT 1;", 'update');
  $row = mysql_fetch_row($_lastupdate);

  if(($time_now-$row[0]>8)&&(!$doNotUpdateFleet)){
    doquery("LOCK TABLE {{table}} WRITE", 'update');
    doquery("UPDATE {{table}} SET `lastupdate` = ".$time_now."", 'update');
    doquery("UNLOCK TABLES", '');
    $_lastupdate = $time_now;

    $_fleets = doquery("SELECT DISTINCT fleet_start_galaxy, fleet_start_system, fleet_start_planet, fleet_start_type FROM {{table}} WHERE `fleet_start_time` <= '{$time_now}' ORDER BY fleet_start_time;", 'fleets'); // OR fleet_end_time <= ".$time_now
    while ($row = mysql_fetch_array($_fleets)) {
      $array = array();
      $array['galaxy'] = $row['fleet_start_galaxy'];
      $array['system'] = $row['fleet_start_system'];
      $array['planet'] = $row['fleet_start_planet'];
      $array['planet_type'] = $row['fleet_start_type'];

      $temp = FlyingFleetHandler ($array);
    }

    $_fleets = doquery("SELECT DISTINCT fleet_end_galaxy, fleet_end_system, fleet_end_planet, fleet_end_type FROM {{table}} WHERE `fleet_end_time` <= '".$time_now."' ORDER BY fleet_end_time;", 'fleets'); // OR fleet_end_time <= ".$time_now
    while ($row = mysql_fetch_array($_fleets)) {
      $array = array();
      $array['galaxy'] = $row['fleet_end_galaxy'];
      $array['system'] = $row['fleet_end_system'];
      $array['planet'] = $row['fleet_end_planet'];
      $array['planet_type'] = $row['fleet_end_type'];

      $temp = FlyingFleetHandler ($array);
    }

    // $dbg_msg .= "Common.php Removing all arrived fleets<br />";
    // doquery("UPDATE {{table}} SET `metal` = 0 WHERE `metal` < 0 ;", 'planets');
    //$_fleets = doquery("SELECT * FROM {{table}} WHERE `fleet_end_time` <= '".$time_now."';", 'fleets'); // OR fleet_end_time <= ".$time_now
    // doquery("DELETE FROM {{table}} WHERE `fleet_end_time` <= '".$time_now."' ;", 'fleets');

    //if ($user['id']==492 && $dbg_msg) {
    //if ($dbg_msg<>"") {
    // $debug->warning($dbg_msg,"Debug");
    //};

    unset($_fleets);

    $aks = doquery("SELECT id FROM {{table}};", 'aks');
    while ($aks_row = mysql_fetch_array($aks)) {
      $aks_fleet = doquery("SELECT DISTINCT fleet_group FROM {{table}} WHERE fleet_group = {$aks_row['id']};", 'fleets', true);
      if (!$aks_fleet){
        doquery("DELETE FROM {{table}} WHERE id = {$aks_row['id']};", 'aks');
      }
    };

    include_once($ugamela_root_path . 'includes/mip_calculate.'.$phpEx);
  };

  if ( defined('IN_ADMIN') ) {
    $UserSkin  = $user['dpath'];
    $local     = stristr ( $UserSkin, "http:");
    if ($local === false) {
      if (!$user['dpath']) {
        $dpath     = "../". DEFAULT_SKINPATH  ;
      } else {
        $dpath     = "../". $user["dpath"];
      }
    } else {
      $dpath     = $UserSkin;
    }
  } else {
    $dpath     = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  }

  SetSelectedPlanet ( $user );

  $planetrow = doquery("SELECT * FROM {{table}} WHERE `id` = '".$user['current_planet']."';", 'planets', true);
  $galaxyrow = doquery("SELECT * FROM {{table}} WHERE `id_planet` = '".$planetrow['id']."';", 'galaxy', true);

  CheckPlanetUsedFields($planetrow);
  } else {
    // Bah si déja y a quelqu'un qui passe par là et qu'a rien a faire de pressé ...
    // On se sert de lui pour mettre a jour tout les retardataires !!
  }
} else {
  $dpath     = "../" . DEFAULT_SKINPATH;
}

?>
