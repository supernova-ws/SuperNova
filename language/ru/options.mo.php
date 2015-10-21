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
* @version 40a17.0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'opt_account' => 'Профиль',
  'opt_int_options' => 'Интерфейс',
  'opt_settings_statistics' => 'Статистика игрока',
  'opt_settings_info' => 'Информация об игроке',
  'opt_alerts' => 'Уведомления',
  'opt_common' => 'Общие настройки',

  'opt_birthday' => 'День рождения',

  'opt_header' => 'Настройки пользователя',
  'opt_messages' => 'Автоматические уведомления',
  'opt_msg_saved' => 'Настройки успешно изменены',
  'opt_msg_name_changed' => 'Имя пользователя успешно изменено.<br /><a href="login.php" target="_top">Назад</a>',
  'opt_msg_name_change_err_used_name' => 'Это имя принадлежит другому пользователю',
  'opt_msg_name_change_err_no_dm' => 'Не хватает ТМ для смены имени',

  'username_old' => 'Старое имя',
  'username_new' => 'Новое имя',
  'username_change_confirm' => 'Сменить имя',
  'username_change_confirm_payed' => 'за',

  'opt_msg_pass_changed' => 'Пароль успешно изменен',
  'opt_err_pass_wrong' => 'Неправильный текущий пароль. Пароль не был изменен',
  'opt_err_pass_unmatched' => 'Введенный пароль не совпадает с подтвержденим пароля. Пароль не был изменен',
  'changue_pass' => 'Сменить пароль',
  'Download' => 'Загрузка',
  'userdata' => 'Информация',
  'username' => 'Имя',
  'lastpassword' => 'Старый пароль',
  'newpassword' => 'Новый пароль<br>(мин. 8 символов)',
  'newpasswordagain' => 'Повторите новый пароль',
  'emaildir' => 'Адрес e-mail',
  'emaildir_tip' => 'Этот адрес может быть изменён в любое время. Адрес станет основным, если он не изменялся в течении 7 дней.',
  'permanentemaildir' => 'Основной адрес e-mail',
  'opt_planet_sort_title' => 'Сортировать планеты по',
  'opt_planet_sort_options' => array(
    SORT_ID       => 'Времени колонизации',
    SORT_LOCATION => 'Координатам',
    SORT_NAME     => 'Алфавиту',
    SORT_SIZE     => 'Количеству полей',
  ),
  'opt_planet_sort_ascending' => array(
    SORT_ASCENDING  => 'Возрастанию',
    SORT_DESCENDING => 'Убыванию',
  ),

  'PLAYER_OPTION_NAVBAR' => 'Панель навигации (вверху экрана)',
  'PLAYER_OPTION_NAVBAR_DISABLE_PLANET' => 'Отключить кнопку планеты',
  'PLAYER_OPTION_NAVBAR_DISABLE_HANGAR' => 'Отключить кнопку верфи',
  'PLAYER_OPTION_NAVBAR_DISABLE_QUESTS' => 'Отключить кнопку квестов',
  'PLAYER_OPTION_UNIVERSE_OLD' => 'Использовать старый вид "Обзора Вселенной"',
  'PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE' => 'Отключить кнопку колонизации',

  'opt_chk_skin' => 'Использовать оформление',
  'opt_adm_title' => 'Опции администрирования',
  'opt_adm_planet_prot' => 'Защита планет',
  'thanksforregistry' => 'Спасибо за регистрацию.<br />Через несколько минут вы получите ваше сообщение с паролем.',
  'general_settings' => 'Общие настройки',
  'skins_example' => 'Оформление',


  'opt_avatar' => 'Аватар',
  'opt_avatar_search' => 'Искать в Google',
  'opt_avatar_remove' => 'Удалить аватар',
  'opt_upload' => 'Загрузить',

  'opt_msg_avatar_removed' => 'Аватар удален',
  'opt_msg_avatar_uploaded' => 'Аватар изменен успешно',
  'opt_msg_avatar_error_delete' => 'Ошибка удаления файла аватара. Обратитесь к Администрации сервера',
  'opt_msg_avatar_error_writing' => 'Ошибка сохранения файла аватара. Обратитесь к Администрации сервера',
  'opt_msg_avatar_error_upload' => 'Ошибка загрузки изображения %1. Обратитесь к Администрации сервера',
  'opt_msg_avatar_error_unsupported' => 'Формат загруженного изображения не поддерживается. Поддерживаются только файлы JPG, GIF, PNG размером до 200КБ',



  'untoggleip' => 'Выключить функцию проверки по IP',
  'untoggleip_tip' => 'Проверка IP означает то, что вы не сможете войти под своим именем с двух разных IP. Проверка даёт вам преимущество в безопасности!',
  'galaxyvision_options' => 'Вселенная',
  'spy_cant' => 'Количество зондов',
  'spy_cant_tip' => 'Количество зондов, которое будет отправляться, когда вы будете за кем-то следить.',
  'tooltip_time' => 'Задержка перед показом подсказки',
  'mess_ammount_max' => 'Количество максимальных сообщений флота',
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
  'deleteaccount_on' => 'При неактивности аккаунта его удаление произойдет',
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
  'opt_vacation_err_building' => 'Вы что-то строите или исследуете на %s и поэтому вы не можете уйти в отпуск',
  'opt_vacation_err_research' => 'Ваши ученные исследует технологию и поэтому вы не можете уйти в отпуск',
  'opt_vacation_err_que' => 'У вас либо исследуются технология, либо что-то строиться на одной из планет и поэтому вы не можете уйти в отпуск. Используйте ссылку "Империя", что бы просмотреть очереди построек на планетах',
  'opt_vacation_err_timeout' => 'Вы еще не наработали на отпуск - таймаут ухода в отпуск не исчерпан',
  'opt_vacation_next' => 'Пойти в отпуск можно будет после',
  'opt_vacation_min' => 'минимум до',
  'succeful_changepass' => 'Пароль успешно изменён.<br /><a href="login.php" target="_top">Назад</a>',

  'opt_time_diff_clear' => 'Замерить разницу между временем у игрока и временем на сервере',
  'opt_time_diff_manual' => 'Задать вручную разницу во времени',
  'opt_time_diff_explain' => 'При правильно выставленной разнице во времени, часы "Время у игрока" в навбаре должны идти секунда в секунду с часами на устройстве игрока<br />
  Обычно игра сама автоматически устанавливает правильную разницу во времени. Однако при неправильной установке часового пояса на устройстве игрока, при игре с нескольких устройств, а так же
  при очень медленном интернете иногда нужно установить разницу во времени вручную',

  'opt_custom' => array(
    'opt_uni_avatar_user' => 'Показывать аватар пользователя',
    'opt_uni_avatar_ally' => 'Показывать логотип Альянса',
    'opt_int_struc_vertical' => 'Вертикальная очередь построек',
    'opt_int_navbar_resource_force' => 'Всегда показывать планетарный навбар',
    'opt_int_overview_planet_columns' => 'Количество колонок в списке планет',
    'opt_int_overview_planet_columns_hint' => '0 - рассчитать по максимальному количество рядов',
    'opt_int_overview_planet_rows' => 'Максимальное количество рядов в списке планет',
    'opt_int_overview_planet_rows_hint' => 'Игнорируется, если указано количество колонок',
  ),

  'opt_mail_optional_description' => 'На этот почтовый адрес отправляются личные сообщения от других игроков и уведомления о внутриигровых событиях (например, отчеты об экспедициях и отчеты шпионажа)',
  'opt_mail_permanent_description' => 'К этому почтовому адресу привязывается игровой аккаунт. Ввести его можно только один раз. Все системные уведомления (например, о смене пароля) отправляются именно на этот адрес',

  'opt_sound_enabled' => 'Включить звуки в игре',
  'opt_animation_disabled' => 'Отключить эффекты анимации в игре',

  'opt_account_name' => 'Ваш логин<br />Это имя надо вводить при входе в игру',
  'opt_game_user_name' => 'Имя в игре<br />По этим именем вы будете видны другим игрокам сервера',

  'opt_build_autoconvert_hide' => 'Скрыть кнопку автоконвертации',

));
