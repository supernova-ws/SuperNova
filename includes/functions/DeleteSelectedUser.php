<?php

/**
 * DeleteSelectedUser.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function DeleteSelectedUser ( $UserID ) {
  $TheUser = doquery ( "SELECT * FROM `{{users}}` WHERE `id` = '" . $UserID . "';", '', true );
  if ( $TheUser['ally_id'] != 0 ) {
    $TheAlly = doquery ( "SELECT * FROM `{{alliance}}` WHERE `id` = '" . $TheUser['ally_id'] . "';", '', true );
    $TheAlly['ally_members'] -= 1;
    if ( $TheAlly['ally_members'] > 0 ) {
      doquery ( "UPDATE `{{alliance}}` SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");
    } else {
      doquery ( "DELETE FROM `{{alliance}}` WHERE `id` = '" . $TheAlly['id'] . "';");
      doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';");
    }
  }
  doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';");

  $ThePlanets = doquery ( "SELECT * FROM `{{planets}}` WHERE `id_owner` = '" . $UserID . "';" );
  while ( $OnePlanet = mysql_fetch_assoc ( $ThePlanets ) ) {
    if ( $OnePlanet['planet_type'] == 1 ) {
      // doquery ( "DELETE FROM `{{galaxy}}` WHERE `galaxy` = '" . $OnePlanet['galaxy'] . "' AND `system` = '" . $OnePlanet['system'] . "' AND `planet` = '" . $OnePlanet['planet'] . "';" );
    }
    doquery ( "DELETE FROM `{{planets}}` WHERE `id` = '" . $ThePlanets['id'] . "';");
  }
  doquery ( "DELETE FROM `{{messages}}` WHERE `message_sender` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{messages}}` WHERE `message_owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{notes}}` WHERE `owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner1` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner2` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `sender` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{annonce}}` WHERE `user` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{users}}` WHERE `id` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{referrals}}` WHERE (`id` = '{$UserID}') OR (`id_partner` = '{$UserID}');");
  doquery ( "UPDATE `{{config}}` SET `config_value`= `config_value` - 1 WHERE `config_name` = 'users_amount';");
}

?>
