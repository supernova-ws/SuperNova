<?php

/**
 * fleetback.php
 *
 * Return your fleet
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

  $ugamela_root_path = './';
  include($ugamela_root_path . 'extension.inc');
  include($ugamela_root_path . 'common.' . $phpEx);

  if ($IsUserChecked == false) {
    includeLang('login');
    header("Location: login.php");
  }

  includeLang('fleet');

  $BoxTitle   = $lang['fl_error'];
  $TxtColor   = "red";
  $BoxMessage = $lang['fl_notback'];
  if ( is_numeric($_POST['fleetid']) ) {
    $fleetid  = intval($_POST['fleetid']);

    $FleetRow = doquery("SELECT * FROM {{table}} WHERE `fleet_id` = '". $fleetid ."';", 'fleets', true);
    $i = 0;

    if ($FleetRow['fleet_owner'] == $user['id']) {
      if ($FleetRow['fleet_mess'] == 0) {
        if ($FleetRow['fleet_end_stay'] != 0) {
          // Faut calculer le temps reel de retour
          if ($FleetRow['fleet_start_time'] < time()) {
            // On a pas encore entamé le stationnement
            // Il faut calculer la parcelle de temps ecoulée depuis le lancement de la flotte
            $CurrentFlyingTime = time() - $FleetRow['start_time'];
          } else {
            // On est deja en stationnement
            // Il faut donc directement calculer la durée d'un vol aller ou retour
            $CurrentFlyingTime = $FleetRow['fleet_start_time'] - $FleetRow['start_time'];
          }
        } else {
          // C'est quoi le stationnement ??
          // On calcule sagement la parcelle de temps ecoulée depuis le depart
          $CurrentFlyingTime = time() - $FleetRow['start_time'];
        }
        // Allez houste au bout du compte y a la maison !! (E.T. phone home.............)
        $ReturnFlyingTime  = $CurrentFlyingTime + time();

        $QryUpdateFleet  = "UPDATE {{table}} SET ";
        $QryUpdateFleet .= "`fleet_start_time` = '". (time() - 1) ."', ";
        $QryUpdateFleet .= "`fleet_end_stay` = '0', ";
        $QryUpdateFleet .= "`fleet_end_time` = '". ($ReturnFlyingTime + 1) ."', ";
        $QryUpdateFleet .= "`fleet_target_owner` = '". $user['id'] ."', ";
        $QryUpdateFleet .= "`fleet_mess` = '1' ";
        $QryUpdateFleet .= "WHERE ";
        $QryUpdateFleet .= "`fleet_id` = '" . $fleetid . "';";
        doquery( $QryUpdateFleet, 'fleets');

        $BoxTitle   = $lang['fl_sback'];
        $TxtColor   = "lime";
        $BoxMessage = $lang['fl_isback'];
      } elseif ($FleetRow['fleet_mess'] == 1) {
        $BoxMessage = $lang['fl_notback'];
      }
    } else {
      $BoxMessage = $lang['fl_onlyyours'];
    }
  }

  message ("<font color=\"".$TxtColor."\">". $BoxMessage ."</font>", $BoxTitle, "fleet.". $phpEx, 2);

// -----------------------------------------------------------------------------------------------------------
// History version
// Updated by Chlorel. 22 Jan 2008 (String extraction, bug corrections, code uniformisation
// Created by DxPpLmOs. All rights reversed (C) 2007
// Updated by -= MoF =- for Deutsches Ugamela Forum
// 06.12.2007 - 08:41
// Open Source
// (c) by MoF
?>