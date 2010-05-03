<?php

/**
 * GetElementRessources.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Retourne un tableau des ressources necessaires par type pour le lot d'elements
// $Element   -> L'element visé
// $Count     -> Nombre d'elements a construire
function GetElementRessources ( $Element, $Count ) {
	global $pricelist;

	$ResType['metal']     = ($pricelist[$Element]['metal']     * $Count);
	$ResType['crystal']   = ($pricelist[$Element]['crystal']   * $Count);
	$ResType['deuterium'] = ($pricelist[$Element]['deuterium'] * $Count);

	return $ResType;
}
?>