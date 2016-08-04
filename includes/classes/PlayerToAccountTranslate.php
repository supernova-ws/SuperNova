<?php

/**
 * Created by Gorlum 18.09.2015 11:36
 */

/**
 * Class PlayerToAccountTranslate
 */
class PlayerToAccountTranslate {
  /**
   * БД из которой читать данные
   *
   * @var db_mysql $db
   */
  protected static $db = null;
  protected static $is_init = false;

  protected static function init() {
    if(!empty(static::$db)) {
      return;
    }
    static::$db = classSupernova::$db;
  }

  /**
   * Регистрирует игрока на указанный аккаунт указанного провайдера
   *
   * @param $provider_id_safe
   * @param $provider_account_id_safe
   * @param $user_id_safe
   *
   * @return array|resource
   */
  // OK v4.7
  public static function db_translate_register_user($provider_id_unsafe, $provider_account_id_unsafe, $user_id_unsafe) {
    static::init();

    return static::$db->doInsertSet(TABLE_ACCOUNT_TRANSLATE, array(
      'provider_id'         => $provider_id_unsafe,
      'provider_account_id' => $provider_account_id_unsafe,
      'user_id'             => $user_id_unsafe,
    ));
  }

  /**
   * Возвращает из `account_translate` список пользователей, которые прилинкованы к списку аккаунтов на указанном провайдере
   *
   * @param int $provider_id_unsafe Идентификатор провайдера авторизации
   * @param int|int[] $account_list
   *
   * @return array
   */
  public static function db_translate_get_users_from_account_list($provider_id_unsafe, $account_list) {
    static::init();

    $account_translation = array();

    $provider_id_safe = intval($provider_id_unsafe);
    !is_array($account_list) ? $account_list = array($account_list) : false;

    foreach($account_list as $provider_account_id_unsafe) {
      $provider_account_id_safe = intval($provider_account_id_unsafe);

      // TODO - Здесь могут отсутствовать аккаунты - проверять провайдером
      $query = static::$db->doSelect(
        "SELECT `user_id` 
        FROM {{account_translate}} 
        WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$provider_account_id_safe} 
        FOR UPDATE"
      );
      while($row = static::$db->db_fetch($query)) {
        $account_translation[$row['user_id']][$provider_id_unsafe][$provider_account_id_unsafe] = true;
      }
    }

    return $account_translation;
  }

  public static function db_translate_get_account_by_user_id($user_id_unsafe, $provider_id_unsafe = 0) {
    static::init();

    $user_id_safe = static::$db->db_escape($user_id_unsafe);
    $provider_id_safe = static::$db->db_escape($provider_id_unsafe);

    $account_translation = array();

    $query = static::$db->doSelect(
      "SELECT * FROM {{account_translate}} WHERE `user_id` = {$user_id_safe} " .
      ($provider_id_unsafe ? "AND `provider_id` = {$provider_id_safe} " : '') .
      "ORDER BY `timestamp` FOR UPDATE");
    while($row = static::$db->db_fetch($query)) {
      $account_translation[$row['user_id']][$row['provider_id']][$row['provider_account_id']] = $row;
    }

    return $account_translation;
  }

  public static function db_translate_unregister_user($user_id_unsafe) {
    static::init();

    return static::$db->doDeleteWhere(TABLE_ACCOUNT_TRANSLATE, array('user_id' => $user_id_unsafe));
  }

}
