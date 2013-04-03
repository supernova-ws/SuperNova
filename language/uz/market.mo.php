<?php

/*
#############################################################################
#  Filename: market.mo
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
* @system [Russian]
* @version 37a5.8
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'eco_mrk_title' => 'Qora bozor ',
  'eco_mrk_description' => 'Imperiyani boshqarish interfeysida bunday joyi yo`q edi... Qiziq qayoqdan paydo bo`ldi ekan ?',
  'eco_mrk_service' => 'Xizmat',
  'eco_mrk_service_cost' => 'Xizmat xaqi',

  'eco_mrk_trader' => 'Resurslarni almashtirish',
  'eco_mrk_trader_cost' => 'Almashtiriladigan resurslar qiymati',
  'eco_mrk_trader_exchange' => 'Almashtish',
  'eco_mrk_trader_to' => 'ga almashtirish',
  'eco_mrk_trader_course' => 'Kurs',
  'eco_mrk_trader_left' => 'Qoldiq',
  'eco_mrk_trader_resources_all' => 'Hamma resurslar',

  'eco_mrk_scraper' => 'Kema xaridi',
  'eco_mrk_scraper_price' => 'Chiqish bo`lagi',
  'eco_mrk_scraper_perShip' => 'Kema bilan',
  'eco_mrk_scraper_total' => 'Umumiy',
  'eco_mrk_scraper_cost' => 'Kemani bo`laklagan holatda sotish',
  'eco_mrk_scraper_onOrbit' => 'Orbitada',
  'eco_mrk_scraper_to' => 'Parchalab tashlash',
  'eco_mrk_scraper_res' => 'Keyingi parchalarni olish:',
  'eco_mrk_scraper_ships' => 'Keyingi kemalar parchalandi:',
  'eco_mrk_scraper_noShip' => 'Orbitada kema mavjud emas',

  'eco_mrk_stockman' => 'Foydalanilgan kemalarni sotish',
  'eco_mrk_stockman_price' => 'Bahosi',
  'eco_mrk_stockman_perShip' => 'Kema',
  'eco_mrk_stockman_onStock' => 'Sotuvchida',
  'eco_mrk_stockman_buy' => 'Kema sotib olish',
  'eco_mrk_stockman_res' => 'Sotib olingan kemaning bahosi:',
  'eco_mrk_stockman_ships' => 'Sotilgan keyingi kema :',
  'eco_mrk_stockman_noShip' => 'Sotuvchida hozir sotishga kema yo`q',

  'eco_mrk_exchange' => 'Rerus almashtirish birjasi',
  'eco_mrk_banker'   => 'Bankir',
  'eco_mrk_pawnshop' => 'Lombard',

  'eco_mrk_info' => 'Sotuvchining ma`lumoti',
  'eco_mrk_info_description' => 'Kiruvchi pochta quydagi mazmundagi maktub topildi:',
  'eco_mrk_info_description_2' => 'Menda ko\'pgina qiziqarli ma\'lumotlarga ruxsat bor. Men buni siz bilan baham ko\'rishim mumkin... kamtarona mukofot evaziga. Birgina so\'rov - faqat',
  'eco_mrk_info_buy' => 'Ma`lumotni sotib olish',

  'eco_mrk_info_player' => 'O`yinchi haqida ma`lumot',
  'eco_mrk_info_player_description' => 'Men sizga o\'yinchining hozirda qanaqa yollanma ishchilari borligini bilib berishim mumkin',
  'eco_mrk_info_player_message' => 'Mendagi ishonchli ma\'lumotlarga qaraganda ID %1$d [%2$s] gi o\'yinchining yollanma ishchilari quyidagilardan iborat:',

  'eco_mrk_info_not_hired' => 'yollanmagan',

  'eco_mrk_info_ally' => 'Ittifoq haqida ma\'lumot',
  'eco_mrk_info_online' => 'Fazodagi hozirgi faollik',

  'eco_mrk_info_msg_from' => 'Noma\'lum manbaa',

  'eco_mrk_error_title' => 'Qora bozor - Xato',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'Jarayon muvoffaqqiyatli o\'tdi',
    MARKET_SCRAPPER => 'Ma\'lumot almashinuv jarayoni muvaffaqiyatli o\'tdi',
    MARKET_NOT_A_SHIP => 'Kemadan tashqari narsani sotishga urunish kerakmas!',
    MARKET_STOCKMAN => 'Jarayon tugallanishi uchun TM yetishmayapti',
    MARKET_NO_RESOURCES => 'Jarayonni tugatish uchun resurs yetishmayapti',
    MARKET_PAWNSHOP => 'Siz orbitada mavjud kemalardanda ko\'proq kemani bo\'laklarga bo\'lmoqchisiz',
    MARKET_NO_STOCK => 'Siz sotuvchida mavjud kemadanda ko\'proq sotib olmoqchi bo\'layapsiz. Siz kemalarni tanlayotganingizda boshqa bir o\'yinchi sotib olgan bo\'lishi mumkin',
    MARKET_ZERO_DEAL => 'Almashinuv uchun resurs miqdori ko\'rsatilmadi',
    MARKET_NOTHING => 'Sotish uchun kemalarni tanlash kerak',
    MARKET_ZERO_RES_STOCK => 'Harid qilish uchun kemalarni tanlash kerak',
    MARKET_NEGATIVE_SHIPS => 'Manfiy miqdorda kema sotishga harakat qilmang!',

    MARKET_NO_DM => 'Jarayonni tugatish uchun To`q Materiya yetishmaydi',
    MARKET_INFO_WRONG => 'Bunday ma`lumot yo`q',
    MARKET_INFO_PLAYER => 'Ma`lumot muvaffaqiyatli sotib olindi. Elektron pochtangizni tekshirib ko`ring',
    MARKET_INFO_PLAYER_WRONG => 'ID yoki o`yinchining nomini ko`rsatish kerak',
    MARKET_INFO_PLAYER_NOT_FOUND => 'O`yinchini aniqlashtira olmaysiz.Agar o`yinchining nomi raqam yoki qiyin yozuvlardan iborat bo`lsa, u holda uning ID sidan foydalaning',
    MARKET_INFO_PLAYER_SAME => 'Nimaga o`zing haqingdagi ma`lumotni o`rganding?',
  ),

));

?>
