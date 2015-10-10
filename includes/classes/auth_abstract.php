<?php

/**
 * User: Gorlum
 * Date: 10.10.2015
 * Time: 6:05
 */
class auth_abstract extends sn_module {
  public $manifest = array();

  public $provider_id = ACCOUNT_PROVIDER_NONE;

  protected $features = array();

  /**
   * @var Account
   */
  public $account = null;

  /**
   * Статус входа аккаунта в игру
   *
   * @var int
   */
  public $account_login_status = LOGIN_UNDEFINED;

  /**
   * @var db_mysql $db
   */
  // TODO Should be PROTECTED
  public $db;

  /**
   * @param string $filename
   */
  // OK 4.9
  public function __construct($filename = __FILE__) {
    if($this->provider_id == ACCOUNT_PROVIDER_NONE) {
      die('У всех провайдеров должен быть $provider_id!');
    }

    parent::__construct($filename);
  }

  /**
   * @return int
   */
  // OK 4.9
  public function login() {
//    // TODO Проверяем поддерживаемость метода
//    // TODO Пытаемся залогиниться
//    $this->password_reset_send_code();
//    $this->password_reset_confirm();
//    $this->register();
//    $this->login_username();
//    $this->login_cookie();
//
//    // $this->is_impersonating = $this->account_login_status == LOGIN_SUCCESS && !empty($_COOKIE[$this->cookie_name_impersonate]);
    return $this->account_login_status;
  }

  /**
   *
   */
  // OK 4.9
  public function logout() {
//    $this->cookie_clear();
  }

  /**
   * Меняет пароль у аккаунта с проверкой старого пароля
   *
   * @param      $old_password_unsafe
   * @param      $new_password_unsafe
   * @param null $salt_unsafe
   *
   * @return array|bool|resource
   */
  // OK v4.9
  public function password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe = null) {
//    $result = $this->account->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
//    if($result) {
//      $this->cookie_set();
//    }
//
//    return $result;
    return
      $this->is_feature_supported(AUTH_FEATURE_PASSWORD_CHANGE)
      && is_object($this->account)
      && $this->account->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
  }

  /**
   * @param $account_to_impersonate
   */
  // OK 4.9
  public function impersonate($account_to_impersonate) {
//    $this->cookie_set($account_to_impersonate);
  }

  /**
   * Меняет пароль на пристыкованном аккаунте
   *
   * @param $password_unsafe
   *
   * @return bool
   */
  // OK 4.9
  public function password_check($password_unsafe) {
    return
      $this->is_feature_supported(AUTH_FEATURE_HAS_PASSWORD)
      && is_object($this->account)
      && $this->account->password_check($password_unsafe);
  }

  /**
   * Проверка на поддержку фичи
   *
   * @param $feature
   *
   * @return bool
   */
  // OK v4.9
  public function is_feature_supported($feature) {
    return !empty($this->features[$feature]);
  }

  /**
   * Функция предлогает имя игрока (`users`) по данным аккаунта
   *
   * @return string
   */
  // OK 4.6
  public function player_name_suggest() {
    $name = '';
    if(is_object($this->account) && !empty($this->account->account_email)) {
      list($name) = explode('@', $this->account->account_email);
    }

    empty($name) && is_object($this->account) && !empty($this->account->account_name) ? $name = $this->account->account_name : false;

    return $name;
  }

}
