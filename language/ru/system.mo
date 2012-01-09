<?php
/*
#############################################################################
#  Filename: system.mo
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
  'sys_empire'          => 'Империя',
  'VacationMode'			=> "Ваше производство закрыто, так как вы в Отпуске",
  'sys_moon_destruction_report' => "Рапорт разрушения луны",
  'sys_moon_destroyed' => "Ваши Звёзды Смерти произвели мощную гравитационную волну, которая разрушила луну! ",
  'sys_rips_destroyed' => "Ваши Звёзды Смерти произвели мощную гравитационную волну, но её мощности оказалось не достаточно для уничтожения луны такого размера. Но гравитационная волна отразилась от лунной поверхности и разрушила ваш флот.",
  'sys_rips_come_back' => "Ваши Звёзды Смерти не имеют достаточно энергии, чтоб нанести ущерб этой луне. Ваш флот возвращается не уничтожив луну.",
  'sys_chance_moon_destroy' => "Изменение лунного уничтожения: ",
  'sys_chance_rips_destroy' => "Изменение разрывного уничтожения: ",

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
  'sys_dark_matter'    => "Темная Материя",
  'sys_dark_matter_sh' => "ТМ",

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

  'sys_planet_type' => array(
    PT_PLANET => 'Планета', 
    PT_DEBRIS => 'Поле обломков', 
    PT_MOON   => 'Луна',
  ),

  'sys_planet_type_sh' => array(
    PT_PLANET => '(П)',
    PT_DEBRIS => '(О)',
    PT_MOON   => '(Л)',
  ),

  'sys_capacity' 			=> 'Грузоподъёмность',
  'sys_cargo_bays' 		=> 'Трюмы',

  'sys_supernova' 		=> 'Сверхновая',
  'sys_server' 			=> 'Сервер',

  'sys_unbanned'			=> 'Разблокирован',

  'sys_date_time'			=> 'Дата и время',
  'sys_from_person'	   => 'От кого',

  'sys_from'		  => 'с',

// Resource page
  'res_planet_production' => 'Производство ресурсов на планете',
  'res_basic_income' => 'Естественное производство',
  'res_total' => 'ВСЕГО',
  'res_calculate' => 'Рассчитать',
  'res_hourly' => 'В час',
  'res_daily' => 'За день',
  'res_weekly' => 'За неделю',
  'res_monthly' => 'За месяц',
  'res_storage_fill' => 'Заполненность хранилища',
  'res_hint' => '<ul><li>Производство ресурсов <100% означает нехватку энергии. Постройте дополнительные электростанции или уменьшите производство ресурсов<li>Если ваше производство равно 0% скорее всего вы вышли из отпуска и вам нужно включить все заводы<li>Что бы выставить добычу для всех заводов сразу используйте дроп-даун в загловке таблицы. Особенно удобно использовать его после выхода из отпуска</ul>',

// Build page
  'bld_destroy' => 'Уничтожить',
  'bld_create'  => 'Построить',

// Imperium page
  'imp_imperator' => "Император",
  'imp_overview' => "Обзор Империи",
  'imp_fleets' => "Флоты в полете",
  'imp_production' => "Производство",
  'imp_name' => "Название",
  'imp_research' => "Исследования",
  'sys_fields' => "Сектора",

// Cookies
  'err_cookie' => "Ошибка! Невозможно авторизировать пользователя по информации в cookie. <a href='login." . PHP_EX . "'>Войдите</a> в игру или <a href='reg." . PHP_EX . "'>зарегестрируйтесь</a>.",

// Supported languages
  'ru'              	  => 'Русский',
  'en'              	  => 'Английский',

  'sys_vacation'        => 'Вы же в отпуске до',
  'sys_vacation_leave'  => 'Я уже отдохнул - выйти из отпуска!',
  'sys_level'           => 'Уровень',
  'sys_level_short'     => 'Ур',

  'sys_yes'             => 'Да',
  'sys_no'              => 'Нет',

  'sys_on'              => 'Включен',
  'sys_off'             => 'Отключен',

  'sys_confirm'         => 'Подтвердить',
  'sys_save'            => 'Сохранить',
  'sys_create'          => 'Создать',
  'sys_write_message'   => 'Написать сообщение',

// top bar
  'top_of_year' => 'года',
  'top_online'			=> 'Игроки on-line',

  'sys_first_round_crash_1'	=> 'Контакт с атакованным флотом потерян.',
  'sys_first_round_crash_2'	=> 'Это означает что он был уничтожен в первом раунде боя.',

  'sys_ques' => array(
    QUE_STRUCTURES => 'Здания',
    QUE_HANGAR     => 'Верфь',
    QUE_RESEARCH   => 'Исследования',
  ),

  'eco_que_empty' => 'Очередь пуста',
  'eco_que_clear' => 'Очистить очередь',
  'eco_que_trim'  => 'Отменить последнее',

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
  'sys_mess_attack_report' 	=> 'Боевой доклад',
  'sys_spy_maretials' 		=> 'Сырьё на',
  'sys_spy_fleet' 			=> 'Флот',
  'sys_spy_defenses' 		=> 'Оборона',
  'sys_mess_qg' 			=> 'Командование флотом',
  'sys_mess_spy_report' 		=> 'Шпионский доклад',
  'sys_mess_spy_lostproba' 	=> 'Погрешность информации, полученной спутником %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Шанс обнаружения вашего разведывательного флота %d%%',
  'sys_mess_spy_control' 		=> 'Контрразведка',
  'sys_mess_spy_activity' 		=> 'Шпионская активность',
  'sys_mess_spy_ennemyfleet' 	=> 'Чужой флот с планеты',
  'sys_mess_spy_seen_at'		=> 'был обнаружен возле планеты',
  'sys_mess_spy_destroyed'		=> 'Разведывательный флот был уничтожен',
  'sys_mess_spy_destroyed_enemy'		=> 'Вражеский шпионский флот уничтожен',
  'sys_object_arrival'		=> 'Прибыл на планету',
  'sys_stay_mess_stay' => 'Оставить флот',
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
  'sys_expe_nothing_1' 		=> 'Ваш исследователи стали свидетелями СверхНовой Звезды! И ваши накопители успели принять часть высвободившейся энергии.',
  'sys_expe_nothing_2' 		=> 'Ваш исследователи ничего не обнаружили!',
  'sys_expe_found_goods' 		=> 'Ваш исследователи нашли планету, богатую сырьём!<br>Вы получили %s %s, %s %s и %s %s',
  'sys_expe_found_ships' 		=> 'Ваш исследователи нашли безупречно новый флот!<br>Вы получили: ',
  'sys_expe_back_home' 		=> 'Ваш флот возвращается обратно.',
  'sys_mess_transport' 		=> 'Транспорт',
  'sys_tran_mess_owner' 		=> 'Один из ваших флотов достигает планеты %s %s и доставляет %s %s, %s  %s и %s %s.',
  'sys_tran_mess_user'  		=> 'Ваш флот отправленный с планеты %s %s прибыл на %s %s и доставил %s %s, %s  %s и %s %s.',
  'sys_mess_fleetback' 		=> 'Возвращение',
  'sys_tran_mess_back' 		=> 'Один из ваших флотов возвращается на планету %s %s.',
  'sys_recy_gotten' 		=> 'Один из Ваших флотов добыл %s %s и %s %s Возвращается на планету.',
  'sys_notenough_money' 		=> 'Вам не хватает ресурсов, чтобы построить: %s. У Вас сейчас: %s %s , %s %s и %s %s. Для строительства необходимо: %s %s , %s %s и %s %s.',
  'sys_nomore_level'		=> 'Вы больше не можете совершенствовать это. Оно достигло макс. уровня ( %s ).',
  'sys_buildlist' 			=> 'Список построек',
  'sys_buildlist_fail' 		=> 'нет построек',
  'sys_gain' 			=> 'Добыча: ',
  'sys_perte_attaquant' 		=> 'Атакующий потерял',
  'sys_perte_defenseur' 		=> 'Обороняющийся потерял',
  'sys_debris' 			=> 'Обломки: ',
  'sys_noaccess' 			=> 'В доступе отказано',
  'sys_noalloaw' 			=> 'Вам закрыт доступ в эту зону!',
  'sys_governor'        => 'Губернатор',

  // News page & a bit of imperator page
  'news_title'      => 'Новости',
  'news_none'       => 'Нет новостей',
  'news_new'        => 'НОВАЯ',
  'news_future'     => 'АНОНС',
  'news_more'       => 'Подробнее...',
                    
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
  'sys_max' => 'макс',
  'sys_banned_msg' => 'Вы забанены. Для получения информации зайдите <a href="banned.php">сюда</a>. Срок окончания блокировки аккаунта: ',
  'sys_total_time' => 'Общее время',

  // Universe
  'uni_moon_of_planet' => 'планеты',

  // Combat reports
  'cr_view_title'  => "Просмотр боевых отчетов",
  'cr_view_button' => "Просмотреть отчет",
  'cr_view_prompt' => "Введите код",
  'cr_view_my'     => "Мои боевые отчеты",
  'cr_view_hint'   => '<ul><li>Свои боевые отчеты можно посмотреть, кликнув по ссылке "Мои боевые отчеты" в заголовке</li><li>Код боевого отчета указывается в его последней строке и является последовательностью 32 цифр и символов латниского алфавита</li></ul>',

  // Dark Matter
  'sys_dark_matter_text' => '<h2>Что такое Темная Материя?</h2>
    Темная Материя - это игровая валюта, за счет которой в игре вы можете совершать различные операции:
    <ul><li>Обменивать один вид ресурсов на другой</li>
    <li>Вызывать скупщика флота</li>
    <li>Вызвать продавца Б/У кораблей</li>
    <li>Нанимать офицеров</li></ul>
    <h2>Где взять Темную Материю?</h2>
    Вы получаете Темную Материю в процессе игры: набирая опыт за рейды на чужие планеты и постройку зданий.
    Так же иногда исследовательские экспедиции могут принести ТМ.',
  'sys_dark_matter_purchase' => 'Кроме того вы можете приобрести ТМ за WebMoney.',
  'sys_dark_matter_get'  => 'Откройте эту ссылку, что бы узнать подробности.',

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
  'chat_history'  => 'История',
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
  'msg_class' => array(
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
  ),

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

  'sys_max' => 'Макс',
  'sys_maximum' => 'Максимум',
  'sys_maximum_level' => 'Максимальный уровень',

  'sys_user_name' => 'Имя пользователя',
  'sys_user_name_short' => 'Имя',

  'sys_planets' => 'Планеты',
  'sys_moons' => 'Луны',

  'sys_no_governor' => 'Нанять губернатора',

  'sys_quantity' => 'Количество',
  'sys_quantity_maximum' => 'Максимальное количество',
  'sys_qty' => 'К-во',

  'sys_buy_for' => 'Купить за',

  'sys_eco_lack_dark_matter' => 'Не хватает Темной Материи',

  // Arrays
  'sys_build_result' => array(
    BUILD_ALLOWED => 'Можно построить',
    BUILD_REQUIRE_NOT_MEET => 'Требования не удовлетворены',
    BUILD_AMOUNT_WRONG => 'Слишком много',
    BUILD_QUE_WRONG => 'Несуществующая очередь',
    BUILD_QUE_UNIT_WRONG => 'Неправильная очередь',
    BUILD_INDESTRUCTABLE => 'Нельзя уничтожить',
    BUILD_NO_RESOURCES => 'Не хватает ресурсов',
    BUILD_NO_UNITS => 'Нет юнитов',
  ),

  'sys_game_mode' => array(
    GAME_SUPERNOVA => 'Сверхновая',
    GAME_OGAME     => 'оГейм',
  ),

  'months' => array(
    '01'=>'Января',
    '02'=>'Февраля',
    '03'=>'Марта',
    '04'=>'Апреля',
    '05'=>'Мая',
    '06'=>'Июня',
    '07'=>'Июля',
    '08'=>'Августа',
    '09'=>'Сентября',
    '10'=>'Октября',
    '11'=>'Ноября',
    '12'=>'Декабря'
  ),

  'weekdays' => array(
    0 => 'Воскресенье',
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота'
  ),

  'user_level' => array(
    0 => 'Игрок',
    1 => 'Модератор',
    2 => 'Оператор',
    3 => 'Администратор',
  ),

  'user_level_shortcut' => array(
    0 => 'И',
    1 => 'М',
    2 => 'О',
    3 => 'А',
  ),

  'sys_lessThen15min'   => '&lt; 15 мин',

  'mrc_period_list' => array(
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
  ),

  // Officers/mercenaries
  'off_no_points'        => 'У вас недостаточно тёмной материи!',
  'off_recruited'        => 'Офицер был нанят! <a href="officer.php">Назад</a>',
  'off_tx_lvl'           => 'Текущий уровень: ',
  'off_points'           => 'Доступно тёмной материи: ',
  'off_not_available'    => 'Офицер вам еще не доступен!',
  'off_dark_matter_desc' => 'Тёмная материя - необнаружиая стандартными методами небарионная материя, на которую приходится 23% массы Вселенной. Из неё можно добывать невероятное количество энергии. Из-за этого, а так же из-за сложностей, связанных с её добычей, Темная Материя ценится очень высоко.',
  'off_dark_matter_hint' => 'При помощи этой субстанции можно нанять офицеров и командиров.',

  'mrc_up_to' => 'до',
  'mrc_hire' => 'Нанять',
  'mrc_hire_for' => 'Нанять за',
  'mrc_msg_error_wrong_mercenary' => 'Неправильный идентификатор наемника',
  'mrc_msg_error_wrong_level' => 'Неправильный уровень наемника',
  'mrc_msg_error_wrong_period' => 'Недопустимый срок найма',
  'mrc_msg_error_already_hired' => 'Наемник уже рекрутирован. Дождитесь окончания срока найма',
  'mrc_msg_error_no_resource' => 'Не хватает Темной Материи для найма',
  'mrc_msg_error_requirements' => 'Не удовлетворены требования',
));

?>
