<?php

/*
#############################################################################
#  Filename: options.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 31a10
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'opt_header' => 'Настройки пользователя',
  'opt_messages' => 'Автоматические уведомления',
  'opt_msg_saved' => 'Настройки успешно изменены',
  'opt_msg_name_changed' => 'Имя пользователя успешно изменено.<br /><a href="login.php" target="_top">Назад</a>',
  'opt_msg_pass_changed' => 'Пароль успешно изменен.<br /><a href="login.php" target="_top">Назад</a>',
  'opt_err_pass_wrong' => 'Неправильный текущий пароль. Пароль не был изменен',
  'opt_err_pass_unmatched' => 'Введенный пароль не совпадает с подтвержденим пароля. Пароль не был изменен',
  'changue_pass' => 'Сменить пароль',
  'Download' => 'Загрузка',
  'Search' => 'Поиск',
  'userdata' => 'Информация',
  'username' => 'Имя',
  'lastpassword' => 'Старый пароль',
  'newpassword' => 'Новый пароль<br>(мин. 8 символов)',
  'newpasswordagain' => 'Повторите новый пароль',
  'emaildir' => 'Адрес e-mail',
  'emaildir_tip' => 'Этот адрес может быть изменён в любое время. Адрес станет основным, если он не изменялся в течении 7 дней.',
  'permanentemaildir' => 'Основной адрес e-mail',
  'opt_lst_ord' => 'Упорядочить планеты по:',
  'opt_lst_ord0' => 'Времени колонизации',
  'opt_lst_ord1' => 'Координатам',
  'opt_lst_ord2' => 'Алфавитному порядку',
  'opt_lst_ord3' => 'Количеству полей',
  'opt_lst_cla' => 'Упорядочить по:',
  'opt_lst_cla0' => 'Возрастанию',
  'opt_lst_cla1' => 'Убыванию',
  'opt_chk_skin' => 'Использовать оформление',
  'opt_adm_title' => 'Опции администрирования',
  'opt_adm_planet_prot' => 'Защита планет',
  'thanksforregistry' => 'Спасибо за регистрацию.<br />Через несколько минут вы получите ваше сообщение с паролем.',
  'general_settings' => 'Общие настройки',
  'skins_example' => 'Оформление<br>(например C:/ogame/skin/)',
  'avatar_example' => 'Аватар<br>(например /img/avatar.jpg)',
  'untoggleip' => 'Выключить функцию проверки по IP',
  'untoggleip_tip' => 'Проверка IP означает то, что вы не сможете войти под своим именем с двух разных IP. Проверка даёт вам преимущество в безопасности!',
  'galaxyvision_options' => 'Настройки галактики',
  'spy_cant' => 'Количество зондов',
  'spy_cant_tip' => 'Количество зондов, которое будет отправляться, когда вы будете за кем-то следить.',
  'tooltip_time' => 'Время показа подсказок',
  'mess_ammount_max' => 'Количество максимальных сообщений флота',
  'show_ally_logo' => 'Показывать логотип альянсов',
  'seconds' => 'Секунд(а/ы)',
  'shortcut' => 'Быстрый доступ',
  'show' => 'Показывать',
  'write_a_messege' => 'Написать сообщение',
  'spy' => 'Шпионаж',
  'add_to_buddylist' => 'Добавить в друзья',
  'attack_with_missile' => 'Ракетная атака',
  'show_report' => 'Просмотреть отчёт',
  'delete_vacations' => 'Управление профилем',
  'mode_vacations' => 'Включить режим отпуска',
  'vacations_tip' => 'Режим отпуска нужен для защиты планет во время вашего отсутствия.',
  'deleteaccount' => 'Отключить профиль',
  'deleteaccount_tip' => 'Профиль будет удалён через 45 дней неактивности.',
  'save_settings' => 'Сохранить изменения',
  'exit_vacations' => 'Выйти из режима отпуска',
  'Vaccation_mode' => 'Режим отпуска включён. Он продлится до: ',
  'You_cant_exit_vmode' => 'Вы не можете выйти из режима отпуска, пока не истечёт минимальное время',
  'Error' => 'Ошибка',
  'cans_resource' => 'Прекратите добычу ресурсов на планетах',
  'cans_reseach' => 'Остановите иследования на планетах',
  'cans_build' => 'Остановите строительство на планетах',
  'cans_fleet_build' => 'Остановите постройку флота и обороны',
  'cans_fly_fleet2' => 'Чужой флот приближается... вы не можите уйти в отпуск',
  'vacations_exit' => 'Режим отпуска отключен... Перезайдите',
  'select_skin_path' => 'ВЫБРАТЬ',
  'opt_language' => 'Язык интерфейса',
  'opt_compatibility' => 'Совместимость - старые интерфейсов',
  'opt_compat_structures' => 'Старый интерфейс строительства зданий',
  'opt_vacation_err_your_fleet' => 'Нельзя уйти в отпуск пока в полете находится хотя бы один ваш флот',
  'opt_vacation_err_building' => 'Вы что-то строите или исследуете на %s и поэтому не можете уйти в отпуск',
  'opt_vacation_min' => 'минимум до',
  'succeful_changepass' => 'Пароль успешно изменён.<br /><a href="login.php" target="_top">Назад</a>',
));

?>
