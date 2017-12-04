<?php
/**
 * Created by Gorlum 04.12.2017 5:10
 */

namespace Note;

use template;
use HelperString;

class Note {

  /**
   * @param template $template
   * @param array    $note_row
   *
   * @deprecated
   */
  public static function note_assign(&$template, $note_row) {
    global $note_priority_classes, $lang;

    $template->assign_block_vars('note', [
      'ID'                     => $note_row['id'],
      'TIME'                   => $note_row['time'],
      'TIME_TEXT'              => date(FMT_DATE_TIME, $note_row['time']),
      'PRIORITY'               => $note_row['priority'],
      'PRIORITY_CLASS'         => $note_priority_classes[$note_row['priority']],
      'PRIORITY_TEXT'          => $lang['sys_notes_priorities'][$note_row['priority']],
      'TITLE'                  => htmlentities($note_row['title'], ENT_COMPAT, 'UTF-8'),
      'GALAXY'                 => intval($note_row['galaxy']),
      'SYSTEM'                 => intval($note_row['system']),
      'PLANET'                 => intval($note_row['planet']),
      'PLANET_TYPE'            => intval($note_row['planet_type']),
      'PLANET_TYPE_TEXT'       => $lang['sys_planet_type'][$note_row['planet_type']],
      'PLANET_TYPE_TEXT_SHORT' => $lang['sys_planet_type_sh'][$note_row['planet_type']],
      'TEXT'                   => HelperString::htmlEncode($note_row['text'], HTML_ENCODE_MULTILINE),
      'TEXT_EDIT'              => htmlentities($note_row['text'], ENT_COMPAT, 'UTF-8'),
      'STICKY'                 => intval($note_row['sticky']),
    ]);
  }

}
