<?php

/**
 * CheckLabSettingsInQueue.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Teste si la queue de construction eventuelle a le labo en premiere position ....
function CheckLabSettingsInQueue ( $CurrentPlanet ) {
	global $lang, $game_config;

	if ($CurrentPlanet['b_building_id'] != "0") {
		$BuildQueue = $CurrentPlanet['b_building_id'];
		if (strpos ($BuildQueue, ";")) {
			$Queue = explode (";", $BuildQueue);
			$CurrentBuilding = $Queue[0];
		} else {
			// Y a pas de queue de construction la liste n'a qu'un seul element
			$CurrentBuilding = $BuildQueue;
		}

		if ($CurrentBuilding == 31 || $CurrentBuilding == 35 && $game_config['BuildLabWhileRun'] != 1) {
			$return = false;
		} else {
			$return = true;
		}

	} else {
		$return = true;
	}

	return $return;
}
?>