<?php

/*
#############################################################################
#  Filename: buddy.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 43a16.13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'buddy_buddies' => 'Friends',
  'buddy_request_text' => 'Request text',
  'buddy_request_text_default' => 'Please add me to your friendlist',
  'buddy_request_none' => 'There are no friends and friendship requests',
  'buddy_request_write_header' => 'New friendship request',
  'buddy_request_player_name' => 'Player name',
  'buddy_request_accept' => 'Add player to friendlist',

  'buddy_status' => 'Status',
  'buddy_status_active' => 'It is your mutuall friend',
  'buddy_status_incoming_waiting' => 'Incoming friending request',
  'buddy_status_incoming_denied' => 'You deny friending request',
  'buddy_status_outcoming_waiting' => 'You request sent. Wait for answer',
  'buddy_status_outcoming_denied' => 'You request denied',

  // Result messages
  'buddy_err_not_exist' => 'Request does not exists. Perhaps it was deleted or denied',

  'buddy_err_accept_own' => 'You can not accept own request',
  'buddy_err_accept_alien' => 'You can not accept request which wasn\'t sent to you',
  'buddy_err_accept_already' => 'You already accepted this request and already in friends with this player',
  'buddy_err_accept_denied' => 'You already denied this request and can not accept it',
  'buddy_err_accept_internal' => 'There is error while accepting request. Try again later. If error stil persists - please contact server administration',
  'buddy_err_accept_none' => 'Friendship request granted',

  'buddy_err_delete_alien' => 'This request was made not by you and not for you! Do not interfere in other people relations! Find you own friends!',
  'buddy_err_unfriend_none' => 'You broke friendship',
  'buddy_err_delete_own' => 'You request was deleted',

  'buddy_err_deny_none' => 'You deny friendship from player. Why, o why?!',

  'buddy_err_adding_exists' => 'You can not then request to this player - you already friends or there is exists some friendship request between yours',
  'buddy_err_adding_none' => 'You friendship request was sent',
  'buddy_err_adding_self' => 'You can not send friendship request to yourself',

  // PM messages
  'buddy_msg_accept_title' => 'You have new friend!',
  'buddy_msg_accept_text' => 'Player %s added you to friendlist!',
  'buddy_msg_unfriend_title' => 'You lost friend!',
  'buddy_msg_unfriend_text' => 'Player %s broke friendship and removed you from friendlist. How sad...',
  'buddy_msg_deny_title' => 'Unable to make a new friend',
  'buddy_msg_deny_text' => 'Player %s denied your friendship offer',
  'buddy_msg_adding_title' => 'Friendship offer',
  'buddy_msg_adding_text' => 'Player %s offers you his friendship',

  'buddy_hint' => '
    <li>Send offer friendship through a menu item <a href="search.php">Search</a>.</li>
    <li>You can see the status of your friends online or offline. However, and your friends can see your status. Consider this fact before to accept a request on friendship.</li>
    <li>If you denied friendship request you can not start any friendship relation with this player until he deletes his offer</li>',

));
