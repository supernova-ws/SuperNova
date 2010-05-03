<?php

/**
 * SortUserPlanets.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function SortUserPlanets ( $CurrentUser ) {
	$Order = ( $CurrentUser['planet_sort_order'] == 1 ) ? "DESC" : "ASC" ;
	$Sort  = $CurrentUser['planet_sort'];

	$QryPlanets  = "SELECT `id`, `name`, `galaxy`, `system`, `planet`, `planet_type` FROM {{table}} WHERE `id_owner` = '". $CurrentUser['id'] ."' ORDER BY ";
	if       ( $Sort == 0 ) {
		$QryPlanets .= "`id` ". $Order;
	} elseif ( $Sort == 1 ) {
		$QryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` ". $Order;
	} elseif ( $Sort == 2 ) {
		$QryPlanets .= "`name` ". $Order;
	}
	$Planets = doquery ( $QryPlanets, 'planets');

	return $Planets;
}
?>