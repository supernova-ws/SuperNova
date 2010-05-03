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
if (!defined('INSIDE')) {
	die("Обнаружена попытка взлома!");
}
$lang['adm_done']               = "Успешно выполнено";
$lang['adm_stat_title']         = "Обновление статистики";
$lang['adm_cleaner_title']      = "Чистка очереди построек";
$lang['adm_cleaned']            = "Кол-во удаленных задач: ";
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

$lang['adm_an_title']     = "Новости";
$lang['adm_an_date']      = "Дата";
$lang['adm_an_announce']  = "Содержание";
$lang['adm_an_total']     = "Всего новостей: ";
$lang['adm_an_add']       = "Добавить новость";
$lang['adm_an_edit']      = "Редактировать новость";
$lang['adm_an_mode_new']  = "Новая";
$lang['adm_an_mode_edit'] = "Редактирование";
$lang['adm_an_mode_dupe'] = "Дубликат";

$lang['adm_am_plid']  = "ID планеты";
$lang['adm_am_done']  = "Добавление прошло успешно";
$lang['adm_am_ttle']  = "Добавить ресурсы";
$lang['adm_am_add']   = "Подтвердить";
$lang['adm_am_form']  = "Форма добавления ресурсов";

$lang['adm_bn_ttle']  = "Забанить игрока";
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
$lang['adm_ch_nbs']   = "сообщений всего...";

$lang['adm_er_ttle']  = "Ошибки";
$lang['adm_er_list']  = "Ошибки полученные в игре";
$lang['adm_er_clear'] = "Очистить";
$lang['adm_er_idmsg'] = "ID";
$lang['adm_er_type']  = "Тип";
$lang['adm_er_play']  = "id Игрока";
$lang['adm_er_time']  = "Дата";
$lang['adm_er_nbs']   = "ошибок всего...";

$lang['adm_maint']    = "Обслуживание";
?>