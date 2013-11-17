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
* @system [Uzbek]
* @version 38a3.1
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  // Metamatter
  'sys_metamatter_what_header' => 'MM (Metamatter) nima degani?',
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

  'pay_mm_buy' => 'MM sotib olish',
  'pay_mm_buy_text_cost' => 'Narhi',
  'pay_mm_buy_text_unit' => 'tuzmoq',
  'pay_mm_buy_url_description' => 'MM ni Webmoney orqali olishingiz ham mumkin.',
  'pay_mm_buy_url_get'  => 'Batafsilroq ma`lumot olish uchun ushbu manzilga o`ting.',
  'pay_mm_buy_url_none' => 'Свяжитесь с Администрацией сервера по вопросам получения Метаматерии',


  'pay_mm_bonus_header' => 'Стоимость Метаматерии и бонусы за оптовую покупку',
  'pay_mm_bonus' => 'MM ni ko`p miqdorda sotib olganlarga bonus taqdim etiladi:',
  'pay_mm_bonus_each' => 'boshlang\'ich %s MM - bonus %d%% miqdorda qoshib beriladi',


  'pay_mm_buy_step1_text' => 'MM miqdorini, pul to`lash yo`lini tanlang va tasdiqlang',
  'pay_mm_buy_unit' => 'единиц Метаматерии',
  'pay_mm_buy_select' => 'Pul to`lash yo`li',
  'pay_mm_buy_confirm' => 'Tasdiqlang',
  'pay_mm_buy_payment_selected' => 'Pul tushirish uchun kerakli karmonlardan foydalaning',

  'pay_mm_buy_step2_text' => 'Siz uchun kerakli bo`lgan MM miqdorini ko`rsating va to`lash usulini tanlang. Agar hammasi to`g`ri bolsa "MM uchun tolov" tugmasini bosing. Agar adashgan bo`lsangiz "Yangitdan boshlash" tugmasini bosing',
  'pay_mm_buy_pay' => 'MM to`lash',
  'pay_mm_buy_reset' => 'Yangitdan boshlash',
  'pay_mm_buy_in_progress' => 'To`lov bajarilmoqda...',
  'pay_mm_buy_conversion_cost' => 'Стоимость %s Метаматерии составит %s %s',

  'pay_currency_name' => 'Валюта',
  'pay_currency_symbol' => 'Символ',
  'pay_currency_choose' => 'Выберите валюту платежа',
  'pay_currency_list' => array(
    'RUB' => 'Российский рубль',
    'USD' => 'Доллар США',
    'EUR' => 'Евро',
    'UAH' => 'Украинская гривна',
    'WMR' => 'WebMoney рубль',
    'WMZ' => 'WebMoney доллар',
    'WME' => 'WebMoney евро',
    'WMU' => 'WebMoney гривна',
  ),

  'pay_currency_exchange_title' => 'Внутренние курсы валют',
  'pay_currency_exchange_direct' => 'Прямой курс',
  'pay_currency_exchange_reverse' => 'Обратный курс',
  'pay_currency_exchange_mm' => 'ММ за 1 у.е.',
  'pay_currency_exchange_note' => 'Внутренний курс используется для пересчета из основной валюты сервера в валюту плтаженой системы. Курс не включает комиссию посредников и/или платежных систем',

  'pay_msg_mm_purchase_complete'   => 'Вы успешно заплатили за %d единиц Метаматерии через сервис %s. Вам начислено %s единиц Метаматерии',
  'pay_msg_mm_purchase_incomplete' => 'Ваш платеж за %d единиц Метаматерии через сервис %s не завершен. Если вы считаете, что произошла ошибка - свяжитесь с Администрацией сервера',
  'pay_msg_mm_purchase_test'       => 'На самом деле - шутка. Платеж был тестовый, поэтому ты ничего не получил ха-ха-ха! Если считаешь, что это ошибка - обратись к Администрации сервера',

  'pay_msg_request_user_found' => 'Foydalanuvchi topildi',

  'pay_msg_request_unsupported' => 'So`rovning bunday turi qo`llab quvvatlanmaydi',
  'pay_msg_request_signature_invalid' => 'So`rovning imzosi noto`g`ri',
  'pay_msg_request_user_invalid' => 'Foydalanuvchining identifikatori noto`g`ri',
  'pay_msg_request_server_wrong' => 'Server noto`g`ri',
  'pay_msg_request_payment_amount_invalid' => 'To`lov summasi noto`g`ri',
  'pay_msg_request_payment_id_invalid' => 'To`lov identifikatori noto`g`ri',
  'pay_msg_request_payment_date_invalid' => 'to`lovning kuni noto`g`ri',
  'pay_msg_request_internal_error' => 'Serverning ichida hatolik. To`lovni keyinroq amalga oshirib koring',
  'pay_msg_request_paylink_unsupported' => 'Bunday turdagi to`lov sahifasi qo`llanmaydi. SN ning eskirgan versiyasini qo`llayotgan bo`lishingiz mumkin',
  'pay_msg_request_payment_write_error' => 'Ошибка записи платежа',

  'pay_msg_module_disabled' => 'To`lov moduli o`chirilgan',

  'pay_msg_mm_request_amount_invalid' => 'MM soni noto`g`ri',
  'pay_msg_mm_request_config_invalid' => 'Ошибка в конфигурации модуля платежа. Свяжитесь с Администрацией сервера',
  'pay_msg_mm_request_mm_adjust_error' => 'Ошибка начисления Метаматерии',

));
