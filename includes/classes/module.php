<?php

class sn_module {
  public $manifest = array(
    'package' => 'core',
    'name' => 'sn_module',
    'version' => '1c0',
    'copyright' => 'Project "SuperNova.WS" #39b14.2# copyright © 2009-2014 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'installed' => true,
    'active' => true,

    // 'constants' array - contents of this array would be instaled into engine
    'constants' => array(
//      'UNIT_STRUCTURE_NEW' => 999999,
    ),

    'vars' => array(), // Just a placeholder. vars assigned via special method __assign_vars(). Need 'cause of new constants that can be defined within module. See below

    // 'functions' array - this functions would be installed as hooks
    // Key: overwritable function name to replace
    // Value: which method to use. Format: [*][<object_name>][.]<method>
    // '*' means that new function would replace old one
    // If object_name is ommited but "." is present - hook linked to global function
    // If only "method" present - overwritable linked to appropriate method of current object
    // Function/Method should be accessible on module init
    'functions' => array(
//      'test_object_test_method' => 'test_object.test_method',
//      'test_function' => '.my_test_function',
//      'this_object_test_method' => 'test_method',
    ),

    // 'menu' array - this menu items would be merged into main game menu
    // Array element almost identical to $sn_menu with additional param 'LOCATION'.
    // 'LOCATION' => '-news', // Special atrtribute for modules
    // [-|+][<menu_item_id>]
    // <menu_item_id> identifies menu item aginst new menu item would be placed. When ommited new item placed against whole menu
    // -/+ indicates that new item should be placed before/after identified menu item (or whole menu). If ommited and menu item exists - new item will replace previous one
    // Empty or non-existent LOCATION equivalent to '+' - place item at end of menu
    // Non-existent menu_item_id treated as ommited
    'menu' => array(),

    // 'page' array - defines pages which will handle this module and appropriate handlers
    'page' => array(),
  );

  protected $config = array();

  function __assign_vars() {
    return array(
/*
      'sn_data' => array(
        UNIT_STRUCTURE_NEW => array( // Will honor new constants
          'name' => 'robot_factory',
          'type' => UNIT_STRUCTURE,
          'location' => LOC_PLANET,
          'cost' => array(
            RES_METAL     => 400,
            RES_CRYSTAL   => 120,
            RES_DEUTERIUM => 200,
            RES_ENERGY    => 0,
            'factor' => 2,
          ),
          'metal' => 400,
          'crystal' => 120,
          'deuterium' => 200,
          'energy' => 0,
          'factor' => 2,
        ),
      ),
*/
    );
  }

  function sn_module($filename = __FILE__) {
    // Getting module PHP class name
    $class_module_name = get_class($this);

    // Getting module root relative to SN
    $this->manifest['root_relative'] = str_replace(array(SN_ROOT_PHYSICAL, basename($filename)), '', str_replace('\\', '/', $filename));

    // TODO: Load configuration from DB. Manifest setting
    // Trying to load configuration from file
    if(file_exists($config_filename = dirname($filename) . '/config.php')) {
      include($config_filename);
      $module_config_array = $class_module_name . '_config';
      $this->config = $$module_config_array;
    }

    // Registering module
    global $sn_module;
    $sn_module[$class_module_name] = $this;
  }

  protected function __patch_menu(&$sn_menu_extra, &$menu_patch) {
    if(isset($menu_patch) && is_array($menu_patch) && !empty($menu_patch)) {
      foreach($menu_patch as $menu_item_name => $menu_item_data) {
        $sn_menu_extra[$menu_item_name] = $menu_item_data;
      }
    }
  }


  public function initialize() {
    // Checking module status - is it installed and active
    $this->check_status();
    if(!$this->manifest['active']) {
      return;
    }

    // Setting constants - if any
    if(isset($this->manifest['constants']) && is_array($this->manifest['constants']) && !empty($this->manifest['constants'])) {
      foreach($this->manifest['constants'] as $constant_name => $constant_value) {
        defined($constant_name) or define($constant_name, $constant_value);
      }
    }

    // Adding vars - if any
    // Due to possible introduce of new constants in previous step vars is assigned via special method to honor new constants
    // Assignation can work with simple variables and with multidimensional arrays - for ex. 'sn_data[groups][test]'
    // New values from module variables will overwrite previous values (for root variables) and array elements with corresponding indexes (for arrays)
    // Constants as array indexes are honored - it's make valid such declarations as 'sn_data[ques][QUE_STRUCTURES]'
    $this->manifest['vars'] = $this->__assign_vars();
    if(!empty($this->manifest['vars'])) {
      $vars_assigned = array();
      foreach($this->manifest['vars'] as $var_name => $var_value) {
        $sub_vars = explode('[', str_replace(']', '', $var_name));
        $var_name = $sub_vars[0];

        if(!isset($vars_assigned[$var_name])) {
          $vars_assigned[$var_name] = true;
          global $$var_name;
        }

        $pointer = &$$var_name;
        if(($n = count($sub_vars)) > 1) {
          for($i = 1; $i < $n; $i++) {
            if(defined($sub_vars[$i])) {
              $sub_vars[$i] = constant($sub_vars[$i]);
            }

            if(!isset($pointer[$sub_vars[$i]]) && $i != $n) {
              $pointer[$sub_vars[$i]] = array();
            }
            $pointer = &$pointer[$sub_vars[$i]];
          }
        }

        if(!isset($pointer) || !is_array($pointer)) {
          $pointer = $var_value;
        } elseif(is_array($$var_name)) {
          $pointer = array_merge_recursive_numeric($pointer, $var_value);
        }
      }
    }

    // Overriding function if any
    global $functions;
    sn_sys_handler_add($functions, $this->manifest['functions'], $this);

    // Patching game menu - if any
    global $sn_menu_extra, $sn_menu_admin_extra;
    isset($this->manifest['menu']) and $this->__patch_menu($sn_menu_extra, $this->manifest['menu']);
    isset($this->manifest['menu_admin']) and $this->__patch_menu($sn_menu_admin_extra, $this->manifest['menu_admin']);

    global $sn_mvc;
    foreach($sn_mvc as $handler_type => &$handler_data) {
      sn_sys_handler_add($handler_data, $this->manifest['mvc'][$handler_type], $this, $handler_type);
    }

    if(isset($this->manifest['i18n']) && is_array($this->manifest['i18n']) && !empty($this->manifest['i18n'])) {
      foreach($this->manifest['i18n'] as $i18n_page_name => &$i18n_file_list) {
        foreach($i18n_file_list as &$i18n_file_data) {
          if(is_array($i18n_file_data) && !$i18n_file_data['path']) {
            $i18n_file_data['path'] = $this->manifest['root_relative'];
          }
        }
        if(!isset($sn_mvc['i18n'][$i18n_page_name])) {
          $sn_mvc['i18n'][$i18n_page_name] = array();
        }
        $sn_mvc['i18n'][$i18n_page_name] += $i18n_file_list;
      }
    }
  }

  function check_status() {
  }
}

abstract class sn_module_payment extends sn_module {
/*
  static $bonus_table = array(
      2500 => 0,
     // 5000 => 0,
     10000 => 0,
     25000 => 0,

     50000 => 0.02,
    100000 => 0.04,
    150000 => 0.05,
    200000 => 0.07,
    250000 => 0.11,
    380000 => 0.15,
    500000 => 0.22,
    750000 => 0.33,
   1000000 => 0.44,
   1250000 => 0.55,
   1500000 => 0.57,
   2500000 => 0.60,
  );
*/
  static $bonus_table = array(
      2000 => 0,
     // 5000 => 0,
     10000 => 0,
     20000 => 0,

     50000 => 0.02,
    100000 => 0.05,
    200000 => 0.07,
    300000 => 0.10,
    400000 => 0.15,
    500000 => 0.20,
    800000 => 0.25,
   1000000 => 0.30,
   1500000 => 0.40,
   2000000 => 0.50,
   3000000 => 0.60,
   5000000 => 0.70,
  );

  static $payment_methods = array(
    PAYMENT_METHOD_BANK_CARD => array(
      /*
      PAYMENT_METHOD_id => array(
        'currency' => 'WMR', // Currency code 3 letter
        'image' => 'design/images/payments/emoney/webmoney.png', // Optional - image location from root. Setting image disables buttoning and name printing
        'name' => true, // Optional. Forces method name printing with 'image' set
        'button' => true, // Optional. Forces method buttoning with 'image' set
      ),
      */
      PAYMENT_METHOD_BANK_CARD_STANDARD => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/card/generic.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_CARD_LIQPAY => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/card/liqpay.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_CARD_EASYPAY => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/card/easypay.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => array(
        'currency' => 'USD',
        'image' => 'design/images/payments/card/american_express.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_CARD_JCB => array(
        'currency' => 'USD',
        'image' => 'design/images/payments/card/jcb.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_CARD_UNIONPAY => array(
        'currency' => 'USD',
        'image' => 'design/images/payments/card/unionpay.png',
        'button' => true,
      ),
    ),

    PAYMENT_METHOD_EMONEY => array(
      PAYMENT_METHOD_EMONEY_YANDEX => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/yandexmoney.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_QIWI => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/qiwi.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_PAYPAL => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/paypal.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => array(
        'currency' => 'WMR',
        'image' => 'design/images/payments/emoney/webmoney_wmr.gif',
      ),
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => array(
        'currency' => 'WMZ',
        'image' => 'design/images/payments/emoney/webmoney_wmz.gif',
      ),
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => array(
        'currency' => 'WMU',
        'image' => 'design/images/payments/emoney/webmoney_wmu.gif',
      ),
      PAYMENT_METHOD_EMONEY_WEBMONEY_WME => array(
        'currency' => 'WME',
        'image' => 'design/images/payments/emoney/webmoney_wme.gif',
      ),
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => array(
        'currency' => 'WMB',
        'image' => 'design/images/payments/emoney/webmoney_wmb.gif',
      ),
      PAYMENT_METHOD_EMONEY_TELEMONEY => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/telemoney.gif',
      ),
      PAYMENT_METHOD_EMONEY_ELECSNET => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/elecsnet.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_EASYPAY => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/easypay.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_RUR_W1R => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/walletone.png',
        'button' => true,
      ),
      PAYMENT_METHOD_EMONEY_MAILRU => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/emoney/mailru.gif',
      ),
    ),

    PAYMENT_METHOD_MOBILE => array(
      PAYMENT_METHOD_MOBILE_SMS => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/mobile/sms.png',
        'name' => true,
        'button' => true,
      ),
      PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => array(
        'currency' => 'USD',
        'image' => 'design/images/payments/mobile/paypal_zong.png',
        'name' => true,
        'button' => true,
      ),
      PAYMENT_METHOD_MOBILE_XSOLLA => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/mobile/xsolla.png',
        'name' => true,
        'button' => true,
      ),


      PAYMENT_METHOD_MOBILE_MEGAPHONE => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/mobile/megafon.png',
        'button' => true,
      ),
      PAYMENT_METHOD_MOBILE_MTS => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/mobile/mts.png',
        'button' => true,
      ),
      PAYMENT_METHOD_MOBILE_KYIVSTAR => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/mobile/kyivstar.png',
        'button' => true,
      ),
    ),

    PAYMENT_METHOD_BANK_INTERNET => array(
      PAYMENT_METHOD_BANK_INTERNET_PRIVAT24 => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/bank_internet/privat24.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_BANK24 => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/bank_internet/bank24.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/alfa_bank.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_SBERBANK => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/sberbank.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/prosmvyazbank.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/handy_bank.png',
        'button' => true,
      ),
      PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/russkiy_standart.gif',
      ),
      PAYMENT_METHOD_BANK_INTERNET_VTB24 => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/vtb24.gif',
      ),
      PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/bank_internet/ocean_bank.gif',
      ),
      PAYMENT_METHOD_BANK_INTERNET_007 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_008 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_009 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_010 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_011 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_012 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_013 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_014 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_015 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_016 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_017 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_018 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_019 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_020 => array(
        'currency' => 'RUB',
      ),
      PAYMENT_METHOD_BANK_INTERNET_021 => array(
        'currency' => 'RUB',
      ),
    ),

    PAYMENT_METHOD_BANK_TRANSFER => array(
    ),

    PAYMENT_METHOD_TERMINAL => array(
      PAYMENT_METHOD_TERMINAL_UKRAINE => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/terminal/ukraine.png',
        'button' => true,
        'name' => true,
      ),
      PAYMENT_METHOD_TERMINAL_IBOX => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/terminal/ibox.png',
        'button' => true,
      ),
      PAYMENT_METHOD_TERMINAL_EASYPAY => array(
        'currency' => 'UAH',
        'image' => 'design/images/payments/terminal/easypay.png',
        'button' => true,
      ),
      PAYMENT_METHOD_TERMINAL_RUSSIA => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/russia.png',
        'button' => true,
        'name' => true,
      ),
      PAYMENT_METHOD_TERMINAL_QIWI => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/qiwi.png',
        'button' => true,
      ),
      PAYMENT_METHOD_TERMINAL_ELECSNET => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/elecsnet.png',
        'button' => true,
      ),
      PAYMENT_METHOD_TERMINAL_TELEPAY => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/telepay.png',
        'button' => true,
      ),
      PAYMENT_METHOD_TERMINAL_ELEMENT => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/element.gif',
      ),
      PAYMENT_METHOD_TERMINAL_KASSIRANET => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/terminal/kassira_net.gif',
        'button' => true,
      ),
    ),

    PAYMENT_METHOD_OTHER => array(
      PAYMENT_METHOD_OTHER_EVROSET => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/other/evroset.gif',
      ),
      PAYMENT_METHOD_OTHER_SVYAZNOY => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/other/svyaznoy.gif',
      ),
      PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/other/robokassa_mobile.gif',
        'name' => true,
      ),
    ),

    PAYMENT_METHOD_GENERIC => array(
      PAYMENT_METHOD_GENERIC_XSOLLA => array(
        'currency' => 'USD',
        'image' => 'design/images/payments/generic/xsolla.png',
        'name' => true,
        'button' => true,
      ),

      PAYMENT_METHOD_GENERIC_ROBOKASSA => array(
        'currency' => 'RUB',
        'image' => 'design/images/payments/generic/robokassa.jpeg',
        // 'name' => true,
        'button' => true,
      ),
    ),
  );

  // Function converts money values between currencies
  static function currency_convert($value, $currency_from = '', $currency_to = '', $round = 2) {
    global $config;

    if(strtolower($currency_from) != strtolower($currency_to)) {
      $exchange_from = ($exchange_from = $config->__get('payment_currency_exchange_' . strtolower($currency_from))) ? $exchange_from : 0;
      $exchange_to = ($exchange_to = $config->__get('payment_currency_exchange_' . strtolower($currency_to))) ? $exchange_to : 0;

      // $value = $value / $exchange_from * $exchange_to;
      // $value = round($value, $currency_to == 'MM_' ? 0 : 2);

      $value = $exchange_from ? $value / $exchange_from * $exchange_to * pow(10, $round) : 0;
      $value = ceil($value) / pow(10, $round);
    }

    return $value;
  }

  // Function calculates bonused DM amount for bulk purchase and ($direct = false) vice versa
  static function bonus_calculate($dark_matter, $direct = true, $return_bonus = false) {
    $bonus = 0;
    $dark_matter_new = $dark_matter;
    if(!empty(self::$bonus_table) && $dark_matter >= self::$bonus_table[0]) {
      if($direct) {
        foreach(self::$bonus_table as $dm_for_bonus => $multiplyer) {
          if($dm_for_bonus <= $dark_matter) {
            $dark_matter_new = $dark_matter * (1 + $multiplyer);
            $bonus = $multiplyer;
          } else {
            break;
          }
        }
      } else {
        foreach(self::$bonus_table as $dm_for_bonus => $multiplyer) {
          $temp = $dm_for_bonus * (1 + $multiplyer);
          if($dark_matter >= $temp) {
            $dark_matter_new = round($dark_matter / (1 + $multiplyer));
            $bonus = $multiplyer;
          } else {
            break;
          }
        }
      }
    }

    return $return_bonus ? $bonus : $dark_matter_new;
  }

  /*
  // Function calculates amount of dark_matter for entered money and vice versa
  static function exchange($dark_matter = 0, $money = 0, $currency = '')
  {
    global $config;

    if(!$dark_matter && !$money)
    {
      return 0;
    }

    $currency = $currency ? $currency : $config->payment_currency_default;
    if($money)
    {
      $dark_matter = $money * $config->payment_lot_size / $config->payment_lot_price;
      // $bonus = ($dark_matter - ($dark_matter % 100000)) / 100000 / 10;
      // $bonus = min(0.5, $bonus);
      // $dark_matter *= 1 + $bonus;
      return floor($dark_matter);
    }
    elseif($dark_matter)
    {
      $money = $dark_matter * $config->payment_lot_price / $config->payment_lot_size;

      return round($money, 2);
    }
  }
  */

  // Дополнительная ре-трансляция адреса, если в каком-то случае платежная система ожидает нелогичный ответ
  // Пример: иксолла при неправильно заданном пользователе в ордере ожидает НЕПРАВИЛЬНЫЙ_ОРДЕР, а не НЕПРАВИЛЬНЫЙ_ПОЛЬЗОВАТЕЛЬ
  function retranslate_error($error_code, $options = array()) {
    return isset($options['retranslate_error'][$error_code]) ? $options['retranslate_error'][$error_code] : $error_code;
  }


  function payment_request_process($options = array()) {
    global $lang;

    if(!$this->manifest['active']) {
      throw new exception($lang['pay_msg_module_disabled'], SN_MODULE_DISABLED);
    }

    if(SN_ROOT_VIRTUAL != $options['server_id']) {
      throw new exception($lang['pay_msg_request_server_wrong'] . " {$options['server_id']} вместо " . SN_ROOT_VIRTUAL, SN_PAYMENT_REQUEST_SERVER_WRONG);
    }

    if(!$options['user_id'] || !$payer = db_user_by_id($options['user_id'], true)) {
      throw new exception($lang['pay_msg_request_user_invalid'], $this->retranslate_error(SN_PAYMENT_REQUEST_USER_NOT_FOUND, $options));
    }

    return array(
      'payer' => $payer,
    );
  }


  function db_insert_payment(&$payer, &$payment, $options = array()) {
    global $config;

    $payment['payment_test'] = isset($payment['payment_test']) && $payment['payment_test'] ? 1 : 0;

    $payment['payment_status'] = !isset($payment['payment_status']) ? PAYMENT_STATUS_COMPLETE : $payment['payment_status'];

    $payment['payment_user_id'] = !isset($payment['payment_user_id']) || !$payment['payment_user_id'] ? $payer['id'] : $payment['payment_user_id'];
    $payment['payment_user_name'] = !isset($payment['payment_user_name']) || !$payment['payment_user_name'] ? $payer['username'] : $payment['payment_user_name'];

    $payment['payment_module_name'] = $this->manifest['name'];
    $payment['payment_currency'] = $config->payment_currency_default;

    // $payment['payment_amount'] - уникальный для каждого модуля. TODO: Поднять ошибку если пустое или 0
    // $payment['payment_dark_matter_paid'] - посмотреть, нельзя ли как-то унифицировать. TODO: Поднять ошибку если пустое или 0

    $payment['payment_dark_matter_gained'] = self::bonus_calculate($payment['payment_dark_matter_paid'], true);

    // $payment['payment_external_date'] - свой для каждого модуля

    $payment['payment_external_lots'] = !isset($payment['payment_external_lots']) || !$payment['payment_external_lots']
      ? $payment['payment_dark_matter_paid'] / $config->payment_currency_exchange_mm_
      : $payment['payment_external_lots'];

    // $payment['payment_external_amount'] - уникальный для каждого модуля. TODO: Поднять ошибку если пустое или 0
    // $payment['payment_external_currency'] - уникальный для каждого модуля. TODO: Поднять ошибку если пустое или 0
    // $payment['payment_external_id'] - свой для каждого модуля


    // TODO - системная локализация
    $payment['payment_comment'] = 
      ($payment['payment_test'] ? "ТЕСТОВЫЙ ПЛАТЕЖ! " : '') .
      "Платеж от игрока '{$payment['payment_user_name']}' ID {$payment['payment_user_id']} на сервере " . SN_ROOT_VIRTUAL .
      " сумма {$payment['payment_amount']} {$payment['payment_currency']} за {$payment['payment_dark_matter_paid']} ММ (начислено {$payment['payment_dark_matter_gained']} ММ)" .
      " через '{$payment['payment_module_name']}' сумма {$payment['payment_external_amount']} {$payment['payment_external_currency']}"
    ;

    $query = array();
    foreach($payment as $key => $value)
    {
      $value = is_string($value) ? '"' . db_escape($value) . '"' : $value;
      $query[] = "`{$key}` = {$value}";
    }

    $query = (isset($options['replace']) && $options['replace'] ? 'REPLACE' : 'INSERT') . ' INTO `{{payment}}` SET ' . implode(',', $query) . ';';
    doquery($query);

    /*
    if(isset($options['test']) && $options['test'])
    {
      // TEST payment
    }

    if(isset($options['replace']) && $options['replace'])
    {
      // do REPLACE instead of insert
    }
    // doquery("INSERT INTO {{payment}} SET
    */

    $payment_id = db_insert_id();
    // $payment = doquery("SELECT * FROM `{{payment}}` WHERE `payment_id` = {$payment_id}", true);

    return $payment_id;
  }

  // Response to payment system request
  function payment_request_response() {
    global $debug;

    sn_db_transaction_start();
    try {
      $response = $this->payment_request_process();
    } catch(exception $e) {
      $response['result'] = $e->getCode();
      $response['message'] = $e->getMessage();
    }

    if($response['result'] == SN_PAYMENT_REQUEST_OK) {
      sn_db_transaction_commit();
      // $debug->warning('Результат операции: код ' . $response['result'] . ' сообщение "' . $response['message'] . '"', 'Ошибка платежа', LOG_INFO_PAYMENT);
    } else {
      sn_db_transaction_rollback();
      $debug->warning('Результат операции: код ' . $response['result'] . ' сообщение "' . $response['message'] . '"', 'Ошибка платежа', LOG_INFO_PAYMENT);
    }

    // Переводим код результата из СН в код платежной системы
    if(is_array($this->result_translations) && !empty($this->result_translations)) {
      $response['result'] = isset($this->result_translations[$response['result']]) ? $this->result_translations[$response['result']] : $this->result_translations[SN_PAYMENT_REQUEST_UNDEFINED_ERROR];
    }

    return $response;
  }

  function payment_adjust_mm(&$payment) {
    if(!$payment['payment_test']) {
      // Not a test payment. Adding DM to account
      $result = mm_points_change($payment['payment_user_id'], RPG_PURCHASE, $payment['payment_dark_matter_gained'], $payment['payment_comment']);
      if(!$result) {
        throw new exception('Ошибка начисления ММ', SN_METAMATTER_ERROR_ADJUST);
      }
    }
  }

  function payment_cancel(&$payment) {
    global $lang;

    if(!isset($payment['payment_status'])) {
      throw new exception($lang['pay_msg_request_payment_not_found'], SN_PAYMENT_REQUEST_ORDER_NOT_FOUND);
    }

    if($payment['payment_status'] == PAYMENT_STATUS_COMPLETE) {
      $safe_comment = db_escape($payment['payment_comment'] = $lang['pay_msg_request_payment_cancelled'] .' ' . $payment['payment_comment']);

      if(!$payment['payment_test']) {
        $result = mm_points_change($payment['payment_user_id'], RPG_PURCHASE_CANCEL, -$payment['payment_dark_matter_gained'], $payment['payment_comment']);
        if(!$result) {
          throw new exception('Ошибка начисления ММ', SN_METAMATTER_ERROR_ADJUST);
        }
      }
      $payment['payment_status'] = PAYMENT_STATUS_CANCELED;
      doquery("UPDATE {{payment}} SET payment_status = {$payment['payment_status']}, payment_comment = '{$safe_comment}' WHERE payment_id = {$payment['payment_id']};");
      throw new exception($lang['pay_msg_request_payment_cancel_complete'], SN_PAYMENT_REQUEST_OK);
    } elseif($payment['payment_status'] == PAYMENT_STATUS_CANCELED) {
      throw new exception($lang['pay_msg_request_payment_cancelled_already'], SN_PAYMENT_REQUEST_OK);
    } elseif($payment['payment_status'] == PAYMENT_STATUS_NONE) {
      throw new exception($lang['pay_msg_request_payment_cancel_not_complete'], SN_PAYMENT_REQUEST_PAYMENT_NOT_COMPLETE);
    }
  }
}
