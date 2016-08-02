<?php

/**
 * Class DBStaticUser
 */
class DBStaticUser extends DBStaticRecord {

  public static $_table = 'users';
  public static $_idField = 'id';

  protected static function whereNotAlly() {

  }

  // TODO - это вообще-то надо хранить в конфигурации
  /**
   * @return string
   */
  public static function getLastRegisteredUserName() {
    $query =
      static::buildDBQ()
        ->field('username')
        ->where('`user_as_ally` IS NULL')
        ->orderBy(array('`id` DESC'));

    return (string)$query->selectValue();
  }

  /**
   * @return DbResultIterator
   */
  public static function db_player_list_export_blitz_info() {
    return
      static::buildDBQ()
        ->fields(array('id', 'username', 'total_rank', 'total_points', 'onlinetime',))
        ->where('`user_as_ally` IS NULL')
        ->orderBy(array('`id`'))
        ->selectIterator();
  }

  /**
   * @return DbResultIterator
   */
  public static function db_user_list_non_bots() {
//    $query = doquery("SELECT `id` FROM {{users}} WHERE `user_as_ally` IS NULL AND `user_bot` = " . USER_BOT_PLAYER . " FOR UPDATE;");

    $query =
      static::buildDBQ()
        ->field('id')
        ->where("`user_as_ally` IS NULL")
        ->where("`user_bot` = " . USER_BOT_PLAYER)
        ->setForUpdate();

    return $query->selectIterator();
  }

  public static function db_user_lock_with_target_owner_and_acs($user, $planet = array()) {
    $query = "SELECT 1 FROM `{{users}}` WHERE `id` = " . idval($user['id']) .
      (!empty($planet['id_owner']) ? ' OR `id` = ' . idval($planet['id_owner']) : '')
      . " FOR UPDATE";

    static::getDb()->doSelect($query);
  }

  /**
   * @param bool $online
   *
   * @return int
   */
  public static function db_user_count($online = false) {
    return intval(static::getDb()->doSelectFetchValue(
      "SELECT COUNT(`id`) AS `user_count` 
      FROM `{{users}}` 
      WHERE 
        `user_as_ally` IS NULL" .
        ($online ? ' AND `onlinetime` > ' . (SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : '')
    ));
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

    $query = static::buildDBQ()
      ->setAlias('u')
      ->field('u.*')
      ->fieldCount('r.id', 'referral_count')
      ->fieldSingleFunction('sum', 'r.dark_matter', 'referral_dm')
      ->join('LEFT JOIN {{referrals}} as r on r.id_partner = u.id')
      ->where($online ? "`onlinetime` >= " . intval(SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : 'user_as_ally IS NULL')
      ->groupBy('u.id')
      ->orderBy("user_as_ally, {$sort} ASC");

    $result = $query->selectIterator();

    return $result;
  }

  public static function db_user_list_to_celebrate($config_user_birthday_range) {
    $query = static::buildDBQ()
      ->field('id', 'username', 'user_birthday', 'user_birthday_celebrated')
      ->fieldLiteral('CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, \'-%m-%d\')) AS `current_birthday`')
      ->fieldLiteral('DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, \'-%m-%d\'))) AS `days_after_birthday`')
      ->where('`user_birthday` IS NOT NULL')
      ->where('(`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)')
      ->where('`user_as_ally` IS NULL')
      ->having('`days_after_birthday` >= 0')
      ->having('`days_after_birthday` < ' . intval($config_user_birthday_range))
      ->setForUpdate();

    $result = $query->selectIterator();
//
//    $query = "SELECT
//        `id`, `username`, `user_birthday`, `user_birthday_celebrated`,
//        CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`,
//        DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
//      FROM
//        `{{users}}`
//      WHERE
//        `user_birthday` IS NOT NULL
//        AND `user_as_ally` IS NULL
//        AND (`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)
//      HAVING
//        `days_after_birthday` >= 0 AND `days_after_birthday` < {$config_user_birthday_range} FOR UPDATE";
//
//    $result = static::$dbStatic->doQueryIterator($query);

    return $result;
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

    return static::getDb()->doSelectIterator($query);
  }

  public static function db_player_list_blitz_delete_players() {
    classSupernova::$db->doDeleteDeprecated(TABLE_USERS, array("`username` LIKE 'Игрок%'"));
  }

  public static function db_player_list_blitz_set_50k_dm() {
    classSupernova::$db->doUpdate('UPDATE `{{users}}` SET `dark_matter` = 50000, `dark_matter_total` = 50000;');
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
    // TODO Проверить, кстати - а везде ли нужно выбирать юзеров или где-то все-таки ищутся Альянсы ?
    if (!($username_unsafe = trim($username_unsafe))) {
      return false;
    }

    $user = null;
    if (classSupernova::$gc->snCache->isArrayLocation(LOC_USER)) {
      foreach (classSupernova::$gc->snCache->getData(LOC_USER) as $user_id => $user_data) {
        if (is_array($user_data) && isset($user_data['username'])) {
          // проверяем поле
          // TODO Возможно есть смысл всегда искать по strtolower - но может игрок захочет переименоваться с другим регистром? Проверить!
          if ((!$like && $user_data['username'] == $username_unsafe) || ($like && strtolower($user_data['username']) == strtolower($username_unsafe))) {
            // $user_as_ally = intval($user_data['user_as_ally']);
            $user_as_ally = idval($user_data['user_as_ally']);
            if ($player === null || ($player === true && !$user_as_ally) || ($player === false && $user_as_ally)) {
              $user = $user_data;
              break;
            }
          }
        }
      }
    }

    if ($user === null) {
      // Вытаскиваем запись
      $username_safe = db_escape($like ? strtolower($username_unsafe) : $username_unsafe); // тут на самом деле strtolower() лишняя, но пусть будет

      $user = classSupernova::$db->doSelectFetch(
        "SELECT * FROM {{users}} WHERE `username` " . ($like ? 'LIKE' : '=') . " '{$username_safe}'"
        . " FOR UPDATE"
      );
      classSupernova::$gc->snCache->cache_set(LOC_USER, $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }

  public static function db_user_list($user_filter = '', $for_update = false, $fields = '*') {
    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_USER, $user_filter);
  }

  public static function db_user_set_by_id($user_id, $set) {
    return classSupernova::$gc->cacheOperator->db_upd_record_by_id(LOC_USER, $user_id, $set);
  }

  /**
   * Возвращает информацию о пользователе по его ID
   *
   * @param int|array $user_id_unsafe
   *    <p>int - ID пользователя</p>
   *    <p>array - запись пользователя с установленным полем ['id']</p>
   * @param bool      $for_update @deprecated
   * @param string    $fields @deprecated список полей или '*'/'' для всех полей
   * @param null      $player
   * @param bool|null $player Признак выбора записи пользователь типа "игрок"
   *    <p>null - Можно выбрать запись любого типа</p>
   *    <p>true - Выбирается только запись типа "игрок"</p>
   *    <p>false - Выбирается только запись типа "альянс"</p>
   *
   * @return array|false
   *    <p>false - Нет записи с указанным ID и $player</p>
   *    <p>array - запись типа $user</p>
   */
  public static function db_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null) {
    $user = classSupernova::$gc->cacheOperator->db_get_record_by_id(LOC_USER, $user_id_unsafe, $for_update, $fields);

    return (is_array($user) &&
      (
        $player === null
        ||
        ($player === true && !$user['user_as_ally'])
        ||
        ($player === false && $user['user_as_ally'])
      )) ? $user : false;
  }


  public static function db_user_list_set_mass_mail(&$owners_list, $set) {
    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_USER, $set, !empty($owners_list) ? '`id` IN (' . implode(',', $owners_list) . ');' : '');
  }

  public static function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set) {
    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_USER, $set, "`ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}");
  }

  public static function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id) {
    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_USER, "`ally_rank_id` = {$i}", "`ally_id` = {$ally_id} AND `ally_rank_id`={$rank_id}");
  }

  /**
   * @param array $playerArray
   */
  public static function renderNameAndCoordinates($playerArray) {
    return "{$playerArray['username']} " . uni_render_coordinates($playerArray);
  }

  /**
   * @param mixed $user
   */
  public static function validateUserRecord($user) {
    if (!is_array($user)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_unit_changeset_prepare() - USER is not ARRAY</h1>');
      pdump(debug_backtrace());
      die('USER is not ARRAY');
    }
    if (!isset($user['id']) || !$user['id']) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_unit_changeset_prepare() - USER[id] пустой</h1>');
      pdump($user);
      pdump(debug_backtrace());
      die('USER[id] пустой');
    }
  }

  /**
   * @param array $playerRowFieldChanges - array of $resourceId => $amount
   * @param int   $userId
   */
  public static function db_user_update_resources($playerRowFieldChanges, $userId) {
    foreach ($playerRowFieldChanges as $resourceId => &$value) {
      $fieldName = pname_resource_name($resourceId);
      $value = "{$fieldName} = {$fieldName} + ('{$value}')";
    }
    if($query = implode(',', $playerRowFieldChanges)) {
      classSupernova::$gc->db->doUpdate("UPDATE `{{users}}` SET {$query} WHERE id = {$userId}");
    }
  }

}
