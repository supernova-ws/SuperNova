<?php

use DBStatic\DBStaticAlly;

function ali_rank_list_save($ranks) {
  global $user;

  $ranklist = '';
  if(!empty($ranks)) {
    foreach($ranks as $rank => $rights) {
      $rights = implode(',', $rights);
      $ranklist .= $rights . ';';
    }
  }

  DBStaticAlly::db_ally_update_ranklist($ranklist, $user['ally_id']);

  return $ranklist;
}

function ali_relations($ally_from, $ally_to = 0) {
  $ally_to = intval($ally_to);
  $ally_to = $ally_to ? " AND alliance_diplomacy_contr_ally_id = {$ally_to}" : '';

  $temp_array = array();
  $query = DBStaticAlly::db_ally_diplomacy_get_relations($ally_from, $ally_to);

  while($record = db_fetch($query)) {
    $temp_array[$record['alliance_diplomacy_contr_ally_id']] = $record;
  }

  return $temp_array;
}

function ali_relation($ally_from, $ally_to) {
  $relation = ali_relations($ally_from, $ally_to);

  return empty($relation) ? ALLY_DIPLOMACY_NEUTRAL : $relation[$ally_to]['alliance_diplomacy_relation'];
}
