<?php

/*
#############################################################################
#  Filename: notes.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009-2012 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 38a8.0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


//$lang = array_merge($lang,
//$lang->merge(
$a_lang_array = (array(
  'note_page_header' => 'Notes',
  'note_date' => 'Date',
  'note_priority' => 'Priority',
  'note_note' => 'Note',
//  'note_title' => 'Title',
//  'note_text' => 'Text',
  'note_priorities' => array(
    0 => 'Low priority',
    1 => 'Below normal',
    2 => 'Normal',
    3 => 'Important',
    4 => 'Very important',
  ),
  'note_new_title' => 'New note title',
  'note_new_text' => 'New note text',

  'note_err_none_added' => 'Note succesfully added',
  'note_err_none_changed' => 'Note succesfully changed',
  'note_err_note_not_found' => 'Note with this ID not found. Possibly it was already deleted',
  'note_err_owner_wrong' => 'You are not owner of this note',
  'note_err_note_empty' => 'You did not write anything in note - it will not be added',

  'note_delete' => 'Delete notes',
  'note_range_select' => '-- SELECT RANGE --',
  'note_range_marked' => 'Marked notes',
  'note_range_marked_not' => 'Not marked notes',
  'note_range_all' => 'All notes',

  'note_warn_no_range' => 'You did not select range - nothing to delete',
  'note_err_none_selected' => 'There are no notes selected - nothing to delete. To delete all notes at once select range "All notes"',

));
