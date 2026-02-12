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
* @system [Chinese]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  // Metamatter
  'sys_metamatter_what_header' => '什么是 <span class="metamatter">超物质</span>',
  'sys_metamatter_what_description' => '<span class="metamatter">超物质</span>（简称 <span class="metamatter">MM</span>）是宇宙特殊状态的一个非常随意的名称。实际上 - 它甚至不是物质，而是可分解的概率。<br /><br />
  <span class="metamatter">超物质</span>没有状态 - 同时它处于所有状态。<span class="metamatter">超物质</span>无处不在 - 同时它无处不在。潜在地，如果正确实现概率，超物质可以成为任何东西，在任何地方。',
  'sys_metamatter_what_purchase' => '基本上 <span class="metamatter">超物质</span> 是一种“可购买的 <span class="dark_matter">暗物质</span>” - 当缺少 <span class="metamatter">暗物质</span> 来购买某物时，<span class="metamatter">超物质</span> 将<span class="ok">自动转换</span>为必要数量的 <span class="dark_matter">暗物质</span>，比例为 <span class="dark_matter">1 暗物质</span> = <span class="metamatter">1 超物质</span>',

  'pay_mm_convert_header' => '将超物质转换为暗物质',
  'pay_mm_convert_text' => '超物质按 1 比 1 的汇率转换为暗物质',
  'pay_mm_convert_no_mm' => '没有超物质 - 请先购买',
  'pay_mm_convert_prefix' => '超物质单位',
  'pay_mm_convert_suffix' => '',
  'pay_mm_convert_do' => '转换为暗物质',

  'pay_msg_mm_convert_wrong_amount' => '超物质数量错误',
  'pay_msg_mm_convert_not_enough' => '没有足够的超物质转换为暗物质',
  'pay_msg_mm_convert_mm_error' => '更改超物质数量时出错',
  'pay_msg_mm_convert_dm_error' => '更改暗物质数量时出错',

  'pay_mm_buy' => '购买 <span class="metamatter">超物质</span>',
  'pay_mm_buy_text_cost' => '价格为',
  'pay_mm_buy_text_unit' => '是',
  'pay_mm_buy_url_description' => '此外，您可以用真钱购买',
  'pay_mm_buy_url_get'  => '点击此处阅读详情',
  'pay_mm_buy_url_none' => '联系服务器管理员获取 <span class="metamatter">超物质</span>',

  'pay_mm_bonus_header' => '<span class="metamatter">超物质</span> 批量购买的成本和奖金',
  'pay_mm_bonus' => '当您购买大量 <span class="metamatter">超物质</span> 时，您将获得奖金：',
  'pay_mm_bonus_each' => '从 %s <span class="metamatter">MM</span> 起 - 购买的 <span class="metamatter">MM</span> 数量增加 %d%% 奖金',
  'pay_mm_bonus_text' => '奖金',

  'pay_mm_buy_step1_text' => '选择您希望购买的 <span class="metamatter">MM</span> 数量，选择支付系统并确认您的选择',
  'pay_mm_buy_metamatter_amount' => '从列表中选择 <span class="metamatter">超物质</span> 数量',
  'pay_mm_buy_metamatter_amount_enter' => '...或输入您想要的 <span class="metamatter">超物质</span> 数量',
  'pay_mm_buy_price_for' => '价格为',
  'pay_mm_buy_unit' => '<span class="metamatter">超物质</span>',
  'pay_mm_buy_select' => '选择支付系统',
  'pay_mm_buy_method_detail' => '某些支付方式提供不同的支付系统选择。如果通过一个支付系统支付失败 - 尝试使用相同的支付方式但选择另一个支付系统',
  'pay_mm_buy_confirm' => '确认选择',
  'pay_mm_buy_payment_selected' => '购买将使用支付系统进行',
  'pay_mm_buy_purchase' => '购买',

  'pay_mm_buy_payment_method_more' => '按“显示”按钮，查看更多支付方式',

  'pay_mm_buy_payment_method_select' => '选择支付方式',
  'pay_mm_buy_payment_method_selected' => '您选择的支付方式是',

  'pay_mm_buy_step2_text' => '计算出的价格不包括支付系统或支付聚合器可能收取的额外佣金。验证选择的 <span class="metamatter">超物质</span> 数量和选择的支付系统。如果一切正常，请按“购买 <span class="metamatter">超物质</span>”按钮。如果有任何错误 - 请按“放弃并重新开始”按钮',
  'pay_mm_buy_pay' => '购买 <span class="metamatter">超物质</span>',
  'pay_mm_buy_reset' => '放弃并重新开始',
  'pay_mm_buy_in_progress' => '支付进行中...',
  'pay_mm_buy_conversion_cost' => '以支付系统货币计算的 %1$s <span class="metamatter">超物质</span> 的成本将是 <span class="%4$s">%2$s</span> %3$s',
  'pay_mm_buy_cost_base' => '成本将是',
  'pay_mm_buy_real_income' => '批量购买奖金将为 %s%%，您的游戏账户将记入 %s <span class="metamatter">MM</span>',

  'pay_currency_name' => '货币',
  'pay_currency_symbol' => '符号',
  'pay_currency_choose' => '选择支付货币',
  'pay_currency_list' => array(
    'RUB' => '俄罗斯卢布',
    'USD' => '美元',
    'EUR' => '欧元',
    'UAH' => '乌克兰格里夫纳',
//    'WMR' => 'WebMoney 卢布',
//    'WMZ' => 'WebMoney 美元',
//    'WME' => 'WebMoney 欧元',
//    'WMU' => 'WebMoney 格里夫纳',
//    'WMB' => 'WebMoney 白俄罗斯卢布',
  ),

  'pay_methods' => array(
    PAYMENT_METHOD_EMONEY => '电子钱包',
    PAYMENT_METHOD_EMONEY_YANDEX => 'Yandex.Money',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMR => 'WebMoney WMR',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ => 'WebMoney WMZ',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMU => 'WebMoney WMU',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WME => 'WebMoney WME',
    PAYMENT_METHOD_EMONEY_WEBMONEY_WMB => 'WebMoney WMB',
    PAYMENT_METHOD_EMONEY_QIWI => 'QIWI 钱包',
    PAYMENT_METHOD_EMONEY_ELECSNET => 'Elecsnet 钱包',
    PAYMENT_METHOD_EMONEY_MAILRU => 'Money@Mail.Ru',
    PAYMENT_METHOD_EMONEY_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_EMONEY_RUR_W1R => 'RUR 统一钱包',
    PAYMENT_METHOD_EMONEY_TELEMONEY => 'TeleMoney',

    PAYMENT_METHOD_BANK_CARD => '支付卡 (VISA, MasterCard 等)',
    PAYMENT_METHOD_BANK_CARD_STANDARD => '银行卡',
    PAYMENT_METHOD_BANK_CARD_LIQPAY => 'LiqPay',
    PAYMENT_METHOD_BANK_CARD_EASYPAY => 'EasyPay',
    PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS => 'American Express',
    PAYMENT_METHOD_BANK_CARD_JCB => 'JCB',
    PAYMENT_METHOD_BANK_CARD_UNIONPAY => '银联',

    PAYMENT_METHOD_BANK_INTERNET => '通过网上银行',
    PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK => 'Alfa-Click',
    PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART => '俄罗斯标准银行',
    PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK => 'Promsvyazbank',
    PAYMENT_METHOD_BANK_INTERNET_VTB24 => 'VTB24',
    PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK => 'Ocean Bank',
    PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK => 'HandyBank',
    PAYMENT_METHOD_BANK_INTERNET_007 => 'Bogorodsky Bank',
    PAYMENT_METHOD_BANK_INTERNET_008 => 'Bank Obrazovanie',
    PAYMENT_METHOD_BANK_INTERNET_009 => 'FlexBank',
    PAYMENT_METHOD_BANK_INTERNET_010 => 'FutureBank',
    PAYMENT_METHOD_BANK_INTERNET_011 => 'KranBank',
    PAYMENT_METHOD_BANK_INTERNET_012 => 'Kostromaselcombank',
    PAYMENT_METHOD_BANK_INTERNET_013 => 'Lipetsk Regional Bank',
    PAYMENT_METHOD_BANK_INTERNET_014 => 'Independent Building Bank',
    PAYMENT_METHOD_BANK_INTERNET_015 => 'Russian Trust Bank',
    PAYMENT_METHOD_BANK_INTERNET_016 => 'WestInterBank',
    PAYMENT_METHOD_BANK_INTERNET_017 => 'Mezhtopenergobank',
    PAYMENT_METHOD_BANK_INTERNET_018 => 'Moscow Industrial Bank',
    PAYMENT_METHOD_BANK_INTERNET_019 => 'Intesa Bank',
    PAYMENT_METHOD_BANK_INTERNET_020 => 'Bank Gorod',
    PAYMENT_METHOD_BANK_INTERNET_021 => 'Bank AVB',
    PAYMENT_METHOD_BANK_INTERNET_BANK24 => 'Bank24 National Credit',
    PAYMENT_METHOD_BANK_INTERNET_PRIVAT24 => "Privat24",
    PAYMENT_METHOD_BANK_INTERNET_SBERBANK => "Sberbank Online",

    PAYMENT_METHOD_BANK_TRANSFER => '银行转账',

    PAYMENT_METHOD_MOBILE => '手机支付',
    PAYMENT_METHOD_MOBILE_SMS => '短信',
//    PAYMENT_METHOD_MOBILE_XSOLLA => '手机账户',
    PAYMENT_METHOD_MOBILE_PAYPAL_ZONG => '账户或短信',
    PAYMENT_METHOD_MOBILE_MEGAPHONE => 'Megafon',
    PAYMENT_METHOD_MOBILE_MTS => 'MTS',
    PAYMENT_METHOD_MOBILE_KYIVSTAR => 'Kyivstar',
    PAYMENT_METHOD_MOBILE_BEELINE => 'Beeline',

    PAYMENT_METHOD_TERMINAL => '支付终端',
    PAYMENT_METHOD_TERMINAL_QIWI => 'QIWI 钱包',
    PAYMENT_METHOD_TERMINAL_ELECSNET => 'Elecsnet',
    PAYMENT_METHOD_TERMINAL_ELEMENT => 'Mobile Element',
    PAYMENT_METHOD_TERMINAL_KASSIRANET => 'Kassira.net',
    PAYMENT_METHOD_TERMINAL_IBOX => 'Ibox',
    PAYMENT_METHOD_TERMINAL_UKRAINE => '乌克兰终端',
    PAYMENT_METHOD_TERMINAL_RUSSIA => '俄罗斯终端',
    PAYMENT_METHOD_TERMINAL_EASYPAY => 'EasyPay',

    PAYMENT_METHOD_OTHER => '其他方式',
    PAYMENT_METHOD_OTHER_EVROSET => 'Euroset',
    PAYMENT_METHOD_OTHER_SVYAZNOY => 'Svyaznoy',
    PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE => 'Mobile ROBOKASSA',

    PAYMENT_METHOD_GENERIC => '以上并不是所有可能的支付方式。如果您没有找到适合您的方式 - 请使用聚合器服务',
//    PAYMENT_METHOD_GENERIC_XSOLLA => 'xSolla',
//    PAYMENT_METHOD_GENERIC_ROBOKASSA => 'RoboKassa',
  ),

  'pay_currency_exchange_title' => '内部货币兑换',
  'pay_currency_exchange_rate' => '汇率',
  'pay_currency_exchange_direct' => '直接',
  'pay_currency_exchange_reverse' => '反向',
  'pay_currency_exchange_mm' => '<span class="metamatter">MM</span> 兑换 1 货币',
  'pay_currency_exchange_note' => '内部汇率用于计算支付系统货币的支付金额。汇率不包括支付系统的佣金',

  'pay_msg_mm_purchase_complete'   => '您通过 %s 成功支付了 %d <span class="metamatter">超物质</span>。您获得了 %s <span class="metamatter">超物质</span>',
  'pay_msg_mm_purchase_incomplete' => '您通过 %s 支付的 %d <span class="metamatter">超物质</span> 目前正在处理中。如果您觉得有误，请联系管理员',
  'pay_msg_mm_purchase_test'       => '真的，您什么也没得到！因为这是一个测试支付，哈哈！如果您觉得有误 - 请联系管理员',

  'pay_msg_request_user_found' => '找到用户',
  'pay_msg_request_payment_complete' => '支付完成',
  'pay_msg_request_payment_cancel_complete' => '支付成功取消',

  'pay_msg_request_unsupported' => '不支持的请求',
  'pay_msg_request_signature_invalid' => '请求签名错误',
  'pay_msg_request_user_invalid' => '用户 ID 无效',
  'pay_msg_request_server_wrong' => '服务器错误',
  'pay_msg_request_payment_amount_invalid' => '支付金额错误',
  'pay_msg_request_payment_id_invalid' => '支付 ID 错误',
  'pay_msg_request_payment_date_invalid' => '支付日期错误',
  'pay_msg_request_internal_error' => '服务器内部错误。请稍后再试',
  'pay_msg_request_paylink_unsupported' => '不支持此类型的支付链接。看来您使用的是与所选支付模块不兼容的 SuperNova 过期版本',
  'pay_msg_request_payment_write_error' => '支付写入错误',
  'pay_msg_request_payment_cancelled_already' => '支付已取消',
  'pay_msg_request_payment_cancel_not_complete' => '支付尚未完成，无法取消',
  'pay_msg_request_payment_cancelled' => '!!! 支付被支付系统撤销 !!!',
  'pay_msg_request_payment_not_found' => '未找到支付',

  'pay_msg_module_disabled' => '支付模块已禁用',

  'pay_msg_mm_request_money_and_mm_mismatched' => '支付金额与购买的 MM 数量不匹配',

  'pay_msg_mm_request_amount_invalid' => '错误的 <span class="metamatter">超物质</span> 数量',
  'pay_msg_mm_request_config_invalid' => '支付模块配置有误。请联系服务器管理员',
  'pay_msg_mm_request_mm_adjust_error' => '调整 <span class="metamatter">超物质</span> 时出错',

  'pay_msg_request_error_db_payment_create' => '在数据库中创建支付时出错',
  'pay_msg_request_error_test_payment' => '数据库中的支付状态与请求中的信息不匹配',
  'pay_msg_error_internal_no_external_currency_set' => '内部错误或支付模块配置错误！未设置支付系统货币！请通知服务器管理员！',

));
