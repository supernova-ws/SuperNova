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

$note_priority_classes = array(
  '0' => '',
  '1' => 'ok',
  '2' => 'notice',
  '3' => 'warning',
  '4' => 'error',
);

$template = gettemplate('notes', true);

// owner time priority title text
$result = array();
if(($result_message = sys_get_param_str('MESSAGE')) && isset($lang[$result_message]))
{
  $result[] = array('STATUS' => sys_get_param_int('STATUS'), 'MESSAGE' => $lang[$result_message]);
}

$note_id_edit = sys_get_param_id('note_id_edit');
if(sys_get_param('note_delete'))
{
  try
  {
    $not = '';
    $query_where = '';
    switch(sys_get_param_str('note_delete_range'))
    {
      case 'all':
      break;

      case 'marked_not':
        $not = 'NOT';
      case 'marked':
        if(!is_array($notes_marked = sys_get_param('note')))
        {
          throw new exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = array();
        foreach($notes_marked as $note_id => $note_select)
        {
          if($note_select == 'on' && $note_id = idval($note_id))
          {
            $notes_marked_filtered[] = $note_id;
          }
        }

        if(empty($notes_marked_filtered))
        {
          throw new exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = implode(',', $notes_marked_filtered);
        $query_where = "AND `id` {$not} IN ({$notes_marked_filtered})";
      break;

      default:
        throw new exception('note_warn_no_range', ERR_WARNING);
      break;
    }

    doquery('START TRANSACTION');
    doquery("DELETE FROM {{notes}} WHERE `owner` = {$user['id']} {$query_where};");
    doquery('COMMIT');
    throw new exception($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added', ERR_NONE);
  }
  catch(exception $e)
  {
    $note_id_edit = 0;
    doquery('ROLLBACK');
    $result[] = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $lang[$e->getMessage()],
    );
  }
}
elseif($note_text = sys_get_param_str('note_text'))
{
  $note_title = sys_get_param_str('note_title', mysql_real_escape_string($lang['note_new_title']));
  try
  {
    if($note_text == mysql_real_escape_string($lang['note_new_text']) && $note_title == mysql_real_escape_string($lang['note_new_title']))
    {
      throw new exception('note_err_note_empty', ERR_WARNING);
    }

    $note_priority = min(sys_get_param_id('note_priority', 2), count($note_priority_classes) - 1);

    doquery('START TRANSACTION');
    if($note_id_edit)
    {
      $check_note_id = doquery("SELECT `id`, `owner` FROM {{notes}} WHERE `id` = {$note_id_edit} LIMIT 1 FOR UPDATE", true);
      if(!$check_note_id)
      {
        throw new exception('note_err_note_not_found', ERR_ERROR);
      }
    }

    if($note_id_edit)
    {
      if($check_note_id['owner'] != $user['id'])
      {
        throw new exception('note_err_owner_wrong', ERR_ERROR);
      }

      doquery("UPDATE {{notes}} SET `time` = {$time_now}, `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}' WHERE `id` = {$note_id_edit} LIMIT 1;");
    }
    else
    {
      doquery("INSERT INTO {{notes}} SET `owner` = {$user['id']}, `time` = {$time_now}, `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}';");
    }

    doquery('COMMIT');
    sys_redirect('notes.php?STATUS=' . ERR_NONE . '&MESSAGE=' . ($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added'));
//    throw new exception($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added', ERR_NONE);
  }
  catch(exception $e)
  {
    $note_id_edit = 0;
    doquery('ROLLBACK');
    $result[] = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $lang[$e->getMessage()],
    );
  }
}

function note_assign($note_row)
{
  global $template, $note_priority_classes, $lang;

  $template->assign_block_vars('note', array(
    'ID' => $note_row['id'],
    'TIME' => $note_row['time'],
    'TIME_TEXT' => date(FMT_DATE_TIME, $note_row['time']),
    'PRIORITY' => $note_row['priority'],
    'PRIORITY_CLASS' => $note_priority_classes[$note_row['priority']],
    'PRIORITY_TEXT' => $lang['note_priorities'][$note_row['priority']],
    'TITLE' => $note_row['title'],
    'TEXT' => sys_bbcodeParse(htmlentities($note_row['text'], ENT_COMPAT, 'UTF-8')),
    'TEXT_EDIT' => htmlentities($note_row['text'], ENT_COMPAT, 'UTF-8'),
  ));
}

$note_exist = false;
$notes_query = doquery($q = "SELECT * FROM {{notes}} WHERE owner={$user['id']} ORDER BY priority DESC, time DESC");
while($note_row = mysql_fetch_assoc($notes_query))
{
  note_assign($note_row);
  $note_exist = $note_exist || $note_row['id'] == $note_id_edit;
}
$note_id_edit = $note_exist ? $note_id_edit : 0;
if(!$note_id_edit)
{
  note_assign(array(
    'id' => 0,
    'time' => $time_now,
    'priority' => 2,
    'title' => $lang['note_new_title'],
    'text' => $lang['note_new_text'],
  ));
}

foreach($note_priority_classes as $note_priority_id => $note_priority_class)
{
  $template->assign_block_vars('note_priority', array(
    'ID' => $note_priority_id,
    'CLASS' => $note_priority_classes[$note_priority_id],
    'TEXT' => $lang['note_priorities'][$note_priority_id],
  ));
}

foreach($result as $result_data)
{
  $template->assign_block_vars('result', $result_data);
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['note_page_header'],
  'NOTE_ID_EDIT' => $note_id_edit,
));

display($template);

?>
