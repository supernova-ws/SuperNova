<?php

namespace DBStatic;

use classSupernova;
use DbResultIterator;
use mysqli_result;

/**
 * Class DBStatic\DBStaticUser
 */
class DBStaticUser {

  /**
   * @param mixed $user
   */
  // TODO - remove!
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
   * @param array $playerArray
   *
   * @return string
   */
  // TODO - remove or use something else
  public static function renderNameAndCoordinates($playerArray) {
    return "{$playerArray['username']} " . uni_render_coordinates($playerArray);
  }

  /**
   * @return DbResultIterator
   */
  protected static function playerSelectIterator($fields, $orderBy = '', $forUpdate = false, $groupHaving = '', $where = '', $limit = '') {
    $query = array(
      "SELECT ",
      $fields,
      " FROM `{{users}}` 
      WHERE `user_as_ally` IS NULL AND `user_bot` = " . USER_BOT_PLAYER,
    );
    if ($where) {
      $query[] = " AND ({$where})";
    }
    if ($groupHaving) {
      $query[] = " {$groupHaving}";
    }
    if ($orderBy) {
      $query[] = " ORDER BY {$orderBy}";
    }
    if ($limit) {
      $query[] = " LIMIT {$limit}";
    }
    if ($forUpdate) {
      $query[] = " FOR UPDATE";
    }
    $result = classSupernova::$db->doSelectIterator(implode('', $query));

    return $result;
  }

  /**
   * @return DbResultIterator
   */
  public static function db_player_list_export_blitz_info() {
    return static::playerSelectIterator('`id`, `username`, `total_rank`, `total_points`, `onlinetime`', '`id` ASC');
  }

  /**
   * @return DbResultIterator
   */
  public static function db_user_list_non_bots() {
    return static::playerSelectIterator('`id`', '', true);
  }

  /**
   * @return DbResultIterator
   */
  public static function db_user_list_admin_multiaccounts() {
    return static::playerSelectIterator(
      'COUNT(`id`) AS `ip_count`, 
      `user_lastip`',
      'COUNT(`id`) DESC',
      false,
      'GROUP BY `user_lastip` HAVING COUNT(`id`) > 1',
      '`user_lastip` IS NOT NULL'
    );
  }

  // TODO - это вообще-то надо хранить в конфигурации
  /**
   * @return string
   */
  public static function getLastRegisteredUserName() {
    $iterator = static::playerSelectIterator('`username`', '`id` DESC', false, '', '', '1');

    return classSupernova::$db->getDbIteratorFirstValue($iterator);
  }

  /**
   * @param bool $online
   *
   * @return int
   */
  public static function db_user_count($online = false) {
    $iterator = static::playerSelectIterator('COUNT(`id`)', '', false, '', ($online ? '`onlinetime` > ' . (SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : ''));

    return intval(classSupernova::$db->getDbIteratorFirstValue($iterator));
  }

  public static function db_user_list_admin_sorted($sort, $online = false) {
    $query = "SELECT
          u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm
      FROM
          {{users}} as u
          LEFT JOIN
              {{referrals}} as r on r.id_partner = u.id
      WHERE " .
      ($online ? "`onlinetime` >= " . intval(SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : 'user_as_ally IS NULL') .
      " GROUP BY u.id
      ORDER BY user_as_ally, {$sort} ASC";
    $result = classSupernova::$db->doSelectIterator($query);

    return $result;
  }

  public static function db_user_list_to_celebrate($config_user_birthday_range) {
    $query = "SELECT
        `id`, `username`, `user_birthday`, `user_birthday_celebrated`,
        CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`,
        DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
      FROM
        `{{users}}`
      WHERE
        `user_as_ally` IS NULL
        AND `user_bot` = " . USER_BOT_PLAYER . "
        AND `user_birthday` IS NOT NULL
        AND (`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)
      HAVING
        `days_after_birthday` >= 0 AND `days_after_birthday` < {$config_user_birthday_range} 
      FOR UPDATE";

    $result = classSupernova::$db->doSelectIterator($query);

    return $result;
  }


  public static function lockAllRecords() {
    classSupernova::$db->doSelect("SELECT 1 FROM `{{users}}` FOR UPDATE");
  }

  public static function db_user_lock_with_target_owner_and_acs($user, $planet = array()) {
    $query =
      "SELECT 1 
      FROM `{{users}}` 
      WHERE `id` = " . idval($user['id']) .
      (!empty($planet['id_owner']) ? ' OR `id` = ' . idval($planet['id_owner']) : '') .
      " FOR UPDATE";

    classSupernova::$db->doSelect($query);
  }


  public static function db_player_list_blitz_delete_players() {
    classSupernova::$db->doDeleteDanger(
      TABLE_USERS,
      array(),
      array(
        "`username` LIKE 'Игрок%'"
      )
    );
  }

  // TODO - NEVER change DM amount directly w/o logging!
  public static function db_player_list_blitz_set_50k_dm() {
    classSupernova::$db->doUpdateTableSet(
      TABLE_USERS,
      array(
        'dark_matter'       => 50000,
        'dark_matter_total' => 50000,
      )
    );

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

  /**
   * @param       $user_id
   * @param array $set
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_user_set_by_id($user_id, $set) {
    return classSupernova::$gc->cacheOperator->db_upd_record_by_id(LOC_USER, $user_id, $set, array());
  }

  /**
   * @param       $user_id
   * @param array $set
   * @param array $adjust
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_user_adjust_by_id($user_id, $adjust) {
    return classSupernova::$gc->cacheOperator->db_upd_record_by_id(LOC_USER, $user_id, array(), $adjust);
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

  /**
   * @param       $ally_id
   * @param       $ally_rank_id
   * @param array $set
   * @param array $adjust
   */
  public static function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set, $adjust) {
    classSupernova::$gc->cacheOperator->db_upd_record_list_DANGER(
      LOC_USER,
      $set,
      $adjust,
      array(
        'ally_id' => $ally_id,
      ),
      array(
        // TODO - DANGER !!!
        "`ally_rank_id` >= {$ally_rank_id}"
      )
    );
  }

  /**
   * @param array $playerRowFieldChanges - array of $resourceId => $amount
   * @param int   $userId
   *
   * // TODO - DEDUPLICATE
   *
   * @see DBStaticPlanet::db_planet_update_resources
   */
  public static function db_user_update_resources($playerRowFieldChanges, $userId) {
    $fields = array();
    foreach ($playerRowFieldChanges as $resourceId => $value) {
      $fields[pname_resource_name($resourceId)] = $value;
    }
    if (!empty($fields)) {
      classSupernova::$gc->db->doUpdateRowAdjust(
        TABLE_USERS,
        array(),
        $fields,
        array(
          'id' => $userId
        )
      );
    }
  }

}
