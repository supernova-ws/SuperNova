<?php

function ali_rank_list_save($ranks) {
  global $user;

  if (!empty($ranks)) {
    foreach ($ranks as $rank => $rights) {
      $rights = implode(',', $rights);
      $ranklist .= $rights . ';';
    }
  }

  doquery("UPDATE {{alliance}} SET `ranklist` = '{$ranklist}' WHERE `id` ='{$user['ally_id']}';");

  return $ranklist;
}

function ali_relations($ally_from, $ally_to = 0) {
  $ally_to = intval($ally_to);
  $ally_to = $ally_to ? " AND alliance_diplomacy_contr_ally_id = {$ally_to}" : '';

  $temp_array = array();
  $query = doquery(
    "SELECT b.*
      FROM
        {{alliance_diplomacy}} AS b,
        (SELECT alliance_diplomacy_contr_ally_id, MAX(alliance_diplomacy_time) AS alliance_diplomacy_time
          FROM {{alliance_diplomacy}}
          WHERE alliance_diplomacy_ally_id = {$ally_from}  {$ally_to}
          GROUP BY alliance_diplomacy_ally_id, alliance_diplomacy_contr_ally_id
        ) AS m
      WHERE b.alliance_diplomacy_contr_ally_id = m.alliance_diplomacy_contr_ally_id
        AND b.alliance_diplomacy_time = m.alliance_diplomacy_time AND b.alliance_diplomacy_ally_id = {$ally_from}
      ORDER BY alliance_diplomacy_time, alliance_diplomacy_id;"
  );

  while ($record = db_fetch($query)) {
    $temp_array[$record['alliance_diplomacy_contr_ally_id']] = $record;
  }

  return $temp_array;
}

function ali_relation($ally_from, $ally_to) {
  $relation = ali_relations($ally_from, $ally_to);

  return empty($relation) ? ALLY_DIPLOMACY_NEUTRAL : $relation[$ally_to]['alliance_diplomacy_relation'];
}
