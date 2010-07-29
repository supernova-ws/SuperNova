<?php

/**
 * settings.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

function DisplayGameSettingsPage ( $CurrentUser ) {
  global $lang, $game_config, $_POST, $config;

  includeLang('admin/settings');

  if ( $CurrentUser['authlevel'] >= 3 ) {
    if ($_POST['opt_save'] == "1") {
      // Jeu Ouvert ou FermпїЅ !
      if (isset($_POST['closed']) && $_POST['closed'] == 'on') {
        $config->game_disable         = "1";
      } else {
        $config->game_disable         = "0";
      }
      $config->game_disable_reason  = addslashes( $_POST['game_disable_reason'] );

      $game_config['advGoogleLeftMenuCode'] = mysql_real_escape_string( $_POST['advGoogleLeftMenuCode'] );
      if (isset($_POST['advGoogleLeftMenuIsOn']) && $_POST['advGoogleLeftMenuIsOn'] == 'on') {
        $game_config['advGoogleLeftMenuIsOn'] = "1";
      } else {
        $game_config['advGoogleLeftMenuIsOn'] = "0";
      }

      // Mode Debug ou pas !
      if (isset($_POST['debug']) && $_POST['debug'] == 'on') {
        $game_config['debug'] = "1";
      } else {
        $game_config['debug'] = "0";
      }

      // Nom du Jeu
      if (isset($_POST['game_name']) && $_POST['game_name'] != '') {
        $game_config['game_name'] = $_POST['game_name'];
      }

      // Adresse du Forum
      if (isset($_POST['forum_url']) && $_POST['forum_url'] != '') {
        $game_config['forum_url'] = $_POST['forum_url'];
      }

      // Vitesse du Jeu
      if (isset($_POST['game_speed']) && is_numeric($_POST['game_speed'])) {
        $game_config['game_speed'] = $_POST['game_speed'];
      }

      // Vitesse des Flottes
      if (isset($_POST['fleet_speed']) && is_numeric($_POST['fleet_speed'])) {
        $game_config['fleet_speed'] = $_POST['fleet_speed'];
      }

      // Multiplicateur de Production
      if (isset($_POST['resource_multiplier']) && is_numeric($_POST['resource_multiplier'])) {
        $game_config['resource_multiplier'] = $_POST['resource_multiplier'];
      }

      // Taille de la planete mГЁre
      if (isset($_POST['initial_fields']) && is_numeric($_POST['initial_fields'])) {
        $game_config['initial_fields'] = $_POST['initial_fields'];
      }

      // Revenu de base Metal
      if (isset($_POST['metal_basic_income']) && is_numeric($_POST['metal_basic_income'])) {
        $game_config['metal_basic_income'] = $_POST['metal_basic_income'];
      }

      // Revenu de base Cristal
      if (isset($_POST['crystal_basic_income']) && is_numeric($_POST['crystal_basic_income'])) {
        $game_config['crystal_basic_income'] = $_POST['crystal_basic_income'];
      }

      // Revenu de base Deuterium
      if (isset($_POST['deuterium_basic_income']) && is_numeric($_POST['deuterium_basic_income'])) {
        $game_config['deuterium_basic_income'] = $_POST['deuterium_basic_income'];
      }

      // Revenu de base Energie
      if (isset($_POST['energy_basic_income']) && is_numeric($_POST['energy_basic_income'])) {
        $game_config['energy_basic_income'] = $_POST['energy_basic_income'];
      }

      //24h Urlaubmodus erzwingen ein
      if(isset($_POST["urlaubs_modus_erz"])&& $_POST["urlaubs_modus_erz"] == 'on'){
        $game_config['urlaubs_modus_erz'] = "1";
      }else{
        $game_config['urlaubs_modus_erz'] = "0";
      }

      // Activation du jeu
//      doquery("UPDATE {{table}} SET `config_value` = '". $config->game_disable           ."' WHERE `config_name` = 'game_disable';", 'config');
//      doquery("UPDATE {{table}} SET `config_value` = '". $config->game_disable_reason           ."' WHERE `config_name` = 'game_disable_reason';", 'config');
      $config->db_saveItem('game_disable');
      $config->db_saveItem('game_disable_reason');

      // Configuration du Jeu
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['game_name']              ."' WHERE `config_name` = 'game_name';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['forum_url']              ."' WHERE `config_name` = 'forum_url';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['game_speed']             ."' WHERE `config_name` = 'game_speed';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['fleet_speed']            ."' WHERE `config_name` = 'fleet_speed';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['resource_multiplier']    ."' WHERE `config_name` = 'resource_multiplier';", 'config');

      // Page Generale
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['OverviewNewsFrame']       ."' WHERE `config_name` = 'OverviewNewsFrame';", 'config');

      // Options Planete
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['initial_fields']         ."' WHERE `config_name` = 'initial_fields';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['metal_basic_income']     ."' WHERE `config_name` = 'metal_basic_income';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['crystal_basic_income']   ."' WHERE `config_name` = 'crystal_basic_income';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['deuterium_basic_income'] ."' WHERE `config_name` = 'deuterium_basic_income';", 'config');
      doquery("UPDATE {{table}} SET `config_value` = '". $game_config['energy_basic_income']    ."' WHERE `config_name` = 'energy_basic_income';", 'config');

      // Mode Debug
      doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['debug']                  ."' WHERE `config_name` ='debug'", 'config');

      //24h Urlaubmodus erzwingen
      doquery("UPDATE {{table}} SET config_value='" . $game_config['urlaubs_modus_erz']         ."' WHERE `config_name` ='urlaubs_modus_erz'", 'config');

      doquery("REPLACE INTO {{table}} (`config_name`, `config_value`) VALUES ('advGoogleLeftMenuIsOn', '". $game_config['advGoogleLeftMenuIsOn'] ."');", 'config');
      doquery("REPLACE INTO {{table}} (`config_name`, `config_value`) VALUES ('advGoogleLeftMenuCode', '". $game_config['advGoogleLeftMenuCode'] ."');", 'config');

      $config->db_saveAll();

      AdminMessage ('Настройки сохранены успешно!', 'Готово', '?');
    } else {

      $parse                           = $lang;
      $parse['game_name']              = $game_config['game_name'];
      $parse['game_speed']             = $game_config['game_speed'];
      $parse['fleet_speed']            = $game_config['fleet_speed'];
      $parse['resource_multiplier']    = $game_config['resource_multiplier'];
      $parse['forum_url']              = $game_config['forum_url'];
      $parse['initial_fields']         = $game_config['initial_fields'];
      $parse['metal_basic_income']     = $game_config['metal_basic_income'];
      $parse['crystal_basic_income']   = $game_config['crystal_basic_income'];
      $parse['deuterium_basic_income'] = $game_config['deuterium_basic_income'];
      $parse['energy_basic_income']    = $game_config['energy_basic_income'];

      $parse['closed']                 = ($config->game_disable == 1) ? " checked = 'checked' ":"";
      $parse['game_disable_reason']           = stripslashes( $config->game_disable_reason );

      $parse['newsframe']              = ($game_config['OverviewNewsFrame'] == 1) ? " checked = 'checked' ":"";

      $parse['advGoogleLeftMenuIsOn']  = ($game_config['advGoogleLeftMenuIsOn'] == 1) ? " checked = 'checked' ":"";
      $parse['advGoogleLeftMenuCode']  =  $game_config['advGoogleLeftMenuCode'];

      $parse['debug']                  = ($game_config['debug'] == 1)        ? " checked = 'checked' ":"";

      $PageTPL                         = gettemplate('admin/options_body');
      $Page                           .= parsetemplate( $PageTPL,  $parse );

      display ( $Page, $lang['adm_opt_title'], false, '', true );
    }
  } else {
    AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  }
  return $Page;
}

  $Page = DisplayGameSettingsPage ( $user );
?>