<?php

use DBAL\db_mysql;
use Modules\sn_module;

/**
 * User: Gorlum
 * Date: 10.10.2015
 * Time: 6:05
 */
class auth_abstract extends sn_module {
  public $manifest = [];

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
  public $account_login_message = '';

  /**
   * @var db_mysql $db
   */
  // TODO Should be PROTECTED
  public $db;

  /**
   * @param string $filename
   */
  public function __construct($filename = __FILE__) {
    if ($this->provider_id == ACCOUNT_PROVIDER_NONE) {
      die('У всех провайдеров должен быть $provider_id!');
    }

    parent::__construct($filename);
  }

  /**
   * @return int
   */
  public function login() {
    return $this->account_login_status;
  }

  /**
   *
   */
  public function logout() {
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
  public function password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe = null) {
    return
      $this->is_feature_supported(AUTH_FEATURE_PASSWORD_CHANGE)
      && is_object($this->account)
      && $this->account->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
  }

  /**
   * @param $account_to_impersonate
   */
  public function impersonate($account_to_impersonate) {
  }

  /**
   * Меняет пароль на пристыкованном аккаунте
   *
   * @param $password_unsafe
   *
   * @return bool
   */
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
  public function is_feature_supported($feature) {
    return !empty($this->features[$feature]);
  }

  /**
   * Функция предлогает имя игрока (`users`) по данным аккаунта
   *
   * @return string
   */
  public function player_name_suggest() {
    $name = '';
    if (is_object($this->account) && !empty($this->account->account_email)) {
      list($name) = explode('@', $this->account->account_email);
    }

    empty($name) && is_object($this->account) && !empty($this->account->account_name) ? $name = $this->account->account_name : false;

    return $name;
  }

}
