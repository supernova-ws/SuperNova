<?php

define("DEBUG_AUTH", true);

/**
 * Статический над-класс, который обеспечивает интерфейс авторизации для остального кода
 *
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:51
 *
 * version #40a10.13#
 */

class auth extends sn_module {
  public $manifest = array(
    'package' => 'core',
    'name' => 'auth',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a10.13# copyright © 2009-2015 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'load_order' => 1,

    'installed' => true,
    'active' => true,
  );

  /**
   * Информация об устройстве
   *
   * @var RequestInfo
   */
  static $device;

  /**
   * Статус инициализации
   *
   * @var bool
   */
  protected static $is_init = false;
  /**
   * Список провайдеров
   *
   * @var auth_local[]
   */
  protected static $providers = array();

  // Скрытые данные - общие для всех аккаунтов
  // TODO - ХУЕТА! ИЗБАВИТЬСЯ!
  protected static $hidden = array(
    F_LOGIN_STATUS => LOGIN_UNDEFINED,
  );

  /**
   * Глобальный статус входа в игру
   *
   * @var int
   */
  static $login_status = LOGIN_UNDEFINED;
  /**
   * Имя, предлагаемое пользователю в качестве имени игрока
   *
   * @var string
   */
  static $player_suggested_name = '';

  /**
   * Аккаунт ????
   *
   * @var null|auth_local
   */
  static $account = null;
  /**
   * Запись текущего игрока из `users`
   *
   * @var null
   */
  static $user = null;
  /**
   * Список полностью авторизированных аккаунтов с LOGIN_SUCCESS
   *
   * @var auth_local[]
   */
  static $providers_authorised = array();
  /**
   * Статусы всех провайдеров
   *
   * @var auth_local[]
   */
  static $account_error_list = array();

  /**
   * Список юзеров (user_row - записей из `user`), доступных всем авторизированным аккаунтам
   *
   * @var array
   */
  static $accessible_user_row_list = array();

  /**
   * Флаг имперсонации
   *
   * @var bool
   */
  static $is_impersonating = false;


  // TODO - Это, наверное, всё-таки один и тот же флаг
  /**
   * Флаг запроса кода на сброс пароля
   *
   * @var bool
   */
  static $is_password_reset = false;
  /**
   * Флаг ввода кода для сброса пароля
   *
   * @var bool
   */
  static $is_password_reset_confirm = false;



  /**
   * Функция проверяет наличие имени игрока в базе
   *
   * @param $player_name_unsafe
   *
   * @throws Exception
   */
  // OK v4
  public static function db_player_name_check_existence($player_name_unsafe) {
    sn_db_transaction_check(true);
    $player_name_safe = db_escape($player_name_unsafe);
    $player_name_exists = doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` = '{$player_name_safe}' LIMIT 1 FOR UPDATE", true);
    if(!empty($player_name_exists)) {
      throw new Exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);
    }
  }
  // OK v4
  static function db_player_get_max_id() {
    $max_user_id = doquery("SELECT MAX(`id`) as `max_user_id` FROM `{{user}}`", true);
    return !empty($max_user_id['max_user_id']) ? $max_user_id['max_user_id'] : 0;
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
  // OK v4
  static function db_translate_register_user($provider_id_safe, $provider_account_id_safe, $user_id_safe) {
    return doquery(
      "INSERT INTO `{{account_translate}}` (`provider_id`, `provider_account_id`, `user_id`) VALUES
                  ({$provider_id_safe}, {$provider_account_id_safe}, {$user_id_safe});"
    );
  }
  /**
   * Возвращает из `account_translate` список пользователей, которые прилинкованы к списку аккаунтов на указанном провайдере
   *
   * @param int $provider_id_unsafe Идентификатор провайдера авторизации
   * @param int|int[] $account_list
   *
   * @return array
   */
  // OK v4
  // TODO - возможно, это должен делать провайдер - иметь свой транслятор. Наверное - кэширующий
  static function db_get_account_translation_from_account_list($provider_id_unsafe, $account_list) {
    $provider_id_safe = intval($provider_id_unsafe);
    !is_array($account_list) ? $account_list = array($account_list) : false;
    $account_translation = array();

    foreach($account_list as $provider_account_id_unsafe) {
      $provider_account_id_safe = intval($provider_account_id_unsafe);

      $query = doquery("SELECT `user_id` FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$provider_account_id_safe} FOR UPDATE");
      while($row = db_fetch($query)) {
        $account_translation[$row['user_id']][$provider_id_safe] = $row;
      }
    }

    return $account_translation;
  }



  static function db_confirmation_get_latest_by_email_and_type($email_safe, $confirmation_type_safe) {
    return doquery(
      "SELECT * FROM {{confirmations}} WHERE
          `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}' ORDER BY create_time DESC LIMIT 1;", true);
  }
  static function db_confirmation_delete_by_email_and_type($email_safe, $confirmation_type_safe) {
    return doquery("DELETE FROM {{confirmations}} WHERE `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}';");
  }
  static function db_confirmation_get_unique_code_by_type_on_email($confirmation_type_safe, $email_safe) {
    do {
      // Ну, если у нас > 999.999.999 подтверждений - тут нас ждут проблемы...
      $confirm_code_safe = db_escape($confirm_code_unsafe = self::make_password_reset_code());
      $query = doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = {$confirmation_type_safe} FOR UPDATE", true);
    } while($query);

    doquery(
      "REPLACE INTO {{confirmations}}
        SET `type` = {$confirmation_type_safe}, `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");
  }
  static function db_confirmation_get_by_type_and_code($confirmation_type_safe, $confirmation_code_safe) {
    return doquery(
      "SELECT * FROM {{confirmations}} WHERE
          `type` = {$confirmation_type_safe} AND `code` = '{$confirmation_code_safe}' ORDER BY create_time DESC LIMIT 1;", true);
  }
  static function db_confirmation_delete_by_type_and_email($confirmation_type_safe, $email_safe) {
    return doquery("DELETE FROM {{confirmations}} WHERE `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}'");
  }



  static function db_security_entry_insert() {
    $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;

    return doquery(
      "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
        VALUES ({$user_id}," . self::$device->device_id . "," . self::$device->browser_id . "," . self::$device->ip_v4_int . ", '" . db_escape(self::$device->ip_v4_proxy_chain) . "');"
    );
  }



  static function db_counter_insert() {
    global $config, $sys_stop_log_hit, $is_watching;

    if(!$sys_stop_log_hit && $config->game_counter) {
      $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;
      $proxy_safe = db_escape(self::$device->ip_v4_proxy_chain);

      $is_watching = true;
      doquery(
        "INSERT INTO {{counter}}
          (`visit_time`, `user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`, `page_url_id`, `plain_url_id`)
        VALUES
          ('" . SN_TIME_SQL. "', {$user_id}, " . self::$device->device_id . "," . self::$device->browser_id . ", " .
        self::$device->ip_v4_int . ", '{$proxy_safe}', " . self::$device->page_address_id . ", " . self::$device->page_url_id . ");");
      $is_watching = false;
    }
  }



  /**
   * Статическая инициализация
   */
  // OK v4
  public static function init() {
    global $sn_module_list;

    // В этой точке все модули уже прогружены и инициализированы по 1 экземпляру
    if(self::$is_init) {
      return;
    }

    if(empty($sn_module_list['auth'])) {
      die('{Не обнаружено ни одного провайдера авторизации!}');
    }

    // self::init_device_and_browser();
    self::$device = new RequestInfo();
    self::$is_password_reset = sys_get_param('password_reset') ? true : false;
    self::$is_password_reset_confirm = sys_get_param('password_reset_confirm') ? true : false;

    self::$providers = array();
    foreach($sn_module_list['auth'] as $module_name => $module) {
      if($module_name != 'auth_provider') {
        // Провайдеры подготавливают для себя данные
        self::$providers[$module->manifest['provider_id']] = $module;
        // self::$providers[$module->manifest['provider_id']]->auth_manager_set(get_called_class());
        self::$providers[$module->manifest['provider_id']]->prepare();
      }
    }
    self::$providers = array_reverse(self::$providers, true);


//    self::$hidden += array(
//      F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
//    );

    self::$is_init = true;
  }

  /**
   * Функция пытается создать игрока в БД, делая все нужные проверки
   *
   * @param $player_name_unsafe
   */
  // OK v4
  public static function register_player_db_create($player_name_unsafe) {
    try {
      // Проверить корректность имени
      self::register_player_name_validate($player_name_unsafe);

      sn_db_transaction_start();
      // Проверить наличие такого имени в истории имён
      self::db_player_name_check_existence($player_name_unsafe);

      // Узнаем язык и емейл игрока
      $player_language = '';
      $player_email = '';
      // TODO - порнография - работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
      foreach(self::$providers_authorised as $provider) {
        if(!$player_language && $provider->data[F_ACCOUNT]['account_language']) {
          $player_language = $provider->data[F_ACCOUNT]['account_language'];
        }
        if(!$player_email && $provider->data[F_ACCOUNT]['account_email']) {
          $player_email = $provider->data[F_ACCOUNT]['account_email'];
        }
      }
      $player_language = sys_get_param_str('lang') ? sys_get_param_str('lang') : $player_language;
      $player_language = $player_language ? $player_language : DEFAULT_LANG;

      // TODO - дописать эксепшнов в процедуре создания игрока
      self::$user = player_create($player_name_unsafe, $player_email, array(
        'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
        'language_iso' => db_escape($player_language),
        // 'password_encoded_unsafe' => $this->data[F_ACCOUNT]['account_password'],
        // 'salt' => $this->data[F_ACCOUNT]['account_salt'],
      ));
      // Зарегестрировать на него аккаунты из self::$accounts_authorised
      $a_user = self::$user;
      foreach(self::$providers_authorised as $provider) {
        // TODO - порнография. Должен быть отдельный класс трансляторов - в т.ч. и кэширующий транслятор
        // TODO - ну и работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
        self::db_translate_register_user($provider->manifest['provider_id'], $provider->data[F_ACCOUNT]['account_id'], $a_user['id']);

      }
      // Установить куку игрока
      sn_setcookie(SN_COOKIE_U, self::$user['id'], SN_TIME_NOW + PERIOD_YEAR);

      sn_db_transaction_commit();
      self::$login_status = LOGIN_SUCCESS;
    } catch(Exception $e) {
      sn_db_transaction_rollback();

      // Если старое имя занято
      unset(self::$user);
//      self::$player_suggested_name = $player_name_unsafe;
//      $login_status = LOGIN_SUCCESS;
      self::$login_status == LOGIN_UNDEFINED ? self::$login_status = $e->getMessage() : false;
    }
  }

  /**
   * Функция управляет регистрацией нового игрока для существующих аккаунтов
   */
  // OK v4
  public static function register_player() {
    // Есть хотя бы один удачно залогинившийся аккаунт. Но у него/них нету ни одного связанного аккаунта

    // TODO Сначала пробовать зарегестрировать игрока с тем же именем - что бы избежать лишнего шага
    // TODO в auth_local делать проверку БД на существование имени игрока в локальной БД - что бы избежать лишнего шага (см.выше)
    // TODO Хотя тут может получится вечный цикл - ПОДУМАТЬ
    // TODO Тут же можно пробовать провести попытку слияния аккаунтов - хотя это и очень небезопасно


    // Смотрим - есть ли у нас данные от пользователя
    if(($player_name_submitted = sys_get_param_int('submit_player_name'))) {
      // Попытка регистрации нового игрока из данных, введенных пользователем
      self::$player_suggested_name = sys_get_param_str_unsafe('player_name');
    } else {
      foreach(self::$providers_authorised as $provider) {
        if(self::$player_suggested_name = $provider->suggest_player_name()) {
          break;
        }
      }
    }

    // Если у нас провайдеры не дают имени и пользователь не дал свой вариант - это у нас первый логин в игру
    if(!self::$player_suggested_name) {
      $max_user_id = self::db_player_get_max_id();
      $max_user_id = $max_user_id['max_user_id'];
      // TODO - предлагать имя игрока по локали
      self::$player_suggested_name = 'Emperor ' . mt_rand($max_user_id + 1, $max_user_id + 1000);
    } else {
      self::register_player_db_create(self::$player_suggested_name);
//      if(self::$login_status != LOGIN_SUCCESS) {
//        // TODO Ошибка при регистрации нового игрока под текущим именем
//      }
    }
  }


  /**
   * Функция пытается залогиниться по всем известным провайдерам
   *
   * @param null $result
   */
  // OK v4
  public static function login(&$result = null) {
    !self::$is_init ? self::init() : false;

    self::$login_status = LOGIN_UNDEFINED;
    self::$player_suggested_name = '';
    self::$account = null;
    self::$user = null;
    self::$providers_authorised = array(); // Все аккаунты, которые успешно залогинились
    self::$account_error_list = array(); // Статусы всех аккаунтов
    self::$accessible_user_row_list = array();

    self::flog('starting sequence');
    self::flog(dump($_POST, '$_POST'));
    self::flog(dump($_GET, '$_GET'));
    self::flog(dump($_COOKIE,'$_COOKIE'));

    global $lang, $config;

    // Максимальный уровень авторизации всех записей игроков, к которым аккаунты имеют доступ
    $local_max_auth_level = AUTH_LEVEL_ANONYMOUS;
    $local_user_to_provider_list = array(); // Локальная переменная

    foreach(self::$providers as $provider_id => $provider) {
      self::flog(($provider->manifest['name'] . '->' . 'login_try') . (empty($provider->data[F_ACCOUNT]['account_id']) ? $lang['sys_login_messages'][$provider->data[F_LOGIN_STATUS]] : dump($provider->data)));
      $login_status = $provider->login_try();
      if($login_status == LOGIN_SUCCESS && $provider->data[F_ACCOUNT]['account_id']) {
        self::$providers_authorised[$provider_id] = &self::$providers[$provider_id];

        // TODO - ХУИТА! Тут должен быть список
        $local_user_to_provider_list = array_replace_recursive(
          $local_user_to_provider_list,
          // static::db_translate_get_user_list_on_provider($provider_id, $provider->data[F_ACCOUNT]['account_id'])
          static::db_get_account_translation_from_account_list($provider_id, $provider->data[F_ACCOUNT]['account_id'])
        );
      } elseif($login_status != LOGIN_UNDEFINED) {
        self::$account_error_list[$provider_id] = $login_status;
      }
    }


    if(empty(self::$providers_authorised)) {
      // Ни один аккаунт не авторизирован
      // Проверяем - есть ли у нас ошибки в аккаунтах?
      if(!empty(self::$account_error_list)) {
        // Если есть - выводим их
        self::$login_status = reset(self::$account_error_list);
      } else {
        // Иначе - это первый запуск страницы. ИЛИ СПЕЦИАЛЬНОЕ ДЕЙСТВИЕ!
        self::password_reset_send_code();
        self::password_reset_confirm(); // Если успешно - сюда мы не вернемся, а уйдём на редирект
      }
      // - значит у нас ошибка как минимум в локальном аккаунте - а то и в каком-то другом

      // TODO - если в локальном аккаунте нет никаких данных - значит он должен возвращать LOGIN_UNDEFINED
      // TODO - надо ли что-то делать?
    } else {
//pdump($local_user_to_provider_list, '$user_list');
      if(!empty($local_user_to_provider_list)) {
        // $user_list не пустой

        // Пробиваем все ИД игроков по базе - есть ли вообще такие записи. Вообще-то это не особо нужно - у нас по определению стоят констраинты
        // Зато так мы узнаем максимальный authlevel и проверим права имперсонейта
        foreach($local_user_to_provider_list as $user_id => $cork) {
          $user = db_user_by_id($user_id);
          // Если записи игрока в БД не существует?
          if(empty($user['id'])) {
            // Удаляем этого и переходим к следующему
            unset($local_user_to_provider_list[$user_id]);
          } else {
            self::$accessible_user_row_list[$user['id']] = $user;
            $local_max_auth_level = max($local_max_auth_level, $user['authlevel']);
          }
        }
        unset($user);
      }

//pdump(self::$accessible_user_row_list,'self::$accessible_user_row_list');
      // Остались ли у нас в списке доступные игроки?
      if(!empty(self::$accessible_user_row_list)) {
        // Да, есть доступные игроки, которые так же прописаны в базе

        // Проверяем куку "текущего игрока" из браузера
        if(
          // Кука не пустая
          ($_COOKIE[SN_COOKIE_U] = trim($_COOKIE[SN_COOKIE_U])) && !empty($_COOKIE[SN_COOKIE_U])
          // И в куке находится ID
          && is_id($_COOKIE[SN_COOKIE_U])
          // И у аккаунтов есть права на данного игрока
          && (
            // Есть прямые права из `account_translate`
            !empty(self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]])
            // Или есть доступ через имперсонейт
            || (
              // Максимальные права всех доступных записей игроков - не ниже администраторских
              $local_max_auth_level >= AUTH_LEVEL_ADMINISTRATOR
              // И права игрока, в которого пытаются зайти - меньше текущих максимальных прав
              && self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]['authlevel'] < $local_max_auth_level
            )
          )
        ) {
          // Берем текущим юзером юзера с ИД из куки
          self::$is_impersonating = empty(self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]);
          // self::$user = self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]];
          self::$user = db_user_by_id($_COOKIE[SN_COOKIE_U]);
        }

        // В куке нет валидного ИД записи игрока, доступной с текущих аккаунтов
        if(empty(self::$user['id'])) {
          // Берем первого из доступных
          // TODO - default_user
          self::$user = reset(self::$accessible_user_row_list);
        }
        // Тут ВСЕГДА есть self::$user
      } else {
        // Нет ни одного игрока ни на одном авторизированном аккаунте
        // Надо регать нового игрока
        self::register_player();
        // Либо есть self::$user, либо идем на новый круг авторизации
      }
      // IF self::$user
      // ELSE Либо есть self::$user, либо идем на новый круг авторизации
    }
    // IF ????????
    // ELSE Либо есть self::$user, либо идем на новый круг авторизации

    // die('Before set cookie');

    self::make_return_array();

    return $result = self::$hidden;
  }

  /**
   * Логаут игрока и всех аккаунтов
   *
   * @param bool|string $redirect нужно ли сделать перенаправление после логаута
   * <p><b>false</b> - не перенаправлять</p>
   * <p><i><b>true</b></i> - перенаправить на главную страницу</p>
   * <p><b>string</b> - перенаправить на указанный URL</p>
   *
   * @param bool $only_impersonator Если установлен - то логаут происходит только при имперсонации
   */
  // OK v4
  static function logout($redirect = true) {
    foreach(self::$providers as $provider_name => $provider) {
      $provider->logout_do();
    }

    sn_setcookie(SN_COOKIE_U, '', SN_TIME_NOW - PERIOD_YEAR);

    if($redirect === true) {
      sys_redirect(SN_ROOT_RELATIVE . (empty($_COOKIE[SN_COOKIE]) ? 'login.php' : 'admin/overview.php'));
    } elseif($redirect !== false) {
      sys_redirect($redirect);
    }
  }

  public static function impersonate($user_selected) {
    if($_COOKIE[SN_COOKIE_I]) {
      die('You already impersonating someone. Go back to living other\'s life! Or clear your cookies and try again'); // TODO: Log it
    }

    if(self::$hidden[AUTH_LEVEL] < 3) {
      die('You can\'t impersonate - too low level'); // TODO: Log it
    }

    if(self::$hidden[AUTH_LEVEL] <= $user_selected['authlevel']) {
      die('You can\'t impersonate this account - level is greater or equal to yours'); // TODO: Log it
    }

    sn_setcookie(SN_COOKIE_I, $_COOKIE[SN_COOKIE], 0, SN_ROOT_RELATIVE);

    $expire_time = SN_TIME_NOW + PERIOD_YEAR; // TODO - Имперсонейт - только на одну сессию
    $password_encoded = md5("{$user_selected['password']}--" . classSupernova::$sn_secret_word);
    $cookie = $user_selected['id'] . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . '1';
    sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);

    // sec_set_cookie_by_user($user_selected, 0);
    sys_redirect(SN_ROOT_RELATIVE);
  }

  /**
   * Устанавливает емейл на аккаунтах
   *
   * @param $new_email_unsafe
   */
  // TODO v4
  static function email_set($new_email_unsafe) {
    // TODO - результаты работы
    // TODO - верфикация емейла
    foreach(self::$providers_authorised as $provider) {
      if($provider->is_feature_supported(AUTH_FEATURE_EMAIL_CHANGE)) {
        $provider->db_account_set_email($new_email_unsafe);
      }
    }
  }

  /**
   * Проверяет пароль на совпадение с текущими паролями
   *
   * @param $old_password_unsafe
   *
   * @return bool
   */
  // OK v4
  static function password_check($old_password_unsafe) {
    $result = false;

    if(empty(self::$providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("password_check: Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
      return false;
    }

    foreach(self::$providers_authorised as $provider_id => $provider) {
      if($provider->is_feature_supported(AUTH_FEATURE_HAS_PASSWORD)) {
        $result = $result || $provider->auth_password_check($old_password_unsafe);
      }
    }

    return $result;
  }

  /**
   * Меняет старый пароль на новый
   *
   * @param $old_password_unsafe
   * @param $new_password_unsafe
   *
   * @return bool
   */
  // OK v4
  static function password_change($old_password_unsafe, $new_password_unsafe) {
    global $lang;

    if(empty(self::$providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
      return false;
    }

    // TODO - Проверять пароль на корректность

    $salt_unsafe = self::password_salt_generate();
    $providers_changed_password = self::$providers_authorised;
    foreach($providers_changed_password as $provider_id => $provider) {
      $provider_result = false;
      if($provider->is_feature_supported(AUTH_FEATURE_PASSWORD_CHANGE)) {
        $provider_result = $provider->real_password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
      }
      if($provider_result) {
        $account_translation = self::db_get_account_translation_from_account_list($provider_id, $provider->data[F_ACCOUNT]['account_id']);
        foreach($account_translation as $user_id) {
          msg_send_simple_message($user_id, 0, SN_TIME_NOW, MSG_TYPE_ADMIN,
            $lang['sys_administration'], $lang['sys_login_register_message_title'],
            sprintf($lang['sys_login_register_message_body'], $provider->data[F_ACCOUNT]['account_name'], $new_password_unsafe), false //true
          );
        }
      } else {
        unset($providers_changed_password[$provider_id]);
      }
    }

    return !empty($providers_changed_password);
  }




  /**
   * Отсылает письмо с кодом подтверждения для сброса пароля
   *
   * @return int|string
   */
  // OK v4
  static function password_reset_send_code() {
    global $lang, $config;

    if(!self::$is_password_reset) {
      return self::$login_status = LOGIN_UNDEFINED;
    }

    $account_translation = array();
    $email_unsafe = sys_get_param_str_unsafe('email');

    $providers_will_reset = array();
    foreach(self::$providers as $provider_id => $provider) {
      // Проверяем поддержку сброса пароля
      if(!$provider->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
        continue;
      }
      $account_list = $provider->db_account_list_get_on_email($email_unsafe);
      if(!empty($account_list)) {
        $this_provider_translation = self::db_get_account_translation_from_account_list($provider_id, array_keys($account_list));
        if(!empty($this_provider_translation)) {
          $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
          $providers_will_reset[$provider_id] = $provider;
        }
      }
      // $password_reset_providers
//      // if(method_exists($provider, $method_name))
//      if($provider->data[F_INPUT][F_EMAIL_UNSAFE] && $provider->data[F_INPUT][F_IS_PASSWORD_RESET]) {
//        $account = $provider->db_account_by_email($provider->data[F_INPUT][F_EMAIL_UNSAFE]);
//        if($account) {
//          $user = $provider->db_user_id_by_provider_account_id($account['account_id']);
//
//          if($user && $user['authlevel'] > 0) {
//            throw new Exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
//          }
//          $found_provider = $provider;
//          break;
//        }
//      }
    }

    try {
      $user_list = db_user_list_by_id(array_keys($account_translation));
      if(empty($user_list)) {
        throw new Exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL, ERR_ERROR);
      }

      // TODO - Проверять уровень доступа аккаунта!
      // Аккаунты с АУТЛЕВЕЛ больше 0 - НЕ СБРАСЫВАЮТ ПАРОЛИ!
      foreach($user_list as $user_id => $user_data) {
        if($user_data['authlevel'] > AUTH_LEVEL_REGISTERED) {
          throw new Exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT, ERR_ERROR);
        }
      }

      $email_safe = db_escape($email_unsafe);

      $confirmation = static::db_confirmation_get_latest_by_email_and_type($email_safe, CONFIRM_PASSWORD_RESET);
      if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
        throw new Exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN, ERR_ERROR);
      }

      // Удаляем предыдущие записи продтверждения сброса пароля
      !empty($confirmation['id']) or static::db_confirmation_delete_by_email_and_type($email_safe, CONFIRM_PASSWORD_RESET);

      sn_db_transaction_start();
      $confirm_code_unsafe = self::db_confirmation_get_unique_code_by_type_on_email(CONFIRM_PASSWORD_RESET, $email_safe);
      sn_db_transaction_commit();

      @$result = mymail($email_unsafe,
        sprintf($lang['log_lost_email_title'], $config->game_name),
        sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . 'login.php', $confirm_code_unsafe, date(FMT_DATE_TIME, SN_TIME_NOW + AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE), $config->game_name)
      );

      $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
    } catch(Exception $e) {
      sn_db_transaction_rollback();
      $result = $e->getMessage();
    }

    return self::$login_status = $result;
  }


  /**
   * Сброс пароля по введенному коду подтверждения
   *
   * @return int|string
   */
  // OK v4
  static function password_reset_confirm() {
    global $lang, $config;

    if(!self::$is_password_reset_confirm) {
      return LOGIN_UNDEFINED;
    }

    $code_safe = sys_get_param_str('password_reset_code');

    try {
      sn_db_transaction_start();
      $confirmation = self::db_confirmation_get_by_type_and_code(CONFIRM_PASSWORD_RESET, $code_safe);

      if(empty($confirmation)) {
        throw new Exception(PASSWORD_RESET_ERROR_CODE_WRONG, ERR_ERROR);
      }

      if(SN_TIME_NOW - strtotime($confirmation['create_time']) > AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE) {
        throw new Exception(PASSWORD_RESET_ERROR_CODE_TOO_OLD, ERR_ERROR);
      }

      $account_translation = array();

      $result = false;
      $new_password_unsafe = self::make_random_password();
      $salt = self::password_salt_generate();
//      $providers_will_reset = array();
      foreach(self::$providers as $provider_id => $provider) {
        // Проверяем поддержку сброса пароля
        if($provider->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
          // Получаем список аккаунтов у провайдера по емейлу подтверждения
          $account_list = $provider->db_account_list_get_on_email($confirmation['email']);

          // Получаем список юзеров на этом аккаунте
          $this_provider_translation = self::db_get_account_translation_from_account_list($provider_id, array_keys($account_list));
          if(!empty($this_provider_translation)) {
            $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
          }

          // TODO - это всё надо перенести в провайдера!
          // Меняем пароль на всех аккаунтах
          foreach($account_list as $account_id => $account_data) {
            // TODO оно не меняет данных внутри аккаунта. Наверное, это верно...
            $provider_result = $provider->password_set_by_id_and_old_password($account_id, $new_password_unsafe, $salt);
            if($provider_result) {

              // TODO Логиним этого пользователя
              self::$login_status = $provider->login_internal();
              $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $provider->data[F_ACCOUNT]['account_name'], $new_password_unsafe);

              @$operation_result = mymail($confirmation['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
              // TODO - При ошибке отправки емейла добавлять Global Message
            }

            $result = $result || $provider_result;
          }
        }
      }

      if($result) {
        // Отправляем в лички письмо о сбросе пароля

        // ПО ОПРЕДЕЛЕНИЮ в $account_translation только
        //    - аккаунты, поддерживающие сброс пароля
        //    - список аккаунтов, имеющих тот же емейл, что указан в Подтверждении
        //    - игроки, привязанные только к этим аккаунтам
        // Значит им всем сразу скопом можно отправлять сообщения
        $message = sprintf($lang['sys_password_reset_message_body'], $new_password_unsafe);
        $message = sys_bbcodeParse($message) . '<br><br>';
        // msg_send_simple_message($found_provider->data[F_USER_ID], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);

        foreach($account_translation as $user_id => $providers_list) {
          msg_send_simple_message($user_id, 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);
        }
      } else {
        // TODO Системная ошибка
        throw new Exception(AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION, ERR_ERROR);
      }

      // TODO - эксэпшн при ошибке
      static::db_confirmation_delete_by_type_and_email(CONFIRM_PASSWORD_RESET, db_escape($confirmation['email']));

      sn_db_transaction_commit();
      sys_redirect('overview.php');
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      self::$login_status = $e->getMessage();
    }

    return self::$login_status;
  }

  /**
   * Генерирует набор даннх для возврата в основной код
   */
  // OK v4
  static function make_return_array() {
    global $config, $sys_stop_log_hit, $is_watching;

    // $ip = sec_player_ip();
    // $ip_int_safe = self::$device->ip_v4_int;

    $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;
    // if(!empty($user_id) && !$user_impersonator) {
    // $user_id не может быть пустым из-за констраинтов в таблице SPE
    self::db_security_entry_insert();

    if($user_id && !self::$is_impersonating) {
//      doquery(
//        "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
//        VALUES ({$user_id}," . self::$device->device_id . "," . self::$device->browser_id . "," . self::$device->ip_v4_int . ", '{$proxy_safe}');"
//      );

      self::db_counter_insert();

//      if(!$sys_stop_log_hit && $config->game_counter) {
//        $is_watching = true;
//        doquery(
//          "INSERT INTO {{counter}}
//          (`visit_time`, `user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`, `page_url_id`, `plain_url_id`)
//        VALUES
//          ('" . SN_TIME_SQL. "', {$user_id}, " . self::$device->device_id . "," . self::$device->browser_id . ", " .
//          self::$device->ip_v4_int . ", '{$proxy_safe}', " . self::$device->page_address_id . ", " . self::$device->page_url_id . ");");
//        $is_watching = false;
//      }

      $user = &self::$user;

      sys_user_options_unpack($user);

      if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
        $user['banaday'] = 0;
        $user['vacation'] = SN_TIME_NOW;
      }

      $user['user_lastip'] = self::$device->ip_v4_string;// $ip['ip'];
      $user['user_proxy'] = self::$device->ip_v4_proxy_chain; //$ip['proxy_chain'];

      self::$hidden[F_BANNED_STATUS] = $user['banaday'];
      self::$hidden[F_VACATION_STATUS] = $user['vacation'];

      $proxy_safe = db_escape(self::$device->ip_v4_proxy_chain);

      db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ",
      `banaday` = " . db_escape($user['banaday']) . ", `vacation` = " . db_escape($user['vacation']) . ",
      `user_lastip` = '" . db_escape($user['user_lastip']) . "', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = " . self::$device->browser_id
      );
    }

    if($extra = $config->security_ban_extra) {
      $extra = explode(',', $extra);
      array_walk($extra,'trim');
      in_array(self::$device->device_id, $extra) and die();
    }

    self::$hidden[F_LOGIN_STATUS] = self::$login_status = empty(self::$providers_authorised) ? self::$login_status : LOGIN_SUCCESS;
//    self::$hidden[F_PROVIDER_ID] = $found_provider->manifest['provider_id'];
//    self::$hidden[F_ACCOUNT_ID] = $found_provider->data[F_ACCOUNT_ID];
//    self::$hidden[F_ACCOUNT] = $found_provider->data[F_ACCOUNT];
//    self::$hidden[F_USER_ID] = $found_provider->data[F_USER_ID];
    self::$hidden[F_USER] = self::$user;
//    self::$hidden[F_REMEMBER_ME_SAFE] = $found_provider->data[F_REMEMBER_ME_SAFE];

    self::$hidden[AUTH_LEVEL] = isset(self::$user['authlevel']) ? self::$user['authlevel'] : AUTH_LEVEL_ANONYMOUS;

    self::$hidden[F_IMPERSONATE_STATUS] = self::$is_impersonating;
    // TODO
//    self::$hidden[F_IMPERSONATE_OPERATOR] = $found_provider->data[F_IMPERSONATE_OPERATOR];

    //TODO Сол и Парол тоже вкинуть в хидден
    self::$hidden[F_ACCOUNTS_AUTHORISED] = self::$providers_authorised;
  }


  // ХЕЛПЕРЫ ===========================================================================================================
  /**
   * Функция проверяет корректность имени игрока при регистрации
   *
   * @param $player_name_unsafe
   *
   * @throws Exception
   */
  // OK v4
  // TODO - вынести в отдельный хелпер
  public static function register_player_name_validate($player_name_unsafe) {
    // TODO - переделать под RAW-строки
    // Если имя игрока пустое - NO GO!
    if(trim($player_name_unsafe) == '') {
      throw new Exception(REGISTER_ERROR_PLAYER_NAME_EMPTY, ERR_ERROR);
    }
    // Проверяем, что бы в начале и конце не было пустых символов
    if($player_name_unsafe != trim($player_name_unsafe)) {
      throw new Exception(REGISTER_ERROR_PLAYER_NAME_TRIMMED, ERR_ERROR);
    }
    // Если логин имеет запрещенные символы - NO GO!
    if(strpbrk($player_name_unsafe, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      // TODO - выдавать в сообщение об ошибке список запрещенных символов
      // TODO - заранее извещать игрока, какие символы являются запрещенными
      throw new Exception(REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS, ERR_ERROR);
    }
    // Если логин меньше минимальной длины - NO GO!
    if(strlen($player_name_unsafe) < LOGIN_LENGTH_MIN) {
      // TODO - выдавать в сообщение об ошибке минимальную длину имени игрока
      // TODO - заранее извещать игрока, какая минимальная и максимальная длина имени
      throw new Exception(REGISTER_ERROR_PLAYER_NAME_SHORT, ERR_ERROR);
    }

    // TODO проверка на максимальную длину имени игрока
  }

  /**
   * Генерирует случайный код для сброса пароля
   *
   * @return string
   */
  // OK v4
  static function make_password_reset_code() {
    return sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
  }
  /**
   * Генерирует случайный пароль
   *
   * @return string
   */
  // OK v4
  static function make_random_password() {
    return sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
  }
  /**
   * Просаливает пароль
   *
   * @param $password
   * @param $salt
   *
   * @return string
   */
  // OK v4
  static function password_encode($password, $salt) {
    return md5($password . $salt);
  }
  /**
   * Генерирует соль
   *
   * @return string
   */
  // OK v4
  static function password_salt_generate() {
    // НЕ ПЕРЕКРЫВАТЬ
    // TODO ВКЛЮЧИТЬ ГЕНЕРАЦИЮ СОЛИ !!!
    return ''; // sys_random_string(16);
  }


  static function flog($message, $die = false) {
    if(!defined('DEBUG_AUTH') || !DEBUG_AUTH) {
      return;
    }
    list($called, $caller) = debug_backtrace(false);
    $caller_name =
      (!empty($caller['class']) ? $caller['class'] : '') .
      (!empty($caller['type']) ? $caller['type'] : '') .
      (!empty($caller['function']) ? $caller['function'] : '') .
      (!empty($called['line']) ? ':' . $called['line'] : '');

    $_SERVER['SERVER_NAME'] == 'localhost' ? print("<div class='debug'>$message - $caller_name\r\n</div>") : false;

    classSupernova::log_file("$message - $caller_name");
    if($die) {
      // pdump($caller);
      // pdump(debug_backtrace(false));
      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
    }
  }













//  static function db_confirmation_set($account_id_safe, $confirmation_type_safe, $email_safe) {
//    $confirmation = $this->db_confirmation_by_account_id($account_id_safe, CONFIRM_PASSWORD_RESET);
//    if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
//      throw new Exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN);
//    }
//
//    // TODO - уникальный индекс по id_user и type - и делать не INSERT, а REPLACE
//    // Удаляем предыдущие записи продтверждения сброса пароля
//    !empty($confirmation['id']) or doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$confirmation['id']}' LIMIT 1;");
//
//    do {
//      // Ну, если у нас > 999.999.999 подтверждений - тут нас ждут проблемы...
//      $confirm_code_safe = db_escape($confirm_code_unsafe = sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION));
//      $query = doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = {$confirmation_type_safe}", true);
//    } while($query);
//    doquery(
//      "REPLACE INTO {{confirmations}}
//        SET `provider_id` = {$this->manifest['provider_id']}, `account_id` = '{$account_id_safe}',
//        `type` = {$confirmation_type_safe}, `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");
//    return $confirm_code_unsafe;
//  }

//  /**
//   * Возвращает трансляцию аккаунта у провайдера в разрезе пользователь-провайдер
//   *
//   * @param $provider_id_safe
//   * @param $provider_account_id_safe
//   *
//   * @return array
//   */
//  // OK v4
//  // TODO - ФУНКЦИОНАЛЬНЫЙ ДУБЛИКАТ db_get_account_translation_from_account_list() ??
//  protected static function db_translate_get_user_list_on_provider($provider_id_safe, $provider_account_id_safe) {
//    $local_user_to_provider_list = array();
//
//    $query = doquery("SELECT * FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$provider_account_id_safe}");
//    while($row = db_fetch($query)) {
//      $local_user_to_provider_list[$row['user_id']][$provider_id_safe] = $row;
//    }
//
//    return $local_user_to_provider_list;
//  }
//


}
