<?php

// Придумать какой статус должен быть у глобальных ответов, что бы не перекрывать статусы платежных систем
// Может добавить спецстатус "Ответ системы платежа" и парсить дальше getMessage
// см constants.php

include_once('common.' . substr(strrchr(__FILE__, '.'), 1));

if(!sn_module_get_active_count('payment'))
{
  sys_redirect('overview.php');
  die();
}

lng_include('payment');

$template = gettemplate('metamatter', true);

if(sys_get_param('mm_convert_do')) {
  try {
    if(!($mm_convert = sys_get_param_id('mm_convert'))) {
      throw new exception($lang['pay_msg_mm_convert_wrong_amount'], ERR_ERROR);
    }

    sn_db_transaction_start();
    $user = db_user_by_id($user['id'], true);
    if($mm_convert > mrc_get_level($user, '', RES_METAMATTER)) {
      throw new exception($lang['pay_msg_mm_convert_not_enough'], ERR_ERROR);
    }

    $payment_comment = sprintf("Игрок сконвертировал %d Метаматерии в Тёмную Материю", $mm_convert);

    if(!mm_points_change($user['id'], RPG_CONVERT_MM, -$mm_convert, $payment_comment)) {
      throw new exception($lang['pay_msg_mm_convert_mm_error'], ERR_ERROR);
    }
    if(!rpg_points_change($user['id'], RPG_CONVERT_MM, $mm_convert, $payment_comment)) {
      throw new exception($lang['pay_msg_mm_convert_dm_error'], ERR_ERROR);
    }

    $template->assign_block_vars('result', array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => sprintf('Конвертация %1$s единиц Метаматерии в %1$s единиц Тёмной Материи успешно произведена', pretty_number($mm_convert)),
    ));

    sn_db_transaction_commit();
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $template->assign_block_vars('result', $response = array(
      'STATUS'  => $e->getCode(),
      'MESSAGE' => $e->getMessage(),
    ));
  }
}

$prev_discount = 0;
if(isset(sn_module_payment::$bonus_table) && is_array(sn_module_payment::$bonus_table)) {
  foreach(sn_module_payment::$bonus_table as $sum => $discount) {
    if($discount && $discount != $prev_discount) {
      $template->assign_block_vars('discount', array(
        'SUM' => $sum,
        'DISCOUNT' => $discount * 100,
        'DISCOUNT_ONE' => 1 + $discount,
        'TEXT' => sprintf($lang['pay_mm_bonus_each'], pretty_number($sum), round($discount * 100)),
      ));
      $prev_discount = $discount;
    }
  }
}

if($payment_id = sys_get_param_id('payment_id')) {
  $payment = doquery("SELECT * FROM {{payment}} WHERE `payment_id` = {$payment_id} LIMIT 1;", true);
  if($payment && $payment['payment_user_id'] == $user['id']) {
    if($payment['payment_status'] == PAYMENT_STATUS_COMPLETE || $payment['payment_status'] == PAYMENT_STATUS_TEST)
    {
      $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['pay_msg_mm_purchase_complete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name'], $payment['payment_dark_matter_gained'])));
    }
    if($payment['payment_status'] == PAYMENT_STATUS_NONE)
    {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf($lang['pay_msg_mm_purchase_incomplete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name']),
        'STATUS' => 1,
      ));
    }
    if($payment['payment_status'] == PAYMENT_STATUS_TEST)
    {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf($lang['pay_msg_mm_purchase_test']),
        'STATUS' => -1,
      ));
    }
  }
}

$unit_available_amount_list = &sn_module_payment::$bonus_table;

$request = array(
  'metamatter' => sys_get_param_float('metamatter'),
);
// $request['metamatter'] = isset($unit_available_amount_list[$request['metamatter']]) ? $request['metamatter'] : 0;
if(!$request['metamatter']) {
  unset($_POST);
}

$payment_methods_available = array_combine(array_keys(sn_module_payment::$payment_methods), array_fill(0, count(sn_module_payment::$payment_methods), null));
$payment_module_valid = false;
$payment_module = sys_get_param_str('payment_module');
foreach($sn_module_list['payment'] as $module_name => $module) {
  if(!is_object($module) || !$module->manifest['active']) {
    continue;
  }

  lng_include($module_name, $module->manifest['root_relative']);

  foreach(sn_module_payment::$payment_methods as $payment_type_id => $available_methods) {
    foreach($available_methods as $payment_method => $payment_currency) {
      if(isset($module->manifest['payment_method'][$payment_method])) {
        $payment_methods_available[$payment_type_id][$payment_method][$module_name] = $module->manifest['payment_method'][$payment_method];
      }
    }
  }

  $template->assign_block_vars('payment_module', array(
    'ID' => $module_name,
    'NAME' => $lang["module_{$module_name}_name"],
    'DESCRIPTION' => $lang["module_{$module_name}_description"],
  ));

  $payment_module_valid = $payment_module_valid || $module_name == $payment_module;
}

foreach($payment_methods_available as $payment_type_id => $payment_methods) {
  if(empty($payment_methods)) continue;

  $template->assign_block_vars('payment', array(
    'ID' => $payment_type_id,
    'NAME' => $lang['pay_methods'][$payment_type_id],
  ));
  foreach($payment_methods as $payment_method_id => $module_list) {
    $template->assign_block_vars('payment.method', array(
      'ID' => $payment_method_id,
      'NAME' => $lang['pay_methods'][$payment_method_id],
      'IMAGE' => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['image'])
                  ? sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['image'] : '',
      'NAME_FORCE' => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['name']),
      'BUTTON' => isset(sn_module_payment::$payment_methods[$payment_type_id][$payment_method_id]['button']),
    ));
    foreach($module_list as $payment_module_name => $payment_module_method_details) {
      $template->assign_block_vars('payment.method.module', array(
        'MODULE' => $payment_module_name,
      ));
    }
  }
}

// pdump($template);die();

$payment_type_selected = sys_get_param_int('payment_type');
$payment_method_selected = sys_get_param_int('payment_method');

$payment_module_valid = $payment_module_valid && (!$payment_method_selected || isset($payment_methods_available[$payment_type_selected][$payment_method_selected][$module_name]));

// If payment_module invalid - making it empty OR if there is only one payment_module - selecting it
if($payment_module_valid) {
  // $payment_module = $payment_module; // Really - do nothing
} elseif($payment_type_selected && count($payment_methods_available[$payment_type_selected][$payment_method_selected]) == 1) {
  reset($payment_methods_available[$payment_type_selected][$payment_method_selected]);
  $payment_module = key($payment_methods_available[$payment_type_selected][$payment_method_selected]);
} elseif(count($sn_module_list['payment']) == 1) {
  $payment_module = $module_name;
} else {
  $payment_module = '';
}

foreach($lang['pay_currency_list'] as $key => $value) {
  $var_name = 'payment_currency_exchange_' . strtolower($key);
  $course = $config->$var_name;
  if(!$course || $key == $config->payment_currency_default) {
    continue;
  }
  $template->assign_block_vars('exchange', array(
    'SYMBOL' => $key,
    'TEXT' => $value,
    'COURSE_DIRECT' => pretty_number($course, 4),
    'COURSE_REVERSE' => pretty_number(1 / $course, 4),
    'MM_PER_CURRENCY' => pretty_number(sn_module_payment::currency_convert(1, $key, 'MM_')),
    // 'UNIT_PER_LOT' => sn_module_payment::currency_convert(2500, 'MM_', $key),
  ));
}

if($request['metamatter'] && $payment_module) {
  try {
    // Any possible errors about generating paylink should be raised in module!
    $pay_link = $sn_module[$payment_module]->compile_request($request);

    // Поддержка дополнительной информации
    if(is_array($pay_link['RENDER'])) {
      foreach($pay_link['RENDER'] as $html_data) {
        $template->assign_block_vars('render', $html_data);
        if(isset($html_data['VALUE']) && is_array($html_data['VALUE'])) {
          foreach($html_data['VALUE'] as $value_id => $value_value) {
            $template->assign_block_vars('render.value', array(
              'FIELD' => $value_id,
              'VALUE' => $value_value,
            ));
          }
        }
      }
    }

    // Поддержка передачи данных для многошаговых платежных систем
    if(is_array($pay_link['DATA'])) {
      foreach($pay_link['DATA'] as $key => $value) {
        $template->assign_block_vars('pay_link_data', array(
          'FIELD' => $key,
          'VALUE' => $value,
        ));
      }
    }

    /* // TODO PRESUMABLY OUTDATED
    if(is_string($pay_link) && strpos($pay_link, 'http') === 0)
    {
      $template->assign_vars(array(
        'PAY_LINK' => $pay_link,
        'PAY_LINK_METHOD' => 'LINK',
      ));
    }
    else
    */

    if(is_array($pay_link) && in_array($pay_link['PAY_LINK_METHOD'], array('POST', 'GET', 'LINK', 'STEP'))) {
      // TODO Переделать это под assign_vars_recursive и возвращать пустые строки если нет платежного метода - для унификации формы в темплейте
      $template->assign_vars(array(
        'PAY_LINK_METHOD' => $pay_link['PAY_LINK_METHOD'],
        'PAY_LINK_URL' => $pay_link['PAY_LINK_URL'],
      ));
    } else {
      throw new exception($lang['pay_msg_request_paylink_unsupported'], ERR_ERROR);
    }
  } catch(exception $e) {
    $template->assign_block_vars('result', $response = array(
      'STATUS'  => $e->getCode(),
      'MESSAGE' => $e->getMessage(),
    ));
  }
}

foreach($unit_available_amount_list as $unit_amount => $discount) {
  $template->assign_block_vars('mm_amount', array(
    'VALUE' => $unit_amount,
    'DISCOUNT' => $discount,
    'DISCOUNT_PERCENT' => $discount * 100,
    'DISCOUNTED' => $unit_amount * (1 + $discount),
    'TEXT' => pretty_number($unit_amount),
    'TEXT_DISCOUNTED' => pretty_number($unit_amount * (1 + $discount)),
  ));
}

$currency = $payment_module ? sn_module_payment::$payment_methods[$payment_type_selected][$payment_method_selected]['currency'] : '';
$bonus_percent = round(sn_module_payment::bonus_calculate($request['metamatter'], true, true) * 100);
$income_metamatter_text = pretty_number(sn_module_payment::bonus_calculate($request['metamatter']), true, true);

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['sys_metamatter'],

  'URL_PURCHASE' => $config->url_purchase_metamatter,

  'PAYMENT_TYPE' => $payment_type_selected,
  'PAYMENT_METHOD' => $payment_method_selected,
  'PAYMENT_METHOD_NAME' => $lang['pay_methods'][$payment_method_selected],

  'PAYMENT_MODULE' => $payment_module,
  'PAYMENT_MODULE_NAME' => $lang["module_{$payment_module}_name"],
  'PAYMENT_MODULE_DESCRIPTION' => $lang["module_{$payment_module}_description"],

  'UNIT_AMOUNT' => (float)$request['metamatter'],
  'UNIT_AMOUNT_TEXT' => pretty_number($request['metamatter']),
  'UNIT_AMOUNT_BONUS_PERCENT' => $bonus_percent,
  'UNIT_AMOUNT_TEXT_DISCOUNTED' => $income_metamatter_text,
  'UNIT_AMOUNT_TEXT_COST_BASE' => sprintf($lang['pay_mm_buy_cost_base'],
    //pretty_number($request['metamatter'], true, true),
    pretty_number(sn_module_payment::currency_convert($request['metamatter'], 'MM_', $config->payment_currency_default), 2, true),
    $config->payment_currency_default),

  'PAYMENT_CURRENCY_EXCHANGE_DEFAULT' => pretty_number($config->payment_currency_exchange_mm_, true, true),
  'PAYMENT_CURRENCY_DEFAULT_TEXT' => $lang['pay_currency_list'][$config->payment_currency_default],

  'METAMATTER' => mrc_get_level($user, '', RES_METAMATTER),

  'METAMATTER_COST_TEXT' => sprintf($lang['pay_mm_buy_conversion_cost'],
    pretty_number($request['metamatter'], true, true),
    pretty_number(sn_module_payment::currency_convert($request['metamatter'], 'MM_', $currency), 2, true),
    $currency),
  'METAMATTER_COST_BONUS_TEXT' => $bonus_percent
    ? sprintf($lang['pay_mm_buy_real_income'], pretty_number($bonus_percent, true, true), $income_metamatter_text)
    : '',
));

display($template, $lang['sys_metamatter']);
