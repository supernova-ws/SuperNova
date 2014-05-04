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
  global $time_now, $user;

  $ally_id = $ally_id ? $ally_id : $user['ally_id'];

  $query = db_user_list_player_by_ally($ally_id, $ally_rank_id, false, 'id, username');

  $list = '';
  while ($u = mysql_fetch_assoc($query))
  {
    $sendList[] = $u['id'];
    $list .= "<br>{$u['username']} ";
  }

  msg_send_simple_message($sendList, $user['id'], $time_now, MSG_TYPE_ALLIANCE, $user['username'], $subject, sys_bbcodeParse($message, true));

  return $list;
}

function msg_send_simple_message($owners, $sender, $timestamp, $message_type, $from, $subject, $text, $escaped = false, $force = false)
{
  global $config, $user, $sn_message_class_list, $time_now;

  if(!$owners)
  {
    return;
  }

  $timestamp = $timestamp ? $timestamp : $time_now;
  $sender = intval($sender);
  if(!is_array($owners))
  {
    $owners = array($owners);
  }

  if(!$escaped)
  {
    $from = mysql_real_escape_string($from);
    $subject = mysql_real_escape_string($subject);
    $text = mysql_real_escape_string($text);
  }

  $text_unescaped = stripslashes(str_replace(array('\r\n', "\r\n"), "<br />", $text));

  $message_class = $sn_message_class_list[$message_type];
  $message_class_email = $message_class['email'];
  $message_class_switchable = $message_class['switchable'];
  $message_class_name = $message_class['name'];

  $message_class_name_total = $sn_message_class_list[MSG_TYPE_NEW]['name'];

  if($owners[0] == '*')
  {
    if($user['authlevel'] < 3)
    {
      return false;
    }
    // TODO Добавить $timestamp - рассылка может быть и отсроченной
    // TODO Добавить $sender - рассылка может быть и от кого-то
    db_message_insert_all($message_type, $from, $subject, $text);
    $owners = array();
  }
  else
  {
    $insert_values = array();
    $insert_template = "('%u'," . str_replace('%', '%%', " '{$sender}', '{$timestamp}', '{$message_type}', '{$from}', '{$subject}', '{$text}')");

    foreach ($owners as $owner)
    {
      if($user['id'] != $owner)
      {
        $owner_row = db_user_by_id($owner);
      }
      else
      {
        $owner_row = $user;
      }
      sys_user_options_unpack($owner_row);

      if($force || !$message_class_switchable || $owner_row["opt_{$message_class_name}"])
      {
        $insert_values[] = sprintf($insert_template, $owner);
      }

      if($message_class_email && $config->game_email_pm && $owner_row["opt_email_{$message_class_name}"])
      {
        @$result = mymail($owner_row['email'], $subject, $text_unescaped, '', true);
      }
    }

    if(empty($insert_values))
    {
      return;
    }

    doquery($QryInsertMessage = 'INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) ' .
      'VALUES ' . implode(',', $insert_values));
  }
  db_user_list_set_mass_mail("`{$message_class_name}` = `{$message_class_name}` + 1, `{$message_class_name_total}` = `{$message_class_name_total}` + 1",  $owners);

  if(in_array($user['id'], $owners) || $owners[0] == '*')
  {
    $user[$message_class_name]++;
    $user[$message_class_name_total]++;
  }
}
