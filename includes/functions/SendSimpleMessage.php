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
function SendSimpleMessage ( $Owners, $Sender, $Time, $Type, $From, $Subject, $Message, $escaped = false) {
  global $messfields;
  global $user;

  if (!$Time)
    $Time = time();

  if(!is_array($Owners))
    $Owners = array($Owners);

  if(!$escaped){
    $From    = SYS_mysqlSmartEscape( $From    );
    $Subject = SYS_mysqlSmartEscape( $Subject );
    $Message = SYS_mysqlSmartEscape( $Message );
  }

  $QryInsertMessage  = "INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) VALUES ";
  $QryUpdateUser  = "UPDATE {{users}} SET `".$messfields[$Type]."` = `".$messfields[$Type]."` + 1, `".$messfields[100]."` = `".$messfields[100]."` + 1 WHERE `id` IN (";

  foreach($Owners as $Owner){
    $QryInsertMessage .= " ('{$Owner}', '{$Sender}', '{$Time}', '{$Type}', '{$From}', '{$Subject}', '{$Message}'),";
    $QryUpdateUser .= "'{$Owner}',";
  }
  doquery( substr($QryInsertMessage, 0, -1) );
  doquery( substr($QryUpdateUser, 0, -1) . ')' );
  if($user['id'] == $Owner)
  {
    $user[$messfields[$Type]]++;
    $user[$messfields[100]]++;
  }
}

// Revision history :
// 1.0 - Initial release (mise en fonction generique)
// 1.1 - Ajout gestion des messages par type pour le module de messages
// 1.2 - Correction bug (addslashes pour les zone texte pouvant contenir une apostrophe)
// 1.3 - Correction bug (integration de la variable $Time pour afficher l'heure exacte de l'evenement pour les flottes)
// 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
//       [+] Ability to mass-send emails. Mass-sending done via two mysql queries - one for messages table, one for users table
?>