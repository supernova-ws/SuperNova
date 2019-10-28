<?php
/**
 * Created by Gorlum 22.08.2019 3:38
 */

namespace Payment;


use HelperArray;
use SN;
use sn_module_payment;

class PaymentsMethodsActive {
  const P_MODULES = 'modules';

  /**
   * List of installed payment modules
   *
   * @var sn_module_payment[] $modulesInstalled
   */
  protected $modulesInstalled = [];

  /**
   * Generated list of available payment methods and corresponding modules that supports this method
   *
   * @var sn_module_payment[][][] $methodsAvailableV2 [
   *                                  (int)$paymentMethodId => [
   *                                    self::P_MODULES => [(str)$moduleName => (sn_payment_module)$module, ...]
   *                                  ], ...
   *                                ]
   */
  protected $methodsAvailableV2 = [];

  public function __construct() {
    // А теперь из каждого модуля вытаскиваем методы, которые он поддерживает
    $paymentModuleList = SN::$gc->modules->getModulesInGroup('payment', true);
    foreach ($paymentModuleList as $module_name => $module) {
      /**
       * @var sn_module_payment $module
       */
      if (!is_object($module) || !$module instanceof sn_module_payment || !$module->isActive()) {
        continue;
      }

      lng_include($module_name, $module->getRootRelative());

      foreach (PaymentMethods::$payment_methods as $payment_type_id => $available_methods) {
        foreach ($available_methods as $payment_method => $payment_currency) {
          if ($module->isMethodSupported($payment_method)) {
            $this->registerModuleOnMethod($module, $payment_method);
          }
        }
      }
    }
  }

  /**
   * Register module to support payment method
   *
   * @param sn_module_payment $module
   * @param int               $paymentMethodId
   */
  public function registerModuleOnMethod($module, $paymentMethodId) {
    $module_name = $module->getFullName();

    $this->methodsAvailableV2[$paymentMethodId][self::P_MODULES][$module_name] = $module;

    $this->modulesInstalled[$module_name] = $module;
  }

  /**
   * @param $paymentMethodId
   *
   * @return sn_module_payment[]
   */
  public function getModulesOnMethod($paymentMethodId) {
    return
      is_array($this->methodsAvailableV2[$paymentMethodId][self::P_MODULES])
        ? $this->methodsAvailableV2[$paymentMethodId][self::P_MODULES]
        : [];
  }

  /**
   * Return count of registered modules for supplied payment method
   *
   * @param $paymentMethodId
   *
   * @return int
   */
  public function getModuleCount($paymentMethodId) {
    return count($this->getModulesOnMethod($paymentMethodId));
  }

  /**
   * @param $paymentMethodId
   *
   * @return mixed|null
   */
  public function getFirstModuleNameOnMethod($paymentMethodId) {
    return HelperArray::array_key_first($this->methodsAvailableV2[$paymentMethodId][self::P_MODULES]);
  }

  /**
   * @param $moduleName
   *
   * @return bool
   */
  public function isModuleInstalled($moduleName) {
    return in_array($moduleName, array_keys($this->modulesInstalled));
  }

  /**
   * @return int
   */
  public function getInstalledModuleCount() {
    return count($this->modulesInstalled);
  }


  /**
   * @return mixed
   */
  public function getFirstInstalledModuleName() {
    return HelperArray::array_key_first($this->modulesInstalled);
  }


  /**
   * Does module supports this payment method?
   *
   * @param string $moduleName
   * @param int    $paymentMethodId
   *
   * @return bool
   */
  public function isModuleSupportMethod($moduleName, $paymentMethodId) {
    return !empty($this->methodsAvailableV2[$paymentMethodId][self::P_MODULES][$moduleName]);
  }

  /**
   * Checks input params for validity
   *
   * @param string $moduleName
   * @param int    $paymentMethodId
   *
   * @return array
   */
  public function processInputParams($moduleName, $paymentMethodId) {
    $installedModulesCount = $this->getInstalledModuleCount();
    if (!$installedModulesCount) {
      // Если нет инсталлированных модулей - то как вообще мы тут оказались?
      $moduleName      = '';
      $paymentMethodId = 0;
    } elseif ($installedModulesCount == 1) {
      // Если у нас единственный модуль платежа - то только его можно и использовать
      $moduleName = $this->getFirstInstalledModuleName();

      // И если этот модуль не поддерживает выбранный метод платежа - значит метод платежа никуда не годится
      if (!$this->isModuleSupportMethod($moduleName, $paymentMethodId)) {
        $paymentMethodId = 0;
      }

      if(!$paymentMethodId) {
        $module = $this->modulesInstalled[$moduleName];
        $methodsOnModule = $module->getMethodList();
        if(count($methodsOnModule) == 1) {
          $paymentMethodId = HelperArray::array_key_first($methodsOnModule);
        }
      }
    } elseif (!$paymentMethodId) {
      // Если метод платежа не указан - то и модуль под него выбрать нельзя
      $moduleName = '';
    } else {
      // Если у нас всего модулей больше одного и метод платежа указан - то тут становится интереснее

      $modulesCount = $this->getModuleCount($paymentMethodId);
      if (!$modulesCount) {
        // Если метод не поддерживается ни одном модулем - значит левая фигня. Сбрасываем всё в ноль
        $moduleName      = '';
        $paymentMethodId = 0;
      } elseif ($modulesCount == 1) {
        // Если выбран метод платежа и у нас один-единственный модуль - то выбираем его в качестве активного
        $moduleName = $this->getFirstModuleNameOnMethod($paymentMethodId);
      } /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
        // А тут ничего делать и не надо - у нас выбран метод платежа и модуль, который его поддерживает
      }
    }

    return [$moduleName, $paymentMethodId];
  }

}
