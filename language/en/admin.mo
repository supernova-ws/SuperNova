<?php
/*
#############################################################################
#  Filename: admin.mo
#  Create date: Wednesday, April 02, 2008	 19:18:25
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#  Copyright (c) 2009 Gorlum
#############################################################################
*/
if (!defined('INSIDE')) {
	die("attemp hacking");
}
$lang['adm_done']               = "Work complete";
$lang['adm_done_records']       = 'Work complete. %2$d record(s) processed in %1$01.3f seconds.';
$lang['adm_stat_title']         = "Обновление статистики";
$lang['adm_maintenance_title']  = "Database maintenance";
$lang['adm_cleaner_title']      = "Cтатистика";
$lang['adm_cleaned']            = "Кол-во удаленных задач: ";

$lang['Fix']                    = "Обновлено";
$lang['Welcome_to_Fix_section'] = "секция патчей";
$lang['There_is_not_need_fix']  = "Фикс ненужен!";
$lang['Fix_welldone']           = "Сделано!";

$lang['adm_ov_title'] = "Обзор";
$lang['adm_ov_infos'] = "Информация";
$lang['adm_ov_yourv'] = "Версия";
$lang['adm_ov_lastv'] = "Доступная версия";
$lang['adm_ov_here']  = "здесь";
$lang['adm_ov_onlin'] = "Онлайн";
$lang['adm_ov_ally']  = "Альянс";
$lang['adm_ov_point'] = "Очков";
$lang['adm_ov_activ'] = "Активность";
$lang['adm_ov_count'] = " Игроков онлайн";
$lang['adm_ov_wrtpm'] = "Написать в Личку";
$lang['adm_ov_altpm'] = "[PM]";


$lang['adm_ul_title'] = "Список игроков";
$lang['adm_ul_ttle2'] = "Players listed";
$lang['adm_ul_id']    = "ID";
$lang['adm_ul_name']  = "Имя ";
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

$lang['adm_pl_title'] = "Активность на планетах";
$lang['adm_pl_activ'] = "Активность планет";
$lang['adm_pl_name']  = "Имя планеты";
$lang['adm_pl_posit'] = "Позиция";
$lang['adm_pl_point'] = "Значение";
$lang['adm_pl_since'] = "Последний клик";
$lang['adm_pl_they']  = "Всего";
$lang['adm_pl_apla']  = "планет активно";

$lang['adm_an_title']     = "Announces";
$lang['adm_an_date']      = "Date";
$lang['adm_an_announce']  = "Announce";
$lang['adm_an_total']     = "Announces total: ";
$lang['adm_an_add']       = "Add announce";
$lang['adm_an_edit']      = "Edit announce";
$lang['adm_an_mode_new']  = "New";
$lang['adm_an_mode_edit'] = "Editing";
$lang['adm_an_mode_dupe'] = "Duplicate";

$lang['adm_am_plid']  = "ID планеты";
$lang['adm_am_done']  = "Добавление прошло успешно";
$lang['adm_am_ttle']  = "Добавить ресурсы";
$lang['adm_am_add']   = "Добавить";
$lang['adm_am_form']  = "Форма добавления ресурсов";

$lang['adm_bn_ttle']  = "Банлист";
$lang['adm_bn_plto']  = "Забанить игрока";
$lang['adm_bn_name']  = "Имя";
$lang['adm_bn_reas']  = "Причина";
$lang['adm_bn_isvc']  = "With vacation mode";
$lang['adm_bn_time']  = "Длительность";
$lang['adm_bn_days']  = "Дней";
$lang['adm_bn_hour']  = "Часов";
$lang['adm_bn_mins']  = "Минут";
$lang['adm_bn_secs']  = "Секунд";
$lang['adm_bn_bnbt']  = "Забанить";
$lang['adm_bn_thpl']  = "Игрок";
$lang['adm_bn_isbn']  = "забанен!";
$lang['adm_bn_vctn']  = " Vacation mode is on.";
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
$lang['adm_rz_text']  = "Нажимая кнопку \'обнулить\' вы уничтожите все данные базы. Вы сделали резервную копию??? Аккаунты удалены не будут...";
$lang['adm_rz_doit']  = "Обнулить";

$lang['adm_ch_ttle']  = "Администрирование чата";
$lang['adm_ch_list']  = "Список сообщений";
$lang['adm_ch_clear'] = "Очистить";
$lang['adm_ch_idmsg'] = "ID";
$lang['adm_ch_delet'] = "удалить";
$lang['adm_ch_play']  = "Игрок";
$lang['adm_ch_time']  = "Дата";
$lang['adm_ch_chat']  = "Reply";
$lang['adm_ch_nbs']   = "сообщений всего...";

$lang['adm_er_ttle']  = "Ошибки";
$lang['adm_er_list']  = "Ошибки полученные в игре";
$lang['adm_er_clear'] = "Очистить";
$lang['adm_er_idmsg'] = "ID";
$lang['adm_er_type']  = "Тип";
$lang['adm_er_play']  = "id Игрока";
$lang['adm_er_time']  = "Дата";
$lang['adm_er_page']  = "Page address";
$lang['adm_er_nbs']   = "ошибок всего...";
$lang['adm_er_text']  = "Error message";
$lang['adm_er_bktr']  = "Backtrace";

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

$lang['adm_apply']    = "Apply";
$lang['adm_maint']    = "Maintenance";
$lang['adm_backup']   = "Backup";

$lang['addm_title']    = "Add a moon";
$lang['addm_addform']  = "Adding Form";
$lang['addm_playerid'] = "ID of player";
$lang['addm_moonname'] = "Moon name";
$lang['addm_moongala'] = "Choose galaxy";
$lang['addm_moonsyst'] = "Choose system";
$lang['addm_moonplan'] = "Choose position";
$lang['addm_moondoit'] = "Add";
$lang['addm_done']     = "Added";

$lang['adm_usr_level'][0] = "Player";
$lang['adm_usr_level'][1] = "GameOperator";
$lang['adm_usr_level'][2] = "SuperGameOperator";
$lang['adm_usr_level'][3] = "Administrator";
$lang['adm_usr_genre']['M'] = "Male";
$lang['adm_usr_genre']['F'] = "Female";

// Admin Strings
$lang['panel_mainttl'] = "Administration panel";

// Admin Panel A Template 1
$lang['adm_panel_mnu'] = "Search for a player";
$lang['adm_panel_ttl'] = "Type of research";
$lang['adm_search_pl'] = "Find a player";
$lang['adm_search_ip'] = "Search an IP";
$lang['adm_stat_play'] = "Statistic of a player";
$lang['adm_mod_level'] = "Modify access";

$lang['adm_player_nm'] = "Player name";
$lang['adm_ip']        = "Players using";
$lang['adm_plyer_wip'] = "Players with IP";
$lang['adm_frm1_id']   = "ID";
$lang['adm_frm1_name'] = "Name";
$lang['adm_frm1_ip']   = "IP";
$lang['adm_frm1_mail'] = "E-mail";
$lang['adm_frm1_acc']  = "Access";
$lang['adm_frm1_gen']  = "Gender";
$lang['adm_frm1_main'] = "ID planet";
$lang['adm_frm1_gpos'] = "Position";
$lang['adm_mess_lvl1'] = "Access level";
$lang['adm_mess_lvl2'] = "now ";
$lang['adm_colony']    = "Colony";
$lang['adm_planet']    = "Planet";
$lang['adm_moon']      = "Moon";
$lang['adm_technos']   = "Research and development";
$lang['adm_bt_search'] = "Search";
$lang['adm_bt_change'] = "Change";

$lang['flt_id']       = "ID";
$lang['flt_fleet']    = "Fleet";
$lang['flt_mission']  = "Mission";
$lang['flt_owner']    = "Owner";
$lang['flt_planet']   = "Planet";
$lang['flt_time_st']  = "Start time";
$lang['flt_e_owner']  = "Destination";
$lang['flt_time_en']  = "End time";
$lang['flt_staying']  = "Hold time";
$lang['flt_action']   = "Action";
$lang['flt_title']    = "Fleets in air";

$lang['md5_title']  = "md5 password crypting";
$lang['md5_pswcyp'] = "Password crypting";
$lang['md5_psw']    = "Password";
$lang['md5_pswenc'] = "Crypted password";
$lang['md5_doit']   = "[ Crypt ]";

$lang['mlst_title']       = "Message list";
$lang['mlst_mess_del']    = "Delete messages";
$lang['mlst_hdr_page']    = "Page";
$lang['mlst_hdr_title']   = " ) of messages :";
$lang['mlst_hdr_prev']    = "[ &lt;- ]";
$lang['mlst_hdr_next']    = "[ -&gt; ]";
$lang['mlst_hdr_id']      = "ID";
$lang['mlst_hdr_type']    = "Type";
$lang['mlst_hdr_time']    = "Time";
$lang['mlst_hdr_from']    = "From";
$lang['mlst_hdr_to']      = "To";
$lang['mlst_hdr_text']    = "Content";
$lang['mlst_hdr_action']  = "Select";
$lang['mlst_del_mess']    = "Delete";
$lang['mlst_bt_delsel']   = "Delete Selected";
$lang['mlst_bt_deldate']  = "Delete from";
$lang['mlst_hdr_delfrom'] = "Delete from";
$lang['mlst_mess_typ__0'] = "Espionnage";
$lang['mlst_mess_typ__1'] = "Player";
$lang['mlst_mess_typ__2'] = "Alliance";
$lang['mlst_mess_typ__3'] = "Attack";
$lang['mlst_mess_typ__4'] = "Exploitation";
$lang['mlst_mess_typ__5'] = "Transport";
$lang['mlst_mess_typ_15'] = "Expeditions";
$lang['mlst_mess_typ_99'] = "Building Queue";

$lang['adm_opt_title']         = "Server configuration";
$lang['adm_opt_game_settings'] = "Game settings";
$lang['adm_opt_game_name']     = "Game name";
$lang['adm_opt_game_gspeed']   = "Game speed";
$lang['adm_opt_game_fspeed']   = "Fleet speed";
$lang['adm_opt_game_pspeed']   = "Production speed<br>(normal = 1)";
$lang['adm_opt_game_forum']    = "Board address";
$lang['adm_opt_game_copyrigh'] = "Copyright";
$lang['adm_opt_game_online']   = "On-line Status";
$lang['adm_opt_game_offreaso'] = "Raison for offline";
$lang['adm_opt_plan_settings'] = "Planet settings";
$lang['adm_opt_plan_initial']  = "Initial fields";
$lang['adm_opt_plan_base_inc'] = "Basic income ";
$lang['adm_opt_game_debugmod'] = "Debug mode";
$lang['adm_opt_game_oth_info'] = "Other information";
$lang['adm_opt_game_oth_news'] = "Show framework News";
$lang['adm_opt_game_oth_chat'] = "Show external Chat";
$lang['adm_opt_game_oth_adds'] = "Google AdSense";
$lang['adm_opt_game_oth_lstc'] = "Last planet coordinates";
$lang['adm_opt_game_oth_gala'] = "Galaxy";
$lang['adm_opt_game_oth_syst'] = "System";
$lang['adm_opt_game_oth_plan'] = "Position";
$lang['adm_opt_btn_save']      = "Save";

?>