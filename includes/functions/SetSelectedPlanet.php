<?php

/**
 * SetSelectedPlanet.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function SetSelectedPlanet ( &$CurrentUser ) {
	global $_GET;

	$SelectPlanet  = $_GET['cp'];
	$RestorePlanet = $_GET['re'];

	if (isset($SelectPlanet)      &&
		is_numeric($SelectPlanet) &&
		isset($RestorePlanet)     &&
		$RestorePlanet == 0) {
		$IsPlanetMine   = doquery("SELECT `id` FROM {{table}} WHERE `id` = '". $SelectPlanet ."' AND `id_owner` = '". $CurrentUser['id'] ."';", 'planets', true);
		if ($IsPlanetMine) {
			// Ouaip elle est a moi ... Donc ... on met la met comme planete courrante
			$CurrentUser['current_planet'] = $SelectPlanet;
			// Puis tant qu'a faire ... On l'enregistre aussi sait on jamais
			doquery("UPDATE {{table}} SET `current_planet` = '". $SelectPlanet ."' WHERE `id` = '".$CurrentUser['id']."';", 'users');
		}
	}
}
?>