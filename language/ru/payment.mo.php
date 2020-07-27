<?php

/*
#############################################################################
#  Filename: payment.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  // Metamatter
  'sys_metamatter_what_header' => 'Что такое <span class="metamatter">Метаматерия</span>',
  'sys_metamatter_what_description' => '<span class="metamatter">Метаматерия</span> (сокращенно <span class="metamatter">ММ</span>) - это весьма условное название для особого состояния Вселенной. Фактически - это даже не материя, а факторизируемая вероятность.<br /><br />
  У <span class="metamatter">Метаматерии</span> нет состояния - и в то же время она находится во всех состояних. <span class="metamatter">Метаматерия</span> нигде не находится - и в то же время находится везде. Потенциально метаматерия может стать чем угодно и где угодно - если правильно актуализировать вероятность.',
  'sys_metamatter_what_purchase' => '<span class="metamatter">Метаматерию</span> можно рассматривать как "Покупную <span class="dark_matter">Тёмную Материю</span>" - при нехватке <span class="metamatter">ТМ</span> для приобретения чего-либо <span class="metamatter">ММ</span> будет <span class="ok">автоматически сконвертирована</span> в недостающее количество <span class="dark_matter">ТМ</span> по курсу <span class="dark_matter">1 ТМ</span> = <span class="metamatter">1 ММ</span>',

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

  'pay_mm_buy' => 'Приобрести <span class="metamatter">Метаматерию</span>',
  'pay_mm_buy_text_cost' => 'Стоимость',
  'pay_mm_buy_text_unit' => 'составляет',
  'pay_mm_buy_url_description' => '<span class="metamatter">ММ</span> можно приобрести только за реальные деньги',
  'pay_mm_buy_url_get'  => 'Откройте эту ссылку, что бы узнать подробности',
  'pay_mm_buy_url_none' => 'Свяжитесь с Администрацией сервера по вопросам получения <span class="metamatter">Метаматерии</span>',

  'pay_mm_bonus_header' => 'Стоимость <span class="metamatter">Метаматерии</span> и бонусы за оптовую покупку',
  'pay_mm_bonus' => 'При оптовой покупке <span class="metamatter">ММ</span> предоставляются бонусы',
  'pay_mm_bonus_each' => 'от %s <span class="metamatter">ММ</span> - бонус %d%% к количеству <span class="metamatter">ММ</span>',
  'pay_mm_bonus_text' => 'Бонус',

  'pay_mm_buy_step1_text' => 'Выберите количество <span class="metamatter">ММ</span>, способ оплаты и подтвердите свой выбор',
  'pay_mm_buy_metamatter_amount' => 'Выберите количество <span class="metamatter">Метаматерии</span> из списка',
  'pay_mm_buy_metamatter_amount_enter' => '...или введите другое количество <span class="metamatter">Метаматерии</span>',
  'pay_mm_buy_price_for' => 'Цена за',
  'pay_mm_buy_unit' => '<span class="metamatter">Метаматерии</span>',
  'pay_mm_buy_select' => 'Выберите платёжную систему',
  'pay_mm_buy_method_detail' => 'Некоторые способы оплаты предлагают выбор разных платёжных систем. Если платёж не проходит через одну платёжную систему - попробуйте использовать тот же способ оплаты с другой платёжной системой',
  'pay_mm_buy_confirm' => 'Подтвердить выбор',
  'pay_mm_buy_payment_selected' => 'Оплата будет произведена с использованием платёжной системы',
  'pay_mm_buy_purchase' => 'Покупка',

  'pay_mm_buy_payment_method_more' => 'Нажмите здесь, что бы увидеть больше способов оплаты',

  'pay_mm_buy_payment_method_select' => 'Выберите способ оплаты',
  'pay_mm_buy_payment_method_selected' => 'Вы выбрали способ оплаты',

  'pay_mm_buy_step2_text' => 'Рассчётная стоимость не включает дополнительные комиссии, которые могут взимать платёжные системы и/или разнообразные посредники. Проверьте выбранное количество <span class="metamatter">Метаматерии</span> и способ оплаты. Если все правильно - нажмите кнопку "Оплатить <span class="metamatter">Метаматерию</span>". Если вы ошиблись - нажмите кнопку "Начать заново"',
  'pay_mm_buy_pay' => 'Оплатить <span class="metamatter">Метаматерию</span>',
  'pay_mm_buy_reset' => 'Начать заново',
  'pay_mm_buy_in_progress' => 'Происходит оплата...',
  'pay_mm_buy_conversion_cost' => 'Рассчётная стоимость %1$s единиц <span class="metamatter">Метаматерии</span> в валюте платежной системы составит <span class="%4$s">%2$s</span> %3$s',
  'pay_mm_buy_cost_base' => 'Стоимость составит',
  'pay_mm_buy_real_income' => 'Бонус за оптовую покупку составит %s%% и на ваш игровой счёт будет зачислено %s <span class="metamatter">ММ</span>',
  'pay_mm_buy_approximate_cost' => 'Приблизительная стоимость ММ на платёжной системе составляет <span class="notice">%1$s %2$s</span> (стоимость дана ПРИБЛИЗИТЕЛЬНО. Итоговая сумма на платёжной системе может отличаться)',

  'pay_currency_name' => 'Валюта',
  'pay_currency_symbol' => 'Символ',
  'pay_currency_choose' => 'Выберите валюту платежа',
  'pay_currency_list' => array(
    'RUB' => 'Российский рубль',
    'USD' => 'Доллар США',
    'EUR' => 'Евро',
    'UAH' => 'Украинская гривна',
//    'WMR' => 'WebMoney рубль',
//    'WMZ' => 'WebMoney доллар',
//    'WME' => 'WebMoney евро',
//    'WMU' => 'WebMoney гривна',
//    'WMB' => 'WebMoney белорусский рубль',
  ),

  'pay_methods' => array(
    PAYMENT_METHOD_EMONEY => 'Электронный кошелёк',
    PAYMENT_METHOD_EMONEY_YANDEX => 'Яндекс.Деньги',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => 'WebMoney WMR',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => 'WebMoney WMZ',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => 'WebMoney WMU',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WME => 'WebMoney WME',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => 'WebMoney WMB',
    PAYMENT_METHOD_EMONEY_QIWI => 'QIWI Кошелек',
    PAYMENT_METHOD_EMONEY_ELECSNET => 'Кошелек Элекснет',
    PAYMENT_METHOD_EMONEY_MAILRU => 'Деньги@Mail.Ru',
    PAYMENT_METHOD_EMONEY_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_EMONEY_RUR_W1R => 'RUR Единый Кошелек',
    PAYMENT_METHOD_EMONEY_TELEMONEY => 'TeleMoney',

    PAYMENT_METHOD_BANK_CARD => 'Платежная карта (VISA, MasterCard итд)',
    PAYMENT_METHOD_BANK_CARD_STANDARD => 'Банковская карта',
    PAYMENT_METHOD_BANK_CARD_LIQPAY => 'LiqPay',
    PAYMENT_METHOD_BANK_CARD_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => 'American Express',
    PAYMENT_METHOD_BANK_CARD_JCB => 'JCB',
    PAYMENT_METHOD_BANK_CARD_UNIONPAY => 'UnionPay',

    PAYMENT_METHOD_BANK_INTERNET => 'Через интернет-банк',
    PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK => 'Альфа-Клик',
    PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => 'Банк Русский Стандарт',
    PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK => 'Промсвязьбанк',
    PAYMENT_METHOD_BANK_INTERNET_VTB24 => 'ВТБ24',
    PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK => 'Океан Банк',
    PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK => 'HandyBank',
    PAYMENT_METHOD_BANK_INTERNET_007 => 'Банк Богородский',
    PAYMENT_METHOD_BANK_INTERNET_008 => 'Банк Образование',
    PAYMENT_METHOD_BANK_INTERNET_009 => 'ФлексБанк',
    PAYMENT_METHOD_BANK_INTERNET_010 => 'ФьючерБанк',
    PAYMENT_METHOD_BANK_INTERNET_011 => 'КранБанк',
    PAYMENT_METHOD_BANK_INTERNET_012 => 'Костромаселькомбанк',
    PAYMENT_METHOD_BANK_INTERNET_013 => 'Липецкий областной банк',
    PAYMENT_METHOD_BANK_INTERNET_014 => 'Независимый строительный банк',
    PAYMENT_METHOD_BANK_INTERNET_015 => 'Русский Трастовый Банк',
    PAYMENT_METHOD_BANK_INTERNET_016 => 'ВестИнтерБанк',
    PAYMENT_METHOD_BANK_INTERNET_017 => 'Межтопэнергобанк',
    PAYMENT_METHOD_BANK_INTERNET_018 => 'Московский Индустриальный Банк',
    PAYMENT_METHOD_BANK_INTERNET_019 => 'Банк Интеза',
    PAYMENT_METHOD_BANK_INTERNET_020 => 'Банк Город',
    PAYMENT_METHOD_BANK_INTERNET_021 => 'Банк АВБ',
    PAYMENT_METHOD_BANK_INTERNET_BANK24 => 'Банк24 Национальный кредит',
    PAYMENT_METHOD_BANK_INTERNET_PRIVAT24 => "Приват24",
    PAYMENT_METHOD_BANK_INTERNET_SBERBANK => "Сбербанк Онлайн",

    PAYMENT_METHOD_BANK_TRANSFER => 'Банковский перевод',

    PAYMENT_METHOD_MOBILE => 'С мобильного телефона',
    PAYMENT_METHOD_MOBILE_SMS => 'SMS',
//    PAYMENT_METHOD_MOBILE_XSOLLA => 'Со счёта мобильного',
    PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => 'Со счёта или SMS',
    PAYMENT_METHOD_MOBILE_MEGAPHONE => 'Мегафон',
    PAYMENT_METHOD_MOBILE_MTS => 'МТС',
    PAYMENT_METHOD_MOBILE_KYIVSTAR => 'Киевстар',
    PAYMENT_METHOD_MOBILE_BEELINE => 'Билайн',

    PAYMENT_METHOD_TERMINAL => 'Терминал оплаты',
    PAYMENT_METHOD_TERMINAL_QIWI => 'QIWI Кошелек',
    PAYMENT_METHOD_TERMINAL_ELECSNET => 'Элекснет',
    PAYMENT_METHOD_TERMINAL_ELEMENT => 'Мобил Элемент',
    PAYMENT_METHOD_TERMINAL_KASSIRANET => 'Кассира.нет',
    PAYMENT_METHOD_TERMINAL_IBOX => 'Ibox',
    PAYMENT_METHOD_TERMINAL_UKRAINE => 'Терминалы Украины',
    PAYMENT_METHOD_TERMINAL_RUSSIA => 'Терминалы России',
    PAYMENT_METHOD_TERMINAL_EASYPAY => 'EasyPay',

    PAYMENT_METHOD_OTHER => 'Другие способы',
    PAYMENT_METHOD_OTHER_EVROSET => 'Евросеть',
    PAYMENT_METHOD_OTHER_SVYAZNOY => 'Связной',
    PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => 'Мобильная ROBOKASSA',

    PAYMENT_METHOD_GENERIC => 'Выше перечислены далеко не все возможнные способы оплаты. Если вы не нашли подходящего для себя способа - воспользуйтесь услугами агрегаторов',
//    PAYMENT_METHOD_GENERIC_XSOLLA => 'xSolla',
//    PAYMENT_METHOD_GENERIC_ROBOKASSA => 'RoboKassa',
  ),

  'pay_currency_exchange_title' => 'Внутренние курсы валют',
  'pay_currency_exchange_rate' => 'Курс',
  'pay_currency_exchange_direct' => 'Прямой',
  'pay_currency_exchange_reverse' => 'Обратный',
  'pay_currency_exchange_mm' => '<span class="metamatter">ММ</span> за 1 у.е.',
  'pay_currency_exchange_note' => 'Внутренний курс используется для пересчета из основной валюты сервера в валюту платёжной системы. Курс не включает комиссию посредников и/или платёжных систем',

  'pay_msg_mm_purchase_complete'   => 'Вы успешно заплатили за %d единиц Метаматерии через сервис %s. Вам начислено %s единиц <span class="metamatter">Метаматерии</span>',
  'pay_msg_mm_purchase_incomplete' => 'Ваш платёж за %d единиц <span class="metamatter">Метаматерии</span> через сервис %s не завершен. Если вы считаете, что произошла ошибка - свяжитесь с Администрацией сервера',
  'pay_msg_mm_purchase_test'       => 'На самом деле - шутка. Платеж был тестовый, поэтому ты ничего не получил ха-ха-ха! Если считаешь, что это ошибка - обратись к Администрации сервера',

  'pay_msg_request_user_found' => 'Пользователь найден',
  'pay_msg_request_payment_complete' => 'Платёж завершен',
  'pay_msg_request_payment_cancel_complete' => 'Платёж успешно отменён',

  'pay_msg_request_unsupported' => 'Данный тип запроса не поддерживается',
  'pay_msg_request_signature_invalid' => 'Неправильная подпись запроса',
  'pay_msg_request_user_invalid' => 'Неправильный идентификатор пользователя',
  'pay_msg_request_server_wrong' => 'Неправильный сервер',
  'pay_msg_request_payment_amount_invalid' => 'Неправильная сумма платежа',
  'pay_msg_request_payment_id_invalid' => 'Неправильный идентификатор платежа',
  'pay_msg_request_payment_date_invalid' => 'Неправильная дата платежа',
  'pay_msg_request_internal_error' => 'Внутренняя ошибка сервера. Попробуйте повторить платёж позже',
  'pay_msg_request_paylink_unsupported' => 'Данный тип платёжной ссылке не поддерживается. Возможно используется устаревшая версия СН, не совместимая с данным платёжным модулем',
  'pay_msg_request_payment_write_error' => 'Ошибка записи платежа',
  'pay_msg_request_payment_cancelled_already' => 'Платёж уже отменен',
  'pay_msg_request_payment_cancel_not_complete' => 'Платёж еще не завершен и не может быть отменен',
  'pay_msg_request_payment_cancelled' => '!!! Платёж отозван платёжной системой!!!',
  'pay_msg_request_payment_not_found' => 'Платёж не найден',

  'pay_msg_module_disabled' => 'Платёжный модуль отключен',

  'pay_msg_mm_request_money_and_mm_mismatched' => 'Не совпадает сумма оплаты и количество покупаемой ММ',

  'pay_msg_mm_request_amount_invalid' => 'Неправильное количество <span class="metamatter">Метаматерии</span>',
  'pay_msg_mm_request_config_invalid' => 'Ошибка в конфигурации модуля платежа. Свяжитесь с Администрацией сервера',
  'pay_msg_mm_request_mm_adjust_error' => 'Ошибка начисления <span class="metamatter">Метаматерии</span>',

  'pay_msg_request_error_db_payment_create' => 'Ошибка создания платежа в БД',
  'pay_msg_request_error_test_payment' => 'Статус платежа в БД не совпадает с информацией в запросе',
  'pay_error_internal_no_external_currency_set' => 'ВНУТРЕННЯЯ ОШИБКА или ОШИБКА КОНФИГУРАЦИИ ПЛАТЁЖНОГО МОДУЛЯ! Не установлена валюта платёжной системы! Пожалуйста, сообщите Администрации сервера!',

));
