<?php

/**
 * ShowBuildingQueue.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------
// Construit le code html pour afficher une liste de construction en cours ...
// Données en entree :
// $CurrentPlanet -> Planete sur la quelle on affiche la page de construction de batiments
// $CurrentUser   -> Joueur courrant (inutilisé pour le moment ... Mais sait on jamais)
// Données en sortie :
// $ListIDRow     -> lignes d'une table de 3 colonnes integrable au dessus (ou au dessous) de la page de
//                   construction des batiments
// Necessite :
// Attention il faut avoir integré une fois au moins le script de controle en java ...
// Donc lancer : InsertBuildListScript () avant la balise <table> de la page
//
function ShowBuildingQueue ( $CurrentPlanet, $CurrentUser ) {
	global $lang;

	$CurrentQueue  = $CurrentPlanet['b_building_id'];
	$QueueID       = 0;
	if ($CurrentQueue != 0) {
		// Queue de fabrication documentée ... Y a au moins 1 element a construire !
		$QueueArray    = explode ( ";", $CurrentQueue );
		// Compte le nombre d'elements
		$ActualCount   = count ( $QueueArray );
	} else {
		// Queue de fabrication vide
		$QueueArray    = "0";
		$ActualCount   = 0;
	}

	$ListIDRow    = "";
	if ($ActualCount != 0) {
		$PlanetID     = $CurrentPlanet['id'];
		for ($QueueID = 0; $QueueID < $ActualCount; $QueueID++) {
			// Chaque element de la liste de fabrication est un tableau de 5 données
			// [0] -> Le batiment
			// [1] -> Le niveau du batiment
			// [2] -> La durée de construction
			// [3] -> L'heure théorique de fin de construction
			// [4] -> type d'action
			$BuildArray   = explode (",", $QueueArray[$QueueID]);
			$BuildEndTime = floor($BuildArray[3]);
			$CurrentTime  = floor(time());
			if ($BuildEndTime >= $CurrentTime) {
				$ListID       = $QueueID + 1;
				$Element      = $BuildArray[0];
				$BuildLevel   = $BuildArray[1];
				$BuildMode    = $BuildArray[4];
				$BuildTime    = $BuildEndTime - time();
				$ElementTitle = $lang['tech'][$Element];

				if ($ListID > 0) {
					$ListIDRow .= "<tr>";
					if ($BuildMode == 'build') {
						$ListIDRow .= "	<td class=\"l\" colspan=\"2\">". $ListID .".: ". $ElementTitle ." ". $BuildLevel ."</td>";
					} else {
						$ListIDRow .= "	<td class=\"l\" colspan=\"2\">". $ListID .".: ". $ElementTitle ." ". $BuildLevel ." ". $lang['destroy'] ."</td>";
					}
					$ListIDRow .= "	<td class=\"k\">";
					if ($ListID == 1) {
						$ListIDRow .= "		<div id=\"blc\" class=\"z\">". $BuildTime ."<br>";
						$ListIDRow .= "		<a href=\"buildings.php?listid=". $ListID ."&amp;cmd=cancel&amp;planet=". $PlanetID ."\">". $lang['DelFirstQueue'] ."</a></div>";
						$ListIDRow .= "		<script language=\"JavaScript\">";
						$ListIDRow .= "			pp = \"". $BuildTime ."\";\n";      // temps necessaire (a compter de maintenant et sans ajouter time() )
						$ListIDRow .= "			pk = \"". $ListID ."\";\n";         // id index (dans la liste de construction)
						$ListIDRow .= "			pm = \"cancel\";\n";                // mot de controle
						$ListIDRow .= "			pl = \"". $PlanetID ."\";\n";       // id planete
						$ListIDRow .= "			t();\n";
						$ListIDRow .= "		</script>";
						$ListIDRow .= "		<strong color=\"lime\"><br><font color=\"lime\">". date("j/m H:i:s" ,$BuildEndTime) ."</font></strong>";
					} else {
						$ListIDRow .= "		<font color=\"red\">";
						$ListIDRow .= "		<a href=\"buildings.php?listid=". $ListID ."&amp;cmd=remove&amp;planet=". $PlanetID ."\">". $lang['DelFromQueue'] ."</a></font>";
					}
					$ListIDRow .= "	</td>";
					$ListIDRow .= "</tr>";
				}
			}
		}
	}

	$RetValue['lenght']    = $ActualCount;
	$RetValue['buildlist'] = $ListIDRow;

	return $RetValue;
}
?>