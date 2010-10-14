<?php

// System-wide localization

$lang['user_level'] = array (
	'0' => 'Игрок',
	'1' => 'Модератор',
	'2' => 'Оператор',
	'3' => 'Администратор',
);

foreach($lang['user_level'] as $ID => $levelName)
{
  $lang['user_level_shortcut'][$ID] = $levelName[0];
}

$lang['sys_first_round_crash_1']	= 'Контакт с атакованным флотом потерян.';
$lang['sys_first_round_crash_2']	= 'Это означает что он был уничтожен в первом раунде боя.';

$lang['sys_overview']			= "Обзор";
$lang['mod_marchand']			= "Торговец";
$lang['sys_moon']			= "Луна";
$lang['sys_planet']			= "Планета";
$lang['sys_error']			= "Ошибка";
$lang['sys_no_vars']			= "Ошибка инициализации переменных, обратитесь к администрации!";
$lang['sys_attacker_lostunits']		= "Атакующий потерял %s единиц.";
$lang['sys_defender_lostunits']		= "Обороняющийся потерял %s единиц.";
$lang['sys_gcdrunits'] 			= "Теперь на этих пространственных координатах находятся %s %s и %s %s.";
$lang['sys_moonproba'] 			= "Шанс появления луны составляет: %d %% ";
$lang['sys_moonbuilt'] 			= "Благодаря огромной энергии огромные куски металла и кристалла соединяются и образуется новая луна %s [%d:%d:%d] !";
$lang['sys_attack_title']    		= "%s. Произошёл бой между следующими флотами::";
$lang['sys_attack_attacker_pos']      	= "Атакующий %s [%s:%s:%s]";
$lang['sys_attack_techologies'] 	= "Вооружение: %d %% Щиты: %d %% Броня: %d %% ";
$lang['sys_attack_defender_pos'] 	= "Обороняющийся %s [%s:%s:%s]";
$lang['sys_ship_type'] 			= "Тип";
$lang['sys_ship_count'] 		= "Кол-во";
$lang['sys_ship_weapon'] 		= "Вооружение";
$lang['sys_ship_shield'] 		= "Щиты";
$lang['sys_ship_armour'] 		= "Броня";
$lang['sys_destroyed'] 			= "уничтожен";
$lang['sys_attack_attack_wave'] 	= "Атакующий делает выстрелы общей мощностью %s по обороняющемуся. Щиты обороняющегося поглощают %s выстрелов.";
$lang['sys_attack_defend_wave']		= "Обороняющийся делает выстрелы общей мощностью %s по атакующему. Щиты атакующего поглащают %s выстрелов.";
$lang['sys_attacker_won'] 		= "Атакующий выиграл битву!";
$lang['sys_defender_won'] 		= "Обороняющийся выиграл битву!";
$lang['sys_both_won'] 			= "Бой закончился ничьёй!";
$lang['sys_stealed_ressources'] 	= "Он получает %s металла %s %s кристалла %s и %s дейтерия.";
$lang['sys_rapport_build_time'] 	= "Время генерации страницы %s секунд";
$lang['sys_mess_tower'] 		= "Транспорт";
$lang['sys_mess_attack_report'] 	= "Боевой доклад";
$lang['sys_spy_maretials'] 		= "Сырьё на";
$lang['sys_spy_fleet'] 			= "Флот";
$lang['sys_spy_defenses'] 		= "Оборона";
$lang['sys_mess_qg'] 			= "Командование флотом";
$lang['sys_mess_spy_report'] 		= "Шпионский доклад";
$lang['sys_mess_spy_lostproba'] 	= "Погрешность информации, полученной спутником %d %% ";
$lang['sys_mess_spy_control'] 		= "Контрразведка";
$lang['sys_mess_spy_activity'] 		= "Шпионская активность";
$lang['sys_mess_spy_ennemyfleet'] 	= "Чужой флот с планеты";
$lang['sys_mess_spy_seen_at']		= "был обнаружен возле планеты";
$lang['sys_mess_spy_destroyed']		= "Шпионский спутник был уничтожен";
$lang['sys_object_arrival']		= "Прибыл на планету";
$lang['sys_stay_mess_stay'] = "Оставить флот";
$lang['sys_stay_mess_start'] 		= "Ваш флот прибыл на планету";
$lang['sys_stay_mess_back']		= "Ваш флот вернулся ";
$lang['sys_stay_mess_end']		= " и доставил:";
$lang['sys_stay_mess_bend']		= " и доставил следующие ресурсы:";
$lang['sys_adress_planet'] 		= "[%s:%s:%s]";
$lang['sys_stay_mess_goods'] 		= "%s : %s, %s : %s, %s : %s";
$lang['sys_colo_mess_from'] 		= "Колонизация";
$lang['sys_colo_mess_report'] 		= "Отчёт о колонизации";
$lang['sys_colo_defaultname'] 		= "Колония";
$lang['sys_colo_arrival'] 		= "Флот достигает координат ";
$lang['sys_colo_maxcolo'] 		= ", но колонизировать планету нельзя, достигнуто максимальное число колоний для вашего уровня колонизации";
$lang['sys_colo_allisok'] 		= ", и колонисты начинают осваивать новую планету.";
$lang['sys_colo_badpos']  			= ", и колонисты нашли среду мало выгодной для Вашей империи. Миссия колонизации возвращается обратно на планету отправки.";
$lang['sys_colo_notfree'] 			= ", и колонисты не нашли планету в этих координатах. Они вынуждены проложить дорогу обратно абсолютно обескураженными.";
$lang['sys_colo_planet']  		= " Планета колонизирована!";
$lang['sys_expe_report'] 		= "Отчёт экспедиции";
$lang['sys_recy_report'] 		= "Системная информация";
$lang['sys_expe_blackholl_1'] 		= "Ваш флот попал в чёрную дыру и частично потерян!";
$lang['sys_expe_blackholl_2'] 		= "Ваш флот попал в чёрную дыру и полностью потерян!";
$lang['sys_expe_nothing_1'] 		= "Ваш исследователи стали свидетелями СверхНовой Звезды! И ваши накопители успели принять часть высвободившейся энергии.";
$lang['sys_expe_nothing_2'] 		= "Ваш исследователи ничего не обнаружили!";
$lang['sys_expe_found_goods'] 		= "Ваш исследователи нашли планету, богатую сырьём!<br>Вы получили %s %s, %s %s и %s %s";
$lang['sys_expe_found_ships'] 		= "Ваш исследователи нашли безупречно новый флот!<br>Вы получили: ";
$lang['sys_expe_back_home'] 		= "Ваш флот возвращается обратно.";
$lang['sys_mess_transport'] 		= "Транспорт";
$lang['sys_tran_mess_owner'] 		= "Один из ваших флотов достигает планеты %s %s и доставляет %s %s, %s  %s и %s %s.";
$lang['sys_tran_mess_user']  		= "Ваш флот отправленный с планеты %s %s прибыл на %s %s и доставил %s %s, %s  %s и %s %s.";
$lang['sys_mess_fleetback'] 		= "Возвращение";
$lang['sys_tran_mess_back'] 		= "Один из ваших флотов возвращается на планету %s %s.";
$lang['sys_recy_gotten'] 		= "Один из Ваших флотов добыл %s %s и %s %s Возвращается на планету.";
$lang['sys_notenough_money'] 		= "Вам не хватает ресурсов, чтобы построить: %s. У Вас сейчас: %s %s , %s %s и %s %s. Для строительства необходимо: %s %s , %s %s и %s %s.";
$lang['sys_nomore_level']		= "Вы больше не можете совершенствовать это. Оно достигло макс. уровня ( %s ).";
$lang['sys_buildlist'] 			= "Список построек";
$lang['sys_buildlist_fail'] 		= "нет построек";
$lang['sys_gain'] 			= "Добыча: ";
$lang['sys_perte_attaquant'] 		= "Атакующий потерял";
$lang['sys_perte_defenseur'] 		= "Обороняющийся потерял";
$lang['sys_debris'] 			= "Обломки: ";
$lang['sys_noaccess'] 			= "В доступе отказано";
$lang['sys_noalloaw'] 			= "Вам закрыт доступ в эту зону!";

$lang['VacationMode']			= "Ваше производство закрыто, так как вы в Отпуске";
$lang['sys_moon_destruction_report'] = "Рапорт разрушения луны";
$lang['sys_moon_destroyed'] = "Ваши Звёзды Смерти произвеои мощную гравитационную волну, которая разрушила луну! ";
$lang['sys_rips_destroyed'] = "Ваши Звёзды Смерти произвеои мощную гравитационную волну, но её мощности оказалось не достаточно для уничтожения луны такого размера. Но гравитационная волна отразилась от лунной поверхности и разрушила ваш флот.";
$lang['sys_rips_come_back'] = "Ваши Звёзды Смерти не имеют достаточно энергии, чтоб нанести ущерб этой луне. Ваш флот возвращается не уничтожив луну.";
$lang['sys_chance_moon_destroy'] = "Изменение лунного уничтожения: ";
$lang['sys_chance_rips_destroy'] = "Изменение разрывного уничтожения: ";

$lang['sys_day'] = "дней";
$lang['sys_hrs'] = "часов";
$lang['sys_min'] = "минут";
$lang['sys_sec'] = "секунд";

$lang['sys_day_short'] = "д";
$lang['sys_hrs_short'] = "ч";
$lang['sys_min_short'] = "м";
$lang['sys_sec_short'] = "с";

$lang['sys_ask_admin'] = ' - Отдельные вопросы вы можете задать по адресу';
$lang['TranslationBy'] = '';

$lang['sys_wait'] = 'Запрос выполняется. Пожалуйста, подождите.';

$lang['sys_affilates_title'] = "Партнерская программа";
$lang['sys_affilates_text1'] = "Разместите эту ссылку, баннер или юзербар на форуме или сайте и каждый пришедший по ссылке станет вашим Приглашенным. За каждые ";
$lang['sys_affilates_text2'] = " ТМ, заработанных приглашенным, вы получите 1 ТМ!";
$lang['sys_affilate_list']   = "Список приглашенных";
$lang['sys_affilates_none']  = "Нет приглашенных";
$lang['sys_total']           = "ИТОГО";
$lang['sys_link_name']       = "Личная ссылка в партнерской программе";
$lang['sys_link_bb']         = "BBCode для размещения личной ссылки на форуме";
$lang['sys_link_html']       = "HTML-код для размещения личной ссылки на веб-странице";
$lang['sys_banner_name']     = "Баннер";
$lang['sys_banner_bb']       = "BBCode для размещения баннера на форуме";
$lang['sys_banner_html']     = "HTML-код для размещения баннера на веб-странице";
$lang['sys_userbar_name']    = "Юзербар";
$lang['sys_userbar_bb']      = "BBCode для размещения юзербара на форуме";
$lang['sys_userbar_html']    = "HTML-код для размещения юзербара на веб-странице";

$lang['sys_attacker'] 		= "Атакующий";
$lang['sys_defender'] 		= "Обороняющийся";

$lang['COE_combatSimulator'] = "Симулятор боя";
$lang['COE_simulate']        = "Запуск симулятора";
$lang['COE_fleet']           = "Флот";
$lang['COE_defense']         = "Оборона";
$lang['sys_resources']       = "Ресурсы";
$lang['sys_ships']           = "Корабли";

$lang['sys_metal']          = "Металл";
$lang['sys_metal_sh']       = "М";
$lang['sys_crystal']        = "Кристалл";
$lang['sys_crystal_sh']     = "К";
$lang['sys_deuterium']      = "Дейтрий";
$lang['sys_deuterium_sh']   = "Д";
$lang['sys_energy']         = "Энергия";
$lang['sys_energy_sh']      = "Э";
$lang['sys_dark_matter']    = "Темная Материя";
$lang['sys_dark_matter_sh'] = "ТМ";

$lang['sys_resource'] = array(
  1 => $lang['sys_metal'],
  2 => $lang['sys_crystal'],
  3 => $lang['sys_deuterium'],
  4 => $lang['sys_dark_matter'],
  5 => $lang['sys_energy'],
);

$lang['sys_reset']           = "Сбросить";
$lang['sys_send']            = "Отправить";
$lang['sys_characters']      = "символов";
$lang['sys_back']            = "Назад";
$lang['sys_return']          = "Вернуться";
$lang['sys_delete']          = "Удалить";
$lang['sys_writeMessage']    = "Написать сообщение";
$lang['sys_hint']            = "Подсказка";

$lang['sys_alliance']        = "Альянс";
$lang['sys_player']          = "Игрок";
$lang['sys_coordinates']     = "Координаты";

$lang['sys_online']          = "Онлайн";
$lang['sys_offline']         = "Оффлайн";
$lang['sys_lessThen15min']   = '&lt; 15 м';
$lang['sys_status']          = "Статус";

$lang['sys_universe']        = "Вселенная";
$lang['sys_goto']            = "Перейти";

$lang['sys_time']            = "Время";

$lang['sys_no_task']         = "нет задания";

$lang['sys_affilates']       = "Приглашенные игроки";

$lang['sys_fleet_arrived']   = "Флот прибыл";

$lang['sys_planet_type1']    = "Планета";
$lang['sys_planet_type2'] 	  = "Поле обломков";
$lang['sys_planet_type3']    = "Луна";

$lang['sys_planet_type'] = array(
  1 => $lang['sys_planet_type1'], 
  2 => $lang['sys_planet_type2'], 
  3 => $lang['sys_planet_type3']
);

$lang['sys_planet_type_sh1'] = "(П)";
$lang['sys_planet_type_sh2'] = "(О)";
$lang['sys_planet_type_sh3'] = "(Л)";

$lang['sys_planet_type_sh'] = array(
  1 => $lang['sys_planet_type_sh1'], 
  2 => $lang['sys_planet_type_sh2'], 
  3 => $lang['sys_planet_type_sh3']
);

$lang['sys_capacity'] 			= 'Грузоподъёмность';

$lang['sys_supernova'] 			= 'Сверхновая';
$lang['sys_server'] 			= 'Сервер';


// Resource page
$lang['res_planet_production'] = 'Производство ресурсов на планете';
$lang['res_basic_income'] = 'Естесственное производство';
$lang['res_total'] = 'ВСЕГО';
$lang['res_calculate'] = 'Рассчитать';
$lang['res_daily'] = 'За день';
$lang['res_weekly'] = 'За неделю';
$lang['res_monthly'] = 'За месяц';
$lang['res_storage_fill'] = 'Заполненность хранилища';
$lang['res_hint'] = '<ul><li>Производство ресурсов <100% означает нехватку энергии. Постройте дополнительные электростанции или уменьшите производство ресурсов<li>Если ваше производство равно 0% скорее всего вы вышли из отпуска и вам нужно включить все заводы<li>Что бы выставить добычу для всех заводов сразу используйте дроп-даун в загловке таблицы. Особенно удобно использовать его после выхода из отпуска</ul>';

// Build page
$lang['bld_destroy'] = 'Уничтожить';

// Imperium page
$lang['imp_imperator'] = "Император";
$lang['imp_overview'] = "Обзор Империи";
$lang['imp_production'] = "Производство";
$lang['imp_name'] = "Название";
$lang['sys_fields'] = "Сектора";

// Cookies
$lang['cookies']['Error1'] = 'Ошибка! Очистите cookie или разрешите их приём! <a href=login.php>Войти</a>';
$lang['cookies']['Error2'] = 'Ошибка! Очистите cookie или разрешите их приём! <a href=login.php>Войти</a>';
$lang['cookies']['Error3'] = 'Ошибка! Очистите cookie или разрешите их приём! <a href=login.php>Войти</a>';

// Supported languages
$lang['ru']              	  = 'Русский';
$lang['en']              	  = 'Английский';

$lang['sys_vacancy']         = 'Вы же в отпуске!';
$lang['sys_level']           = 'Уровень';

$lang['sys_yes']             = 'Да';
$lang['sys_no']              = 'Нет';

$lang['sys_on']              = 'Включен';
$lang['sys_off']             = 'Отключен';

$lang['sys_game_mode'][0]    = 'Сверхновая';
$lang['sys_game_mode'][1]    = 'оГейм';

// top bar
$lang['top_of_year'] = 'года';
$lang['top_online']			= 'Игроки on-line';

$lang['months'] = array(
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
);

$lang['weekdays'] = array(
	'0' => 'Воскресенье',
	'1' => 'Понедельник',
	'2' => 'Вторник',
	'3' => 'Среда',
	'4' => 'Четверг',
	'5' => 'Пятница',
	'6' => 'Суббота'
);

$lang['sys_max'] = 'макс';
?>