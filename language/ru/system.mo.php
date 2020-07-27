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
* @system [Russian]
* @version #45d0#
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE'))
{
  exit;
}

// System-wide localization

global $config;

$a_lang_array = [
  'sys_birthday' => 'День рождения',
  'sys_birthday_message' => '%1$s! Администрация СуперНовы сердечно поздравляет тебя с твоим Днем Рождения, который пришелся на %2$s и преподносит тебе в качестве подарка %3$d %4$s! От всей души желаем тебе успехов в игре и высоких рангов в статистике! Может это поздравление и запоздало, но лучше раньше, чем позже.',

  'adm_err_denied' => 'Доступ запрещен. У вас не хватает прав, что бы пользоваться этой страницей интерфейса управления сервером',

  'sys_empire'          => 'Империя',
  'VacationMode'			=> "Ваше производство закрыто, так как вы в Отпуске",
  'sys_moon_destruction_report' => "Рапорт разрушения луны",
  'sys_moon_destroyed' => "Ваши Звёзды Смерти произвели мощную гравитационную волну, которая разрушила луну! ",
  'sys_rips_destroyed' => "Ваши Звёзды Смерти произвели мощную гравитационную волну, но её мощности оказалось не достаточно для уничтожения луны такого размера. Но гравитационная волна отразилась от лунной поверхности и разрушила ваш флот.",
  'sys_rips_come_back' => "Ваши Звёзды Смерти не имеют достаточно энергии, чтоб нанести ущерб этой луне. Ваш флот возвращается не уничтожив луну.",
  'sys_chance_moon_destroy' => "Изменение лунного уничтожения: ",
  'sys_chance_rips_destroy' => "Изменение разрывного уничтожения: ",

  'sys_impersonate' => 'Воплотиться',
  'sys_impersonate_done' => 'Развоплотиться',
  'sys_impersonated_as' => 'ВНИМАНИЕ! Вы сейчас Воплотились в игрока %1$s. Не забывайте, что на самом деле - вы %2$s! Развоплотиться можно выбрав соответствующий пункт меню.',

  'menu_admin_mining'          => 'Добыча игроков',
  'menu_admin_units'          => 'Юниты',
  'menu_admin_ube_balance'          => 'Баланс UBE',

  'sys_day' => "дней",
  'sys_hrs' => "часов",
  'sys_min' => "минут",
  'sys_sec' => "секунд",
  'sys_day_short' => "д",
  'sys_hrs_short' => "ч",
  'sys_min_short' => "м",
  'sys_sec_short' => "с",

  'sys_ask_admin' => 'Вопросы и предложения направлять по адресу',

  'sys_wait'      => 'Запрос выполняется. Пожалуйста, подождите.',

  'sys_fleets'       => 'Флоты',
  'sys_expeditions'  => 'Экспедиции',
  'sys_fleet'        => 'Флот',
  'sys_expedition'   => 'Экспедиция',
  'sys_event_next'   => 'Следующее событие:',
  'sys_event_arrive' => 'прибудет',
  'sys_event_stay'   => 'закончит задание',
  'sys_event_return' => 'вернется',

  'sys_total'           => "ИТОГО",
  'sys_need'				=> 'Нужно',
  'sys_register_date'   => 'Дата регистрации',

  'sys_attacker' 		=> "Атакующий",
  'sys_defender' 		=> "Обороняющийся",

  'COE_combatSimulator' => "Симулятор боя",
  'COE_simulate'        => "Запуск симулятора",
  'COE_fleet'           => "Флот",
  'COE_defense'         => "Оборона",
  'sys_coe_combat_start'=> "Флоты соперников встретились",
  'sys_coe_combat_end'  => "Результаты боя",
  'sys_coe_round'       => "Раунд",

  'sys_coe_attacker_turn'=> 'Атакующий делает выстрелы общей мощностью %1$s. Щиты обороняющегося поглощают %2$s выстрелов<br />',
  'sys_coe_defender_turn'=> 'Обороняющийся делает выстрелы общей мощностью %1$s. Щиты атакующего поглощают %2$s выстрелов<br /><br /><br />',
  'sys_coe_outcome_win'  => 'Обороняющийся выиграл битву!<br />',
  'sys_coe_outcome_loss' => 'Атакующий выиграл битву!<br />',
  'sys_coe_outcome_loot' => 'Он получает %1$s металла, %2$s кристаллов, %3$s дейтерия<br />',
  'sys_coe_outcome_draw' => 'Бой закончился ничьёй.<br />',
  'sys_coe_attacker_lost'=> 'Атакующий потерял %1$s единиц.<br />',
  'sys_coe_defender_lost'=> 'Обороняющийся потерял %1$s единиц.<br />',
  'sys_coe_debris_left'  => 'Теперь на этих пространственных координатах находятся %1$s металла и %2$s кристаллов.<br /><br />',
  'sys_coe_moon_chance'  => 'Шанс появления луны составляет %1$s%%<br />',
  'sys_coe_rw_time'      => 'Время генерации страницы %1$s секунд<br />',

  'sys_resources'       => "Ресурсы",
  'sys_ships'           => "Корабли",
  'sys_metal'          => "Металл",
  'sys_metal_sh'       => "М",
  'sys_crystal'        => "Кристалл",
  'sys_crystal_sh'     => "К",
  'sys_deuterium'      => "Дейтерий",
  'sys_deuterium_sh'   => "Д",
  'sys_energy'         => "Энергия",
  'sys_energy_sh'      => "Э",
  'sys_dark_matter'    => "Тёмная Материя",
  'sys_dark_matter_sh' => "ТМ",
  'sys_metamatter'     => "Метаматерия",
  'sys_metamatter_sh'  => "ММ",

  'sys_reset'           => "Сбросить",
  'sys_send'            => "Отправить",
  'sys_characters'      => "символов",
  'sys_back'            => "Назад",
  'sys_return'          => "Вернуться",
  'sys_delete'          => "Удалить",
  'sys_writeMessage'    => "Написать сообщение",
  'sys_hint'            => "Подсказка",

  'sys_alliance'        => "Альянс",
  'sys_player'          => "Игрок",
  'sys_coordinates'     => "Координаты",

  'sys_online'          => "Онлайн",
  'sys_offline'         => "Оффлайн",
  'sys_status'          => "Статус",

  'sys_universe'        => "Вселенная",
  'sys_goto'            => "Перейти",

  'sys_time'            => "Время",
  'sys_temperature'		=> 'Температура',

  'sys_no_task'         => "нет задания",

  'sys_affilates'       => "Приглашенные игроки",

  'sys_fleet_arrived'   => "Флот прибыл",

  'sys_planet_type' => [
    PT_PLANET => 'Планета',
    PT_DEBRIS => 'Поле обломков',
    PT_MOON   => 'Луна',
  ],

  'sys_planet_type_sh' => [
    PT_PLANET => '(П)',
    PT_DEBRIS => '(О)',
    PT_MOON   => '(Л)',
  ],

  'sys_planet_expedition' => 'неисследованное пространство',

  'sys_capacity' 			=> 'Грузоподъёмность',
  'sys_cargo_bays' 		=> 'Трюмы',

  'sys_supernova' 		=> 'СуперНова',
  'sys_server' 			=> 'Сервер',

  'sys_unbanned'			=> 'Разблокирован',

  'sys_date_time'			=> 'Дата и время',
  'sys_from_person'	   => 'От кого',
  'sys_from_speed'	   => 'от',

  'sys_from'		  => 'с',
  'tp_on'            => 'на',

// Resource page
  'res_planet_production' => 'Производство ресурсов на планете',
  'res_basic_starting_resources' => 'Стартовые ресурсы на планете',
  'res_basic_income' => 'Естественное производство',
  'res_basic_storage_size' => 'Размер складов',
  'res_total' => 'ВСЕГО',
  'res_calculate' => 'Рассчитать',
  'res_hourly' => 'В час',
  'res_daily' => 'За день',
  'res_weekly' => 'За неделю',
  'res_monthly' => 'За месяц',
  'res_storage_fill' => 'Заполненность складов',
  'res_hint' => '<ul><li>Производство ресурсов <100% означает нехватку энергии. Постройте дополнительные электростанции или уменьшите производство ресурсов<li>Если ваше производство равно 0% скорее всего вы вышли из отпуска и вам нужно включить все заводы<li>Что бы выставить добычу для всех заводов сразу используйте дроп-даун в загловке таблицы. Особенно удобно использовать его после выхода из отпуска</ul>',

// Build page
  'bld_destroy' => 'Уничтожить',
  'bld_create'  => 'Построить',
  'bld_research' => 'Исследовать',
  'bld_hire' => 'Нанять',

// Imperium page
  'imp_imperator' => "Император",
  'imp_overview' => "Обзор Империи",
  'imp_fleets' => "Флоты в полете",
  'imp_production' => "Производство",
  'imp_name' => "Название",
  'imp_research' => "Исследования",
  'imp_exploration' => "Экспедиции",
  'imp_imperator_none' => "Нет такого Императора во Вселенной!",
  'sys_fields' => "Сектора",

// Cookies
  'err_cookie' => "Ошибка! Невозможно авторизировать пользователя по информации в cookie.<br />Очистите куки браузера, затем еще раз попытайтесь <a href='login" . DOT_PHP_EX . "'>войти</a> в игру или <a href='reg" . DOT_PHP_EX . "'>зарегестрироваться</a>.",

// Supported languages
  'ru'              	  => 'Русский',
  'en'              	  => 'Английский',

  'sys_vacation'        => 'Вы же в отпуске до',
  'sys_vacation_leave'  => 'Я уже отдохнул - выйти из отпуска!',
  'sys_vacation_in'     => 'В отпуске',
  'sys_level'           => 'Уровень',
  'sys_level_short'     => 'Ур',
  'sys_level_max'       => 'Максимальный уровень',

  'sys_yes'             => 'Да',
  'sys_no'              => 'Нет',

  'sys_on'              => 'Включен',
  'sys_off'             => 'Отключен',

  'sys_confirm'         => 'Подтвердить',
  'sys_save'            => 'Сохранить',
  'sys_create'          => 'Создать',
  'sys_write_message'   => 'Написать сообщение',

// top bar
  'top_of_year' => 'г.',
  'top_online'			=> 'Игроки',

  'sys_first_round_crash_1'	=> 'Контакт с атакованным флотом потерян.',
  'sys_first_round_crash_2'	=> 'Это означает что он был уничтожен в первом раунде боя.',

  'sys_ques' => [
    QUE_STRUCTURES => 'Здания',
    QUE_HANGAR     => 'Верфь',
    SUBQUE_DEFENSE => 'Оборона',
    QUE_RESEARCH   => 'Исследования',
  ],

  'navbar_button_expeditions_short' => 'Экспа',
  'navbar_button_fleets' => 'Флоты',
  'navbar_button_quests' => 'Квесты',
  'navbar_font' => 'Шрифт',
  'navbar_font_normal' => 'Норма',
  'sys_que_structures' => 'Здания',
  'sys_que_hangar' => 'Верфь',
  'sys_que_defense' => 'Оборона',
  'sys_que_research' => 'Исследования',
  'sys_que_research_short' => 'Наука',

  'eco_que' => 'Очередь',
  'eco_que_empty' => 'Очередь пуста',
  'eco_que_clear' => 'Очистить очередь',
  'eco_que_trim'  => 'Отменить последнее',
  'eco_que_artifact'  => 'Использовать Артефакт',

  'sys_cancel' => 'Отменить',

  'sys_overview'			=> 'Обзор',
  'mod_marchand'			=> 'Торговец',
  'sys_galaxy'			=> 'Галактика',
  'sys_system'			=> 'Система',
  'sys_planet'			=> 'Планета',
  'sys_planet_title'			=> 'Тип планеты',
  'sys_planet_title_short'			=> 'Тип',
  'sys_moon'			=> 'Луна',
  'sys_error'			=> 'Ошибка',
  'sys_done'				=> 'Готово',
  'sys_no_vars'			=> 'Ошибка инициализации переменных, обратитесь к администрации!',
  'sys_attacker_lostunits'		=> 'Атакующий потерял %s единиц.',
  'sys_defender_lostunits'		=> 'Обороняющийся потерял %s единиц.',
  'sys_gcdrunits' 			=> 'Теперь на этих пространственных координатах находятся %s %s и %s %s.',
  'sys_moonproba' 			=> 'Шанс появления луны составляет: %d %% ',
  'sys_moonbuilt' 			=> 'Благодаря огромной энергии огромные куски металла и кристалла соединяются и образуется новая луна %s %s!',
  'sys_attack_title'    		=> '%s. Произошёл бой между следующими флотами::',
  'sys_attack_attacker_pos'      	=> 'Атакующий %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> 'Вооружение: %d %% Щиты: %d %% Броня: %d %% ',
  'sys_attack_defender_pos' 	=> 'Обороняющийся %s [%s:%s:%s]',
  'sys_ship_type' 			=> 'Тип',
  'sys_ship_count' 		=> 'Кол-во',
  'sys_ship_weapon' 		=> 'Вооружение',
  'sys_ship_shield' 		=> 'Щиты',
  'sys_ship_armour' 		=> 'Броня',
  'sys_ship_speed' 		=> 'Скорость',
  'sys_ship_consumption' 		=> 'Потребление',
  'sys_ship_capacity' 		=> 'Трюм/Бак',
  'sys_destroyed' 			=> 'уничтожен',
  'sys_attack_attack_wave' 	=> 'Атакующий делает выстрелы общей мощностью %s по обороняющемуся. Щиты обороняющегося поглощают %s выстрелов.',
  'sys_attack_defend_wave'		=> 'Обороняющийся делает выстрелы общей мощностью %s по атакующему. Щиты атакующего поглащают %s выстрелов.',
  'sys_attacker_won' 		=> 'Атакующий выиграл битву!',
  'sys_defender_won' 		=> 'Обороняющийся выиграл битву!',
  'sys_both_won' 			=> 'Бой закончился ничьёй!',
  'sys_stealed_ressources' 	=> 'Он получает %s металла %s %s кристалла %s и %s дейтерия.',
  'sys_rapport_build_time' 	=> 'Время генерации страницы %s секунд',
  'sys_mess_tower' 		=> 'Транспорт',
  'sys_coe_lost_contact' 		=> 'Связь с вашим флотом потеряна',
  'sys_spy_activity' => 'Наблюдается шпионская активность возле ваших планет',
  'sys_spy_materials' 		=> 'Сырьё на',
  'sys_spy_fleet' 			=> 'Флот',
  'sys_spy_defenses' 		=> 'Оборона',
  'sys_mess_qg' 			=> 'Командование флотом',
  'sys_mess_spy_report' 		=> 'Шпионский доклад',
  'sys_mess_spy_lostproba' 	=> 'Погрешность информации, полученной спутником %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Шанс обнаружения вашего разведывательного флота %d%%',
  'sys_mess_spy_detect_chance_no_percent' 	=> 'Шанс обнаружения вашего разведывательного флота',
  'sys_mess_spy_control' 		=> 'Контрразведка',
  'sys_mess_spy_activity' 		=> 'Шпионская активность',
  'sys_mess_spy_enemy_fleet' 	=> 'Чужой флот с планеты',
  'sys_mess_spy_seen_at'		=> 'был обнаружен возле планеты',
  'sys_mess_spy_destroyed'		=> 'Разведывательный флот был уничтожен',
  'sys_mess_spy_destroyed_enemy'		=> 'Вражеский шпионский флот уничтожен',
  'sys_object_arrival'		=> 'Прибыл на планету',
  'sys_stay_mess_stay' => 'Передислокация флота',
  'sys_stay_mess_start' 		=> 'Ваш флот прибыл на планету',
  'sys_stay_mess_back'		=> 'Ваш флот вернулся ',
  'sys_stay_mess_end'		=> ' и доставил:',
  'sys_stay_mess_bend'		=> ' и доставил следующие ресурсы:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> 'Колонизация',
  'sys_colo_mess_report' 		=> 'Отчёт о колонизации',
  'sys_colo_defaultname' 		=> 'Колония',
  'sys_colo_arrival' 		=> 'Флот достигает координат ',
  'sys_colo_maxcolo' 		=> ', но колонизировать планету нельзя, достигнуто максимальное число колоний для вашего уровня колонизации',
  'sys_colo_allisok' 		=> ', и колонисты начинают осваивать новую планету.',
  'sys_colo_badpos'  			=> ', и колонисты нашли среду мало выгодной для Вашей империи. Миссия колонизации возвращается обратно на планету отправки.',
  'sys_colo_notfree' 			=> ', и колонисты не нашли планету в этих координатах. Они вынуждены проложить дорогу обратно абсолютно обескураженными.',
  'sys_colo_no_colonizer'     => 'Во флоте нет колонизатора',
  'sys_colo_planet'  		=> ' Планета колонизирована!',
  'sys_expe_report' 		=> 'Отчёт экспедиции',
  'sys_recy_report' 		=> 'Системная информация',
  'sys_expe_blackholl_1' 		=> 'Ваш флот попал в чёрную дыру и частично потерян!',
  'sys_expe_blackholl_2' 		=> 'Ваш флот попал в чёрную дыру и полностью потерян!',
  'sys_expe_nothing_1' 		=> 'Ваш исследователи стали свидетелями СуперНовы! И ваши накопители успели принять часть высвободившейся энергии.',
  'sys_expe_nothing_2' 		=> 'Ваш исследователи ничего не обнаружили!',
  'sys_expe_found_goods' 		=> 'Ваш исследователи нашли планету, богатую сырьём!<br>Вы получили %s %s, %s %s и %s %s',
  'sys_expe_found_ships' 		=> 'Ваш исследователи нашли безупречно новый флот!<br>Вы получили: ',
  'sys_expe_back_home' 		=> 'Ваш флот возвращается обратно.',
  'sys_mess_transport' 		=> 'Транспорт',
//  'sys_tran_mess_owner' 		=> 'Один из ваших флотов достигает планеты %s %s и доставляет %s %s, %s  %s и %s %s.',
  'sys_tran_mess_user'  		=> 'Флот с планеты %s %s прибыл на %s %s и доставил %s %s, %s %s и %s %s.',
  'sys_relocate_mess_user'  		=> 'Так же на планету передислоцированы следующие боевые единицы:<br />',
  'sys_mess_fleetback' 		=> 'Возвращение',
  'sys_tran_mess_back' 		=> 'Один из ваших флотов возвращается на планету %s %s.',
  'sys_recy_gotten' 		=> 'Один из Ваших флотов добыл %s %s и %s %s Возвращается на планету.',
  'sys_notenough_money' 		=> 'Вам не хватает ресурсов, чтобы построить: %s. У Вас сейчас: %s %s , %s %s и %s %s. Для строительства необходимо: %s %s , %s %s и %s %s.',
  'sys_nomore_level'		=> 'Вы больше не можете совершенствовать это. Оно достигло макс. уровня ( %s ).',
  'sys_buildlist' 			=> 'Список построек',
  'sys_buildlist_fail' 		=> 'нет построек',
  'sys_gain' 			=> 'Добыча: ',
  'sys_debris' 			=> 'Обломки: ',
  'sys_noaccess' 			=> 'В доступе отказано',
  'sys_noalloaw' 			=> 'Вам закрыт доступ в эту зону!',
  'sys_governor'        => 'Губернатор',

  'flt_error_duration_wrong' => 'Невозможно отправить флот - нет доступных интервалов для задержки. Изучите еще уровни Астрокартографии',
  'flt_stay_duration' => 'Время',

  'flt_mission_expedition' => [
    'msg_sender' => 'Отчет экспедиции',
    'msg_title' => 'Отчет экспедиции',

    'found_dark_matter_new' => 'Получена ТМ:',
    'found_resources_new' => "Найдены ресурсы:",
    'found_fleet_new' => "Найдены корабли:",
    'lost_fleet_new' => "Потеряны следующие корабли:",

    'found_dark_matter' => 'Получено %1$d единиц ТМ',
    'found_resources' => "Найдены ресурсы:\r\n",
    'found_fleet' => "Найдены корабли:\r\n",
    'lost_fleet' => "Потеряны следующие корабли:\r\n",
    'outcomes' => [
      FLT_EXPEDITION_OUTCOME_NONE => [
        'messages' => [
          'Ваши исследователи ничего не обнаружили',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_LOST_FLEET => [
        'messages' => [
          'Флот попал в черную дыру и частично утерян',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL => [
        'messages' => [
          'Если бы вы только это видели! Оно такое красивое... Оно зовёт к себе... (связь с флотом утеряна)',
          // 'Отчёт флота %1$s. Мы завершили исследование сектора. Команда недовольна Эй, ты что делаешь на мостике?! (связь с флотом утеряна)',
          'Отчёт флота %1$s. Всё спокойно (помехи) (связь с флотом утеряна)',
          'АААААА! ЧТО ЭТО?! ОТКУДА ОНО ВЗЯ (связь с флотом утеряна)',
          'Обнаружен неизвестный объект. Он не отвечает на запросы стандартных протоколов. Высылаем зонд для проведения исследований (связь с флотом утеряна)',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_FLEET => [
        'no_result' => 'К сожалению, совокупной мощности всех компьютеров флота не хватило даже на контроль самого мелкого корабля. Попробуйте отправлять больше кораблей и/или более крупные корабли',
        'messages' => [
          0 => [
            'Вы нашли абсолютно новый флот',
          ],
          1 => [
            'Вы нашли флот',
          ],
          2 => [
            'Вы нашли б/у флот',
          ],
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => [
        'no_result' => 'Трюмы вашего флота оказались неспособны вместить хоть один контейнер с ресурсами. Попробуйте отправлять флот с большим количеством транспортников',
        'messages' => [
          0 => [
            'Вы нашли пиратский клад с ресурсами. Сколько же кораблей было уничтожено, что бы собрать столько добра?',
          ],
          1 => [
            'Вы нашли заброшенную астероидную базу. Интересно, куда делись её обитатели? Исследовав руины, вы нашли несколько уцелевших складов с ресурсами',
          ],
          2 => [
            'Вы наткнулись на уничтоженный транспортный конвой. Обыскав трюмы разбитых кораблей, вы обнаружили немного ресурсов',
          ],
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_DM => [
        'no_result' => 'К сожалению, всех накопителей флота не хватило что бы собрать одну-единственую ТМ. Попробуйте отправлять флот побольше',
        'messages' => 'Ваш флот стал свидетелем рождения СуперНовы',
        // 'messages' => array(
        //   'Ваш флот стал свидетелем рождения СуперНовы 1',
        //   'Ваш флот стал свидетелем рождения СуперНовы 2',
        //   'Ваш флот стал свидетелем рождения СуперНовы 3',
        // ),
      ],

    ],
  ],

  // News page & a bit of imperator page
  'news_fresh'      => 'Свежие новости',
  'news_all'        => 'Все новости',
  'news_title'      => 'Новости',
  'news_none'       => 'Нет новостей',
  'news_new'        => 'СВЕЖАЯ',
  'news_future'     => 'АНОНС',
  'news_more'       => 'Подробнее...',
  'news_hint'       => 'Что бы убрать список последних новостей - прочтите их все, кликнув на заголовке "[ Новости ]"',

  'news_date'       => 'Дата',
  'news_announce'   => 'Содержание',
  'news_detail_url' => 'Ссылка на подробности',
  'news_mass_mail'  => 'Разослать новость всем игрокам',

  'news_total'      => 'Всего новостей: ',

  'news_add'        => 'Добавить новость',
  'news_edit'       => 'Редактировать новость',
  'news_copy'       => 'Скопировать новость',
  'news_mode_new'   => 'Новая',
  'news_mode_edit'  => 'Редактирование',
  'news_mode_copy'  => 'Копия',

  'sys_administration' => 'Администрация сервера',

  'note_add'        => 'Добавить заметку',
  'note_del'        => 'Удалить заметку',
  'note_edit'        => 'Изменить заметку',

  // Shortcuts
  'shortcut_title'     => 'Закладки',
  'shortcut_none'      => 'Нет закладок',
  'shortcut_new'       => 'НОВАЯ',
  'shortcut_text'      => 'Текст',

  'shortcut_add'       => 'Добавить закладку',
  'shortcut_edit'      => 'Редактировать закладку',
  'shortcut_copy'      => 'Скопировать закладку',
  'shortcut_mode_new'  => 'Новая',
  'shortcut_mode_edit' => 'Редактирование',
  'shortcut_mode_copy' => 'Копия',

  // Missile-related
  'mip_h_launched'			=> 'Запуск межпланетных ракет',
  'mip_launched'				=> 'Запущено межпланетных ракет: <b>%s</b>!',

  'mip_no_silo'				=> 'Недостаточен уровень ракетных шахт на планете <b>%s</b>.',
  'mip_no_impulse'			=> 'Необходимо исследовать импульсный двигатель.',
  'mip_too_far'				=> 'Ракета не может лететь так далеко.',
  'mip_planet_error'			=> 'Ошибка - больше одной планеты по одной координате',
  'mip_no_rocket'				=> 'Недостаточно ракет в шахте для проведения атаки.',
  'mip_hack_attempt'			=> ' Ты чо хакер? Еще один такой прикол и будешь забанен. ip адрес и логин я записал.',

  'mip_all_destroyed' 		=> 'Все межпланетные ракеты были уничтожены ракетами-перехватчиками<br>',
  'mip_destroyed'				=> '%s межпланетых ракет были уничтожены ракетами-перехватчиками.<br>',
  'mip_defense_destroyed'	=> 'Уничтожены следующие оборонительные сооружения:<br />',
  'mip_recycled'				=> 'Переработано из обломков защитных сооружений: ',
  'mip_no_defense'			=> 'На атакуемой планете не было защиты!',

  'mip_sender_amd'			=> 'Ракетно-космические войска',
  'mip_subject_amd'			=> 'Ракетная атака',
  'mip_body_attack'			=> 'Атака межпланетными ракетами (%1$s шт.) с планеты %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> на планету %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',

  // Misc
  'sys_game_rules' => 'Правила игры',
  'sys_game_documentation' => 'Описание игры',
  'sys_banned_msg' => 'Вы забанены. Для получения информации зайдите <a href="banned.php">сюда</a>. Срок окончания блокировки аккаунта: ',
  'sys_total_time' => 'Общее время',
  'sys_total_time_short' => 'Очередь',
  'eco_que_finish' => 'Завершение',

  // Universe
  'uni_moon_of_planet' => 'планеты',

  // Combat reports
  'cr_view_title'  => "Просмотр боевых отчетов",
  'cr_view_button' => "Просмотреть отчет",
  'cr_view_prompt' => "Введите код",
  'cr_view_my'     => "Мои боевые отчеты",
  'cr_view_hint'   => '<ul><li>Свои боевые отчеты можно посмотреть, кликнув по ссылке "Мои боевые отчеты" в заголовке</li><li>Код боевого отчета указывается в его последней строке и является последовательностью 32 цифр и символов латинского алфавита</li></ul>',

  // Fleet
  'flt_gather_all'    => 'Свезти ресурсы',

  // Ban system
  'ban_title'      => 'Чёрный список',
  'ban_name'       => 'Имя',
  'ban_reason'     => 'Причина блокировки',
  'ban_from'       => 'Дата блокировки',
  'ban_to'         => 'Срок блокировки',
  'ban_by'         => 'Выдал',
  'ban_no'         => 'Нет заблокированных игроков',
  'ban_thereare'   => 'Всего',
  'ban_players'    => 'заблокировано',
  'ban_banned'     => 'Игроков заблокировано: ',

  // Contacts
  'ctc_title' => 'Администрация',
  'ctc_intro' => 'Здесь вы найдёте адреса всех администраторов и операторов игры для обратной связи',
  'ctc_name'  => 'Имя',
  'ctc_rank'  => 'Звание',
  'ctc_mail'  => 'eMail',

  // Records page
  'rec_title'  => 'Рекорды Вселенной',
  'rec_build'  => 'Постройки',
  'rec_specb'  => 'Специальные постройки',
  'rec_playe'  => 'Игрок',
  'rec_defes'  => 'Оборона',
  'rec_fleet'  => 'Флот',
  'rec_techn'  => 'Технологии',
  'rec_level'  => 'Уровень',
  'rec_nbre'   => 'Количество',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => 'Интернет',
  'cred_site'    => 'Сайт',
  'cred_forum'   => 'Форум',
  'cred_credit'  => 'Авторы',
  'cred_creat'   => 'Директор',
  'cred_prog'    => 'Программист',
  'cred_master'  => 'Ведущий',
  'cred_design'  => 'Дизайнер',
  'cred_web'     => 'Вебмастер',
  'cred_thx'     => 'Благодарности',
  'cred_based'   => 'Основа для создания XNova',
  'cred_start'   => 'Место дебюта XNova',

  // Built-in chat
  'chat_common'   => 'Общий чат',
  'chat_ally'     => 'Чат Альянса',
  'chat_history'  => 'История чата',
  'chat_message'  => 'Сообщение',
  'chat_send'     => 'Отправить',
  'chat_page'     => 'Страница',
  'chat_timeout'  => 'Чат отключен из-за вашей неактивности. Обновите страницу.',

  // ----------------------------------------------------------------------------------------------------------
  // Interface of Jump Gate
  'gate_start_moon' => 'Начальная Луна',
  'gate_dest_moon'  => 'Конечная Луна',
  'gate_use_gate'   => 'Использовать врата',
  'gate_ship_sel'   => 'Выделить корабли',
  'gate_ship_dispo' => 'доступно',
  'gate_jump_btn'   => 'Выполнить прыжок!!',
  'gate_jump_done'  => 'врата находятся в стадии перезарядки!<br>Врата будут готовы к использованию через: ',
  'gate_wait_dest'  => 'Точка назначения врат в стадии подготовки! Врата будут готовы к использованию через: ',
  'gate_no_dest_g'  => 'На конечной точке назначения не обнаруженно врат для перемещения флота',
  'gate_no_src_ga'  => 'Нет врат для перемещения флота',
  'gate_wait_star'  => 'врата находятся в стадии перезарядки!<br>ворота будут готовы к использованию через: ',
  'gate_wait_data'  => 'Ошибка, нет данных для прыжка!',
  'gate_vacation'   => 'Ошибка, Вы не можете совершить прыжок т.к. находитесь в Режиме Отпуска !',
  'gate_ready'      => 'Врата готовы к прыжку',

  // quests
  'qst_quests'               => 'Квесты',
  'qst_msg_complete_subject' => 'Квест закончен',
  'qst_msg_complete_body'    => 'Вы выполнили квест "%s".',
  'qst_msg_your_reward'      => 'Ваша награда:',

  // Messages
  'msg_from_admin' => 'Администрация Вселенной',
  'msg_class' => [
    MSG_TYPE_OUTBOX => 'Отправленные сообщения',
    MSG_TYPE_SPY => 'Шпионские отчёты',
    MSG_TYPE_PLAYER => 'Сообщения от игроков',
    MSG_TYPE_ALLIANCE => 'Сообщения альянса',
    MSG_TYPE_COMBAT => 'Военные отчёты',
    MSG_TYPE_RECYCLE => 'Отчеты переработки',
    MSG_TYPE_TRANSPORT => 'Прибытие флота',
    MSG_TYPE_ADMIN => 'Сообщения Администрации',
    MSG_TYPE_EXPLORE => 'Отчёты экспедиций',
    MSG_TYPE_QUE => 'Сообщения очереди построек',
    MSG_TYPE_NEW => 'Все сообщения',
  ],

  'msg_que_research_from'    => 'Научно-исследовательский институт',
  'msg_que_research_subject' => 'Новая технология',
  'msg_que_research_message' => 'Исследована новая технология \'%s\'. Новый уровень - %d',

  'msg_que_planet_from'    => 'Губернатор',

  'msg_que_hangar_subject' => 'Работа на верфи завершена',
  'msg_que_hangar_message' => "Верфь на %s завершила работу",

  'msg_que_built_subject'   => 'Планетарные работы завершены',
  'msg_que_built_message'   => "Завершено строительство здания '%2\$s' на %1\$s. Построено уровней: %3\$d",
  'msg_que_destroy_message' => "Завершено разрушение здания '%2\$s' на %1\$s. Разрушено уровней: %3\$d",

  'msg_personal_messages' => 'Личные сообщения',

  'sys_opt_bash_info'    => 'Настройки системы антибашинга',
  'sys_opt_bash_attacks' => 'Количество атак в одной волне',
  'sys_opt_bash_interval' => 'Интервал между волнами',
  'sys_opt_bash_scope' => 'Период расчета башинга',
  'sys_opt_bash_war_delay' => 'Мораторий после объявления войны',
  'sys_opt_bash_waves' => 'Количество волн за один период',
  'sys_opt_bash_disabled'    => 'Система антибашинга отключена',

  'sys_id' => 'ИД',
  'sys_identifier' => 'Идентификатор',

  'sys_email'   => 'Е-Мейл',
  'sys_ip' => 'IP',

  'sys_max' => 'Макс',
  'sys_maximum' => 'Максимум',
  'sys_maximum_level' => 'Максимальный уровень',

  'sys_user_name' => 'Имя пользователя',
  'sys_player_name' => 'Имя игрока',
  'sys_user_name_short' => 'Имя',

  'sys_planets' => 'Планеты',
  'sys_moons' => 'Луны',

  'sys_quantity' => 'Количество',
  'sys_quantity_maximum' => 'Максимальное количество',
  'sys_qty' => 'К-во',
  'sys_quantity_total' => 'Общее количество',

  'sys_buy_for' => 'Купить за',
  'sys_buy' => 'Купить',
  'sys_for' => 'за',

  'sys_eco_lack_dark_matter' => 'Не хватает Тёмной Материи',

  'time_local' => 'Время у игрока',
  'time_server' => 'Время на сервере',

  'sys_result' => [
    'error_dark_matter_not_enough' => 'Не хватает Тёмной Материи для завершения операции',
    'error_dark_matter_change' => 'Ошибка изменения количества Тёмной Материи! Повторите операцию еще раз. Если ошибка повторится - сообщите Администрации сервера',
  ],

  // Arrays
  'sys_build_result' => [
    BUILD_ALLOWED => 'Можно построить',
    BUILD_REQUIRE_NOT_MEET => 'Требования не удовлетворены',
    BUILD_AMOUNT_WRONG => 'Слишком много',
    BUILD_QUE_WRONG => 'Несуществующая очередь',
    BUILD_QUE_UNIT_WRONG => 'Неправильная очередь',
    BUILD_INDESTRUCTABLE => 'Нельзя уничтожить',
    BUILD_NO_RESOURCES => 'Не хватает ресурсов',
    BUILD_NO_UNITS => 'Нет юнитов',
    BUILD_UNIT_BUSY => [
      0 => 'Строение занято',
      STRUC_LABORATORY => 'Идет исследование',
      STRUC_LABORATORY_NANO => 'Идет исследование',
    ],
    BUILD_QUE_FULL => 'Очередь полна',
    BUILD_SILO_FULL => 'Ракетная шахта заполнена',
    BUILD_MAX_REACHED => 'Вы уже построили и/или поставили в очередь максимальное количество юнитов данного типа',
    BUILD_SECTORS_NONE => 'Нет свободных секторов',
    BUILD_AUTOCONVERT_AVAILABLE => 'Доступна автоконвертация',
    BUILD_HIGHSPOT_NOT_ACTIVE => 'Ивент не активен',
  ],

  'sys_game_mode' => [
    GAME_SUPERNOVA => 'СуперНова',
    GAME_OGAME     => 'оГейм',
    GAME_BLITZ     => 'Блиц-сервер',
  ],

  'months' => [
     1 =>'января',
     2 =>'февраля',
     3 =>'марта',
     4 =>'апреля',
     5 =>'мая',
     6 =>'июня',
     7 =>'июля',
     8 =>'августа',
     9 =>'сентября',
    10 =>'октября',
    11 =>'ноября',
    12 =>'декабря'
  ],

  'weekdays' => [
    0 => 'Воскресенье',
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота'
  ],

  'user_level' => [
    0 => 'Игрок',
    1 => 'Модератор',
    2 => 'Оператор',
    3 => 'Администратор',
    4 => 'Разработчик',
  ],

  'user_level_shortcut' => [
    0 => 'И',
    1 => 'М',
    2 => 'О',
    3 => 'А',
    4 => 'Р',
  ],

  'sys_lessThen15min'   => '&lt; 15 мин',

  'sys_no_points'         => 'У вас недостаточно <span class="dark_matter">Тёмной Материи</span>!',
  'sys_dark_matter_obtain_header' => 'Как получить <span class="dark_matter">Тёмную Материю</span>',
  'sys_dark_matter_desc' => 'Тёмная материя - необнаружимая стандартными методами небарионная материя, на которую приходится 23% массы Вселенной. Из неё можно добывать невероятное количество энергии. Из-за этого, а так же из-за сложностей, связанных с её добычей, Тёмная Материя ценится очень высоко.',
  'sys_dark_matter_hint' => 'При помощи этой субстанции можно нанять офицеров и командиров.',

  'sys_dark_matter_what_why_how' => 'Что такое <span class="dark_matter">Тёмная Материя</span> и <span class="metamatter">Метаматерия</span>',
  'sys_dark_matter_what_header' => 'Что такое <span class="dark_matter">Тёмная Материя</span>',
  'sys_dark_matter_description_header' => 'Зачем нужна <span class="dark_matter">Тёмная Материя</span>',
  'sys_dark_matter_description_text' => '<span class="dark_matter">Тёмная Материя</span> - это внутриигровой ресурс, за счет которой в игре вы можете совершать различные операции:
    <ul>
      <li>Покупать <a href="index.php?page=premium"><span class="link">Премиум-аккаунт</span></a></li>
      <li>Рекрутировать <a href="officer.php?mode=600"><span class="link">Наемников</span></a> в Империю </li>
      <li>Нанимать Губернаторов и покупать дополнительные сектора <a href="overview.php?mode=manage"><span class="link">на планеты</span></a></li>
      <li>Покупать <a href="officer.php?mode=1100"><span class="link">Чертежи</span></a></li>
      <li>Покупать <a href="artifacts.php"><span class="link">Артефакты</span></a></li>
      <li>Использовать <a href="market.php"><span class="link">Чёрный Рынок</span></a>: Обменивать один вид ресурсов на другой; продавать корабли; покупать Б/У корабли итд</li>
      <li>...и многое, многое другое</li>
    </ul>',
  'sys_dark_matter_obtain_text' => 'Вы получаете <span class="metamatter">Тёмную Материю</span> в процессе игры: набирая опыт за успешные рейды на чужие планеты, исследование новых технологий, а так же за постройку и разрушение зданий.
    Так же иногда исследовательские экспедиции могут принести <span class="metamatter">ТМ</span>.',

  // 'sys_dark_matter_obtain_text_convert' => '<br /><br />Кроме того, вы можете сконвертировать Метаматерию в Тёмную Материю. <a href="metamatter.php" class="link">Узнать подробнее про Метаматерию</a>',
  'sys_dark_matter_obtain_text_convert' => '<br />Если вам не хватает <span class="dark_matter">Тёмной Материи</span> - приобретите <span class="metamatter">Метаматерию</span>. В случае недостатка <span class="dark_matter">ТМ</span> нужное количество <span class="metamatter">Метаматерии</span> будет использована вместо <span class="dark_matter">ТМ</span>',

  'sys_msg_err_update_dm' => 'Ошибка обновления количества ТМ!',

  'sys_na' => 'Не доступен',
  'sys_na_short' => 'Н/Д',

  'sys_ali_res_title' => 'Ресурсы Альянса',

  'sys_bonus' => 'Бонус',

  'sys_of_ally' => 'Альянса',

  'sys_hint_player_name' => 'Поиск игрока может производиться по идентификатору или имени. Если имя игрока состоит из нечитаемых символов или только из цифр - для поиска нужно использовать идентификатор',
  'sys_hint_ally_name' => 'Поиск Альянса может производиться по идентификатору, тэгу или имени. Если тэг или название Альянса состоят из нечитаемых символов или только из цифр - для поиска нужно использовать идентификатор',

  'sys_fleet_and' => '+ флоты',

  'sys_on_planet' => 'На планете',
  'fl_on_stores' => 'На складе',

  'sys_ali_bonus_members' => 'Минимальное размер Альянса для получения бонуса',

  'sys_premium' => 'Премиум',

  'mrc_period_list' => [
    PERIOD_MINUTE    => '1 минута',
    PERIOD_MINUTE_3  => '3 минуты',
    PERIOD_MINUTE_5  => '5 минут',
    PERIOD_MINUTE_10 => '10 минут',
    PERIOD_DAY       => '1 день',
    PERIOD_DAY_3     => '3 дня',
    PERIOD_WEEK      => '1 неделя',
    PERIOD_WEEK_2    => '2 недели',
    PERIOD_MONTH     => '30 дней',
    PERIOD_MONTH_2   => '60 дней',
    PERIOD_MONTH_3   => '90 дней',
  ],

  'sys_sector_buy' => 'Купить 1 сектор',

  'sys_select_confirm' => 'Подвердить выбор',

  'sys_capital' => 'Столица',

  'sys_result_operation' => 'Сообщения',

  'sys_password' => 'Пароль',
  'sys_password_length' => 'Длина пароля',
  'sys_password_seed' => 'Используемые символы',

  'sys_msg_ube_report_err_not_found' => 'Боевой отчет не найден. Проверьте правильность ключа. Так же есть вероятность, что отчет удален как устаревший',

  'sys_mess_attack_report' 	=> 'Боевой отчет',
  'sys_perte_attaquant' 		=> 'Атакующий потерял',
  'sys_perte_defenseur' 		=> 'Обороняющийся потерял',



  'ube_report_info_page_header' => 'Боевой отчёт',
  'ube_report_info_page_header_cypher' => 'Код допуска',
  'ube_report_info_main' => 'Основная информация о бое',
  'ube_report_info_date' => 'Дата и время',
  'ube_report_info_location' => 'Место',
  'ube_report_info_rounds_number' => 'Количество раундов',
  'ube_report_info_outcome' => 'Результат боя',
  'ube_report_info_outcome_win' => 'Атакующий выиграл бой',
  'ube_report_info_outcome_loss' => 'Атакующий проиграл бой',
  'ube_report_info_outcome_draw' => 'Бой закончился ничьей',
  'ube_report_info_link' => 'Ссылка на боевой отчет',
  'ube_report_info_bbcode' => 'BBCode для вставки в чат',
  'ube_report_info_sfr' => 'Бой закончился за один раунд проигрышем атакующего<br />Вероятна РМФ',
  'ube_report_info_debris' => 'Обломки на орбите',
  'ube_report_info_debris_simulator' => '(не считая создания Луны)',
  'ube_report_info_loot' => 'Добыча',
  'ube_report_info_loss' => 'Боевые потери',
  'ube_report_info_generate' => 'Время генерации страницы',

  'ube_report_moon_was' => 'У этой планеты уже была луна',
  'ube_report_moon_chance' => 'Шанс образования луны',
  'ube_report_moon_created' => 'На орбите планеты образовалась луна диаметром',

  'ube_report_moon_reapers_none' => 'Все корабли с гравитационными двигателями были уничтожены в процессе боя',
  'ube_report_moon_reapers_wave' => 'Корабли атакующего создали сфокусированную гравитационную волну',
  'ube_report_moon_reapers_chance' => 'Шанс уничтожения луны',
  'ube_report_moon_reapers_success' => 'Луна уничтожена',
  'ube_report_moon_reapers_failure' => 'Мощности волны не хватило для уничтожения луны',

  'ube_report_moon_reapers_outcome' => 'Шанс взрыва двигателей',
  'ube_report_moon_reapers_survive' => 'Точная компенсация гравитационных полей системы позволила погасить отдачу от разрушения луны',
  'ube_report_moon_reapers_died' => 'Не сумев компенсировать добавочные гравитационные поля системы, флот был уничтожен',

  'ube_report_side_attacker' => 'Атакующий',
  'ube_report_side_defender' => 'Защитник',

  'ube_report_round' => 'Раунд',
  'ube_report_unit' => 'Боевая единица',
  'ube_report_attack' => 'Атака',
  'ube_report_shields' => 'Щиты',
  'ube_report_shields_passed' => 'Пробой',
  'ube_report_armor' => 'Броня',
  'ube_report_damage' => 'Урон',
  'ube_report_loss' => 'Потери',


  'ube_report_info_restored' => 'Восстановленно оборонительных сооружений',
  'ube_report_info_loss_final' => 'Итоговые потери боевых единиц',
  'ube_report_info_loss_resources' => 'Потери в пересчете на ресурсы',
  'ube_report_info_loss_dropped' => 'Потери ресурсов из-за уменьшения трюмов',
  'ube_report_info_loot_lost' => 'Увезено ресурсов со складов планеты',
  'ube_report_info_loss_gained' => 'Потери из-за вывоза ресурсов с планеты',
  'ube_report_info_loss_in_metal' => 'Общие потери в пересчете на металл',


  'ube_report_msg_body_common' => 'Бой состоялся %s на орбите %s [%d:%d:%d] %s<br />%s<br /><br />',
  'ube_report_msg_body_debris' => 'В результат боя на орбите планеты образовались обломки:<br />',
  'ube_report_msg_body_sfr' => 'Связь с флотом утеряна',

  'ube_report_capture' => 'Захват планеты',
  'ube_report_capture_result' => [
    UBE_CAPTURE_DISABLED => 'Захват планет отключён',
    UBE_CAPTURE_NON_PLANET => 'Захватывать можно только планеты',
    UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND => 'Для захвата планеты бой должен закончиться победой в первом раунде',
    UBE_CAPTURE_TOO_MUCH_FLEETS => 'При захвате планеты в бою должен участвовать только флот-захватчик и планетарный флот',
    UBE_CAPTURE_NO_ATTACKER_USER_ID => 'ВНУТРЕННЯЯ ОШИБКА - Нет ИД атакующего! Сообщите разработчику!',
    UBE_CAPTURE_NO_DEFENDER_USER_ID => 'ВНУТРЕННЯЯ ОШИБКА - Нет ИД защитника! Сообщите разработчику!',
    UBE_CAPTURE_CAPITAL => 'Нельзя захватывать столицу',
    UBE_CAPTURE_TOO_LOW_POINTS => 'Можно захватывать планеты только у игроков, чье общее количество очков не менее чем в 2 раза больше количество очков у атакующего',
    UBE_CAPTURE_NOT_ENOUGH_SLOTS => 'Больше нет слотов захвата планеты',
    UBE_CAPTURE_SUCCESSFUL => 'Планета захвачена атакующим игроком',
  ],

  'sys_kilometers_short' => 'км',

  'ube_simulation' => 'Симуляция',

  'sys_hire_do' => 'Нанять',

  'sys_captains' => 'Капитаны',

  'sys_fleet_composition' => 'Состав флота',

  'sys_continue' => 'Продолжить',

  'uni_planet_density_types' => [
    PLANET_DENSITY_NONE => 'Не бывает',
    PLANET_DENSITY_ICE_HYDROGEN => 'Водородный лёд',
    PLANET_DENSITY_ICE_METHANE => 'Метановый лёд',
    PLANET_DENSITY_ICE_WATER => 'Водный лёд',
    PLANET_DENSITY_CRYSTAL_RAW => 'Кристалл',
    PLANET_DENSITY_CRYSTAL_SILICATE => 'Силикат',
    PLANET_DENSITY_CRYSTAL_STONE => 'Камень',
    PLANET_DENSITY_STANDARD => 'Стандарт',
    PLANET_DENSITY_METAL_ORE => 'Руда',
    PLANET_DENSITY_METAL_PERIDOT => 'Оливин',
    PLANET_DENSITY_METAL_RAW => 'Металл',
  ],

  'sys_planet_density' => 'Плотность',
  'sys_planet_density_units' => 'кг/м&sup3;',
  'sys_planet_density_core' => 'Тип ядра',

  'sys_change' => 'Изменить',
  'sys_show' => 'Показать',
  'sys_hide' => 'Скрыть',
  'sys_close' => 'Закрыть',
  'sys_unlimited' => 'Без ограничений',

  'ov_core_type_current' => 'Текущий тип ядра',
  'ov_core_change_to' => 'Изменить на',
  'ov_core_err_none' => 'Тип ядра планеты успешно изменен с "%s" на "%s".<br />Новая плотность планеты %d кг/м3',
  'ov_core_err_not_a_planet' => 'Только на планете можно менять плотность ядра',
  'ov_core_err_denisty_type_wrong' => 'Неправильный тип ядра',
  'ov_core_err_same_density' => 'Новый тип ядра не отличается от текущего - нечего менять',
  'ov_core_err_no_dark_matter' => 'Не хватает Тёмной Материи для смены типа ядра',

  'sys_color'    => "Цвет",

  'topnav_imp_attack' => 'Ваша Империя атакована!',
  'topnav_user_rank' => 'Ваше текущее место в рейтинговой статистике',
  'topnav_users' => 'Всего зарегистрированных игроков',
  'topnav_users_online' => 'Текущее количество игроков онлайн',

  'topnav_refresh_page' => 'Перегрузить страницу',

  'sys_colonies' => 'Колонии',
  'sys_radio' => 'Радио "Космос"',

  'sys_auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'Таблица USERS',
    ACCOUNT_PROVIDER_LOCAL => 'Таблица ACCOUNT',
    ACCOUNT_PROVIDER_CENTRAL => 'Центральная таблица ACCOUNT',
  ],

  'sys_login_messages' => [
    LOGIN_UNDEFINED => 'Процесс логина не начался',
    LOGIN_SUCCESS => 'Вход успешен',
    LOGIN_ERROR_USERNAME_EMPTY => 'Имя игрока не может быть пустым',
    LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS => 'В имени игрока и логине не допускаются символы ',
    LOGIN_ERROR_USERNAME => 'Игрок с таким именем не найден',
    LOGIN_ERROR_USERNAME_ALLY_OR_BOT => 'Это имя принадлежит Альянсу или боту. Под ним нельзя логиниться... по крайней мере пока',
    LOGIN_ERROR_PASSWORD_EMPTY => 'Пароль не может быть пустым',
    LOGIN_ERROR_PASSWORD_TRIMMED => 'Пароль не может начинаться или заканчиваться пробелом, табуляцией или символом перевода строки',
    LOGIN_ERROR_PASSWORD => 'Неправильный пароль',
  //    LOGIN_ERROR_COOKIE => '',

    REGISTER_SUCCESS => 'Регистрация успешно завершена',
    REGISTER_ERROR_BLITZ_MODE => 'Регистрация новых игроков в режиме Блиц-сервера отключена',
    REGISTER_ERROR_USERNAME_WRONG => 'Некорректное имя игрока',
    REGISTER_ERROR_ACCOUNT_NAME_EXISTS => 'Имя учётной записи уже занято. Попробуйте войти с этим именем и вашим паролем или воспользоваться сбросом пароля',
    REGISTER_ERROR_PASSWORD_INSECURE => 'Неправильный пароль. Пароль должен состоять минимум из ' . PASSWORD_LENGTH_MIN . ' символов',
    REGISTER_ERROR_USERNAME_SHORT => 'Слишком короткое имя. Имя должно состоять минимум из ' . LOGIN_LENGTH_MIN. ' символов',
    REGISTER_ERROR_PASSWORD_DIFFERENT => 'Пароль и проверочный пароль не совпадают. Проверьте правильность ввода',
    REGISTER_ERROR_EMAIL_EMPTY => 'Е-Мейл не может быть пустым',
    REGISTER_ERROR_EMAIL_WRONG => 'Введенный Е-Мейл не является допустимым адресом электронной почты. Проверьте написание Е-Мейла или используйте другой адрес почтового ящика',
    REGISTER_ERROR_EMAIL_EXISTS => 'Этот электронный адрес уже зарегестрирован. Если вы уже регистрировались в игре - попробуйте воспользоваться сбросом пароля. Иначе - используйте другой почтовый ящик',

    PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS => 'Нет игрока с таким основным емейлом',
    PASSWORD_RESTORE_ERROR_TOO_OFTEN => 'Запросить код восстановления можно только 1 раз в 10 минут. Если вы не получили письмо - проверьте папку СПАМа или напишите письмо Администрации сервера на email <span class="ok">' . $config->server_email . '</span> с адреса, который вы использовали при регистрации',
    PASSWORD_RESTORE_ERROR_SENDING => 'Ошибка отправки письма. Напишите письмо Администрации сервера на email <span class="ok">' . $config->server_email . '</span>',
    PASSWORD_RESTORE_SUCCESS_CODE_SENT => 'Письмо с кодом восстановления успешно отправлено',

    PASSWORD_RESTORE_ERROR_CODE_EMPTY => 'Код восстановления не может быть пустым',
    PASSWORD_RESTORE_ERROR_CODE_WRONG => 'Неправильный код восстановления',
    PASSWORD_RESTORE_ERROR_CODE_TOO_OLD => 'Код восстановления устарел. Получите новый',
    PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL => 'Код восстановления указан верно, однако не найдено ни одного аккаунта с таким емейлом. Возможно, он был удалён или произошла внутренняя ошибка. Обратитесь к Администрации сервера',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT => 'Пароль успешно сброшен. Вам отправлено письмо с новым паролем',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR => 'Ошибка отправки письма с новым паролем. Получите новый код восстановления и повторите попытку',

    REGISTER_ERROR_PLAYER_NAME_TRIMMED => 'Имя игрока не может начинаться или заканчиваться пробельными символами (символы "Пробел", "Табуляция", "Перенос строки" итд)',
    REGISTER_ERROR_PLAYER_NAME_EMPTY => 'Имя игрока не может быть пустым',
    REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS => 'Имя игрока содержит запрещенные символы',
    REGISTER_ERROR_PLAYER_NAME_SHORT => 'Имя игрока не может быть короче ' . LOGIN_LENGTH_MIN . ' символов',
    REGISTER_ERROR_PLAYER_NAME_EXISTS => 'Это имя игрока уже занято. Пожалуйста, выберите другое',

    // Внутренние ошибки
    AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE => 'ВНУТРЕННЯЯ ОШИБКА! СООБЩИТЕ АДМИНИСТРАЦИИ! Ошибка смены пароля. Пожалуйста, сообщите об этой ошибке Администрации Вселенной!',
    PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT => 'Запрещено восстановление пароля для Команды сервера. Обратитесь к Администратору',
    REGISTER_ERROR_ACCOUNT_CREATE => 'Ошибка создания учётной записи! Пожалуйста, сообщите об этом Администрации!',
    LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION => 'СИСТЕМНАЯ ОШИБКА - СБОЙ В ТАБЛИЦЕ ТРАНСЛЯЦИИ ПРОВАЙДЕРОВ! Сообщите администрации сервера!',
    PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS => 'Внутренняя ошибка - при смене пароля аккаунт не найден! Сообщите об этой ошибке Администрации!',
    AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION => 'ВНУТРЕННЯЯ ОШИБКА! Нет аккаунтов для сброса пароля при корректном коде подтверждения! Пожалуйста, сообщите об этой ошибке Администрации Вселенной!',
    LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET => 'ВНУТРЕННЯЯ ОШИБКА! СООБЩИТЕ АДМИНИСТРАЦИИ! Не установлен аккаунт при cookie_set()! Пожалуйста, сообщите об этой ошибке Администрации Вселенной!',
  ],

  'log_reg_email_title' => "Ваша регистрация на сервере %1\$s игры СуперНова",
  'log_reg_email_text' => "Подтверждение регистрации для %3\$s\r\n\r\n
  Это письмо содержит Ваши регистрационные данные на сервере %1\$s игры СуперНова\r\n
  Сохраните эти данные в безопасном месте\r\n\r\n
  Адрес сервера: %2\$s\r\n
  Ваш логин: %3\$s\r\n
  Ваш пароль: %4\$s\r\n\r\n
  Спасибо за регистрацию на нашем сервере! Желаем Вам удачи в игре!\r\n
  Администрация сервера %1\$s %2\$s\r\n\r\n
  Сервер работает на свободном движке 'Project SuperNova.WS'. Зажги свою СуперНову http://supernova.ws/",

  'log_lost_email_title' => 'СуперНова, Вселенная %s: Сброс пароля',
  'log_lost_email_code' => "Кто-то (может быть Вы) запросил сброс пароля во Вселенной %1\$4 игры СуперНова. Если Вы не запрашивали сброс пароля - просто проигнорируйте это письмо.\r\n\r\nДля сброса пароля перейдите по адресу \r\n%1\$s?password_reset_confirm=1&password_reset_code=%2\$s#tab_password_reset\r\n или введите код подтверждения \"%2\$s\" (БЕЗ ДВОЙНЫХ КАВЫЧЕК!) на странице %1\$s#tab_password_reset\r\n\r\nЭтот код будет действителен до %3\$s. После указанного срока для сброса пароля Вам нужно будет запросить новый код подтверждения",
  'log_lost_email_pass' => "Вы сбросили пароль на сервере %1\$s игры 'СуперНова'.\r\n\r\nВаше имя для входа в игру:\r\n%2\$s\r\n\r\nВаш новый пароль для входа в игру:\r\n%3\$s\r\n\r\nЗапомните его!\r\n\r\nВы можете войти в игру по ссылке " . SN_ROOT_VIRTUAL . "login.php используя имя и пароль, указанные выше",

  'login_player_register_player_name' => 'Имя игрока',
  'login_player_register_description' => 'Остался всего один шаг! Выберите имя игрока - имя, которое будет показываться другим другим игрокам в этой Вселенной',
  'login_player_register_do' => 'Выбрать имя',
  'login_player_register_logout' => 'Зайти под другим аккаунтом',
  'login_player_register_logout_description' => 'Если вы хотите зайти под другим аккаунтом - нажмите кнопку',

  'sys_password_reset_message_body' => "Вы сбросили пароль для доступа в игру в этой Вселенной.\r\n\r\nВаш новый пароль для входа в игру:\r\n\r\n%1\$s\r\n\r\nЗапомните его!\r\n\r\nВы можете в любой момент сменить пароль на более удобный для вас в пункте меню 'Настройки'.",

  'sys_login_password_show' => 'Показать пароль',
  'sys_login_password_hide' => 'Скрыть пароль',
  'sys_password_repeat' => 'Повторите пароль',

  'sys_game_disable_reason' => [
    GAME_DISABLE_NONE => 'Игра включена',
    GAME_DISABLE_REASON => 'Игра отключена. Игроки увидят сообщение',
    GAME_DISABLE_UPDATE => 'Игра обновляется',
    GAME_DISABLE_STAT => 'Происходит пересчет статистики',
    GAME_DISABLE_INSTALL => 'Игра еще не сконфигурирована',
    GAME_DISABLE_MAINTENANCE => 'Техобслуживание базы данных сервера',
    GAME_DISABLE_EVENT_BLACK_MOON => 'Чёрная Луна!',
    GAME_DISABLE_EVENT_OIS => 'Объекты в космосе',
  ],

  'sys_sector_purchase_log' => 'Пользователь {%2$d} {%1$s} купил 1 сектор на планете {%5$d} {%3$s} тип "%4$s" за %6$d ТМ',

  'sys_notes' => 'Заметки',
  'sys_notes_priorities' => [
    0 => 'Совсем не важная',
    1 => 'Не важная',
    2 => 'Обычная',
    3 => 'Важная',
    4 => 'Очень важная',
  ],

  'sys_milliseconds' => 'миллисекунд',

  'sys_gender' => 'Пол',
  'sys_gender_list' => [
    GENDER_UNKNOWN => 'Вырастет - само решит',
    GENDER_MALE => 'Мужской',
    GENDER_FEMALE => 'Женский',
  ],

  'imp_stat_header' => 'График изменений данных статистики',
  'imp_stat_types' => [
    'TOTAL_RANK' => 'Место в общей статистике',
    'TOTAL_POINTS' => 'Общее количество очков',
    // 'TOTAL_COUNT' => 'Общее количество ресурсов',
    'TECH_RANK' => 'Место в статистике по Исследованиям',
    'TECH_POINTS' => 'Количество очков за Исследования',
    // 'TECH_COUNT' => 'Количество уровней',
    'BUILD_RANK' => 'Место в статистике по Постройкам',
    'BUILD_POINTS' => 'Количество очков за Постройки',
    // 'BUILD_COUNT' => '',
    'DEFS_RANK' => 'Место в статистике по Обороне',
    'DEFS_POINTS' => 'Количество очков за Оборону',
    //'DEFS_COUNT' => '',
    'FLEET_RANK' => 'Место в статистике по Кораблям',
    'FLEET_POINTS' => 'Количество очков за Корабли',
    //'FLEET_COUNT' => '',
    'RES_RANK' => 'Место в статистике по свободным ресурсам',
    'RES_POINTS' => 'Количество очков за свободные ресурсы',
    //'RES_COUNT' => '',
  ],

  'sys_date' => 'Дата',

  'sys_blitz_global_button' => 'Блиц-сервер',
  'sys_blitz_page_disabled' => 'В режиме Блиц-сервера эта страница недоступна',
  'sys_blitz_registration_disabled' => 'Регистрация на игру в Блиц-сервер отключена',
  'sys_blitz_registration_no_users' => 'Нет зарегестрированных игроков',
  'sys_blitz_registration_player_register' => 'Зарегестрироваться для игры',
  'sys_blitz_registration_player_register_un' => 'Отозвать регистрацию',
  'sys_blitz_registration_closed' => 'Регистрация пока закрыта. Попробуйте зайти позже',
  'sys_blitz_registration_player_generate' => 'Сгенерировать логины и пароли',
  'sys_blitz_registration_player_import_generated' => 'Импортировать сгенерированную строку',
  'sys_blitz_registration_player_name' => 'Ваш логин для Блиц-сервера:',
  'sys_blitz_registration_player_password' => 'Ваш пароль для Блиц-сервера:',
  'sys_blitz_registration_server_link' => 'Ссылка на Блиц-сервер',
  'sys_blitz_registration_player_blitz_name' => 'Имя на Блиц-сервере',
  'sys_blitz_registration_price' => 'Стоимость подачи заявки',
  'sys_blitz_registration_mode_list' => [
    BLITZ_REGISTER_DISABLED => 'Регистрация отключена',
    BLITZ_REGISTER_OPEN => 'Регистрация открыта',
    BLITZ_REGISTER_CLOSED => 'Регистрация закрыта',
    BLITZ_REGISTER_SHOW_LOGIN => 'Открыты логины и пароли',
    BLITZ_REGISTER_DISCLOSURE_NAMES => 'Подведение итогов',
  ],

  'survey' => 'Опрос',
  'survey_questions' => 'Варианты для выбора',
  'survey_questions_hint' => '1 вариант на строку',
  'survey_questions_hint_edit' => 'Редактированние опроса обнулит его результаты',
  'survey_until' => 'Длительность опроса (1 сутки по умолчанию)',

  'survey_votes_total_none' => 'Еще никто не проголосовал... Проголосуй первым!',
  'survey_votes_total_voted' => 'Уже проголосовало:',
  'survey_votes_total_voted_join' => 'Голосуй - или проиграешь!',
  'survey_votes_total_voted_has_answer' => 'Вы уже проголосовали. Вместе с вами проголосовавших',

  'survey_lasts_until' => 'Опрос продлится до',

  'survey_select_one' => 'Выберите один вариант ответа и нажмите',
  'survey_confirm' => 'Проголосовать!',
  'survey_result_sent' => 'Ваш голос учтен. Обновите страницу или воспользуйтесь ссылкой <a class="link" href="announce.php">Новости</a> что бы увидеть текущие результаты опроса',
  'survey_complete' => 'Опрос завершен',

  'player_option_fleet_ship_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Стандартная',
    PLAYER_OPTION_SORT_NAME => 'По названию',
    PLAYER_OPTION_SORT_ID => 'По ID',
    PLAYER_OPTION_SORT_SPEED => 'По скорости',
    PLAYER_OPTION_SORT_COUNT => 'По количеству',
  ],

  'player_option_building_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Стандартная',
    PLAYER_OPTION_SORT_NAME => 'По названию',
    PLAYER_OPTION_SORT_ID => 'По ID',
    PLAYER_OPTION_SORT_CREATE_TIME_LENGTH => 'По времени постройки',
  ],

  'sys_sort' => 'Сортировка',
  'sys_sort_inverse' => 'В обратном порядке',

  'sys_blitz_reward_log_message' => 'Блиц-сервер %1$d призовое место блиц-имя "%2$s"',
  'sys_blitz_registration_view_stat' => 'Посмотреть статистику Блиц-сервера',

  'sys_login_register_message_title' => "Ваше имя и пароль для входа в игру",
  'sys_login_register_message_body' => "Ваше имя для входа в игру (логин)\r\n%1\$s\r\n\r\nВаш пароль для входа в игру\r\n%2\$s\r\n\r\nЗапишите или запомните эти данные!",

  'auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'Таблица users',
    ACCOUNT_PROVIDER_LOCAL => 'Таблица account',
    ACCOUNT_PROVIDER_CENTRAL => 'Центральное хранилище',
  ],

  'bld_autoconvert' => 'Автоматическая конвертация при создании на планете %3$s юнита {%1$d} "%4$s" в количесте %2$d ценой "%5$s". Debug: $resource_got = "%6$s", $exchange = %7$s""',

  'news_show_rest' => 'Показать текст новости',

  'wiki_requrements' => 'Требуется',
  'wiki_grants' => 'Предоставляет',

  'que_slot_length' => 'Слоты',
  'que_slot_length_long' => 'Слоты очереди',

  'sys_buy_doing' => 'Вы покупаете',
  'sys_planet_sector' => 'сектор',
  'sys_planet_on' => 'на',

  'sys_purchase_confirm' => 'Подвердите покупку',

  'sys_confirm_action_title' => 'Подтвердите ваше действие',
  'sys_confirm_action' => 'Вы действительно хотите сделать это?',

  'sys_system_speed_original' => 'Оригинальная скорость',
  'sys_system_speed_for_action' => 'В рамках акции',

  'menu_info_best_battles' => 'Лучшие бои',

  'sys_cost' => 'Стоимость',
  'sys_price' => 'Цена',

  'sys_governor_none' => 'Губернатор не нанят',
  'sys_governor_hire' => 'Нанять Губернатора',
  'sys_governor_upgrade_or_change' => 'Улучшить или сменить Губернатора',

  'tutorial_prev' => '<< Предыдущий',
  'tutorial_next' => 'Следующий >>',
  'tutorial_finish' => 'Завершить',
  'tutorial_window' => 'Открыть в окне',
  'tutorial_window_off' => 'Вернуть на страницу',

  'tutorial_error_load' => "Ошибка загрузки туториала - попробуйте еще раз! В случае повторной ошибки - сообщите Администрации игры",
  'tutorial_error_next' => "Ошибка: Не существует следующей страницы туториала - сообщите Администрации игры",
  'tutorial_error_prev' => "Ошибка: Не существует предыдущей страницы туториала - сообщите Администрации игры",

  'sys_click_here_to_continue' => 'Кликните здесь, что бы продолжить',

  'sys_module_error_not_found' => 'Модуль наград "%1$s" не найден или отключён!',

  'rank_page_title' => 'Воинские звания',
  'rank' => 'Звание',
  'ranks' => [
    0  => 'Курсант',
    1  => 'Рекрут',
    2  => 'Рядовой',
    3  => 'Ефрейтор',
    4  => 'Капрал',
    5  => 'Сержант',
    6  => 'Старшина',
    7  => 'Гардемарин',
    8  => 'Мичман',
    9  => 'Энсин',
    10 => 'Лейтенант',
    11 => 'Капитан',
    12 => 'Майор',
    13 => 'Подполковник',
    14 => 'Полковник',
    15 => 'Контр-Адмирал',
    16 => 'Вице-Адмирал',
    17 => 'Адмирал',
    18 => 'Адмирал флота',
    19 => 'Маршал',
    20 => 'Генералиссимус',
  ],

];
