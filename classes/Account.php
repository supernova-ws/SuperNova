<?php

use DBAL\db_mysql;
use \DBAL\DbQuery;

/**
 * User: Gorlum
 * Date: 24.08.2015
 * Time: 6:00
 */
class Account {
  /**
   * @var int
   */
  public $account_id = 0;
  /**
   * @var string
   */
  public $account_name = '';
  /**
   * @var string
   */
  public $account_password = '';
  /**
   * @var string
   */
  public $account_salt = '';
  /**
   * @var string
   */
  public $account_email = '';
  /**
   * @var int
   */
  public $account_email_verified = 0;
  /**
   * @var string
   */
  public $account_register_time = '';
  /**
   * @var string
   */
  public $account_language = '';

  public $account_metamatter = 0;
  public $account_metamatter_total = 0;

  /**
   * @var int
   */
  public $is_exists = 0;
  /**
   * @var int
   */
  public $is_loaded = 0;

  /**
   * @var db_mysql
   */
  public $db;

  protected $sn_root_path = SN_ROOT_RELATIVE;
  protected $cookie_name = SN_COOKIE;
  protected $cookie_name_impersonate = SN_COOKIE_I;

  protected $table_check = array(
    'account' => 'account',
    'log_metamatter' => 'log_metamatter',
  );

  public function reset() {
    $this->account_id = 0;
    $this->account_name = '';
    $this->account_password = '';
    $this->account_salt = '';
    $this->account_email = '';
    $this->account_email_verified = 0;
    $this->account_register_time = '';
    $this->account_language = '';

    $this->is_exists = 0;
    $this->is_loaded = 0;
  }
  public function __construct($db = null) {
    $this->reset();

    $this->db = is_object($db) ? $db : SN::$db;

    $this->sn_root_path = SN_ROOT_RELATIVE;
    $this->cookie_name = SN_COOKIE;
    $this->cookie_name_impersonate = $this->cookie_name . AUTH_COOKIE_IMPERSONATE_SUFFIX;
    $this->secret_word = SN::$sn_secret_word;

    $snTableNames = $this->db->schema()->getSnTables();
    foreach($this->table_check as $table_name) {
      if(empty($snTableNames[$table_name])) {
        die('Если вы видите это сообщение первый раз после обновления релиза - просто перегрузите страницу.<br />
              В противном случае - сообщите Администрации сервера об ошибке.<br/>
              Не хватает таблицы для работы системы авторизации: ' . $table_name);
      }
    }
  }

  // OK 4.5
  public function password_check($password_unsafe) {
    return $this->password_encode($password_unsafe, $this->account_salt) == $this->account_password;
  }

  /**
   * Меняет пароль у аккаунта в БД
   *
   * @param      $old_password_unsafe
   * @param      $new_password_unsafe
   * @param null $salt_unsafe
   *
   * @return bool
   */
  // OK v4.6
  public function password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe = null) {
    if(!$this->password_check($old_password_unsafe)) {
      return false;
    }

    $salt_unsafe === null ? $salt_unsafe = $this->password_salt_generate() : false;
    $result = $this->db_set_password($new_password_unsafe, $salt_unsafe);

    return $result;
  }


  /**
   * Заполняет поля объекта значениями результата запроса
   *
   * @param array $row
   *
   * @return bool
   */
  // OK v4.5
  public function assign_from_db_row($row) {
    $this->reset();
    if(empty($row) || !is_array($row)) {
      return false;
    }
    $this->account_id = $row['account_id'];
    $this->account_name = $row['account_name'];
    $this->account_password = $row['account_password'];
    $this->account_salt = $row['account_salt'];
    $this->account_email = $row['account_email'];
    $this->account_email_verified = $row['account_email_verified'];
    $this->account_register_time = $row['account_register_time'];
    $this->account_language = $row['account_language'];

    $this->account_metamatter = $row['account_metamatter'];
    $this->account_metamatter_total = $row['account_metamatter_total'];

    $this->is_exists = 1;
    $this->is_loaded = 1;

    return true;
  }

  /**
   * Возвращает аккаунт по его ID
   *
   * @param $account_id_unsafe
   *
   * @return bool
   */
  // OK v4.5
  public function db_get_by_id($account_id_unsafe) {
    $this->reset();

    $account_id_safe = round(floatval($account_id_unsafe));

    $account_row = $this->db->doQueryAndFetch("SELECT * FROM `{{account}}` WHERE `account_id` = {$account_id_safe}");
    return $this->assign_from_db_row($account_row);
  }
  /**
   * Возвращает аккаунт по имени
   *
   * @param string $account_name_safe
   *
   * @return bool
   */
  // OK v4.5
  public function db_get_by_name($account_name_unsafe) {
    $this->reset();

    $account_name_safe = $this->db->db_escape($account_name_unsafe);

    $account_row = $this->db->doQueryAndFetch("SELECT * FROM `{{account}}` WHERE LOWER(`account_name`) = LOWER('{$account_name_safe}') FOR UPDATE");
    return $this->assign_from_db_row($account_row);
  }
  /**
   * Возвращает аккаунт по емейлу
   *
   * @param string $email_unsafe
   *
   * @return bool
   */
  // OK v4.5
  public function db_get_by_email($email_unsafe) {
    $this->reset();

    $email_safe = $this->db->db_escape($email_unsafe);
    if($email_safe) {
      $account_row = $this->db->doQueryAndFetch("SELECT * FROM `{{account}}` WHERE LOWER(`account_email`) = LOWER('{$email_safe}') FOR UPDATE;");

      return $this->assign_from_db_row($account_row);
    } else {
      return false;
    }
  }
  /**
   * Возвращает аккаунт по имени или аккаунту - проверка уникальных значений
   *
   * @param string $account_name_unsafe
   * @param string $email_unsafe
   *
   * @return bool
   *
   */
  // OK v4.5
  public function db_get_by_name_or_email($account_name_unsafe, $email_unsafe) {
    $this->reset();

    $account_name_safe = $this->db->db_escape($account_name_unsafe);
    $email_safe = $this->db->db_escape($email_unsafe);

    $account = $this->db->doQueryAndFetch(
      "SELECT * FROM `{{account}}` 
      WHERE 
        LOWER(`account_name`) = LOWER('{$account_name_safe}') 
        OR LOWER(`account_name`) = LOWER('{$email_safe}') 
        OR LOWER(`account_email`) = LOWER('{$email_safe}') 
        FOR UPDATE"
    );
    return $this->assign_from_db_row($account);
  }

  /**
   * @param int|string $player_id_unsafe - player ID
   *
   * @return bool
   */
  public function dbGetByPlayerId($player_id_unsafe) {
    $translation = PlayerToAccountTranslate::db_translate_get_account_by_user_id($player_id_unsafe, core_auth::$main_provider->provider_id);
    if (empty($translation[$player_id_unsafe][core_auth::$main_provider->provider_id])) {
      return false;
    }

    $account_translation = reset($translation[$player_id_unsafe][core_auth::$main_provider->provider_id]);
    if (empty($account_translation['provider_account_id'])) {
      return false;
    }

    return $this->db_get_by_id($account_translation['provider_account_id']);
  }


  /**
   * Создает аккаунт
   *
   * @throws Exception
   */
  // OK v4.5
  public function db_create($account_name_unsafe, $password_raw, $email_unsafe, $language_unsafe = null, $salt_unsafe = null) {
    $this->reset();

    $account_name_safe = $this->db->db_escape($account_name_unsafe);
    $email_safe = $this->db->db_escape($email_unsafe);
    $language_safe = $this->db->db_escape($language_unsafe === null ? DEFAULT_LANG : $language_unsafe);

    $salt_unsafe === null ? $salt_unsafe = $this->password_salt_generate() : false;
    $password_salted_safe = $this->db->db_escape($this->password_encode($password_raw, $salt_unsafe));
    $salt_safe = $this->db->db_escape($salt_unsafe);

    $result = $this->db->doquery(
      "INSERT INTO `{{account}}` SET
        `account_name` = '{$account_name_safe}',
        `account_password` = '{$password_salted_safe}',
        `account_salt` = '{$salt_safe}',
        `account_email` = LOWER('{$email_safe}'),
        `account_language` = '{$language_safe}'"
    );
    if(!$result) {
      throw new Exception(REGISTER_ERROR_ACCOUNT_CREATE, ERR_ERROR);
    }

    if(!($account_id = $this->db->db_insert_id())) {
      throw new Exception(REGISTER_ERROR_ACCOUNT_CREATE, ERR_ERROR);
    }

    return $this->db_get_by_id($account_id);
  }

  /**
   * Физически меняет пароль аккаунта в БД
   *
   * @param string $password_unsafe
   * @param string $salt_unsafe
   *
   * @return bool
   */
  // OK v4.5
  public function db_set_password($password_unsafe, $salt_unsafe) {
    $password_encoded_unsafe = $this->password_encode($password_unsafe, $salt_unsafe);
    $password_encoded_safe = $this->db->db_escape($password_encoded_unsafe);

    $account_id_safe = $this->db->db_escape($this->account_id);
    $salt_safe = $this->db->db_escape($salt_unsafe);

    $result = $this->db->doquery(
      "UPDATE `{{account}}` SET
        `account_password` = '{$password_encoded_safe}',
        `account_salt` = '{$salt_safe}'
      WHERE `account_id` = '{$account_id_safe}'"
    ) ? true : false;

    if($result) {
      $result = $this->db_get_by_id($this->account_id);
    }

    return $result;
  }



  /**
   * Просаливает пароль
   *
   * @param $password
   * @param $salt
   *
   * @return string
   */
  // OK v4.5
  protected function password_encode($password, $salt) {
    return core_auth::password_encode($password, $salt);
  }
  /**
   * Генерирует соль
   *
   * @return string
   */
  // OK v4.5
  protected function password_salt_generate() {
    return core_auth::password_salt_generate();
  }

  /**
   * Вставляет запись об изменении количества ММ в лог ММ
   *
   * @param $comment
   * @param $change_type
   * @param $metamatter
   *
   * @return int|string
   */
  // OK 4.8
  protected function db_mm_log_insert($comment, $change_type, $metamatter, $user_id_unsafe) {
    $provider_id_safe = intval(core_auth::$main_provider->provider_id);
    //$account_id_safe = $this->db->db_escape($this->account_id);
    $account_id_safe = intval($this->account_id);
    $account_name_safe = $this->db->db_escape($this->account_name);

    // $user_id_safe = $this->db->db_escape(core_auth::$user['id']);
    // $user_id_safe = intval(core_auth::$user['id']);
    $user_id_safe = intval($user_id_unsafe);
    $username_safe = !empty(core_auth::$user['username']) ? $this->db->db_escape(core_auth::$user['username']) : '';

    $metamatter = round(floatval($metamatter));

    $comment_safe = $this->db->db_escape($comment);

    $server_name_safe = $this->db->db_escape(SN_ROOT_VIRTUAL);
    $page_url_safe = $this->db->db_escape($_SERVER['SCRIPT_NAME']);

    $this->db->doquery(
      "INSERT INTO `{{log_metamatter}}` 
      SET
        `provider_id` = {$provider_id_safe},
        `account_id` = {$account_id_safe},
        `account_name` = '{$account_name_safe}',
        `user_id` = {$user_id_safe},
        `username` = '{$username_safe}',
        `reason` = {$change_type},
        `amount` = {$metamatter},
        `comment` = '{$comment_safe}',
        `server_name` = '{$server_name_safe}',
        `page` = '{$page_url_safe}'
      ;"
    );
    $result = $this->db->db_insert_id();

    return $result;
  }

  // OK 4.8

  /**
   * @param int               $change_type
   * @param float             $metamatter
   * @param string|array|bool $comment
   * @param bool              $already_changed
   *
   * @return array|bool|int|mysqli_result|null|string
   */
  public function metamatter_change($change_type, $metamatter, $comment = false, $already_changed = false) {
    global $debug, $mm_change_legit, $config;

    if(!$this->is_exists || !($metamatter = round(floatval($metamatter)))) {
      $debug->error('Ошибка при попытке манипуляции с ММ');
      return false;
    }

    $account_id_safe = $this->db->db_escape($this->account_id);

    $mm_change_legit = true;
    // $sn_data_metamatter_db_name = pname_resource_name(RES_METAMATTER);
    if($already_changed) {
      $metamatter_total_delta = 0;
      $result = -1;
    } else {
      $metamatter_total_delta = $metamatter > 0 ? $metamatter : 0;

      $result = $this->db->doquery(
        "UPDATE `{{account}}`
        SET
          `account_metamatter` = `account_metamatter` + '{$metamatter}'" .
          ($metamatter_total_delta ? ", `account_immortal` = IF(`account_metamatter_total` + '{$metamatter_total_delta}' >= {$config->player_metamatter_immortal} AND `account_immortal` IS NULL, NOW(), `account_immortal`), `account_metamatter_total` = `account_metamatter_total` + '{$metamatter_total_delta}'" : '') .
        " WHERE `account_id` = {$account_id_safe}"
      );
      if(!$result) {
        $debug->error("Error adjusting Metamatter for player ID {$this->account_id} (Player Not Found?) with {$metamatter}. Reason: {$comment}", 'Metamatter Change', 402);
      }
      $result = SN::$db->db_affected_rows();

      $this->awardImmortal($metamatter, $config);
    }

    if(empty(core_auth::$user['id'])) {
      $user_list = PlayerToAccountTranslate::db_translate_get_users_from_account_list(core_auth::$main_provider->provider_id, $this->account_id);
      reset($user_list);
      $user_id_unsafe = key($user_list);
    } else {
      $user_id_unsafe = core_auth::$user['id'];
    }
    $user_id_safe = $this->db->db_escape($user_id_unsafe);

    if(!$result) {
      $debug->error("Error adjusting Metamatter for player ID {$this->account_id} (Player Not Found?) with {$metamatter}. Reason: {$comment}", 'Metamatter Change', 402);
    }

    if(!$already_changed) {
      $this->account_metamatter += $metamatter;
      $this->account_metamatter_total += $metamatter_total_delta;
    }

    if(is_array($comment)) {
      $comment = call_user_func_array('sprintf', $comment);
    }

    $result = $this->db_mm_log_insert($comment, $change_type, $metamatter, $user_id_unsafe);

    if($metamatter > 0 && !empty($user_id_safe)) {
      $old_referral = doquery("SELECT * FROM `{{referrals}}` WHERE `id` = {$user_id_safe} LIMIT 1 FOR UPDATE;", '', true);
      if($old_referral['id']) {
        $dark_matter_from_metamatter = $metamatter * AFFILIATE_MM_TO_REFERRAL_DM;
        doquery("UPDATE `{{referrals}}` SET dark_matter = dark_matter + '{$dark_matter_from_metamatter}' WHERE `id` = {$user_id_safe} LIMIT 1;");
        $new_referral = doquery("SELECT * FROM `{{referrals}}` WHERE `id` = {$user_id_safe} LIMIT 1;", '', true);

        $partner_bonus = floor($new_referral['dark_matter'] / $config->rpg_bonus_divisor) - ($old_referral['dark_matter'] >= $config->rpg_bonus_minimum ? floor($old_referral['dark_matter'] / $config->rpg_bonus_divisor) : 0);
        if($partner_bonus > 0 && $new_referral['dark_matter'] >= $config->rpg_bonus_minimum) {
          rpg_points_change($new_referral['id_partner'], RPG_REFERRAL_BOUGHT_MM, $partner_bonus, "Incoming MM From Referral ID {$user_id_safe}");
        }
      }
    }

    $mm_change_legit = false;
    return $result;
  }

  /**
   *
   * @return bool
   * @throws Exception
   */
  public function cookieSet($rememberMe = false, $domain = null) {
    if(!$this->is_exists) {
      throw new Exception(LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET, ERR_ERROR);
    }

    $expire_time = $rememberMe ? SN_TIME_NOW + PERIOD_YEAR : 0;

    $password_encoded = $this->password_encode_for_cookie($this->account_password);
    $cookie = $this->account_id . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . $rememberMe;
//    $this->flog("cookie_set() - Устанавливаем куку {$cookie}");

    return sn_setcookie($this->cookie_name, $cookie, $expire_time, $this->sn_root_path, $domain);
  }

  /**
   * Очищает куку аккаунта - совсем или восстанавливая куку текущего имперсонатора
   */
  // OK v4.1
  public function cookieClear($domain = null) {
    // Автоматически вообще-то - если установлена кука имперсонатора - то чистим обычную, а куку имперсонатора - копируем в неё
    if(!empty($_COOKIE[$this->cookie_name_impersonate])) {
      sn_setcookie($this->cookie_name, $_COOKIE[$this->cookie_name_impersonate], SN_TIME_NOW + PERIOD_YEAR, $this->sn_root_path, $domain);
      sn_setcookie($this->cookie_name_impersonate, '', SN_TIME_NOW - PERIOD_WEEK, $this->sn_root_path, $domain);
    } else {
      sn_setcookie($this->cookie_name, '', SN_TIME_NOW - PERIOD_WEEK, $this->sn_root_path, $domain);
    }
  }

  public function cookieLogin(&$rememberMe = false) {
    // Пытаемся войти по куке
    if(!empty($_COOKIE[$this->cookie_name])) {
      if(count(explode("/%/", $_COOKIE[$this->cookie_name])) < 4) {
        list($account_id_unsafe, $cookie_password_hash_salted, $user_remember_me) = explode(AUTH_COOKIE_DELIMETER, $_COOKIE[$this->cookie_name]);
      } else {
        list($account_id_unsafe, $user_name, $cookie_password_hash_salted, $user_remember_me) = explode("/%/", $_COOKIE[$this->cookie_name]);
      }

      if(
        $this->db_get_by_id($account_id_unsafe)
        && ($this->password_encode_for_cookie($this->account_password) == $cookie_password_hash_salted)
      ) {
        $rememberMe = intval($user_remember_me);

        return true;
      }
    }

    // Невалидная кука - чистим
    $this->cookieClear();

    return false;
  }

  protected function password_encode_for_cookie($password) {
    return md5("{$password}--" . $this->secret_word);
  }

  /**
   * @param int|float   $metamatter
   * @param classConfig $config
   */
  protected function awardImmortal($metamatter, $config) {
    if(!is_object($awardModule = moduleAward())) {
      return;
    }
    if ($this->account_metamatter + $metamatter >= $config->player_metamatter_immortal ) {
      $account_translation = PlayerToAccountTranslate::db_translate_get_users_from_account_list(ACCOUNT_PROVIDER_LOCAL, $this->account_id);
      if (!empty($account_translation)) {
        reset($account_translation);
        $thisUserId = key($account_translation);
        if ($thisUserId) {
          $thisUser = ['id' => $thisUserId];
          if (!mrc_get_level($thisUser, [], UNIT_AWARD_MEMORY_IMMORTAL, true)) {
            $awardModule->award($thisUserId, UNIT_AWARD_MEMORY, UNIT_AWARD_MEMORY_IMMORTAL);
          }
        }
      }
    }
  }

  /**
   * @param string|array $where
   * @param string|array $group
   *
   * @return bool|mysqli_result|null
   */
  public function getMetamatterSum($where = '', $group = '') {
    if(is_array($where) && !empty($where)) {
      $where = implode(' AND ', $where);
    }
    if(is_array($group) && !empty($group)) {
      $group = implode(',', $group);
    }

    $sql = "SELECT SUM(`amount`) as 'mm', reason, account_id, account_name, user_id, username
          FROM `{{log_metamatter}}`"
      . (!empty($where) ? ' WHERE ' . $where : '')
      . (!empty($group) ? ' GROUP BY ' . $group : '')
    ;

    return $this->db->doquery($sql);
  }

}
