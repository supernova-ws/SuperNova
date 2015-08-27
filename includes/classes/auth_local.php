<?php

class auth_local extends sn_module {
  public $manifest = array(
    'package' => 'auth',
    'name' => 'basic',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a10.0# copyright © 2009-2015 Gorlum',

    // 'require' => array('auth_provider'),
    'root_relative' => '',

    'load_order' => 2,

    'installed' => true,
    'active' => true,

    'provider_id' => ACCOUNT_PROVIDER_LOCAL,
  );
  public $login_methods_supported = array(
    // 'login_cookie' => 'login_cookie',
    // 'login_username' => 'login_username',
    // 'register_username' => 'register_username',
  );
  protected $features = array(
    AUTH_FEATURE_EMAIL_CHANGE => AUTH_FEATURE_EMAIL_CHANGE,
    AUTH_FEATURE_PASSWORD_RESET => AUTH_FEATURE_PASSWORD_RESET,
  );

  // TODO - должны быть PRIVATE
  public $data = array();

  // TODO - private ??
  public $user;

  function is_feature_supported($feature) {
    return !empty($this->features[$feature]);
  }

  // abstract override
  function db_user_id_by_provider_account_id($accound_id_safe) {
    if(!$accound_id_safe) {
      self::flog('ERROR! No $accound_id_unsafe on db_user_id_by_provider_account() in ' . get_called_class(), true);
    }
//    $provider_id_safe = $this->manifest['provider_id'];
//    $result = doquery("SELECT * FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$account_id_safe}", true);
//    return !empty($result['user_id']) ? $result['user_id'] : false;
    return round(floatval($accound_id_safe));
  }
  function db_account_id_by_provider_user($user_id_safe) {
    // $provider_id_safe = $this->manifest['provider_id'];
    // auth->cookie_get_account()
    /*
        $result = doquery("SELECT * FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `user_id` = {$user_id_safe}", true);
        return !empty($result['provider_account_id']) ? $result['provider_account_id'] : false;
    */
    return $user_id_safe;
  }

  /**
   * Возвращает аккаунт по его ID
   *
   * @param $account_id_unsafe
   *
   * @return array|false
   */
  // OK v4
  function v4_db_get_account_by_id($account_id_unsafe) {
    $account_id_safe = round(floatval($account_id_unsafe));
    // $account = db_user_by_id($account_id_safe);
    $account = doquery("SELECT * FROM {{account}} WHERE `account_id` = {$account_id_safe}", true);
    !empty($account) ? $this->db_account_convert($account) : ($account = null);
    return $account;
  }

  /**
   * Возвращает аккаунт по имени
   *
   * @param $account_name_safe
   *
   * @return array|false
   */
  // OK v4
  // TODO - все данные для операций с БД должны приводится в безопасный вид ПО МЕСТУ! Движок всегда оперирует НЕБЕЗОПАСНЫМИ ДАННЫМИ!
  function db_account_by_name_safe($account_name_safe) {
    $account = doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$account_name_safe}'", true);
    if(!empty($account)) {
      $this->db_account_convert($account);
    }

    return $account;
  }
  function db_account_by_email($email_unsafe) {
//    TODO auth_account
//    if(empty($result) && ($email_safe = db_escape(trim($email_unsafe)))) {
//      $result = doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}';", true);
//    }
    if($account = db_user_by_email($email_unsafe, true)) {
      $this->db_account_convert($account);
    }

    return $account;
  }

  /**
   * Заполняет общие поля аккаунта из инфы, которую возвращает провайдер
   *
   * @param $account
   */
  // OK v4
  // TODO DEPRECATED
  function db_account_convert(&$account) {
    $account = array(
      'account_id' => $account['account_id'],
      'account_name' => $account['account_name'],
      'account_password' => $account['account_password'],
      'account_salt' => $account['account_salt'],
      'account_email' => $account['account_email'],
      'account_email_verified' => $account['account_email_verified'],
      'account_register_time' => $account['account_register_time'],
      'account_language' => $account['account_language'],
    );
  }
  function db_account_create() {
    /*
    if($result = db_field_set_create('account', array(
      'account_name' => $this->data[F_INPUT][F_LOGIN_UNSAFE],
      'account_password' => $this->data[F_INPUT][F_LOGIN_PASSWORD_RAW_TRIMMED],
      'account_email' => $this->data[F_INPUT][F_EMAIL_UNSAFE],
      'account_language' => $this->data[F_INPUT][F_LANGUAGE_SAFE],
    ))) {
      $this->data[F_ACCOUNT] = db_account_by_id(db_insert_id());
      $this->data[F_ACCOUNT] ? $this->register_account() : false;
      $this->data[F_ACCOUNT_ID] = !empty($this->data[F_ACCOUNT]['account_id']) ? $this->data[F_ACCOUNT]['account_id'] : 0;
    }
    */
    // Тут надо делать проверку на ИД существование емейла и имени нового аккаунта
    // И делать новое имя другим если есть дубликаты



    $this->data[F_ACCOUNT] = $this->data[F_USER];
    $this->db_account_convert($this->data[F_ACCOUNT]);
    $this->data[F_ACCOUNT_ID] = !empty($this->data[F_ACCOUNT]['account_id']) ? $this->data[F_ACCOUNT]['account_id'] : 0;
    // ТОЛЬКО ДЛЯ ЭТОГО МЕТОДА - ПОТОМУ ЧТО ОН НЕ ТРЕБУЕТ РЕГИСТРАЦИИ !!!
    $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;
  }

  /**
   * Создает пользователя по данным ввода
   *
   * @throws exception
   */
  // OK v4
  function db_account_create_from_input() {
    $salt = auth::password_salt_generate();
    $db_stored_password = auth::password_encode($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW], $salt);
    $email_safe = db_escape($this->data[F_INPUT][F_EMAIL_UNSAFE]);
    $result = doquery(
      "INSERT INTO {{account}} SET
        `account_name` = '{$this->data[F_INPUT][F_LOGIN_UNSAFE]}',
        `account_password` = '{$db_stored_password}',
        `account_salt` = '{$salt}',
        `account_email` = '{$email_safe}',
        `account_language` = '{$this->data[F_INPUT][F_LANGUAGE_SAFE]}'"
    );
    if(!$result) {
      throw new exception(REGISTER_ERROR_ACCOUNT_CREATE, ERR_ERROR);
    }
    $account_id = db_insert_id();
    $this->data[F_ACCOUNT] = $this->v4_db_get_account_by_id($account_id);


//    $this->auth_basic_user_create_from_input();
//    $this->db_account_convert($this->data[F_ACCOUNT]);
    $this->data[F_ACCOUNT_ID] = $this->data[F_ACCOUNT]['account_id'];
  }
  function db_account_password_change($new_password_encoded_safe, $salt_safe) {
    return db_user_set_by_id($this->data[F_ACCOUNT]['account_id'], "`password` = '{$new_password_encoded_safe}', `salt` = '{$salt_safe}'");
  }
  function email_set_do($new_email_unsafe) {
    return db_user_set_by_id($this->data[F_ACCOUNT_ID], "`email_2` = '" . db_escape($new_email_unsafe) . "'");
  }

  /**
   * Проверки в БД на возможность регистрации
   *
   * @throws exception
   */
  // OK v4
  function register_check_db() {
    $account = doquery("SELECT * FROM {{account}} WHERE `account_name` = '{$this->data[F_INPUT][F_LOGIN_SAFE]}' FOR UPDATE", true);
    if(!empty($account)) {
      throw new exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
    }
    // TODO - проверить - а вдруг чувак пытается зарегаться с тем же паролем?
//    if($this->db_account_by_name_safe($this->data[F_INPUT][F_LOGIN_UNSAFE])) {
//      throw new exception(REGISTER_ERROR_ACCOUNT_NAME_EXISTS, ERR_ERROR);
//    }
    if($this->db_account_by_email($this->data[F_INPUT][F_EMAIL_UNSAFE])) {
      throw new exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
    }
  }
  function db_confirmation_by_account_id($account_id_unsafe, $confirmation_type_safe, $email_unsafe = null) {
    $account_id_safe = round(floatval($account_id_unsafe));
    $email_safe = db_escape($email_unsafe);
    return doquery(
      "SELECT *
      FROM {{confirmations}}
      WHERE
        `provider_id` = {$this->manifest['provider_id']}
        AND `account_id`= {$account_id_safe}
        AND `type` = {$confirmation_type_safe} " .
        ($email_safe ? " AND `email` = '{$email_safe}' " : '') .
      " ORDER BY create_time DESC;", true
    );
  }
  function db_confirmation_set($account_id_safe, $confirmation_type_safe, $email_safe) {
    $confirmation = $this->db_confirmation_by_account_id($account_id_safe, CONFIRM_PASSWORD_RESET);
    if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
      throw new exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN);
    }

    // TODO - уникальный индекс по id_user и type - и делать не INSERT, а REPLACE
    // Удаляем предыдущие записи продтверждения сброса пароля
    !empty($confirmation['id']) or doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$confirmation['id']}' LIMIT 1;");

    do {
      // Ну, если у нас > 999.999.999 подтверждений - тут нас ждут проблемы...
      $confirm_code_safe = db_escape($confirm_code_unsafe = sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION));
      $query = doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = {$confirmation_type_safe}", true);
    } while($query);
    doquery(
      "REPLACE INTO {{confirmations}}
        SET `provider_id` = {$this->manifest['provider_id']}, `account_id` = '{$account_id_safe}',
        `type` = {$confirmation_type_safe}, `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");
    return $confirm_code_unsafe;
  }
  function db_confirmation_by_code($code_safe, $confirmation_type_safe) {
    return doquery(
      "SELECT *
      FROM {{confirmations}}
      WHERE
        `provider_id` = {$this->manifest['provider_id']}
        AND `code`= '{$code_safe}'
        AND `type` = {$confirmation_type_safe}
      ORDER BY create_time DESC", true
    );
  }

  // global functions
  /**
   * Функция инициализирует данные провайдера - разворачивает куки, берет данные итд
   */
  // OK v4
  public function prepare() {
    // $this->data = &$this->data[$this->manifest['provider_id']];
    !is_array($this->data) ? $this->data = array() : false;

    $this->data = array(
      F_PROVIDER_ID => $this->manifest['provider_id'],
      F_LOGIN_STATUS => LOGIN_UNDEFINED,
      F_IMPERSONATE_STATUS => LOGIN_UNDEFINED,
      F_IMPERSONATE_OPERATOR => null,
      F_INPUT => array(
        F_IS_REGISTER => $is_register = sys_get_param('register'),
        F_LOGIN_UNSAFE => sys_get_param_str_unsafe('username', sys_get_param_str_unsafe('login')), // TODO переделать эту порнографию
        F_LOGIN_PASSWORD_RAW => sys_get_param('password'),
        F_LOGIN_PASSWORD_REPEAT_RAW => trim(sys_get_param('password_repeat')),
        F_EMAIL_UNSAFE => sys_get_param_str_unsafe('email'),
        F_LANGUAGE_SAFE => sys_get_param_str('lang', DEFAULT_LANG),

        F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
        F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
        F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
      ),
      F_REMEMBER_ME_SAFE => intval(sys_get_param_int('rememberme') || $is_register),

      F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
      F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
      F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
    );
  }

  /**
   * Попытка залогиниться с использованием метода $method
   *
   * @param string $method_name
   */
  // OK v4
  public function login_try() {
    // TODO Проверяем поддерживаемость метода
    // TODO Пытаемся залогиниться
    $this->register();
    $this->login_username();
    $this->login_cookie();

//    switch($method_name) {
//      // TODO - сделать workflow без break;
//      case 'register':
//        $this->register();
//        break;
//      case 'login_username':
//        $this->login_username();
//        break;
//      case 'login_cookie':
//        $this->login_cookie();
//        break;
//
//    }

    return $this->data[F_LOGIN_STATUS];
  }


  /**
   * Логин по выставленным полям
   */
  public function login_internal() {
    try {
      $this->data[F_LOGIN_STATUS] = LOGIN_UNDEFINED;
      $this->data[F_REMEMBER_ME_SAFE] = true;
      $this->cookie_set();
      $this->login_cookie();
    } catch(exception $e) {
      // sn_db_transaction_rollback();
      $this->data[F_LOGIN_STATUS] == LOGIN_UNDEFINED ? $this->data[F_LOGIN_STATUS] = $e->getMessage() : false;
    }

    return $this->data[F_LOGIN_STATUS];
  }



  public function password_reset_for_email($email, $new_password) {

  }


  function load_user_data() {
    if(!$this->data[F_USER] && $this->data[F_USER_ID]) {
      $this->data[F_USER] = db_user_by_id($this->data[F_USER_ID]);
    }
    if(!$this->data[F_USER]) {
      $this->data[F_USER_ID] = $this->db_user_id_by_provider_account_id($this->data[F_ACCOUNT_ID]);
      $this->data[F_USER] = db_user_by_id($this->data[F_USER_ID]);
    }
    $this->data[F_USER_ID] = $this->data[F_USER] ? $this->data[F_USER]['id'] : 0;
  }

  function password_match_account_to_user($account, $user) {
    // TODO - если логин, то проверить еще и на энкод пароля с солью
    // if(self::password_encode($user['password'], $user['salt'])) {
    $match = false;
    if(!empty($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW])) {
      $match = self::password_encode($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW], $user['salt']) == $user['password'];
    }
    return $match || $account['account_password'] == $user['password'] && $account['account_salt'] == $user['salt'];
  }

  function user_create_from_account() {
    if(empty($this->data[F_ACCOUNT])) {
      self::flog('Не установлен аккаунт при создании пользователя из аккаунта', true);
    }
    sn_db_transaction_start();

    if($double_email = db_user_by_username($this->data[F_ACCOUNT]['account_email'])) { // Может быть дубликат по емейлу
      if($this->password_match_account_to_user($this->data[F_ACCOUNT], $double_email)) { // Проверить на совпадение пароля
        $this->data[F_USER] = $double_email; // Если пароль и емейл совпадают - мы нашли нашего пользователя
      } else { // TODO - Решить, что делать если емейл аккаунта, который заходит на сервер в первый раз совпадает с емейлом уже существующего игрока, но при этом не совпадают пароли
        $this->data[F_ACCOUNT]['account_email'] = '';
        self::flog('Защита от взлома. Емейл аккаунта совпадает с емейлом существующего пользователя, а пароль отличается. Обратитесь к Администрации', true);
      }
    }

    $user_name = $this->data[F_ACCOUNT]['account_name'];
    if(!$this->data[F_USER] && ($double_username = db_user_by_username($user_name))) { // Может быть дубликат по имени
      if($this->password_match_account_to_user($this->data[F_ACCOUNT], $double_username)) { // Проверить на совпадение пароля
        $this->data[F_USER] = $double_username; // Если подходит - это тот же пользователь
      } else { // Пароль не совпадает - сделать новый аккаунт
        do {
          $user_name .= mt_rand(0, 9);
          $double_username = db_user_by_username($user_name);
        } while($double_username);
      }
    }

    if(!$this->data[F_USER]) {
      $this->data[F_USER] = player_create($user_name, $this->data[F_ACCOUNT]['account_email'], array(
        'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
        'language_iso' => $this->data[F_ACCOUNT]['account_language'],
        'password_encoded_unsafe' => $this->data[F_ACCOUNT]['account_password'],
        'salt' => $this->data[F_ACCOUNT]['account_salt'],
        // 'remember_me' => $this->data[F_REMEMBER_ME_SAFE],
      ));
    }
    sn_db_transaction_commit();
  }
  function register_account() {
    if(empty($this->data[F_ACCOUNT_ID])) {
      self::flog('Не установлен F_ACCOUNT_ID при регистрации пользователя', true);
    }
    if(empty($this->data[F_USER_ID]) && empty($this->data[F_USER]['id'])) {
      self::flog('При регистрации пользователя должен быть установлен F_USER_ID или F_USER_ID[id]', true);
    }

    // Проверяем - если аккаунт уже зареган на пользователя, то регать заново его не надо
    if($this->data[F_USER]['id'] != $this->data[F_USER_ID]) {
      $this->data[F_USER_ID] = $this->data[F_USER]['id'];

      db_field_set_create('account_translate', array(
        'provider_id' => $this->manifest['provider_id'],
        'provider_account_id' => $this->data[F_ACCOUNT_ID],
        'user_id' => $this->data[F_USER_ID],
      ));
    }
    $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;
  }

  function password_match_account_to_account($account1, $account2) {
    // TODO - если логин, то проверить еще и на энкод пароля с солью
    $match = false;
    if(!empty($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW])) {
      $match = self::password_encode($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW], $account2['account_salt']) == $account2['account_password'];
    }

    return $match || ($account1['account_password'] == $account2['account_password'] && $account1['account_salt'] == $account2['account_salt']);
  }

  function db_create_account_from_provider($found_provider) {
    // $this->data[F_USER] = $found_provider->data[F_USER];
    if($this->data[F_ACCOUNT_ID]) {
      // $this->register_account();
      $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;
      return;
    }

    sn_db_transaction_start();
    if($double_email = $this->db_account_by_email($found_provider->data[F_ACCOUNT]['account_email'])) { // Может быть дубликат по емейлу, тогда
      // Проверить на совпадение пароля
      $email_password_match = $this->password_match_account_to_account($found_provider->data[F_ACCOUNT], $double_email);
      if($email_password_match) { // Если пароль и емейл совпадают - мы нашли нашего пользователя
        $this->data[F_ACCOUNT] = $double_email;
        $this->data[F_ACCOUNT_ID] = $this->data[F_ACCOUNT]['account_id'];
        $this->register_account(); // Дальше нас не интересует совпадение имени - ведь мы не будем создавать пользователя, а используем этого
      } else {
        // TODO - Решить, что делать если емейл аккаунта, который заходит на сервер в первый раз совпадает с емейлом уже существующего аккаунта, но при этом не совпадают пароли
        $this->data[F_ACCOUNT]['account_email'] = '';
        self::flog('Защита от взлома. Емейл глобального аккаунта совпадает с емейлом существующего локального аккаунта, а пароль отличается. Обратитесь к Администрации', true);
      }
    }

    $account_name = $found_provider->data[F_ACCOUNT]['account_name'];
    if(!$this->data[F_ACCOUNT_ID] && ($double_username = $this->db_account_by_name_safe($account_name))) { // Может быть дубликат по имени
      // Проверить на совпадение пароля
      $username_password_match = $this->password_match_account_to_account($found_provider->data[F_ACCOUNT], $double_username);
      if($username_password_match) { // Если подходит - это тот же пользователь
        $this->data[F_ACCOUNT] = $double_username;
        $this->data[F_ACCOUNT_ID] = $this->data[F_ACCOUNT]['account_id'];
      } else { // Пароль не совпадает - сделать новый аккаунт
        do {
          $account_name .= mt_rand(0, 9);
          $double_username = $this->db_account_by_name_safe($account_name);
        } while($double_username);
      }
    }

    if(!$this->data[F_ACCOUNT_ID]) {
      // Пробуем создать аккаунт с данными от провайдера
      $this->data[F_ACCOUNT] = $found_provider->data[F_ACCOUNT];
      $this->data[F_ACCOUNT]['account_name'] = $account_name;
      unset($this->data[F_ACCOUNT]['account_id']);
      $this->db_account_create();
      // ПРОВАЙДЕР МОЖЕТ НЕ ПОДДЕРЖИВАТЬ ТАКОЕ СОЗДАНИЕ АККУНТОВ! ЭТО НОРМАЛЬНО
      // НО ТОГДА ОН ДОЛЖЕН ВЫСТАВЛЯТЬ СТАТУС LOGIN_SUCCESS И ВСЁ РАВНО ПРИНИМАТЬ ПОЛЬЗОВАТЕЛЯ!
      $this->data[F_ACCOUNT_ID] = !empty($this->data[F_ACCOUNT]['account_id']) ? $this->data[F_ACCOUNT]['account_id'] : 0;
    }
    // Если получилось - делаем запись в таблице account translate
    $this->data[F_ACCOUNT_ID] ? $this->register_account() : false;
    sn_db_transaction_commit();
  }

  // OK v4
  function register_validate_input() {
    // То, что не подходит для логина - не подходит и для регистрации
    $this->login_validate_input();

    // Если нет имени пользователя - NO GO!
    if(!$this->data[F_INPUT][F_LOGIN_UNSAFE]) {
      throw new exception(LOGIN_ERROR_USERNAME_EMPTY, ERR_ERROR);
    }
    // Если логин имеет запрещенные символы - NO GO!
    if(strpbrk($this->data[F_INPUT][F_LOGIN_UNSAFE], LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      throw new exception(LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS, ERR_ERROR);
    }
    // Если логин меньше минимальной длины - NO GO!
    if(strlen($this->data[F_INPUT][F_LOGIN_UNSAFE]) < LOGIN_LENGTH_MIN) {
      throw new exception(REGISTER_ERROR_USERNAME_SHORT, ERR_ERROR);
    }
    // Если пароль меньше минимальной длины - NO GO!
    if(strlen($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW]) < PASSWORD_LENGTH_MIN) {
      throw new exception(REGISTER_ERROR_PASSWORD_INSECURE, ERR_ERROR);
    }
    // Если пароль имеет пробельные символы в начале или конце - NO GO!
    if($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW] != trim($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW])) {
      throw new exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    // Если пароль не совпадает с подтверждением - NO GO! То, что у пароля нет пробельных символов в начале/конце - мы уже проверили выше
    //Если они есть у повтора - значит пароль и повтор не совпадут
    if($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW] <> $this->data[F_INPUT][F_LOGIN_PASSWORD_REPEAT_RAW]) {
      throw new exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
    }
    // Если нет емейла - NO GO!
    // TODO - регистрация без емейла
    if(!$this->data[F_INPUT][F_EMAIL_UNSAFE]) {
      throw new exception(REGISTER_ERROR_EMAIL_EMPTY, ERR_ERROR);
    }
    // Если емейл не является емейлом - NO GO!
    if(!is_email($this->data[F_INPUT][F_EMAIL_UNSAFE])) {
      throw new exception(REGISTER_ERROR_EMAIL_WRONG, ERR_ERROR);
    }
  }

  /**
   * Пытается зарегестрировать пользователя по введенным данным
   *
   * @return mixed
   */
  // OK v4
  function register() {
    // TODO РЕГИСТРАЦИЯ ВСЕГДА ДОЛЖНА ЛОГИНИТЬ ПОЛЬЗОВАТЕЛЯ!
    self::flog('Регистрация: начинаем. Провайдер ' . $this->manifest['provider_id']);

    try {
      if(!$this->data[F_INPUT][F_IS_REGISTER]) {
        self::flog('Регистрация: не выставлен флаг регистрации - пропускаем');
        throw new exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->register_validate_input();

      sn_db_transaction_start();
      $this->register_check_db();
      $this->db_account_create_from_input();

      // Устанавливать не надо - мы дальше пойдем по workflow
      $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;
      $this->cookie_set();

      // А вот это пока не нужно. Трансляцией аккаунтов в юзеров и созданием новых юзеров для новозашедших аккаунтов занимается Auth
      // $this->register_account();
      sn_db_transaction_commit();
    } catch(exception $e) {
      sn_db_transaction_rollback();
      $this->data[F_LOGIN_STATUS] == LOGIN_UNDEFINED ? $this->data[F_LOGIN_STATUS] = $e->getMessage() : false;
    }

    return $this->data[F_LOGIN_STATUS];
  }


  function auth_password_check($old_password_unsafe) {
    unset($this->data[F_PASSWORD_MATCHED]);
    if($old_password_unsafe !== false) {
      if(!$this->data[F_ACCOUNT]['account_password'] ||
        self::password_encode($old_password_unsafe, $this->data[F_ACCOUNT]['account_salt']) != $this->data[F_ACCOUNT]['account_password']
      ) {
        return false;
      }
    }
    $this->data[F_PASSWORD_MATCHED] = $old_password_unsafe;

    return true;
  }
  function real_password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe) {
    if(!$this->auth_password_check($old_password_unsafe)) {
      return false;
    }

    $new_password_encoded_unsafe = self::password_encode($new_password_unsafe, $salt_unsafe);
    // Это здесь - потому что db_escape в общем случае может быть другая!
    $salt_safe = db_escape($salt_unsafe);
    $new_password_encoded_safe = db_escape($new_password_encoded_unsafe);
    $result = $this->db_account_password_change($new_password_encoded_safe, $salt_safe);

    if($result) {
      $this->data[F_ACCOUNT]['account_password'] = $new_password_encoded_unsafe;
      $this->data[F_ACCOUNT]['account_salt'] = $salt_unsafe;
      $this->cookie_set();
    }

    return $result;
  }

  /**
   * Физически меняется пароль в БД
   *
   * @param int $account_id_unsafe
   * @param string $new_password_unsafe
   * @param null $salt_unsafe
   *
   * @return boolean
   * @throws Exception
   */
  // OK v4
  function v4_db_password_set($account_id_unsafe, $new_password_unsafe, $salt_unsafe = null) {
    $salt_unsafe === null ? $salt_unsafe = static::password_salt_generate() : false;
    $account = $this->v4_db_get_account_by_id($account_id_unsafe);
    if(empty($account['account_id'])) {
      // Внутренняя ошибка. Такого быть не должно!
      throw new Exception(PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS, ERR_ERROR);
    }

    // Проверка на тот же пароль
    $salted_password_unsafe = static::password_encode($new_password_unsafe, $salt_unsafe);
    if($account['account_password'] == $new_password_unsafe && $account['account_salt'] == $salt_unsafe) {
      return $account;
    }

    $salted_password_safe = db_escape($salted_password_unsafe);
    $salt_safe = db_escape($salt_unsafe);
    $account_id_safe = db_escape($account_id_unsafe);

    $result = doquery("UPDATE {{account}} SET `account_password` = '{$salted_password_safe}', `account_salt` = '{$salt_safe}' WHERE `account_id` = '{$account_id_safe}'");
    if($result && db_affected_rows()) {
      // Меняем данные аккаунта
      $account = $this->v4_db_get_account_by_id($account_id_unsafe);
      $this->data[F_ACCOUNT] = $account;
      $result = true;
    } else {
      $result = false;
    }
    return $result;
  }

  // OK v4
  function password_salt_generate() {
    return auth::password_salt_generate();
  }


  function password_encode_for_cookie($password) {
    global $config;

    return md5("{$password}--{$config->secret_word}");
  }

  /**
   * Очищает куку аккаунта - совсем или восстанавливая куку текущего имперсонатора
   */
  // OK v4
  function cookie_clear($only_impersonator = null) {
    // Автоматически вообще-то - если установлена кука имперсонатора - то чистим обычную, а куку имперсонатора - копируем в неё
    if(!empty($_COOKIE[SN_COOKIE_I])) {
      sn_setcookie(SN_COOKIE, $_COOKIE[SN_COOKIE_I], SN_TIME_NOW + PERIOD_YEAR, SN_ROOT_RELATIVE);
      sn_setcookie(SN_COOKIE_I, '', SN_TIME_NOW - PERIOD_WEEK, SN_ROOT_RELATIVE);
    } else {
      sn_setcookie(SN_COOKIE, '', SN_TIME_NOW - PERIOD_WEEK, SN_ROOT_RELATIVE);
    }
  }

  /**
   * Получает аккаунт по данным куки
   *
   * @param bool|false $check_impersonator
   * @param bool|false $is_impersonator
   */
  // OK v4
  function cookie_get_account($check_impersonator = false, $is_impersonator = false) {
    $cookie = $_COOKIE[$check_impersonator ? SN_COOKIE_I : SN_COOKIE];

    if(empty($cookie)) {
      // Тут делать ничего не надо. Куки нет - и суда нет
      return;
    }

    if(count(explode("/%/", $cookie)) < 4) {
      list($REAL_account_id_unsafe_was_user, $cookie_password_hash_salted, $user_remember_me) = explode(AUTH_COOKIE_DELIMETER, $cookie);
    } else {
      list($REAL_account_id_unsafe_was_user, $user_name, $cookie_password_hash_salted, $user_remember_me) = explode("/%/", $cookie);
    }
    $this->data[F_REMEMBER_ME_SAFE] = intval($user_remember_me);

    // $REAL_account_id_safe_was_user = round(floatval($REAL_account_id_unsafe_was_user));
    $account = $this->v4_db_get_account_by_id($REAL_account_id_unsafe_was_user);

//    $account_id_unsafe = $this->db_account_id_by_provider_user($REAL_account_id_safe_was_user);
//    $account = $this->db_account_by_id($account_id_unsafe);

    if(!empty($account) && ($this->password_encode_for_cookie($account['account_password']) == $cookie_password_hash_salted)) {
      $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;

      $this->data[F_ACCOUNT] = $account;
      $this->data[F_ACCOUNT_ID] = $this->data[F_ACCOUNT]['account_id'];
    } else {
      // А ничего не делать. Здесь невалидная кука - может позже будет валидной
    }
  }
  function impersonate_check_cookie($that) {
    $this->data[F_IMPERSONATE_STATUS] = LOGIN_UNDEFINED;

    if(empty($_COOKIE[SN_COOKIE_I])) {
      return $this->data[F_IMPERSONATE_STATUS];
    }

    $this->cookie_get_account(true);
    $this->db_user_id_by_provider_account_id($this->data[F_ACCOUNT_ID]);
    $this->load_user_data();
    if(empty($this->data[F_USER]['authlevel']) || $this->data[F_USER]['authlevel'] < 3) {
      // Чистим куку имперсонатора
      $this->cookie_clear();
      // Чистим куку юзера
      $this->cookie_clear();
      // Переходим на страницу логина
      sys_redirect(SN_ROOT_VIRTUAL);
//      unset($this->data[F_ACCOUNT_ID]);
//      unset($this->data[F_ACCOUNT]);
//      unset($this->data[F_USER_ID]);
//      unset($this->data[F_USER]);
//      $this->data[F_LOGIN_STATUS] = LOGIN_UNDEFINED;
//      sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
//      sn_setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
    } else {
      $this->data[F_IMPERSONATE_STATUS] = IMPERSONATOR_OK;
    }

    $that->data[F_IMPERSONATE_STATUS] = $this->data[F_IMPERSONATE_STATUS];
    $that->data[F_IMPERSONATE_OPERATOR] = $this->data[F_USER];

    return $this->data[F_IMPERSONATE_STATUS];
  }

  /**
   * Пытается залогинить пользователя по куке
   *
   * @return int Результат попытки
   */
  // OK v4
  function login_cookie() {
    if($this->data[F_LOGIN_STATUS] != LOGIN_UNDEFINED) {
      return $this->data[F_LOGIN_STATUS];
    }

//    global $provider_impersonator;
//    $provider_impersonator = new auth_local();
//    $this->data[F_IMPERSONATE_STATUS] = $provider_impersonator->impersonate_check_cookie($this);
    // Пытаемся войти по куке
    if(empty($_COOKIE[SN_COOKIE])) {
      // Ошибка кукеса или не найден пользователь по кукесу
      $this->cookie_clear();
      // Если это был корректный имперсонатор - просто выходим и редиректимся в админку
      if(!empty($_COOKIE[SN_COOKIE])) {
        sys_redirect(SN_ROOT_VIRTUAL . 'admin/overview.php');
      }
    } else {
      $this->cookie_get_account();
    }

    return $this->data[F_LOGIN_STATUS];
  }

  /**
   * Проверяет введенные данные логина на корректность
   *
   * @throws exception
   */
  // OK v4
  function login_validate_input() {
    $this->data[F_INPUT][F_LOGIN_SAFE] = db_escape($this->data[F_INPUT][F_LOGIN_UNSAFE]);
    // Проверяем, что бы в начале и конце не было пустых символов
    // TODO - при копировании Эксель -> Опера - в конце образуются пустые места. Это не должно быть проблемой! Вынести проверку пароля в регистрацию!
    if($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW] != trim($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW])) {
      throw new exception(LOGIN_ERROR_PASSWORD_TRIMMED, ERR_ERROR);
    }
    if(!$this->data[F_INPUT][F_LOGIN_PASSWORD_RAW]) {
      throw new exception(LOGIN_ERROR_PASSWORD_EMPTY, ERR_ERROR);
    }
  }

  /**
   * Устанавливает куку аккаунта по данным $this->data[F_ACCOUNT]
   *
   * @return bool
   */
  // OK v4
  function cookie_set() {
    $expire_time = $this->data[F_REMEMBER_ME_SAFE] ? SN_TIME_NOW + PERIOD_YEAR : 0;

    $password_encoded = $this->password_encode_for_cookie($this->data[F_ACCOUNT]['account_password']);
    $cookie = $this->data[F_ACCOUNT]['account_id'] . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . $this->data[F_REMEMBER_ME_SAFE];
    self::flog("cookie_set() - Устанавливаем куку {$cookie}");
    return sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
  }


  /**
   * Пытается залогинить пользователя по имени аккаунта и паролю
   *
   * @return mixed
   */
  // OK v4
  function login_username() {
    // TODO - Логин по старым именам
    try {
      // TODO - в безбрейковом воркфлоу это не нужно
      if($this->data[F_INPUT][F_IS_REGISTER]) {
        self::flog('Логин: выставлен флаг регистрации - это не логин');
        throw new exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      if(!$this->data[F_INPUT][F_LOGIN_UNSAFE]) {
        throw new exception(LOGIN_UNDEFINED, ERR_ERROR);
      }

      $this->login_validate_input();

      $account = $this->db_account_by_name_safe($this->data[F_INPUT][F_LOGIN_SAFE]);
      if(empty($account)) {
        throw new exception(LOGIN_ERROR_USERNAME, ERR_ERROR);
      }

      // TODO Простой метод для проверки - сол-парол
      if($this->password_encode($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW], $account['account_salt']) != $account['account_password']) {
        throw new exception(LOGIN_ERROR_PASSWORD, ERR_ERROR);
      }

      $this->data[F_ACCOUNT] = $account;
      $this->data[F_ACCOUNT_ID] = $account['account_id'];

      $this->cookie_set();
      $this->data[F_LOGIN_STATUS] = LOGIN_SUCCESS;

//      $user_id = $this->db_user_id_by_provider_account_id($account['account_id']);
//      if(!$user_id) {
//        throw new exception(LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION, ERR_ERROR);
//      }
//
//      $user = db_user_by_id($user_id);
//      if(empty($user) || !empty($user['user_as_ally']) || $user['user_bot'] != USER_BOT_PLAYER) {
//        throw new exception(LOGIN_ERROR_USERNAME_ALLY_OR_BOT, ERR_ERROR);
//      }
//
//      $this->data[F_USER_ID] = $user_id;
//      $this->data[F_USER] = $user;

    } catch(exception $e) {
      // sn_db_transaction_rollback();
      $this->data[F_LOGIN_STATUS] == LOGIN_UNDEFINED ? $this->data[F_LOGIN_STATUS] = $e->getMessage() : false;
    }

    return $this->data[F_LOGIN_STATUS];
  }

  function logout_do() {
    $this->cookie_clear();
  }


  // Local functions
  function auth_basic_user_create_from_input() {
    $this->data[F_USER] = player_create($this->data[F_INPUT][F_LOGIN_UNSAFE], $this->data[F_INPUT][F_EMAIL_UNSAFE],
      array(
        'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
        'language_iso' => $this->data[F_ACCOUNT]['account_language'],
        'salt' => $salt = auth::password_salt_generate(),
        'password_encoded_unsafe' => self::password_encode($this->data[F_INPUT][F_LOGIN_PASSWORD_RAW], $salt),

        // 'remember_me' => $this->data[F_REMEMBER_ME_SAFE],
      )
    );
    $this->data[F_USER_ID] = $this->data[F_USER]['id'];
  }

  static function flog($message, $die = false) {
//    list($called, $caller) = debug_backtrace(false);
//    $caller_name =
//      (!empty($caller['class']) ? $caller['class'] : '') .
//      (!empty($caller['type']) ? $caller['type'] : '') .
//      (!empty($caller['function']) ? $caller['function'] : '') .
//      (!empty($called['line']) ? ':' . $called['line'] : '');
//
//    // $_SERVER['SERVER_NAME'] == 'localhost' ? print("<div class='debug'>$message - $caller_name\r\n</div>") : false;
//
//    // classSupernova::log_file("$message - $caller_name");
//    if($die) {
//      // $die && die("Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . ")");
//      // pdump($caller);
//      // pdump(debug_backtrace(false));
//      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
//    }
  }

  /**
   * Функция предлогает имя игрока (`users`) по данным аккаунта
   *
   * @return string
   */
  public function suggest_player_name() {
    return !empty($this->data[F_ACCOUNT]['account_name']) ? $this->data[F_ACCOUNT]['account_name'] : '';
  }

//  /**
//   * Функция возвращает емейл аккаунта
//   *
//   * @return string
//   */
//  public function get_email() {
//    return !empty($this->data[F_ACCOUNT]['account_email']) ? $this->data[F_ACCOUNT]['account_email'] : '';
//  }

  /**
   * Возвращает список аккаунтов, которые привязаны к указанному емейлу
   *
   * @param $email_unsafe
   *
   * @return array
   */
  public function db_get_accounts_on_email($email_unsafe) {
    $email_safe = db_escape($email_unsafe);
    $query = doquery("SELECT * FROM {{account}} WHERE `account_email` = '{$email_safe}' FOR UPDATE;");
    $account_list = array();
    while($row = db_fetch($query)) {
      $account_list[$row['account_id']] = $row;
    }
    return $account_list;
  }


  function password_encode($password, $salt) {
    return auth::password_encode($password, $salt);
  }
}
