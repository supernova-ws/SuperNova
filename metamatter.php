<?php

// Придумать какой статус должен быть у глобальных ответов, что бы не перекрывать статусы платежных систем
// Может добавить спецстатус "Ответ системы платежа" и парсить дальше getMessage
// см constants.php

use Payment\PaymentMethods;

global $debug;
global $template_result;
global $config;

/** @noinspection PhpIncludeInspection */
require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

if (!SN::$gc->modules->countModulesInGroup('payment')) {
  sys_redirect('dark_matter.php');
  die();
}

lng_include('payment');
lng_include('infos');

$template = SnTemplate::gettemplate('metamatter', true);

// $player_currency_default = player_load_option($user, PLAYER_OPTION_CURRENCY_DEFAULT);
$player_currency_default = SN::$user_options[PLAYER_OPTION_CURRENCY_DEFAULT];
$player_currency         = sys_get_param_str('player_currency', $player_currency_default);
empty(SN::$lang['pay_currency_list'][$player_currency]) ? ($player_currency = $player_currency_default ? $player_currency_default : SN::$config->payment_currency_default) : false;
// $player_currency_default != $player_currency ? player_save_option($user, PLAYER_OPTION_CURRENCY_DEFAULT, $player_currency) : false;
$player_currency_default != $player_currency ? SN::$user_options[PLAYER_OPTION_CURRENCY_DEFAULT] = $player_currency : false;

// Таблица скидок
$prev_discount = 0;
if (isset(sn_module_payment::$bonus_table) && is_array(sn_module_payment::$bonus_table)) {
  foreach (sn_module_payment::$bonus_table as $sum => $discount) {
    if ($discount && $discount != $prev_discount) {
      $template->assign_block_vars('discount', array(
        'SUM'          => $sum,
        'DISCOUNT'     => $discount * 100,
        'DISCOUNT_ONE' => 1 + $discount,
        'TEXT'         => sprintf(SN::$lang['pay_mm_bonus_each'], HelperString::numberFloorAndFormat($sum), round($discount * 100)),
      ));
      $prev_discount = $discount;
    }
  }
}

// Результат платежа
if (
  ($payment_id = sys_get_param_id('payment_id'))
  ||
  ($payment_id = sys_get_param_id('ik_pm_no'))
) {
  /** @noinspection PhpDeprecationInspection */
  $payment = doquery("SELECT * FROM {{payment}} WHERE `payment_id` = {$payment_id} LIMIT 1;", true);
  if ($payment && $payment['payment_user_id'] == $user['id']) {
    if ($payment['payment_status'] == PAYMENT_STATUS_COMPLETE) {
      $template->assign_block_vars('result', array('MESSAGE' => sprintf(SN::$lang['pay_msg_mm_purchase_complete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name'], $payment['payment_dark_matter_gained'])));
    }
    if ($payment['payment_status'] == PAYMENT_STATUS_NONE) {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf(SN::$lang['pay_msg_mm_purchase_incomplete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name']),
        'STATUS'  => 1,
      ));
    }
    if ($payment['payment_test']) {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf(SN::$lang['pay_msg_mm_purchase_test']),
        'STATUS'  => -1,
      ));
    }
  }
}

$unit_available_amount_list = &sn_module_payment::$bonus_table;

$request = array(
  'metamatter' => sys_get_param_float('metamatter'),
);

if (!$request['metamatter']) {
  unset($_POST);
}

$payment_module_request  = sys_get_param_str('payment_module');
//$payment_type_selected   = sys_get_param_int('payment_type');
$payment_method_selected = sys_get_param_int('payment_method');

//pdump($payment_module_request, '$payment_module_request');
//pdump($payment_type_selected, '$payment_type_selected');
//pdump($payment_method_selected, '$payment_method_selected');

list($payment_module_request, $payment_method_selected) = PaymentMethods::getActiveMethods()->processInputParams($payment_module_request, $payment_method_selected);

//pdump($payment_module_request, '$payment_module_request');
//pdump($payment_type_selected, '$payment_type_selected');
//pdump($payment_method_selected, '$payment_method_selected');

//die();

if (!$payment_module_request && $payment_method_selected) {
  $template_result['.']['payment_module'] = PaymentMethods::renderModulesForMethod($payment_method_selected, $player_currency, $request);
} elseif (!$payment_module_request || !$payment_method_selected) {
  $template_result['.']['payment'] = PaymentMethods::renderPaymentMethodList();
}

foreach (SN::$lang['pay_currency_list'] as $key => $value) {
  $course = get_exchange_rate($key);
  if (!$course) {
    continue;
  }
  $template->assign_block_vars('exchange', array(
    'SYMBOL'          => $key,
    'TEXT'            => $value,
    'COURSE_DIRECT'   => HelperString::numberFormat($course, 4),
    'COURSE_REVERSE'  => HelperString::numberFormat(1 / $course, 4),
    'MM_PER_CURRENCY' => HelperString::numberFormat(sn_module_payment::currency_convert(1, $key, 'MM_'), 2),
    'LOT_PRICE'       => sn_module_payment::currency_convert(get_mm_cost(), 'MM_', $key),
    'DEFAULT'         => $key == SN::$config->payment_currency_default,
  ));
}

if ($request['metamatter'] && $payment_module_request && $payment_method_selected) {
  try {
    $paymentModuleReal = SN::$gc->modules->getModule($payment_module_request);
    if (!is_object($paymentModuleReal)) {
      throw new Exception('{ Менеджер модулей вернул null вместо платёжного модуля для }' . $payment_module_request, ERR_ERROR);
    }

    /**
     * @var sn_module_payment $paymentModuleReal
     */
    // Any possible errors about generating paylink should be raised in module!
    $pay_link = $paymentModuleReal->compile_request($request, $payment_method_selected);

    // Поддержка дополнительной информации
    if (is_array($pay_link['RENDER'])) {
      foreach ($pay_link['RENDER'] as $html_data) {
        $template->assign_block_vars('render', $html_data);
        if (isset($html_data['VALUE']) && is_array($html_data['VALUE'])) {
          foreach ($html_data['VALUE'] as $value_id => $value_value) {
            $template->assign_block_vars('render.value', array(
              'FIELD' => $value_id,
              'VALUE' => $value_value,
            ));
          }
        }
      }
    }

    // Поддержка передачи данных для многошаговых платежных систем
    if (is_array($pay_link['DATA'])) {
      foreach ($pay_link['DATA'] as $key => $value) {
        $template->assign_block_vars('pay_link_data', array(
          'FIELD' => $key,
          'VALUE' => $value,
        ));
      }
    }

    if (is_array($pay_link) && in_array($pay_link['PAY_LINK_METHOD'], array('POST', 'GET', 'LINK', 'STEP', 'REDIRECT'))) {
      if($pay_link['PAY_LINK_METHOD'] == 'REDIRECT') {
        sys_redirect($pay_link['PAY_LINK_URL']);
      }

      // TODO Переделать это под assign_vars_recursive и возвращать пустые строки если нет платежного метода - для унификации формы в темплейте
      $template->assign_vars(array(
        'PAY_LINK_METHOD' => $pay_link['PAY_LINK_METHOD'],
        'PAY_LINK_URL'    => $pay_link['PAY_LINK_URL'],
      ));
    } else {
      throw new exception(SN::$lang['pay_msg_request_paylink_unsupported'], ERR_ERROR);
    }
  } catch (exception $e) {
    $template->assign_block_vars('result', $response = array(
      'STATUS'  => $e->getCode(),
      'MESSAGE' => $e->getMessage(),
    ));
    $debug->warning('Результат операции: код ' . $e->getCode() . ' сообщение "' . $e->getMessage() . '"', 'Ошибка платежа', LOG_INFO_PAYMENT);
  }
}

// Прегенерированные пакеты
foreach ($unit_available_amount_list as $unit_amount => $discount) {
  $temp = sn_module_payment::currency_convert($unit_amount, 'MM_', $player_currency);
  $template->assign_block_vars('mm_amount', array(
    'VALUE'            => $unit_amount,
    // 'PRICE' => $temp,
    'PRICE_TEXT'       => HelperString::numberFormat($temp, 2),
    'CURRENCY'         => $player_currency,
    'DISCOUNT'         => $discount,
    'DISCOUNT_PERCENT' => $discount * 100,
    'DISCOUNTED'       => $unit_amount * (1 + $discount),
    'TEXT'             => HelperString::numberFloorAndFormat($unit_amount),
    'TEXT_DISCOUNTED'  => HelperString::numberFloorAndFormat($unit_amount * (1 + $discount)),
  ));
}

$currency               = PaymentMethods::getCurrencyFromMethod($payment_module_request, $payment_method_selected);
$bonus_percent          = round(sn_module_payment::bonus_calculate($request['metamatter'], true, true) * 100);
$income_metamatter_text = prettyNumberStyledDefault(sn_module_payment::bonus_calculate($request['metamatter']));

$approxCost = '';
if (!empty($payment_module_request) && !empty($payment_method_selected)) {
  $mod = SN::$gc->modules->getModule($payment_module_request);

  /**
   * @var sn_module_payment $mod
   */
  $tPrice = $mod->getPrice($payment_method_selected, $player_currency, $request['metamatter']);
  if (!empty($tPrice) && is_array($tPrice)) {
    $approxCost = sprintf(
      SN::$lang['pay_mm_buy_approximate_cost'],
      HelperString::numberFormat($tPrice[$mod::FIELD_SUM], 2),
      $tPrice[$mod::FIELD_CURRENCY]
    );
  }
}


$template->assign_vars([
  'PAGE_HEADER' => SN::$lang['sys_metamatter'],

  'URL_PURCHASE' => SN::$config->url_purchase_metamatter,

  'PAYMENT_METHOD'      => $payment_method_selected,
  'PAYMENT_METHOD_NAME' => SN::$lang['pay_methods'][$payment_method_selected],

  'PAYMENT_MODULE'             => $payment_module_request,
  'PAYMENT_MODULE_NAME'        => SN::$lang["module_{$payment_module_request}_name"],
  'PAYMENT_MODULE_DESCRIPTION' => SN::$lang["module_{$payment_module_request}_description"],

  'PLAYER_CURRENCY'              => $player_currency,
  'PLAYER_CURRENCY_PRICE_PER_MM' => sn_module_payment::currency_convert(1, $player_currency, 'MM_', 10),

  'UNIT_AMOUNT'                 => (float)$request['metamatter'],
  'UNIT_AMOUNT_TEXT'            => HelperString::numberFloorAndFormat($request['metamatter']),
  'UNIT_AMOUNT_BONUS_PERCENT'   => $bonus_percent,
  'UNIT_AMOUNT_TEXT_DISCOUNTED' => $income_metamatter_text,
  'UNIT_AMOUNT_TEXT_COST_BASE'  => HelperString::numberFormat(sn_module_payment::currency_convert($request['metamatter'], 'MM_', $player_currency), 2),

  'PAYMENT_CURRENCY_EXCHANGE_DEFAULT' => prettyNumberStyledDefault(get_mm_cost()),
  'PAYMENT_CURRENCY_DEFAULT_TEXT'     => SN::$lang['pay_currency_list'][SN::$config->payment_currency_default],

  'METAMATTER' => mrc_get_level($user, '', RES_METAMATTER),

  'METAMATTER_COST_TEXT'       => sprintf(SN::$lang['pay_mm_buy_conversion_cost'],
    prettyNumberStyledDefault($request['metamatter']),
    number_format($mmWish = sn_module_payment::currency_convert($request['metamatter'], 'MM_', $currency), 2, ',', '.'),
    $currency,
    prettyNumberGetClass($mmWish, true)),
  'METAMATTER_COST_BONUS_TEXT' => $bonus_percent
    ? sprintf(SN::$lang['pay_mm_buy_real_income'], prettyNumberStyledDefault($bonus_percent), $income_metamatter_text)
    : '',

  'METAMATTER_COST_ON_PAYMENT' => $approxCost,

  'DARK_MATTER_DESCRIPTION' => SN::$lang['info'][RES_DARK_MATTER]['description'],

  'PAYMENT_AVAILABLE' => SN::$gc->modules->countModulesInGroup('payment') && !defined('SN_GOOGLE'),

]);

$template->assign_recursive($template_result);

SnTemplate::display($template, SN::$lang['sys_metamatter']);
