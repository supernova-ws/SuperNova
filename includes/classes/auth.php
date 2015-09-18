<?php

// define("DEBUG_AUTH", true);

/**
 * Статический над-класс, который обеспечивает интерфейс авторизации для остального кода
 *
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:51
 *
 * version #40a10.23#
 */

class auth extends sn_module {
  public $manifest = array(
    'package' => 'core',
    'name' => 'auth',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a10.23# copyright © 2009-2015 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'load_order' => 1,

    'installed' => true,
    'active' => true,

    'mvc' => array(
      'model' => array(
        'player_register' => array(
          'callable' => 'player_register_model',
        ),
      ),

      'view' => array(
        'player_register' => array(
          'callable' => 'player_register_view',
        ),
      ),
    ),
  );

  /**
   * БД из которой читать данные
   *
   * @var db_mysql $db
   */
  static $db;
  /**
   * Информация об устройстве
   *
   * @var RequestInfo
   */
  static $device;
  /**
   * Аккаунт ????
   *
   * @var Account
   */
  public static $account = null;
  /**
   * Запись текущего игрока из `users`
   *
   * @var null
   */
  static $user = null;

  /**
   * @var auth_local
   */
  public static $main_provider = null;

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

  static $user_id_to_provider = array();

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
  static $is_player_register = false;
  /**
   * @var int
   */
  static $register_status = LOGIN_UNDEFINED;

  /**
   * Максимальный локальный уровень авторизации игрока
   *
   * @var int
   */
  static $auth_level_max_local = AUTH_LEVEL_ANONYMOUS;

  /**
   * @var int
   */
  protected static $partner_id = 0;

  /**
   * @var string
   */
  protected static $server_name = '';

  function __assign_vars() {
    return array(
      'sn_data[pages]' => array(
        'player_register' => 'includes/classes/auth',
      ),
    );
  }

  /**
   * @param string $filename
   */
  public function __construct($filename = __FILE__) {
    parent::__construct($filename);

    self::$main_provider = new auth_local();
  }

  /**
   * Функция пытается залогиниться по всем известным провайдерам
   *
   * @param null $result
   */
  // OK v4.5
  public static function login() {
    global $lang;

    !self::$is_init ? self::init() : false;

    self::auth_reset(); // OK v4.5

    !empty($_POST) ? self::flog(dump($_POST, '$_POST')) : false;
    !empty($_GET) ? self::flog(dump($_GET, '$_GET')) : false;
    !empty($_COOKIE) ? self::flog(dump($_COOKIE,'$_COOKIE')) : false;

    foreach(self::$providers as $provider_id => $provider) {
      $login_status = $provider->login(); // OK v4.5
      self::flog(($provider->manifest['name'] . '->' . 'login_try - ') . (empty($provider->account->account_id) ? $lang['sys_login_messages'][$provider->account_login_status] : dump($provider)));
      if($login_status == LOGIN_SUCCESS && is_object($provider->account) && $provider->account instanceof Account && $provider->account->account_id) {
        self::$providers_authorised[$provider_id] = &self::$providers[$provider_id];

        self::$user_id_to_provider = array_replace_recursive(
          self::$user_id_to_provider,
          static::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id) // OK 4.5
        );
      } elseif($login_status != LOGIN_UNDEFINED) {
        self::$account_error_list[$provider_id] = $login_status;
      }
    }

    // TODO - 4.6 - Все провайдеры возвращают Account

    if(empty(self::$providers_authorised)) {
      // Ни один аккаунт не авторизирован
      // Проверяем - есть ли у нас ошибки в аккаунтах?
      if(!empty(self::$account_error_list)) {
        // Если есть - выводим их
        self::$login_status = reset(self::$account_error_list);
      } else {
        // Иначе - это первый запуск страницы. ИЛИ СПЕЦИАЛЬНОЕ ДЕЙСТВИЕ!
        // ...которые по факты должны обрабатываться в рамках provider->login()
      }
    } else {
      $temp = reset(self::$providers_authorised);
      self::$account = $temp->account;

      self::user_check_access(); // 4.5

      // Остались ли у нас в списке доступные игроки?
      if(empty(self::$accessible_user_row_list)) {
        // Нет ни одного игрока ни на одном авторизированном аккаунте
        // Надо регать нового игрока
        self::register_player(); // OK 4.5
        // Либо есть self::$user, либо идем на новый круг авторизации
      }

      if(!empty(self::$accessible_user_row_list)) {
        // Да, есть доступные игроки, которые так же прописаны в базе

        // Проверяем куку "текущего игрока" из браузера
        self::user_check_cookie(); // 4.5

        // В куке нет валидного ИД записи игрока, доступной с текущих аккаунтов
        if(empty(self::$user['id'])) {
          // Берем первого из доступных
          // TODO - default_user
          self::$user = reset(self::$accessible_user_row_list);
        }
        // Тут ВСЕГДА есть self::$user

        //Прописываем текущего игрока на все авторизированные аккаунты
        // TODO - ИЛИ ВСЕХ ИГРОКОВ??
        if(!self::$is_impersonating) {
          foreach(self::$providers_authorised as $provider_id => $provider) {
            if(empty(self::$user_id_to_provider[self::$user['id']][$provider_id])) {
              self::db_translate_register_user($provider_id, $provider->account->account_id, self::$user['id']);
              self::$user_id_to_provider[self::$user['id']][$provider_id][$provider->account->account_id] = true;
            }
          }
        }

      } else {
//        // Нет ни одного игрока ни на одном авторизированном аккаунте
//        // Надо регать нового игрока
//        self::register_player(); // OK 4.5
//        // Либо есть self::$user, либо идем на новый круг авторизации
      }
      // IF self::$user
      // ELSE Либо есть self::$user, либо идем на новый круг авторизации
    }
    // IF ????????
    // ELSE Либо есть self::$user, либо идем на новый круг авторизации

    if(empty(self::$user['id'])) {
      self::cookie_set(''); // OK 4.5
    } elseif(self::$user['id'] != $_COOKIE[SN_COOKIE_U]) {
      self::cookie_set(self::$user['id']); // OK 4.5
    }

    return self::make_return_array();
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
  // OK v4.2
  static function logout($redirect = true) {
    foreach(self::$providers as $provider_name => $provider) {
      $provider->logout();
    }
    // TODO - IMPERSONATE - SN_COOKIE_U_I
    if(!empty($_COOKIE[SN_COOKIE_U . '_I'])) {
      // sn_setcookie(SN_COOKIE_U, $_COOKIE[SN_COOKIE_U . '_I'], SN_TIME_NOW + PERIOD_YEAR, SN_ROOT_RELATIVE);
      self::cookie_set($_COOKIE[SN_COOKIE_U . '_I']);
      // sn_setcookie(SN_COOKIE_U . '_I', '', SN_TIME_NOW - PERIOD_YEAR, SN_ROOT_RELATIVE);
      self::cookie_set(0, true);
    } else {
      // sn_setcookie(SN_COOKIE_U, '', SN_TIME_NOW - PERIOD_YEAR, SN_ROOT_RELATIVE);
      self::cookie_set(0);
    }
//die();

    if($redirect === true) {
      sys_redirect(SN_ROOT_RELATIVE . (empty($_COOKIE[SN_COOKIE_U]) ? 'login.php' : 'admin/overview.php'));
    } elseif($redirect !== false) {
      sys_redirect($redirect);
    }
  }


  /**
   * Функция проверяет наличие имени игрока в базе
   *
   * @param $player_name_unsafe
   *
   * @return bool
   */
  // OK v4.6
  public static function db_player_name_exists($player_name_unsafe) {
    sn_db_transaction_check(true);

    $player_name_safe = static::$db->db_escape($player_name_unsafe);

    $player_name_exists = static::$db->doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` = '{$player_name_safe}' LIMIT 1 FOR UPDATE", true);
    return !empty($player_name_exists);
  }
  /**
   * Получение максимального ID игрока
   *
   * @return int
   */
  // OK v4.6
  static function db_player_get_max_id() {
    $max_user_id = static::$db->doquery("SELECT MAX(`id`) as `max_user_id` FROM `{{user}}`", true);
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
  // OK v4.5
  // TODO - неправильные параметры
  protected static function db_translate_register_user($provider_id_safe, $provider_account_id_safe, $user_id_safe) {
    return static::$db->doquery(
      "INSERT INTO `{{account_translate}}` (`provider_id`, `provider_account_id`, `user_id`) VALUES
                  ({$provider_id_safe}, {$provider_account_id_safe}, {$user_id_safe});"
    );
  }
  /**
   * Возвращает из `account_translate` список пользователей, которые прилинкованы к списку аккаунтов на указанном провайдере
   * @version 4.5
   *
   * @param int $provider_id_unsafe Идентификатор провайдера авторизации
   * @param int|int[] $account_list
   *
   * @return array
   */
  // OK v4.6
  public static function db_translate_get_users_from_account_list($provider_id_unsafe, $account_list) {
    $account_translation = array();

    $provider_id_safe = intval($provider_id_unsafe);
    !is_array($account_list) ? $account_list = array($account_list) : false;

    foreach($account_list as $provider_account_id_unsafe) {
      $provider_account_id_safe = intval($provider_account_id_unsafe);

      // TODO - Здесь могут отсутствовать аккаунты - проверять провайдером
      $query = static::$db->doquery("SELECT `user_id` FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$provider_account_id_safe} FOR UPDATE");
      while($row = static::$db->db_fetch($query)) {
        $account_translation[$row['user_id']][$provider_id_unsafe][$provider_account_id_unsafe] = true;
      }
    }

    return $account_translation;
  }

  /**
   * Сбрасывает значения полей
   */
  // OK v4.5
  protected static function auth_reset() {
    self::$login_status = LOGIN_UNDEFINED;
    self::$player_suggested_name = '';
    self::$account = null;
    self::$user = null;
    self::$providers_authorised = array(); // Все аккаунты, которые успешно залогинились
    self::$account_error_list = array(); // Статусы всех аккаунтов
    self::$accessible_user_row_list = array();
    self::$user_id_to_provider = array();
  }

  /**
   * Статическая инициализация
   */
  // OK v4
  public static function init($db = null) {
    global $sn_module_list;

    // В этой точке все модули уже прогружены и инициализированы по 1 экземпляру
    if(self::$is_init) {
      return;
    }

    self::$db = !is_object($db) ? classSupernova::$db : $db;

//pdump(self::$main_provider);
//pdump(array_keys($sn_module_list));

    if(empty($sn_module_list['auth'])) {
      die('{Не обнаружено ни одного провайдера авторизации!}');
    }

    // self::init_device_and_browser();
    self::$device = new RequestInfo();
    self::$is_password_reset = sys_get_param('password_reset') ? true : false;
    self::$is_password_reset_confirm = sys_get_param('password_reset_confirm') ? true : false;
    self::$is_player_register = sys_get_param('player_register') ? true : false;
    self::$partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id'));
    self::$server_name = sys_get_param_str_unsafe('server_name', SN_ROOT_VIRTUAL);

    self::$providers = array();
    foreach($sn_module_list['auth'] as $module_name => $module) {
      if($module_name != 'auth_provider') {
        // Провайдеры подготавливают для себя данные
        self::$providers[$module->manifest['provider_id']] = $module;
        // self::$providers[$module->manifest['provider_id']]->auth_manager_set(get_called_class());
        //self::$providers[$module->manifest['provider_id']]->prepare(); // protected method called in constructor
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

      if(self::db_player_name_exists($player_name_unsafe)) {
        throw new Exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);
      }

      ;

      // Узнаем язык и емейл игрока
      $player_language = '';
      $player_email = '';
      // TODO - порнография - работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
      foreach(self::$providers_authorised as $provider) {
        if(!$player_language && $provider->account->account_language) {
          $player_language = $provider->account->account_language;
        }
        if(!$player_email && $provider->account->account_email) {
          $player_email = $provider->account->account_email;
        }
      }
      $player_language = sys_get_param_str('lang') ? sys_get_param_str('lang') : $player_language;
      $player_language = $player_language ? $player_language : DEFAULT_LANG;

      // TODO - дописать эксепшнов в процедуре создания игрока
      self::$user = player_create($player_name_unsafe, $player_email, array(
        'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
        'language_iso' => static::$db->db_escape($player_language),
        // 'password_encoded_unsafe' => $this->data[F_ACCOUNT]['account_password'],
        // 'salt' => $this->data[F_ACCOUNT]['account_salt'],
      ));
      // Зарегестрировать на него аккаунты из self::$accounts_authorised
      $a_user = self::$user;
      foreach(self::$providers_authorised as $provider) {
        // TODO - порнография. Должен быть отдельный класс трансляторов - в т.ч. и кэширующий транслятор
        // TODO - ну и работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
        self::db_translate_register_user($provider->manifest['provider_id'], $provider->account->account_id, $a_user['id']);

      }
      // Установить куку игрока
      // sn_setcookie(SN_COOKIE_U, self::$user['id'], SN_TIME_NOW + PERIOD_YEAR);
      self::cookie_set(self::$user['id']);

      sn_db_transaction_commit();
      self::$register_status = LOGIN_SUCCESS;
    } catch(Exception $e) {
      sn_db_transaction_rollback();

      // Если старое имя занято
      // unset(self::$user);
      self::$user = null;
//      self::$player_suggested_name = $player_name_unsafe;
//      $login_status = LOGIN_SUCCESS;
      self::$register_status == LOGIN_UNDEFINED ? self::$register_status = $e->getMessage() : false;
    }
  }

  public function player_register_model() {
    // TODO ВСЕГДА ПРЕДЛАГАТЬ РЕГАТЬ ИГРОКА ИЛИ ПОДКЛЮЧИТЬ ИМЕЮЩЕГОСЯ!
    // TODO - НЕ РЕГАТЬ ИГРОКА ТОЛЬКО ЕСЛИ МЫ ТОЛЬКО ЧТО ЗАРЕГЕСТРИРОВАЛИ НОВЫЙ АККАУНТ! ДЛЯ ЭТОГО ДОЛЖЕН БЫТЬ СПЕЦИАЛЬНЫЙ ФЛАГ!

    // TODO Сначала пробовать зарегестрировать игрока с тем же именем - что бы избежать лишнего шага
    // TODO в auth_local делать проверку БД на существование имени игрока в локальной БД - что бы избежать лишнего шага (см.выше)
    // TODO Хотя тут может получится вечный цикл - ПОДУМАТЬ
    // TODO Тут же можно пробовать провести попытку слияния аккаунтов - хотя это и очень небезопасно

    if(sys_get_param('login_player_register_logout')) {
      self::logout();
    }

    $original_suggest = '';
    // Смотрим - есть ли у нас данные от пользователя
    if(($player_name_submitted = sys_get_param('submit_player_name'))) {
      // Попытка регистрации нового игрока из данных, введенных пользователем
      self::$player_suggested_name = sys_get_param_str_unsafe('player_suggested_name');
    } else {
      foreach(self::$providers_authorised as $provider) {
        if(self::$player_suggested_name = $provider->player_name_suggest()) { // OK 4.5
          $original_suggest = $provider->player_name_suggest();
          break;
        }
      }
    }

    // Если у нас провайдеры не дают имени и пользователь не дал свой вариант - это у нас первый логин в игру
    if(!self::$player_suggested_name) {
      $max_user_id = self::db_player_get_max_id(); // 4.5
      // TODO - предлагать имя игрока по локали

      // Проверить наличие такого имени в истории имён
      do {
        sn_db_transaction_rollback();
        self::$player_suggested_name = 'Emperor ' . mt_rand($max_user_id + 1, $max_user_id + 1000);
        sn_db_transaction_start();
      } while(self::db_player_name_exists(self::$player_suggested_name));

    }

    if($player_name_submitted) {
      self::register_player_db_create(self::$player_suggested_name); // OK 4.5
      if(self::$register_status == LOGIN_SUCCESS) {
        sys_redirect(SN_ROOT_VIRTUAL . 'overview.php');
      } elseif(self::$register_status == REGISTER_ERROR_PLAYER_NAME_EXISTS && $original_suggest == self::$player_suggested_name) {
        // self::$player_suggested_name .=
      }
//      if(self::$login_status != LOGIN_SUCCESS) {
//        // TODO Ошибка при регистрации нового игрока под текущим именем
//      }
    }

//    pdump(self::$player_suggested_name);
//    pdump('model');
//    die('model');

  }

  public function player_register_view($template = null) {
    global $template_result, $lang;

    define('LOGIN_LOGOUT', true);

    $template_result[F_PLAYER_REGISTER_MESSAGE] =
      isset($template_result[F_PLAYER_REGISTER_MESSAGE]) && $template_result[F_PLAYER_REGISTER_MESSAGE]
        ? $template_result[F_PLAYER_REGISTER_MESSAGE]
        : (self::$register_status != LOGIN_UNDEFINED
        ? $lang['sys_login_messages'][self::$register_status]
        : false
      );

    if(self::$register_status == LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS) {
      $prohibited_characters = array_map(function($value) {
        return "'" . htmlentities($value, ENT_QUOTES, 'UTF-8') . "'";
      }, str_split(LOGIN_REGISTER_CHARACTERS_PROHIBITED));
      $template_result[F_PLAYER_REGISTER_MESSAGE] .= implode(', ', $prohibited_characters);
    }

//    pdump('view');
//    die('view');
    $template_result = array_merge($template_result, array(
      'NAVBAR' => false,
      'PLAYER_SUGGESTED_NAME' => sys_safe_output(self::$player_suggested_name),
      'PARTNER_ID' => sys_safe_output(self::$partner_id),
      'SERVER_NAME' => sys_safe_output(self::$server_name),
      'PLAYER_REGISTER_STATUS' => self::$register_status,
      'PLAYER_REGISTER_MESSAGE' => $template_result[F_PLAYER_REGISTER_MESSAGE],
      'LOGIN_UNDEFINED' => LOGIN_UNDEFINED,
    ));

//    pdump(self::$register_status);

    $template = gettemplate('login_player_register', $template);

    return $template;
  }

  /**
   * Функция управляет регистрацией нового игрока для существующих аккаунтов
   */
  // OK v4
  public static function register_player() {
    // Есть хотя бы один удачно залогинившийся аккаунт. Но у него/них нету ни одного связанного аккаунта

    if(!self::$is_player_register) {
      $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id'));
      sys_redirect(SN_ROOT_VIRTUAL . 'index.php?page=player_register&player_register=1' . ($partner_id ? '&id_ref=' . $partner_id : ''));
    }
  }

  /**
   * Проверяет доступ авторизированных аккаунтов к заявленным в трансляции юзерам
   * @version 4.5
   */
  // OK v4.5
  protected static function user_check_access() {
    if(empty(self::$user_id_to_provider)) {
      return;
    }

    // Пробиваем все ИД игроков по базе - есть ли вообще такие записи. Вообще-то это не особо нужно - у нас по определению стоят констраинты
    // Зато так мы узнаем максимальный authlevel и проверим права имперсонейта
    foreach(self::$user_id_to_provider as $user_id => $cork) {
      $user = db_user_by_id($user_id);
      // Если записи игрока в БД не существует?
      if(empty($user['id'])) {
        // Удаляем этого и переходим к следующему
        unset(self::$user_id_to_provider[$user_id]);
        // TODO - де-регистрируем игрока из таблицы translate
      } else {
        self::$accessible_user_row_list[$user['id']] = $user;
        // $local_max_auth_level = max($local_max_auth_level, $user['authlevel']);
        self::$auth_level_max_local = max(self::$auth_level_max_local, $user['authlevel']);
      }
    }
  }

  /**
   * Проверяем куку "текущего игрока" из браузера
   * @version 4.5
   */
  // OK v4.5
  protected static function user_check_cookie() {
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
          self::$auth_level_max_local >= AUTH_LEVEL_ADMINISTRATOR
          // И права игрока, в которого пытаются зайти - меньше текущих максимальных прав
          && self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]['authlevel'] < self::$auth_level_max_local
        )
      )
    ) {
      // Берем текущим юзером юзера с ИД из куки
      self::$is_impersonating = empty(self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]);
      // self::$user = self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]];
      self::$user = db_user_by_id($_COOKIE[SN_COOKIE_U]);
    }
  }



  public static function impersonate($user_selected) {
    if($_COOKIE[SN_COOKIE_U . '_I']) {
      die('You already impersonating someone. Go back to living other\'s life! Or clear your cookies and try again'); // TODO: Log it
    }

    if(self::$auth_level_max_local < AUTH_LEVEL_ADMINISTRATOR) {
      die('You can\'t impersonate - too low level'); // TODO: Log it
    }

    if(self::$auth_level_max_local <= $user_selected['authlevel']) {
      die('You can\'t impersonate this account - level is greater or equal to yours'); // TODO: Log it
    }

    // sn_setcookie(SN_COOKIE_U . '_I', $_COOKIE[SN_COOKIE_U], 0, SN_ROOT_RELATIVE);
    self::cookie_set($_COOKIE[SN_COOKIE_U], true, 0);

//    $expire_time = SN_TIME_NOW + PERIOD_YEAR;
//    $password_encoded = md5("{$user_selected['password']}--" . classSupernova::$sn_secret_word);
//    $cookie = $user_selected['id'] . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . '1';
//    sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
    // sn_setcookie(SN_COOKIE_U, $user_selected['id'], SN_TIME_NOW + PERIOD_YEAR, SN_ROOT_RELATIVE);
    // TODO - Имперсонейт - только на одну сессию
    self::cookie_set($user_selected['id']);

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
    die('{Пока не работает}');
    // TODO - результаты работы
    // TODO - верфикация емейла
//    foreach(self::$providers_authorised as $provider) {
//      if($provider->is_feature_supported(AUTH_FEATURE_EMAIL_CHANGE)) {
//        $provider->db_account_set_email($new_email_unsafe);
//      }
//    }
  }

  /**
   * Проверяет пароль на совпадение с текущими паролями
   *
   * @param $password_unsafe
   *
   * @return bool
   */
  // OK v4.6
  public static function password_check($password_unsafe) {
    $result = false;

    if(empty(self::$providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("password_check: Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
    } else {
      foreach(self::$providers_authorised as $provider_id => $provider) {
        if($provider->is_feature_supported(AUTH_FEATURE_HAS_PASSWORD)) {
          $result = $result || $provider->password_check($password_unsafe);
        }
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
  // OK v4.6
  public static function password_change($old_password_unsafe, $new_password_unsafe) {
    global $lang;

    if(empty(self::$providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
      return false;
    }

    // TODO - Проверять пароль на корректность

    // TODO - Не менять (?) пароль у аккаунтов, к которым пристёгнут хоть один игрок с AUTH_LEVEL > 0

    $salt_unsafe = self::password_salt_generate();

    $providers_changed_password = array();
    foreach(self::$providers_authorised as $provider_id => $provider) {
      if(
        !$provider->is_feature_supported(AUTH_FEATURE_PASSWORD_CHANGE)
        || !$provider->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe)
      ) {
        continue;
      }

      // Узнаем список игроков, которые прикреплены к этому аккаунту
      $account_translation = self::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id);

      // Рассылаем уведомления о смене пароля в ЛС
      foreach($account_translation as $user_id => $provider_info) {
        // TODO - УКазывать тип аккаунта, на котором сменён пароль
        msg_send_simple_message($user_id, 0, SN_TIME_NOW, MSG_TYPE_ADMIN,
          $lang['sys_administration'], $lang['sys_login_register_message_title'],
          sprintf($lang['sys_login_register_message_body'], $provider->account->account_name, $new_password_unsafe), false //true
        );
      }
      $providers_changed_password[$provider_id] = $provider;
    }

    // TODO - отсылать уведомление на емейл

    return !empty($providers_changed_password);
  }

  /**
   * Генерирует набор даннх для возврата в основной код
   */
  // OK v4.5
  static function make_return_array() {
    global $config;

    $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;
    // if(!empty($user_id) && !$user_impersonator) {
    // $user_id не может быть пустым из-за констраинтов в таблице SPE
    // self::db_security_entry_insert();
    self::$device->db_security_entry_insert($user_id);

    $result = array();

    if($user_id && !self::$is_impersonating) {
      // self::db_counter_insert();
      self::$device->db_counter_insert($user_id);

      $user = &self::$user;

      sys_user_options_unpack($user);

      if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
        $user['banaday'] = 0;
        $user['vacation'] = SN_TIME_NOW;
      }

      $user['user_lastip'] = self::$device->ip_v4_string;// $ip['ip'];
      $user['user_proxy'] = self::$device->ip_v4_proxy_chain; //$ip['proxy_chain'];

      $result[F_BANNED_STATUS] = $user['banaday'];
      $result[F_VACATION_STATUS] = $user['vacation'];

      $proxy_safe = static::$db->db_escape(self::$device->ip_v4_proxy_chain);

      db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ",
      `banaday` = " . static::$db->db_escape($user['banaday']) . ", `vacation` = " . static::$db->db_escape($user['vacation']) . ",
      `user_lastip` = '" . static::$db->db_escape($user['user_lastip']) . "', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = " . self::$device->browser_id
      );
    }

    if($extra = $config->security_ban_extra) {
      $extra = explode(',', $extra);
      array_walk($extra,'trim');
      in_array(self::$device->device_id, $extra) and die();
    }

    $result[F_LOGIN_STATUS] = self::$login_status = empty(self::$providers_authorised) ? self::$login_status : LOGIN_SUCCESS;
    $result[F_PLAYER_REGISTER_STATUS] = self::$register_status;
//    self::$hidden[F_PROVIDER_ID] = $found_provider->manifest['provider_id'];
//    self::$hidden[F_ACCOUNT_ID] = $found_provider->data[F_ACCOUNT_ID];
//    self::$hidden[F_ACCOUNT] = $found_provider->data[F_ACCOUNT];
//    self::$hidden[F_USER_ID] = $found_provider->data[F_USER_ID];
    $result[F_USER] = self::$user;
//    self::$hidden[F_REMEMBER_ME_SAFE] = $found_provider->data[F_REMEMBER_ME_SAFE];

    // $result[AUTH_LEVEL] = isset(self::$user['authlevel']) ? self::$user['authlevel'] : AUTH_LEVEL_ANONYMOUS;
    $result[AUTH_LEVEL] = self::$auth_level_max_local;

    $result[F_IMPERSONATE_STATUS] = self::$is_impersonating;
    // TODO
//    self::$hidden[F_IMPERSONATE_OPERATOR] = $found_provider->data[F_IMPERSONATE_OPERATOR];

    //TODO Сол и Парол тоже вкинуть в хидден
    $result[F_ACCOUNTS_AUTHORISED] = self::$providers_authorised;

    return $result;
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

  // OK v4.5
  // TODO - REMEMBER_ME
  static protected function cookie_set($value, $impersonate = false, $period = null) {
    sn_setcookie(SN_COOKIE_U . ($impersonate ? '_I' : ''), $value, $period === null ? SN_TIME_NOW + PERIOD_YEAR : $period, SN_ROOT_RELATIVE);
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

//  /**
//   * Отсылает письмо с кодом подтверждения для сброса пароля
//   *
//   * @return int|string
//   */
//  // OK v4.5
//  static function password_reset_send_code() {
//    global $lang, $config;
//
//    if(!self::$is_password_reset) {
//      return self::$login_status = LOGIN_UNDEFINED;
//    }
//
//    $account_translation = array();
//    $email_unsafe = sys_get_param_str_unsafe('email');
//
//    $providers_will_reset = array();
//    foreach(self::$providers as $provider_id => $provider) {
//      // Проверяем поддержку сброса пароля
//      if(!$provider->is_feature_supported(AUTH_FEATURE_PASSWORD_RESET)) {
//        continue;
//      }
//
//      if($provider->account_get_by_email($email_unsafe)) {
//        $providers_will_reset[$provider_id] = $provider;
//
//        $this_provider_translation = self::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id); // OK 4.5
//        if(!empty($this_provider_translation)) {
//          $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
//        }
//      }
////      $account_list = $provider->db_account_list_get_on_email($email_unsafe); // OK 4.5
////      if(!empty($account_list)) {
////        $providers_will_reset[$provider_id] = $provider;
////
////        $this_provider_translation = self::db_translate_get_users_from_account_list($provider_id, array_keys($account_list)); // OK 4.5
////        if(!empty($this_provider_translation)) {
////          $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
////        }
////      }
//    }
//
//    try {
//      $user_list = db_user_list_by_id(array_keys($account_translation));
//      //if(empty($user_list)) {
//      // TODO - НЕПРАВИЛЬНО! Таблица трансляций МОЖЕТ быть пустой! Когда аккаунт есть, но нет ни одного пользователя для него!
//      // throw new Exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL, ERR_ERROR);
//      //}
//
//      // TODO - Проверять уровень доступа аккаунта!
//      // Аккаунты с АУТЛЕВЕЛ больше 0 - НЕ СБРАСЫВАЮТ ПАРОЛИ!
//      foreach($user_list as $user_id => $user_data) {
//        if($user_data['authlevel'] > AUTH_LEVEL_REGISTERED) {
//          throw new Exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT, ERR_ERROR);
//        }
//      }
//
//      $email_safe = static::$db->db_escape($email_unsafe);
//
//      $confirmation = static::db_confirmation_get_latest_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5
//      if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
//        throw new Exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN, ERR_ERROR);
//      }
//
//      // Удаляем предыдущие записи продтверждения сброса пароля
//      !empty($confirmation['id']) or static::db_confirmation_delete_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5
//
//      sn_db_transaction_start();
//      $confirm_code_unsafe = self::db_confirmation_get_unique_code_by_type_and_email(CONFIRM_PASSWORD_RESET, $email_unsafe); // OK 4.5
//      sn_db_transaction_commit();
//
//      @$result = mymail($email_unsafe,
//        sprintf($lang['log_lost_email_title'], $config->game_name),
//        sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . 'login.php', $confirm_code_unsafe, date(FMT_DATE_TIME, SN_TIME_NOW + AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE), $config->game_name)
//      );
//
//      $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
//    } catch(Exception $e) {
//      sn_db_transaction_rollback();
//      $result = $e->getMessage();
//    }
//
//    return self::$login_status = $result;
//  }
//
//  /**
//   * Сброс пароля по введенному коду подтверждения
//   *
//   * @return int|string
//   */
//  // OK v4.5
//  static function password_reset_confirm() {
//    global $lang;
//
//    if(!self::$is_password_reset_confirm) {
//      return LOGIN_UNDEFINED;
//    }
//
//    try {
//      sn_db_transaction_start();
//      $code_unsafe = sys_get_param_str_unsafe('password_reset_code');
//      $confirmation = self::db_confirmation_get_by_type_and_code(CONFIRM_PASSWORD_RESET, $code_unsafe); // OK 4.5
//
//      if(empty($confirmation)) {
//        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_WRONG, ERR_ERROR);
//      }
//
//      if(SN_TIME_NOW - strtotime($confirmation['create_time']) > AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE) {
//        throw new Exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD, ERR_ERROR);
//      }
//
//      $result = false;
//      $new_password_unsafe = self::make_random_password();
//      $salt_unsafe = self::password_salt_generate();
//
//      $users_translated = array();
//      foreach(self::$providers as $provider_id => $provider) {
//        if($provider->password_change_on_email($confirmation['email'], $new_password_unsafe, $salt_unsafe) == LOGIN_SUCCESS) { // OK 4.5
//          $this_provider_translation = self::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id); // OK 4.5
//          $users_translated = array_replace_recursive($users_translated, $this_provider_translation);
//        }
//      }
//
//      if(!empty($users_translated)) {
//        // Отправляем в лички письмо о сбросе пароля
//
//        // ПО ОПРЕДЕЛЕНИЮ в $users_translated только
//        //    - аккаунты, поддерживающие сброс пароля
//        //    - список аккаунтов, имеющих тот же емейл, что указан в Подтверждении
//        //    - игроки, привязанные только к этим аккаунтам
//        // Значит им всем сразу скопом можно отправлять сообщения
//        $message = sprintf($lang['sys_password_reset_message_body'], $new_password_unsafe);
//        $message = sys_bbcodeParse($message) . '<br><br>';
//        // msg_send_simple_message($found_provider->data[F_USER_ID], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);
//
//        foreach($users_translated as $user_id => $providers_list) {
//          msg_send_simple_message($user_id, 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['sys_login_register_message_title'], $message);
//        }
//      } else {
//        // Фигня - может быть и пустой, если у нас есть только аккаунт, но нет пользователей
//        // throw new Exception(AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION, ERR_ERROR);
//      }
//
//      // TODO - эксэпшн при ошибке
//      static::db_confirmation_delete_by_type_and_email(CONFIRM_PASSWORD_RESET, $confirmation['email']); // OK 4.5
//
//      sn_db_transaction_commit();
//      sys_redirect('overview.php');
//    } catch (Exception $e) {
//      sn_db_transaction_rollback();
//      self::$login_status = $e->getMessage();
//    }
//
//    return self::$login_status;
//  }
//
//  // TODO - НЕ ОБЯЗАТЕЛЬНО ОТПРАВЛЯТЬ ЧЕРЕЗ ЕМЕЙЛ! ЕСЛИ ЭТО ФЕЙСБУЧЕК ИЛИ ВКШЕЧКА - МОЖНО ЧЕРЕЗ ЛС ПИСАТЬ!!
//  // TODO - OK 4.6
//  public static function db_confirmation_get_latest_by_type_and_email($confirmation_type_safe, $email_unsafe) {
//    $email_safe = static::$db->db_escape($email_unsafe);
//
//    return static::$db->doquery(
//      "SELECT * FROM {{confirmations}} WHERE
//          `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}' ORDER BY create_time DESC LIMIT 1;", true);
//  }
//  // TODO - OK 4.6
//  public static function db_confirmation_delete_by_type_and_email($confirmation_type_safe, $email_unsafe) {
//    $email_safe = static::$db->db_escape($email_unsafe);
//
//    return static::$db->doquery("DELETE FROM {{confirmations}} WHERE `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}'");
//  }
//  // TODO - OK 4.6
//  public static function db_confirmation_get_unique_code_by_type_and_email($confirmation_type_safe, $email_unsafe) {
//    $email_safe = static::$db->db_escape($email_unsafe);
//
//    do {
//      // Ну, если у нас > 999.999 подтверждений - тут нас ждут проблемы...
//      $confirm_code_safe = static::$db->db_escape($confirm_code_unsafe = self::make_password_reset_code());
//      // $query = static::$db->doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = {$confirmation_type_safe} FOR UPDATE", true);
//      // Тип не нужен для проверки - код подтверждения должен быть уникален от слова "совсем"
//      $query = static::$db->doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' FOR UPDATE", true);
//    } while($query);
//
//    static::$db->doquery(
//      "REPLACE INTO {{confirmations}}
//        SET `type` = {$confirmation_type_safe}, `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");
//
//    return $confirm_code_unsafe;
//  }
//  // TODO - OK 4.6
//  public static function db_confirmation_get_by_type_and_code($confirmation_type_safe, $confirmation_code_unsafe) {
//    $confirmation_code_safe = static::$db->db_escape($confirmation_code_unsafe);
//
//    return static::$db->doquery(
//      "SELECT * FROM {{confirmations}} WHERE
//          `type` = {$confirmation_type_safe} AND `code` = '{$confirmation_code_safe}' ORDER BY create_time DESC LIMIT 1 FOR UPDATE", true);
//  }
  // OK v4.1
//  public static function db_player_name_check_existence($player_name_unsafe) {
//    sn_db_transaction_check(true);
//
//    $player_name_safe = static::$db->db_escape($player_name_unsafe);
//
//    $player_name_exists = static::$db->doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` = '{$player_name_safe}' LIMIT 1 FOR UPDATE", true);
//    if(!empty($player_name_exists)) {
//      throw new Exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);
//    }
//  }

}
