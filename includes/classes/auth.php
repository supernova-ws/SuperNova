<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:51
 *
 * version #40a10.0#
 */

class auth extends sn_module {
  public $manifest = array(
    'package' => 'core',
    'name' => 'auth',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a10.0# copyright © 2009-2015 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'load_order' => 1,

    'installed' => true,
    'active' => true,
  );
  protected static $is_init = false;
  /**
   * @var auth_local[]
   */
  protected static $providers = array();
  // protected static $provider_data = array();
  // Скрытые данные - общие для всех аккаунтов
  protected static $hidden = array(
    F_LOGIN_STATUS => LOGIN_UNDEFINED,
  );
  // Этот список можно строить динамично по $login_methods_supported каждого модуля
  // Локальные данные для каждого метода
  // public $data = array();
  /**
   * @var auth $auth
   */
  // protected static $auth;
  public static $login_methods_list = array(
    'login_cookie',
    'register',
    'login_username',
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
  static $accounts_authorised = array();
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


  /**
   * @param $variable
   * @param $variable_id
   * @param $field_value
   * @param $db_field_id
   * @param $db_table_name
   * @param $db_field_name
   */
  public static function db_get_set_unique_value_by_id(&$variable, &$variable_id, $field_value, $db_field_id, $db_table_name, $db_field_name) {
    $browser_safe = db_escape($variable = $field_value);
    $browser_id = doquery("SELECT `{$db_field_id}` AS id_field FROM {{{$db_table_name}}} WHERE `{$db_field_name}` = '{$browser_safe}' LIMIT 1 FOR UPDATE", true);
    if(!isset($browser_id['id_field']) || !$browser_id['id_field']) {
      doquery("INSERT INTO {{{$db_table_name}}} (`{$db_field_name}`) VALUES ('{$browser_safe}');");
      $variable_id = db_insert_id();
    } else {
      $variable_id = $browser_id['id_field'];
    }
  }

  public static function init() {
    // В этой точке все модули уже прогружены и инициализированы по 1 экземпляру
    if(self::$is_init) {
      return;
    }

    self::init_device_and_browser();

    global $sn_module_list;

    self::$providers = array();

    foreach($sn_module_list['auth'] as $module_name => $module) {
      if($module_name != 'auth_provider') {
        // Провайдеры подготавливают для себя данные
        self::$providers[$module->manifest['provider_id']] = $module;
        self::$providers[$module->manifest['provider_id']]->prepare();
      }
    }

    self::$providers = array_reverse(self::$providers, true);

    self::$is_init = true;

    // TODO НЕПРАВИЛЬНО! Здесь надо брать флаги из провайдеров!
    self::$hidden += array(
      F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
      F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
      F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
    );
  }

  /**
   * Функция проверяет корректность имени игрока
   *
   * @param $player_name_unsafe
   *
   * @throws exception
   */
  // OK v4
  public static function register_player_validate_name($player_name_unsafe) {
    // $player_name_safe = db_escape($player_name_unsafe);

    // Проверяем, что бы в начале и конце не было пустых символов
    if($player_name_unsafe != trim($player_name_unsafe)) {
      throw new exception(REGISTER_ERROR_PLAYER_NAME_TRIMMED, ERR_ERROR);
    }
    // Если имя игрока пустое - NO GO!
    if(empty($player_name_unsafe)) {
      throw new exception(REGISTER_ERROR_PLAYER_NAME_EMPTY, ERR_ERROR);
    }
    // Если логин имеет запрещенные символы - NO GO!
    if(strpbrk($player_name_unsafe, LOGIN_REGISTER_CHARACTERS_PROHIBITED)) {
      throw new exception(REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS, ERR_ERROR);
    }
    // Если логин меньше минимальной длины - NO GO!
    if(strlen($player_name_unsafe) < LOGIN_LENGTH_MIN) {
      throw new exception(REGISTER_ERROR_PLAYER_NAME_SHORT, ERR_ERROR);
    }
  }
  /**
   * Функция проверяет наличие имени игрока в базе
   *
   * @param $player_name_unsafe
   *
   * @throws exception
   */
  // OK v4
  public static function register_player_check_db($player_name_unsafe) {
    sn_db_transaction_check(true);
    $player_name_safe = db_escape($player_name_unsafe);
    $player_name_exists = doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` = '{$player_name_safe}' LIMIT 1 FOR UPDATE", true);
    if(!empty($player_name_exists)) {
      throw new exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);
    }
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
      self::register_player_validate_name($player_name_unsafe);

      sn_db_transaction_start();
      // Проверить наличие такого имени в истории имён
      self::register_player_check_db($player_name_unsafe);

      // TODO Создаем игрока

      // Узнаем язык и емейл игрока
      $player_language = '';
      $player_email = '';
      foreach(self::$accounts_authorised as $provider) {
        if(!$player_language && $provider->data[F_ACCOUNT]['account_language']) {
          $player_language = $provider->data[F_ACCOUNT]['account_language'];
        }
        if(!$player_email && $provider->data[F_ACCOUNT]['account_email']) {
          $player_email = $provider->data[F_ACCOUNT]['account_email'];
        }
      }
      $player_language = sys_get_param_str('lang') ? sys_get_param_str('lang') : $player_language;
      $player_language = $player_language ? $player_language : DEFAULT_LANG;

      self::$user = player_create($player_name_unsafe, $player_email, array(
        'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
        'language_iso' => db_escape($player_language),
        // 'password_encoded_unsafe' => $this->data[F_ACCOUNT]['account_password'],
        // 'salt' => $this->data[F_ACCOUNT]['account_salt'],
        // 'remember_me' => $this->data[F_REMEMBER_ME_SAFE],
      ));
      // Зарегестрировать на него аккаунты из self::$accounts_authorised
      $a_user = self::$user;
      foreach(self::$accounts_authorised as $provider) {
        doquery(
          "INSERT INTO `{{account_translate}}` (`provider_id`, `provider_account_id`, `user_id`) VALUES
                  ({$provider->manifest['provider_id']}, {$provider->data[F_ACCOUNT]['account_id']}, {$a_user['id']});"
        );
      }
      // TODO Установить куку игрока
      sn_setcookie(SN_COOKIE_U, self::$user['id'], SN_TIME_NOW + PERIOD_YEAR);

      sn_db_transaction_commit();
      self::$login_status = LOGIN_SUCCESS;
    } catch(exception $e) {
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
  //
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
      foreach(self::$accounts_authorised as $provider) {
        if(self::$player_suggested_name = $provider->suggest_player_name()) {
          break;
        }
      }
    }

    // Если у нас провайдеры не дают имени и пользователь не дал свой вариант - это у нас первый логин в игру
    if(!self::$player_suggested_name) {
      $max_user_id = doquery("SELECT MAX(`id`) as `max_user_id` FROM `{{user}}`", true);
      $max_user_id = $max_user_id['max_user_id'];
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
  public static function login(&$result = null) {
    !self::$is_init ? self::init() : false;

    self::$login_status = LOGIN_UNDEFINED;
    self::$player_suggested_name = '';
    self::$account = null;
    self::$user = null;
    self::$accounts_authorised = array(); // Все аккаунты, которые успешно залогинились
    self::$account_error_list = array(); // Статусы всех аккаунтов
    self::$accessible_user_row_list = array();

    self::flog('starting sequence');
    self::flog('$_POST = ' . dump($_POST));
    self::flog('$_GET = ' . dump($_GET));
    self::flog('$_COOKIE = ' . dump($_COOKIE));

    global $lang, $config;

    // Максимальный уровень авторизации всех записей игроков, к которым аккаунты имеют доступ
    $local_max_auth_level = AUTH_LEVEL_ANONYMOUS;
    $local_user_to_provider_list = array(); // Локальная переменная

    foreach(self::$providers as $provider_id => $provider) {
      self::flog(($provider->manifest['name'] . '->' . 'login_try') . (!$provider->data[F_ACCOUNT_ID] ? $lang['sys_login_messages'][$provider->data[F_LOGIN_STATUS]] : dump($provider->data)));
      $login_status = $provider->login_try();
      if($login_status == LOGIN_SUCCESS && $provider->data[F_ACCOUNT]['account_id']) {
        self::$accounts_authorised[$provider_id] = &self::$providers[$provider_id];
        $attached_users = doquery("SELECT * FROM {{account_translate}} WHERE `provider_id` = {$provider->manifest['provider_id']} AND `provider_account_id` = {$provider->data[F_ACCOUNT]['account_id']}");
        while($row = db_fetch($attached_users)) {
          $local_user_to_provider_list[$row['user_id']][$provider_id] = $row;
        }
      } elseif($login_status != LOGIN_UNDEFINED) {
        self::$account_error_list[$provider_id] = $login_status;
      }
    }


    if(empty(self::$accounts_authorised)) {
      // Ни один аккаунт не авторизирован
      // Проверяем - есть ли у нас ошибки в аккаунтах?
      if(!empty(self::$account_error_list)) {
        // Если есть - выводим их
        self::$login_status = reset(self::$account_error_list);
      } else {
        // Иначе - это первый запуск страницы. ИЛИ СПЕЦИАЛЬНОЕ ДЕЙСТВИЕ!
        self::password_reset_send_code();
        self::password_reset_confirm(); // Если успешно - сюда мы не вернемся, а уйдём на редирект

//        if(($result_password_reset = self::password_reset()) != LOGIN_UNDEFINED) {
//          self::$hidden[F_LOGIN_STATUS] = $result_password_reset;
//        } elseif(($result_password_reset = self::password_reset_confirm()) != LOGIN_UNDEFINED) {
//          self::$hidden[F_LOGIN_STATUS] = $result_password_reset;
//          // self::$hidden[F_LOGIN_STATUS] = $result_password_reset[F_LOGIN_STATUS];
//          // self::$hidden[F_LOGIN_MESSAGE] = $result_password_reset[F_LOGIN_MESSAGE];
//        }
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

    self::login_process();
    self::extract_to_hidden();

    $result = self::$hidden;

    return $result;

//    $suggested_name = '';
    // TODO - Нет игрока, к которому аккаунты имеют доступ
    if(empty(self::$user['id'])) {
      // Значит у нас - новый игрок или какие-то спец-действия

      // Может это у нас - регистрация нового игрока?
      if(!empty(self::$accounts_authorised)) {




        {
          // Предлагаем имя игроку. Оно должно браться из аккаунтов
          $first_authorised_account = reset(self::$accounts_authorised);
          $suggested_name = $first_authorised_account->data[F_ACCOUNT]['account_name'];
          // TODO Придумать вариант, если пустое
          self::$hidden[F_LOGIN_SUGGESTED_NAME] = $suggested_name;
          $this->data[F_LOGIN_STATUS] == LOGIN_UNDEFINED ? $this->data[F_LOGIN_STATUS] = $e->getMessage() : false;
        }
      }

      {
        // ИЛИ ОШИБКА!
      }

      die('{New user create OR error}');
      // Стираем куку юзера
    }


    // $account = null;
    {
      // Таки есть юзер, к которому текущие аккаунты имеют права доступа
      // В качестве аккаунта берем первый попавшийся из имеющих LOGIN_SUCCESS
      // TODO НИХУЯ! НАДО БРАТЬ ПЕРВОГО С LOGIN_SUCCESS
      self::$account = reset($local_user_to_provider_list[self::$user['id']]);
      // Устанавливаем новую куку юзера или обновляем старую - в зависимости от предыдущего workflow
      sn_setcookie(SN_COOKIE_U, self::$user['id'], SN_TIME_NOW + PERIOD_YEAR);
//pdump($user_list);
      die('Past set cookie');
    }

    // Здесь у нас возможны варианты:
    //    - ИЛИ валидный User и есть хотя бы один аккаунт, который может с ним работать
    //    - ИЛИ eсть код ошибки
    // TODO: По нормальным делам - тут надо определять какие аккаунты умеют в этого игрока и узнать у них поддерживаемые фишки. Как-то:
    //    - смена пароля
    //    - смена емейла
    //    - итд



//    $auth_level = AUTH_LEVEL_ANONYMOUS;
//    $login_status = LOGIN_UNDEFINED;
//    self::$account = reset($user_list[$user['id']]);
//    self::$user = $user;
//    $auth_level = self::$user['authlevel']; // По итогу должен быть передан в основной код аут_левел найденного игрока - что бы видеть 1 к 1 его экран в случае имперсонейта
    //self::$account_status_list[$provider_id]



    // У найденного провайдера ВСЕГДА должен быть выставлен F_ACCOUNT_ID и F_ACCOUNT!
    if($found_provider && $found_provider->data[F_ACCOUNT_ID]) {
      self::flog(dump($found_provider->data));
      // Безопасно. Если пользователь уже подгружен - ничего не произойдет
      $found_provider->load_user_data();

      // Если пользователь не найден - значит у нас первый логин с этого аккаунта. Надо создать или прилинковать нового пользователя
      if(!$found_provider->data[F_USER]) {
        static::flog("Почему-то нет пользователя", true);
        // TODO Аккаунт мог быть удален по блокировке. Или отправлен на хранение

        // Создаем пользователя
        // TODO - Решить, что делать если емейл аккаунта, который заходит на сервер в первый раз совпадает с емейлом уже существующего игрока, но при этом не совпадают пароли
        $found_provider->user_create_from_account();

        // Регистрация меняет статус аккаунта на LOGIN_SUCCESS
        $found_provider->register_account();
      }

      // В этой точке у нас уже есть и созданный пользователь и созданный аккаунт хотя бы по одному провайдеру
      // Так же юзер уже зарегестрирован на аккаунт, а операция - LOGIN_SUCCESS

      // Проверяем всех провайдеров
      foreach(self::$providers as $provider_id => $provider) {
        // Если провайдер не вошел своим аккаунтом - создаем для него аккаунт по данным успешного провайдера
        // TODO - Решить, что делать если емейл аккаунта, который заходит на сервер в первый раз совпадает с емейлом уже существующего игрока, но при этом не совпадают пароли
        $provider->db_create_account_from_provider($found_provider);
        // ПРОВАЙДЕР МОЖЕТ НЕ ПОДДЕРЖИВАТЬ ТАКОЕ СОЗДАНИЕ АККУНТОВ! ЭТО НОРМАЛЬНО
        // НО ТОГДА ОН ДОЛЖЕН ВЫСТАВЛЯТЬ СТАТУС LOGIN_SUCCESS И ВСЁ РАВНО ПРИНИМАТЬ ПОЛЬЗОВАТЕЛЯ!
      }

      // В этой точке все провайдеры ЛИБО имеют какие-то аккаунты, ЛИБО отказались создать их. ОДНАКО! У НИХ ВСЁ РАВНО ВСТАВЛЕН LOGIN_SUCCESS И ПРИНЯТ ПОЛЬЗОВАТЕЛЬ!
      foreach(self::$providers as $provider_id => $provider) {
        // Если пользователь провайдера не равен текущему пользователю НО У НЕГО ЕСТЬ АККАУНТ - регестрируем аккаунт на нового пользователя
        if(($provider->data[F_USER_ID] != $found_provider->data[F_USER_ID]) && $provider->data[F_ACCOUNT_ID]) {
          $provider->data[F_USER_ID] = 0;
          $provider->data[F_USER] = $found_provider->data[F_USER];
          $provider->register_account();
        }
      }

      // В этой точке У ВСЕХ провайдеров есть пользователи и ВСЕ провайдеры имеют статус LOGIN_SUCCESS, а так же на всех провайдеров с ACCOUNT_ID зарегестрирован текущий пользователь
    } else {
      // Ищем провайдера с ошибкой
      $found_provider = null;
      foreach(self::$providers as $provider_id => $provider) {
        if($provider->data[F_LOGIN_STATUS] != LOGIN_UNDEFINED) {
          $found_provider = $provider;
          break;
        }
      }

      // Нет ошибок - тогда берем первого попавшегося
      if(!$found_provider) {
        $found_provider = reset(self::$providers);
      }
    }

    // pdump($found_provider);die();
    // Сверить еще ИД юзеров, которые принадлежат кажому аккаунту - что бы коллизия не случилась
    // Ну и на аккаунте может быть больше одного ЮЗЕРА - официальные мультики

    self::login_process($found_provider);

    if(($result_password_reset = self::password_reset_send_code()) != LOGIN_UNDEFINED) {
      self::$hidden[F_LOGIN_STATUS] = $result_password_reset;
    } elseif(($result_password_reset = self::password_reset_confirm()) != LOGIN_UNDEFINED) {
      self::$hidden[F_LOGIN_STATUS] = $result_password_reset;
      // self::$hidden[F_LOGIN_STATUS] = $result_password_reset[F_LOGIN_STATUS];
      // self::$hidden[F_LOGIN_MESSAGE] = $result_password_reset[F_LOGIN_MESSAGE];
    }

    $result = self::$hidden;
  }
  /**
   * @param bool|string $redirect нужно ли сделать перенаправление после логаута
   * <p><b>false</b> - не перенаправлять</p>
   * <p><i><b>true</b></i> - перенаправить на главную страницу</p>
   * <p><b>string</b> - перенаправить на указанный URL</p>
   *
   * @param bool $only_impersonator Если установлен - то логаут происходит только при имперсонации
   */
  static function logout($redirect = true, $only_impersonator = false) {
//    global $user_impersonator;
//
//    if($only_impersonator && !$user_impersonator) {
//      return;
//    }

//    if($_COOKIE[SN_COOKIE_I] && $user_impersonator['authlevel'] >= 3) {
//      // TODO REWRITE
////      self::$auth->data[F_USER_ID] = $user_impersonator['id'];
////      self::$auth->data[F_ACCOUNT] = db_account_by_user($user_impersonator);
////      self::$auth->data[F_ACCOUNT_ID] = self::$auth->data[F_ACCOUNT]['id'];
////      self::$auth->cookie_set(); // TODO REWRITE
////      $redirect = $redirect === true ? 'admin/userlist.php' : $redirect;
//    } else
//    {
//      sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
//    }
//
//    sn_setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);

    foreach(self::$providers as $provider_name => $provider) {
      $provider->logout_do();
    }

    if($redirect === true) {
      sys_redirect(SN_ROOT_RELATIVE . (empty($_COOKIE[SN_COOKIE]) ? 'login.php' : 'admin/overview.php'));
    } elseif($redirect !== false) {
      sys_redirect($redirect);
    }
  }

  public static function impersonate($user_selected) {
    global $config;

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
    $password_encoded = md5("{$user_selected['password']}--{$config->secret_word}");
    $cookie = $user_selected['id'] . AUTH_COOKIE_DELIMETER . $password_encoded . AUTH_COOKIE_DELIMETER . '1';
    sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);

    // sec_set_cookie_by_user($user_selected, 0);
    sys_redirect(SN_ROOT_RELATIVE);
  }

  static function email_set($new_email_unsafe) {
    self::flog('Это пока не работает', true);

    foreach(self::$providers as $provider) {
      // TODO - только для провайдеров, которые поддерживают смену емейла
      if($provider->data[F_ACCOUNT_ID] && !$provider->data[F_ACCOUNT]['account_email']) {
        $provider->email_set_do($new_email_unsafe);
      }
    }
  }

  static function password_check($old_password_unsafe) {
    $return = false;

    // $old_password_unsafe === false - ничего не менять
    $found_provider = null;
    foreach(self::$providers as $provider) {
      // TODO - только для провайдеров, которые поддерживают смену пароля
      if($provider->data[F_ACCOUNT_ID]) {
        $found_provider = $provider;
        break;
      }
    }

    return $found_provider && $found_provider->auth_password_check($old_password_unsafe);
  }

  static function password_change($old_password_unsafe, $new_password_unsafe) {
    global $lang;

    $return = false;

    // $old_password_unsafe === false - ничего не менять
    $found_provider = null;
    foreach(self::$providers as $provider) {
      // TODO - только для провайдеров, которые поддерживают смену пароля
      if($provider->data[F_ACCOUNT_ID]) {
        $found_provider = $provider;
        break;
      }
    }

    if(!$found_provider) {
      self::flog("Не найдено ни одного провайдера, у которого можно сменить пароль", true);
    }

    $salt_unsafe = self::password_salt_generate();

    if($found_provider->real_password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe)) {
      // Менять везде совпадающие пароли
      // TODO - только для провайдеров, которые поддерживают смену пароля
      foreach(self::$providers as $provider) {
        if($provider != $found_provider) {
          $provider->real_password_change($old_password_unsafe, $new_password_unsafe, $salt_unsafe);
        }
      }
//      foreach(self::$providers as $provider) {
//        // TODO - только для провайдеров, которые поддерживают смену пароля
//        if($provider->data[F_ACCOUNT_ID]) {
//          $found_provider = $provider;
//          break;
//        }
//      }
      // $found_provider = reset(self::$providers);
      msg_send_simple_message($found_provider->data[F_USER_ID], 0, SN_TIME_NOW, MSG_TYPE_ADMIN,
        $lang['sys_administration'], $lang['sys_login_register_message_title'],
        sprintf($lang['sys_login_register_message_body'], $found_provider->data[F_ACCOUNT]['account_name'], $new_password_unsafe), true
      );
      $return = true;
    }

    return $return;
  }

  /**
   * Возвращает из `account_translate` список пользователей, которые прилинкованы к списку аккаунтов на указанном провайдере
   *
   * @param int $provider_id_unsafe Идентификатор провайдера авторизации
   * @param int|int[] $account_list
   *
   * @return array
   */
  static function db_get_account_translation_from_account_list($provider_id_unsafe, $account_list) {
    $provider_id_safe = intval($provider_id_unsafe);
    !array($account_list) ? $account_list = array($account_list) : false;
    $account_translation = array();

    foreach($account_list as $provider_account_id_unsafe) {
      $provider_account_id_safe = intval($provider_account_id_unsafe);

      $query = doquery("SELECT `user_id` FROM {{account_translate}} WHERE `provider_id` = {$provider_id_safe} AND `provider_account_id` = {$provider_account_id_safe} FOR UPDATE");
      while($row = db_fetch($query)) {
        $account_translation[$row['user_id']][$provider_id_safe] = $provider_account_id_safe;
      }
    }

    return $account_translation;
  }


//  static function db_confirmation_set($account_id_safe, $confirmation_type_safe, $email_safe) {
//    $confirmation = $this->db_confirmation_by_account_id($account_id_safe, CONFIRM_PASSWORD_RESET);
//    if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
//      throw new exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN);
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
   * Отсылает письмо с кодом подтверждения для сброса пароля
   *
   * @return int|string
   */
  // OK v4
  static function password_reset_send_code() {
    if(!self::$hidden[F_IS_PASSWORD_RESET]) {
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
      $account_list = $provider->db_get_accounts_on_email($email_unsafe);
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
//            throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
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

      sn_db_transaction_start();
      $confirmation = doquery(
        "SELECT * FROM {{confirmations}} WHERE
          `type` = " . CONFIRM_PASSWORD_RESET . " AND `email` = '{$email_safe}' ORDER BY create_time DESC LIMIT 1;", true);

      if(isset($confirmation['create_time']) && SN_TIME_NOW - strtotime($confirmation['create_time']) < PERIOD_MINUTE_10) {
        throw new Exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN, ERR_ERROR);
      }

      // Удаляем предыдущие записи продтверждения сброса пароля
      !empty($confirmation['id']) or doquery("DELETE FROM {{confirmations}} WHERE `type` = " . CONFIRM_PASSWORD_RESET . " AND `email` = '{$email_safe}';");

      do {
        // Ну, если у нас > 999.999.999 подтверждений - тут нас ждут проблемы...
        $confirm_code_safe = db_escape($confirm_code_unsafe = self::make_password_reset_code());
        $query = doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = " . CONFIRM_PASSWORD_RESET . " FOR UPDATE", true);
      } while($query);

      doquery(
        "REPLACE INTO {{confirmations}}
        SET `type` = " . CONFIRM_PASSWORD_RESET . ", `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");

      // return $confirm_code_unsafe;
      sn_db_transaction_commit();

      global $lang, $config;

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
////        F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
////        F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
////        F_PASSWORD_RESET_CODE => sys_get_param_str('password_reset_code'),
//
//    $found_provider = null;
//    $account = null;
//    try {
//      sn_db_transaction_start();
//      foreach(self::$providers as $provider_name => $provider) {
//        // if(method_exists($provider, $method_name)) // TODO проверять поддержку сброса пароля
//        if($provider->data[F_INPUT][F_EMAIL_UNSAFE] && $provider->data[F_INPUT][F_IS_PASSWORD_RESET]) {
//          $account = $provider->db_account_by_email($provider->data[F_INPUT][F_EMAIL_UNSAFE]);
//          if($account) {
//            $user = $provider->db_user_id_by_provider_account_id($account['account_id']);
//            // TODO - Проверять уровень доступа аккаунта!
//            if($user && $user['authlevel'] > 0) {
//              throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
//            }
//            $found_provider = $provider;
//            break;
//          }
//        }
//      }
//
//      if(!$found_provider || empty($account['account_email'])) {
//        throw new exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL);
//      }
//
//      $email_safe = db_escape($found_provider->data[F_INPUT][F_EMAIL_UNSAFE]);
//      $confirm_code_unsafe = $found_provider->db_confirmation_set($account['account_id'], CONFIRM_PASSWORD_RESET, $email_safe);
//      sn_db_transaction_commit();
//
//      @$result = mymail($found_provider->data[F_INPUT][F_EMAIL_UNSAFE],
//        sprintf($lang['log_lost_email_title'], $config->game_name),
//        sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . 'login.php', $confirm_code_unsafe, date(FMT_DATE_TIME, SN_TIME_NOW + 3*24*60*60), $config->game_name)
//      );
//
//      $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
//    } catch(exception $e) {
//      sn_db_transaction_rollback();
//      $result = $e->getMessage();
//    }
//
//    $found_provider ? $found_provider->data[F_LOGIN_STATUS] = $result : false;;
//
//    return $result;
  }

  static function password_reset_confirm() {
    global $lang, $config;

    if(!self::$hidden[F_IS_PASSWORD_RESET_CONFIRM]) {
      return LOGIN_UNDEFINED;
    }

    $code_safe = sys_get_param_str('password_reset_code');

    try {
      sn_db_transaction_start();
      $confirmation = doquery(
        "SELECT * FROM {{confirmations}} WHERE
          `type` = " . CONFIRM_PASSWORD_RESET . " AND `code` = '{$code_safe}' ORDER BY create_time DESC LIMIT 1;", true);

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
          $account_list = $provider->db_get_accounts_on_email($confirmation['email']);

          // Получаем список юзеров на этом аккаунте
          $this_provider_translation = self::db_get_account_translation_from_account_list($provider_id, array_keys($account_list));
          if(!empty($this_provider_translation)) {
            $account_translation = array_replace_recursive($account_translation, $this_provider_translation);
          }

          // Меняем пароль на всех аккаунтах
          foreach($account_list as $account_id => $account_data) {
            // TODO оно не меняет данных внутри аккаунта. Наверное, это верно...
            $provider_result = $provider->v4_db_password_set($account_id, $new_password_unsafe, $salt);
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

      doquery("DELETE FROM {{confirmations}} WHERE `type` = " . CONFIRM_PASSWORD_RESET . " AND `email` = '" . db_escape($confirmation['email']) . "'");

      sn_db_transaction_commit();
      sys_redirect('overview.php');
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      self::$login_status = $e->getMessage();
    }

    return self::$login_status;
  }



  // UNUSED??????????
  static function propagade_user($found_provider) {
    foreach(self::$providers as $provider_name => $provider) {
      $provider->db_create_account_from_provider($found_provider);

//      db_field_set_create('account_translate', array(
//        'provider_id' => $this->manifest['provider_id'],
//        'provider_account_id' => $this->data[F_ACCOUNT_ID],
//        'user_id' => $this->data[F_USER]['id'],
//      ));
    }
  }
  // UNUSED??????????
  // static function password_check($old_password_unsafe) {  }

  static function password_encode($password, $salt) {
    return md5($password . $salt);
  }
  static function password_salt_generate() {
    // НЕ ПЕРЕКРЫВАТЬ
    // TODO ВКЛЮЧИТЬ ГЕНЕРАЦИЮ СОЛИ !!!
    return ''; // sys_random_string(16);
  }
  static function sec_player_ip() {
    $ip = array(
      'ip' => $_SERVER["REMOTE_ADDR"],
      'proxy_chain' => $_SERVER["HTTP_X_FORWARDED_FOR"]
        ? $_SERVER["HTTP_X_FORWARDED_FOR"]
        : ($_SERVER["HTTP_CLIENT_IP"]
          ? $_SERVER["HTTP_CLIENT_IP"]
          : '' // $_SERVER["REMOTE_ADDR"]
        ),
    );

    return array_map('db_escape', $ip);
  }


  static function extract_to_hidden() {
    self::$hidden[F_LOGIN_STATUS] = self::$login_status = empty(self::$accounts_authorised) ? self::$login_status : LOGIN_SUCCESS;
//    self::$hidden[F_PROVIDER_ID] = $found_provider->manifest['provider_id'];
//    self::$hidden[F_ACCOUNT_ID] = $found_provider->data[F_ACCOUNT_ID];
//    self::$hidden[F_ACCOUNT] = $found_provider->data[F_ACCOUNT];
//    self::$hidden[F_USER_ID] = $found_provider->data[F_USER_ID];
    self::$hidden[F_USER] = self::$user;
//    self::$hidden[F_REMEMBER_ME_SAFE] = $found_provider->data[F_REMEMBER_ME_SAFE];

    self::$hidden[AUTH_LEVEL] = isset(self::$user['authlevel']) ? self::$user['authlevel'] : AUTH_LEVEL_ANONYMOUS;

    // TODO
//    self::$hidden[F_BANNED_STATUS] = $found_provider->data[F_BANNED_STATUS];
//    self::$hidden[F_VACATION_STATUS] = $found_provider->data[F_VACATION_STATUS];

    self::$hidden[F_IMPERSONATE_STATUS] = self::$is_impersonating;
    // TODO
//    self::$hidden[F_IMPERSONATE_OPERATOR] = $found_provider->data[F_IMPERSONATE_OPERATOR];

    //TODO Сол и Парол тоже вкинуть в хидден
    self::$hidden[F_ACCOUNTS_AUTHORISED] = self::$accounts_authorised;
  }
  static function login_process() {
    global $config, $sys_stop_log_hit, $is_watching;

    $ip = self::sec_player_ip();
    $ip_int_safe = ip2longu($ip['ip']);
    $proxy_safe = db_escape($ip['proxy_chain']);

    $user_id = !empty(self::$user['id']) ? self::$user['id'] : 0;
    // if(!empty($user_id) && !$user_impersonator) {
    // $user_id не может быть пустым из-за констраинтов в таблице SPE
    if($user_id && !self::$is_impersonating) {
      doquery(
        "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
        VALUES ({$user_id}," . self::$hidden[F_DEVICE_ID] . "," . self::$hidden[F_BROWSER_ID]. ",{$ip_int_safe}, '{$proxy_safe}');"
      );

      if(!$sys_stop_log_hit && $config->game_counter) {
        sn_db_transaction_start();
        $is_watching = true;
        self::db_get_set_unique_value_by_id(self::$hidden[F_PAGE], self::$hidden[F_PAGE_ID], $_SERVER['PHP_SELF'], 'url_id', 'security_url', 'url_string');
        self::db_get_set_unique_value_by_id(self::$hidden[F_URL], self::$hidden[F_URL_ID], $_SERVER['REQUEST_URI'], 'url_id', 'security_url', 'url_string');

        doquery(
          "INSERT INTO {{counter}}
          (`visit_time`, `user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`, `page_url_id`, `plain_url_id`)
        VALUES
          ('" . SN_TIME_SQL. "', {$user_id}, " . self::$hidden[F_DEVICE_ID] . "," . self::$hidden[F_BROWSER_ID] . ",
            {$ip_int_safe},'{$proxy_safe}', " . self::$hidden[F_PAGE_ID] . ", " . self::$hidden[F_URL_ID] . ");");
        $is_watching = false;
        sn_db_transaction_commit();
      }

      $user = &self::$user;

      sys_user_options_unpack($user);

      if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
        $user['banaday'] = 0;
        $user['vacation'] = SN_TIME_NOW;
      }

      $user['user_lastip'] = $ip['ip'];
      $user['user_proxy'] = $ip['proxy_chain'];

      // TODO
      // $found_provider->data[F_BANNED_STATUS] = $user['banaday'];
      // $found_provider->data[F_VACATION_STATUS] = $user['vacation'];

      db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ",
      `banaday` = " . db_escape($user['banaday']) . ", `vacation` = " . db_escape($user['vacation']) . ",
      `user_lastip` = '" . db_escape($user['user_lastip']) . "', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = " . self::$hidden[F_BROWSER_ID]
      );
    }

    if($extra = $config->security_ban_extra) {
      $extra = explode(',', $extra);
      array_walk($extra,'trim');
      in_array(self::$hidden[F_DEVICE_ID], $extra) and die();
    }

//    self::extract_to_hidden($found_provider);
//
//    if($found_provider->data[F_LOGIN_STATUS] != LOGIN_SUCCESS) {
//      return;
//    }
//
//    if($user_id) {
//      $user = &$found_provider->data[F_USER];
//
//      sys_user_options_unpack($user);
//
//      if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
//        $user['banaday'] = 0;
//        $user['vacation'] = SN_TIME_NOW;
//      }
//
//      $user['user_lastip'] = $ip['ip'];
//      $user['user_proxy'] = $ip['proxy_chain'];
//
//      $found_provider->data[F_BANNED_STATUS] = $user['banaday'];
//      $found_provider->data[F_VACATION_STATUS] = $user['vacation'];
//
//      db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ",
//      `banaday` = " . db_escape($user['banaday']) . ", `vacation` = " . db_escape($user['vacation']) . ",
//      `user_lastip` = '" . db_escape($user['user_lastip']) . "', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = " . self::$hidden[F_BROWSER_ID]
//      );
//    }

    // Не должно никуда уходить
//    unset($result[F_DEVICE_ID]);
//    unset($result[F_DEVICE_CYPHER]);
  }

  static function init_device_and_browser() {
    // Инфа об устройстве и браузере - общая для всех
    sn_db_transaction_start();
    self::$hidden[F_DEVICE_CYPHER] = $_COOKIE[SN_COOKIE_D];
    if(self::$hidden[F_DEVICE_CYPHER]) {
      $cypher_safe = db_escape(self::$hidden[F_DEVICE_CYPHER]);
      $device_id = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
      if(!empty($device_id['device_id'])) {
        self::$hidden[F_DEVICE_ID] = $device_id['device_id'];
      }
    }

    if(self::$hidden[F_DEVICE_ID] <= 0) {
      do {
        $cypher_safe = db_escape(self::$hidden[F_DEVICE_CYPHER] = sys_random_string());
        $row = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
      } while (!empty($row));
      doquery("INSERT INTO {{security_device}} (`device_cypher`) VALUES ('{$cypher_safe}');");
      self::$hidden[F_DEVICE_ID] = db_insert_id();
      sn_setcookie(SN_COOKIE_D, self::$hidden[F_DEVICE_CYPHER], PERIOD_FOREVER, SN_ROOT_RELATIVE);
    }
    sn_db_transaction_commit();

    sn_db_transaction_start();
    self::db_get_set_unique_value_by_id(self::$hidden[F_BROWSER], self::$hidden[F_BROWSER_ID], $_SERVER['HTTP_USER_AGENT'], 'browser_id', 'security_browser', 'browser_user_agent');
    sn_db_transaction_commit();
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
}
