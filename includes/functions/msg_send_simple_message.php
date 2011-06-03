<?php

/**
 * SendSimpleMessage.php
 *
 * @version 1.3
 * @copyright 2008 by Chlorel for XNova
   Revision history :
   1.0 - Initial release (mise en fonction generique)
   1.1 - Ajout gestion des messages par type pour le module de messages
   1.2 - Correction bug (addslashes pour les zone texte pouvant contenir une apostrophe)
   1.3 - Correction bug (integration de la variable $Time pour afficher l'heure exacte de l'evenement pour les flottes)
   1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
         [+] Ability to mass-send emails. Mass-sending done via two mysql queries - one for messages table, one for users table
   1.5 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
         [+] SuperMassMailing - authlevel=3 player can send messages to whole server ('*' as $owners)
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

  msg_send_simple_message($sendList, $GLOBALS['user']['id'], $GLOBALS['time_now'], MSG_TYPE_ALLIANCE, $GLOBALS['user']['username'], $subject, sys_bbcodeParse($message, true));

  return $list;
}

function msg_send_simple_message($owners, $sender, $timestamp, $message_type, $from, $subject, $text, $escaped = false)
{
  global $sn_message_class_list, $sn_message_groups, $user;

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

  $message_class_name = $sn_message_class_list[$message_type]['name'];
  $message_class_name_total = $sn_message_class_list[MSG_TYPE_NEW]['name'];

  $QryInsertMessage = 'INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) ';
  $QryUpdateUser = "UPDATE {{users}} SET `{$message_class_name}` = `{$message_class_name}` + 1, `{$message_class_name_total}` = `{$message_class_name_total}` + 1 ";
  if($owners[0] == '*')
  {
    if($user['authlevel'] < 3)
    {
      return false;
    }
    $QryInsertMessage .= "SELECT `id`, 0, unix_timestamp(now()), 1, '{$from}', '{$subject}', '{$text}' FROM {{users}}; ";
  }
  else
  {
    $insert_values = array();
    $insert_template = "('%u'," . str_replace('%', '%%', " '{$sender}', '{$timestamp}', '{$message_type}', '{$from}', '{$subject}', '{$text}')");

    foreach ($owners as $owner)
    {
//      $insert_values[] = "('{$owner}', '{$sender}', '{$timestamp}', '{$message_type}', '{$from}', '{$subject}', '{$text}')";
      if($user['id'] != $owner)
      {
        $owner_row = doquery("SELECT * FROM {{users}} WHERE id = {$owner} LIMIT 1;", '', true);
        sys_user_options_unpack($owner_row);
      }
      else
      {
        $owner_row = &$user;
      }

      if(!in_array($message_type, $sn_message_groups['switchable']) || $owner_row["opt_{$message_class_name}"])
      {
        $insert_values[] = sprintf($insert_template, $owner);
      }
    }

    if(empty($insert_values))
    {
      return;
    }

    $QryInsertMessage .= 'VALUES ' . implode(',', $insert_values) . ';';
    $QryUpdateUser .= 'WHERE `id` IN (' . implode(',', $owners) . ');';
  }

  doquery($QryInsertMessage);
  doquery($QryUpdateUser);

  if(in_array($user['id'], $owners) || $owners[0] == '*')
  {
    $user[$message_class_name]++;
    $user[$message_class_name_total]++;
  }
}

?>
