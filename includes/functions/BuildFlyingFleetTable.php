<?php

/**
 * BuildFlyingFleetTable.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function BuildFlyingFleetTable () {
	global $lang;

	$TableTPL     = gettemplate('admin/fleet_rows');
	$FlyingFleets = doquery ("SELECT * FROM `{{table}}` ORDER BY `fleet_end_time` ASC;", 'fleets');
	while ( $CurrentFleet = mysql_fetch_assoc( $FlyingFleets ) ) {
		$FleetOwner       = doquery("SELECT `username` FROM `{{table}}` WHERE `id` = '". $CurrentFleet['fleet_owner'] ."';", 'users', true);
		$TargetOwner      = doquery("SELECT `username` FROM `{{table}}` WHERE `id` = '". $CurrentFleet['fleet_target_owner'] ."';", 'users', true);
		$Bloc['Id']       = $CurrentFleet['fleet_id'];
		$Bloc['Mission']  = CreateFleetPopupedMissionLink ( $CurrentFleet, $lang['type_mission'][ $CurrentFleet['fleet_mission'] ], '' );
		$Bloc['Mission'] .= "<br>". (($CurrentFleet['fleet_mess'] == 1) ? "R" : "A" );

		$Bloc['Fleet']    = CreateFleetPopupedFleetLink ( $CurrentFleet, $lang['tech'][200], '' );
		$Bloc['St_Owner'] = "[". $CurrentFleet['fleet_owner'] ."]<br>". $FleetOwner['username'];
		$Bloc['St_Posit'] = "[".$CurrentFleet['fleet_start_galaxy'] .":". $CurrentFleet['fleet_start_system'] .":". $CurrentFleet['fleet_start_planet'] ."]<br>". ( ($CurrentFleet['fleet_start_type'] == 1) ? "[P]": (($CurrentFleet['fleet_start_type'] == 2) ? "D" : "L"  )) ."";
		$Bloc['St_Time']  = date('G:i:s d/n/Y', $CurrentFleet['fleet_start_time']);
		if (is_array($TargetOwner)) {
			$Bloc['En_Owner'] = "[". $CurrentFleet['fleet_target_owner'] ."]<br>". $TargetOwner['username'];
		} else {
			$Bloc['En_Owner'] = "";
		}
		$Bloc['En_Posit'] = "[".$CurrentFleet['fleet_end_galaxy'] .":". $CurrentFleet['fleet_end_system'] .":". $CurrentFleet['fleet_end_planet'] ."]<br>". ( ($CurrentFleet['fleet_end_type'] == 1) ? "[P]": (($CurrentFleet['fleet_end_type'] == 2) ? "D" : "L"  )) ."";
		if ($CurrentFleet['fleet_mission'] == 15) {
			$Bloc['Wa_Time']  = date('G:i:s d/n/Y', $CurrentFleet['fleet_stay_time']);
		} else {
			$Bloc['Wa_Time']  = "";
		}
		$Bloc['En_Time']  = date('G:i:s d/n/Y', $CurrentFleet['fleet_end_time']);

		$table .= parsetemplate( $TableTPL, $Bloc );
	}
	return $table;
}
?>