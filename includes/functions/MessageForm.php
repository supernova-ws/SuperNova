<?php

/**
 * MessageForm.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// Parametres en entrée:
// $Title    -> Titre du Message
// $Message  -> Texte contenu dans le message
// $Goto     -> Adresse de saut pour le formulaire
// $Button   -> Bouton de validation du formulaire
// $TwoLines -> Sur une ou sur 2 lignes
//
// Retour
//           -> Une chaine formatée affichable en html
function MessageForm ($Title, $Message, $Goto = '', $Button = ' ok ', $TwoLines = false) {
	$Form  = "<center>";
	$Form .= "<form action=\"". $Goto ."\" method=\"post\">";
	$Form .= "<table width=\"519\">";
	$Form .= "<tr>";
		$Form .= "<td class=\"c\" colspan=\"2\">". $Title ."</td>";
	$Form .= "</tr><tr>";
	if ($TwoLines == true) {
		$Form .= "<th colspan=\"2\">". $Message ."</th>";
		$Form .= "</tr><tr>";
		$Form .= "<th colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"". $Button ."\"></th>";
	} else {
		$Form .= "<th colspan=\"2\">". $Message ."<input type=\"submit\" value=\"". $Button ."\"></th>";
	}
	$Form .= "</tr>";
	$Form .= "</table>";
	$Form .= "</form>";
	$Form .= "</center>";

	return $Form;
}

// Release History
// - 1.0 Mise en fonction, Documentation
?>