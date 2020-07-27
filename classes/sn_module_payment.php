<?php

use DBAL\db_mysql;
use Modules\sn_module;
use Payment\PaymentMethods;

/**
 * User: Gorlum
 * Date: 21.04.2015
 * Time: 3:49
 */
abstract class sn_module_payment extends sn_module {
  const DO_NOT_REDIRECT = 'DO_NOT_REDIRECT';
  const FIELD_SUM = 'SUM';
  const FIELD_CURRENCY = 'CURRENCY';

  public $versionCommitted = '#45d0#';

  public $active = false;

  public static $bonus_table = [
    2000    => 0,
    // 5000 => 0,
    10000   => 0,
    20000   => 0,
    50000   => 0.02,
    100000  => 0.05,
    200000  => 0.07,
    300000  => 0.10,
    400000  => 0.15,
    500000  => 0.20,
    800000  => 0.25,
    1000000 => 0.30,
    1500000 => 0.40,
    2000000 => 0.50,
    3000000 => 0.60,
    5000000 => 0.70,
  ];

  /**
   * @var Account $account
   */
  public $account = null;

  /**
   * @var db_mysql $db
   */
  public $db = null;

  /**
   * @var int
   */
  public $request_payment_id = 0;
  /**
   * Идентификатор сервера, на который производится оплата
   *
   * @var string $request_server_id
   */
  public $request_server_id = '';
  /**
   * Идентификатор платящего пользователя
   *
   * @var int
   */
  public $request_account_id = 0;
  /**
   * @var int
   */
  // public $request_mm_amount = 0;
  /**
   * @var float
   */
  // public $request_money_out = 0.0;

  /**
   * Внутренний идентификатор платежа
   *
   * @var int
   */
  public $payment_id = 0;
  public $payment_status = PAYMENT_STATUS_NONE;
  public $payment_provider_id = ACCOUNT_PROVIDER_NONE;
  public $payment_account_id = 0;
  public $payment_account_name = '';
  public $payment_user_id = 0;
  public $payment_user_name = '';
  public $payment_amount = 0;
  public $payment_currency = '';
  public $payment_dark_matter_paid = 0;
  public $payment_dark_matter_gained = 0;
  public $payment_date = SN_TIME_SQL;
  public $payment_comment = '';
  public $payment_module_name = '';

  public $payment_external_id = '';
  public $payment_external_date = '';
  public $payment_external_lots = 0;
  public $payment_external_amount = 0;
  public $payment_external_currency = '';

  public $payment_method = null;

  public $payment_test = 0;

  public $is_exists = false;
  public $is_loaded = false;

  protected $description_generated = array();

  protected $debug = false;

  protected $payment_params = array(
//    'server_id' => 'shp_server', // Должен быть server_id
//    'account_id' => 'shp_id', // Должен быть user_id
//    'payment_id' => 'InvId', // Должен быть внутренний payment_id
//    'payment_dark_matter_gained' => 'shp_dm', // TODO - Реально - dark_matter_gained! Что бы учитывались акции!
//    'payment_external_money' => 'OutSum', // Количество денег "к оплате" от СН
//    'test' => 'shp_z_test', // Тестовый статус аккаунта
//    'payment_external_id' => '', // ИД платежа в платёжной системе
//    'payment_external_currency' => 'payment_currency', // Валюта платежа в платёжной системе
  );

  protected $result_translations = array(
    // Универсальный ответ на неизвестную ошибку
    SN_PAYMENT_REQUEST_UNDEFINED_ERROR => SN_PAYMENT_REQUEST_UNDEFINED_ERROR,
    // Утвердительный ответ
    SN_PAYMENT_REQUEST_OK              => SN_PAYMENT_REQUEST_OK,
  );

  /**
   * sn_module_payment constructor.
   *
   * @param string $filename
   *
   * @throws Exception
   */
  public function __construct($filename = __FILE__) {
    parent::__construct($filename);

    if (!empty($this->config['debug'])) {
      $this->debug = true;
    }
  }

  /**
   * @param array $data
   */
  public function debug($data) {
    if (!$this->debug) {
      return;
    }

    file_put_contents(SN_ROOT_PHYSICAL . '_' . get_called_class() . '_debug.txt', $data, FILE_APPEND);
  }

  /**
   * Компилирует запрос к платёжной системе
   *
   * @param $request
   *
   * @throws Exception
   */
  public function compile_request($request, $payment_method_selected) {
    global $config, $lang, $user;

//    if (!(SN::$auth->account instanceof Account)) {
//      // TODO - throw new Exception($lang['pay_msg_mm_request_amount_invalid'], SN_PAYMENT_REQUEST_ERROR_UNIT_AMOUNT);
//    }
    $this->account = SN::$auth->account;

    $this->db = $this->account->db;

    $this->payment_provider_id  = core_auth::$main_provider->provider_id;
    $this->payment_account_id   = $this->account->account_id;
    $this->payment_account_name = $this->account->account_name;
    $this->payment_user_id      = $user['id'];
    $this->payment_user_name    = $user['username'];

    // TODO - минимальное количество ММ к оплате
    $this->payment_dark_matter_paid   = $request['metamatter'];
    $this->payment_dark_matter_gained = self::bonus_calculate($this->payment_dark_matter_paid, true);

    $this->payment_currency = $config->payment_currency_default;
    $this->payment_amount   = self::currency_convert($this->payment_dark_matter_paid, 'MM_', $this->payment_currency);

    $this->payment_method = $payment_method_selected;
    if (empty($this->payment_external_currency)) {
      $this->payment_external_currency = $this->getMethodCurrency($this->payment_method);
    }
    if (empty($this->payment_external_currency) && !empty($this->config['currency'])) {
      $this->payment_external_currency = $this->config['currency'];
    }
    if (empty($this->payment_external_currency)) {
      throw new Exception($lang['pay_error_internal_no_external_currency_set'], SN_PAYMENT_ERROR_INTERNAL_NO_EXTERNAL_CURRENCY_SET);
    }

    $this->payment_external_amount = self::currency_convert($this->payment_dark_matter_paid, 'MM_', $this->payment_external_currency);
    if ($this->payment_external_amount < 0.01) {
      throw new Exception($lang['pay_msg_mm_request_amount_invalid'], SN_PAYMENT_REQUEST_ERROR_UNIT_AMOUNT);
    }

    $this->payment_test = !empty($this->config['test']);

    $this->generate_description();

    $this->db_insert();
    if (!$this->is_exists) {
      throw new Exception($lang['pay_msg_request_error_db_payment_create'], SN_PAYMENT_REQUEST_DB_ERROR_PAYMENT_CREATE);
    }
  }

  /**
   * @param array $options
   *
   * @return array
   * @throws Exception
   */
  protected function payment_request_process($options = []) {
    global $lang, $config;

    if (!$this->active) {
      throw new Exception($lang['pay_msg_module_disabled'], SN_MODULE_DISABLED);
    }

    // Если есть payment_id - загружаем под него данные
    if (!empty($this->payment_params['payment_id'])) {
      $this->request_payment_id = sys_get_param_id($this->payment_params['payment_id']);
      if (!$this->request_payment_id) {
        throw new Exception($lang['pay_msg_request_payment_id_invalid'], SN_PAYMENT_REQUEST_INTERNAL_ID_WRONG);
      }

      if (!$this->db_get_by_id($this->request_payment_id)) {
        throw new Exception($lang['pay_msg_request_payment_id_invalid'], SN_PAYMENT_REQUEST_INTERNAL_ID_WRONG);
      }

      // Проверяем - был ли этот платеж обработан?
      // TODO - Статусы бывают разные. Нужен спецфлаг payment_processed
      if ($this->payment_status != PAYMENT_STATUS_NONE && empty($options[self::DO_NOT_REDIRECT])) {
        sn_db_transaction_rollback();
        sys_redirect(SN_ROOT_VIRTUAL . 'metamatter.php?payment_id=' . $this->payment_id);
        die();
      }
    }

    // Пытаемся получить из запроса ИД аккаунта
    $request_account_id = !empty($this->payment_params['account_id']) ? sys_get_param_id($this->payment_params['account_id']) : 0;
    // Если в запросе нет ИД аккаунта - пытаемся использовать payment_account_id
    if (empty($request_account_id) && !empty($this->payment_account_id)) {
      $request_account_id = $this->payment_account_id;
    }
    // Если теперь у нас нету ИД аккаунта ни в запросе, ни в записи таблицы - можно паниковать
    if (empty($request_account_id)) {
      // TODO - аккаунт
      throw new Exception($lang['pay_msg_request_user_invalid'], $this->retranslate_error(SN_PAYMENT_REQUEST_USER_NOT_FOUND, $options));
    }
    // Если нет записи в таблице - тогда берем payment_account_id из запроса
    if (empty($this->payment_account_id)) {
      $this->payment_account_id = $request_account_id;
    }
    // Если у нас отличаются ИД аккаунта в запросе и ИД аккаунта в записи - тоже можно паниковать
    if ($this->payment_account_id != $request_account_id) {
      // TODO - Поменять сообщение об ошибке
      throw new Exception($lang['pay_msg_request_user_invalid'], $this->retranslate_error(SN_PAYMENT_REQUEST_USER_NOT_FOUND, $options));
    }
    // Проверяем существование аккаунта с данным ИД
    if (!$this->account->db_get_by_id($this->payment_account_id)) {
      throw new Exception($lang['pay_msg_request_user_invalid'] . ' ID ' . $this->payment_account_id, $this->retranslate_error(SN_PAYMENT_REQUEST_USER_NOT_FOUND, $options));
    }

    // TODO Проверка на сервер_ид - как бы и не нужна, наверное?
    if (!empty($this->payment_params['server_id'])) {
      $this->request_server_id = sys_get_param_str($this->payment_params['server_id']);
      if (SN_ROOT_VIRTUAL != $this->request_server_id) {
        throw new Exception($lang['pay_msg_request_server_wrong'] . " {$this->request_server_id} вместо " . SN_ROOT_VIRTUAL, SN_PAYMENT_REQUEST_SERVER_WRONG);
      }
    }

    // Сверка количества оплаченной ММ с учётом бонусов
    if (!empty($this->payment_params['payment_dark_matter_gained'])) {
      $request_mm_amount = sys_get_param_id($this->payment_params['payment_dark_matter_gained']);
      if ($request_mm_amount != $this->payment_dark_matter_gained && $this->is_loaded) {
        throw new Exception($lang['pay_msg_mm_request_amount_invalid'] . " пришло {$request_mm_amount} ММ вместо {$this->payment_dark_matter_gained} ММ", SN_PAYMENT_REQUEST_MM_AMOUNT_INVALID);
      }
      empty($this->payment_dark_matter_gained) ? $this->payment_dark_matter_gained = $request_mm_amount : false;
    }
//    if (empty($this->payment_dark_matter_paid)) {
//      // TODO - обратный расчёт из gained
//    }

    // Проверка наличия внешнего ИД платежа
    if (!empty($this->payment_params['payment_external_id'])) {
      $request_payment_external_id = sys_get_param_str($this->payment_params['payment_external_id']);
      if (empty($request_payment_external_id)) {
        throw new exception($lang['pay_msg_request_payment_id_invalid'], SN_PAYMENT_REQUEST_EXTERNAL_ID_WRONG);
      } elseif (!empty($this->payment_external_id) && $this->payment_external_id != $request_payment_external_id) {
        // TODO - Может быть поменять сообщение
        throw new exception($lang['pay_msg_request_payment_id_invalid'], SN_PAYMENT_REQUEST_EXTERNAL_ID_WRONG);
      }
      $this->payment_external_id = $request_payment_external_id;
    }
    // Сверка суммы, запрошенной СН к оплате
    if (!empty($this->payment_params['payment_external_money'])) {
      $request_money_out = sys_get_param_float($this->payment_params['payment_external_money']);
      if ($request_money_out != $this->payment_external_amount && $this->is_loaded) {
        throw new Exception($lang['pay_msg_request_payment_amount_invalid'] . " пришло {$request_money_out} денег вместо {$this->payment_external_amount} денег", SN_PAYMENT_REQUEST_CURRENCY_AMOUNT_INVALID);
      }
      empty($this->payment_external_amount) ? $this->payment_external_amount = $request_money_out : false;
    }
    // Заполняем поле валюты платёжной системы
    if (!empty($this->payment_params['payment_external_currency'])) {
      $this->payment_external_currency = sys_get_param_str($this->payment_params['payment_external_currency']);
      if (empty($this->payment_external_currency)) {
        // TODO - поменять сообщение
        throw new Exception($lang['pay_msg_request_payment_amount_invalid'] . " {$this->payment_external_currency}", SN_PAYMENT_REQUEST_CURRENCY_AMOUNT_INVALID);
      }
    }
    if (empty($this->payment_external_currency)) {
      $this->payment_external_currency = $this->config['currency'];
    }

    // Заполнение внутренней суммы и валюты из внешних данных
    if (empty($this->payment_currency)) {
      $this->payment_currency = $config->payment_currency_default;
    }
    if (empty($this->payment_amount) && !empty($this->payment_external_currency)) {
      $this->payment_amount = self::currency_convert($this->payment_external_amount, $this->payment_external_currency, $this->payment_currency);
    }

    // TODO - Тестовый режим
    if (!empty($this->payment_params['test'])) {
      $this->payment_test = $this->config['test'] || sys_get_param_int($this->payment_params['test']);
    }

    $this->generate_description();
  }

  /**
   * Точка входа для коллбэка системы платежей - вызывается из <class_name>_response.php
   *
   * @return array
   */
  // OK 4.8
  // TODO - Здесь должно происходить разделение на resultURL, successURL, failURL
  public function payment_request_response() {
    global $debug;

    $this->db      = core_auth::$main_provider->db;
    $this->account = new Account($this->db);

    // TODO - REPLACE WITH INNATE CALL!
    sn_db_transaction_start();
    try {
      $response = $this->payment_request_process();
    } catch (Exception $e) {
      $response['result']  = $e->getCode();
      $response['message'] = $e->getMessage();

      $this->debug([
        "\n",
        "Kinda Error!\n",
        '$response => ', var_export($response, true),
        "\n",
      ]);
    }

    if ($response['result'] == SN_PAYMENT_REQUEST_OK) {
      sn_db_transaction_commit();
      $debug->warning('Результат операции: код ' . $response['result'] . ' сообщение "' . $response['message'] . '"', 'Успешный платёж', LOG_INFO_PAYMENT);
    } else {
      sn_db_transaction_rollback();
      $debug->warning('Результат операции: код ' . $response['result'] . ' сообщение "' . $response['message'] . '"', 'Ошибка платежа', LOG_INFO_PAYMENT, true);
    }

    // Переводим код результата из СН в код платежной системы
    if (is_array($this->result_translations) && !empty($this->result_translations)) {
      $response['result'] = isset($this->result_translations[$response['result']]) ? $this->result_translations[$response['result']] : $this->result_translations[SN_PAYMENT_REQUEST_UNDEFINED_ERROR];
    }

    return $response;
  }

  /**
   *
   *
   * @param string  $payment_method_selected
   * @param string  $player_currency
   * @param integer $metamatter
   *
   * @return array [self::FIELD_SUM => (float){sum_to_pay}, self::FIELD_CURRENCY => (str){currency_code}]. Currency code is optional. Empty if no data
   */
  public function getPrice($payment_method_selected, $player_currency, $metamatter) {
    return [];
  }
  // Function converts money values between currencies

  /**
   * Внутриигровая конвертация валют
   *
   * @param        $value
   * @param string $currency_from
   * @param string $currency_to
   * @param int    $round
   *
   * @return float|int
   */
  public static function currency_convert($value, $currency_from = '', $currency_to = '', $round = 2) {
//    global $config;

    $currency_from = strtolower($currency_from);
    $currency_to   = strtolower($currency_to);

    if ($currency_from != $currency_to) {
//      $config_currency_from_name = 'payment_currency_exchange_' . $currency_from;
//      $config_currency_to_name = 'payment_currency_exchange_' . $currency_to;

//      $exchange_from = floatval($currency_from == 'mm_' ? get_mm_cost() : $config->$config_currency_from_name);
//      $exchange_to = floatval($currency_to == 'mm_' ? get_mm_cost() : $config->$config_currency_to_name);

      $exchange_from = get_exchange_rate($currency_from);
      $exchange_to   = get_exchange_rate($currency_to);

      $value = $exchange_from ? $value / $exchange_from * $exchange_to * pow(10, $round) : 0;
      $value = ceil($value) / pow(10, $round);
    }

    return $value;
  }

  // Function calculates bonused DM amount for bulk purchase and ($direct = false) vice versa

  /**
   * Рассчёт бонуса ММ
   *
   * @param            $dark_matter
   * @param bool|true  $direct
   * @param bool|false $return_bonus
   *
   * @return float|int
   */
  public static function bonus_calculate($dark_matter, $direct = true, $return_bonus = false) {
    $bonus           = 0;
    $dark_matter_new = $dark_matter;
    if (!empty(self::$bonus_table) && $dark_matter >= self::$bonus_table[0]) {
      if ($direct) {
        foreach (self::$bonus_table as $dm_for_bonus => $multiplier) {
          if ($dm_for_bonus <= $dark_matter) {
            $dark_matter_new = $dark_matter * (1 + $multiplier);
            $bonus           = $multiplier;
          } else {
            break;
          }
        }
      } else {
        foreach (self::$bonus_table as $dm_for_bonus => $multiplier) {
          $temp = $dm_for_bonus * (1 + $multiplier);
          if ($dark_matter >= $temp) {
            $dark_matter_new = round($dark_matter / (1 + $multiplier));
            $bonus           = $multiplier;
          } else {
            break;
          }
        }
      }
    }

    return $return_bonus ? $bonus : $dark_matter_new;
  }

  // Дополнительная ре-трансляция адреса, если в каком-то случае платежная система ожидает нелогичный ответ
  // Пример: иксолла при неправильно заданном пользователе в ордере ожидает НЕПРАВИЛЬНЫЙ_ОРДЕР, а не НЕПРАВИЛЬНЫЙ_ПОЛЬЗОВАТЕЛЬ
  protected function retranslate_error($error_code, $options = array()) {
    return isset($options['retranslate_error'][$error_code]) ? $options['retranslate_error'][$error_code] : $error_code;
  }


  protected function db_insert() {
    $this->payment_test = !empty($this->config['test']) || $this->payment_test;

    $payment = array(
      'payment_module_name' => $this->manifest['name'],

      'payment_status' => $this->payment_status,
      // 'payment_date' => $this->payment_date, // Не нужно

      'payment_provider_id'  => $this->payment_provider_id,
      'payment_account_id'   => $this->payment_account_id,
      'payment_account_name' => $this->payment_account_name,
      'payment_user_id'      => $this->payment_user_id,
      'payment_user_name'    => $this->payment_user_name,

      'payment_dark_matter_paid'   => $this->payment_dark_matter_paid,
      'payment_dark_matter_gained' => $this->payment_dark_matter_gained,

      'payment_amount'   => $this->payment_amount,
      'payment_currency' => $this->payment_currency,

      'payment_external_id'       => $this->payment_external_id, // TODO
      'payment_external_amount'   => $this->payment_external_amount,
      'payment_external_currency' => $this->payment_external_currency,
      'payment_external_date'     => $this->payment_external_date, // TODO

      'payment_test' => $this->payment_test ? 1 : 0, // Boolean -> int

      'payment_comment' => $this->description_generated[PAYMENT_DESCRIPTION_MAX],

      'payment_external_lots' => $this->payment_dark_matter_paid / get_mm_cost(),

      'payment_method_id' => $this->payment_method,
    );

    $replace = false;
    if ($this->payment_id) {
      $payment['payment_id'] = $this->payment_id;
      $replace               = true;
    }

    $query = array();
    foreach ($payment as $key => $value) {
      if ($value === null) {
        $value = 'NULL';
      } else {
        $value = is_string($value) ? '"' . db_escape($value) . '"' : $value;
      }
      $query[] = "`{$key}` = {$value}";
    }

    $this->db->doquery(($replace ? 'REPLACE' : 'INSERT') . ' INTO `{{payment}}` SET ' . implode(',', $query) . ';');

    return $this->db_get_by_id($this->db->db_insert_id());
  }

  /**
   * Get currency that module support for method
   *
   * @param $paymentMethod
   *
   * @return string
   */
  public function getMethodCurrency($paymentMethod) {
    // Generally each module can support range of currencies and override this method
    // This is just a plug to route requests by default
    return PaymentMethods::getDefaultCurrency($paymentMethod);
  }


  /**
   * @throws Exception
   */
  protected function payment_adjust_mm_new() {
    if (!$this->payment_test) {
      // Not a test payment. Adding DM to account
      $this->account = new Account($this->db);
      $this->account->db_get_by_id($this->payment_account_id);
      $result = $this->account->metamatter_change(RPG_PURCHASE, $this->payment_dark_matter_gained, $this->payment_comment);
      if (!$result) {
        throw new Exception('Ошибка начисления ММ', SN_METAMATTER_ERROR_ADJUST);
      }
    }
  }

  /**
   * @param $payment
   *
   * @throws exception
   */
  function payment_cancel(/** @noinspection PhpUnusedParameterInspection */ &$payment) {
    die('{НЕ РАБОТАЕТ! СООБЩИТЕ АДМИНИСТРАЦИИ!}');
    global $lang;

    if (!isset($payment['payment_status'])) {
      throw new exception($lang['pay_msg_request_payment_not_found'], SN_PAYMENT_REQUEST_ORDER_NOT_FOUND);
    }

    if ($payment['payment_status'] == PAYMENT_STATUS_COMPLETE) {
      $safe_comment = db_escape($payment['payment_comment'] = $lang['pay_msg_request_payment_cancelled'] . ' ' . $payment['payment_comment']);

      if (!$payment['payment_test']) {
        $result = $this->account->metamatter_change(RPG_PURCHASE_CANCEL, -$payment['payment_dark_matter_gained'], $payment['payment_comment']);
        if (!$result) {
          throw new exception('Ошибка начисления ММ', SN_METAMATTER_ERROR_ADJUST);
        }
      }
      $payment['payment_status'] = PAYMENT_STATUS_CANCELED;
      doquery("UPDATE {{payment}} SET payment_status = {$payment['payment_status']}, payment_comment = '{$safe_comment}' WHERE payment_id = {$payment['payment_id']};");
      throw new exception($lang['pay_msg_request_payment_cancel_complete'], SN_PAYMENT_REQUEST_OK);
    } elseif ($payment['payment_status'] == PAYMENT_STATUS_CANCELED) {
      throw new exception($lang['pay_msg_request_payment_cancelled_already'], SN_PAYMENT_REQUEST_OK);
    } elseif ($payment['payment_status'] == PAYMENT_STATUS_NONE) {
      throw new exception($lang['pay_msg_request_payment_cancel_not_complete'], SN_PAYMENT_REQUEST_PAYMENT_NOT_COMPLETE);
    }
  }


  protected function db_get_by_id($payment_id_unsafe) {
    $payment_id_internal_safe = $this->db->db_escape($payment_id_unsafe);
    $payment                  = $this->db->doQueryAndFetch(
      "SELECT * 
      FROM {{payment}} 
      WHERE 
        `payment_module_name` = '{$this->manifest['name']}' 
        AND `payment_id` = '{$payment_id_internal_safe}' 
        LIMIT 1 FOR UPDATE;"
    );

    return $this->db_assign_payment($payment);
  }

  /**
   * @throws Exception
   */
  protected function db_complete_payment() {
    // TODO - поле payment_processed
    if ($this->payment_status == PAYMENT_STATUS_NONE) {
      if (!defined('PAYMENT_EXPIRE_TIME') || PAYMENT_EXPIRE_TIME == 0 || empty($this->payment_date) || strtotime($this->payment_date) + PAYMENT_EXPIRE_TIME <= SN_TIME_NOW) {
        $this->payment_adjust_mm_new();
        $this->payment_status = PAYMENT_STATUS_COMPLETE;
      } else {
        $this->payment_status = PAYMENT_STATUS_EXPIRED;
      }

      $this->db_insert();
    }
  }

  protected function payment_reset() {
    $this->payment_id     = 0;
    $this->payment_status = PAYMENT_STATUS_NONE;

    $this->payment_provider_id  = ACCOUNT_PROVIDER_NONE;
    $this->payment_account_id   = 0;
    $this->payment_account_name = '';
    $this->payment_user_id      = 0;
    $this->payment_user_name    = '';

    $this->payment_amount   = 0;
    $this->payment_currency = '';

    $this->payment_dark_matter_paid   = 0;
    $this->payment_dark_matter_gained = 0;
    $this->payment_date               = SN_TIME_SQL;
    $this->payment_comment            = '';
    $this->payment_module_name        = '';

    $this->payment_external_id       = '';
    $this->payment_external_date     = '';
    $this->payment_external_lots     = 0;
    $this->payment_external_amount   = 0;
    $this->payment_external_currency = '';

    $this->payment_test = 0;

    $this->is_exists = false;
    $this->is_loaded = false;

    $this->description_generated = array();
  }

  protected function db_assign_payment($payment = null) {
    $this->payment_reset();

    if (is_array($payment) && isset($payment['payment_id'])) {
      $this->payment_id     = $payment['payment_id'];
      $this->payment_status = $payment['payment_status'];
      $this->payment_date   = $payment['payment_date'];

      $this->payment_provider_id  = $payment['payment_provider_id'];
      $this->payment_account_id   = $payment['payment_account_id'];
      $this->payment_account_name = $payment['payment_account_name'];
      $this->payment_user_id      = $payment['payment_user_id'];
      $this->payment_user_name    = $payment['payment_user_name'];

      $this->payment_amount   = $payment['payment_amount'];
      $this->payment_currency = $payment['payment_currency'];

      $this->payment_dark_matter_paid   = $payment['payment_dark_matter_paid'];
      $this->payment_dark_matter_gained = $payment['payment_dark_matter_gained'];

      $this->payment_comment     = $payment['payment_comment'];
      $this->payment_module_name = $payment['payment_module_name'];

      $this->payment_external_id       = $payment['payment_external_id'];
      $this->payment_external_date     = $payment['payment_external_date'];
      $this->payment_external_lots     = $payment['payment_external_lots'];
      $this->payment_external_amount   = $payment['payment_external_amount'];
      $this->payment_external_currency = $payment['payment_external_currency'];

      $this->payment_test = $payment['payment_test'];

      $this->is_exists = true;
      $this->is_loaded = true;

      $this->generate_description();

      return true;
    } else {
      return false;
    }
  }

  protected function generate_description() {
    $mmShort = SN::$lang['sys_metamatter_sh'];
    // TODO - системная локализация
    $this->description_generated = array(
      PAYMENT_DESCRIPTION_50 => substr(
        ($this->payment_test ? "T!" : '') .
        "{$this->payment_dark_matter_gained} {$mmShort} {$this->account->account_name}",
        0,
        PAYMENT_DESCRIPTION_50
      ),
      PAYMENT_DESCRIPTION_100 => substr("{$this->payment_dark_matter_gained} {$mmShort} аккаунт [{$this->account->account_name}] ID {$this->account->account_id} на " . SN_ROOT_VIRTUAL, 0, PAYMENT_DESCRIPTION_100),
      PAYMENT_DESCRIPTION_250 => substr("Оплата {$this->payment_dark_matter_gained} {$mmShort} для аккаунта [{$this->payment_user_name}] ID {$this->payment_user_id} на сервере " . SN_ROOT_VIRTUAL, 0, PAYMENT_DESCRIPTION_250),
      PAYMENT_DESCRIPTION_MAX => ($this->payment_test ? "ТЕСТОВЫЙ ПЛАТЕЖ! " : '') .
        "Платеж от аккаунта '{$this->payment_account_name}' ID {$this->payment_account_id} игрока '{$this->payment_user_name}' ID {$this->payment_user_id} на сервере " . SN_ROOT_VIRTUAL .
        " сумма {$this->payment_amount} {$this->payment_currency} за {$this->payment_dark_matter_paid} {$mmShort} (начислено {$this->payment_dark_matter_gained} {$mmShort})" .
        " через '{$this->manifest['name']}' сумма {$this->payment_external_amount} {$this->payment_external_currency}",
    );
  }


  /**
   * Does this module support payment method?
   *
   * @param $payment_method
   *
   * @return bool
   */
  public function isMethodSupported($payment_method) {
    return array_key_exists($payment_method, $this->manifest['payment_method']);
  }

  /**
   * Get details about payment method
   *
   * @param int $payment_method
   *
   * @return mixed|null
   */
  public function getMethodDetails($payment_method) {
    return $this->isMethodSupported($payment_method) ? $this->manifest['payment_method'][$payment_method] : null;
  }

  public function getMethodList() {
    return $this->manifest['payment_method'];
  }

  /**
   * Get payment method external ID used by payment provider
   *
   * @param $payment_method
   *
   * @return mixed|null
   */
  public function getMethodExternalId($payment_method) {
    return $this->getMethodDetails($payment_method);
  }

}
