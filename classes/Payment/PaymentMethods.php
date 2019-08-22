<?php
/**
 * Created by Gorlum 21.08.2019 11:53
 */

namespace Payment;

use SN;
use sn_module_payment;

class PaymentMethods {
  const P_CURRENCY = 'currency';

  /**
   * @var array $payment_methods
   */
  public static $payment_methods = [
    PAYMENT_METHOD_BANK_CARD => [
      /*
      PAYMENT_METHOD_id => [
        'currency' => 'WMR', // Currency code 3 letter
        'image' => 'design/images/payments/emoney/webmoney.png', // Optional - image location from root. Setting image disables buttoning and name printing
        'name' => true, // Optional. Forces method name printing with 'image' set
        'button' => true, // Optional. Forces method buttoning with 'image' set
      ],
      */
      PAYMENT_METHOD_BANK_CARD_STANDARD         => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/card/generic.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_CARD_LIQPAY           => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/card/liqpay.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_CARD_EASYPAY          => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/card/easypay.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => [
        self::P_CURRENCY => 'USD',
        'image'          => 'design/images/payments/card/american_express.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_CARD_JCB              => [
        self::P_CURRENCY => 'USD',
        'image'          => 'design/images/payments/card/jcb.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_CARD_UNIONPAY         => [
        self::P_CURRENCY => 'USD',
        'image'          => 'design/images/payments/card/unionpay.png',
        'button'         => true,
      ],
    ],

    PAYMENT_METHOD_EMONEY => [
      PAYMENT_METHOD_EMONEY_YANDEX       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/yandexmoney.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_QIWI         => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/qiwi.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_PAYPAL       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/paypal.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => [
//        'currency' => 'WMR',
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/webmoney_wmr.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => [
//        'currency' => 'WMZ',
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/webmoney_wmz.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => [
//        'currency' => 'WMU',
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/webmoney_wmu.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WME => [
//        'currency' => 'WME',
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/webmoney_wme.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => [
//        'currency' => 'WMB',
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/webmoney_wmb.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_TELEMONEY    => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/telemoney.gif',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_ELECSNET     => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/elecsnet.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_EASYPAY      => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/easypay.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_RUR_W1R      => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/walletone.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_EMONEY_MAILRU       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/emoney/mailru.gif',
      ],
    ],

    PAYMENT_METHOD_MOBILE => [
      PAYMENT_METHOD_MOBILE_SMS         => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/mobile/sms.png',
        'name'           => true,
        'button'         => true,
      ],
      PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => [
        self::P_CURRENCY => 'USD',
        'image'          => 'design/images/payments/mobile/paypal_zong.png',
        'name'           => true,
        'button'         => true,
      ],


      PAYMENT_METHOD_MOBILE_MEGAPHONE => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/mobile/megafon.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_MOBILE_MTS       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/mobile/mts.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_MOBILE_BEELINE   => [
        self::P_CURRENCY => 'RUB',
//        'image'    => 'design/images/payments/mobile/mts.png',
        'name'           => true,
//        'button'   => true,
      ],
      PAYMENT_METHOD_MOBILE_KYIVSTAR  => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/mobile/kyivstar.png',
        'button'         => true,
      ],
    ],

    PAYMENT_METHOD_BANK_INTERNET => [
      PAYMENT_METHOD_BANK_INTERNET_PRIVAT24         => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/bank_internet/privat24.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_BANK24           => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/bank_internet/bank24.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK        => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/alfa_bank.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_SBERBANK         => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/sberbank.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK    => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/prosmvyazbank.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/handy_bank.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/russkiy_standart.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_VTB24            => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/vtb24.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/bank_internet/ocean_bank.gif',
      ],
      PAYMENT_METHOD_BANK_INTERNET_007              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_008              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_009              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_010              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_011              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_012              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_013              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_014              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_015              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_016              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_017              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_018              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_019              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_020              => [
        self::P_CURRENCY => 'RUB',
      ],
      PAYMENT_METHOD_BANK_INTERNET_021              => [
        self::P_CURRENCY => 'RUB',
      ],
    ],

    PAYMENT_METHOD_BANK_TRANSFER => [],

    PAYMENT_METHOD_TERMINAL => [
      PAYMENT_METHOD_TERMINAL_UKRAINE    => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/terminal/ukraine.png',
        'button'         => true,
        'name'           => true,
      ],
      PAYMENT_METHOD_TERMINAL_IBOX       => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/terminal/ibox.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_TERMINAL_EASYPAY    => [
        self::P_CURRENCY => 'UAH',
        'image'          => 'design/images/payments/terminal/easypay.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_TERMINAL_RUSSIA     => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/russia.png',
        'button'         => true,
        'name'           => true,
      ],
      PAYMENT_METHOD_TERMINAL_QIWI       => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/qiwi.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_TERMINAL_ELECSNET   => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/elecsnet.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_TERMINAL_TELEPAY    => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/telepay.png',
        'button'         => true,
      ],
      PAYMENT_METHOD_TERMINAL_ELEMENT    => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/element.gif',
      ],
      PAYMENT_METHOD_TERMINAL_KASSIRANET => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/terminal/kassira_net.gif',
        'button'         => true,
      ],
    ],

    PAYMENT_METHOD_OTHER => [
      PAYMENT_METHOD_OTHER_EVROSET          => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/other/evroset.gif',
      ],
      PAYMENT_METHOD_OTHER_SVYAZNOY         => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/other/svyaznoy.gif',
      ],
      PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => [
        self::P_CURRENCY => 'RUB',
        'image'          => 'design/images/payments/other/robokassa_mobile.gif',
        'name'           => true,
      ],
    ],

    PAYMENT_METHOD_GENERIC => [],
  ];

//  /**
//   * Ordered list of payment types
//   *
//   * @var int[] $paymentTypes
//   */
//  protected static $paymentTypes = [
//    PAYMENT_METHOD_BANK_CARD     => PAYMENT_METHOD_BANK_CARD,
//    PAYMENT_METHOD_EMONEY        => PAYMENT_METHOD_EMONEY,
//    PAYMENT_METHOD_MOBILE        => PAYMENT_METHOD_MOBILE,
//    PAYMENT_METHOD_BANK_INTERNET => PAYMENT_METHOD_BANK_INTERNET,
//    PAYMENT_METHOD_BANK_TRANSFER => PAYMENT_METHOD_BANK_TRANSFER,
//    PAYMENT_METHOD_TERMINAL      => PAYMENT_METHOD_TERMINAL,
//    PAYMENT_METHOD_OTHER         => PAYMENT_METHOD_OTHER,
//    PAYMENT_METHOD_GENERIC       => PAYMENT_METHOD_GENERIC,
//  ];

  protected static $methodsActive = null;

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
  public static function renderPaymentMethodList() {
    // Доступные платежные методы
    $paymentMethodList = [];

    foreach (PaymentMethods::$payment_methods as $payment_type_id => $payment_methods_of_type) {
      if (empty($payment_methods_of_type)) {
        continue;
      }

      foreach ($payment_methods_of_type as $payment_method_id => $methodDetails) {
        if (!self::getActiveMethods()->getModuleCount($payment_method_id)) {
          continue;
        }

        $paymentMethodList[$payment_type_id]['.']['method'][$payment_method_id] = [
          'ID'         => $payment_method_id,
          'NAME'       => SN::$lang['pay_methods'][$payment_method_id],
          'IMAGE'      => !empty($methodDetails['image']) ? $methodDetails['image'] : '',
          'NAME_FORCE' => !empty($methodDetails['name']),
          'BUTTON'     => !empty($methodDetails['button']),
        ];
        foreach (self::getActiveMethods()->getModulesOnMethod($payment_method_id) as $payment_module_name => $payment_module) {
          $paymentMethodList[$payment_type_id]['.']['method'][$payment_method_id]['.']['module'][] = [
            'MODULE' => $payment_module_name,
          ];
        }
      }

      if (!empty($paymentMethodList[$payment_type_id]['.'])) {
        $paymentMethodList[$payment_type_id] = array_merge(
          $paymentMethodList[$payment_type_id],
          [
            'ID'   => $payment_type_id,
            'NAME' => SN::$lang['pay_methods'][$payment_type_id],
          ]
        );
      }
    }

    return $paymentMethodList;
  }

  /**
   * @param $paymentMethodId
   * @param $player_currency
   * @param $request
   *
   * @return array
   */
  public static function renderModulesForMethod($paymentMethodId, $player_currency, $request) {
    $block = [];
    foreach (self::getActiveMethods()->getModulesOnMethod($paymentMethodId) as $module_name => $module) {
      /**
       * @var sn_module_payment $module
       */
      $aPrice = $module->getPrice($paymentMethodId, $player_currency, $request['metamatter']);

      $row = [
        'ID'          => $module_name,
        'NAME'        => SN::$lang["module_{$module_name}_name"],
        'DESCRIPTION' => SN::$lang["module_{$module_name}_description"],
      ];

      if (is_array($aPrice) && !empty($aPrice)) {
        $row['COST']     = $aPrice[$module::FIELD_SUM];
        $row['CURRENCY'] = $aPrice[$module::FIELD_CURRENCY];
      }

      $block[] = $row;
    }

    return $block;
  }

  /**
   * @param $moduleName
   * @param $paymentMethodId
   *
   * @return string
   */
  public static function getCurrencyFromMethod($moduleName, $paymentMethodId) {
    $result = '';

    /**
     * @var sn_module_payment[] $modules
     */
    $modules = self::getActiveMethods()->getModulesOnMethod($paymentMethodId);
    if ($moduleName
      && $paymentMethodId
      && is_object($modules[$moduleName])
      && $modules[$moduleName] instanceof sn_module_payment
    ) {
      $result = $modules[$moduleName]->getMethodCurrency($paymentMethodId);
    }

    return $result;
  }


  /**
   * @param $paymentMethodId
   *
   * @return string
   *               TODO REDO
   */
  public static function getDefaultCurrency($paymentMethodId) {
    $result = '';

    foreach (self::$payment_methods as $paymentTypeId => $methodList) {
      foreach ($methodList as $methodId => $details) {
        if ($methodId == $paymentMethodId) {
          $result = !empty($details[self::P_CURRENCY]) ? $details[self::P_CURRENCY] : '';
        }
      }
    }

    return $result;
  }


  public static function getActiveMethods() {
    if (empty(self::$methodsActive)) {
      self::$methodsActive = new PaymentsMethodsActive();
    }

    return self::$methodsActive;
  }

}
