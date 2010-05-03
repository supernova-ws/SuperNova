<?php

/**
 * GetBuildingPrice.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

// Verifie si un element est achetable au moment demandé
// $CurrentUser   -> Le Joueur lui meme
// $CurrentPlanet -> La planete sur laquelle l'Element doit etre construit
// $Element       -> L'Element que l'on convoite
// $Incremental   -> true  par defaut pour un batiment ou une recherche
//                -> false pour une defense ou un vaisseau
// $ForDestroy    -> false par defaut pour une construction
//                -> true pour calculer la demi valeur du niveau en cas de destruction
//
// Reponse        -> un tableau avec les couts de construction (a ajouter ou retirer des ressources)
function GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false) {
	global $pricelist, $resource;

	if ($Incremental) {
		$level = ($CurrentPlanet[$resource[$Element]]) ? $CurrentPlanet[$resource[$Element]] : $CurrentUser[$resource[$Element]];
	}

	$array = array('metal', 'crystal', 'deuterium', 'energy_max');
	foreach ($array as $ResType) {
		if ($Incremental) {
			$cost[$ResType] = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
		} else {
			$cost[$ResType] = floor($pricelist[$Element][$ResType]);
		}

		if ($ForDestroy == true) {
			$cost[$ResType]  = floor($cost[$ResType]) / 2;
			$cost[$ResType] /= 2;
		}
	}

	return $cost;
}
?>