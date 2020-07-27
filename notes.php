<?php

/**
 * notes.php
 *
 * Changelog:
 *   2.0 copyright © 2009-2012 Gorlum for http://supernova.ws
 *     [!] Wrote from scratch
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('notes');

$template = SnTemplate::gettemplate('notes', true);

$result = array();
if(($result_message = sys_get_param_str('MESSAGE')) && isset($lang[$result_message])) {
  $result[] = array('STATUS' => sys_get_param_int('STATUS'), 'MESSAGE' => $lang[$result_message]);
}

$note_id_edit = sys_get_param_id('note_id_edit');
if(sys_get_param('note_delete')) {
  try {
    $not = '';
    $query_where = '';
    switch(sys_get_param_str('note_delete_range')) {
      case 'all':
      break;

      case 'marked_not':
        $not = 'NOT';
      case 'marked':
        if(!is_array($notes_marked = sys_get_param('note'))) {
          throw new exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = array();
        foreach($notes_marked as $note_id => $note_select) {
          if($note_select == 'on' && $note_id = idval($note_id)) {
            $notes_marked_filtered[] = $note_id;
          }
        }

        if(empty($notes_marked_filtered)) {
          throw new exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = implode(',', $notes_marked_filtered);
        $query_where = "AND `id` {$not} IN ({$notes_marked_filtered})";
      break;

      default:
        throw new exception('note_warn_no_range', ERR_WARNING);
      break;
    }

    sn_db_transaction_start();
    doquery("DELETE FROM {{notes}} WHERE `owner` = {$user['id']} {$query_where};");
    sn_db_transaction_commit();
    throw new exception($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added', ERR_NONE);
  } catch(exception $e) {
    $note_id_edit = 0;
    sn_db_transaction_rollback();
    $result[] = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $lang[$e->getMessage()],
    );
  }
} elseif(($note_title = sys_get_param_str('note_title')) || ($note_text = sys_get_param_str('note_text'))) {
  $note_title == db_escape($lang['note_new_title']) ? $note_title = '' : false;
  ($note_text = sys_get_param_str('note_text')) == db_escape($lang['note_new_text']) ? $note_text = '' : false;

  try {
    $note_galaxy = max(0, min(sys_get_param_id('note_galaxy'), SN::$config->game_maxGalaxy));
    $note_system = max(0, min(sys_get_param_id('note_system'), SN::$config->game_maxSystem));
    $note_planet = max(0, min(sys_get_param_id('note_planet'), SN::$config->game_maxPlanet + 1));

    if(!$note_text && !$note_title && !$note_galaxy && !$note_system && !$note_planet) {
      throw new exception('note_err_note_empty', ERR_WARNING);
    }

    $note_priority = min(sys_get_param_id('note_priority', 2), count($note_priority_classes) - 1);
    $note_planet_type = max(1, min(sys_get_param_id('note_planet_type', 1), count($lang['sys_planet_type'])));
    $note_sticky = intval(sys_get_param_id('note_sticky')) ? 1 : 0;

    sn_db_transaction_start();
    if($note_id_edit) {
      $check_note_id = doquery("SELECT `id`, `owner` FROM {{notes}} WHERE `id` = {$note_id_edit} LIMIT 1 FOR UPDATE", true);
      if(!$check_note_id) {
        throw new exception('note_err_note_not_found', ERR_ERROR);
      }
    }

    if($note_id_edit) {
      if($check_note_id['owner'] != $user['id']) {
        throw new exception('note_err_owner_wrong', ERR_ERROR);
      }

      doquery("UPDATE {{notes}} SET `time` = " . SN_TIME_NOW . ", `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}',
        `galaxy` = {$note_galaxy}, `system` = {$note_system}, `planet` = {$note_planet}, `planet_type` = {$note_planet_type}, `sticky` = {$note_sticky}
        WHERE `id` = {$note_id_edit} LIMIT 1;");
    } else {
      doquery("INSERT INTO {{notes}} SET `owner` = {$user['id']}, `time` = " . SN_TIME_NOW . ", `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}',
        `galaxy` = {$note_galaxy}, `system` = {$note_system}, `planet` = {$note_planet}, `planet_type` = {$note_planet_type}, `sticky` = {$note_sticky};");
    }

    sn_db_transaction_commit();
    sys_redirect('notes.php?STATUS=' . ERR_NONE . '&MESSAGE=' . ($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added'));
//    throw new exception($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added', ERR_NONE);
  } catch(exception $e) {
    $note_id_edit = 0;
    sn_db_transaction_rollback();
    $result[] = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $lang[$e->getMessage()],
    );
  }
}

if(!$note_id_edit) {
  \Note\Note::note_assign($template, array(
    'id' => 0,
    'time' => SN_TIME_NOW,
    'priority' => 2,
    'planet_type' => PT_PLANET,
    'title' => $lang['note_new_title'],
    'text' => $lang['note_new_text'],
  ));
}

$note_exist = false;
$notes_query = doquery("SELECT * FROM {{notes}} WHERE owner={$user['id']} ORDER BY priority DESC, galaxy ASC, system ASC, planet ASC, planet_type ASC, `time` DESC");
while($note_row = db_fetch($notes_query)) {
  \Note\Note::note_assign($template, $note_row);
  $note_exist = $note_exist || $note_row['id'] == $note_id_edit;
}
$note_id_edit = $note_exist ? $note_id_edit : 0;

foreach($note_priority_classes as $note_priority_id => $note_priority_class) {
  $template->assign_block_vars('note_priority', array(
    'ID' => $note_priority_id,
    'CLASS' => $note_priority_classes[$note_priority_id],
    'TEXT' => $lang['sys_notes_priorities'][$note_priority_id],
  ));
}

foreach($lang['sys_planet_type'] as $planet_type_id => $planet_type_string) {
  $template->assign_block_vars('planet_type', array(
    'ID' => $planet_type_id,
    'TEXT' => $planet_type_string,
  ));
}

foreach($result as $result_data) {
  $template->assign_block_vars('result', $result_data);
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['note_page_header'],
  'NOTE_ID_EDIT' => $note_id_edit,
  'NOTE_FULL_RENDER' => true,
));

SnTemplate::display($template);
