<?php

namespace Alliance;

use classLocale;
use mysqli_result;

class DBStaticAlly {

// ALLY *************************************************************************************************************
  public static function db_ally_list_recalc_counts() {
    doquery("UPDATE `{{alliance}}` AS a LEFT JOIN (SELECT ally_id, count(*) AS ally_memeber_count FROM `{{users}}`
      WHERE ally_id IS NOT NULL GROUP BY ally_id) AS u ON u.ally_id = a.id SET a.`ally_members` = u.ally_memeber_count;");
  }

  public static function db_ally_request_list($ally_id) {
    return doquery("SELECT {{alliance_requests}}.*, {{users}}.username FROM {{alliance_requests}} LEFT JOIN {{users}} ON {{users}}.id = {{alliance_requests}}.id_user WHERE id_ally='{$ally_id}'");
  }

  public static function db_ally_request_get_by_user_id($player_id) {
    return doquery("SELECT * FROM {{alliance_requests}} WHERE `id_user` ='{$player_id}' LIMIT 1;", true);
  }

  public static function db_ally_count() {
    $result = doquery('SELECT COUNT(`id`) AS ally_count FROM `{{alliance}}`', true);

    return isset($result['ally_count']) ? $result['ally_count'] : 0;
  }

  /**
   * @param $id_ally
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_id($id_ally) {
    $ally = doquery("SELECT * FROM `{{alliance}}` WHERE `id` ='{$id_ally}' LIMIT 1", true);

    return $ally;
  }

  /**
   * @param $searchtext
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_list_search($searchtext) {
    $search = doquery("SELECT ally_name, ally_tag, total_rank, ally_members FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");

    return $search;
  }

  /**
   * @param $ally_tag
   * @param $ally_name
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_name_or_tag($ally_tag, $ally_name) {
    $query = doquery("SELECT ally_tag FROM {{alliance}} WHERE `ally_tag` = '{$ally_tag}' or `ally_name` = '{$ally_name}' LIMIT 1;", true);

    return $query;
  }

  /**
   * @param $ally_name
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_name($ally_name) {
    $query = doquery("SELECT ally_name FROM {{alliance}} WHERE `ally_name` = '{$ally_name}' LIMIT 1;", true);

    return $query;
  }

  /**
   * @param $ally_name
   * @param $ally_tag
   * @param $user
   */
  public static function db_ally_insert($ally_name, $ally_tag, $user) {
    $ally = doquery("INSERT INTO {{alliance}} SET
    `ally_name` = '{$ally_name}',
    `ally_tag` = '{$ally_tag}',
    `ally_owner` = '{$user['id']}',
    `ally_owner_range` = '" . classLocale::$lang['ali_leaderRank'] . "',
    `ally_members` = 1,
    `ranklist` = '" . classLocale::$lang['ali_defaultRankName'] . ",0,0,0,0,0',
    `ally_register_time`= " . SN_TIME_NOW
    );

    return $ally;
  }

  /**
   * @param $ally_user_id
   * @param $ally_id
   */
  public static function db_ally_update_ally_user($ally_user_id, $ally_id) {
    doquery("UPDATE {{alliance}} SET ally_user_id = {$ally_user_id} WHERE id = {$ally_id} LIMIT 1;");
  }

  /**
   * @param $user
   * @param $id_ally
   * @param $POST_text
   */
  public static function db_ally_request_insert($user, $id_ally, $POST_text) {
    doquery("INSERT INTO {{alliance_requests}} SET `id_user` = {$user['id']}, `id_ally`='{$id_ally}', request_text ='{$POST_text}', request_time=" . SN_TIME_NOW . ";");
  }

  /**
   * @param $user
   */
  public static function db_ally_request_delete_by_user($user) {
    doquery("DELETE FROM {{alliance_requests}} WHERE `id_user` = {$user['id']};");
  }


  /**
   * @param $tag
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_by_tag($tag) {
    $ally = doquery("SELECT * FROM {{alliance}} WHERE ally_tag='{$tag}' LIMIT 1;", '', true);

    return $ally;
  }

  /**
   * @param $ali_search_text
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_search_by_name_or_tag($ali_search_text) {
    $search = doquery("SELECT DISTINCT * FROM {{alliance}} WHERE `ally_name` LIKE '%{$ali_search_text}%' OR `ally_tag` LIKE '%{$ali_search_text}%' LIMIT 30");

    return $search;
  }

  /**
   * @param $ally
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_request_count_by_id($ally) {
    $request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", true);

    return $request;
  }


  /**
   * @param $ally_changeset
   * @param $ally
   */
  public static function db_ally_update_by_changeset($ally_changeset, $ally) {
    doquery("UPDATE {{alliance}} SET " . implode(',', $ally_changeset) . " WHERE `id`='{$ally['id']}' LIMIT 1;");
  }

  /**
   * @param $text_list
   * @param $allyTextID
   * @param $text
   * @param $ally
   */
  public static function db_ally_update_texts($text_list, $allyTextID, $text, $ally) {
    doquery("UPDATE {{alliance}} SET `{$text_list[$allyTextID]['db_field']}`='{$text['safe']}' WHERE `id`='{$ally['id']}';");
  }

  /**
   * @param $idNewLeader
   * @param $user
   */
  public static function db_ally_update_owner($idNewLeader, $user) {
    doquery("UPDATE {{alliance}} SET `ally_owner`='{$idNewLeader}' WHERE `id`={$user['ally_id']};");
  }

  /**
   * @param $ally
   */
  public static function db_ally_delete($ally) {
    doquery("DELETE FROM {{alliance}} WHERE id='{$ally['id']}';");
  }


  /**
   * @param $user
   * @param $alliance_negotiation_contr_ally_id
   */
  public static function db_ally_negotiation_delete($user, $alliance_negotiation_contr_ally_id) {
    doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id = {$user['ally_id']} AND alliance_negotiation_contr_ally_id = {$alliance_negotiation_contr_ally_id} LIMIT 1;");
  }

  /**
   * @param $offer_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_negotiation_get_by_offer_id($offer_id) {
    $negotiation = doquery("SELECT * FROM {{alliance_negotiation}} WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;", '', true);

    return $negotiation;
  }

  /**
   * @param $offer_id
   */
  public static function db_ally_negotiation_delete_by_offer_id($offer_id) {
    doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;");
  }

  /**
   * @param $offer_id
   */
  public static function db_ally_negotiation_update_status_1($offer_id) {
    doquery("UPDATE {{alliance_negotiation}} SET alliance_negotiation_status = 1 WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;");
  }

  /**
   * @param $negotiation
   * @param $user
   */
  public static function db_ally_negotiatiion_delete_extended($negotiation, $user) {
    doquery(
      "DELETE FROM {{alliance_negotiation}}
  	 WHERE
        (alliance_negotiation_ally_id = {$negotiation['alliance_negotiation_ally_id']} AND alliance_negotiation_contr_ally_id = {$user['ally_id']})
        OR
        (alliance_negotiation_ally_id = {$user['ally_id']} AND alliance_negotiation_contr_ally_id = {$negotiation['alliance_negotiation_ally_id']});"
    );
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_list_get_by_not_user_ally($user) {
    $query = doquery("SELECT id, ally_name, ally_tag FROM {{alliance}} WHERE `id` != {$user['ally_id']} ORDER BY ally_name;");

    return $query;
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_negotiation_list($user) {
    $query = doquery(
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
   * @param $d
   */
  public static function db_ally_request_deny($d) {
    doquery("UPDATE {{alliance_requests}} SET `request_denied` = 1, `request_text` = '" . classLocale::$lang['ali_req_deny_reason'] . "' WHERE `id_user`= {$d} LIMIT 1;");
  }

  /**
   * @param $ally
   */
  public static function db_ally_update_member_increase($ally) {
    doquery("UPDATE {{alliance}} SET `ally_members`= `ally_members` + 1 WHERE `id`='{$ally['id']}'");
  }

  /**
   * @param $ally
   */
  public static function db_ally_update_member_decrease($ally) {
    doquery("UPDATE {{alliance}} SET `ally_members`= `ally_members` - 1 WHERE `id`='{$ally['id']}' LIMIT 1;");
  }

  /**
   * @param $i
   * @param $ally
   */
  public static function db_ally_update_member_set($i, $ally) {
    doquery("UPDATE {{alliance}} SET `ally_members`='{$i}' WHERE `id`='{$ally['id']}'");
  }

  /**
   * @param $id_user
   */
  public static function db_ally_request_delete_by_user_id($id_user) {
    doquery("DELETE FROM {{alliance_requests}} WHERE `id_user`= '{$id_user}' LIMIT 1;");
  }


  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_members_by_user_as_ally(&$user) {
    $alliance = doquery("SELECT `ally_members` FROM {{alliance}} WHERE `ally_user_id` = {$user['id']}", true);

    return $alliance;
  }

  /**
   * @param $ranklist
   * @param $user
   */
  public static function db_ally_update_ranklist($ranklist, $user) {
    doquery("UPDATE {{alliance}} SET `ranklist` = '{$ranklist}' WHERE `id` ='{$user['ally_id']}';");
  }

  /**
   * @param $ally_from
   * @param $ally_to
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_diplomacy_get_relations($ally_from, $ally_to) {
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

    return $query;
  }

  /**
   * @param $user
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_ally_get_ally_count(&$user) {
    $lab_level = doquery("SELECT ally_members AS effective_level FROM {{alliance}} WHERE id = {$user['user_as_ally']} LIMIT 1", true);

    return $lab_level;
  }

}