<?php

/**
 * ShowGalaxyFooter.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function ShowGalaxyFooter ( $Galaxy, $System,  $CurrentMIP, $CurrentRC, $CurrentSP) {
	global $lang, $maxfleet_count, $fleetmax, $planetcount;

	$Result  = "";
	if ($planetcount == 1) {
		$PlanetCountMessage = $planetcount ." ". $lang['gf_cntmone'];
	} elseif ($planetcount == 0) {
		$PlanetCountMessage = $lang['gf_cntmnone'];
	} else {
		$PlanetCountMessage = $planetcount." " . $lang['gf_cntmsome'];
	}
	$LegendPopup = GalaxyLegendPopup ();
	$Recyclers   = pretty_number($CurrentRC);
	$SpyProbes   = pretty_number($CurrentSP);

	$Result .= "\n";
	$Result .= "<tr>";
	$Result .= "<th width=\"30\">16</th>";
	$Result .= "<th colspan=7>";
	$Result .= "<a href=fleet.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=16;planettype=1&amp;target_mission=15>". $lang['gf_unknowsp'] ."</a>";
	$Result .= "</th>";
	$Result .= "</tr>";

	$Result .= "\n";
	$Result .= "<tr>";
	$Result .= "<td class=c colspan=6>( ".$PlanetCountMessage." )</td>";
	$Result .= "<td class=c colspan=2>". $LegendPopup ."</td>";
	$Result .= "</tr>";

	$Result .= "\n";
	$Result .= "<tr>";
	$Result .= "<td class=c colspan=3><span id=\"missiles\">". $CurrentMIP ."</span> ". $lang['gf_mi_title'] ."</td>";
	$Result .= "<td class=c colspan=3><span id=\"slots\">". $maxfleet_count ."</span>/". $fleetmax ." ". $lang['gf_fleetslt'] ."</td>";
	$Result .= "<td class=c colspan=2>";
	$Result .= "<span id=\"recyclers\">". $Recyclers ."</span> ". $lang['gf_rc_title'] ."<br>";
	$Result .= "<span id=\"probes\">". $SpyProbes ."</span> ". $lang['gf_sp_title'] ."</td>";
	$Result .= "</tr>";

	$Result .= "\n";
	$Result .= "<tr style=\"display: none;\" id=\"fleetstatusrow\">";
	$Result .= "<th class=c colspan=8><!--<div id=\"fleetstatus\"></div>-->";
	$Result .= "<table style=\"font-weight: bold\" width=\"100%\" id=\"fleetstatustable\">";
	$Result .= "<!-- will be filled with content later on while processing ajax replys -->";
//	$Result .= "<tr style=\"display: none; align:left\" id=\"fleetstatusrow\">";
//	$Result .= "<th colspan=8><div style=\"align:left\" id=\"fleetstatus\"></div></th>";
//	$Result .= "</tr>";
	$Result .= "</table>";
	$Result .= "</th>";
	$Result .= "\n";
	$Result .= "</tr>";
/*
<tr style=\"display: none;\" id=\"fleetstatusrow\"><th colspan="8"><!--<div id="fleetstatus"></div>-->
<table style="font-weight: bold;" width=100% id="fleetstatustable">
<!-- will be filled with content later on while processing ajax replys -->
</table>
</th>
</tr>
*/
	return $Result;
}
?>