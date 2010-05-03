<?php

/**
 * MissionCaseStay.php
 *
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */


// ----------------------------------------------------------------------------------------------------------------
// Mission Case 4: -> Stationner
//
function MissionCaseStay ( $FleetRow ) {
	global $lang, $resource;

	if ($FleetRow['fleet_mess'] == 0) {
		if ($FleetRow['fleet_start_time'] <= time()) {
			$QryGetTargetPlanet   = "SELECT * FROM {{table}} ";
			$QryGetTargetPlanet  .= "WHERE ";
			$QryGetTargetPlanet  .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
			$QryGetTargetPlanet  .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
			$QryGetTargetPlanet  .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
			$QryGetTargetPlanet  .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."';";
			$TargetPlanet         = doquery( $QryGetTargetPlanet, 'planets', true);
			$TargetUserID         = $TargetPlanet['id_owner'];

			$TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
			$TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
												$lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
												$lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
												$lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

			$TargetMessage        = $lang['sys_stay_mess_start'] ."<a href=\"galaxy.php?mode=3&galaxy=". $FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."\">";
			$TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_end'] ."<br />". $TargetAddedGoods;

			SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $TargetMessage);
			RestoreFleetToPlanet ( $FleetRow, false );
			doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
		}
	} else {
		if ($FleetRow['fleet_end_time'] <= time()) {
			$TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
			$TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
												$lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
												$lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
												$lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

			$TargetMessage        = $lang['sys_stay_mess_back'] ."<a href=\"galaxy.php?mode=3&galaxy=". $FleetRow['fleet_start_galaxy'] ."&system=". $FleetRow['fleet_start_system'] ."\">";
			$TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_bend'] ."<br />". $TargetAddedGoods;

			SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 5, $lang['sys_mess_qg'], $lang['sys_mess_fleetback'], $TargetMessage);
			RestoreFleetToPlanet ( $FleetRow, true );
			doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
		}
	}
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 Mise en module initiale
// 1.1 FIX permet un retour de flotte cohérant
?>