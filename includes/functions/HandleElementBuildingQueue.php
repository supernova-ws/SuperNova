<?php

/**
 * HandleElementBuildingQueue.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function HandleElementBuildingQueue ( $CurrentUser, &$CurrentPlanet, $ProductionTime ) {
	global $resource;
	// Pendant qu'on y est, si on verifiait ce qui se passe dans la queue de construction du chantier ?
	if ($CurrentPlanet['b_hangar_id'] != 0) {
		$Builded                    = array ();
		$CurrentPlanet['b_hangar'] += $ProductionTime;

		$BuildQueue                 = explode(';', $CurrentPlanet['b_hangar_id']);

		foreach ($BuildQueue as $Node => $Array) {
			if ($Array != '') {
				$Item              = explode(',', $Array);
				// On stocke sous forme Element, Nombre, Duree de fab
				$BuildArray[$Node] = array($Item[0], $Item[1], GetBuildingTime ($CurrentUser, $CurrentPlanet, $Item[0]));
			}
		}

		$CurrentPlanet['b_hangar_id'] = '';

		$UnFinished = false;
		foreach ( $BuildArray as $Node => $Item ) {
			if (!$UnFinished) {
				$Element   = $Item[0];
				$Count     = $Item[1];
				$BuildTime = $Item[2];
				while ( $CurrentPlanet['b_hangar'] >= $BuildTime && !$UnFinished ) {
					if ( $Count > 0 ) {
						$CurrentPlanet['b_hangar'] -= $BuildTime;
						$Builded[$Element]++;
						$CurrentPlanet[$resource[$Element]]++;
						$Count--;
						if ($Count == 0) {
							break;
						}
					} else {
						$UnFinished = true;
						break;
					}
				}
			}
			if ( $Count != 0 ) {
				$CurrentPlanet['b_hangar_id'] .= $Element.",".$Count.";";
			}
		}
	} else {
		$Builded                   = '';
		$CurrentPlanet['b_hangar'] = 0;
	}

	return $Builded;
}
?>