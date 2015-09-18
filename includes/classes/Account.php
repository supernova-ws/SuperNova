<?php

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
    $this->db = is_object($db) ? $db : classSupernova::$db;
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

    $account_row = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe}", true);
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

    $account_row = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}'", true);
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

    $account_row = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
    return $this->assign_from_db_row($account_row);
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

    $account = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}' OR `account_name` = '{$email_safe}' OR `account_email` = '{$email_safe}'", true);
    return $this->assign_from_db_row($account);
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
      "INSERT INTO {{account}} SET
        `account_name` = '{$account_name_safe}',
        `account_password` = '{$password_salted_safe}',
        `account_salt` = '{$salt_safe}',
        `account_email` = '{$email_safe}',
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
      "UPDATE {{account}} SET
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




  // ------ UNUSED -----------------------------------------------------------------------------------------------------

//  /**
//   * Физически меняет пароль аккаунта в БД
//   *
//   * @param $new_password_encoded_safe
//   * @param $salt_safe
//   *
//   * @return array|resource
//   */
//  // OK v4.1
//  public function db_set_password_by_id($account_id_unsafe, $new_password_encoded_unsafe, $salt_unsafe) {
//    $account_id_safe = $this->db->db_escape($account_id_unsafe);
//    $new_password_encoded_safe = $this->db->db_escape($new_password_encoded_unsafe);
//    $salt_safe = $this->db->db_escape($salt_unsafe);
//
//    return $this->db->doquery(
//      "UPDATE {{account}} SET
//        `account_password` = '{$new_password_encoded_safe}',
//        `account_salt` = '{$salt_safe}'
//      WHERE `account_id` = '{$account_id_safe}'"
//    );
//  }

}
