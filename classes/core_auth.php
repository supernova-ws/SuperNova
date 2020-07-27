<?php

// define("DEBUG_AUTH", true);
use DBAL\db_mysql;
use Modules\sn_module;

/**
 * Статический над-класс, который обеспечивает интерфейс авторизации для остального кода
 *
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:51
 *
 * version #45d0#
 */

class core_auth extends sn_module {
  public $versionCommitted = '#45d0#';

  public $manifest = [
    'package' => 'core',
    'name' => 'auth',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #45d0# copyright © 2009-2018 Gorlum',

    self::M_LOAD_ORDER => MODULE_LOAD_ORDER_CORE_AUTH,

    'mvc' => [
      'pages' => [
        'player_register' => 'classes/core_auth'
      ],

      'model' => [
        'player_register' => [
          'callable' => 'player_register_model',
        ],
      ],

      'view' => [
        'player_register' => [
          'callable' => 'player_register_view',
        ],
      ],
    ],
  ];

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
  public $account = null;
  /**
   * Запись текущего игрока из `users`
   *
   * @var null
   */
  static $user = null;

  /**
   * Основной провайдер
   *
   * @var auth_local
   */
  public static $main_provider = null;

  /**
   * Статус инициализации
   *
   * @var bool
   */
  // protected static $is_init = false;
  /**
   * Список провайдеров
   *
   * @var auth_local[]
   */
  protected $providers = array();

  /**
   * Глобальный статус входа в игру
   *
   * @var int
   */
  static $login_status = LOGIN_UNDEFINED;
  static $login_message = '';

  /**
   * Имя, предлагаемое пользователю в качестве имени игрока
   *
   * @var string
   */
  protected $player_suggested_name = '';

  /**
   * Список полностью авторизированных аккаунтов с LOGIN_SUCCESS
   *
   * @var auth_local[]
   */
  protected $providers_authorised = array();
  /**
   * Статусы всех провайдеров
   *
   * @var auth_local[]
   */
  protected $provider_error_list = array();
  /**
   * @var string[]
   */
  protected $provider_error_messages = array();
  /**
   * Список юзеров (user_row - записей из `user`), доступных всем авторизированным аккаунтам
   *
   * @var array
   */
  protected $accessible_user_row_list = array();

  protected $user_id_to_provider = array();

  /**
   * Флаг имперсонации
   *
   * @var bool
   */
  protected $is_impersonating = false;
  protected $impersonator_username = '';

  /**
   * Флаг регистрации пользователя
   *
   * @var bool
   */
  protected $is_player_register = false;
  /**
   * @var int
   */
  protected $register_status = LOGIN_UNDEFINED;

  /**
   * Максимальный локальный уровень авторизации игрока
   *
   * @var int
   */
  public $auth_level_max_local = AUTH_LEVEL_ANONYMOUS;

  /**
   * @var int
   */
  public $partner_id = 0;

  /**
   * @var string
   */
  protected $server_name = '';

  /**
   * @param string $filename
   */
  // TODO - OK 4.7
  public function __construct($filename = __FILE__) {
    parent::__construct($filename);

    // В этой точке все модули уже прогружены и инициализированы по 1 экземпляру
    self::$db = SN::$db;

    self::$device = new RequestInfo();
    $this->is_player_register = sys_get_param('player_register') ? true : false;
    $this->partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id'));
    $this->server_name = sys_get_param_str_unsafe('server_name', SN_ROOT_VIRTUAL);

    self::$main_provider = new auth_local();
    SN::$gc->modules->registerModule(core_auth::$main_provider->manifest['name'], core_auth::$main_provider);
  }

  // TODO - OK v4.7
  public function player_register_model() {
    // TODO ВСЕГДА ПРЕДЛАГАТЬ РЕГАТЬ ИГРОКА ИЛИ ПОДКЛЮЧИТЬ ИМЕЮЩЕГОСЯ!

    // TODO в auth_local делать проверку БД на существование имени игрока в локальной БД - что бы избежать лишнего шага (см.выше)
    // TODO Хотя тут может получится вечный цикл - ПОДУМАТЬ
    // TODO Тут же можно пробовать провести попытку слияния аккаунтов - хотя это и очень небезопасно

    if(sys_get_param('login_player_register_logout')) {
      $this->logout();
    }

    $original_suggest = '';
    // Смотрим - есть ли у нас данные от пользователя
    if(($player_name_submitted = sys_get_param('submit_player_name'))) {
      // Попытка регистрации нового игрока из данных, введенных пользователем
      $this->player_suggested_name = sys_get_param_str_unsafe('player_suggested_name');
    } else {
      foreach($this->providers_authorised as $provider) {
        if($this->player_suggested_name = $provider->player_name_suggest()) { // OK 4.5
          $original_suggest = $provider->player_name_suggest();
          break;
        }
      }
    }

    // Если у нас провайдеры не дают имени и пользователь не дал свой вариант - это у нас первый логин в игру
    if(!$this->player_suggested_name) {
      $max_user_id = db_player_get_max_id(); // 4.5
      // TODO - предлагать имя игрока по локали

      // Проверить наличие такого имени в истории имён
      do {
        sn_db_transaction_rollback();
        $this->player_suggested_name = 'Emperor ' . mt_rand($max_user_id + 1, $max_user_id + 1000);
        sn_db_transaction_start();
      } while(db_player_name_exists($this->player_suggested_name));

    }

    if($player_name_submitted) {
      $this->register_player_db_create($this->player_suggested_name); // OK 4.5
      if($this->register_status == LOGIN_SUCCESS) {
        sys_redirect(SN_ROOT_VIRTUAL . 'overview.php');
      } elseif($this->register_status == REGISTER_ERROR_PLAYER_NAME_EXISTS && $original_suggest == $this->player_suggested_name) {
        // self::$player_suggested_name .= ' ' . $this->account->account_id;
      }
//      if(self::$login_status != LOGIN_SUCCESS) {
//        // TODO Ошибка при регистрации нового игрока под текущим именем
//      }
    }

  }

  // TODO - OK v4.7
  public function player_register_view($template = null) {
    global $template_result, $lang;

    define('LOGIN_LOGOUT', true);

    $template_result[F_PLAYER_REGISTER_MESSAGE] =
      isset($template_result[F_PLAYER_REGISTER_MESSAGE]) && $template_result[F_PLAYER_REGISTER_MESSAGE]
        ? $template_result[F_PLAYER_REGISTER_MESSAGE]
        : ($this->register_status != LOGIN_UNDEFINED
        ? $lang['sys_login_messages'][$this->register_status]
        : false
      );

    if($this->register_status == LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS) {
      $prohibited_characters = array_map(function($value) {
        return "'" . htmlentities($value, ENT_QUOTES, 'UTF-8') . "'";
      }, str_split(LOGIN_REGISTER_CHARACTERS_PROHIBITED));
      $template_result[F_PLAYER_REGISTER_MESSAGE] .= implode(', ', $prohibited_characters);
    }

    $template_result = array_merge($template_result, array(
      'NAVBAR' => false,
      'PLAYER_SUGGESTED_NAME' => sys_safe_output($this->player_suggested_name),
      'PARTNER_ID' => sys_safe_output($this->partner_id),
      'SERVER_NAME' => sys_safe_output($this->server_name),
      'PLAYER_REGISTER_STATUS' => $this->register_status,
      'PLAYER_REGISTER_MESSAGE' => $template_result[F_PLAYER_REGISTER_MESSAGE],
      'LOGIN_UNDEFINED' => LOGIN_UNDEFINED,
    ));

    $template = SnTemplate::gettemplate('login_player_register', $template);

    return $template;
  }

  /**
   * Функция пытается залогиниться по всем известным провайдерам
   *
   * @param null $result
   */
  // TODO - OK v4.5
  public function login() {
    global $lang;

    // !self::$is_init ? self::init() : false;

    if(!SN::$gc->modules->countModulesInGroup('auth')) {
      die('{Не обнаружено ни одного провайдера авторизации в core_auth::login()!}');
    }

    !empty($_POST) ? self::flog(dump($_POST, '$_POST')) : false;
    !empty($_GET) ? self::flog(dump($_GET, '$_GET')) : false;
    !empty($_COOKIE) ? self::flog(dump($_COOKIE,'$_COOKIE')) : false;

    $this->auth_reset(); // OK v4.5

    $this->providers = array();
    foreach(SN::$gc->modules->getModulesInGroup('auth', true) as $module_name => $module) {
      /**
       * @var auth_abstract $module
       */
      $this->providers[$module->provider_id] = $module;
    }

    // $this->providers = array_reverse($this->providers, true); // НИНАДА! СН-аккаунт должен всегда авторизироваться первым!
//pdump($this->providers);
    foreach($this->providers as $provider_id => $provider) {
      $login_status = $provider->login(); // OK v4.5
      self::flog(($provider->manifest['name'] . '->' . 'login_try - ') . (empty($provider->account->account_id) ? $lang['sys_login_messages'][$provider->account_login_status] : dump($provider)));
      if($login_status == LOGIN_SUCCESS && is_object($provider->account) && $provider->account instanceof Account && $provider->account->account_id) {
        $this->providers_authorised[$provider_id] = &$this->providers[$provider_id];

        $this->user_id_to_provider = array_replace_recursive(
          $this->user_id_to_provider,
          // static::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id) // OK 4.5
          PlayerToAccountTranslate::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id) // OK 4.5
        );
      } elseif($login_status != LOGIN_UNDEFINED) {
        $this->provider_error_list[$provider_id] = $login_status;
      }
    }

    if(empty($this->providers_authorised)) {
      // Ни один аккаунт не авторизирован
      // Проверяем - есть ли у нас ошибки в аккаунтах?
      if(!empty($this->provider_error_list)) {
        // Если есть - выводим их
        self::$login_status = reset($this->provider_error_list);
        $providerError = $this->providers[key($this->provider_error_list)]->account_login_message;

        if(!empty($providerError)) {
          self::$login_message = $providerError;
        }
      }
      // Иначе - это первый запуск страницы. ИЛИ СПЕЦИАЛЬНОЕ ДЕЙСТВИЕ!
      // ...которые по факты должны обрабатываться в рамках provider->login()
    } else {
      // Есть хотя бы один авторизированный аккаунт
      $temp = reset($this->providers_authorised);
      $this->account = $temp->account;

      $this->get_accessible_user_list();
      // В self::$accessible_user_row_list - список доступных игроков для данных аккаунтов с соответствующими записями из таблицы `users`

      // Остались ли у нас в списке доступные игроки?
      if(empty($this->accessible_user_row_list)) {
        // Нет ни одного игрока ни на одном авторизированном аккаунте
        // Надо регать нового игрока

        // Сейчас происходит процесс регистрации игрока?
        if(!$this->is_player_register) {
          // Нет - отправляем на процесс регистрации
          $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id'));
          sys_redirect(SN_ROOT_VIRTUAL . 'index.php?page=player_register&player_register=1' . ($partner_id ? '&id_ref=' . $partner_id : ''));
        }
      } else {
        // Да, есть доступные игроки, которые так же прописаны в базе
        $this->get_active_user(); // 4.5

        if($this->is_impersonating = !empty($_COOKIE[SN_COOKIE_U_I]) ? $_COOKIE[SN_COOKIE_U_I] : 0) {
          $a_user = db_user_by_id($this->is_impersonating);
          $this->impersonator_username = $a_user['username'];
        }


        //Прописываем текущего игрока на все авторизированные аккаунты
        // TODO - ИЛИ ВСЕХ ИГРОКОВ??
        if(empty($this->is_impersonating)) {
          foreach($this->providers_authorised as $provider_id => $provider) {
            if(empty($this->user_id_to_provider[self::$user['id']][$provider_id])) {
              // self::db_translate_register_user($provider_id, $provider->account->account_id, self::$user['id']);
              PlayerToAccountTranslate::db_translate_register_user($provider_id, $provider->account->account_id, self::$user['id']);
              $this->user_id_to_provider[self::$user['id']][$provider_id][$provider->account->account_id] = true;
            }
          }
        }
      }
    }

    if(empty(self::$user['id'])) {
      self::cookie_set(''); // OK 4.5
    } elseif(self::$user['id'] != $_COOKIE[SN_COOKIE_U]) {
      self::cookie_set(self::$user['id']); // OK 4.5
    }

    return $this->make_return_array();
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
  // OK v4.7
  public function logout($redirect = true) {
    if(!empty($_COOKIE[SN_COOKIE_U_I])) {
      self::cookie_set($_COOKIE[SN_COOKIE_U_I]);
      self::cookie_set(0, true);
      self::$main_provider->logout();
    } else {
      foreach($this->providers as $provider_name => $provider) {
        $provider->logout();
      }

      self::cookie_set(0);
    }

    if($redirect === true) {
      sys_redirect(SN_ROOT_RELATIVE . (empty($_COOKIE[SN_COOKIE_U]) ? 'login.php' : 'admin/overview.php'));
    } elseif($redirect !== false) {
      sys_redirect($redirect);
    }
  }

  /**
   * Имперсонация
   *
   * @param $user_selected
   */
  public function impersonate($user_selected) {
    if($_COOKIE[SN_COOKIE_U_I]) {
      die('You already impersonating someone. Go back to living other\'s life! Or clear your cookies and try again'); // TODO: Log it
    }

    if($this->auth_level_max_local < AUTH_LEVEL_ADMINISTRATOR) {
      die('You can\'t impersonate - too low level'); // TODO: Log it
    }

    if($this->auth_level_max_local <= $user_selected['authlevel']) {
      die('You can\'t impersonate this account - level is greater or equal to yours'); // TODO: Log it
    }

    $account_translate = PlayerToAccountTranslate::db_translate_get_account_by_user_id($user_selected['id'], self::$main_provider->provider_id);
    $account_translate = reset($account_translate[$user_selected['id']][self::$main_provider->provider_id]);
    $account_to_impersonate = new Account(self::$main_provider->db);
    $account_to_impersonate->db_get_by_id($account_translate['provider_account_id']);
    if(!$account_to_impersonate->is_exists) {
      die('Какая-то ошибка - не могу найти аккаунт для имперсонации'); // TODO: Log it
    }
    self::$main_provider->impersonate($account_to_impersonate);

    self::cookie_set($_COOKIE[SN_COOKIE_U], true, 0);

    // TODO - Имперсонейт - только на одну сессию
    self::cookie_set($user_selected['id']);

    // sec_set_cookie_by_user($user_selected, 0);
    sys_redirect(SN_ROOT_RELATIVE);
  }

  /**
   * Проверяет пароль на совпадение с текущими паролями
   *
   * @param $password_unsafe
   *
   * @return bool
   */
  // OK v4.6
  // TODO - ПЕРЕДЕЛАТЬ!
  public function password_check($password_unsafe) {
    $result = false;

    if(empty($this->providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("password_check: Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
    } else {
      foreach($this->providers_authorised as $provider_id => $provider) {
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
  public function password_change($old_password_unsafe, $new_password_unsafe) {
    global $lang;

    if(empty($this->providers_authorised)) {
      // TODO - такого быть не может!
      self::flog("Не найдено ни одного авторизированного провайдера в self::\$providers_authorised", true);
      return false;
    }

    // TODO - Проверять пароль на корректность

    // TODO - Не менять (?) пароль у аккаунтов, к которым пристёгнут хоть один игрок с AUTH_LEVEL > 0

    $salt_unsafe = self::password_salt_generate();

    $providers_changed_password = array();
    foreach($this->providers_authorised as $provider_id => $provider) {
      if(
        !$provider->is_feature_supported(AUTH_FEATURE_PASSWORD_CHANGE)
        || !$provider->password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe)
      ) {
        continue;
      }

      // Узнаем список игроков, которые прикреплены к этому аккаунту
      // $account_translation = self::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id);
      $account_translation = PlayerToAccountTranslate::db_translate_get_users_from_account_list($provider_id, $provider->account->account_id);

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
   * Сбрасывает значения полей
   */
  // OK v4.5
  protected function auth_reset() {
    self::$login_status = LOGIN_UNDEFINED;
    $this->player_suggested_name = '';
    $this->account = null;
    self::$user = null;
    $this->providers_authorised = array(); // Все аккаунты, которые успешно залогинились
    $this->provider_error_list = array(); // Статусы всех аккаунтов
    $this->accessible_user_row_list = array();
    $this->user_id_to_provider = array();
  }

  /**
   * Функция пытается создать игрока в БД, делая все нужные проверки
   *
   * @param $player_name_unsafe
   */
  // OK v4
  protected function register_player_db_create($player_name_unsafe) {
    try {
      // Проверить корректность имени
      $this->register_player_name_validate($player_name_unsafe);

      sn_db_transaction_start();
      // Проверить наличие такого имени в истории имён

      if(db_player_name_exists($player_name_unsafe)) {
        throw new Exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);
      }

      // Узнаем язык и емейл игрока
      $player_language = '';
      $player_email = '';
      // TODO - порнография - работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
      foreach($this->providers_authorised as $provider) {
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
      foreach($this->providers_authorised as $provider) {
        // TODO - порнография. Должен быть отдельный класс трансляторов - в т.ч. и кэширующий транслятор
        // TODO - ну и работа должна происходить над списком аккаунтов, а не только на одном аккаунте...
        // self::db_translate_register_user($provider->provider_id, $provider->account->account_id, $a_user['id']);
        PlayerToAccountTranslate::db_translate_register_user($provider->provider_id, $provider->account->account_id, $a_user['id']);

      }
      // Установить куку игрока
      self::cookie_set(self::$user['id']);

      sn_db_transaction_commit();
      $this->register_status = LOGIN_SUCCESS;
    } catch(Exception $e) {
      sn_db_transaction_rollback();

      // Если старое имя занято
      self::$user = null;
      $this->register_status == LOGIN_UNDEFINED ? $this->register_status = $e->getMessage() : false;
    }
  }


  /**
   * Проверяет доступ авторизированных аккаунтов к заявленным в трансляции юзерам
   * @version 4.5
   */
  // OK v4.5
  protected function get_accessible_user_list() {
    // Пробиваем все ИД игроков по базе - есть ли вообще такие записи
    // Вообще-то это не особо нужно - у нас по определению стоят констраинты
    // Зато так мы узнаем максимальный authlevel, проверим права имперсонейта и вытащим все записи юзеров
    foreach($this->user_id_to_provider as $user_id => $cork) {
      $user = db_user_by_id($user_id);
      // Если записи игрока в БД не существует?
      if(empty($user['id'])) {
        // Удаляем этого и переходим к следующему
        unset($this->user_id_to_provider[$user_id]);
        // Де-регистрируем игрока из таблицы трансляции игроков
        PlayerToAccountTranslate::db_translate_unregister_user($user_id);
      } else {
        $this->accessible_user_row_list[$user['id']] = $user;
        $this->auth_level_max_local = max($this->auth_level_max_local, $user['authlevel']);
      }
      unset($user);
    }
  }

  /**
   * Выбирает активного игрока из куки или из списка доступных игроков
   *
   * @version 4.5
   */
  // OK v4.5
  protected function get_active_user() {
    // Проверяем куку "текущего игрока" из браузера
    if(
      // Кука не пустая
      ($_COOKIE[SN_COOKIE_U] = trim($_COOKIE[SN_COOKIE_U])) && !empty($_COOKIE[SN_COOKIE_U])
      // И в куке находится ID
      && is_id($_COOKIE[SN_COOKIE_U])
      // И у аккаунтов есть права на данного игрока
      && (
        // Есть прямые права из `account_translate`
        !empty($this->accessible_user_row_list[$_COOKIE[SN_COOKIE_U]])
        // Или есть доступ через имперсонейт
        || (
          // Максимальные права всех доступных записей игроков - не ниже администраторских
          $this->auth_level_max_local >= AUTH_LEVEL_ADMINISTRATOR
          // И права игрока, в которого пытаются зайти - меньше текущих максимальных прав
          && $this->accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]['authlevel'] < $this->auth_level_max_local
        )
      )
    ) {
      // Берем текущим юзером юзера с ИД из куки
//      $this->is_impersonating = empty($this->accessible_user_row_list[$_COOKIE[SN_COOKIE_U]]);
      // self::$user = self::$accessible_user_row_list[$_COOKIE[SN_COOKIE_U]];
      self::$user = db_user_by_id($_COOKIE[SN_COOKIE_U]);
    }

    // В куке нет валидного ИД записи игрока, доступной с текущих аккаунтов
    if(empty(self::$user['id'])) {
      // Берем первого из доступных
      // TODO - default_user
      self::$user = reset($this->accessible_user_row_list);
    }
  }



  /**
   * Генерирует набор даннх для возврата в основной код
   */
  // OK v4.5
  protected function make_return_array() {
    global $config;

    $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;
    // if(!empty($user_id) && !$user_impersonator) {
    // $user_id не может быть пустым из-за констраинтов в таблице SPE
    // self::db_security_entry_insert();
    self::$device->db_security_entry_insert($user_id);

    $result = array();

    if($user_id && empty($this->is_impersonating)) {
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

    if(self::$login_message) {
      $result[F_LOGIN_MESSAGE] = self::$login_message;
    }

    $result[F_LOGIN_STATUS] = self::$login_status = empty($this->providers_authorised) ? self::$login_status : LOGIN_SUCCESS;
    $result[F_PLAYER_REGISTER_STATUS] = $this->register_status;
    $result[F_USER] = self::$user;

    // $result[AUTH_LEVEL] = isset(self::$user['authlevel']) ? self::$user['authlevel'] : AUTH_LEVEL_ANONYMOUS;
    $result[AUTH_LEVEL] = $this->auth_level_max_local;

    $result[F_IMPERSONATE_STATUS] = $this->is_impersonating;
    $result[F_IMPERSONATE_OPERATOR] = $this->impersonator_username;
    // TODO
//    self::$hidden[F_IMPERSONATE_OPERATOR] = $found_provider->data[F_IMPERSONATE_OPERATOR];

    //TODO Сол и Парол тоже вкинуть в хидден
    $result[F_ACCOUNTS_AUTHORISED] = $this->providers_authorised;

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
  protected function register_player_name_validate($player_name_unsafe) {
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
  public static function make_password_reset_code() {
    return sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
  }
  /**
   * Генерирует случайный пароль
   *
   * @return string
   */
  // OK v4
  public static function make_random_password() {
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
  public static function password_encode($password, $salt) {
    return md5($password . $salt);
  }
  /**
   * Генерирует соль
   *
   * @return string
   */
  // OK v4
  public static function password_salt_generate() {
    // НЕ ПЕРЕКРЫВАТЬ
    // TODO ВКЛЮЧИТЬ ГЕНЕРАЦИЮ СОЛИ !!!
    return ''; // sys_random_string(16);
  }

  // OK v4.5
  // TODO - REMEMBER_ME
  protected static function cookie_set($value, $impersonate = false, $period = null) {
    sn_setcookie($impersonate ? SN_COOKIE_U_I : SN_COOKIE_U, $value, $period === null ? SN_TIME_NOW + PERIOD_YEAR : $period, SN_ROOT_RELATIVE);
  }

  protected static function flog($message, $die = false) {
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

    SN::log_file("$message - $caller_name");
    if($die) {
      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
    }
  }

}
