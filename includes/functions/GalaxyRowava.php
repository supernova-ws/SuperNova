<?php

/**
 * GalaxyRowava.php
 *
 * @version 1.0
 * @copyright 2008 By Dr.Isaacs for XNova Germany
 */

function GalaxyRowava ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, $PlanetType ) {
	global $lang, $user;

	// Joueur
	$Result  = "<th width=30>";
	if ($GalaxyRowUser && $GalaxyRowPlanet["destruyed"] == 0) {
		if       ($GalaxyRowUser['bana'] == 1 AND
				  $GalaxyRowUser['urlaubs_modus'] == 1) {
			$Systemtatus2 = $lang['vacation_shortcut']." <a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
			$Systemtatus  = "<span class=\"vacation\">";
		
		} else {
			$Systemtatus2 = "";
			$Systemtatus  = "";
		}

				$Result .= "<a style=\"cursor: pointer;\"";
		$Result .= " onmouseover='return overlib(\"";
		$Result .= "<table width=190>";
		if ($GalaxyRowUser['id'] != $user['id']) {
		}
		$Result .= " onmouseout='return nd();'>";
		 $Result .= ""; 
//        $Result .= $GalaxyRowPlanet["name"]; 
        $Result .= "</a>"; 


	}
	$Result .= "</th>";

	return $Result;
}
?>