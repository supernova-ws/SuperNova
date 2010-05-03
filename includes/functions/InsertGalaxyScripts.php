<?php

/**
 * InsertGalaxyScripts.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function InsertGalaxyScripts ( $CurrentPlanet ) {
	global $lang;
	$Script  = "<div style=\"top: 10px;\" id=\"content\">";
	$Script .= "<script language=\"JavaScript\">\n";
	$Script .= "function galaxy_submit(value) {\n";
	$Script .= "	document.getElementById('auto').name = value;\n";
	$Script .= "	document.getElementById('galaxy_form').submit();\n";
	$Script .= "}\n\n";

	$Script .= "function fenster(target_url,win_name) {\n";
	$Script .= "	var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');\n";
	$Script .= "	new_win.focus();\n";
	$Script .= "}\n";
	$Script .= "</script>\n";

	$Script .= "<script language=\"JavaScript\" src=\"scripts/tw-sack.js\"></script>\n";

	$Script .= "<script type=\"text/javascript\">\n\n";
	$Script .= "var ajax = new sack();\n";
	$Script .= "var strInfo = \"\";\n";

	$Script .= "function whenResponse () {\n";
	$Script .= "	retVals   = this.response.split(\"|\");\n";
	$Script .= "	Message   = retVals[0];\n";
	$Script .= "	Infos     = retVals[1];\n";
	$Script .= "	retVals   = Infos.split(\" \");\n";
	$Script .= "	UsedSlots = retVals[0];\n";
	$Script .= "	SpyProbes = retVals[1];\n";
	$Script .= "	Recyclers = retVals[2];\n";
	$Script .= "	Missiles  = retVals[3];\n";
	$Script .= "	retVals   = Message.split(\";\");\n";
	$Script .= "	CmdCode   = retVals[0];\n";
	$Script .= "	strInfo   = retVals[1];\n";
	$Script .= "	addToTable(\"done\", \"success\");\n";
	$Script .= "	changeSlots( UsedSlots );\n";
	$Script .= "	setShips(\"probes\", SpyProbes );\n";
	$Script .= "	setShips(\"recyclers\", Recyclers );\n";
	$Script .= "	setShips(\"missiles\", Missiles );\n";
	$Script .= "}\n\n";

	$Script .= "function doit (order, galaxy, system, planet, planettype, shipcount) {\n";
	$Script .= "	ajax.requestFile = \"flotenajax.php?action=send\";\n";
	$Script .= "	ajax.runResponse = whenResponse;\n";
	$Script .= "	ajax.execute = true;\n\n";
	$Script .= "	ajax.setVar(\"thisgalaxy\", ". $CurrentPlanet["galaxy"] .");\n";
	$Script .= "	ajax.setVar(\"thissystem\", ". $CurrentPlanet["system"] .");\n";
	$Script .= "	ajax.setVar(\"thisplanet\", ". $CurrentPlanet["planet"] .");\n";
	$Script .= "	ajax.setVar(\"thisplanettype\", ". $CurrentPlanet["planet_type"] .");\n";
	$Script .= "	ajax.setVar(\"mission\", order);\n";
	$Script .= "	ajax.setVar(\"galaxy\", galaxy);\n";
	$Script .= "	ajax.setVar(\"system\", system);\n";
	$Script .= "	ajax.setVar(\"planet\", planet);\n";
	$Script .= "	ajax.setVar(\"planettype\", planettype);\n";
	$Script .= "	if (order == 6)\n";
	$Script .= "		ajax.setVar(\"ship210\", shipcount);\n";
	$Script .= "	if (order == 7) {\n";
	$Script .= "		ajax.setVar(\"ship208\", 1);\n\n";
	$Script .= "		ajax.setVar(\"ship203\", 2);\n\n";
	$Script .= "	}\n";
	$Script .= "	if (order == 8)\n";
	$Script .= "		ajax.setVar(\"ship209\", shipcount);\n\n";
	$Script .= "	ajax.runAJAX();\n";
	$Script .= "}\n\n";

	$Script .= "function addToTable(strDataResult, strClass) {\n";
	$Script .= "	var e = document.getElementById('fleetstatusrow');\n";
	$Script .= "	var e2 = document.getElementById('fleetstatustable');\n";
	$Script .= "	e.style.display = '';\n";
	$Script .= "	if(e2.rows.length > 2) {\n";
	$Script .= "		e2.deleteRow(2);\n";
	$Script .= "	}\n";
	$Script .= "	var row = e2.insertRow(0);\n";
	$Script .= "	var td1 = document.createElement(\"td\");\n";
	$Script .= "	var td1text = document.createTextNode(strInfo);\n";
	$Script .= "	td1.appendChild(td1text);\n";
	$Script .= "	var td2 = document.createElement(\"td\");\n";
	$Script .= "	var span = document.createElement(\"span\");\n";
	$Script .= "	var spantext = document.createTextNode(strDataResult);\n";
	$Script .= "	var spanclass = document.createAttribute(\"class\");\n";
	$Script .= "	spanclass.nodeValue = strClass;\n";
	$Script .= "	span.setAttributeNode(spanclass);\n";
	$Script .= "	span.appendChild(spantext);\n";
	$Script .= "	td2.appendChild(span);\n";
	$Script .= "	row.appendChild(td1);\n";
	$Script .= "	row.appendChild(td2);\n";
	$Script .= "}\n\n";

	$Script .= "function changeSlots(slotsInUse) {\n";
	$Script .= "	var e = document.getElementById('slots');\n";
	$Script .= "	e.innerHTML = slotsInUse;\n";
	$Script .= "}\n\n";

	$Script .= "function setShips(ship, count) {\n";
	$Script .= "	var e = document.getElementById(ship);\n";
	$Script .= "	e.innerHTML = count;\n";
	$Script .= "}\n";

	$Script .= "</script>\n";

	return $Script;
}
?>