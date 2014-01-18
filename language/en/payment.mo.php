<?php

/*
#############################################################################
#  Filename: payment.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 38a8.0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

//$lang = array_merge($lang,
//$lang->merge(
$a_lang_array = (array(
  // Metamatter
  'sys_metamatter_what_header' => 'What is Metamatter',
  'sys_metamatter_what_description' => 'Метаматерия - это весьма условное название для особого состояния Вселенной. Фактически - это даже не материя, а факторизируемая вероятность.<br />
  У Метаматерии нет состояния - и в то же время она находится во всех состояних. Метаматерия нигде не находится - и в то же время находится везде. Потенциально метаматерия может стать чем угодно и где угодно - если правильно актуализировать вероятность.<br />
  Увы, свойства Метаматерии и методы её актуализации еще исследованы слабо. Поэтому на сегодня доступен весьма узкий спектр применения ММ:<ul>
  <li>Метаматерию можно сконвертировать в Тёмную Материю по курсу 1 ММ = 1 ТМ без дополнительных затрат</li>
  </ul>',

  'pay_mm_convert_header' => 'Конвертация Метаматерии в Тёмную Материю',
  'pay_mm_convert_text' => 'Метаматерия конвертируется в Тёмную Материю по курсу 1 к 1',
  'pay_mm_convert_no_mm' => 'Нет Метаматерии - купите её сначала',
  'pay_mm_convert_prefix' => 'Единицы Метаматерии',
  'pay_mm_convert_suffix' => '',
  'pay_mm_convert_do' => 'Сконвертировать в ТМ',

  'pay_msg_mm_convert_wrong_amount' => 'Неправильное количество Метаматерии',
  'pay_msg_mm_convert_not_enough' => 'Не хватает Метаматерии для конвертации в Тёмную Материю',
  'pay_msg_mm_convert_mm_error' => 'Ошибка изменения количеста Метаматерии',
  'pay_msg_mm_convert_dm_error' => 'Ошибка изменения количеста Тёмной Материи',

  'pay_mm_buy' => 'Purchase Metamatter',
  'pay_mm_buy_text_cost' => 'Price for',
  'pay_mm_buy_text_unit' => 'is',
  'pay_mm_buy_url_description' => 'In addition you can purchase for real money',
  'pay_mm_buy_url_get'  => 'Click here to read details',
  'pay_mm_buy_url_none' => 'Contact with server Administration to get Metamatter',


  'pay_mm_bonus_header' => 'Metamatter cost and bonuses for bulk purchases',
  'pay_mm_bonus' => 'When you purchasing large amounts of Metamatter you recieve bonuses:',
  'pay_mm_bonus_each' => 'from %s MM - %d%% bonus to purchased MM amount',


  'pay_mm_buy_step1_text' => 'Select amount of MM you wish to purchase, select payment system and confirm your selection',
  'pay_mm_buy_unit' => 'Metamatter units',
  'pay_mm_buy_select' => 'Payment system',
  'pay_mm_buy_confirm' => 'Confirm selection',
  'pay_mm_buy_payment_selected' => 'Purchase would be made using payment system',

  'pay_mm_buy_step2_text' => 'Verify selected amount of Metamatter and selected payment system. If everything is OK press button "Purchase Metamatter". If there is any error - press button "Discard and start again"',
  'pay_mm_buy_pay' => 'Purchase Metamatter',
  'pay_mm_buy_reset' => 'Discard and start again',
  'pay_mm_buy_in_progress' => 'Payment in progress...',
  'pay_mm_buy_conversion_cost' => 'Cost of %s Metamatter will be %s %s',

  'pay_currency_name' => 'Currency',
  'pay_currency_symbol' => 'Symbol',
  'pay_currency_choose' => 'Choose payment currency',
  'pay_currency_list' => array(
    'RUB' => 'Russian ruble',
    'USD' => 'Dollar USA',
    'EUR' => 'Euro',
    'UAH' => 'Ukrainian hryvna',
    'WMR' => 'WebMoney rouble',
    'WMZ' => 'WebMoney dollar',
    'WME' => 'WebMoney euro',
    'WMU' => 'WebMoney hryvna',
  ),

  'pay_currency_exchange_title' => 'Internal currency exchange',
  'pay_currency_exchange_direct' => 'Exchange rate',
  'pay_currency_exchange_reverse' => 'Reverse rate',
  'pay_currency_exchange_mm' => 'MM for 1 currency',
  'pay_currency_exchange_note' => 'Internal exchange rates used to calculate payment amount in payment system currency. Exchange rates does not includes commission of payment system(s)',

  'pay_msg_mm_purchase_complete'   => 'You succesfully paid for %d Metamatter via %s. You gained %s Metamatter',
  'pay_msg_mm_purchase_incomplete' => 'You payment for %d Metamatter via %s currently in progress. If you feel it wrong please contact Administration',
  'pay_msg_mm_purchase_test'       => 'Really you did not gain anything! Because it was a test payment ha-ha-ha! If you feel it wrong - contact Administration',

  'pay_msg_request_user_found' => 'User found',

  'pay_msg_request_unsupported' => 'Unsupported request',
  'pay_msg_request_signature_invalid' => 'Wrong request signature',
  'pay_msg_request_user_invalid' => 'User ID is invalid',
  'pay_msg_request_server_wrong' => 'Wrong server',
  'pay_msg_request_payment_amount_invalid' => 'Wrong payment amount',
  'pay_msg_request_payment_id_invalid' => 'Wrong payment ID',
  'pay_msg_request_payment_date_invalid' => 'Wrong payment date',
  'pay_msg_request_internal_error' => 'Server internal error. Try again later',
  'pay_msg_request_paylink_unsupported' => 'This type of paylink is not supported. It\'s looks like you using outdated version of SuperNova which incompatible with selected payment module',
  'pay_msg_request_payment_write_error' => 'Payment write error',

  'pay_msg_module_disabled' => 'Payment module disabled',

  'pay_msg_mm_request_amount_invalid' => 'Wrong Metamatter amount',
  'pay_msg_mm_request_config_invalid' => 'There is error in payment module configuration. Please contact server AMMinistration',
  'pay_msg_mm_request_mm_adjust_error' => 'Error adjusting Metamatter',

));
