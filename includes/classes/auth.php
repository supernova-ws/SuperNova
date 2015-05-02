<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:51
 *
 * version #40a0.21#
 */

class auth extends sn_module {
  public $manifest = array(
    'package' => 'core',
    'name' => 'auth',
    'version' => '0a0',
    'copyright' => 'Project "SuperNova.WS" #40a0.21# copyright © 2009-2015 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'load_order' => 1,

    'installed' => true,
    'active' => true,
  );
  protected static $is_init = false;
  /**
   * @var auth_basic[]
   */
  protected static $providers = array();
  protected static $provider_data = array();
  // Скрытые данные - общие для всех аккаунтов
  protected static $hidden = array(
    F_LOGIN_STATUS => LOGIN_UNDEFINED,
  );
  // Этот список можно строить динамично по $login_methods_supported каждого модуля
  // Локальные данные для каждого метода
  protected $data = array();
  /**
   * @var auth $auth
   */
  // protected static $auth;
  public static $login_methods_list = array(
    'login_cookie',
    'register',
    'login_username',
  );

  public static function init() {
    // В этой точке все модули уже прогружены и инициализированы по 1 экземпляру
    if(self::$is_init) {
      return;
    }

    self::init_device_and_browser();

    global $sn_module_list;

    foreach($sn_module_list['auth'] as $module_name => $module) {
      $module_name != 'auth_provider' ? self::$providers[$module_name] = $module : false;
    }

    self::$providers = array_reverse(self::$providers, true);

    self::$is_init = true;

    self::$hidden += array(
      F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
      F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
      F_PASSWORD_RESET_CODE_SAFE => sys_get_param_str('password_reset_code'),
    );
  }
  public static function login(&$result = null) {
    !self::$is_init ? self::init() : false;

    self::flog('starting sequence');
    self::flog('$_POST = ' . dump($_POST));
    self::flog('$_GET = ' . dump($_GET));
    self::flog('$_COOKIE = ' . dump($_COOKIE));

    global $lang, $config;

    // Провайдеры подготавливают для себя внешние данные
    foreach(self::$providers as $provider_name => $provider) {
      $provider->prepare();
    }


    // TODO
    $found_provider = null;
    foreach(self::$login_methods_list as $method_name) {
      foreach(self::$providers as $provider_name => $provider) {
        if(method_exists($provider, $method_name)) {
          $provider->$method_name();
          self::flog(($provider_name . '->' . $method_name) . (!$provider->data[F_ACCOUNT_ID] ? $lang['sys_login_messages'][$provider->data[F_LOGIN_STATUS]] : dump($provider->data)));
          if($provider->data[F_LOGIN_STATUS] == LOGIN_SUCCESS || ($method_name == 'register' && $provider->data[F_LOGIN_STATUS] != LOGIN_UNDEFINED)) {
            $found_provider = $provider;
            break 2;
          }
          //          if(($provider->data[F_LOGIN_STATUS] != LOGIN_UNDEFINED) && !$found_provider) {
          //            $found_provider = $provider;
          //          }
          //          if($provider->data[F_ACCOUNT_ID] && (!$found_provider || $found_provider->data[F_LOGIN_STATUS] != LOGIN_SUCCESS)) {
          //            $found_provider = $provider;
          //          }
        }
      }
        //      print('<hr>');
        //      if($found_provider) {
        //        pdump($found_provider->data, $found_provider->manifest['name']);
        //        if($found_provider->data[F_LOGIN_STATUS] == LOGIN_SUCCESS) {
        //          break;
        //        }
        //      }
    }
    // pdump($found_provider->data);
    //die();
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
      foreach(self::$providers as $provider_name => $provider) {
        // Если провайдер не вошел своим аккаунтом - создаем для него аккаунт по данным успешного провайдера
        // TODO - Решить, что делать если емейл аккаунта, который заходит на сервер в первый раз совпадает с емейлом уже существующего игрока, но при этом не совпадают пароли
        $provider->db_create_account_from_provider($found_provider);
        // ПРОВАЙДЕР МОЖЕТ НЕ ПОДДЕРЖИВАТЬ ТАКОЕ СОЗДАНИЕ АККУНТОВ! ЭТО НОРМАЛЬНО
        // НО ТОГДА ОН ДОЛЖЕН ВЫСТАВЛЯТЬ СТАТУС LOGIN_SUCCESS И ВСЁ РАВНО ПРИНИМАТЬ ПОЛЬЗОВАТЕЛЯ!
      }

      // В этой точке все провайдеры ЛИБО имеют какие-то аккаунты, ЛИБО отказались создать их. ОДНАКО! У НИХ ВСЁ РАВНО ВСТАВЛЕН LOGIN_SUCCESS И ПРИНЯТ ПОЛЬЗОВАТЕЛЬ!
      foreach(self::$providers as $provider_name => $provider) {
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
      foreach(self::$providers as $provider_name => $provider) {
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


    // Сверить еще ИД юзеров, которые принадлежат кажому аккаунту - что бы коллизия не случилась
    // Ну и на аккаунте может быть больше одного ЮЗЕРА - официальные мультики

    self::login_process($found_provider);

    if(($result_password_reset = self::password_reset()) != LOGIN_UNDEFINED) {
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
      sys_redirect(SN_ROOT_RELATIVE . 'login.php');
    } elseif($redirect !== false) {
      sys_redirect($redirect);
    }
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

  static function password_reset() {
    if(!self::$hidden[F_IS_PASSWORD_RESET]) {
      return LOGIN_UNDEFINED;
    }

    global $lang, $config;

//        F_IS_PASSWORD_RESET => sys_get_param('password_reset'),
//        F_IS_PASSWORD_RESET_CONFIRM => sys_get_param('password_reset_confirm'),
//        F_PASSWORD_RESET_CODE => sys_get_param_str('password_reset_code'),

    $found_provider = null;
    $account = null;
    try {
      sn_db_transaction_start();
      foreach(self::$providers as $provider_name => $provider) {
        // if(method_exists($provider, $method_name)) // TODO проверять поддержку сброса пароля
        if($provider->data[F_INPUT][F_EMAIL_UNSAFE] && $provider->data[F_INPUT][F_IS_PASSWORD_RESET]) {
          $account = $provider->db_account_by_email($provider->data[F_INPUT][F_EMAIL_UNSAFE]);
          if($account) {
            $user = $provider->db_user_id_by_provider_account_id($account['account_id']);
            // TODO - Проверять уровень доступа аккаунта!
            if($user && $user['authlevel'] > 0) {
              throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
            }
            $found_provider = $provider;
            break;
          }
        }
      }

      if(!$found_provider || empty($account['account_email'])) {
        throw new exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL);
      }

      $email_safe = db_escape($found_provider->data[F_INPUT][F_EMAIL_UNSAFE]);
      $confirm_code_unsafe = $found_provider->db_confirmation_set($account['account_id'], CONFIRM_PASSWORD_RESET, $email_safe);
      sn_db_transaction_commit();

      @$result = mymail($found_provider->data[F_INPUT][F_EMAIL_UNSAFE],
        sprintf($lang['log_lost_email_title'], $config->game_name),
        sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . 'login.php', $confirm_code_unsafe, date(FMT_DATE_TIME, SN_TIME_NOW + 3*24*60*60), $config->game_name)
      );

      $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
    } catch(exception $e) {
      sn_db_transaction_rollback();
      $result = $e->getMessage();
    }

    $found_provider ? $found_provider->data[F_LOGIN_STATUS] = $result : false;;

    return $result;
  }

  static function password_reset_confirm() {
    if(!self::$hidden[F_IS_PASSWORD_RESET_CONFIRM]) {
      return LOGIN_UNDEFINED;
    }

    global $lang, $config;

    $found_provider = null;
    $confirmation = null;
    try {
      sn_db_transaction_start();
      foreach(self::$providers as $provider_name => $provider) {
        // if(method_exists($provider, $method_name)) // TODO проверять поддержку сброса пароля
        if($provider->data[F_INPUT][F_PASSWORD_RESET_CODE_SAFE] && $provider->data[F_INPUT][F_IS_PASSWORD_RESET_CONFIRM]) {
          $confirmation = $provider->db_confirmation_by_code($provider->data[F_INPUT][F_PASSWORD_RESET_CODE_SAFE], CONFIRM_PASSWORD_RESET);
          if($confirmation) {
            // TODO - Проверять уровень доступа аккаунта!
            // TODO - Проверять уровень доступа юзера!
            //$user = $provider->db_user_id_by_provider_account_id($account['account_id']);
            //if($user && $user['authlevel'] > 0) {
            //  throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
            //}
            $found_provider = $provider;
            break;
          }
        }
      }

      if(!$found_provider) {
        throw new exception(PASSWORD_RESTORE_ERROR_CODE_WRONG);
      }

      if(SN_TIME_NOW - strtotime($confirmation['create_time']) > PERIOD_DAY) {
        throw new exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD);
      }

      $new_password = sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
      $found_provider->data[F_ACCOUNT]['account_id'] = $confirmation['account_id'];
      $found_provider->data[F_REMEMBER_ME_SAFE] = 1;
      $found_provider->data[F_USER_ID] = $found_provider->db_user_id_by_provider_account_id($confirmation['account_id']);
      if(!$found_provider->real_password_change(false, $new_password, self::password_salt_generate())) {
        throw new exception(PASSWORD_RESTORE_ERROR_CHANGE, ERR_ERROR);
      }
//die();

      $found_provider->data[F_ACCOUNT] = $found_provider->db_account_by_id($found_provider->data[F_ACCOUNT]['account_id']);
      $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $found_provider->data[F_ACCOUNT]['account_name'], $new_password);
      @$operation_result = mymail($confirmation['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
      // TODO - Отправлять в личку сообщение о смене пароля
      //$message = sys_bbcodeParse($message) . '<br><br>';

      // TODO - При ошибке добавлять Global Message
      $result[F_LOGIN_STATUS] = LOGIN_SUCCESS; // $operation_result ? PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT : PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR;
      // $result[F_LOGIN_MESSAGE] = $message . ($operation_result ? $lang['log_lost_sent_pass'] : $lang['log_lost_err_sending']);
      doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$confirmation['id']}'");

      sn_db_transaction_commit();
      sys_redirect('overview.php');
    } catch(exception $e) {
      sn_db_transaction_rollback();
      $result = $e->getMessage();
    }

    return $result;
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


  static function extract_to_hidden($found_provider) {
    self::$hidden[F_LOGIN_STATUS] = $found_provider->data[F_LOGIN_STATUS];
    self::$hidden[F_PROVIDER_ID] = $found_provider->manifest['provider_id'];
    self::$hidden[F_ACCOUNT_ID] = $found_provider->data[F_ACCOUNT_ID];
    self::$hidden[F_ACCOUNT] = $found_provider->data[F_ACCOUNT];
    self::$hidden[F_USER_ID] = $found_provider->data[F_USER_ID];
    self::$hidden[F_USER] = $found_provider->data[F_USER];
    self::$hidden[F_REMEMBER_ME_SAFE] = $found_provider->data[F_REMEMBER_ME_SAFE];

    self::$hidden[AUTH_LEVEL] = isset($found_provider->data[F_USER]['authlevel']) ? $found_provider->data[F_USER]['authlevel'] : AUTH_LEVEL_ANONYMOUS;

    self::$hidden[F_BANNED_STATUS] = $found_provider->data[F_BANNED_STATUS];
    self::$hidden[F_VACATION_STATUS] = $found_provider->data[F_VACATION_STATUS];

    //TODO Сол и Парол тоже вкинуть в хидден
  }
  static function login_process($found_provider) {
    self::extract_to_hidden($found_provider);

    global $user_impersonator, $config, $sys_stop_log_hit, $is_watching;

    $ip = self::sec_player_ip();
    $ip_int_safe = ip2longu($ip['ip']);
    $proxy_safe = db_escape($ip['proxy_chain']);

    $user_id = !empty($found_provider->data[F_USER_ID]) ? $found_provider->data[F_USER_ID] : 0;
    // if(!empty($user_id) && !$user_impersonator) {
    // $user_id не может быть пустым из-за констраинтов в таблице SPE
    if($user_id && !$user_impersonator) {
      doquery(
        "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
        VALUES ({$user_id}," . self::$hidden[F_DEVICE_ID] . "," . self::$hidden[F_BROWSER_ID]. ",{$ip_int_safe}, '{$proxy_safe}');"
      );

      if(!$sys_stop_log_hit && $config->game_counter) {
        sn_db_transaction_start();
        $is_watching = true;
        sec_login_set_fields(self::$hidden[F_PAGE], self::$hidden[F_PAGE_ID], $_SERVER['PHP_SELF'], 'url_id', 'security_url', 'url_string');
        sec_login_set_fields(self::$hidden[F_URL], self::$hidden[F_URL_ID], $_SERVER['REQUEST_URI'], 'url_id', 'security_url', 'url_string');

        doquery(
          "INSERT INTO {{counter}}
          (`visit_time`, `user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`, `page_url_id`, `plain_url_id`)
        VALUES
          ('" . SN_TIME_SQL. "', {$user_id}, " . self::$hidden[F_DEVICE_ID] . "," . self::$hidden[F_BROWSER_ID] . ",
            {$ip_int_safe},'{$proxy_safe}', " . self::$hidden[F_PAGE_ID] . ", " . self::$hidden[F_URL_ID] . ");");
        $is_watching = false;
        sn_db_transaction_commit();
      }

      if($extra = $config->security_ban_extra) {
        $extra = explode(',', $extra);
        array_walk($extra,'trim');
        in_array(self::$hidden[F_DEVICE_ID], $extra) and die();
      }
    }

    if($found_provider->data[F_LOGIN_STATUS] != LOGIN_SUCCESS) {
      return;
    }

    if($user_id) {
      $user = &$found_provider->data[F_USER];

      sys_user_options_unpack($user);

      if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
        $user['banaday'] = 0;
        $user['vacation'] = SN_TIME_NOW;
      }

      $user['user_lastip'] = $ip['ip'];
      $user['user_proxy'] = $ip['proxy_chain'];

      $found_provider->data[F_BANNED_STATUS] = $user['banaday'];
      $found_provider->data[F_VACATION_STATUS] = $user['vacation'];

      db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ",
      `banaday` = " . db_escape($user['banaday']) . ", `vacation` = " . db_escape($user['vacation']) . ",
      `user_lastip` = '" . db_escape($user['user_lastip']) . "', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = " . self::$hidden[F_BROWSER_ID]
      );
    }

    // Не должно никуда уходить
//    unset($result[F_DEVICE_ID]);
//    unset($result[F_DEVICE_CYPHER]);
  }

  static function init_device_and_browser() {
    // TODO - срятать в статик-функцию
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

    // TODO - срятать в статик-функцию
    sec_login_set_fields(self::$hidden[F_BROWSER], self::$hidden[F_BROWSER_ID], $_SERVER['HTTP_USER_AGENT'], 'browser_id', 'security_browser', 'browser_user_agent');
    sn_db_transaction_commit();
  }
  static function flog($message, $die = false) {
    list($called, $caller) = debug_backtrace(false);
    $caller_name =
      (!empty($caller['class']) ? $caller['class'] : '') .
      (!empty($caller['type']) ? $caller['type'] : '') .
      (!empty($caller['function']) ? $caller['function'] : '') .
      (!empty($called['line']) ? ':' . $called['line'] : '');

    // $_SERVER['SERVER_NAME'] == 'localhost' ? print("<div class='debug'>$message - $caller_name\r\n</div>") : false;

    classSupernova::log_file("$message - $caller_name");
    if($die) {
      // $die && die("Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . ")");
      // pdump($caller);
      // pdump(debug_backtrace(false));
      $die && die("<div class='negative'>СТОП! Функция {$caller_name} при вызове в " . get_called_class() . " (располагается в " . get_class() . "). СООБЩИТЕ АДМИНИСТРАЦИИ!</div>");
    }
  }
}
