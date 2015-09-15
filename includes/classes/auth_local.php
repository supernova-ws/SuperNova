<?php

/**
 * Class auth_local
 */
// Расширяет sn_module, потому что его потомки так же являются модулями
class auth_local extends sn_module {
  public $manifest = array(
    'package' => 'auth',
    'name' => 'local',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a10.21# copyright © 2009-2015 Gorlum',

    // 'require' => array('auth_provider'),
    'root_relative' => '',

    'load_order' => 2,

    'installed' => true,
    'active' => true,

    'provider_id' => ACCOUNT_PROVIDER_LOCAL,

    // 'class_path' => __FILE__,
    'config_path' => SN_ROOT_PHYSICAL,
  );

  /**
   * Флаг регистрации
   *
   * @var bool
   */
  public $is_register = false;
  /**
   * Нужно ли запоминать креденшиался при выходе из браузера
   *
   * @var bool
   */
  public $remember_me = 1;
  /**
   * @var Account
   */
  public $account = null;
  // TODO - private ??
  //public $user = null;

  // TODO - должны быть PRIVATE
  public $data = array();

  /**
   * Статус входа аккаунта в игру
   *
   * @var int
   */
  public $account_login_status = LOGIN_UNDEFINED;





  protected $features = array(
    AUTH_FEATURE_EMAIL_CHANGE => AUTH_FEATURE_EMAIL_CHANGE,
    AUTH_FEATURE_PASSWORD_RESET => AUTH_FEATURE_PASSWORD_RESET,
    AUTH_FEATURE_PASSWORD_CHANGE => AUTH_FEATURE_PASSWORD_CHANGE,
    AUTH_FEATURE_HAS_PASSWORD => AUTH_FEATURE_HAS_PASSWORD,
  );

  /**
   * @var db_mysql $db
   */
  // Should be PROTECTED
  public $db;

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
  protected $sn_root_path = null;
  protected $cookie_name = SN_COOKIE;
  protected $secret_word = '';

//  public $login_methods_supported = array(
//    // 'login_cookie' => 'login_cookie',
//    // 'login_username' => 'login_username',
//    // 'register_username' => 'register_username',
//  );
//  Пока не будем с этим заморачиваться. Будут юниттесты - будем плакать. А так - только лишний гемморой
//  /**
//   * Используемый менеджер авторизации
//   *
//   * @var auth $auth
//   */
//  protected $auth = null;
//
//  /**
//   * Конструктор
//   *
//   * @param auth $auth
//   */
//  function __construct($auth) {
//    parent::__construct(__FILE__);
//
//    $this->auth = $auth;
//  }
//  public function auth_manager_set($auth) {
//    $this->auth = $auth;
//  }
//

  public function set_database($db = null) {
    $this->db = is_object($db) ? $db : classSupernova::$db;
  }

  /**
   * @param string $filename
   */
  // OK v4.5
  public function __construct($filename = __FILE__) {
    parent::__construct($filename);

    $this->prepare();

    $this->manifest['active'] = false;
    if(!empty($this->config) && is_array($this->config['db'])) {
      // БД, отличная от стандартной
      $this->db = new db_mysql();

      $this->db->sn_db_connect($this->config['db']);
      if($this->manifest['active'] = $this->db->connected) {
        $this->manifest['provider_id'] = ACCOUNT_PROVIDER_CENTRAL;

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
    if(!$this->manifest['active']) {
      $this->db = classSupernova::$db;

      $this->manifest['provider_id'] = ACCOUNT_PROVIDER_LOCAL;

      $this->domain = null;
      $this->sn_root_path = SN_ROOT_RELATIVE;
      $this->cookie_name = SN_COOKIE;
      $this->secret_word = classSupernova::$sn_secret_word;

      $this->manifest['active'] = true;
    }

    $this->account = new Account($this->db);
  }

  /**
   * Попытка залогиниться с использованием метода $method
   * @version 4.5
   *
   * @param string $method_name
   */
  // OK v4.5
  public function login() {
    // TODO Проверяем поддерживаемость метода
    // TODO Пытаемся залогиниться
    $this->register();
    $this->login_username();
    $this->login_cookie();

    return $this->account_login_status;
  }

  public function logout() {
    $this->cookie_clear();
  }

  // TODO - переделать!
  // TODO - NOT OK v4
  public function password_check($password_unsafe) {
    return $this->account->password_check($password_unsafe);
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
  // OK v4.5
  public function password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe = null) {
    $result = $this->account->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
    if($result) {
      $this->cookie_set();
    }

    return $result;
  }

  /**
   * Проверка на поддержку фичи
   *
   * @param $feature
   *
   * @return bool
   */
  // OK v4.1
  public function is_feature_supported($feature) {
    return !empty($this->features[$feature]);
  }

  /**
   * Функция предлогает имя игрока (`users`) по данным аккаунта
   *
   * @return string
   */
  // OK 4.5
  public function suggest_player_name() {
    return !empty($this->account->account_name) ? $this->account->account_name : '';
  }

  /**
   * Логин по выставленным полям
   */
  // OK v4.1
  // TODO - protected
  public function login_internal() {
    try {
      $this->account_login_status = LOGIN_UNDEFINED;
      $this->remember_me = true;
      $this->cookie_set();
      $this->login_cookie();
    } catch(Exception $e) {
      // sn_db_transaction_rollback();
      $this->account_login_status == LOGIN_UNDEFINED ? $this->account_login_status = $e->getMessage() : false;
    }

    return $this->account_login_status;
  }


  /**
   * Меняет пароль на всех аккаунтах, у которых есть данный емейл
   *
   * @param $email_unsafe
   * @param $new_password_unsafe
   * @param $salt_unsafe
   *
   * @return array
   */
  // OK 4.5
  public function password_change_on_email($email_unsafe, $new_password_unsafe, $salt_unsafe) {
    global $lang, $config;

    $account_translation = array();

    // Проверяем поддержку сброса пароля
    if(!$this->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
      return $account_translation;
    }
    // Получаем список аккаунтов у провайдера по емейлу подтверждения
    $account_list = $this->db_account_list_get_on_email($email_unsafe); // OK 4.5

    // TODO - это всё надо перенести в провайдера!
    // Меняем пароль на всех аккаунтах
    foreach($account_list as $account_id_unsafe => $account_data) {
      // unset($account);

      $account = new Account($this->db);
      // Если аккаунт не существует или пароль тот же самый
      if(!$account->db_get_by_id($account_id_unsafe) || $account->password_check($new_password_unsafe)) { // OK 4.5
        // Пропускаем смену пароля
        // TODO - Или меняем с новой солью?
        continue;
      }

      if($account->db_set_password($new_password_unsafe, $salt_unsafe)) { // OK 4.5
        // Получаем список юзеров на этом аккаунте
        $this_provider_translation = auth::db_translate_get_users_from_account_list($this->manifest['provider_id'], $account->account_id); // OK 4.5
        if(!empty($this_provider_translation)) {
          $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
          // TODO - if !$this->account - тогда берем первый аккаунт и в него логиним
          // TODO Логиним этого пользователя
          // self::$login_status = $provider->login_internal();
          // TODO - При ошибке отправки емейла добавлять Global Message
        }

        // TODO - НЕ ОБЯЗАТЕЛЬНО ОТПРАВЛЯТЬ ЧЕРЕЗ ЕМЕЙЛ! ЕСЛИ ЭТО ФЕЙСБУЧЕК ИЛИ ВКШЕЧКА - МОЖНО ЧЕРЕЗ ЛС ПИСАТЬ!!
        $message_header = sprintf($lang['log_lost_email_title'], $config->game_name);
        $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $account->account_name, $new_password_unsafe);
        @$operation_result = mymail($email_unsafe, $message_header, htmlspecialchars($message));
      }
    }

    return $account_translation;
  }



  /**
   * Физически меняется пароль в БД
   *
   * @param int $account_id_unsafe
   * @param string $new_password_unsafe
   * @param null $new_salt_unsafe
   *
   * @return boolean
   * @throws Exception
   */
  // TODO - переделать! См. точку вызова
  // TODO - Должен работать со списками и без ID!
  // OK v4.1
  // TODO - protected
  public function password_set_by_account_id($account_id_unsafe, $new_password_unsafe, $new_salt_unsafe) {
    $account = new Account($this->db);
    if(!$account->db_get_by_id($account_id_unsafe)) {
      throw new Exception(PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS, ERR_ERROR);
    }

    return $account->password_check($new_password_unsafe) || $account->db_set_password($new_password_unsafe, $new_salt_unsafe);

//    $account = $this->db_account_get_by_id($account_id_unsafe);
//    if(empty($account['account_id'])) {
//      // Внутренняя ошибка. Такого быть не должно!
//      throw new Exception(PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS, ERR_ERROR);
//    }

//    $new_salt_unsafe === null ? $new_salt_unsafe = $this->password_salt_generate() : false;
    // Проверка на тот же пароль
//    if($account['account_password'] == $new_password_unsafe && $account['account_salt'] == $new_salt_unsafe) {
//      return $account;
//    }


//    $salted_password_unsafe = $this->password_encode($new_password_unsafe, $new_salt_unsafe);
//    $result = $this->db_account_set_password_by_id($account_id_unsafe, $salted_password_unsafe, $new_salt_unsafe);
//    // $result = doquery("UPDATE {{account}} SET `account_password` = '{$salted_password_safe}', `account_salt` = '{$salt_safe}' WHERE `account_id` = '{$account_id_safe}'");
//    if($result && $this->db->db_affected_rows()) {
//      // Меняем данные аккаунта
//      $account = $this->db_account_get_by_id($account_id_unsafe);
//      $this->data[F_ACCOUNT] = $account;
//      $result = true;
//    } else {
//      $result = false;
//    }
//    return $result;
  }

  /**
   * @param $new_email_unsafe
   *
   * @return array|bool|resource
   */
  // TODO v4.1
  // TODO Должен работать со списками или с ID!
  // TODO - protected
  public function db_account_set_email($new_email_unsafe) {
    die('{Смена емейла пока не работает}');
    // return db_user_set_by_id($this->data[F_ACCOUNT_ID], "`email_2` = '" . db_escape($new_email_unsafe) . "'");
  }





  /**
   * Возвращает список аккаунтов, которые привязаны к указанному емейлу
   *
   * @param $email_unsafe
   *
   * @return array
   */
  // OK v4.5
  // TODO - вынести в отдельный объект
  protected function db_account_list_get_on_email($email_unsafe) {
    $email_safe = $this->db->db_escape($email_unsafe);
    $query = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}' FOR UPDATE;");
    $account_list = array();
    while($row = $this->db->db_fetch($query)) {
      $account_list[$row['account_id']] = $row;
    }
    return $account_list;
  }









  /**
   * Функция инициализирует данные провайдера - разворачивает куки, берет данные итд
   */
  // OK v4.5
  protected function prepare() {
    // $this->data = &$this->data[$this->manifest['provider_id']];
    !is_array($this->data) ? $this->data = array() : false;

    $this->input_login_unsafe = sys_get_param_str_unsafe('username', sys_get_param_str_unsafe('login')); // TODO переделать эту порнографию

    $this->is_register = sys_get_param('register');
    $this->remember_me = intval(sys_get_param_int('rememberme') || $this->is_register);
    $this->input_login_password_raw = sys_get_param('password');
    $this->input_login_password_raw_repeat = sys_get_param('password_repeat');
    $this->input_email_unsafe = sys_get_param_str_unsafe('email');
    $this->input_language_unsafe = sys_get_param_str_unsafe('lang', DEFAULT_LANG);
    $this->input_language_safe = sys_get_param_str('lang', DEFAULT_LANG);


    $this->data = array(
      // F_PROVIDER_ID => $this->manifest['provider_id'],
      // F_LOGIN_STATUS => LOGIN_UNDEFINED,
      F_IMPERSONATE_STATUS => LOGIN_UNDEFINED,
      F_IMPERSONATE_OPERATOR => null,
      // F_INPUT => array(
      // F_IS_REGISTER => $is_register = sys_get_param('register'),
      // F_LOGIN_UNSAFE => sys_get_param_str_unsafe('username', sys_get_param_str_unsafe('login')),
      // F_LOGIN_PASSWORD_RAW => sys_get_param('password'),
      // F_LOGIN_PASSWORD_REPEAT_RAW => trim(sys_get_param('password_repeat')),
      // F_EMAIL_UNSAFE => sys_get_param_str_unsafe('email'),
      // F_LANGUAGE_SAFE => sys_get_param_str('lang', DEFAULT_LANG),

      // F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
      // F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
      // F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
      // ),
      // F_REMEMBER_ME_SAFE => intval(sys_get_param_int('rememberme') || $this->is_register),

      // F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
      // F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
      // F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
    );
  }

  /**
   * Пытается зарегестрировать пользователя по введенным данным
   * @version 4.5
   *
   * @return mixed
   */
  // OK v4.5
  protected function register() {
    // TODO РЕГИСТРАЦИЯ ВСЕГДА ДОЛЖНА ЛОГИНИТЬ ПОЛЬЗОВАТЕЛЯ!
    $this->flog('Регистрация: начинаем. Провайдер ' . $this->manifest['provider_id']);

    try {
      if(!$this->is_register) {
        $this->flog('Регистрация: не выставлен флаг регистрации - пропускаем');
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->register_validate_input();

      sn_db_transaction_start();
      // $this->provider_account = new Account($this->db);
      // $this->account_check_duplicate_name_or_email($this->input_login_unsafe, $this->input_email_unsafe);

      $this->account->db_get_by_name_or_email($this->input_login_unsafe, $this->input_email_unsafe);
      if($this->account->is_exists) {
        if($this->account->account_name == $this->input_login_unsafe) {
          throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
        } else {
          throw new Exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
        }
      }

//      if($this->provider_account->db_get_by_name($this->input_login_unsafe)) {
//        throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
//      }
//
//      if($this->provider_account->db_get_by_email($this->input_email_unsafe)) {
//        throw new Exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
//      }


      // Проблемы с созданием аккаунта - вызовут эксершн и обработается catch()
      $this->account->db_create(
        $this->input_login_unsafe,
        $this->input_login_password_raw,
        $this->input_email_unsafe,
        $this->input_language_unsafe
      );
//      $this->db_account_create(
//        $this->input_login_unsafe,
//        $this->input_login_password_raw,
//        $this->input_email_unsafe,
//        $this->input_language_unsafe
//      );

      // $this->data[F_ACCOUNT] = $this->db_account_get_by_id($account_id);

      // Устанавливать не надо - мы дальше пойдем по workflow
      $this->account_login_status = LOGIN_SUCCESS;
      $this->cookie_set();

      // А вот это пока не нужно. Трансляцией аккаунтов в юзеров и созданием новых юзеров для новозашедших аккаунтов занимается Auth
      // $this->register_account();
      sn_db_transaction_commit();
    } catch(Exception $e) {
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
  // OK v4.5
  protected function login_cookie() {
    if($this->account_login_status != LOGIN_UNDEFINED) {
      return $this->account_login_status;
    }

    // Пытаемся войти по куке
    if(!empty($_COOKIE[$this->cookie_name])) {
      if(count(explode("/%/", $_COOKIE[$this->cookie_name])) < 4) {
        list($account_id_unsafe, $cookie_password_hash_salted, $user_remember_me) = explode(AUTH_COOKIE_DELIMETER, $_COOKIE[$this->cookie_name]);
      } else {
        list($account_id_unsafe, $user_name, $cookie_password_hash_salted, $user_remember_me) = explode("/%/", $_COOKIE[$this->cookie_name]);
      }

      // $account = $this->db_account_get_by_id($account_id_unsafe);

      if(
        $this->account->db_get_by_id($account_id_unsafe)
        && ($this->password_encode_for_cookie($this->account->account_password) == $cookie_password_hash_salted)
      ) {
        $this->account_login_status = LOGIN_SUCCESS;
        $this->remember_me = intval($user_remember_me);
        // $this->data[F_ACCOUNT] = $account;
      }
    }

    if($this->account_login_status != LOGIN_SUCCESS) {
      // Невалидная кука - чистим
      $this->cookie_clear();
    }

    return $this->account_login_status;
  }

  /**
   * Пытается залогинить пользователя по имени аккаунта и паролю
   * @version 4.5
   *
   * @return mixed
   */
  // OK v4.5
  protected function login_username() {
    // TODO - Логин по старым именам
    try {
      if($this->is_register) {
        $this->flog('Логин: выставлен флаг регистрации - это не логин');
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      // TODO Пустое имя аккаунта
      if(!$this->input_login_unsafe) {
        throw new Exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->login_validate_input();

//      $account = $this->db_account_get_by_name($this->input_login_unsafe);
//      if(empty($account)) {
//        throw new Exception(LOGIN_ERROR_USERNAME, ERR_ERROR);
//      }
      if(!$this->account->db_get_by_name($this->input_login_unsafe)) {
        throw new Exception(LOGIN_ERROR_USERNAME, ERR_ERROR);
      }

      if(!$this->account->password_check($this->input_login_password_raw)) {
        throw new Exception(LOGIN_ERROR_PASSWORD, ERR_ERROR);
      }

      // $this->data[F_ACCOUNT] = $account;

      $this->cookie_set();
      $this->account_login_status = LOGIN_SUCCESS;
    } catch(Exception $e) {
      $this->account_login_status == LOGIN_UNDEFINED ? $this->account_login_status = $e->getMessage() : false;
    }

    return $this->account_login_status;
  }

  /**
   * Устанавливает куку аккаунта по данным $this->data[F_ACCOUNT]
   *
   * @return bool
   */
  // OK v4.5
  // TODO - должен устанавливать куку исходя из пользователя, что бы пользователь мог логинится
  // TODO - или ставить мультикуку - хотя нахуя, спрашивается
  protected function cookie_set() {
    if(!is_object($this->account) || !$this->account->is_exists) {
      throw new Exception(LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET, ERR_ERROR);
    }

    // $expire_time = $this->data[F_REMEMBER_ME_SAFE] ? SN_TIME_NOW + PERIOD_YEAR : 0;
    $expire_time = $this->remember_me ? SN_TIME_NOW + PERIOD_YEAR : 0;

    $password_encoded = $this->password_encode_for_cookie($this->account->account_password);
    $cookie = $this->account->account_id . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . $this->remember_me;
    $this->flog("cookie_set() - Устанавливаем куку {$cookie}");
    return sn_setcookie($this->cookie_name, $cookie, $expire_time, $this->sn_root_path, $this->domain);
  }

  /**
   * Очищает куку аккаунта - совсем или восстанавливая куку текущего имперсонатора
   */
  // OK v4.1
  protected function cookie_clear() {
    // Автоматически вообще-то - если установлена кука имперсонатора - то чистим обычную, а куку имперсонатора - копируем в неё
    if(!empty($_COOKIE[$this->cookie_name . '_I'])) {
      sn_setcookie($this->cookie_name, $_COOKIE[$this->cookie_name . '_I'], SN_TIME_NOW + PERIOD_YEAR, $this->sn_root_path, $this->domain);
      sn_setcookie($this->cookie_name . '_I', '', SN_TIME_NOW - PERIOD_WEEK, $this->sn_root_path, $this->domain);
      // Если это был корректный имперсонатор - просто выходим и редиректимся в админку
      if(!empty($_COOKIE[$this->cookie_name])) {
        sys_redirect(SN_ROOT_VIRTUAL . 'admin/overview.php');
      }
    } else {
//print('<hr>');
//pdump(get_called_class());
//pdump($this->domain);
//pdump($this->sn_root_path);
//print('<hr>');
// die();
      sn_setcookie($this->cookie_name, '', SN_TIME_NOW - PERIOD_WEEK, $this->sn_root_path, $this->domain);
    }
  }


  // ХЕЛПЕРЫ ===========================================================================================================
  /**
   * Проверяет введенные данные логина на корректность
   *
   * @throws Exception
   */
  // OK v4.1
  protected function login_validate_input() {
    // Проверяем, что бы в начале и конце не было пустых символов
    // TODO - при копировании Эксель -> Опера - в конце образуются пустые места. Это не должно быть проблемой! Вынести проверку пароля в регистрацию!
    if($this->input_login_password_raw != trim($this->input_login_password_raw)) {
      throw new Exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    if(!$this->input_login_password_raw) {
      throw new Exception(LOGIN_ERROR_PASSWORD_EMPTY, ERR_ERROR);
    }
  }

  /**
   * Проверяет данные для регистрации на корректность
   *
   * @throws Exception
   */
  // OK v4.5
  protected function register_validate_input() {
    // То, что не подходит для логина - не подходит и для регистрации
    $this->login_validate_input();

    // Если нет имени пользователя - NO GO!
    if(!$this->input_login_unsafe) {
      throw new Exception(LOGIN_ERROR_USERNAME_EMPTY, ERR_ERROR);
    }
    // Если логин имеет запрещенные символы - NO GO!
    if(strpbrk($this->input_login_unsafe, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      throw new Exception(LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS, ERR_ERROR);
    }
    // Если логин меньше минимальной длины - NO GO!
    if(strlen($this->input_login_unsafe) < LOGIN_LENGTH_MIN) {
      throw new Exception(REGISTER_ERROR_USERNAME_SHORT, ERR_ERROR);
    }
    // Если пароль меньше минимальной длины - NO GO!
    if(strlen($this->input_login_password_raw) < PASSWORD_LENGTH_MIN) {
      throw new Exception(REGISTER_ERROR_PASSWORD_INSECURE, ERR_ERROR);
    }
    // Если пароль имеет пробельные символы в начале или конце - NO GO!
    if($this->input_login_password_raw != trim($this->input_login_password_raw)) {
      throw new Exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    // Если пароль не совпадает с подтверждением - NO GO! То, что у пароля нет пробельных символов в начале/конце - мы уже проверили выше
    //Если они есть у повтора - значит пароль и повтор не совпадут
    if($this->input_login_password_raw <> $this->input_login_password_raw_repeat) {
      throw new Exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
    }
    // Если нет емейла - NO GO!
    // TODO - регистрация без емейла
    if(!$this->input_email_unsafe) {
      throw new Exception(REGISTER_ERROR_EMAIL_EMPTY, ERR_ERROR);
    }
    // Если емейл не является емейлом - NO GO!
    if(!is_email($this->input_email_unsafe)) {
      throw new Exception(REGISTER_ERROR_EMAIL_WRONG, ERR_ERROR);
    }
  }



  // OK v4
  protected function password_encode_for_cookie($password) {
    return md5("{$password}--" . $this->secret_word);
  }
  // OK v4
  protected function password_encode($password, $salt) {
//    $class_name = $this->auth;
//    return $class_name::password_encode($password, $salt);
    return auth::password_encode($password, $salt);
  }
  // OK v4
  protected function password_salt_generate() {
//    $class_name = $this->auth;
//    return $class_name::password_salt_generate();
    return auth::password_salt_generate();
  }
  protected function flog($message, $die = false) {
    if(!defined('DEBUG_AUTH') || !DEBUG_AUTH) {
      return;
    }
    list($called, $caller) = debug_backtrace(false);

    $caller_name =
      ((get_called_class()) ? get_called_class() : (!empty($caller['class']) ? $caller['class'] : '')) .
      (!empty($caller['type']) ? $caller['type'] : '') .
      (!empty($caller['function']) ? $caller['function'] : '') .
      (!empty($called['line']) ? ':' . $called['line'] : '');

//     $real_caller_class = get_called_class();

    $_SERVER['SERVER_NAME'] == 'localhost' ? print("<div class='debug'>$message - $caller_name\r\n</div>") : false;

    classSupernova::log_file("$message - $caller_name");
    if($die) {
      // pdump($caller);
      // pdump(debug_backtrace(false));
      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
    }
  }



  // TODO - REMOVE UNUSED

  //  /**
//   * Возвращает аккаунт по имени
//   *
//   * @param $account_name_safe
//   *
//   * @return array|false
//   */
//  // OK v4.1
//  function db_account_get_by_name($account_name_unsafe) {
//    $account_name_safe = $this->db->db_escape($account_name_unsafe);
//    // $account = doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}'", true);
//    $account = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}'", true);
//    if(!empty($account)) {
//      $this->account_convert($account);
//    }
//
//    return $account;
//  }
//  /**
//   * Возвращает аккаунт по емейлу
//   *
//   * @param $email_unsafe
//   *
//   * @return array|bool|resource
//   */
//  // TODO NOT OK v4
//  // TODO - надо возвращать список аккаунтов
//  // TODO - DEPRECATED! Использовать db_account_list_get_on_email()
//  // OK v4.1
//  function db_account_by_email($email_unsafe) {
////    TODO auth_account
////    if($email_safe = db_escape(trim($email_unsafe))) {
////      $result = doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
//    if($email_safe = $this->db->db_escape(trim($email_unsafe))) {
//      $result = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
//    } else {
//      return false;
//    }
////    if($account = db_user_by_email($email_unsafe, true)) {
////      $this->db_account_convert($account);
////    }
//
//    return $result;
//  }
//  /**
//   * Создает аккаунт
//   *
//   * @throws Exception
//   */
//  // OK v4.1
//  function db_account_create($account_name_unsafe, $password_raw, $email_unsafe, $language_unsafe = null, $salt_unsafe = null) {
//    $account_name_safe = $this->db->db_escape($account_name_unsafe);
//    $email_safe = $this->db->db_escape($email_unsafe);
//    $language_safe = $this->db->db_escape($language_unsafe === null ? DEFAULT_LANG : $language_unsafe);
//
//    $salt_unsafe === null ? $salt_unsafe = $this->password_salt_generate() : false;
//    $password_salted_safe = $this->db->db_escape($this->password_encode($password_raw, $salt_unsafe));
//    $salt_safe = $this->db->db_escape($salt_unsafe);
//
//    $result = $this->db->doquery(
//      "INSERT INTO {{account}} SET
//        `account_name` = '{$account_name_safe}',
//        `account_password` = '{$password_salted_safe}',
//        `account_salt` = '{$salt_safe}',
//        `account_email` = '{$email_safe}',
//        `account_language` = '{$language_safe}'"
//    );
//    if(!$result) {
//      throw new Exception(REGISTER_ERROR_ACCOUNT_CREATE, ERR_ERROR);
//    }
//
//    if(!($account_id = $this->db->db_insert_id())) {
//      throw new Exception(REGISTER_ERROR_ACCOUNT_CREATE, ERR_ERROR);
//    }
//
//    return $account_id;
//  }
//  /**
//   * Возвращает аккаунт по его ID
//   *
//   * @param $account_id_unsafe
//   *
//   * @return array|false
//   */
//  // OK v4.1
//  protected function db_account_get_by_id($account_id_unsafe) {
//    $account_id_safe = round(floatval($account_id_unsafe));
//    // $account = doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe}", true);
//    $account = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe}", true);
//    !empty($account) ? $this->account_convert($account) : ($account = null);
//    return $account;
//  }
//  /**
//   * Физически меняет пароль аккаунта в БД
//   *
//   * @param $new_password_encoded_safe
//   * @param $salt_safe
//   *
//   * @return array|resource
//   */
//  // OK v4.1
//  protected function db_account_set_password_by_id($account_id_unsafe, $new_password_encoded_unsafe, $salt_unsafe) {
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
//  /**
//   * Проверки в БД на возможность регистрации
//   *
//   * @throws Exception
//   */
//  // OK v4.5
//  function account_check_duplicate_name_or_email($account_name_unsafe, $email_unsafe) {
//    // $account_name_safe = $this->db->db_escape($account_name_unsafe);
//
//    if($this->provider_account->db_get_by_name($account_name_unsafe)) {
//      throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
//    }
////    $account = $this->db->doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}' FOR UPDATE", true);
////    if(!empty($account)) {
////      throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
////    }
//
//
//    // TODO - добавить ограничение при регистрации ??
//    // Проверить - а вдруг чувак пытается зарегаться с тем же паролем?
//    // НЕ НАДО! Многие регаются с логином = паролю
////    if($this->db_account_by_name_safe($this->data[F_INPUT][F_LOGIN_UNSAFE])) {
////      throw new Exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
////    }
//
////    if($this->db_account_by_email($email_unsafe)) {
////      throw new Exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
////    }
//    if($this->provider_account->db_get_by_email($email_unsafe)) {
//      throw new Exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
//    }
//  }
//  /**
//   * Заполняет общие поля аккаунта из инфы, которую возвращает провайдер
//   *
//   * @param $account
//   */
//  // TODO DEPRECATED - это должен быть хелпер???? Или работать в геттере
//  // OK v4.1
//  protected function account_convert(&$account) {
//    $account = array(
//      'account_id' => $account['account_id'],
//      'account_name' => $account['account_name'],
//      'account_password' => $account['account_password'],
//      'account_salt' => $account['account_salt'],
//      'account_email' => $account['account_email'],
//      'account_email_verified' => $account['account_email_verified'],
//      'account_register_time' => $account['account_register_time'],
//      'account_language' => $account['account_language'],
//    );
//  }

}
