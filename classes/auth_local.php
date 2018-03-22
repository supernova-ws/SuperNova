<?php

use DBAL\db_mysql;

/**
 * Class auth_local
 */
// Расширяет Modules\sn_module, потому что его потомки так же являются модулями
class auth_local extends auth_abstract {
  public $versionCommitted = '#43a15.27#';

  public $manifest = [
    'package'   => 'auth',
    'name'      => 'local',
    'version'   => '0a0',
    'copyright' => 'Project "SuperNova.WS" #43a15.27# copyright © 2009-2015 Gorlum',

    self::M_LOAD_ORDER => MODULE_LOAD_ORDER_AUTH_LOCAL,

    'config_path' => SN_ROOT_PHYSICAL,
  ];

  public $provider_id = ACCOUNT_PROVIDER_LOCAL;

  /**
   * Флаг входа в игру
   *
   * @var bool
   */
  protected $is_login = false;
  /**
   * Флаг регистрации
   *
   * @var bool
   */
  protected $is_register = false;
  /**
   * Флаг запроса кода на сброс пароля
   *
   * @var bool
   */
  protected $is_password_reset = false;
  /**
   * Флаг запроса на сброс пароля по коду
   *
   * @var bool
   */
  protected $is_password_reset_confirm = false;
  /**
   * Нужно ли запоминать креденшиался при выходе из браузера
   *
   * @var bool
   */
  protected $remember_me = 1;

  /**
   * @var Confirmation
   */
  protected $confirmation = null;


  protected $features = array(
    AUTH_FEATURE_EMAIL_CHANGE    => AUTH_FEATURE_EMAIL_CHANGE,
    AUTH_FEATURE_PASSWORD_RESET  => AUTH_FEATURE_PASSWORD_RESET,
    AUTH_FEATURE_PASSWORD_CHANGE => AUTH_FEATURE_PASSWORD_CHANGE,
    AUTH_FEATURE_HAS_PASSWORD    => AUTH_FEATURE_HAS_PASSWORD,
  );

  /**
   * @var string $input_login_unsafe
   */
  protected $input_login_unsafe = '';
  protected $input_login_password_raw = '';
  protected $input_login_password_raw_repeat = '';
  protected $input_email_unsafe = '';
  protected $input_language_unsafe = '';
  protected $input_language_safe = '';

  protected $domain = null;
  protected $sn_root_path = SN_ROOT_RELATIVE;
  protected $cookie_name = SN_COOKIE;
  protected $cookie_name_impersonate = SN_COOKIE_I;
  protected $secret_word = '';

  /**
   * @param string $filename
   */
  public function __construct($filename = __FILE__) {
    parent::__construct($filename);

    $this->prepare();

    $this->active = false;
    if (!empty($this->config) && is_array($this->config['db'])) {
      // БД, отличная от стандартной
      $this->db = new db_mysql();

      $this->db->sn_db_connect($this->config['db']);
      if ($this->active = $this->db->connected) {
        $this->provider_id = ACCOUNT_PROVIDER_CENTRAL;

        $this->domain = $this->config['domain'];
        $this->sn_root_path = $this->config['sn_root_path'];
        $this->cookie_name = $this->config['cookie_name'];
        $this->secret_word = $this->config['secretword'];
        // TODO Проверить наличие всех нужных таблиц
      } else {
        unset($this->db);
      }
    }

    // Fallback to local DB
    if (!$this->active) {
      $this->db = SN::$db;

      $this->provider_id = ACCOUNT_PROVIDER_LOCAL;

      $this->domain = null;
      $this->sn_root_path = SN_ROOT_RELATIVE;
      $this->cookie_name = SN_COOKIE;
      $this->secret_word = SN::$sn_secret_word;

      $this->active = true;
    }

    $this->cookie_name_impersonate = $this->cookie_name . AUTH_COOKIE_IMPERSONATE_SUFFIX;

    $this->account = new Account($this->db);
    $this->confirmation = new Confirmation($this->db);
  }

  /**
   * Попытка залогиниться с использованием метода $method
   * @version 4.5
   *
   * @param string $method_name
   */
  public function login() {
    // TODO Проверяем поддерживаемость метода
    // TODO Пытаемся залогиниться
    $this->password_reset_send_code();
    $this->password_reset_confirm();
    $this->register();
    $this->login_username();
    $this->login_cookie();

    // $this->is_impersonating = $this->account_login_status == LOGIN_SUCCESS && !empty($_COOKIE[$this->cookie_name_impersonate]);

    return $this->account_login_status;
  }

  public function logout() {
    $this->cookie_clear();
  }

  /**
   * Меняет пароль у аккаунта с проверкой старого пароля
   *
   * @param      $old_password_unsafe
   * @param      $new_password_unsafe
   * @param null $salt_unsafe
   *
   * @return array|bool|resource
   * @throws Exception
   */
  public function password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe = null) {
    $result = parent::password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
    if ($result) {
      $this->cookie_set();
    }

    return $result;
  }

  public function impersonate($account_to_impersonate) {
    $this->cookie_set($account_to_impersonate);
  }

  /**
   * @param Account $account
   */
  public function login_with_account($account) {
    $this->account = $account;
    $this->cookie_set();

    return $this->login_cookie();
  }


  /**
   * Отсылает письмо с кодом подтверждения для сброса пароля
   *
   * @return int|string
   */
  protected function password_reset_send_code() {
    global $lang, $config;

    if (!$this->is_password_reset) {
      return $this->account_login_status;
    }

    // Проверяем поддержку сброса пароля
    if (!$this->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
      return $this->account_login_status;
    }

    try {
      $email_unsafe = $this->input_email_unsafe;

      unset($this->account);
      $this->account = new Account($this->db);

      if (!$this->account->db_get_by_email($email_unsafe)) {
        throw new Exception(PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS, ERR_ERROR);
        // return $this->account_login_status;
      }

      $account_translation = PlayerToAccountTranslate::db_translate_get_users_from_account_list($this->provider_id, $this->account->account_id); // OK 4.5
      $user_list = db_user_list_by_id(array_keys($account_translation));

      // TODO - Проверять уровень доступа аккаунта!
      // Аккаунты с АУТЛЕВЕЛ больше 0 - НЕ СБРАСЫВАЮТ ПАРОЛИ!
      foreach ($user_list as $user_id => $user_data) {
        if ($user_data['authlevel'] > AUTH_LEVEL_REGISTERED) {
          throw new Exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT, ERR_ERROR);
        }
      }

      $confirmation = $this->confirmation->db_confirmation_get_latest_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5
      if (isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
        throw new Exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN, ERR_ERROR);
      }

      // Удаляем предыдущие записи продтверждения сброса пароля
      !empty($confirmation['id']) or $this->confirmation->db_confirmation_delete_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5

      sn_db_transaction_start();
      $confirm_code_unsafe = $this->confirmation->db_confirmation_get_unique_code_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5
      sn_db_transaction_commit();

      if (!is_email($email_unsafe)) {
        SN::$debug->error("Email is invalid: '{$email_unsafe}'", 'Invalid email for password restoration');
      }

      @$result = mymail($email_unsafe,
        sprintf($lang['log_lost_email_title'], $config->game_name),
        sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . 'login.php', $confirm_code_unsafe, date(FMT_DATE_TIME, SN_TIME_NOW + AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE), $config->game_name)
      );

      $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      $result = $e->getMessage();
    }

    return $this->account_login_status = $result;
  }

  /**
   * Сброс пароля по введенному коду подтверждения
   *
   * @return int|string
   */
  protected function password_reset_confirm() {
    global $lang, $config;

    if (!$this->is_password_reset_confirm) {
      return $this->account_login_status;
    }

    if ($this->account_login_status != LOGIN_UNDEFINED) {
      return $this->account_login_status;
    }

    // Проверяем поддержку сброса пароля
    if (!$this->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
      return $this->account_login_status;
    }

    try {
      $code_unsafe = sys_get_param_str_unsafe('password_reset_code');
      if (empty($code_unsafe)) {
        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_EMPTY, ERR_ERROR);
      }

      sn_db_transaction_start();
      $confirmation = $this->confirmation->db_confirmation_get_by_type_and_code(CONFIRM_PASSWORD_RESET, $code_unsafe); // OK 4.5

      if (empty($confirmation)) {
        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_WRONG, ERR_ERROR);
      }

      if (SN_TIME_NOW - strtotime($confirmation['create_time']) > AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE) {
        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD, ERR_ERROR);
      }

      unset($this->account);
      $this->account = new Account($this->db);

      if (!$this->account->db_get_by_email($confirmation['email'])) {
        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL, ERR_ERROR);
      }

      $new_password_unsafe = $this->make_random_password();
      $salt_unsafe = $this->password_salt_generate();
      if (!$this->account->db_set_password($new_password_unsafe, $salt_unsafe)) {
        // Ошибка смены пароля
        throw new Exception(AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE, ERR_ERROR);
      }

      $this->account_login_status = LOGIN_UNDEFINED;
      $this->remember_me = 1;
      $this->cookie_set();
      $this->login_cookie();

      if ($this->account_login_status == LOGIN_SUCCESS) {
        // TODO - НЕ ОБЯЗАТЕЛЬНО ОТПРАВЛЯТЬ ЧЕРЕЗ ЕМЕЙЛ! ЕСЛИ ЭТО ФЕЙСБУЧЕК ИЛИ ВКШЕЧКА - МОЖНО ЧЕРЕЗ ЛС ПИСАТЬ!!
        $message_header = sprintf($lang['log_lost_email_title'], $config->game_name);
        $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $this->account->account_name, $new_password_unsafe);
        @$operation_result = mymail($confirmation['email'], $message_header, htmlspecialchars($message));

        $users_translated = PlayerToAccountTranslate::db_translate_get_users_from_account_list($this->provider_id, $this->account->account_id); // OK 4.5
        if (!empty($users_translated)) {
          // Отправляем в лички письмо о сбросе пароля

          // ПО ОПРЕДЕЛЕНИЮ в $users_translated только
          //    - аккаунты, поддерживающие сброс пароля
          //    - список аккаунтов, имеющих тот же емейл, что указан в Подтверждении
          //    - игроки, привязанные только к этим аккаунтам
          // Значит им всем сразу скопом можно отправлять сообщения
          $message = sprintf($lang['sys_password_reset_message_body'], $new_password_unsafe);
          $message = HelperString::nl2br($message) . '<br><br>';
          // msg_send_simple_message($found_provider->data[F_USER_ID], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);

          foreach ($users_translated as $user_id => $providers_list) {
            msg_send_simple_message($user_id, 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);
          }
        } else {
          // Фигня - может быть и пустой, если у нас есть только аккаунт, но нет пользователей
          // throw new Exception(AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION, ERR_ERROR);
        }
      }

      $this->confirmation->db_confirmation_delete_by_type_and_email(CONFIRM_PASSWORD_RESET, $confirmation['email']); // OK 4.5

      sn_db_transaction_commit();

      sys_redirect('overview.php');
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      $this->account_login_status = $e->getMessage();
    }

    return $this->account_login_status;
  }

  /**
   * Функция инициализирует данные провайдера - разворачивает куки, берет данные итд
   */
  protected function prepare() {
    $this->input_login_unsafe = sys_get_param_str_unsafe('username', sys_get_param_str_unsafe('email')); // TODO переделать эту порнографию

    $this->is_login = sys_get_param('login') ? true : false;
    $this->is_register = sys_get_param('register') ? true : false;
    $this->is_password_reset = sys_get_param('password_reset') ? true : false;
    $this->is_password_reset_confirm = sys_get_param('password_reset_confirm') ? true : false;

    $this->remember_me = intval(sys_get_param_int('rememberme') || $this->is_register);
    $this->input_login_password_raw = sys_get_param('password');
    $this->input_login_password_raw_repeat = sys_get_param('password_repeat');
    $this->input_email_unsafe = sys_get_param_str_unsafe('email');
    $this->input_language_unsafe = sys_get_param_str_unsafe('lang', DEFAULT_LANG);
    $this->input_language_safe = sys_get_param_str('lang', DEFAULT_LANG);

  }

  /**
   * Пытается зарегестрировать пользователя по введенным данным
   * @version 4.5
   *
   * @return mixed
   */
  protected function register() {
    // TODO РЕГИСТРАЦИЯ ВСЕГДА ДОЛЖНА ЛОГИНИТЬ ПОЛЬЗОВАТЕЛЯ!
    $this->flog('Регистрация: начинаем. Провайдер ' . $this->provider_id);

    try {
      if (!$this->is_register) {
        $this->flog('Регистрация: не выставлен флаг регистрации - пропускаем');
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->register_validate_input();

      sn_db_transaction_start();
      $this->account->db_get_by_name_or_email($this->input_login_unsafe, $this->input_email_unsafe);
      if ($this->account->is_exists) {
        if ($this->account->account_email == $this->input_email_unsafe) {
          throw new Exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
        } else {
          throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
        }
      }

      // Проблемы с созданием аккаунта - вызовут эксершн и обработается catch()
      $this->account->db_create(
        $this->input_login_unsafe,
        $this->input_login_password_raw,
        $this->input_email_unsafe,
        $this->input_language_unsafe
      );

      // Устанавливать не надо - мы дальше пойдем по workflow
      $this->account_login_status = LOGIN_SUCCESS;
      $this->cookie_set();

      // А вот это пока не нужно. Трансляцией аккаунтов в юзеров и созданием новых юзеров для новозашедших аккаунтов занимается Auth
      // $this->register_account();
      sn_db_transaction_commit();
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      $this->account_login_status == LOGIN_UNDEFINED ? $this->account_login_status = $e->getMessage() : false;
    }

    return $this->account_login_status;
  }

  /**
   * Пытается залогинить пользователя по куке
   * @version 4.5
   *
   * @return int Результат попытки
   */
  protected function login_cookie() {
    if ($this->account_login_status != LOGIN_UNDEFINED) {
      return $this->account_login_status;
    }

    if ($this->account->cookieLogin($rememberMe)) {
      $this->account_login_status = LOGIN_SUCCESS;
      $this->remember_me = intval($rememberMe);
    }

    return $this->account_login_status;
  }

  /**
   * Пытается залогинить пользователя по имени аккаунта и паролю
   * @version 4.5
   *
   * @return mixed
   */
  protected function login_username() {
    // TODO - Логин по старым именам
    try {
      if (!$this->is_login) {
        $this->flog('Логин: не выставлен флаг входа в игру - это не логин');
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      // TODO Пустое имя аккаунта
      if (!$this->input_login_unsafe) {
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->login_validate_input();

      if (!$this->account->db_get_by_name($this->input_login_unsafe) && !$this->account->db_get_by_email($this->input_login_unsafe)) {
        throw new Exception(LOGIN_ERROR_USERNAME, ERR_ERROR);
      }

      if (!$this->account->password_check($this->input_login_password_raw)) {
        throw new Exception(LOGIN_ERROR_PASSWORD, ERR_ERROR);
      }

      $this->cookie_set();
      $this->account_login_status = LOGIN_SUCCESS;
    } catch (Exception $e) {
      $this->account_login_status == LOGIN_UNDEFINED ? $this->account_login_status = $e->getMessage() : false;
    }

    return $this->account_login_status;
  }

  /**
   * Устанавливает куку аккаунта по данным $this->data[F_ACCOUNT]
   *
   * @param Account|null $account_to_impersonate
   *
   * @return bool
   * @throws Exception
   *
   */
  // TODO - должен устанавливать куку исходя из пользователя, что бы пользователь мог логинится
  // TODO - или ставить мультикуку - хотя нахуя, спрашивается
  protected function cookie_set($account_to_impersonate = null) {
    $this_account = is_object($account_to_impersonate) ? $account_to_impersonate : $this->account;

    if (!is_object($this_account) || !$this_account->is_exists) {
      throw new Exception(LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET, ERR_ERROR);
    }

    if (is_object($account_to_impersonate) && $account_to_impersonate->is_exists) {
      sn_setcookie($this->cookie_name_impersonate, $_COOKIE[$this->cookie_name], SN_TIME_NOW + PERIOD_YEAR, $this->sn_root_path, $this->domain);
    }

    $this->flog("cookie_set() - Устанавливаем куку");

    return $this_account->cookieSet($this->remember_me, $this->domain);
  }

  /**
   * Очищает куку аккаунта - совсем или восстанавливая куку текущего имперсонатора
   */
  protected function cookie_clear() {
    $this->account->cookieClear($this->domain);
  }


  // ХЕЛПЕРЫ ===========================================================================================================

  /**
   * Проверяет введенные данные логина на корректность
   *
   * @throws Exception
   */
  protected function login_validate_input() {
    // Проверяем, что бы в начале и конце не было пустых символов
    // TODO - при копировании Эксель -> Опера - в конце образуются пустые места. Это не должно быть проблемой! Вынести проверку пароля в регистрацию!
    if ($this->input_login_password_raw != trim($this->input_login_password_raw)) {
      throw new Exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    if (!$this->input_login_password_raw) {
      throw new Exception(LOGIN_ERROR_PASSWORD_EMPTY, ERR_ERROR);
    }
  }

  /**
   * Проверяет данные для регистрации на корректность
   *
   * @throws Exception
   */
  protected function register_validate_input() {
    // То, что не подходит для логина - не подходит и для регистрации
    $this->login_validate_input();

    // Если нет имени пользователя - NO GO!
    if (!$this->input_login_unsafe) {
      throw new Exception(LOGIN_ERROR_USERNAME_EMPTY, ERR_ERROR);
    }
    // Если логин имеет запрещенные символы - NO GO!
    if (strpbrk($this->input_login_unsafe, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      throw new Exception(LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS, ERR_ERROR);
    }
    // Если логин меньше минимальной длины - NO GO!
    if (strlen($this->input_login_unsafe) < LOGIN_LENGTH_MIN) {
      throw new Exception(REGISTER_ERROR_USERNAME_SHORT, ERR_ERROR);
    }
    // Если пароль меньше минимальной длины - NO GO!
    if (strlen($this->input_login_password_raw) < PASSWORD_LENGTH_MIN) {
      throw new Exception(REGISTER_ERROR_PASSWORD_INSECURE, ERR_ERROR);
    }
    // Если пароль имеет пробельные символы в начале или конце - NO GO!
    if ($this->input_login_password_raw != trim($this->input_login_password_raw)) {
      throw new Exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    // Если пароль не совпадает с подтверждением - NO GO! То, что у пароля нет пробельных символов в начале/конце - мы уже проверили выше
    //Если они есть у повтора - значит пароль и повтор не совпадут
    if ($this->input_login_password_raw <> $this->input_login_password_raw_repeat) {
      throw new Exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
    }
    // Если нет емейла - NO GO!
    // TODO - регистрация без емейла
    if (!$this->input_email_unsafe) {
      throw new Exception(REGISTER_ERROR_EMAIL_EMPTY, ERR_ERROR);
    }
    // Если емейл не является емейлом - NO GO!
    if (!is_email($this->input_email_unsafe)) {
      throw new Exception(REGISTER_ERROR_EMAIL_WRONG, ERR_ERROR);
    }
  }


  protected function password_encode($password, $salt) {
    return core_auth::password_encode($password, $salt);
  }

  protected function password_salt_generate() {
    return core_auth::password_salt_generate();
  }

  /**
   * Генерирует случайный пароль
   *
   * @return string
   */
  protected function make_random_password() {
    return core_auth::make_random_password();
  }

  protected function flog($message, $die = false) {
    if (!defined('DEBUG_AUTH') || !DEBUG_AUTH) {
      return;
    }
    list($called, $caller) = debug_backtrace(false);

    $caller_name =
      ((get_called_class()) ? get_called_class() : (!empty($caller['class']) ? $caller['class'] : '')) .
      (!empty($caller['type']) ? $caller['type'] : '') .
      (!empty($caller['function']) ? $caller['function'] : '') .
      (!empty($called['line']) ? ':' . $called['line'] : '');

    $_SERVER['SERVER_NAME'] == 'localhost' ? print("<div class='debug'>$message - $caller_name\r\n</div>") : false;

    SN::log_file("$message - $caller_name");
    if ($die) {
      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
    }
  }

}
