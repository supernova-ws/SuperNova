<?php

if (!defined('INSIDE'))
{
  die("Попытка взлома!");
}

global $sn_message_groups, $sn_message_class_list;
$lang['opt_custom'] = $lang['opt_custom'] === null ? array() : $lang['opt_custom'];
foreach($sn_message_groups['switchable'] as $option_id)
{
  $option_name = $sn_message_class_list[$option_id]['name'];
  $lang['opt_custom']["opt_{$option_name}"] = &$lang['msg_class'][$option_id];
}

$lang = array_merge($lang, array(
  'opt_header'           => 'Настройки пользователя',

  'opt_messages'         => 'Автоматические уведомления',
  'opt_msg_saved'        => 'Настройки успешно изменены',
  'opt_msg_name_changed' => 'Имя пользователя успешно изменено.<br /><a href="login.php" target="_top">Назад</a>',
  'opt_msg_pass_changed' => 'Пароль успешно изменен.<br /><a href="login.php" target="_top">Назад</a>',
  'opt_err_pass_wrong'   => 'Неправильный текущий пароль. Пароль не был изменен',
  'opt_err_pass_unmatched' => 'Введенный пароль не совпадает с подтвержденим пароля. Пароль не был изменен',

));

//
$lang['changue_pass']        = "Сменить пароль";
$lang['Download']            = "Загрузка";
$lang['Search']              = "Поиск";
$lang['succeful_changepass'] = "Пароль успешно изменён.<br /><a href=\"login.php\" target=\"_top\">Назад</a>";

//
$lang['userdata']			= "Информация";
$lang['username']			= "Имя";
$lang['lastpassword']			= "Старый пароль";
$lang['newpassword']			= "Новый пароль<br>(мин. 8 символов)";
$lang['newpasswordagain']		= "Повторите новый пароль";
$lang['emaildir']			= "Адрес e-mail";
$lang['emaildir_tip']			= "Этот адрес может быть изменён в любое время. Адрес станет основным, если он не изменялся в течении 7 дней.";
$lang['permanentemaildir']		= "Основной адрес e-mail";

$lang['opt_lst_ord']			= "Упорядочить планеты по:";
$lang['opt_lst_ord0']			= "Времени колонизации";
$lang['opt_lst_ord1']			= "Координатам";
$lang['opt_lst_ord2']			= "Алфавитному порядку";
$lang['opt_lst_ord3']			= "Количеству полей";
$lang['opt_lst_cla']			= "Упорядочить по:";
$lang['opt_lst_cla0']			= "Возрастанию";
$lang['opt_lst_cla1']			= "Убыванию";
$lang['opt_chk_skin']			= "Использовать оформление";

// 	
$lang['opt_adm_title']			= "Опции администрирования";
$lang['opt_adm_planet_prot']		= "Защита планет";

// 	
$lang['thanksforregistry']		= "Спасибо за регистрацию.<br />Через несколько минут вы получите ваше сообщение с паролем.";
$lang['general_settings']		= "Общие настройки";
$lang['skins_example']			= "Оформление<br>(например C:/ogame/skin/)";
$lang['avatar_example']			= "Аватар<br>(например /img/avatar.jpg)";
$lang['untoggleip']			= "Выключить функцию проверки по IP";
$lang['untoggleip_tip']			= "Проверка IP означает то, что вы не сможете войти под своим именем с двух разных IP. Проверка даёт вам преимущество в безопасности!";

// 	
$lang['galaxyvision_options']		= "Настройки галактики";
$lang['spy_cant']			= "Количество зондов";
$lang['spy_cant_tip']			= "Количество зондов, которое будет отправляться, когда вы будете за кем-то следить.";
$lang['tooltip_time']			= "Время показа подсказок";
$lang['mess_ammount_max']		= "Количество максимальных сообщений флота";
$lang['show_ally_logo']			= "Показывать логотип альянсов";
$lang['seconds']			= "Секунд(а/ы)";

//	
$lang['shortcut']			= "Быстрый доступ";
$lang['show']				= "Показывать";
$lang['write_a_messege']		= "Написать сообщение";
$lang['spy']				= "Шпионаж";
$lang['add_to_buddylist']		= "Добавить в друзья";
$lang['attack_with_missile']		= "Ракетная атака";
$lang['show_report']			= "Просмотреть отчёт";

//	
$lang['delete_vacations']		= "Управление профилем";
$lang['mode_vacations']			= "Включить режим отпуска";
$lang['vacations_tip']			= "Режим отпуска нужен для защиты планет во время вашего отсутствия.";
$lang['deleteaccount']			= "Отключить профиль";
$lang['deleteaccount_tip']		= "Профиль будет удалён через 45 дней неактивности.";
$lang['save_settings']			= "Сохранить изменения";
$lang['exit_vacations']			= "Выйти из режима отпуска";
$lang['Vaccation_mode']			= "Режим отпуска включён. Он продлится до: ";
$lang['You_cant_exit_vmode']		= "Вы не можете выйти из режима отпуска, пока не истечёт минимальное время";
$lang['Error']				= "Ошибка";
$lang['cans_resource']			= "Прекратите добычу ресурсов на планетах";
$lang['cans_reseach']                   = "Остановите иследования на планетах";
$lang['cans_build']                     = "Остановите строительство на планетах";
$lang['cans_fleet_build']               = "Остановите постройку флота и обороны";
$lang['cans_fly_fleet2']                 = "Чужой флот приближается... вы не можите уйти в отпуск";
$lang['vacations_exit']                 = "Режим отпуска отключен... Перезайдите";

$lang['select_skin_path']		= "ВЫБРАТЬ";

$lang['opt_language']         = 'Язык интерфейса';

$lang['opt_compatibility']    = 'Совместимость - старые интерфейсов';
$lang['opt_compat_structures']= 'Старый интерфейс строительства зданий';

$lang['opt_vacation_err_your_fleet'] = "Нельзя уйти в отпуск пока в полете находится хотя бы один ваш флот";
$lang['opt_vacation_err_building']   = "Вы что-то строите или исследуете и поэтому не можете уйти в отпуск";
$lang['opt_vacation_min'] = 'минимум до';

?>
