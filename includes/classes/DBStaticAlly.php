<?php

class DBStaticAlly {

// ALLY *************************************************************************************************************
  public static function db_ally_list_recalc_counts() {
    classSupernova::$db->doUpdateSqlNoParam(
      "UPDATE `{{alliance}}` AS a 
        LEFT JOIN (
          SELECT `ally_id`, count(*) AS ally_memeber_count 
          FROM `{{users}}`
          WHERE `ally_id` IS NOT NULL 
          GROUP BY ally_id
        ) AS u ON u.`ally_id` = a.`id` 
      SET 
        a.`ally_members` = u.ally_memeber_count;"
    );
  }

  public static function db_ally_request_list($ally_id) {
    return classSupernova::$db->doSelect("SELECT {{alliance_requests}}.*, {{users}}.username FROM {{alliance_requests}} LEFT JOIN {{users}} ON {{users}}.id = {{alliance_requests}}.id_user WHERE id_ally='{$ally_id}'");
  }

  public static function db_ally_request_get_by_user_id($player_id) {
    return classSupernova::$db->doSelectFetch("SELECT * FROM {{alliance_requests}} WHERE `id_user` ='{$player_id}' LIMIT 1;");
  }

  public static function db_ally_count() {
    $result = classSupernova::$db->doSelectFetch('SELECT COUNT(`id`) AS ally_count FROM `{{alliance}}`');

    return isset($result['ally_count']) ? $result['ally_count'] : 0;
  }

  /**
   * @param $id_ally
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_id($id_ally) {
    $ally = classSupernova::$db->doSelectFetch("SELECT * FROM `{{alliance}}` WHERE `id` ='{$id_ally}' LIMIT 1");

    return $ally;
  }

  /**
   * @param $searchtext
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_list_search($searchtext) {
    $search = classSupernova::$db->doSelect("SELECT ally_name, ally_tag, total_rank, ally_members FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");

    return $search;
  }

  /**
   * @param $ally_tag
   * @param $ally_name
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_name_or_tag($ally_tag, $ally_name) {
    $query = classSupernova::$db->doSelectFetch("SELECT ally_tag FROM {{alliance}} WHERE `ally_tag` = '{$ally_tag}' or `ally_name` = '{$ally_name}' LIMIT 1;");

    return $query;
  }

  /**
   * @param $ally_name_unsafe
   * @param $ally_tag_unsafe
   * @param $userId
   */
  public static function db_ally_insert($ally_name_unsafe, $ally_tag_unsafe, $userId) {
    $ally = classSupernova::$db->doInsertSet(TABLE_ALLIANCE, array(
      'ally_name'          => (string)$ally_name_unsafe,
      'ally_tag'           => (string)$ally_tag_unsafe,
      'ally_owner'         => $userId,
      'ally_owner_range'   => (string)classLocale::$lang['ali_leaderRank'],
      'ally_members'       => (int)1,
      'ranklist'           => (string)(classLocale::$lang['ali_defaultRankName'] . ",0,0,0,0,0"),
      'ally_register_time' => SN_TIME_NOW,
    ));

    return $ally;
  }

  /**
   * @param $ally_user_id
   * @param $ally_id
   */
  public static function db_ally_update_ally_user($ally_user_id, $ally_id) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE,
      array(
        'ally_user_id' => $ally_user_id,
      ),
      array(
        'id' => $ally_id,
      )
    );
  }

  /**
   * @param int    $userId
   * @param int    $id_ally
   * @param string $requestTextUnsafe
   */
  public static function db_ally_request_insert($userId, $id_ally, $requestTextUnsafe) {
    classSupernova::$db->doInsertSet(TABLE_ALLIANCE_REQUESTS, array(
      'id_user'      => $userId,
      'id_ally'      => $id_ally,
      'request_text' => $requestTextUnsafe,
      'request_time' => SN_TIME_NOW,
    ));
  }

  /**
   * @param $userId
   */
  public static function db_ally_request_delete_own($userId, $allyId) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_ALLIANCE_REQUESTS, array('id_user' => $userId, 'id_ally' => $allyId,));
  }


  /**
   * @param $tag
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_tag($tag) {
    $ally = classSupernova::$db->doSelectFetch("SELECT * FROM {{alliance}} WHERE ally_tag='{$tag}' LIMIT 1;");

    return $ally;
  }

  /**
   * @param $ali_search_text
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_search_by_name_or_tag($ali_search_text) {
    $search = classSupernova::$db->doSelect("SELECT DISTINCT * FROM {{alliance}} WHERE `ally_name` LIKE '%{$ali_search_text}%' OR `ally_tag` LIKE '%{$ali_search_text}%' LIMIT 30");

    return $search;
  }

  /**
   * @param $ally
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_request_count_by_id($ally) {
    $request = classSupernova::$db->doSelectFetch("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'");

    return $request;
  }


  /**
   * @param $ally_changeset
   * @param $ally
   */
  public static function db_ally_update_by_changeset($ally_changeset, $ally) {
    classSupernova::$db->doUpdateComplex("UPDATE {{alliance}} SET " . implode(',', $ally_changeset) . " WHERE `id`='{$ally['id']}' LIMIT 1;");
  }

  /**
   * @param $text_unsafe
   * @param $allyId
   * @param $fieldNameUnsafe
   */
  public static function db_ally_update_texts($text_unsafe, $allyId, $fieldNameUnsafe) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE,
      array(
        $fieldNameUnsafe => $text_unsafe,
      ),
      array(
        'id' => $allyId,
      )
    );
  }

  /**
   * @param $idNewLeader
   * @param $userAllyId
   */
  public static function db_ally_update_owner($idNewLeader, $userAllyId) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE,
      array(
        'ally_owner' => $idNewLeader,
      ),
      array(
        'id' => $userAllyId,
      )
    );
  }

  /**
   * @param int $allyId
   */
  public static function db_ally_delete($allyId) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_ALLIANCE, array('id' => $allyId));
  }


  /**
   * @param int $userAllyId
   * @param int $alliance_negotiation_contr_ally_id
   */
  public static function db_ally_negotiation_delete($userAllyId, $alliance_negotiation_contr_ally_id) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_ALLIANCE_NEGOTIATION, array(
      'alliance_negotiation_ally_id'       => $userAllyId,
      'alliance_negotiation_contr_ally_id' => $alliance_negotiation_contr_ally_id,
    ));
  }

  /**
   * @param $offer_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_negotiation_get_by_offer_id($offer_id) {
    $negotiation = classSupernova::$db->doSelectFetch("SELECT * FROM {{alliance_negotiation}} WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;");

    return $negotiation;
  }

  /**
   * @param $offer_id
   */
  public static function db_ally_negotiation_delete_by_offer_id($offer_id) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_ALLIANCE_NEGOTIATION, array('alliance_negotiation_id' => $offer_id));
  }

  /**
   * @param $offer_id
   */
  public static function db_ally_negotiation_deny($offer_id) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE_NEGOTIATION,
      array(
        'alliance_negotiation_status' => ALLY_PROPOSE_DENIED,
      ),
      array(
        'alliance_negotiation_id' => $offer_id,
      )
    );
  }

  /**
   * @param $negotiatorId
   * @param $userAllyId
   */
  public static function db_ally_negotiation_delete_extended($negotiatorId, $userAllyId) {
    classSupernova::$gc->db->doDeleteWhere(TABLE_ALLIANCE_NEGOTIATION, array(
      'alliance_negotiation_id' => $negotiatorId,
      'alliance_negotiation_contr_ally_id' => $userAllyId,
    ));
    classSupernova::$gc->db->doDeleteWhere(TABLE_ALLIANCE_NEGOTIATION, array(
      'alliance_negotiation_id' => $userAllyId,
      'alliance_negotiation_contr_ally_id' => $negotiatorId,
    ));
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_list_get_by_not_user_ally($user) {
    $query = classSupernova::$db->doSelect("SELECT id, ally_name, ally_tag FROM {{alliance}} WHERE `id` != {$user['ally_id']} ORDER BY ally_name;");

    return $query;
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_negotiation_list($user) {
    $query = classSupernova::$db->doSelect(
      "SELECT
        *,
        if(alliance_negotiation_ally_id = {$user['ally_id']}, 1, 0) AS owner,
        if(alliance_negotiation_ally_id = {$user['ally_id']}, alliance_negotiation_contr_ally_name, alliance_negotiation_ally_name) AS ally_name
      FROM
        {{alliance_negotiation}}
      WHERE
        alliance_negotiation_ally_id = {$user['ally_id']} OR alliance_negotiation_contr_ally_id = {$user['ally_id']};"
    );

    return $query;
  }

  /**
   * @param $id
   */
  public static function db_ally_request_deny($id) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE_REQUESTS,
      array(
        'request_denied' => ALLY_REQUEST_DENIED,
        'request_text' => classLocale::$lang['ali_req_deny_reason'],
      ),
      array(
        'id_user' => $id,
      )
    );

  }

  /**
   * @param $i
   * @param $allyId
   */
  public static function db_ally_update_member_set($i, $allyId) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE,
      array(
        'ally_members' => $i,
      ),
      array(
        'id' => $allyId,
      )
    );
  }

  /**
   * @param $id_user
   */
  public static function db_ally_request_delete_all_when_accepted($id_user) {
    classSupernova::$gc->db->doDeleteWhere(TABLE_ALLIANCE_REQUESTS, array('id_user' => $id_user));
  }


  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_members_by_user_as_ally(&$user) {
    $alliance = classSupernova::$db->doSelectFetch("SELECT `ally_members` FROM {{alliance}} WHERE `ally_user_id` = {$user['id']}");

    return $alliance;
  }

  /**
   * @param $ranklist
   * @param $userAllyId
   */
  public static function db_ally_update_ranklist($ranklist, $userAllyId) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ALLIANCE,
      array(
        'ranklist' => $ranklist,
      ),
      array(
        'id' => $userAllyId,
      )
    );
  }

  /**
   * @param $ally_from
   * @param $ally_to
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_diplomacy_get_relations($ally_from, $ally_to) {
    $query = classSupernova::$db->doSelect(
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

    return $query;
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_ally_count(&$user) {
    $lab_level = classSupernova::$db->doSelectFetch("SELECT ally_members AS effective_level FROM {{alliance}} WHERE id = {$user['user_as_ally']} LIMIT 1");

    return $lab_level;
  }

}