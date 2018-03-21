<?php

global $debug;

// Придумать какой статус должен быть у глобальных ответов, что бы не перекрывать статусы платежных систем
// Может добавить спецстатус "Ответ системы платежа" и парсить дальше getMessage
// см constants.php

use Modules\sn_module;

include_once('common.' . substr(strrchr(__FILE__, '.'), 1));

if (!SN::$gc->modules->countModulesInGroup('payment')) {
  sys_redirect('dark_matter.php');
  die();
}

global $config;

lng_include('payment');
lng_include('infos');

$template = gettemplate('metamatter', true);

// $player_currency_default = player_load_option($user, PLAYER_OPTION_CURRENCY_DEFAULT);
$player_currency_default = SN::$user_options[PLAYER_OPTION_CURRENCY_DEFAULT];
$player_currency = sys_get_param_str('player_currency', $player_currency_default);
empty(SN::$lang['pay_currency_list'][$player_currency]) ? ($player_currency = $player_currency_default ? $player_currency_default : SN::$config->payment_currency_default) : false;
// $player_currency_default != $player_currency ? player_save_option($user, PLAYER_OPTION_CURRENCY_DEFAULT, $player_currency) : false;
$player_currency_default != $player_currency ? SN::$user_options[PLAYER_OPTION_CURRENCY_DEFAULT] = $player_currency : false;

//// Конвертация ММ в ТМ
//if(sys_get_param('mm_convert_do')) {
//  try {
//    if(!($mm_convert = sys_get_param_id('mm_convert'))) {
//      throw new exception($lang['pay_msg_mm_convert_wrong_amount'], ERR_ERROR);
//    }
//
//    sn_db_transaction_start();
//    $user = db_user_by_id($user['id'], true);
//    if($mm_convert > mrc_get_level($user, null, RES_METAMATTER)) {
//      throw new exception($lang['pay_msg_mm_convert_not_enough'], ERR_ERROR);
//    }
//
//    $payment_comment = sprintf("Игрок сконвертировал %d Метаматерии в Тёмную Материю", $mm_convert);
//    if(!mm_points_change($user['id'], RPG_CONVERT_MM, -$mm_convert, $payment_comment)) {
//      throw new exception($lang['pay_msg_mm_convert_mm_error'], ERR_ERROR);
//    }
//    if(!rpg_points_change($user['id'], RPG_CONVERT_MM, $mm_convert, $payment_comment)) {
//      throw new exception($lang['pay_msg_mm_convert_dm_error'], ERR_ERROR);
//    }
//
//    $template->assign_block_vars('result', array(
//      'STATUS'  => ERR_NONE,
//      'MESSAGE' => sprintf('Конвертация %1$s единиц Метаматерии в %1$s единиц Тёмной Материи успешно произведена', pretty_number($mm_convert)),
//    ));
//
//    sn_db_transaction_commit();
//  } catch(exception $e) {
//    sn_db_transaction_rollback();
//    $template->assign_block_vars('result', $response = array(
//      'STATUS'  => $e->getCode(),
//      'MESSAGE' => $e->getMessage(),
//    ));
//  }
//}

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
if ($payment_id = sys_get_param_id('payment_id')) {
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

$payment_methods_available = array_combine(array_keys(sn_module_payment::$payment_methods), array_fill(0, count(sn_module_payment::$payment_methods), null));
array_walk($payment_methods_available, function (&$value, $index) {
  $value = !empty(sn_module_payment::$payment_methods[$index]) ? array_combine(array_keys(sn_module_payment::$payment_methods[$index]), array_fill(0, count(sn_module_payment::$payment_methods[$index]), null)) : $value;
});

$payment_module_valid = false;
$payment_module_request = sys_get_param_str('payment_module');
foreach (SN::$gc->modules->getModulesInGroup('payment', true) as $module_name => $module) {
  /**
   * @var sn_module $module
   */

  if (!is_object($module) || !$module->isActive()) {
    continue;
  }

  lng_include($module_name, $module->getRootRelative());

  foreach (sn_module_payment::$payment_methods as $payment_type_id => $available_methods) {
    foreach ($available_methods as $payment_method => $payment_currency) {
      if (isset($module->manifest['payment_method'][$payment_method])) {
        $payment_methods_available[$payment_type_id][$payment_method][$module_name] = $module->manifest['payment_method'][$payment_method];
      }
    }
  }

  $payment_module_valid = $payment_module_valid || $module_name == $payment_module_request;
}

global $template_result;
// Доступные платежные методы
foreach ($payment_methods_available as $payment_type_id => $payment_methods) {
  if (empty($payment_methods)) {
    continue;
  }

  $template_result['.']['payment'][$payment_type_id] = array(
    'ID'   => $payment_type_id,
    'NAME' => SN::$lang['pay_methods'][$payment_type_id],
  );
  foreach ($payment_methods as $payment_method_id => $module_list) {
    if (empty($module_list)) {
      continue;
    }
    $template_result['.']['payment'][$payment_type_id]['.']['method'][$payment_method_id] = array(
      'ID'         => $payment_method_id,
      'NAME'       => SN::$lang['pay_methods'][$payment_method_id],
      'IMAGE'      => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['image'])
        ? sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['image'] : '',
      'NAME_FORCE' => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['name']),
      'BUTTON'     => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['button']),
    );
    foreach ($module_list as $payment_module_name => $payment_module_method_details) {
      $template_result['.']['payment'][$payment_type_id]['.']['method'][$payment_method_id]['.']['module'][] = array(
        'MODULE' => $payment_module_name,
      );
    }
  }

  if (empty($template_result['.']['payment'][$payment_type_id]['.'])) {
    unset($template_result['.']['payment'][$payment_type_id]);
  }
}

$template->assign_recursive($template_result);

$payment_type_selected = sys_get_param_int('payment_type');
$payment_method_selected = sys_get_param_int('payment_method');

$payment_module_valid = $payment_module_valid && (!$payment_method_selected || isset($payment_methods_available[$payment_type_selected][$payment_method_selected][$module_name]));

// If payment_module invalid - making it empty OR if there is only one payment_module - selecting it
if ($payment_module_valid) {
  // $payment_module = $payment_module; // Really - do nothing
} elseif ($payment_type_selected && count($payment_methods_available[$payment_type_selected][$payment_method_selected]) == 1) {
  reset($payment_methods_available[$payment_type_selected][$payment_method_selected]);
  $payment_module_request = key($payment_methods_available[$payment_type_selected][$payment_method_selected]);
} elseif (SN::$gc->modules->countModulesInGroup('payment') == 1) {
  $payment_module_request = $module_name;
} else {
  $payment_module_request = '';
}

if ($payment_type_selected && $payment_method_selected) {
  foreach ($payment_methods_available[$payment_type_selected][$payment_method_selected] as $module_name => $temp) {
    $template->assign_block_vars('payment_module', array(
      'ID'          => $module_name,
      'NAME'        => SN::$lang["module_{$module_name}_name"],
      'DESCRIPTION' => SN::$lang["module_{$module_name}_description"],
    ));
  }
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

if ($request['metamatter'] && $payment_module_request) {
  try {
    $paymentModuleReal = SN::$gc->modules->getModule($payment_module_request);
    if(!is_object($paymentModuleReal)) {
      throw new Exception('{ Менеджер модулей вернул null вместо платёжного модуля для }' . $payment_module_request, ERR_ERROR);
    }
    /**
     * @var sn_module_payment $paymentModuleReal
     */

    // Any possible errors about generating paylink should be raised in module!
    $pay_link = $paymentModuleReal->compile_request($request);

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

    if (is_array($pay_link) && in_array($pay_link['PAY_LINK_METHOD'], array('POST', 'GET', 'LINK', 'STEP'))) {
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

$currency = $payment_module_request ? sn_module_payment::$payment_methods[$payment_type_selected][$payment_method_selected]['currency'] : '';
$bonus_percent = round(sn_module_payment::bonus_calculate($request['metamatter'], true, true) * 100);
$income_metamatter_text = prettyNumberStyledDefault(sn_module_payment::bonus_calculate($request['metamatter']));

$template->assign_vars(array(
  'PAGE_HEADER' => SN::$lang['sys_metamatter'],

  'URL_PURCHASE' => SN::$config->url_purchase_metamatter,

  'PAYMENT_TYPE'        => $payment_type_selected,
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

  'DARK_MATTER_DESCRIPTION' => SN::$lang['info'][RES_DARK_MATTER]['description'],

  'PAYMENT_AVAILABLE' => SN::$gc->modules->countModulesInGroup('payment') && !defined('SN_GOOGLE'),

));

display($template, SN::$lang['sys_metamatter']);
