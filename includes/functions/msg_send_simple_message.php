<?php

/**
 * SendSimpleMessage.php
 *
 * @version 1.3
 * @copyright 2008 by Chlorel for XNova
// Revision history :
// 1.0 - Initial release (mise en fonction generique)
// 1.1 - Ajout gestion des messages par type pour le module de messages
// 1.2 - Correction bug (addslashes pour les zone texte pouvant contenir une apostrophe)
// 1.3 - Correction bug (integration de la variable $Time pour afficher l'heure exacte de l'evenement pour les flottes)
// 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
//       [+] Ability to mass-send emails. Mass-sending done via two mysql queries - one for messages table, one for users table
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

function msg_ali_send($message, $subject, $ally_rank_id = 0, $ally_id = 0)
{
  global $user;

  if(!$ally_id)
  {
    $ally_id = $user['ally_id'];
  }

  $query = "SELECT id, username FROM {{users}} WHERE ally_id = '{$ally_id}'";
  if ($ally_rank_id >= 0) {
    $query .= " AND ally_rank_id = {$ally_rank_id}";
  }
  $query = doquery($query);

  $list = '';
  while ($u = mysql_fetch_assoc($query)) {
    $sendList[] = $u['id'];
    $list .= "<br>{$u['username']} ";
  }

  msg_send_simple_message($sendList, $GLOBALS['user']['id'], $GLOBALS['time_now'], 2, $GLOBALS['user']['username'], $subject, sys_bbcodeParse($message, true));

  return $list;
}

function msg_send_simple_message($owners, $sender, $timestamp, $message_type, $from, $subject, $text, $escaped = false)
{
  global $messfields, $user;

  $timestamp = $timestamp ? $timestamp : $GLOBALS['time_now'];

  if (!is_array($owners))
  {
    $owners = array($owners);
  }

  if (!$escaped)
  {
    $from = mysql_real_escape_string($from);
    $subject = mysql_real_escape_string($subject);
    $text = mysql_real_escape_string($text);
  }

  $QryInsertMessage = "INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) VALUES ";
  $QryUpdateUser = "UPDATE {{users}} SET `" . $messfields[$message_type] . "` = `" . $messfields[$message_type] . "` + 1, `" . $messfields[100] . "` = `" . $messfields[100] . "` + 1 WHERE `id` IN (";

  foreach ($owners as $owner)
  {
    $QryInsertMessage .= " ('{$owner}', '{$sender}', '{$timestamp}', '{$message_type}', '{$from}', '{$subject}', '{$text}'),";
    $QryUpdateUser .= "'{$owner}',";
  }

  doquery(substr($QryInsertMessage, 0, -1));
  doquery(substr($QryUpdateUser, 0, -1) . ')');
  if ($user['id'] == $owner)
  {
    $user[$messfields[$message_type]]++;
    $user[$messfields[100]]++;
  }
}

?>
