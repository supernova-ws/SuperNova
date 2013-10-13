<?php

/*
#############################################################################
#  Filename: system.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Uzbekin]
* @version #37a13.13#
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE'))
{
  exit;
}

if (empty($lang) || !is_array($lang))
{
  $lang = array();
}

// System-wide localization
$lang = array_merge($lang, array(
  // Dark Matter
  'sys_dark_matter_what_header' => 'TM (Tyomniy Materiya) nima degani?',
  'sys_dark_matter_description_header' => 'TM nimaga kerak bo`ladi?',
  'sys_dark_matter_description_text' => 'TM - bu o`yin ichidagi asosiy resurslardan biri bo`lib, o`yin ichida har xil operatsiyalarni bajarishga yordam beradi:
    <ul>
      <li>Sotib olish <a href="index.php?page=premium"><span class="link">Premium-akkaunt</span></a></li>
      <li>Ishga biriktirish <a href="officer.php?mode=600"><span class="link">Yollanma odamlar</span></a> Imperiya uchun </li>
      <li>Gubernatorlar yollash va sektorlar sotib olish <a href="overview.php?mode=manage"><span class="link">sayyorada</span></a></li>
      <li>Sotib olish mumkin: <a href="officer.php?mode=1100"><span class="link">Chizmalarni</span></a></li>
      <li>Sotib olish mumkin: <a href="artifacts.php"><span class="link">Artefaktlarni</span></a></li>
      <li>Foydalanish mumkin: <a href="market.php"><span class="link">Qora bozor</span></a>: Bir turdagi resursni boshqa turdagi resursga almashtirish; Kemalarni sotish va boshqalar</li>
      <li>...va boshqa ko`plab ishlarni o`z ichiga oladi</li>
    </ul>',
  'sys_dark_matter_obtain_header' => 'TM ni qayerdan olsa boladi?',
  'sys_dark_matter_obtain_text' => 'Siz TM ni o`yin ichida quyidagi holatlarda yutib olishingiz mumkin bo`ladi: Tajribangiz oshgan sayin, begona sayyoralarda muvaffaqayatli yurishlar bilan, yangi tehnologiyalarni darajasini oshirish bilan, hamda yangi binolarni qurish va uni buzish bilan ham TM olsa boladi.
    Hamda ba`zida Ekspeditsiya vaqtida yuborgan flotingiz TM olib kelishi mumkin.',

  'sys_dark_matter_purchase_url_description' => 'TM ni Webmoney orqali olishingiz ham mumkin.',
  'sys_dark_matter_purchase_url_get'  => 'Batafsilroq ma`lumot olish uchun ushbu manzilga o`ting.',


  'sys_dark_matter_purchase' => 'TM sotib olish',
  'sys_dark_matter_purchase_text_cost' => 'Narhi',
  'sys_dark_matter_purchase_text_unit' => 'tuzmoq',
  'sys_dark_matter_purchase_text_end' => 'TM ni ko`p miqdorda sotib olganlarga bonus taqdim etiladi:', //  TM sotib olishni yo'li.
  'sys_dark_matter_purchase_text_bonus' => 'boshlang\'ich %s ТМ - bonus %d%% miqdorda qoshib beriladi',
  'sys_dark_matter_purchase_step1' => '1-qadam',
  'sys_dark_matter_purchase_step1_text' => 'TM miqdorini, pul to`lash yo`lini tanlang va tasdiqlang',
  'sys_dark_matter_purchase_amount' => 'TM soni',
  'sys_dark_matter_purchase_select' => 'Pul to`lash yo`li',
  'sys_dark_matter_purchase_confirm' => 'Tasdiqlang',
  'sys_dark_matter_purchase_payment_selected' => 'Pul tushirish uchun kerakli karmonlardan foydalaning',

  'sys_dark_matter_purchase_step2' => '2-qadam',
  'sys_dark_matter_purchase_step2_text' => 'Siz uchun kerakli bo`lgan TM miqdorini ko`rsating va to`lash usulini tanlang. Agar hammasi to`g`ri bolsa "TM uchun tolov" tugmasini bosing. Agar adashgan bo`lsangiz "Yangitdan boshlash" tugmasini bosing',
  'sys_dark_matter_purchase_pay' => 'TM to`lash',
  'sys_dark_matter_purchase_reset' => 'Yangitdan boshlash',
  'sys_dark_matter_purchase_in_progress' => 'To`lov bajarilmoqda...',
  'sys_dark_matter_purchase_conversion_cost' => 'Стоимость %d Тёмной Материи составит %s %s',

  'sys_dark_matter_purchase_exchange' => 'Внутренние курсы валют',
  'sys_currency_name' => 'Валюта',
  'sys_currency_symbol' => 'Символ',
  'sys_currency_exchange_direct' => 'Прямой курс',
  'sys_currency_exchange_reverse' => 'Обратный курс',
  'sys_currency_exchange_dm' => 'ТМ за 1 у.е.',
  'sys_currencies' => array(
    'RUB' => 'Российский рубль',
    'USD' => 'Доллар США',
    'EUR' => 'Евро',
    'UAH' => 'Украинская гривна',
    'WMR' => 'WebMoney рубль',
    'WMZ' => 'WebMoney доллар',
    'WME' => 'WebMoney евро',
    'WMU' => 'WebMoney гривна',
  ),
  'sys_dark_matter_purchase_exchange_note' => 'Внутренний курс используется для пересчета из основной валюты сервера в валюту плтаженой системы. Курс не включает комиссию посредников и/или платежных систем',

  'sys_dark_matter_purchase_result_complete' => 'Вы успешно заплатили за %d Тёмной Материи через сервис %s. Вам начислено %s Тёмной Материи',
  'sys_dark_matter_purchase_result_incomplete' => 'Ваш платеж за %d Тёмной Материи через сервис %s не закончен. Если вы считаете, что произошла ошибка - свяжитесь с Администрацией сервера',
  'sys_dark_matter_purchase_result_test' => 'На самом деле - шутка. Платеж был тестовый, поэтому ты ничего не получил ха-ха-ха! Если считаешь, что это ошибка - обратись к Администрации сервера',

  'pay_msg_request_user_found' => 'Foydalanuvchi topildi',

  'pay_msg_request_unsupported' => 'So`rovning bunday turi qo`llab quvvatlanmaydi',
  'pay_msg_request_signature_invalid' => 'So`rovning imzosi noto`g`ri',
  'pay_msg_request_user_invalid' => 'Foydalanuvchining identifikatori noto`g`ri',
  'pay_msg_request_server_wrong' => 'Server noto`g`ri',
  'pay_msg_request_payment_amount_invalid' => 'To`lov summasi noto`g`ri',
  'pay_msg_request_payment_id_invalid' => 'To`lov identifikatori noto`g`ri',
  'pay_msg_request_payment_date_invalid' => 'to`lovning kuni noto`g`ri',
  'pay_msg_request_internal_error' => 'Serverning ichida hatolik. To`lovni keyinroq amalga oshirib koring',

  'pay_msg_request_dark_matter_amount_invalid' => 'TM soni noto`g`ri',
  'pay_msg_request_paylink_unsupported' => 'Bunday turdagi to`lov sahifasi qo`llanmaydi. SN ning eskirgan versiyasini qo`llayotgan bo`lishingiz mumkin',
  'pay_msg_request_dark_matter_config_invalid' => 'Ошибка в конфигурации модуля платежа. Свяжитесь с Администрацией сервера',

  'pay_msg_module_disabled' => 'To`lov moduli o`chirilgan',


  'sys_administration' => 'Sverhnovaning Adminstratorlari',
  'sys_birthday' => 'Tug`ilgan yil',
  'sys_birthday_message' => '%1$s! Sverhnovaning Adminstratorlari sizni tug`ilgan kuningiz bilan muborakbod etadi va sizga  %3$d %4$s sovg`a qiladi! Sizga chin qalbdan o`yinimizda omad va statistikamizning yuqori o`rinlarida bo`lishingizni tilab qoladi!',

  'adm_err_denied' => 'Kirish taqiqlanaid. Sizda bu sahifani boshqarish huquqi yo`q',

  'sys_empire'          => 'Imperiya',
  'VacationMode'			=> "Siz ta`tilga chiqqaniz uchun sizning ishlab chiqarishingiz yopilgan",
  'sys_moon_destruction_report' => "Oyni yo`q qilish haqida raport",
  'sys_moon_destroyed' => "Sizning O`lim yulduzingiz (Звёзды Смерти ZS) singiz oyni yo`qotishda kuchli tortishuvchi to`lqinga duch keldi! ",
  'sys_rips_destroyed' => "Sizning O`lim yulduzingiz (Звёзды Смерти ZS) singiz kuchli tortishuvchi to`lqin olib keldi lekin bunday kattalikdagi oyni yo`q qila olmadi. Yuzaga kelgan tortishuvchi to`lqindan sizning flotingiz buzilib ketdi.",
  'sys_rips_come_back' => "Sizning O`lim yulduzingiz (Звёзды Смерти ZS) singiz bu oyni yo`q qilish uchun energiya yetishmayapdi. Sizning flotingiz oyni yo`q qila olmasdan qaytib kelayapdi.",
  'sys_chance_moon_destroy' => "Oyni yo`q qilishdagi o`zgarishlar: ",
  'sys_chance_rips_destroy' => "Yoq qilishda portlash ishlari qo`llashdagi o`zgarishlar: ",

  'sys_impersonate' => 'To`lov',
  'sys_impersonate_done' => 'qayta to`lov',
  'sys_impersonated_as' => 'Diqqat! Siz hozir %1$s o`yinchiga tolov qilyapsiz.',

  'sys_day' => "kun",
  'sys_hrs' => "soat",
  'sys_min' => "daqiqa",
  'sys_sec' => "soniya",
  'sys_day_short' => "k",
  'sys_hrs_short' => "s",
  'sys_min_short' => "d",
  'sys_sec_short' => "s",

  'sys_ask_admin' => 'Manzil bo`yicha savollar va takliflar',

  'sys_wait'      => 'So`rov bajarilmoqda. Iltimos biroz kuting.',

  'sys_fleets'       => 'Flotlar',
  'sys_expeditions'  => 'Ekspeditsiyalar',
  'sys_fleet'        => 'Flot',
  'sys_expedition'   => 'Ekspeditsiya',
  'sys_event_next'   => 'Keyingi hodisa:',
  'sys_event_arrive' => 'kelish',
  'sys_event_stay'   => 'vazifani tugatish',
  'sys_event_return' => 'qaytish',

  'sys_total'           => "Jami",
  'sys_need'				=> 'Zarur',
  'sys_register_date'   => 'Ro`yhatdan o`tgan vaqt',

  'sys_attacker' 		=> "Hujumchi",
  'sys_defender' 		=> "Himoyalanuvchi",

  'COE_combatSimulator' => "Urushni simulyatsiya qilish",
  'COE_simulate'        => "simulyatorni ishga tushirish",
  'COE_fleet'           => "Flot",
  'COE_defense'         => "Himoya",
  'sys_coe_combat_start'=> "Dushman floti bilan uchrashuv",
  'sys_coe_combat_end'  => "Urush natijasi",
  'sys_coe_round'       => "Round",

  'sys_coe_attacker_turn'=> 'Hujumchi bor kuchi bilan hujumga o`tdi. Uning qalqonlari %2$s o`qqa bardosh bera oladi<br />',
  'sys_coe_defender_turn'=> 'Himoyalanuvchi bor kuchini tashladi. Uning qalqonlari %2$s o`qqa bardosh bera oladi<br /><br /><br />',
  'sys_coe_outcome_win'  => 'Himoyalanuvchi bu jangda g`olib bo`ldi!<br />',
  'sys_coe_outcome_loss' => 'Hujumchi bu jangda g`olib bo`ldi!<br />',
  'sys_coe_outcome_loot' => 'U %1$s metal, %2$s kristal, %3$s yoqilgi ga ega bo`ldi<br />',
  'sys_coe_outcome_draw' => 'Urush durang bilan tugadi.<br />',
  'sys_coe_attacker_lost'=> 'Hujumchi %1$s ta kema yo`qotdi.<br />',
  'sys_coe_defender_lost'=> 'Himoyachi %1$s ta kema yo`qotdi.<br />',
  'sys_coe_debris_left'  => 'Endi bu koordinatasida %1$s metal va %2$s kristal mavjud.<br /><br />',
  'sys_coe_moon_chance'  => 'Oy paydo bo`lishiga %1$s%% qoldi<br />',
  'sys_coe_rw_time'      => 'Sahifa ochilishi uchun %1$s soniya qoldi<br />',

  'sys_resources'       => "Resurslar",
  'sys_ships'           => "Kemalar",
  'sys_metal'          => "Metal",
  'sys_metal_sh'       => "M",
  'sys_crystal'        => "Kristal",
  'sys_crystal_sh'     => "K",
  'sys_deuterium'      => "Yoqilg`i",
  'sys_deuterium_sh'   => "Y",
  'sys_energy'         => "Energiya",
  'sys_energy_sh'      => "E",
  'sys_dark_matter'    => "Tyomniy Materiya TM",
  'sys_dark_matter_sh' => "TM",

  'sys_reset'           => "Tashlab yuborish",
  'sys_send'            => "Jo`natish",
  'sys_characters'      => "belgilar",
  'sys_back'            => "Ortga",
  'sys_return'          => "Qaytish",
  'sys_delete'          => "O`chirish",
  'sys_writeMessage'    => "Hat yuborish",
  'sys_hint'            => "Podskazka",

  'sys_alliance'        => "Ittifoqdosh",
  'sys_player'          => "O`yinchi",
  'sys_coordinates'     => "Koordinatalar",

  'sys_online'          => "Onlayn",
  'sys_offline'         => "Offlayn",
  'sys_status'          => "Status",

  'sys_universe'        => "Koinot",
  'sys_goto'            => "Kirish",

  'sys_time'            => "Vaqt",
  'sys_temperature'		=> 'Harorat',

  'sys_no_task'         => "vazifa yo`q",

  'sys_affilates'       => "Taklif qilingan o`yinchilar",

  'sys_fleet_arrived'   => "Flot keldi",

  'sys_planet_type' => array(
    PT_PLANET => 'Sayyora', 
    PT_DEBRIS => 'Siniq parchalari', 
    PT_MOON   => 'OY',
  ),

  'sys_planet_type_sh' => array(
    PT_PLANET => '(S)',
    PT_DEBRIS => '(S)',
    PT_MOON   => '(O)',
  ),

  'sys_capacity' 			=> 'Yuk sig`imi',
  'sys_cargo_bays' 		=> 'Tryumlar',

  'sys_supernova' 		=> 'Sverhnovaya',
  'sys_server' 			=> 'Server',

  'sys_unbanned'			=> 'Berkitilgan',

  'sys_date_time'			=> 'Kun va vaqt',
  'sys_from_person'	   => 'Kimdan',
  'sys_from_speed'	   => 'dan',

  'sys_from'		  => 's',

// Resource page
  'res_planet_production' => 'Sayyorada resurslarni ishlab chiqarish',
  'res_basic_income' => 'tabiiy ishlab chiqarish',
  'res_total' => 'JAMI',
  'res_calculate' => 'Hisoblash',
  'res_hourly' => 'Soatiga',
  'res_daily' => 'Kuniga',
  'res_weekly' => 'Haftasiga',
  'res_monthly' => 'Oyiga',
  'res_storage_fill' => 'Ombor to`lishiga',
  'res_hint' => '<ul><li>Resurslarni ishlab chiqarish uchun <100% energiya zarur. Qo`shimcha energiyazavodlar quring yoki resurs ishlab chiqarish hajmini kamaytiring<li>Agar sizning ishlab chiqarishingiz 0% ga teeng bolsa siz yaqin orada ta`tilga chiqasiz. Qaytgach barcha zavodlarni ishga tushirishingiz kerak bo`ladi<li>Barcha zavodlarni teng ishga tushirish uchun tablitsadagi drup-down ni ishlating.Ta`tilgan qaytgach bu belgi ishlatish osonroq</ul>',

// Build page
  'bld_destroy' => 'Yo`q qilish',
  'bld_create'  => 'Qurish',

// Imperium page
  'imp_imperator' => "Imperator",
  'imp_overview' => "Imperiya ko`rinishi",
  'imp_fleets' => "Parvozdagi flotlar",
  'imp_production' => "Ishlab chiqarish",
  'imp_name' => "Nomi",
  'imp_research' => "Daraja ko`tarish",
  'sys_fields' => "Sektor",

// Cookies
  'err_cookie' => "Hatolik! Siz avtorizatsiya qila olmaysiz.<br />Brauseringizdan kuklarni o`chiring va  qayta kirib ko`ring<a href='login." . PHP_EX . "'>kirish</a> o`yinga yoki <a href='reg." . PHP_EX . "'>ro`yhatdan o`tish</a>.",

// Supported languages
  'ru'              	  => 'ruscha',
  'en'              	  => 'Inglizcha',

  'sys_vacation'        => 'Ahir siz malum vaqtgacha ta`tildasizku',
  'sys_vacation_leave'  => 'Men ahir dam olib bo`ldim endi ta`tildan qaytmoqchiman!',
  'sys_level'           => 'Daraja',
  'sys_level_short'     => 'Dar',

  'sys_yes'             => 'Ha',
  'sys_no'              => 'Yo`q',

  'sys_on'              => 'Yoniq',
  'sys_off'             => 'O`chiq',

  'sys_confirm'         => 'Tasdiqlash',
  'sys_save'            => 'Saqlab qo`yish',
  'sys_create'          => 'Yaratish',
  'sys_write_message'   => 'Hat yuborish',

// top bar
  'top_of_year' => 'yilda',
  'top_online'			=> 'O`yinchilar',

  'sys_first_round_crash_1'	=> 'Hujum qilgan flot bilan aloqa yo`qoldi.',
  'sys_first_round_crash_2'	=> 'Bu urushning birinchi raundidayoq yo`q qilingan.',

  'sys_ques' => array(
    QUE_STRUCTURES => 'Binolar',
    QUE_HANGAR     => 'Verf',
    QUE_RESEARCH   => 'Daraja ko`tarish',
  ),

  'eco_que' => 'Navbat',
  'eco_que_empty' => 'Navbat bosh',
  'eco_que_clear' => 'Navbatni bo`shatish',
  'eco_que_trim'  => 'Ohirgi navbatni bo`shatish',
  'eco_que_artifact'  => 'Использовать Артефакт',

  'sys_cancel' => 'Bekor qilish',

  'sys_overview'			=> 'Ko`rish',
  'mod_marchand'			=> 'Sotuvchi',
  'sys_galaxy'			=> 'Gallaktika',
  'sys_system'			=> 'Sistema',
  'sys_planet'			=> 'Sayyora',
  'sys_planet_title'			=> 'Sayyora turi',
  'sys_planet_title_short'			=> 'Tur',
  'sys_moon'			=> 'OY',
  'sys_error'			=> 'Hatolik',
  'sys_done'				=> 'Tayyor',
  'sys_no_vars'			=> 'Joylashtirilganlar bilan hatolik yuz berdi, Adminstratorga habar bering!',
  'sys_attacker_lostunits'		=> 'Hujumchi %s ta kema yo`qotdi.',
  'sys_defender_lostunits'		=> 'Humoyachi %s ta kema yo`qotdi.',
  'sys_gcdrunits' 			=> 'Endi bu narsalarning koordinatasi %s %s и %s %s.',
  'sys_moonproba' 			=> 'Oy paydo bo`lishi uchun: %d %% ',
  'sys_moonbuilt' 			=> 'Kuchli energiya va metalning ulkan b`olaklari paydo bo`lganligi uchun Oy paydo bo`ldi %s %s!',
  'sys_attack_title'    		=> '%s. Keyingi flot bilan jang bo`lib o`tdi::',
  'sys_attack_attacker_pos'      	=> 'Hujumchi %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> 'Qurollangan: %d %% Щиты: %d %% Броня: %d %% ',
  'sys_attack_defender_pos' 	=> 'Himoyalanuvchi %s [%s:%s:%s]',
  'sys_ship_type' 			=> 'Tur',
  'sys_ship_count' 		=> 'Soni',
  'sys_ship_weapon' 		=> 'Qurolangan',
  'sys_ship_shield' 		=> 'Qalqoni',
  'sys_ship_armour' 		=> 'Zirhi',
  'sys_destroyed' 			=> 'yo`q qilindi',
  'sys_attack_attack_wave' 	=> 'Hujumchi bor kuchi bilan himoya qalqonlariga hujum qildi. Uning qalqonlari %s o`qqa bardosh bera oladi.',
  'sys_attack_defend_wave'		=> 'Himoyachi bor kuchini ishga soldi. Uning qalqonlari %s o`qqa bardosh bera oladi.',
  'sys_attacker_won' 		=> 'Hujumchi jangda g`olib bo`ldi!',
  'sys_defender_won' 		=> 'Himoyachi jangda g`olib bo`ldi!',
  'sys_both_won' 			=> 'Jang durang bilan yakunlandi!',
  'sys_stealed_ressources' 	=> 'U %s metal %s %s kristal %s va %s yoqilg`i oldi.',
  'sys_rapport_build_time' 	=> 'Sahifani ochilishi %s soniya qoldi',
  'sys_mess_tower' 		=> 'Transport',
  'sys_coe_lost_contact' 		=> 'Sizning flotingiz bilan aloqa yo`qoldi',
  'sys_spy_maretials' 		=> 'Hom ashyo',
  'sys_spy_fleet' 			=> 'Flot',
  'sys_spy_defenses' 		=> 'Himoya',
  'sys_mess_qg' 			=> 'Flotlarga buyruq berish',
  'sys_mess_spy_report' 		=> 'Josuslik doklati',
  'sys_mess_spy_lostproba' 	=> 'Sputnikdan qabul qilingan ma`lumot %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Sizning flotingiz yutish imkoniyati %d%%',
  'sys_mess_spy_control' 		=> 'Kontrrazvedka',
  'sys_mess_spy_activity' 		=> 'Josuslik harakati aniqlandi',
  'sys_mess_spy_ennemyfleet' 	=> 'Sayyoradagi boshqa flot',
  'sys_mess_spy_seen_at'		=> 'Sayyodan topildi',
  'sys_mess_spy_destroyed'		=> 'Josuslik bilan shug`ullanayotgan flot yo`q qilindi',
  'sys_mess_spy_destroyed_enemy'		=> 'Fuqorolik josuslik floti yo`q qilindi',
  'sys_object_arrival'		=> 'Sayyoraga tushum',
  'sys_stay_mess_stay' => 'Flotni qoldirish',
  'sys_stay_mess_start' 		=> 'Sizning flotingiz sayyoraga kirdi',
  'sys_stay_mess_back'		=> 'Sizning flotingiz qaytdi ',
  'sys_stay_mess_end'		=> ' va qo`lga kiritdi:',
  'sys_stay_mess_bend'		=> ' va ushbu resurslarni qo`lga kiritdi:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> 'Klonlashtirish',
  'sys_colo_mess_report' 		=> 'Klonlashtirishdagi otchot ',
  'sys_colo_defaultname' 		=> 'Koloniya',
  'sys_colo_arrival' 		=> 'Flot koordinataga yetib keladi ',
  'sys_colo_maxcolo' 		=> ', Klonlashtirib bo`lmaydi. Sababi sizning klonlashtirish darajangiz yetarlicha to`lmadi',
  'sys_colo_allisok' 		=> ', klonlashtiruvchilar yangi sayyorani yaratishga kirishdi.',
  'sys_colo_badpos'  			=> ', klonlashtuiruvchilar imperiyangiz uchun juda kichik bolgan joy topishdi va maqul ko`rishmadi. Ular ortga qaytishmoqda.',
  'sys_colo_notfree' 			=> ', klonlashtiruvchilar bu koordinatada sayyora topa olishmadi. Ular ortga qaytishmoqda.',
  'sys_colo_no_colonizer'     => 'Flotda klonlashtiruvchi yo`q',
  'sys_colo_planet'  		=> ' Sayyora klonlashtirildi!',
  'sys_expe_report' 		=> 'Ekspeditsiya hisoboti',
  'sys_recy_report' 		=> 'Sistema ma`lumoti',
  'sys_expe_blackholl_1' 		=> 'Sizning flotingiz qora tuynukka duch keldi va yo`qoldi!',
  'sys_expe_blackholl_2' 		=> 'Sizning flotingiz qora tuynukka duch keldi va yo`qoldi!',
  'sys_expe_nothing_1' 		=> 'Sizning tadqiqotchilaringiz Sverhnova yulduzi uchayotganini guvohi bo`lishdi! Ular energiya yig`ib olishga muvaffaq bo`lishdi.',
  'sys_expe_nothing_2' 		=> 'Sizning tadqiqotchilaringiz hech nima topa olishmadi!',
  'sys_expe_found_goods' 		=> 'Sizning tadqiqotchilaringiz yahshigina hom ashyo bilan qaytishdi!<br>Siz %s %s, %s %s va %s %s ega bo`ldingiz',
  'sys_expe_found_ships' 		=> 'Sizning tadqiqotchilaringiz yangi flotni topib oldi!<br>Siz ega bo`ldingiz: ',
  'sys_expe_back_home' 		=> 'Sizning flotingiz ortga qaytmoqda.',
  'sys_mess_transport' 		=> 'Transport',
  'sys_tran_mess_owner' 		=> 'Sizning bir flotingiz %s %s sayyorasiga bordi va %s %s, %s  %s va %s %s olib keldi.',
  'sys_tran_mess_user'  		=> 'Sizning flotingiz %s %s planetasidan %s %s va %s %s, %s  %s va %s %s olib keldi.',
  'sys_mess_fleetback' 		=> 'Qaytish',
  'sys_tran_mess_back' 		=> 'Sizning bir flotingiz ushbu sayyoraga qaytmoqda %s %s.',
  'sys_recy_gotten' 		=> 'Sizning bir flotingiz %s %syetib keldi va %s %s sayyoraga qaytmoqda.',
  'sys_notenough_money' 		=> 'Sizda qurish uchun resurslar yetishmayapdi: %s. Hozir sizda: %s %s , %s %s и %s %s. Qurish uchun bo`lsa: %s %s , %s %s и %s %s.',
  'sys_nomore_level'		=> 'Siz bu qurilmani ortiq qura olmaysiz. Sababi u eng yuqori darajaga yetib bo`ldi ( %s ).',
  'sys_buildlist' 			=> 'Qurilmalar spiskasi',
  'sys_buildlist_fail' 		=> 'qurilma yo`q',
  'sys_gain' 			=> 'O`lja: ',
  'sys_debris' 			=> 'Siniqlar to`plami: ',
  'sys_noaccess' 			=> 'Kirish rad etilgan',
  'sys_noalloaw' 			=> 'Bu zona siz uchun yopiq!',
  'sys_governor'        => 'Gubernator',

  // News page & a bit of imperator page
  'news_title'      => 'Yangiliklar',
  'news_none'       => 'yangilik yo`q',
  'news_new'        => 'YANGI',
  'news_future'     => 'ANONS',
  'news_more'       => 'Batafsil...',
  'news_hint'       => 'Ohirgi yangilikni ajratish uchun ularni o`qing"[ Yangiliklar ]"',

  'news_date'       => 'Kun',
  'news_announce'   => 'Jamlanma',
  'news_detail_url' => 'Batafsil yozilgan sahifaga o`tish',
  'news_mass_mail'  => 'Yangilikni barcha o`yinchilarga tarqatish',

  'news_total'      => 'Barcha yangiliklar: ',

  'news_add'        => 'Yangilik qo`shish',
  'news_edit'       => 'Yangilikni tahrirlash',
  'news_copy'       => 'Yangilikni ko`chirib olish',
  'news_mode_new'   => 'Yangi',
  'news_mode_edit'  => 'Tahrirlash',
  'news_mode_copy'  => 'Kopaytirish',

  'sys_administration' => 'Server Adminstratsiyasi',

  // Shortcuts
  'shortcut_title'     => 'Zakladkalar',
  'shortcut_none'      => 'Zakladkalar yo`q',
  'shortcut_new'       => 'YANGI',
  'shortcut_text'      => 'Matn',

  'shortcut_add'       => 'Zakladka qo`shish',
  'shortcut_edit'      => 'Zakladkani tahrirlash',
  'shortcut_copy'      => 'Zakladkani ko`paytirish',
  'shortcut_mode_new'  => 'Yangi',
  'shortcut_mode_edit' => 'Tahrirlash',
  'shortcut_mode_copy' => 'Kopaytirish',

  // Missile-related
  'mip_h_launched'			=> 'Sayyoralararo raketalarni jo`natish',
  'mip_launched'				=> 'Sayyoralararo raketalarni jo`natildi: <b>%s</b>!',

  'mip_no_silo'				=> 'Sayyorada raketalar shahtasi yetishmayapdi <b>%s</b>.',
  'mip_no_impulse'			=> 'Impulsniy dvigatelni darajasini ko`taring.',
  'mip_too_far'				=> 'Raketa bunday uzoqlikka ucholmaydi.',
  'mip_planet_error'			=> 'Hatolik - bir koordinatada bo`lgan bir sayyora',
  'mip_no_rocket'				=> 'Hujumni amalga oshirish uchun raketalar yetishmayapdi.',
  'mip_hack_attempt'			=> ' Sen xakermisan? Yana bir marta shunday qilsang ban olasan. IP adresingni va loginingni men yozib oldim.',

  'mip_all_destroyed' 		=> 'Barcha sayyoralararo raketalar raketa tutuvchilar tomonidan yo`q qilindi<br>',
  'mip_destroyed'				=> '%s ta sayyoralararo raketalar raketa tutuvchilar tomonidan yo`q qilindi.<br>',
  'mip_defense_destroyed'	=> 'Yo`q qilingan himoyalanuvchi qurol:<br />',
  'mip_recycled'				=> 'siniqlar bo`lagidan himoyalanuvchi qurol qayta yig`ildi: ',
  'mip_no_defense'			=> 'Hujum qilingan sayyorada himoyalanuvchi qurol yo`q!',

  'mip_sender_amd'			=> 'Raketa otuvchi koinot qo`shini',
  'mip_subject_amd'			=> 'Raketalar hujumi',
  'mip_body_attack'			=> 'Sayyoralararo raketalar (%1$s ta.) sayyoraga %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> sayyoraga %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',
  
  // Misc
  'sys_game_rules' => 'O`yin qoidalari',
  'sys_max' => 'макс',
  'sys_banned_msg' => 'Siz ban oldingiz. Ma`lumot uchun bu yerga kiring <a href="banned.php">bu yerga</a>. Akkauntingiz blokdan chiqish muddati: ',
  'sys_total_time' => 'Jami vaqt',

  // Universe
  'uni_moon_of_planet' => 'sayyoralar',

  // Combat reports
  'cr_view_title'  => "Jangovor hisobotni ko`rish",
  'cr_view_button' => "Hisobotni ko`rish",
  'cr_view_prompt' => "Kodni tering",
  'cr_view_my'     => "Mening jangovor hisobotlarim",
  'cr_view_hint'   => '<ul><li>O`zingizning jangovor hisobotlaringizni ko`rish uchun "Mening jangovor hisobotlarim" sahifasiga o`ting</li><li>Jangovor hisobotingizning kodi 32 raqamli bo`lib ohirgi qatorda turibdi</li></ul>',

  // Fleet
  'flt_gather_all'    => 'Resurslarni qaytarish',

  // Ban system
  'ban_title'      => 'Qora ro`yhat',
  'ban_name'       => 'ISM',
  'ban_reason'     => 'Bloklanish sababi',
  'ban_from'       => 'Bloklangan kun',
  'ban_to'         => 'Blok muddati',
  'ban_by'         => 'Berildi',
  'ban_no'         => 'Blokdan chiqqan o`yinchilar yo`q',
  'ban_thereare'   => 'Jami',
  'ban_players'    => 'blokdan chiqdi',
  'ban_banned'     => 'Blokdan chiqqan o`yinchilar: ',

  // Contacts
  'ctc_title' => 'Adminstratsiya',
  'ctc_intro' => 'Bu yerda siz adminstrator va operator bilan bog`lanish imkoniga egasiz',
  'ctc_name'  => 'Ism',
  'ctc_rank'  => 'Unvon',
  'ctc_mail'  => 'eMail',

  // Records page
  'rec_title'  => 'Koinotdagi rekordlar',
  'rec_build'  => 'Qurish',
  'rec_specb'  => 'Mahsus qurish',
  'rec_playe'  => 'o`yinchi',
  'rec_defes'  => 'Himoya',
  'rec_fleet'  => 'Flot',
  'rec_techn'  => 'Tehnologiyalar',
  'rec_level'  => 'Daraja',
  'rec_nbre'   => 'Soni',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => 'Internet',
  'cred_site'    => 'Sayt',
  'cred_forum'   => 'Forum',
  'cred_credit'  => 'Mualliflar',
  'cred_creat'   => 'Direktor',
  'cred_prog'    => 'Proglammalashtiruvchi',
  'cred_master'  => 'Olib boruvchi',
  'cred_design'  => 'Dizayner',
  'cred_web'     => 'Vebmaster',
  'cred_thx'     => 'Minnatdorchilik',
  'cred_based'   => 'Xnovani tashkil qilish asosi',
  'cred_start'   => 'XNova birinchi o`rini',

  // Built-in chat
  'chat_common'   => 'Umumiy chat',
  'chat_ally'     => 'Ittifoqdagilar chati',
  'chat_history'  => 'Tarih',
  'chat_message'  => 'Habar',
  'chat_send'     => 'Jo`natish',
  'chat_page'     => 'Sahifa',
  'chat_timeout'  => 'Chat sizning faol bolmaganingiz uchun o`chdi. Sahifani yangilang',

  // ----------------------------------------------------------------------------------------------------------
  // Interface of Jump Gate
  'gate_start_moon' => 'Oyni boshlanishi',
  'gate_dest_moon'  => 'Oyni tugashi',
  'gate_use_gate'   => 'Darvozani ishga tushirish',
  'gate_ship_sel'   => 'Kemalarni belgilash',
  'gate_ship_dispo' => 'ochiq',
  'gate_jump_btn'   => 'Sakrashni amalga oshirish!!',
  'gate_jump_done'  => 'Darvoza qayta ishlatishga tayyorlanmoqda!<br>Darvoza yana shuncha muddatda tayyor bo`ladi: ',
  'gate_wait_dest'  => 'aniq joyga darvozadan o`tishga tayyorlamoqda! U tayyor bo`ladigan vaqt: ',
  'gate_no_dest_g'  => 'Flotni darvoza orqali o`tkazish uchun belgilangan joy aniqlanmadi',
  'gate_no_src_ga'  => 'Flotni o`tkazish uchun darvoza yo`q',
  'gate_wait_star'  => 'Darvoza qayta ishlatishga tayyorlanmoqda!<br>Darvoza yana shuncha muddatda tayyor bo`ladi: ',
  'gate_wait_data'  => 'Hatolik, sakrash uchun ma`lumot yo`q!',
  'gate_vacation'   => 'Hatolik, Ta`til rejimidagi o`yinchining sayyorasiga ota olmaysiz!',
  'gate_ready'      => 'Darvoza sakrash uchun tayyor',

  // quests
  'qst_quests'               => 'Kvestlar',
  'qst_msg_complete_subject' => 'Kvest bajarildi',
  'qst_msg_complete_body'    => 'Siz "%s" kvestini bajargansiz.',
  'qst_msg_your_reward'      => 'Sizning mukofotingiz:',

  // Messages
  'msg_from_admin' => 'Koinot Adminstratsiyasi',
  'msg_class' => array(
    MSG_TYPE_OUTBOX => 'Jo`natilgan habarlar',
    MSG_TYPE_SPY => 'Josuslik hisoboti',
    MSG_TYPE_PLAYER => 'O`yinchilardan habarlar',
    MSG_TYPE_ALLIANCE => 'Ittifoqdagilardan habarlar',
    MSG_TYPE_COMBAT => 'Jang hisobotlari',
    MSG_TYPE_RECYCLE => 'Qayta ishlovchilardan hisobotlar',
    MSG_TYPE_TRANSPORT => 'Flotni joyidan jilishi',
    MSG_TYPE_ADMIN => 'Adminstratordan habarlar',
    MSG_TYPE_EXPLORE => 'Ekspeditsiya hisobotlari',
    MSG_TYPE_QUE => 'Qurilmalar haqida hisobot',
    MSG_TYPE_NEW => 'Barcha habarlar',
  ),

  'msg_que_research_from'    => 'Daraja oshirish uchun ishlovchi institut',
  'msg_que_research_subject' => 'Yangi tehnologiya',
  'msg_que_research_message' => 'Yangi tehnologiya ochildi \'%s\'. Yangi daraja - %d',

  'msg_que_planet_from'    => 'Gubernator',

  'msg_que_hangar_subject' => 'Verfdagi ishlar yakunlandi',
  'msg_que_hangar_message' => "Verf %s ga ishni bajardi",

  'msg_que_built_subject'   => 'Sayyora ishi yakunlandi',
  'msg_que_built_message'   => "Zavod qurilishi yakunlandi '%2\$s' на %1\$s. Qurilgan zavodlar soni: %3\$d",
  'msg_que_destroy_message' => "Zavod yo`q qilinishi yakunlandi '%2\$s' на %1\$s. Yo`q qilingan zavodlar: %3\$d",

  'msg_personal_messages' => 'Shahsiy habar',

  'sys_opt_bash_info'    => 'Antibashing sistemasini sozlash',
  'sys_opt_bash_attacks' => 'Bir hujumda ishtirok etuvchilar soni',
  'sys_opt_bash_interval' => 'To`lqinlar orasidagi tahtaluv',
  'sys_opt_bash_scope' => 'Bashing hisobi ketdi',
  'sys_opt_bash_war_delay' => 'Urush e`lon qilingandan keyin moratoriy',
  'sys_opt_bash_waves' => 'Bir kelishdagi to`lqin',
  'sys_opt_bash_disabled'    => 'Antibashing sistemasi o`chirilgan',

  'sys_id' => 'ID',
  'sys_identifier' => 'Identifikator',

  'sys_email'   => 'E-mail',
  'sys_ip' => 'IP',

  'sys_max' => 'Eng yuq',
  'sys_maximum' => 'Eng yuqorisi',
  'sys_maximum_level' => 'Eng yuqori daraja',

  'sys_user_name' => 'foydalanuvchi ismi',
  'sys_player_name' => 'o`yinchi ismi',
  'sys_user_name_short' => 'ISM',

  'sys_planets' => 'Sayyoralar',
  'sys_moons' => 'Oy',

  'sys_no_governor' => 'Gubernatorlar yollash',

  'sys_quantity' => 'Soni',
  'sys_quantity_maximum' => 'Eng yuqori son',
  'sys_qty' => 'Soni',

  'sys_buy_for' => 'Sotib olish',
  'sys_buy' => 'Sotib olish',
  'sys_for' => 'ga',

  'sys_eco_lack_dark_matter' => 'TM yetishmayapdi',

  'sys_result' => array(
    'error_dark_matter_not_enough' => 'Operatsiyani amalga oshirish uchun TM yetishmayapdi',
    'error_dark_matter_change' => 'TM o`zgarishida hatolik yuz berdi! Operatsiyani qayta bajaring. Agar hatolik yana yuz bersa adminstratorga habar bering',
  ),

  // Arrays
  'sys_build_result' => array(
    BUILD_ALLOWED => 'Qurish mumkin',
    BUILD_REQUIRE_NOT_MEET => 'Ta`lab qilinadi',
    BUILD_AMOUNT_WRONG => 'O`ta ko`p',
    BUILD_QUE_WRONG => 'Bor bo`lmagan navbat',
    BUILD_QUE_UNIT_WRONG => 'Noto`g`ri navbat',
    BUILD_INDESTRUCTABLE => 'Yo`q qilib bo`lmaydi',
    BUILD_NO_RESOURCES => 'Resurslar yetishmayapdi',
    BUILD_NO_UNITS => 'Kemalar yo`q',
    BUILD_UNIT_BUSY => array(
      0 => 'Строение занято',
      STRUC_LABORATORY => 'Идет исследование',
      STRUC_LABORATORY_NANO => 'Идет исследование',
    ),
  ),

  'sys_game_mode' => array(
    GAME_SUPERNOVA => 'Sverhnova',
    GAME_OGAME     => 'O`yin haqida',
  ),

  'months' => array(
    '01'=>'Yanvar',
    '02'=>'Fevral',
    '03'=>'Mart',
    '04'=>'Aprel',
    '05'=>'May',
    '06'=>'Iyun',
    '07'=>'Iyul',
    '08'=>'Avgust',
    '09'=>'Sentyabr',
    '10'=>'Oktyabr',
    '11'=>'Noyabr',
    '12'=>'Dekabr'
  ),

  'weekdays' => array(
    0 => 'Yakshanba',
    1 => 'Dushanba',
    2 => 'Seshanba',
    3 => 'Chorshanba',
    4 => 'Payshanba',
    5 => 'Muborak kun Juma',
    6 => 'Shanba'
  ),

  'user_level' => array(
    0 => 'O`yinchi',
    1 => 'Moderator',
    2 => 'Operator',
    3 => 'Administrator',
  ),

  'user_level_shortcut' => array(
    0 => 'O',
    1 => 'M',
    2 => 'O',
    3 => 'A',
  ),

  'sys_lessThen15min'   => '&lt; 15 daq',

  'sys_no_points'         => 'Sizga TM yetishmayapdi!',
  'sys_dark_matter_desc' => 'Tyomniy materiya - energiya qancha yuqori bo`lsa uni olish ham shuncha oson boladi.',
  'sys_dark_matter_hint' => 'Bu substansiyaga ofitserlar va kamandirlar yollash mumkin.',

  'sys_msg_err_update_dm' => 'TM o`zgarishishida hatolik yuz berdi!',

  'sys_na' => 'Yopiq',
  'sys_na_short' => 'N/D',

  'sys_ali_res_title' => 'Itttifoq resurslari',

  'sys_bonus' => 'Bonus',

  'sys_of_ally' => 'Ittifoq',

  'sys_hint_player_name' => 'Oyinchilarni identifikatori bilan yoki nomi bilan izlash mumkin. Agar o`yinchining ismida o`qilmaydigan belgilar bo`lsa izlashni ID bilan amalga oshiring',
  'sys_hint_ally_name' => 'Ittifoqni identifikator, teglari va nomi bilan izlash mumkin. Agar ittifoqning teglari yoki ittifoq nomida o`qilmaydigan belgilar bo`lsa izlashni ID bilan amalga oshiring',

  'sys_fleet_and' => '+ kemalar',

  'sys_on_planet' => 'Sayyorada',
  'fl_on_stores' => 'Omborda',

  'sys_ali_bonus_members' => 'Bonusni qo`lga kiritish uchun Ittifoqqa kerak boladigan hajm',

  'sys_premium' => 'Premium',

  'mrc_period_list' => array(
    PERIOD_MINUTE    => '1 daqiqa',
    PERIOD_MINUTE_3  => '3 daqiqa',
    PERIOD_MINUTE_5  => '5 daqiqa',
    PERIOD_MINUTE_10 => '10 daqiqa',
    PERIOD_DAY       => '1 kun',
    PERIOD_DAY_3     => '3 kun',
    PERIOD_WEEK      => '1 hafta',
    PERIOD_WEEK_2    => '2 hafta',
    PERIOD_MONTH     => '30 kun',
    PERIOD_MONTH_2   => '60 kun',
    PERIOD_MONTH_3   => '90 kun',
  ),

  'sys_sector_buy' => '1 sektor sotib olish',

  'sys_select_confirm' => 'Tanlanganni tasdiqlash ',

  'sys_capital' => 'Poytaxt',

  'sys_result_operation' => 'Habar',

  'sys_password' => 'Parol',
  'sys_password_length' => 'Parol uzunligi',
  'sys_password_seed' => 'Ishlatish mumkin bo`lgan belgilar',

  'sys_msg_ube_report_err_not_found' => 'Harbiy hisobot topilmadi. Klyuch to`g`riligini tekshiring. Balki hisobotga ancha bo`lgani sabab o`chirilgan bolishi mumkin',

  'sys_mess_attack_report' 	=> 'Harbiy hisobot',
  'sys_perte_attaquant' 		=> 'Hujumchi yo`qoldi',
  'sys_perte_defenseur' 		=> 'Himoyachi yo`qoldi',



  'ube_report_info_main' => 'Jang haqida qo`shimcha ma`lumot',
  'ube_report_info_date' => 'Kun va vaqti',
  'ube_report_info_location' => 'Joyi',
  'ube_report_info_rounds_number' => 'Poundlar soni',
  'ube_report_info_outcome' => 'Jang natijasi',
  'ube_report_info_outcome_win' => 'Hujumchi jangda g`olib bo`ldi',
  'ube_report_info_outcome_loss' => 'Hujumchi jangda mag`lub bo`ldi',
  'ube_report_info_outcome_draw' => 'Jang durrang bilan yakunlandi',
  'ube_report_info_link' => 'Harbiy hisobot sahifasi',
  'ube_report_info_sfr' => 'Jang birinchi raunddayoq hujumchining mag`lub bo`lishi bilan yakunlandi<br />PMF dan bo`lishi mumkin',
  'ube_report_info_debris' => 'Orbitadagi siniqlar to`plami',
  'ube_report_info_loot' => 'O`lja',
  'ube_report_info_loss' => 'Harbiy yo`qotish',
  'ube_report_info_generate' => 'Sahifani yangilanish vaqti',

  'ube_report_moon_was' => 'Bu sayyorada oy bor',
  'ube_report_moon_chance' => 'Oy paydo bo`lishiga imkoniyat',
  'ube_report_moon_created' => 'Sayyora orbitasida oy paydo boldi. Uning diametri',

  'ube_report_moon_reapers_none' => 'Tortishish dvigatelli kemalarning barchasi jangda yo`q bo`ldi.',
  'ube_report_moon_reapers_wave' => 'Hujum qilayotgan kemalar tortishuvchi to`lqin hosil qildi',
  'ube_report_moon_reapers_chance' => 'Oyni yo`q qilishga imkoniyat',
  'ube_report_moon_reapers_success' => 'Oy yo`q qilindi',
  'ube_report_moon_reapers_failure' => 'To`lqinning kuchi oyni yo`q qilishga yetmadi',

  'ube_report_moon_reapers_outcome' => 'Dvigatellarning portlashiga imkoniyat',
  'ube_report_moon_reapers_survive' => 'Tortishish dvigatelli kemalar oyni yo`q qilishga yetmadi',
  'ube_report_moon_reapers_died' => 'Kompensatsiya to`lamang bois flot yo`q qilindi',

  'ube_report_side_attacker' => 'Hujumchi',
  'ube_report_side_defender' => 'Himoyachi',

  'ube_report_round' => 'Round',
  'ube_report_unit' => 'Harbiy qo`shin',
  'ube_report_attack' => 'Hujum',
  'ube_report_shields' => 'Qalqonlar',
  'ube_report_shields_passed' => 'Eshik halqasi',
  'ube_report_armor' => 'Zirh',
  'ube_report_damage' => 'Kuch',
  'ube_report_loss' => 'Yo`qotishlar',


  'ube_report_info_restored' => 'Himoyalanuvchi qurollar tiklandi',
  'ube_report_info_loss_final' => 'Jami harbiy yo`qotishlar',
  'ube_report_info_loss_resources' => 'Jami yo`qotilgan resurslar',
  'ube_report_info_loss_dropped' => 'Tryumning kuchiklashgani sabab yo`qotilgan resurslar',
  'ube_report_info_loot_lost' => 'Sayyora omboridan olib ketilgan resurslar',
  'ube_report_info_loss_gained' => 'Chaqiruv orqali yo`qotilgan resurslar',
  'ube_report_info_loss_in_metal' => 'Jami yo`qotishni metalda hisoblash',


  'ube_report_msg_body_common' => 'Jang %s orbitasida %s [%d:%d:%d] %s<br />%s<br /><br />',
  'ube_report_msg_body_debris' => 'Jangdan so`ng orbitada hosil bolgan siniqlar to`plami:<br />',
  'ube_report_msg_body_sfr' => 'Flot bilan aloqa yo`qoldi',

  'sys_kilometers_short' => 'km',

  'ube_simulation' => 'Simulyatsiya',

  'sys_hire_do' => 'Yollash',

  'sys_captains' => 'Kapitanlar',

  'sys_fleet_composition' => 'Flot tarkibi',

  'sys_continue' => 'Продолжить',

  'uni_planet_density_types' => array(
    PLANET_DENSITY_NONE => 'Не бывает',
    PLANET_DENSITY_ICE_WATER => 'Лёд',
    PLANET_DENSITY_SILICATE => 'Силикат',
    PLANET_DENSITY_STONE => 'Камень',
    PLANET_DENSITY_STANDARD => 'Стандарт',
    PLANET_DENSITY_METAL_ORE => 'Руда',
    PLANET_DENSITY_METAL_PRILL => 'Металл',
    PLANET_DENSITY_METAL_HEAVY => 'Тяжелый металл',
  ),

  'sys_planet_density' => 'Плотность',
  'sys_planet_density_units' => 'кг/м&sup3;',
  'sys_planet_density_core' => 'Тип ядра',

  'sys_change' => 'Изменить',
  'sys_show' => 'Показать',
  'sys_hide' => 'Скрыть',

));
