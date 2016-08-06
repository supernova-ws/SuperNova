<?php

class DBStaticNews {

  public static function db_news_update_set($announce_time, $text_unsafe, $detail_url_unsafe, $announce_id) {
    classSupernova::$db->doUpdateRowSet(
      TABLE_ANNOUNCE,
      array(
        'tsTimeStamp' => date(FMT_DATE_TIME_SQL, $announce_time),
        'strAnnounce' => $text_unsafe,
        'detail_url'  => $detail_url_unsafe,
      ),
      array(
        'idAnnounce' => $announce_id,
      )
    );
  }

  public static function db_news_insert_set($announce_time, $text_unsafe, $detail_url_unsafe, $userId, $userNameUnsafe) {
    classSupernova::$db->doInsertSet(TABLE_ANNOUNCE, array(
      'tsTimeStamp' => date(FMT_DATE_TIME_SQL, $announce_time),
      'strAnnounce' => $text_unsafe,
      'detail_url'  => $detail_url_unsafe,
      'user_id'     => $userId,
      'user_name'   => $userNameUnsafe,
    ));
  }

  public static function db_news_delete_by_id($announce_id) {
    classSupernova::$gc->db->doDeleteRowWhere(TABLE_ANNOUNCE, array('idAnnounce' => $announce_id));
  }

  public static function db_news_with_survey_select_by_id($announce_id) {
    return classSupernova::$db->doSelectFetch(
      "SELECT a.*, s.survey_id, s.survey_question, s.survey_until
        FROM {{announce}} AS a
        LEFT JOIN {{survey}} AS s ON s.survey_announce_id = a.idAnnounce
        WHERE `idAnnounce` = {$announce_id} LIMIT 1;");
  }

  /**
   * @param template $template
   * @param $query_where
   * @param $query_limit
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_news_list_get_by_query(&$template, $query_where, $query_limit) {
    $announce_list = classSupernova::$db->doSelect(
      "SELECT a.*, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time, u.authlevel, s.*
    FROM
      {{announce}} AS a
      LEFT JOIN {{survey}} AS s ON s.survey_announce_id = a.idAnnounce
      LEFT JOIN {{users}} AS u ON u.id = a.user_id
    {$query_where}
    ORDER BY `tsTimeStamp` DESC, idAnnounce" .
      ($query_limit ? " LIMIT {$query_limit}" : ''));

    $template->assign_var('NEWS_COUNT', classSupernova::$db->db_num_rows($announce_list));

    return $announce_list;
  }

}
