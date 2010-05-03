<?php

/**
 * CheckPlanetBuildingQueue
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// Gestion de la queue de construction des batiments
// Parametres en entree :
// &$CurrentPlanet -> la planete ou l'on verifie
// $CurrentUser    -> Joueur a qui appartient la planete
//
// Retour :
//                 -> boolean (vrai ou faux) non exploité pour le moment
function CheckPlanetBuildingQueue ( &$CurrentPlanet, &$CurrentUser ) {
	global $lang, $resource;

	// Table des batiments donnant droit de l'experience minier
	$XPBuildings  = array(  1,  2,  3, 22, 23, 24);

	$RetValue     = false;
	if ($CurrentPlanet['b_building_id'] != 0) {
		$CurrentQueue  = $CurrentPlanet['b_building_id'];
		if ($CurrentQueue != 0) {
			$QueueArray    = explode ( ";", $CurrentQueue );
			$ActualCount   = count ( $QueueArray );
		}

		$BuildArray   = explode (",", $QueueArray[0]);
		$BuildEndTime = floor($BuildArray[3]);
		$BuildMode    = $BuildArray[4];
		$Element      = $BuildArray[0];
		array_shift ( $QueueArray );

		if ($BuildMode == 'destroy') {
			$ForDestroy = true;
		} else {
			$ForDestroy = false;
		}

		if ($BuildEndTime <= time()) {
			// Mise a jours des points
			$Needed                        = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, $ForDestroy);
			$Units                         = $Needed['metal'] + $Needed['crystal'] + $Needed['deuterium'];
			if ($ForDestroy == false) {
				// Mise a jours de l'XP Minier
				if (in_array($Element, $XPBuildings)) {
					$AjoutXP                        = $Units / 1000;
					$CurrentUser['xpminier']       += $AjoutXP;
				}
			} else {
				// Mise a jours de l'XP Minier
				if (in_array($Element, $XPBuildings)) {
					$AjoutXP                        = ($Units * 3) / 1000;
					$CurrentUser['xpminier']       -= $AjoutXP;
				}
			}

			$current = intval($CurrentPlanet['field_current']);
			$max     = intval($CurrentPlanet['field_max']);
			// Pour une lune
			if ($CurrentPlanet['planet_type'] == 3) {
				if ($Element == 41) {
					// Base Lunaire
					$current += 1;
					$max     += FIELDS_BY_MOONBASIS_LEVEL;
					$CurrentPlanet[$resource[$Element]]++;
				} elseif ($Element != 0) {
					if ($ForDestroy == false) {
						$current += 1;
						$CurrentPlanet[$resource[$Element]]++;
					} else {
						$current -= 1;
						$CurrentPlanet[$resource[$Element]]--;
					}
				}
			} elseif ($CurrentPlanet['planet_type'] == 1) {
				if ($ForDestroy == false) {
					$current += 1;
					$CurrentPlanet[$resource[$Element]]++;
				} else {
					$current -= 1;
					$CurrentPlanet[$resource[$Element]]--;
				}
			}
			if (count ( $QueueArray ) == 0) {
				$NewQueue = 0;
			} else {
				$NewQueue = implode (";", $QueueArray );
			}
			$CurrentPlanet['b_building']    = 0;
			$CurrentPlanet['b_building_id'] = $NewQueue;
			$CurrentPlanet['field_current'] = $current;
			$CurrentPlanet['field_max']     = $max;

			$QryUpdatePlanet  = "UPDATE `{{table}}` SET ";
			$QryUpdatePlanet .= "`".$resource[$Element]."` = '".$CurrentPlanet[$resource[$Element]]."', ";
			// Mise a 0 de l'heure de fin de construction ...
			// Ca va activer la mise en place du batiment suivant de la queue
			$QryUpdatePlanet .= "`b_building` = '". $CurrentPlanet['b_building'] ."' , ";
			$QryUpdatePlanet .= "`b_building_id` = '". $CurrentPlanet['b_building_id'] ."' , ";
			$QryUpdatePlanet .= "`field_current` = '" . $CurrentPlanet['field_current'] . "', ";
			$QryUpdatePlanet .= "`field_max` = '" . $CurrentPlanet['field_max'] . "' ";
			$QryUpdatePlanet .= "WHERE ";
			$QryUpdatePlanet .= "`id` = '" . $CurrentPlanet['id'] . "';";
			doquery( $QryUpdatePlanet, 'planets');

			$QryUpdateUser    = "UPDATE `{{table}}` SET ";
			$QryUpdateUser   .= "`xpminier` = '".$CurrentUser['xpminier']."' ";
			$QryUpdateUser   .= "WHERE ";
			$QryUpdateUser   .= "`id` = '" . $CurrentUser['id'] . "';";
			doquery( $QryUpdateUser, 'users');

			$RetValue = true;
		} else {
			$RetValue = false;
		}
	} else {
		$CurrentPlanet['b_building']    = 0;
		$CurrentPlanet['b_building_id'] = 0;

		$QryUpdatePlanet  = "UPDATE `{{table}}` SET ";
		$QryUpdatePlanet .= "`b_building` = '". $CurrentPlanet['b_building'] ."' , ";
		$QryUpdatePlanet .= "`b_building_id` = '". $CurrentPlanet['b_building_id'] ."' ";
		$QryUpdatePlanet .= "WHERE ";
		$QryUpdatePlanet .= "`id` = '" . $CurrentPlanet['id'] . "';";
		doquery( $QryUpdatePlanet, 'planets');

		$RetValue = false;
	}

	return $RetValue;
}
?>