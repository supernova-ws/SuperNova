<?php

/**
 * SendSimpleMessage.php
 *
 * @version 1.3
 * @copyright 2008 by Chlorel for XNova
 */

// Envoi d'un message simple
//
// $Owner   -> destinataire
// $Sender  -> ID de l'emeteur
// $Time    -> Heure theorique a laquelle l'evenement s'est produit
// $Type    -> Type de message (pour classement dans les onglets message plus tard)
// $From    -> Description de l'emeteur
// $Subject -> Sujet
// $Message -> Le message lui meme !!
//
function SendSimpleMessage ( $Owner, $Sender, $Time, $Type, $From, $Subject, $Message) {
	global $messfields;

	if ($Time == '') {
		$Time = time();
	}

	$QryInsertMessage  = "INSERT INTO {{table}} SET ";
	$QryInsertMessage .= "`message_owner` = '". $Owner ."', ";
	$QryInsertMessage .= "`message_sender` = '". $Sender ."', ";
	$QryInsertMessage .= "`message_time` = '" . $Time . "', ";
	$QryInsertMessage .= "`message_type` = '". $Type ."', ";
	$QryInsertMessage .= "`message_from` = '". addslashes( $From ) ."', ";
	$QryInsertMessage .= "`message_subject` = '". addslashes( $Subject ) ."', ";
	$QryInsertMessage .= "`message_text` = '". addslashes( $Message ) ."';";
	doquery( $QryInsertMessage, 'messages');

	$QryUpdateUser  = "UPDATE {{table}} SET ";
	$QryUpdateUser .= "`".$messfields[$Type]."` = `".$messfields[$Type]."` + 1, ";
	$QryUpdateUser .= "`".$messfields[100]."` = `".$messfields[100]."` + 1 ";
	$QryUpdateUser .= "WHERE ";
	$QryUpdateUser .= "`id` = '". $Owner ."';";
	doquery( $QryUpdateUser, 'users');

}

// Revision history :
// 1.0 - Initial release (mise en fonction generique)
// 1.1 - Ajout gestion des messages par type pour le module de messages
// 1.2 - Correction bug (addslashes pour les zone texte pouvant contenir une apostrophe)
// 1.3 - Correction bug (integration de la variable $Time pour afficher l'heure exacte de l'evenement pour les flottes)
?>