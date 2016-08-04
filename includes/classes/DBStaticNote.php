<?php

class DBStaticNote {

  public static function db_note_list_delete($where) {
    classSupernova::$gc->db->doDeleteDeprecated(TABLE_NOTES, $where);
  }

  public static function db_note_get_id_and_owner($note_id_edit) {
    return classSupernova::$db->doSelectFetch("SELECT `id`, `owner` FROM {{notes}} WHERE `id` = {$note_id_edit} LIMIT 1 FOR UPDATE");
  }

  /**
   * @param      $user_id
   * @param bool $sticky
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_note_list_by_owner($user_id, $sticky = false) {
    $sticky = $sticky ? ' AND `sticky` = 1' : '';
    $extra_sort = $sticky ? ' `galaxy` ASC, `system` ASC, `planet` ASC, `planet_type` ASC,' : '';
    $notes_query = classSupernova::$db->doSelect("SELECT * FROM `{{notes}}` WHERE `owner` = {$user_id} {$sticky} ORDER BY `priority` DESC, {$extra_sort} `time` DESC");

    return $notes_query;
  }

  /**
   * @param $note_priority
   * @param $note_title
   * @param $note_text
   * @param $note_galaxy
   * @param $note_system
   * @param $note_planet
   * @param $note_planet_type
   * @param $note_sticky
   * @param $note_id_edit
   */
  public static function db_note_update_by_id($note_priority, $note_title, $note_text, $note_galaxy, $note_system, $note_planet, $note_planet_type, $note_sticky, $note_id_edit) {
    classSupernova::$db->doUpdate("UPDATE {{notes}} SET `time` = " . SN_TIME_NOW . ", `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}',
        `galaxy` = {$note_galaxy}, `system` = {$note_system}, `planet` = {$note_planet}, `planet_type` = {$note_planet_type}, `sticky` = {$note_sticky}
        WHERE `id` = {$note_id_edit} LIMIT 1;");
  }

  /**
   * @param $userId
   * @param $note_priority
   * @param $note_title_unsafe
   * @param $note_text_unsafe
   * @param $note_galaxy
   * @param $note_system
   * @param $note_planet
   * @param $note_planet_type
   * @param $note_sticky
   */
  public static function db_note_insert(
    $userId, $note_priority, $note_title_unsafe, $note_text_unsafe, $note_galaxy, $note_system, $note_planet, $note_planet_type, $note_sticky) {
    classSupernova::$db->doInsertSet(TABLE_NOTES, array(
      'owner'       => $userId,
      'time'        => SN_TIME_NOW,
      'priority'    => $note_priority,
      'title'       => $note_title_unsafe,
      'text'        => $note_text_unsafe,
      'galaxy'      => $note_galaxy,
      'system'      => $note_system,
      'planet'      => $note_planet,
      'planet_type' => $note_planet_type,
      'sticky'      => $note_sticky,
    ));
  }


  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_note_list_select_by_owner_and_planet($user) {
    $query = classSupernova::$db->doSelect("SELECT * FROM {{notes}} WHERE `owner` = {$user['id']} AND `galaxy` <> 0 AND `system` <> 0 AND `planet` <> 0 ORDER BY `priority` DESC, `galaxy`, `system`, `planet`, `planet_type`;");

    return $query;
  }

  /**
   * @param array $user
   * @param int   $note_id_edit
   *
   * @throws Exception
   */
  public static function processDelete($user, $note_id_edit) {
    $not = '';
    $query_where = '';
    switch (sys_get_param_str('note_delete_range')) {
      case 'all':
      break;

      case 'marked_not':
        $not = 'NOT';
      case 'marked':
        if (!is_array($notes_marked = sys_get_param('note'))) {
          throw new Exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = array();
        foreach ($notes_marked as $note_id => $note_select) {
          if ($note_select == 'on' && $note_id = idval($note_id)) {
            $notes_marked_filtered[] = $note_id;
          }
        }

        if (empty($notes_marked_filtered)) {
          throw new Exception('note_err_none_selected', ERR_WARNING);
        }

        $notes_marked_filtered = implode(',', $notes_marked_filtered);
        $query_where = "AND `id` {$not} IN ({$notes_marked_filtered})";
      break;

      default:
        throw new Exception('note_warn_no_range', ERR_WARNING);
      break;
    }

    sn_db_transaction_start();
    $where['owner'] = $user['id'];
    $where[] = $query_where;
    DBStaticNote::db_note_list_delete($where);
    sn_db_transaction_commit();

    throw new Exception($note_id_edit ? 'note_err_none_changed' : 'note_err_none_added', ERR_NONE);
  }

}
