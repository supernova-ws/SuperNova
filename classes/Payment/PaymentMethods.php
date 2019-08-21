<?php
/**
 * Created by Gorlum 21.08.2019 11:53
 */

namespace Payment;


use HelperArray;
use Modules\sn_module;
use SN;
use sn_module_payment;

class PaymentMethods {
  /**
   * @var array $payment_methods
   */
  protected static $payment_methods = [
    PAYMENT_METHOD_BANK_CARD => [
      /*
      PAYMENT_METHOD_id => array(
        'currency' => 'WMR', // Currency code 3 letter
        'image' => 'design/images/payments/emoney/webmoney.png', // Optional - image location from root. Setting image disables buttoning and name printing
        'name' => true, // Optional. Forces method name printing with 'image' set
        'button' => true, // Optional. Forces method buttoning with 'image' set
      ),
      */
      PAYMENT_METHOD_BANK_CARD_STANDARD         => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/card/generic.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_CARD_LIQPAY           => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/card/liqpay.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_CARD_EASYPAY          => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/card/easypay.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => [
        'currency' => 'USD',
        'image'    => 'design/images/payments/card/american_express.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_CARD_JCB              => [
        'currency' => 'USD',
        'image'    => 'design/images/payments/card/jcb.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_CARD_UNIONPAY         => [
        'currency' => 'USD',
        'image'    => 'design/images/payments/card/unionpay.png',
        'button'   => true,
      ],
    ],

    PAYMENT_METHOD_EMONEY => [
      PAYMENT_METHOD_EMONEY_YANDEX       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/yandexmoney.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_QIWI         => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/qiwi.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_PAYPAL       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/paypal.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => [
//        'currency' => 'WMR',
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/webmoney_wmr.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => [
//        'currency' => 'WMZ',
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/webmoney_wmz.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => [
//        'currency' => 'WMU',
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/webmoney_wmu.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WME => [
//        'currency' => 'WME',
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/webmoney_wme.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => [
//        'currency' => 'WMB',
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/webmoney_wmb.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_TELEMONEY    => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/telemoney.gif',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_ELECSNET     => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/elecsnet.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_EASYPAY      => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/easypay.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_RUR_W1R      => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/walletone.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_EMONEY_MAILRU       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/emoney/mailru.gif',
      ],
    ],

    PAYMENT_METHOD_MOBILE => [
      PAYMENT_METHOD_MOBILE_SMS         => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/mobile/sms.png',
        'name'     => true,
        'button'   => true,
      ],
      PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => [
        'currency' => 'USD',
        'image'    => 'design/images/payments/mobile/paypal_zong.png',
        'name'     => true,
        'button'   => true,
      ],
      PAYMENT_METHOD_MOBILE_XSOLLA      => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/mobile/xsolla.png',
        'name'     => true,
        'button'   => true,
      ],


      PAYMENT_METHOD_MOBILE_MEGAPHONE => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/mobile/megafon.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_MOBILE_MTS       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/mobile/mts.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_MOBILE_KYIVSTAR  => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/mobile/kyivstar.png',
        'button'   => true,
      ],
    ],

    PAYMENT_METHOD_BANK_INTERNET => [
      PAYMENT_METHOD_BANK_INTERNET_PRIVAT24         => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/bank_internet/privat24.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_BANK24           => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/bank_internet/bank24.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK        => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/alfa_bank.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_SBERBANK         => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/sberbank.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK    => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/prosmvyazbank.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/handy_bank.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/russkiy_standart.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_VTB24            => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/vtb24.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/bank_internet/ocean_bank.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_007              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_008              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_009              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_010              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_011              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_012              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_013              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_014              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_015              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_016              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_017              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_018              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_019              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_020              => [
        'currency' => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_021              => [
        'currency' => 'RUB',
      ],
    ],

    PAYMENT_METHOD_BANK_TRANSFER => [],

    PAYMENT_METHOD_TERMINAL => [
      PAYMENT_METHOD_TERMINAL_UKRAINE    => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/terminal/ukraine.png',
        'button'   => true,
        'name'     => true,
      ],
      PAYMENT_METHOD_TERMINAL_IBOX       => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/terminal/ibox.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_TERMINAL_EASYPAY    => [
        'currency' => 'UAH',
        'image'    => 'design/images/payments/terminal/easypay.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_TERMINAL_RUSSIA     => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/russia.png',
        'button'   => true,
        'name'     => true,
      ],
      PAYMENT_METHOD_TERMINAL_QIWI       => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/qiwi.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_TERMINAL_ELECSNET   => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/elecsnet.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_TERMINAL_TELEPAY    => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/telepay.png',
        'button'   => true,
      ],
      PAYMENT_METHOD_TERMINAL_ELEMENT    => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/element.gif',
      ],
      PAYMENT_METHOD_TERMINAL_KASSIRANET => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/terminal/kassira_net.gif',
        'button'   => true,
      ],
    ],

    PAYMENT_METHOD_OTHER => [
      PAYMENT_METHOD_OTHER_EVROSET          => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/other/evroset.gif',
      ],
      PAYMENT_METHOD_OTHER_SVYAZNOY         => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/other/svyaznoy.gif',
      ],
      PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => [
        'currency' => 'RUB',
        'image'    => 'design/images/payments/other/robokassa_mobile.gif',
        'name'     => true,
      ],
    ],

    PAYMENT_METHOD_GENERIC => [],
  ];

  /**
   * Ordered list of payment types
   *
   * @var int[] $paymentTypes
   */
  protected static $paymentTypes = [
    PAYMENT_METHOD_BANK_CARD     => PAYMENT_METHOD_BANK_CARD,
    PAYMENT_METHOD_EMONEY        => PAYMENT_METHOD_EMONEY,
    PAYMENT_METHOD_MOBILE        => PAYMENT_METHOD_MOBILE,
    PAYMENT_METHOD_BANK_INTERNET => PAYMENT_METHOD_BANK_INTERNET,
    PAYMENT_METHOD_BANK_TRANSFER => PAYMENT_METHOD_BANK_TRANSFER,
    PAYMENT_METHOD_TERMINAL      => PAYMENT_METHOD_TERMINAL,
    PAYMENT_METHOD_OTHER         => PAYMENT_METHOD_OTHER,
    PAYMENT_METHOD_GENERIC       => PAYMENT_METHOD_GENERIC,
  ];

  /**
   * List of installed modules
   *
   * @var array $modulesInstalled
   */
  protected static $modulesInstalled = [];

  /**
   * @var array $payment_methods_available
   */
  protected static $payment_methods_available = [];

  /**
   * Add payment method to repository
   *
   * @param int   $type   Provider type
   * @param int   $id     Provider ID
   * @param array $data   Info about method [
   *                      'currency' => (str)currencyId3Letters,
   *                      'image'    => (str)$pathToImage,
   *                      'name'     => (bool)$showName,
   *                      'button'   => (bool)$showButton,
   *                      ]
   */
  public static function addPaymentProvider($type, $id, array $data) {
    PaymentMethods::$payment_methods[$type][$id] = $data;
  }

  /**
   * @return array
   */
  public static function getPaymentTypeAndMethod() {
    $payment_type_selected   = sys_get_param_int('payment_type');
    $payment_method_selected = sys_get_param_int('payment_method');

    if (empty($payment_type_selected) || empty(PaymentMethods::$payment_methods[$payment_type_selected])) {
      $payment_type_selected = HelperArray::array_key_first(PaymentMethods::$payment_methods);
    }

    if (empty($payment_method_selected) || empty(PaymentMethods::$payment_methods[$payment_type_selected][$payment_method_selected])) {
      $payment_method_selected = HelperArray::array_key_first(PaymentMethods::$payment_methods[$payment_type_selected]);
    }

    return [$payment_type_selected, $payment_method_selected];
  }

  /**
   */
  protected static function fillSupportedMethods() {
    // Генерируем список типов платежей, что бы он был упорядоченный
    PaymentMethods::$payment_methods_available = array_combine(array_keys(PaymentMethods::$payment_methods), array_fill(0, count(PaymentMethods::$payment_methods), null));
    // По каждому типу генерируем список методов, что бы он был упорядоченный
    array_walk(PaymentMethods::$payment_methods_available, function (&$value, $index) {
      $value = !empty(PaymentMethods::$payment_methods[$index]) ? array_combine(array_keys(PaymentMethods::$payment_methods[$index]), array_fill(0, count(PaymentMethods::$payment_methods[$index]), null)) : $value;
    });

    // А теперь из каждого модуля вытаскиваем методы, которые он поддерживает
    $paymentModuleList = SN::$gc->modules->getModulesInGroup('payment', true);
    foreach ($paymentModuleList as $module_name => $module) {
      /**
       * @var sn_module $module
       */
      if (!is_object($module) || !$module->isActive()) {
        continue;
      }

      lng_include($module_name, $module->getRootRelative());

      foreach (PaymentMethods::$payment_methods as $payment_type_id => $available_methods) {
        foreach ($available_methods as $payment_method => $payment_currency) {
          if (isset($module->manifest['payment_method'][$payment_method])) {
            PaymentMethods::$payment_methods_available[$payment_type_id][$payment_method][$module_name] = $module->manifest['payment_method'][$payment_method];
          }
        }
      }

      self::$modulesInstalled[] = $module_name;
    }
  }

  /**
   * @return array
   */
  public static function renderPaymentMethodList() {
    // Доступные платежные методы
    $paymentMethodList = [];

    foreach (PaymentMethods::$payment_methods_available as $payment_type_id => $payment_methods) {
      if (empty($payment_methods)) {
        continue;
      }

      $paymentMethodList[$payment_type_id] = [
        'ID'   => $payment_type_id,
        'NAME' => SN::$lang['pay_methods'][$payment_type_id],
      ];
      foreach ($payment_methods as $payment_method_id => $module_list) {
        if (empty($module_list)) {
          continue;
        }
        $paymentMethodList[$payment_type_id]['.']['method'][$payment_method_id] = [
          'ID'         => $payment_method_id,
          'NAME'       => SN::$lang['pay_methods'][$payment_method_id],
          'IMAGE'      => !empty(PaymentMethods::$payment_methods[$payment_type_id][$payment_method_id]['image'])
            ? PaymentMethods::$payment_methods[$payment_type_id][$payment_method_id]['image'] : '',
          'NAME_FORCE' => !empty(PaymentMethods::$payment_methods[$payment_type_id][$payment_method_id]['name']),
          'BUTTON'     => !empty(PaymentMethods::$payment_methods[$payment_type_id][$payment_method_id]['button']),
        ];
        foreach ($module_list as $payment_module_name => $payment_module_method_details) {
          $paymentMethodList[$payment_type_id]['.']['method'][$payment_method_id]['.']['module'][] = array(
            'MODULE' => $payment_module_name,
          );
        }
      }

      if (empty($paymentMethodList[$payment_type_id]['.'])) {
        unset($paymentMethodList[$payment_type_id]);
      }
    }

    return $paymentMethodList;
  }

  /**
   * @return mixed
   */
  public static function getFirstModule() {
    return reset(PaymentMethods::$modulesInstalled);
  }

  /**
   * @param string $payment_module_request
   * @param int    $payment_type_selected
   * @param int    $payment_method_selected
   *
   * @return int|mixed|string|null
   */
  public static function fixPaymentModuleSelection($payment_module_request, $payment_type_selected, $payment_method_selected) {
    $payment_module_valid =
      in_array($payment_module_request, self::$modulesInstalled)
      && (
        !$payment_method_selected
        || isset(PaymentMethods::$payment_methods_available[$payment_type_selected][$payment_method_selected][$payment_module_request]
        )
      );


    // If payment_module invalid - making it empty OR if there is only one payment_module - selecting it
    if ($payment_module_valid) {
      // $payment_module = $payment_module; // Really - do nothing
    } elseif ($payment_type_selected && count(PaymentMethods::$payment_methods_available[$payment_type_selected][$payment_method_selected]) == 1) {
      reset(PaymentMethods::$payment_methods_available[$payment_type_selected][$payment_method_selected]);
      $payment_module_request = key(PaymentMethods::$payment_methods_available[$payment_type_selected][$payment_method_selected]);
    } elseif (SN::$gc->modules->countModulesInGroup('payment') == 1) {
      $payment_module_request = PaymentMethods::getFirstModule();
    } else {
      $payment_module_request = '';
    }

    return $payment_module_request;
  }

  /**
   * @param $payment_module_request
   * @param $payment_type_selected
   * @param $payment_method_selected
   *
   * @return int|mixed|string|null
   */
  public static function processInputParams($payment_module_request, $payment_type_selected, $payment_method_selected) {
    PaymentMethods::fillSupportedMethods();
    $payment_module_request = PaymentMethods::fixPaymentModuleSelection($payment_module_request, $payment_type_selected, $payment_method_selected);

    return $payment_module_request;
  }

  /**
   * @param $payment_type_selected
   * @param $payment_method_selected
   * @param $player_currency
   * @param $request
   *
   * @return array
   */
  public static function renderModulesForMethod($payment_type_selected, $payment_method_selected, $player_currency, $request) {
    $q = [];
    foreach (PaymentMethods::$payment_methods_available[$payment_type_selected][$payment_method_selected] as $module_name => $temp) {
      /**
       * @var sn_module_payment $mod
       */
      $mod = SN::$gc->modules->getModule($module_name);

      $aPrice = method_exists($mod, 'getPrice') ? $mod->getPrice($payment_method_selected, $player_currency, $request['metamatter']) : '';

      $k = [
        'ID'          => $module_name,
        'NAME'        => SN::$lang["module_{$module_name}_name"],
        'DESCRIPTION' => SN::$lang["module_{$module_name}_description"],
      ];

      if (is_array($aPrice) && !empty($aPrice)) {
        $k['COST']     = $aPrice[$mod::FIELD_SUM];
        $k['CURRENCY'] = $aPrice[$mod::FIELD_CURRENCY];
      }

      $q[] = $k;
    }

    return $q;
  }

  /**
   * @param $payment_module_request
   * @param $payment_type_selected
   * @param $payment_method_selected
   *
   * @return string
   */
  public static function getCurrencyFromMethod($payment_module_request, $payment_type_selected, $payment_method_selected) {
    return $payment_module_request ? PaymentMethods::$payment_methods[$payment_type_selected][$payment_method_selected]['currency'] : '';
  }

}
