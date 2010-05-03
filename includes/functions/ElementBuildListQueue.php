<?php

/**
 * ElementBuildListQueue.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function ElementBuildListQueue ( $CurrentUser, $CurrentPlanet ) {
// Jamais appelé pour le moment donc totalement modifiable !

/*
alter table `ogame`.`game_planets`
change `name` `name` varchar (255) NULL COLLATE cp1251_general_ci,
change `b_building_id` `b_building_id` text NULL COLLATE cp1251_general_ci,
change `b_tech_id` `b_tech_id` text NULL COLLATE cp1251_general_ci,
change `b_hangar_id` `b_hangar_id` text NULL COLLATE cp1251_general_ci,
change `image` `image` varchar (32) DEFAULT 'normaltempplanet01' NOT NULL COLLATE cp1251_general_ci,
change `b_building_queue` `b_building_queue` text NULL COLLATE cp1251_general_ci,
change `unbau` `unbau` varchar (100) NULL COLLATE cp1251_general_ci;

*/
	global $lang, $pricelist;

	// Array del b_hangar_id
	$b_building_id = explode(';', $CurrentPlanet['b_building_queue']);

	$a = $b = $c = "";
	foreach($b_hangar_id as $n => $array) {
		if ($array != '') {
			$array = explode(',', $array);
			// calculamos el tiempo
			$time = GetBuildingTime($user, $CurrentPlanet, $array[0]);
			$totaltime += $time * $array[1];
			$c .= "$time,";
			$b .= "'{$lang['tech'][$array[0]]}',";
			$a .= "{$array[1]},";
		}
	}

	$parse = $lang;
	$parse['a'] = $a;
	$parse['b'] = $b;
	$parse['c'] = $c;
	$parse['b_hangar_id_plus'] = $CurrentPlanet['b_hangar'];

	$parse['pretty_time_b_hangar'] = pretty_time($totaltime - $CurrentPlanet['b_hangar']); // //$CurrentPlanet['last_update']

	$text .= parsetemplate(gettemplate('buildings_script'), $parse);

	return $text;
}
?>