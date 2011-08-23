<?php
/*
#############################################################################
#  Filename: admin.mo
#  Create date: Wednesday, April 02, 2008	 19:18:25
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright c 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright c 2005 - 2008 KGsystem
#  Copyright (c) 2009 Gorlum
#############################################################################
*/

if(!defined('INSIDE'))
{
  die("Обнаружена попытка взлома!");
}

$lang['adm_err_denied']         = 'Доступ запрещен';

$lang['adm_done']               = "Успешно выполнено";
$lang['adm_inactive_removed']   = '<li>Удалено неактивных записей игроков: %d</li>';
$lang['adm_stat_title']         = "Обновление статистики";
$lang['adm_maintenance_title']  = "Обслуживание БД";
$lang['adm_records']            = "записей обработано";
$lang['adm_cleaner_title']      = "Чистка очереди построек";
$lang['adm_cleaned']            = "Кол-во удаленных задач: ";

$lang['adm_schedule_none']      = "В расписании нет задач на сейчас";

$lang['Fix']                    = "Обновлено";
$lang['Welcome_to_Fix_section'] = "секция патчей";
$lang['There_is_not_need_fix']  = "Фикс ненужен!";
$lang['Fix_welldone']           = "Сделано!";

$lang['adm_ov_title'] = "Обзор";
$lang['adm_ov_infos'] = "Информация";
$lang['adm_ov_yourv'] = "Текущая версия";
$lang['adm_ov_lastv'] = "Доступная версия";
$lang['adm_ov_here']  = "здесь";
$lang['adm_ov_onlin'] = "Онлайн";
$lang['adm_ov_ally']  = "Альянс";
$lang['adm_ov_point'] = "Очки";
$lang['adm_ov_activ'] = "Активен";
$lang['adm_ov_count'] = "Онлайн игроки";
$lang['adm_ov_wrtpm'] = "Написать в личку";
$lang['adm_ov_altpm'] = "[ЛП]";

$lang['adm_ul_title'] = "Список игроков";
$lang['adm_ul_ttle2'] = "Players listed";
$lang['adm_ul_id']    = "ID";
$lang['adm_ul_name']  = "Имя игрока";
$lang['adm_ul_mail']  = "E-mail";
$lang['adm_ul_adip']  = "IP";
$lang['adm_ul_regd']  = "Registred from";
$lang['adm_ul_lconn'] = "Последний логин";
$lang['adm_ul_bana']  = "Ban";
$lang['adm_ul_detai'] = "Детали";
$lang['adm_ul_actio'] = "Действия";
$lang['adm_ul_playe'] = " игроков";
$lang['adm_ul_yes']   = "Да";
$lang['adm_ul_no']    = "Нет";

$lang['adm_pl_title'] = "Активные планеты";
$lang['adm_pl_activ'] = "Активные планеты";
$lang['adm_pl_name']  = "Имя планеты";
$lang['adm_pl_posit'] = "Координаты";
$lang['adm_pl_point'] = "Значение";
$lang['adm_pl_since'] = "Активна";
$lang['adm_pl_they']  = "Всего";
$lang['adm_pl_apla']  = "планет(а/ы)";

$lang['adm_am_plid']  = "ID планеты";
$lang['adm_am_done']  = "Добавление прошло успешно";
$lang['adm_am_ttle']  = "Добавить ресурсы";
$lang['adm_am_add']   = "Подтвердить";
$lang['adm_am_form']  = "Форма добавления ресурсов";

$lang['adm_ban_title']  = "Забанить игрока";
$lang['adm_bn_plto']  = "Забанить игрока";
$lang['adm_bn_name']  = "Имя игрока";
$lang['adm_bn_reas']  = "Причина бана";
$lang['adm_bn_isvc']  = "С режимом отпуска";
$lang['adm_bn_time']  = "Длительность бана";
$lang['adm_bn_days']  = "Дни";
$lang['adm_bn_hour']  = "Часы";
$lang['adm_bn_mins']  = "Минуты";
$lang['adm_bn_secs']  = "Секунды";
$lang['adm_bn_bnbt']  = "Забанить";
$lang['adm_bn_thpl']  = "Игрок";
$lang['adm_bn_isbn']  = "успешно заблокирован!";
$lang['adm_bn_vctn']  = " Включен режим отпуска.";
$lang['adm_bn_errr']  = "Ошибка блокировки игрока! Возможно ник %s не найден.";
$lang['adm_bn_err2']  = "Ошибка отключения производства на планетах!";
$lang['adm_bn_plnt']  = "Производство на планетах отключено.";

$lang['adm_unbn_ttle']  = "Анбан";
$lang['adm_unbn_plto']  = "Разбанить игрока";
$lang['adm_unbn_name']  = "Имя";
$lang['adm_unbn_bnbt']  = "Разбанить";
$lang['adm_unbn_thpl']  = "Игрок";
$lang['adm_unbn_isbn']  = "разбанен!";

$lang['adm_rz_ttle']  = "Обнуление вселенной";
$lang['adm_rz_done']  = "User(s) of transfer(s)";
$lang['adm_rz_conf']  = "Подтверждение";
$lang['adm_rz_text']  = "Нажимая кнопку (обнулить) вы уничтожите все данные базы. Вы сделали резервную копию??? Аккаунты удалены не будут...";
$lang['adm_rz_doit']  = "Обнулить";

$lang['adm_ch_ttle']  = "Администрирование чата";
$lang['adm_ch_list']  = "Список сообщений";
$lang['adm_ch_clear'] = "Очистить";
$lang['adm_ch_idmsg'] = "ID";
$lang['adm_ch_delet'] = "удалить";
$lang['adm_ch_play']  = "Игрок";
$lang['adm_ch_time']  = "Дата";
$lang['adm_ch_chat']  = "Реплика";
$lang['adm_ch_nbs']   = "сообщений всего...";

$lang['adm_er_ttle']  = "Ошибки";
$lang['adm_er_list']  = "Ошибки полученные в игре";
$lang['adm_er_clear'] = "Очистить";
$lang['adm_er_idmsg'] = "ID";
$lang['adm_er_type']  = "Тип";
$lang['adm_er_play']  = "id Игрока";
$lang['adm_er_time']  = "Дата";
$lang['adm_er_page']  = "Адрес страницы";
$lang['adm_er_nbs']   = "ошибок всего...";
$lang['adm_er_text']  = "Текст ошибки";
$lang['adm_er_bktr']  = "Отладочная информация";

$lang['adm_dm_title'] = "Изменение количества Темной Материи";
$lang['adm_dm_planet'] = "ID, координаты или название планеты";
$lang['adm_dm_oruser'] = "ИЛИ";
$lang['adm_dm_user'] = "ID или имя пользователя";
$lang['adm_dm_no_quant'] = 'Укажите количество ТМ (положительное - для начисления, отрицательное - для снятия)';
$lang['adm_dm_no_dest'] = 'Укажите пользователя или планету для изменения ТМ';
$lang['adm_dm_add_err'] = 'Похоже во время начисления ТМ произошла ошибка.';
$lang['adm_dm_user_none'] = 'Ошибка при поиске пользователя: не найдено пользователя с ID или именем %s';
$lang['adm_dm_user_added'] = 'Количество ТМ у пользователя [%s] (ID: %d) успешно изменено на %d ТМ.';
$lang['adm_dm_user_conflict'] = 'Ошибка при поиске пользователя: похоже, в БД есть пользователь и с таким именем, и с таким ID';

$lang['adm_dm_planet_none'] = 'Ошибка при поиске планеты: не найдено планеты с ID, координатами или именем %s';
$lang['adm_dm_planet_added'] = 'Количество ТМ у пользователя ID %1$d (владельца планеты %4$s %2$s ID %3$d) успешно изменено на %5$d ТМ.';
$lang['adm_dm_planet_conflict'] = 'Неуникальные данные для поиска планеты.<br>Это означает, что в БД одновременно существует ';
$lang['adm_dm_planet_conflict_id'] = 'планета с именем "%1$s" и планета с ID %1$s .<br>Попробуйте использовать координаты планеты.';
$lang['adm_dm_planet_conflict_name'] = 'несколько планет с именем "%1$s".<br>Попробуйте использовать координаты или ID планеты.';
$lang['adm_dm_planet_conflict_coords'] = 'планета с именем "%1$s" и планета с координатами %1$s.<br>Попробуйте использовать ID планеты.';

$lang['adm_apply'] = "Применить";
$lang['adm_maint']    = "Обслуживание";
$lang['adm_backup']   = "Резервная копия";

$lang['adm_tools']   = "Утилиты";
$lang['adm_tools_reloadConfig'] = 'Пересчитать конфигурацию';

$lang['adm_reason']  = "Причина";

$lang = array_merge($lang, array(
// Server settings page
  'adm_opt_title'             => "Настройки Вселенной",
  'adm_opt_game_settings'     => "Параметры Вселенной",
  'adm_opt_game_name'         => "Название Вселенной",
  
  'adm_opt_speed'             => "Скорость",
  'adm_opt_game_gspeed'       => "Игры",
  'adm_opt_game_fspeed'       => "Флота",
  'adm_opt_game_pspeed'       => "Добычи ресурсов",

  'adm_opt_main_not_counted'  => "(не считая главную планету)",
  'adm_opt_game_speed_normal' => "(1&nbsp;-&nbsp;нормальная)",
  'adm_opt_game_faq'          => "Ссылка на ЧаВо",
  'adm_opt_game_forum'        => "Адрес форума",
  'adm_opt_game_dark_matter'  => "Ссылка &quot;Добыть ТМ&quot;",
  'adm_opt_game_copyrigh'     => "Copyright",
  'adm_opt_game_online'       => "Отключить игру. Пользователи увидят следующее сообщение:",
  'adm_opt_game_offreaso'     => "Сообщение",
  'adm_opt_plan_settings'     => "Параметры планет",
  'adm_opt_plan_initial'      => "Размер главной планеты",
  'adm_opt_plan_base_inc'     => "Базовая добыча",
  'adm_opt_game_debugmod'     => "Включить режим отладки",
  'adm_opt_game_counter'      => "Включить счетчик посещений",
  'adm_opt_game_oth_info'     => "Прочие параметры",
  'adm_opt_int_news_count'    => "Количество новостей",
  'adm_opt_int_page_imperor'  => 'На странице &quot;Император&quot;:',
  'adm_opt_game_zero_dsiable' => "(0&nbsp;-&nbsp;отключить)",

  'adm_opt_game_advertise'    => "Рекламные блоки",
  'adm_opt_game_oth_adds'     => "Включить рекламный блок в левом меню. Код баннера:",

  'adm_opt_game_oth_gala'     => "Галактика",
  'adm_opt_game_oth_syst'     => "Система",
  'adm_opt_game_oth_plan'     => "Планета",
  'adm_opt_btn_save'          => "Сохранить",
  'adm_opt_vacation_mode'     => "Отключить режим отпуска",
  'adm_opt_sectors'           => "секторов",
  'adm_opt_per_hour'          => "в час",
  'adm_opt_saved'             => "Настройки игры сохранены успешно",
  'adm_opt_players_online'    => "Игроков на сервере",
  'adm_opt_vacation_mode_is'  => "Режим отпуска",
  'adm_opt_maintenance'       => "Обслуживание сервера",
  'adm_opt_links'             => "Ссылки и баннеры",

  'adm_opt_universe_size'     => "Размер Вселенной",
  'adm_opt_galaxies'          => "Галактик",
  'adm_opt_systems'           => "Систем",
  'adm_opt_planets'           => "Планет",
  'adm_opt_build_on_research' => "Строить лабораторию во время исследования",
  'adm_opt_game_rules'        => "Ссылка на правила",
  'adm_opt_max_colonies'      => "Количество колоний",
  'adm_opt_exchange'          => "Курс обмена ресурсов",
  'adm_opt_game_mode'         => "Тип Вселенной",

  'adm_opt_chat'              => "Настройки чата",
  'adm_opt_chat_timeout'      => "Таймаут по неактивности",

  'adm_opt_allow_buffing'     => 'Разрешить прокачку',
  'adm_opt_ally_help_weak'    => 'Разрешить удержание на слабом соаловце',
  'adm_opt_email_pm'          => 'Разрешить пересылку ЛС на e-mail',

  'adm_opt_game_defaults'         => "Настройки игрока по умолчанию",
  'adm_opt_game_default_language' => "Язык интерфейса",
  'adm_opt_game_default_skin'     => "Оформление/Шкурка",
  'adm_opt_game_default_template' => "Шаблон",

  'adm_lm_compensate'        => "Компенсировать",

// Planet compensate page
  'adm_pl_comp_title'   => 'Компенсация уничтоженной планеты',
  'adm_pl_comp_src'     => 'Уничтожить планету',
  'adm_pl_comp_dst'     => 'Зачислить ресурсы на планету',
  'adm_pl_comp_bonus'   => 'Бонус игрока',
  'adm_pl_comp_check'   => 'Проверить',
  'adm_pl_comp_confirm' => 'Подтвердить',
  'adm_pl_comp_done'    => 'Готово',

  'adm_pl_comp_price'   => 'Стоимость построек',
  'adm_pl_comp_got'     => 'Будет зачислено',

  'adm_pl_com_of_plr'   => 'игрока',
  'adm_pl_comp_will_be' => 'будет',
  'adm_pl_comp_destr'   => 'уничтожена.',
  'adm_pl_comp_recieve' => 'Указанное количество ресурсов',
  'adm_pl_comp_recieve2' => 'зачислено на планету',

  'adm_pl_comp_err_0' => 'Не найдена уничтожаемая планета',
  'adm_pl_comp_err_1' => 'Планета уже уничтожена',
  'adm_pl_comp_err_2' => 'Не найдена планета, на которую нужно зачислить ресурсы',
  'adm_pl_comp_err_3' => 'У указанных планет разные владельцы. Зачислить ресурсы можно только на планету того же игрока',
  'adm_pl_comp_err_4' => 'Планета не пренадлежит указанному игроку',
  'adm_pl_comp_err_5' => 'Планеты для уничтжения и для зачисления ресурсов совпадают',

  'adm_ver_versions'  => 'Версии компонентов сервера',
  'adm_ver_version_sn'=> 'Версия движка',
  'adm_ver_version_db'=> 'Версии базы данных',

  'adm_update_force'  => 'Форсировать обновление с нуля',
  'adm_update_repeat' => 'Повторить предыдущее обновление',

  'adm_lm_planet_edit' => 'Редактировать',

  'adm_planet_edit'   => 'Редактирование планеты',
  'adm_planet_id'     => 'Идентификатор планеты',
  'adm_name' => 'Название',
  'adm_on_planet' => 'На планете',
  'adm_planet_change' => 'Изменение',

  'adm_planet_parent' => 'Родительская планета',
  'adm_planet_active' => 'Активные планеты',

  'adm_planet_edit_hint' => '<ul>
    <li>Если на пустой странице ввести идентификатор планеты и нажать кнопку "Подтвердить" движок попытается вывести информацию по планете с таким ИД: тип, название и координаты,
    а так же текущее количество юнитов/ресурсов выбранного типа на планете</li>
    <li>Что бы убрать с планеты определенное количество юнитов/ресурсов нужно вводить отрицательное число</li>
  </ul>',

  'adm_planet_list_title' => 'Список планет',
  'adm_sys_owner' => 'Владелец',
  'adm_sys_owner_id' => 'ИД владельца',
));

// Add moon
$lang['addm_title']    = "Добавить луну";
$lang['addm_addform']  = "Формуляр новой луны";
$lang['addm_playerid'] = "ID планеты размещения";
$lang['addm_moonname'] = "Название луны";
$lang['addm_moongala'] = "Укажите галактику";
$lang['addm_moonsyst'] = "Укажите систему";
$lang['addm_moonplan'] = "Укажите позицию";
$lang['addm_moondoit'] = "Добавить";
$lang['addm_done']     = "Луна создана";


//Admin panel
$lang['adm_usr_level'][0] = "Игрок";
$lang['adm_usr_level'][1] = "Оператор";
$lang['adm_usr_level'][2] = "Модератор";
$lang['adm_usr_level'][3] = "Администратор";
$lang['adm_usr_genre']['M'] = "Мужчина";
$lang['adm_usr_genre']['F'] = "Женщина";

// Admin Strings
$lang['panel_mainttl'] = "Панель администратора";
// Admin Panel A Template 1
$lang['adm_panel_mnu'] = "Поиск игрока";
$lang['adm_panel_ttl'] = "Вид поиска";
$lang['adm_search_pl'] = "Поиск по имени";
$lang['adm_search_ip'] = "Поиск по IP";
$lang['adm_stat_play'] = "Статистика игрока";
$lang['adm_mod_level'] = "Уровень доступа";

$lang['adm_player_nm'] = "Имя игрока";
$lang['adm_ip']        = "IP";
$lang['adm_plyer_wip'] = "Игроки с IP";
$lang['adm_frm1_id']   = "ID";
$lang['adm_frm1_name'] = "Имя";
$lang['adm_frm1_ip']   = "IP";
$lang['adm_frm1_mail'] = "e-Mail";
$lang['adm_frm1_acc']  = "Звание";
$lang['adm_frm1_gen']  = "Пол";
$lang['adm_frm1_main'] = "ID планеты";
$lang['adm_frm1_gpos'] = "Координаты";
$lang['adm_mess_lvl1'] = "Уровень доступа";
$lang['adm_mess_lvl2'] = "&quot;теперь&quot; ";
$lang['adm_colony']    = "Колонии";
$lang['adm_planet']    = "Планета";
$lang['adm_moon']      = "Луна";
$lang['adm_technos']   = "Технологии";
$lang['adm_bt_search'] = "Искать";
$lang['adm_bt_change'] = "Изменить";

// Admin fleet
$lang['flt_id']       = "ID";
$lang['flt_fleet']    = "Флот";
$lang['flt_mission']  = "Задание";
$lang['flt_owner']    = "Отправление";
$lang['flt_planet']   = "Планета";
$lang['flt_time_st']  = "Время отправления";
$lang['flt_e_owner']  = "Прибытие";
$lang['flt_time_en']  = "Время прибытия";
$lang['flt_staying']  = "Stat.";
$lang['flt_action']   = "Действие";
$lang['flt_title']    = "Флоты в полёте";

// MD5
$lang['adm_md5']    = "MD5-хэш";
$lang['md5_title']  = "Утилита шифрования";
$lang['md5_pswcyp'] = "Шифрование пароля";
$lang['md5_psw']    = "Пароль";
$lang['md5_pswenc'] = "Зашифрованный пароль";
$lang['md5_doit']   = "[ зашифровать ]";

// Message list
$lang['mlst_title']       = "Список сообщений";
$lang['mlst_mess_del']    = "Удаление сообщений";
$lang['mlst_hdr_page']    = "Стр.";
$lang['mlst_hdr_title']   = " ) сообщений :";
$lang['mlst_hdr_prev']    = "[ &lt;- ]";
$lang['mlst_hdr_next']    = "[ -&gt; ]";
$lang['mlst_hdr_id']      = "ID";
$lang['mlst_hdr_type']    = "Type";
$lang['mlst_hdr_time']    = "Heure";
$lang['mlst_hdr_from']    = "От";
$lang['mlst_hdr_to']      = "Кому";
$lang['mlst_hdr_text']    = "Contenu";
$lang['mlst_hdr_action']  = "Отм.";
$lang['mlst_del_mess']    = "Удалить";
$lang['mlst_bt_delsel']   = "Удалить выделенные";
$lang['mlst_bt_deldate']  = "Удалить сообщения даты";
$lang['mlst_hdr_delfrom'] = "Удалить с даты";
$lang['mlst_mess_typ__0'] = "Шпионаж";
$lang['mlst_mess_typ__1'] = "Игроков";
$lang['mlst_mess_typ__2'] = "Альянсов";
$lang['mlst_mess_typ__3'] = "Боёв";
$lang['mlst_mess_typ__4'] = "Эксплуатац.";
$lang['mlst_mess_typ__5'] = "Трансорт";
$lang['mlst_mess_typ_15'] = "Экспедиции";
$lang['mlst_mess_typ_99'] = "Список Batiment";

?>
