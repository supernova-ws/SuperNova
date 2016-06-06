<?php

/**
 * Class DBStaticUser
 */
class DBStaticUser extends DBStaticRecord {

  public static $_table = 'users';
  public static $_idField = 'id';


  /**
   * @param int $playerId
   *
   * @return string[]
   */
  public static function getOnlineTime($playerId) {
    $row = static::getRecordById($playerId, array('username', 'onlinetime'));

    return !empty($row) ? $row : array();
  }

  // TODO - это вообще-то надо хранить в конфигурации
  /**
   * @return string
   */
  public static function getLastRegisteredUserName() {
    return (string) static::$dbStatic->selectValue(
      static::buildSelect()
        ->field('username')
        ->where(array('`user_as_ally` IS NULL'))
        ->orderBy(array('`id` DESC'))
        ->fetchOne()
    );
  }

  public static function db_player_list_export_blitz_info() {
    return static::execute(
      static::buildSelect()
        ->fields(array('id', 'username', 'total_rank', 'total_points', 'onlinetime',))
        ->where(array('`user_as_ally` IS NULL'))
        ->orderBy(array('`id`'))
    );
  }

  /**
   * @return array|bool|mysqli_result|null
   */
  public static function db_user_list_non_bots() {
//    $query = doquery("SELECT `id` FROM {{users}} WHERE `user_as_ally` IS NULL AND `user_bot` = " . USER_BOT_PLAYER . " FOR UPDATE;");

    $query = static::execute(
      static::buildSelectAll()
        ->fields('id')
        ->where(array(
          "`user_as_ally` IS NULL AND `user_bot` = " . USER_BOT_PLAYER . " FOR UPDATE;"
        ))
    );

    return $query;
  }

  public static function db_user_lock_with_target_owner_and_acs($user, $planet = array()) {
//    $query = "SELECT 1 FROM `{{users}}` WHERE `id` = :userId" .
//      (!empty($planet['id_owner']) ? ' OR `id` = :planetOwnerId' : '');

    $where = '`id` = :userId';
    if (!empty($planet['id_owner'])) {
      $where .= ' OR `id` = :planetOwnerId';
    }

    $query = static::buildSelectLock()
      ->where($where);

    // TODO - FOR UPDATE
    return static::prepareGetIterator(
      $query,
      array(
        ':userId'        => idval($user['id']),
        ':planetOwnerId' => !empty($planet['id_owner']) ? idval($planet['id_owner']) : 0,
      )
    );
  }

  public static function db_user_count($online = false) {
//    $result = doquery('SELECT COUNT(id) AS user_count FROM {{users}} WHERE user_as_ally IS NULL' . ($online ? ' AND onlinetime > ' . (SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : ''), true);

    $query = static::buildSelectCountId()
      ->where('`user_as_ally` IS NULL');
    if ($online) {
      $query->where('`onlinetime` > :onlineTime');
    }

    return static::$dbStatic->selectValue(DbSqlPrepare::build($query, array(
      ':onlineTime' => SN_TIME_NOW - classSupernova::$config->game_users_online_timeout,
    )));
  }

  public static function db_user_list_admin_sorted($sort, $online = false) {
//    $query = "SELECT
//          u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm
//      FROM
//          {{users}} as u
//          LEFT JOIN
//              {{referrals}} as r on r.id_partner = u.id
//      WHERE " .
//      ($online ? "`onlinetime` >= :onlineTime" : 'user_as_ally IS NULL') .
//      " GROUP BY u.id
//        ORDER BY user_as_ally, {$sort} ASC";

    $query = static::buildSelect()
      ->fromAlias('u')
      ->field('u.*')
      ->fieldCount('r.id', 'referral_count')
      ->fieldSingleFunction('sum', 'r.dark_matter', 'referral_dm')
      ->join('LEFT JOIN {{referrals}} as r on r.id_partner = u.id')
      ->where($online ? "`onlinetime` >= :onlineTime" : 'user_as_ally IS NULL')
      ->groupBy('u.id')
      ->orderBy("user_as_ally, {$sort} ASC");

    return static::prepareGetIterator(
      $query,
      array(
        ':onlineTime' => SN_TIME_NOW - classSupernova::$config->game_users_online_timeout,
      )
    );
  }

  public static function db_user_list_to_celebrate($config_user_birthday_range) {
//    $query = "SELECT
//        `id`, `username`, `user_birthday`, `user_birthday_celebrated`,
//        CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`,
//        DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
//      FROM
//        `{{users}}`
//      WHERE
//        `user_birthday` IS NOT NULL
//        AND (`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)
//        AND `user_as_ally` IS NULL
//      HAVING
//        `days_after_birthday` >= 0 AND `days_after_birthday` < :birthdayRange FOR UPDATE";

    $query = static::buildSelect()
      ->field('id')
      ->field('username')
      ->field('user_birthday')
      ->field('user_birthday_celebrated')
      ->field(DbSqlLiteral::build()->literal('CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, \'-%m-%d\')) AS `current_birthday`'))
      ->field(DbSqlLiteral::build()->literal('DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, \'-%m-%d\'))) AS `days_after_birthday`'))
      ->where('`user_birthday` IS NOT NULL')
      ->where('(`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)')
      ->where('`user_as_ally` IS NULL')
      ->having('`days_after_birthday` >= 0')
      ->having('`days_after_birthday` < :birthdayRange')
      ->forUpdate();

    return static::prepareGetIterator(
      $query,
      array(
        ':birthdayRange' => $config_user_birthday_range,
      )
    );
  }

  /**
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public static function db_user_list_admin_multiaccounts() {
    $query = "SELECT COUNT(*) AS `ip_count`, `user_lastip`
      FROM `{{users}}`
      WHERE `user_as_ally` IS NULL
      GROUP BY `user_lastip`
      HAVING COUNT(*) > 1";

    return static::prepareGetIterator(
      $query
    );
  }

  public static function db_player_list_blitz_delete_players() {
    doquery("DELETE FROM `{{users}}` WHERE username LIKE 'Игрок%';");
  }

  public static function db_player_list_blitz_set_50k_dm() {
    doquery('UPDATE `{{users}}` SET dark_matter = 50000, dark_matter_total = 50000;');
  }


  /**
   * Выбирает записи игроков по списку их ID
   *
   * @param $user_id_list
   *
   * @return array
   */
  public static function db_user_list_by_id($user_id_list) {
    !is_array($user_id_list) ? $user_id_list = array($user_id_list) : false;

    $user_list = array();
    foreach ($user_id_list as $user_id_unsafe) {
      $user = DBStaticUser::db_user_by_id($user_id_unsafe);
      !empty($user) ? $user_list[$user_id_unsafe] = $user : false;
    }

    return $user_list;
  }


  public static function db_user_by_username($username_unsafe, $for_update = false, $fields = '*', $player = null, $like = false) {
    return classSupernova::db_get_user_by_username($username_unsafe, $for_update, $fields, $player, $like);
  }

  public static function db_user_list($user_filter = '', $for_update = false, $fields = '*') {
    return classSupernova::db_get_record_list(LOC_USER, $user_filter);
  }

  public static function db_user_set_by_id($user_id, $set) {
    return classSupernova::db_upd_record_by_id(LOC_USER, $user_id, $set);
  }

  public static function db_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null) {
    return classSupernova::db_get_user_by_id($user_id_unsafe, $for_update, $fields, $player);
  }

  public static function db_user_list_set_mass_mail(&$owners_list, $set) {
    return classSupernova::db_upd_record_list(LOC_USER, !empty($owners_list) ? '`id` IN (' . implode(',', $owners_list) . ');' : '', $set);
  }

  public static function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set) {
    return classSupernova::db_upd_record_list(LOC_USER, "`ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}", $set);
  }

  public static function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id) {
    return classSupernova::db_upd_record_list(LOC_USER, "`ally_id` = {$ally_id} AND `ally_rank_id`={$rank_id}", "`ally_rank_id` = {$i}");
  }

//  public static function db_user_change_active_planet_to_capital($user_id, $captured_planet) {
//    return doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = {$user_id} AND `current_planet` = {$captured_planet};");
//  }

}
