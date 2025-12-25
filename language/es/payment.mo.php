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
* @system [Spanish]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  // Metamateria
  'sys_metamatter_what_header' => 'Qué es la <span class="metamatter">Metamateria</span>',
  'sys_metamatter_what_description' => 'La <span class="metamatter">Metamateria</span> (abreviado <span class="metamatter">MM</span>) es un nombre convencional para un estado especial del Universo. En realidad, ni siquiera es materia, sino una probabilidad factorizable.<br /><br />
  La <span class="metamatter">Metamateria</span> no tiene estado, y al mismo tiempo está en todos los estados. No está en ningún lugar, y al mismo tiempo está en todas partes. Potencialmente puede convertirse en cualquier cosa y aparecer en cualquier lugar, si se actualiza correctamente su probabilidad.',
  'sys_metamatter_what_purchase' => 'La <span class="metamatter">Metamateria</span> puede considerarse como "Materia Oscura comprable". Si falta <span class="dark_matter">Materia Oscura</span> para adquirir algo, la <span class="metamatter">MM</span> se <span class="ok">convertirá automáticamente</span> en la cantidad faltante de <span class="dark_matter">MO</span> a una tasa de <span class="dark_matter">1 MO</span> = <span class="metamatter">1 MM</span>',

  'pay_mm_convert_header' => 'Conversión de Metamateria a Materia Oscura',
  'pay_mm_convert_text' => 'La Metamateria se convierte a Materia Oscura a una tasa de 1 a 1',
  'pay_mm_convert_no_mm' => 'No tienes Metamateria - cómprala primero',
  'pay_mm_convert_prefix' => 'Unidades de Metamateria',
  'pay_mm_convert_suffix' => '',
  'pay_mm_convert_do' => 'Convertir a MO',

  'pay_msg_mm_convert_wrong_amount' => 'Cantidad incorrecta de Metamateria',
  'pay_msg_mm_convert_not_enough' => 'No hay suficiente Metamateria para convertir a Materia Oscura',
  'pay_msg_mm_convert_mm_error' => 'Error al modificar la cantidad de Metamateria',
  'pay_msg_mm_convert_dm_error' => 'Error al modificar la cantidad de Materia Oscura',

  'pay_mm_buy' => 'Comprar <span class="metamatter">Metamateria</span>',
  'pay_mm_buy_text_cost' => 'Costo',
  'pay_mm_buy_text_unit' => 'es',
  'pay_mm_buy_url_description' => 'La <span class="metamatter">MM</span> solo puede adquirirse con dinero real',
  'pay_mm_buy_url_get'  => 'Abre este enlace para más detalles',
  'pay_mm_buy_url_none' => 'Contacta a la Administración del servidor para obtener <span class="metamatter">Metamateria</span>',

  'pay_mm_bonus_header' => 'Costo de la <span class="metamatter">Metamateria</span> y bonos por compra al por mayor',
  'pay_mm_bonus' => 'Al comprar <span class="metamatter">MM</span> al por mayor se otorgan bonos',
  'pay_mm_bonus_each' => 'desde %s <span class="metamatter">MM</span> - bono del %d%% en cantidad de <span class="metamatter">MM</span>',
  'pay_mm_bonus_text' => 'Bono',

  'pay_mm_buy_step1_text' => 'Selecciona la cantidad de <span class="metamatter">MM</span>, método de pago y confirma tu elección',
  'pay_mm_buy_metamatter_amount' => 'Selecciona la cantidad de <span class="metamatter">Metamateria</span> de la lista',
  'pay_mm_buy_metamatter_amount_enter' => '...o ingresa otra cantidad de <span class="metamatter">Metamateria</span>',
  'pay_mm_buy_price_for' => 'Precio por',
  'pay_mm_buy_unit' => '<span class="metamatter">Metamateria</span>',
  'pay_mm_buy_select' => 'Selecciona el sistema de pago',
  'pay_mm_buy_method_detail' => 'Algunos métodos de pago ofrecen diferentes sistemas de pago. Si un pago no se procesa a través de un sistema, intenta usar el mismo método de pago con otro sistema',
  'pay_mm_buy_confirm' => 'Confirmar selección',
  'pay_mm_buy_payment_selected' => 'El pago se realizará usando el sistema de pago',
  'pay_mm_buy_purchase' => 'Compra',

  'pay_mm_buy_payment_method_more' => 'Haz clic aquí para ver más métodos de pago',

  'pay_mm_buy_payment_method_select' => 'Selecciona el método de pago',
  'pay_mm_buy_payment_method_selected' => 'Has seleccionado el método de pago',

  'pay_mm_buy_step2_text' => 'El costo estimado no incluye comisiones adicionales que puedan cobrar los sistemas de pago y/o intermediarios. Verifica la cantidad seleccionada de <span class="metamatter">Metamateria</span> y el método de pago. Si todo es correcto, haz clic en "Pagar <span class="metamatter">Metamateria</span>". Si cometiste un error, haz clic en "Comenzar de nuevo"',
  'pay_mm_buy_pay' => 'Pagar <span class="metamatter">Metamateria</span>',
  'pay_mm_buy_reset' => 'Comenzar de nuevo',
  'pay_mm_buy_in_progress' => 'Procesando pago...',
  'pay_mm_buy_conversion_cost' => 'El costo estimado de %1$s unidades de <span class="metamatter">Metamateria</span> en la moneda del sistema de pago será <span class="%4$s">%2$s</span> %3$s',
  'pay_mm_buy_cost_base' => 'El costo será',
  'pay_mm_buy_real_income' => 'El bono por compra al por mayor será %s%% y se acreditarán %s <span class="metamatter">MM</span> en tu cuenta de juego',
  'pay_mm_buy_approximate_cost' => 'El costo aproximado de MM en el sistema de pago es <span class="notice">%1$s %2$s</span> (el costo es APROXIMADO. El monto final en el sistema de pago puede variar)',

  'pay_currency_name' => 'Moneda',
  'pay_currency_symbol' => 'Símbolo',
  'pay_currency_choose' => 'Selecciona la moneda de pago',
  'pay_currency_list' => array(
    'RUB' => 'Rublo ruso',
    'USD' => 'Dólar estadounidense',
    'EUR' => 'Euro',
    'UAH' => 'Grivna ucraniana',
  ),

  'pay_methods' => array(
    PAYMENT_METHOD_EMONEY => 'Monedero electrónico',
    PAYMENT_METHOD_EMONEY_YANDEX => 'Yandex.Money',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => 'WebMoney WMR',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => 'WebMoney WMZ',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => 'WebMoney WMU',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WME => 'WebMoney WME',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => 'WebMoney WMB',
    PAYMENT_METHOD_EMONEY_QIWI => 'QIWI Wallet',
    PAYMENT_METHOD_EMONEY_ELECSNET => 'Elecsnet Wallet',
    PAYMENT_METHOD_EMONEY_MAILRU => 'Dinero@Mail.Ru',
    PAYMENT_METHOD_EMONEY_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_EMONEY_RUR_W1R => 'RUR Unified Wallet',
    PAYMENT_METHOD_EMONEY_TELEMONEY => 'TeleMoney',

    PAYMENT_METHOD_BANK_CARD => 'Tarjeta de pago (VISA, MasterCard, etc)',
    PAYMENT_METHOD_BANK_CARD_STANDARD => 'Tarjeta bancaria',
    PAYMENT_METHOD_BANK_CARD_LIQPAY => 'LiqPay',
    PAYMENT_METHOD_BANK_CARD_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => 'American Express',
    PAYMENT_METHOD_BANK_CARD_JCB => 'JCB',
    PAYMENT_METHOD_BANK_CARD_UNIONPAY => 'UnionPay',

    PAYMENT_METHOD_BANK_INTERNET => 'A través de banca por internet',
    PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK => 'Alfa-Click',
    PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => 'Banco Russian Standard',
    PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK => 'Promsvyazbank',
    PAYMENT_METHOD_BANK_INTERNET_VTB24 => 'VTB24',
    PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK => 'Ocean Bank',
    PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK => 'HandyBank',
    PAYMENT_METHOD_BANK_INTERNET_007 => 'Banco Bogorodsky',
    PAYMENT_METHOD_BANK_INTERNET_008 => 'Banco Obrazovanie',
    PAYMENT_METHOD_BANK_INTERNET_009 => 'FlexBank',
    PAYMENT_METHOD_BANK_INTERNET_010 => 'FutureBank',
    PAYMENT_METHOD_BANK_INTERNET_011 => 'KranBank',
    PAYMENT_METHOD_BANK_INTERNET_012 => 'Kostromaselkombank',
    PAYMENT_METHOD_BANK_INTERNET_013 => 'Banco Regional de Lipetsk',
    PAYMENT_METHOD_BANK_INTERNET_014 => 'Banco Independiente de Construcción',
    PAYMENT_METHOD_BANK_INTERNET_015 => 'Russian Trust Bank',
    PAYMENT_METHOD_BANK_INTERNET_016 => 'WestInterBank',
    PAYMENT_METHOD_BANK_INTERNET_017 => 'MezhTopEnergoBank',
    PAYMENT_METHOD_BANK_INTERNET_018 => 'Banco Industrial de Moscú',
    PAYMENT_METHOD_BANK_INTERNET_019 => 'Banco Intesa',
    PAYMENT_METHOD_BANK_INTERNET_020 => 'Banco Ciudad',
    PAYMENT_METHOD_BANK_INTERNET_021 => 'Banco AVB',
    PAYMENT_METHOD_BANK_INTERNET_BANK24 => 'Bank24 Crédito Nacional',
    PAYMENT_METHOD_BANK_INTERNET_PRIVAT24 => "Privat24",
    PAYMENT_METHOD_BANK_INTERNET_SBERBANK => "Sberbank Online",

    PAYMENT_METHOD_BANK_TRANSFER => 'Transferencia bancaria',

    PAYMENT_METHOD_MOBILE => 'Desde teléfono móvil',
    PAYMENT_METHOD_MOBILE_SMS => 'SMS',
    PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => 'Desde cuenta o SMS',
    PAYMENT_METHOD_MOBILE_MEGAPHONE => 'Megafon',
    PAYMENT_METHOD_MOBILE_MTS => 'MTS',
    PAYMENT_METHOD_MOBILE_KYIVSTAR => 'Kyivstar',
    PAYMENT_METHOD_MOBILE_BEELINE => 'Beeline',

    PAYMENT_METHOD_TERMINAL => 'Terminal de pago',
    PAYMENT_METHOD_TERMINAL_QIWI => 'QIWI Wallet',
    PAYMENT_METHOD_TERMINAL_ELECSNET => 'Elecsnet',
    PAYMENT_METHOD_TERMINAL_ELEMENT => 'Mobil Element',
    PAYMENT_METHOD_TERMINAL_KASSIRANET => 'Kassira.net',
    PAYMENT_METHOD_TERMINAL_IBOX => 'Ibox',
    PAYMENT_METHOD_TERMINAL_UKRAINE => 'Terminales de Ucrania',
    PAYMENT_METHOD_TERMINAL_RUSSIA => 'Terminales de Rusia',
    PAYMENT_METHOD_TERMINAL_EASYPAY => 'EasyPay',

    PAYMENT_METHOD_OTHER => 'Otros métodos',
    PAYMENT_METHOD_OTHER_EVROSET => 'Euroset',
    PAYMENT_METHOD_OTHER_SVYAZNOY => 'Svyaznoy',
    PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => 'ROBOKASSA Móvil',

    PAYMENT_METHOD_GENERIC => 'No se enumeran todos los métodos de pago posibles. Si no encuentras un método adecuado, utiliza los servicios de agregadores',
  ),

  'pay_currency_exchange_title' => 'Tipos de cambio internos',
  'pay_currency_exchange_rate' => 'Tipo de cambio',
  'pay_currency_exchange_direct' => 'Directo',
  'pay_currency_exchange_reverse' => 'Inverso',
  'pay_currency_exchange_mm' => '<span class="metamatter">MM</span> por 1 u.m.',
  'pay_currency_exchange_note' => 'El tipo de cambio interno se utiliza para convertir de la moneda principal del servidor a la moneda del sistema de pago. El tipo de cambio no incluye comisiones de intermediarios y/o sistemas de pago',

  'pay_msg_mm_purchase_complete'   => 'Has pagado exitosamente por %d unidades de Metamateria a través del servicio %s. Se te han acreditado %s unidades de <span class="metamatter">Metamateria</span>',
  'pay_msg_mm_purchase_incomplete' => 'Tu pago por %d unidades de <span class="metamatter">Metamateria</span> a través del servicio %s no se ha completado. Si crees que hubo un error, contacta a la Administración del servidor',
  'pay_msg_mm_purchase_test'       => 'En realidad, es una broma. ¡El pago fue de prueba, así que no recibiste nada, ja ja ja! Si crees que es un error, contacta a la Administración del servidor',

  'pay_msg_request_user_found' => 'Usuario encontrado',
  'pay_msg_request_payment_complete' => 'Pago completado',
  'pay_msg_request_payment_cancel_complete' => 'Pago cancelado exitosamente',

  'pay_msg_request_unsupported' => 'Este tipo de solicitud no es compatible',
  'pay_msg_request_signature_invalid' => 'Firma de solicitud incorrecta',
  'pay_msg_request_user_invalid' => 'ID de usuario incorrecto',
  'pay_msg_request_server_wrong' => 'Servidor incorrecto',
  'pay_msg_request_payment_amount_invalid' => 'Monto de pago incorrecto',
  'pay_msg_request_payment_id_invalid' => 'ID de pago incorrecto',
  'pay_msg_request_payment_date_invalid' => 'Fecha de pago incorrecta',
  'pay_msg_request_internal_error' => 'Error interno del servidor. Intenta realizar el pago más tarde',
  'pay_msg_request_paylink_unsupported' => 'Este tipo de enlace de pago no es compatible. Posiblemente se esté usando una versión obsoleta de SN, incompatible con este módulo de pago',
  'pay_msg_request_payment_write_error' => 'Error al registrar el pago',
  'pay_msg_request_payment_cancelled_already' => 'El pago ya fue cancelado',
  'pay_msg_request_payment_cancel_not_complete' => 'El pago aún no se ha completado y no puede cancelarse',
  'pay_msg_request_payment_cancelled' => '!!! ¡El pago fue revocado por el sistema de pago!',
  'pay_msg_request_payment_not_found' => 'Pago no encontrado',

  'pay_msg_module_disabled' => 'Módulo de pago desactivado',

  'pay_msg_mm_request_money_and_mm_mismatched' => 'No coincide el monto del pago con la cantidad de MM comprada',

  'pay_msg_mm_request_amount_invalid' => 'Cantidad incorrecta de <span class="metamatter">Metamateria</span>',
  'pay_msg_mm_request_config_invalid' => 'Error en la configuración del módulo de pago. Contacta a la Administración del servidor',
  'pay_msg_mm_request_mm_adjust_error' => 'Error al acreditar <span class="metamatter">Metamateria</span>',

  'pay_msg_request_error_db_payment_create' => 'Error al crear el pago en la base de datos',
  'pay_msg_request_error_test_payment' => 'El estado del pago en la base de datos no coincide con la información en la solicitud',
  'pay_error_internal_no_external_currency_set' => '¡ERROR INTERNO o ERROR DE CONFIGURACIÓN DEL MÓDULO DE PAGO! ¡No se ha establecido la moneda del sistema de pago! Por favor, informa a la Administración del servidor!',
));